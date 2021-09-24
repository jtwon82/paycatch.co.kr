<?php	
	include "../../event/common/function.php";
	$start = getMillisecond();
	include "../../event/common/db.php";
	include "../../event/common/config.php";




	/////// DB에 들어갈 값들을 변환합니다.

	$mode			= anti_injection($_REQUEST['mode']);
	$idx			= anti_injection($_REQUEST['idx']);

	$ssn			= anti_injection($_SESSION['SSN']);
	$CHK			= anti_injection($_COOKIE['CHK']);
	$referer		= anti_injection(base64_decode($_COOKIE['from'])); if ($referer=='') $referer='direct';
	$mobile			= anti_injection($_REQUEST['mobile']);
	$uname			= anti_injection($_REQUEST['uname']);
	$title			= anti_injection($_REQUEST['title']);
	$contents		= anti_injection($_REQUEST['contents']);
	$content		= anti_injection($_REQUEST['content']);
	$pno			= anti_injection($_REQUEST['pno']);
	$pno1			= anti_injection($_REQUEST['pno1']);
	$pno2			= anti_injection($_REQUEST['pno2']);
	$pno3			= anti_injection($_REQUEST['pno3']);
	$user_type		= anti_injection($_REQUEST['user_type']);
	$addr1			= anti_injection($_REQUEST['addr1']);
	$addr2			= anti_injection($_REQUEST['addr2']);
	$addr3			= anti_injection($_REQUEST['addr3']);
	$reg_ip			= anti_injection(getUserIp());
	$url			= anti_injection($_REQUEST['url']);
	$pageno			= anti_injection($_REQUEST['pageno']);
	$page			= anti_injection($_REQUEST['page']);
	$chance_type	= anti_injection($_REQUEST['chance_type']);
	$agree			= anti_injection($_REQUEST['agree']);
	$agree2			= anti_injection($_REQUEST['agree2']);
	$user_type		= anti_injection($_REQUEST['user_type']);
	$share_desc		= anti_injection($_REQUEST['share_desc']);
	$answer1		= anti_injection($_REQUEST['answer1']);
	$answer2		= anti_injection($_REQUEST['answer2']);

	$bucket_txt		= anti_injection($_REQUEST['bucket_txt']);
	$hash_tag_txt	= anti_injection($_REQUEST['hash_tag_txt']);
	$sns_url		= anti_injection($_REQUEST['sns_url']);
	$oreo_name		= anti_injection($_REQUEST['oreo_name']);
	$selectType		= anti_injection($_REQUEST['selectType']);
	$passwd		= anti_injection($_REQUEST['passwd']);
	$reply		= anti_injection($_REQUEST['reply']);
	$selected		= anti_injection($_REQUEST['selected']);

	$letterType		= anti_injection($_REQUEST['letterType']);
	$to_name		= anti_injection($_REQUEST['to_name']);
	$from_name		= anti_injection($_REQUEST['from_name']);
	$letter_txt		= anti_injection($_REQUEST['letter_txt']);
	$rcver_name		= anti_injection($_REQUEST['rcver_name']);
	$rcver_pno		= anti_injection($_REQUEST['rcver_pno']);

	$folder			= explode("/",$_SERVER['PHP_SELF']);
	$folder			= 'paycatch';
	$info[1]		= array('name'=>'paycatch', 'sdate'=>'2021-09-11', 'edate'=>'2021-12-31');


	/////// DB에 들어갈 값들을 정리합니다. 
	switch ($mode) {




		Case "BASE64_UPLOAD" :
			$file			= $_REQUEST['tmpdata'];
			$chk			= anti_injection($_REQUEST['chk']);

			if( $ssn && $chk && $file ){

				$blobname = $chk.".jpg";

				$img = substr($file, strpos($file,',')+1);
				$img = str_replace(' ', '+', $img);
				$data = base64_decode($img);
				file_put_contents('/home/dongsuh/www.oreo-event.com/upfile/'.$blobname, $data);

				echo json_encode( array('result'=>'o', 'blobname'=>$blobname ) );
			} else {
				echo json_encode( array('result'=>'x' ) );
			}
		break;


		Case "OnLoad" :
			setcookie('SSN', "", 0);
		break;

		Case "REGISTER_CHK":
			$sql = "select count(*)c FROM tbl_member WHERE ssn='$ssn' ";
			$rs = db_select($sql);
			if ( !isset($_SESSION[USER][LOGIN_ID]) || $rs[c] == 0 ){
				echo json_encode( array('result'=>'x') );

			} else {
				$agree = db_select("select * FROM tbl_member WHERE ssn='$ssn' ");

				$infoM = info_user_chance($ssn);
				echo json_encode( array('result'=>'o', 'agree'=>$agree[agree], 'chance_info'=>$infoM) );
			}
		break;







		Case "DO_REGISTER":
			$sql = "SELECT * FROM tbl_member WHERE pno='$pno' and uname='$uname' ";
			$rs = db_select($sql);
			$ssn = $rs[ssn];

			if( $ssn!='' ){
				if(isset($agree) && $agree=='Y'){
					db_query("update tbl_member set agree='Y' where ssn='$ssn' ");
				}
				setcookie('SSN', $ssn, 0 );

				$infoM = info_user_chance($ssn);
				if ( $infoM[login_today]<1 ){
					insert_user_chance2($reg_ip, $ssn, $ssn, 'login', 10);
				}
				$infoM = info_user_chance($ssn);
				echo json_encode( array('result'=>'o', 'agree'=>$agree, 'chance_info'=>$infoM) );

			}
			else {
				$ssn = getToken(15);
				setcookie('SSN', $ssn, 0 );

				$sql = "INSERT INTO tbl_member(reg_ip, reg_date, reg_dates, ssn, chk, uname, pno, agree)
						VALUES('$reg_ip', now(), left(now(),10), '$ssn', '$ssn', '$uname', '$pno', '$agree')";
				db_query($sql);

				insert_user_chance2($reg_ip, $ssn, $ssn, 'login', 10);
				$infoM = info_user_chance($ssn);
				echo json_encode( array('result'=>'o', 'agree'=>$agree, 'chance_info'=>$infoM) );
			}
		break;


		Case "CHARGE_CHANCE":
			$infoM = info_user_chance($ssn);

			if ( $chance_type == 'eeeee' ){

			}else if ( $chance_type == 'getInfo' ){
				echo json_encode( array('result'=>'o', 'chance_info'=>$infoM, 'end'=>getMillisecond()-$start) );
			
			}else if ( $chance_type == 'use' ){
				echo json_encode( charge_chance($reg_ip, $ssn, $CHK, 'use', -1));
			
			}else if ( $chance_type == 'gift' ){
				echo json_encode( charge_chance($reg_ip, $ssn, $CHK, 'gift', 3));

			} else {
				echo json_encode( array('result'=>'x', 'end'=>getMillisecond()-$start) );
			}
		break;


		Case "CHKSTART" :
			if( !isset($_SESSION[USER][LOGIN_ID]) || $ssn=='' ){
				echo json_encode( array('result'=>'9') );
			}
			else{

				$sql = "SELECT CASE 
						WHEN LEFT(NOW(),10)<'{$info[1][sdate]}' THEN 'before'
						WHEN LEFT(NOW(),10)>'{$info[1][edate]}' THEN 'end'
						WHEN LEFT(NOW(),10) BETWEEN '{$info[1][sdate]}' AND '{$info[1][edate]}' THEN 
							CASE WHEN COUNT(*) >= 50000 THEN 'x' ELSE 'o' END 
						END c
					from tbl_event 
					where ssn = '$ssn'
					and reg_dates = left(now(), 10)
					";
				$rs = db_select($sql);

				if ( $rs[c]=='o' ){

					$chk = getToken(15);

					db_query("insert into tbl_event_CHKER(ssn, chk, reg_date, reg_ip) values('$ssn', '$chk', now(), '$reg_ip')");

					setcookie('CHK', ($chk), 0);
				}
				echo json_encode( array('result'=>$rs[c]) );
			}
		break;

		Case "CHKWINNER" :

			if ($referer==''){
				$referer='direct';
			}

			if( !isset($_SESSION[USER][LOGIN_ID]) || $ssn=='' ){
				echo json_encode( array('result'=>'9') );

			}
			else if( isset($_COOKIE['CHK']) ){
				$pno = db_select("SELECT pno, uname FROM tbl_member WHERE ssn='$ssn' ");
				$pno1=$pno[pno];
				$uname=$pno[uname];

				$attack = getUserIpIsAttac();

				$sql = "
				SELECT B.c FROM (SELECT 1 no) A, (
					SELECT CASE
						WHEN (select count(1) from tbl_event where reg_dates = left(now(), 10) and ssn = '$ssn' )>10000 THEN 'lose_overjoin'
						WHEN left(now(),10)>'{$info[1][edate]}' then 'end'
						WHEN 'true'='$attack' then 'lose_attack'
						WHEN EXISTS( SELECT ssn FROM tbl_event_CHKER WHERE ssn='$ssn' AND reg_date < DATE_ADD( NOW( ) , INTERVAL - 60 SECOND ) GROUP BY ssn HAVING COUNT(*)>120 ) THEN 'lose_over1'
						WHEN EXISTS( SELECT reg_ip FROM tbl_denyip WHERE event_gubun='$folder' AND reg_ip NOT LIKE '10.%' AND start_date = '{$info[1][name]}' AND reg_ip='$reg_ip' GROUP BY reg_ip HAVING SUM(win_cnt)>1) THEN 'lose_over2'
						WHEN EXISTS( SELECT reg_date FROM tbl_event_CHKER a, ( SELECT paycatch_winner FROM tbl_site_config ) b WHERE win_type IS NOT NULL AND reg_date > DATE_ADD( NOW( ) , INTERVAL (paycatch_winner*-1) MINUTE ) ) THEN 'lose_overwin'
						WHEN EXISTS( SELECT reg_date FROM tbl_event_CHKER WHERE chk = '$CHK' AND reg_date < DATE_ADD( NOW( ) , INTERVAL - 60 SECOND ) ) THEN 'lose_oversec'
						WHEN EXISTS( SELECT pno FROM tbl_event_winnerpno WHERE start_date = '{$info[1][name]}' AND pno='$pno1' ) THEN 'lose_overlab'
						WHEN NOT EXISTS( SELECT NOW( ) FROM tbl_event_CHKER WHERE chk = '$CHK' ) THEN 'lose_over30'
						else
							(
								select case 
									WHEN	/* 30% 안에 들고 */
											(RAND()*100) <= paycatch_pct
										THEN 
											case
												when idx<=30 and b.gift < paycatch_gift then 'gift'	/* gift1 */
												when idx<=60 and b.gift2 < paycatch_gift2 then 'gift2'	/* gift2 */
												when idx<=100 and b.gift3 < paycatch_gift3 then 'gift3'	/* gift3 */
												else 'lose_pct'
											end
										ELSE 'lose_else' END gg
								from (
										select (RAND()*100) idx
									) a, (
										select
											ifnull(sum(web_gift + mob_gift),0) gift
											, ifnull(sum(web_gift2 + mob_gift2),0) gift2
											, ifnull(sum(web_gift3 + mob_gift),0) gift3
										from tbl_event_sum 
										where reg_dates=left(now(), 10)
									) b, (
										select 
											paycatch_pct, paycatch_gift, paycatch_gift2, paycatch_gift3
										from tbl_site_config
										limit 0 UNION ALL SELECT 100, 10, 10, 10 LIMIT 1
									) c
							)
						end c
				)B
				";
				$rs = db_select($sql);
				if($rs[c]=='' || empty($rs[c]) || is_null($rs[c])
						//	|| $pno1=='' || empty($pno1) || is_null($pno1) 
						//	|| $uname=='' || empty($uname) || is_null($uname) 
					){
					$rs[c]='lose';
				}

				$tag['gift']='<img src="/camp/roulette/img/event_kv02_coupon2.png">';
				$tag['gift2']='<img src="/camp/roulette/img/event_kv02_coupon3.png">';
				$tag['gift3']='<img src="/camp/roulette/img/event_kv02_coupon4.png">';

				if( $rs[c]=='end' ){
					echo json_encode( array('result'=>'end') );
				}
				else if( $rs[c]=='x' ){

					echo json_encode( array('result'=>'x') );
				}
				else{
					$reason = $rs[c];

					if ( strpos($rs[c], 'lose')>-1 ){
						$sql = "insert into tbl_event(event_gubun, reg_date, reg_dates, ssn, chk, win_type, reason, mobile, reg_ip, referer, uname, pno1, share_desc )
							values('{$info[1][name]}', now(), left(now(), 10), '$ssn', '$CHK', 'lose', '$reason', '$mobile', '$reg_ip', '$referer', '$uname', '$pno1', '$snsType' )";
						db_query($sql);

						$blob_idx= mysql_insert_id();
						#db_query("INSERT INTO tbl_event_sharesns(pidx, ssn, chk, reg_date, reg_dates, share_desc) VALUES('$blob_idx', '$ssn', '$chk', now(), left(now(),10), '$snsType') ON DUPLICATE KEY UPDATE cnt=cnt+1");

						$uname	= base64_encode($uname);
						$pno1	= base64_encode($pno1);
						$sql = "insert into tbl_event_joiner(event_gubun, reg_date, reg_dates, ssn, chk, win_type, reason, mobile, reg_ip, referer, uname, pno1, share_desc )
							values('{$info[1][name]}', now(), left(now(), 10), '$ssn', '$CHK', 'lose', '$reason', '$mobile', '$reg_ip', '$referer', '$uname', '$pno1', '$snsType' )";
						db_query($sql);

						#$t= charge_chance($reg_ip, $ssn, $CHK, 'gift', 2);
						$chance_info= info_user_chance($ssn);

						echo json_encode( array('result'=>'lose', 'losetype'=>$rs[c], 'chance_info'=>$chance_info ) );
					}
					else{
						setcookie(date('Ymd'), base64_encode($rs[c]), 0);
						$sql = "insert into tbl_event(event_gubun, reg_date, reg_dates, ssn, chk, win_type, reason, mobile, reg_ip, referer, uname, pno1, share_desc )
							values('{$info[1][name]}', now(), left(now(), 10), '$ssn', '$CHK', '$reason', '$reason', '$mobile', '$reg_ip', '$referer', '$uname', '$pno1', '$snsType' )";
						db_query($sql);

						$blob_idx= mysql_insert_id();
						#db_query("INSERT INTO tbl_event_sharesns(pidx, ssn, chk, reg_date, reg_dates, share_desc) VALUES('$blob_idx', '$ssn', '$chk', now(), left(now(),10), '$snsType') ON DUPLICATE KEY UPDATE cnt=cnt+1");

						$uname	= base64_encode($uname);
						$pno1	= base64_encode($pno1);
						$sql = "insert into tbl_event_joiner(event_gubun, reg_date, reg_dates, ssn, chk, win_type, reason, mobile, reg_ip, referer, uname, pno1, share_desc )
							values('{$info[1][name]}', now(), left(now(), 10), '$ssn', '$CHK', '$reason', '$reason', '$mobile', '$reg_ip', '$referer', '$uname', '$pno1', '$snsType' )";
						db_query($sql);

						$sql = "update tbl_event_CHKER set win_type = '".$rs[c]."' where chk = '$CHK' ";
						db_query($sql);

						$chance_info= info_user_chance($ssn);

						echo json_encode( array('result'=>$rs[c], 'CHK'=>$CHK, 'win_time'=>time(), 'winimg_tag'=>$tag[ $rs[c] ], 'chance_info'=>$chance_info) );
					}
				}
			}
			else{
				echo json_encode( array('result'=>'9') );
			}
		break;

		Case "JOIN_GIFTf" :
			//	$pno = db_select("SELECT pno, uname FROM tbl_member WHERE ssn='$ssn' ");
			//	$pno1=$pno[pno];
			//	$uname=$pno[uname];

			$gift = base64_decode($_COOKIE[date('Ymd')]);

			$sql = "select IFNULL(SUM(win_cnt),0)win_cnt from tbl_event_winnerpno where start_date = '{$info[1][name]}' AND gift_type = '$gift' AND pno='{$pno1}{$pno2}{$pno3}' ";
			$rs = db_select($sql);

			if ( $rs[win_cnt]>=5 ) {
				$sql = "UPDATE tbl_event_winnerpno SET over_cnt = over_cnt+1 WHERE start_date = '{$info[1][name]}' AND gift_type = '$gift' AND pno='{$pno1}{$pno2}{$pno3}'";
				db_query($sql);

				echo json_encode( array('result'=>'limit3') );
			}
			else{
				$sql = "select case when count(*)>0 then 1 else 0 end c 
						from tbl_event_CHKER 
						where chk = '$CHK'
						and reg_date > DATE_ADD( NOW( ) , INTERVAL -30 MINUTE )
						and win_ok is null
						";
				$rs = db_select($sql);

				if ( $rs[c]==1 ){
					$udata = "$ssn.$CHK.$uname.$pno1.$pno2.$pno3.$addr1.$addr2.$snsType";
					$sql = "update tbl_event_CHKER set data1 = '$udata' where chk = '$CHK' ";
					db_query($sql);

					echo json_encode( array('result'=>'o') );
				}
				else{
					echo json_encode( array('result'=>'x', 'win_time'=>time(), 'chk'=>$CHK) );
				}
			}
		break;

		Case "JOIN_GIFT" :
			//	$pno = db_select("SELECT pno, uname FROM tbl_member WHERE ssn='$ssn' ");
			//	$pno1=$pno[pno];
			//	$uname=$pno[uname];

			$gift = base64_decode($_COOKIE[date('Ymd')]);

			$udata = "$ssn.$CHK.$uname.$pno1.$pno2.$pno3.$addr1.$addr2.$snsType";
			$sql = "select case when count(*)>0 then 1 else 0 end c 
					from tbl_event_CHKER 
					where chk = '$CHK'
					and reg_date > DATE_ADD( NOW( ) , INTERVAL -30 MINUTE )
					and data1 = '$udata'
					and win_type = '$gift'
					and win_ok is null
					";
			$rs = db_select($sql);

			if( $rs[c]==1 && $ssn && $CHK ){

				db_query("update tbl_event_CHKER set win_ok='1' where chk = '$CHK'");

				//$referer = anti_injection(base64_decode($referer));
				//if ($referer==''){
				//	$referer='direct';
				//}

				$sql = "update tbl_event set update_date = now(), pno1='$pno1', uname='$uname' where ssn='$ssn' and chk='$CHK' AND reg_dates BETWEEN LEFT(DATE_ADD(NOW(),INTERVAL -1 DAY),10) AND LEFT(NOW(),10) ";
				db_query($sql);

				db_query("INSERT INTO tbl_event_winnerhistory(reg_date, reg_dates, ssn, uname, pno) select now(), left(now(),11), '$ssn', '$uname', '{$pno1}{$pno2}{$pno3}' ON DUPLICATE KEY UPDATE update_date=now(), cnt=cnt+1 ");

				setcookie('CHK', "", 0);
				setcookie(date('Ymd'), "", 0);

				$sql = "
					INSERT INTO tbl_denyip(reg_date, event_gubun, start_date, reg_ip, win_type, win_cnt)
						SELECT now(), '$folder', '{$info[1][name]}', '$reg_ip' reg_ip, '$gift' win_type, 1 win_cnt
					ON DUPLICATE KEY UPDATE win_cnt = win_cnt+1, update_date = now()
				";
				db_query($sql);

				$sql = "INSERT INTO tbl_event_winnerpno(start_date, gift_type, pno) select '{$info[1][name]}', '$gift', '{$pno1}{$pno2}{$pno3}' pno ON DUPLICATE KEY UPDATE win_cnt=win_cnt+1 ";
				db_query($sql);

				db_query("INSERT INTO tbl_event_sum(reg_dates, {$mobile}_{$gift}) VALUES(LEFT(NOW(),10), 1) ON DUPLICATE KEY UPDATE {$mobile}_{$gift} = {$mobile}_{$gift} + 1;");

				db_query("UPDATE tbl_member SET pno='$pno1', uname='$uname' WHERE ssn='$ssn'");
				
				#$param			= array( 'mode'=>'SEND_SMS', 'CHK'=>$CHK );
				#$SEND_RESULT	= httpPost("http://db-studio.dbins-promy.com:8081/_exec.php", $param);
				#db_query("insert into tbl_event_result(ssn, chk, pno1, win_type, reg_date, reg_dates) values('$ssn', '$CHK', '$pno1', '$gift', now(), left(now(),10))");

				echo json_encode( array('result'=>'o') );
			}
			else{
				echo json_encode( array('result'=>'x', 'win_time'=>time(), 'chk'=>$CHK) );
			}
		break;

		Case "SEND_SMS":

			$ssn		= anti_injection($_REQUEST['ssn']);
			$CHK		= anti_injection($_REQUEST['CHK']);
			$win_type	= anti_injection($_REQUEST['win_type']);
			
			$dbchk=true;

			$sql = "SELECT win_type, pno1 FROM tbl_event WHERE chk='$CHK' AND ssn='$ssn' AND win_type='$win_type' LIMIT 1 ";
			#$sql = "SELECT win_type, pno1 FROM tbl_event WHERE chk='$CHK' AND ssn='$ssn' AND win_type='$win_type' UNION ALL SELECT '', '' LIMIT 1 ";
			$rs = db_select($sql);

			$url = "https://corp.coufun.kr:446/coupon/couponSend.do";
			if($rs[pno1] && $rs[win_type]){
				# REAL
				#$param = array('POC_ID'=>'POC0000125', 'SEND_PHONE'=>'0269569621', 'SEND_TITLE'=>'DB생명', 'RECEIVER_MOBILE'=>$rs[pno1], 'USER_ID'=>$CHK, 'TR_ID'=>$CHK.getToken(3) );
				$param = array('EVENT_ID'=>'8305', 'GOODS_ID'=>'0000133518', 'ORDER_CNT'=>1, 'RECEIVERMOBILE'=>$rs[pno1], 'USER_ID'=>$CHK, 'TR_ID'=>$CHK.getToken(3) );

				if($rs[win_type]=='gift'){
					$param[GOODS_ID] = '0000133518';
				}
				else {
					$param[GOODS_ID] = '';
				}
			}
			else{
				$dbchk=false;
			}

			# TEST
			#$param = array('POC_ID'=>'POC0000177', 'GOODS_ID'=>'0000005350', 'SEND_PHONE'=>'0269587293', 'SEND_TITLE'=>'DB생명', 'RECEIVER_MOBILE'=>$rs[pno1], 'USER_ID'=>$CHK, 'TR_ID'=>$CHK.getToken(3) );
			#$param = array('EVENT_ID'=>'918', 'GOODS_ID'=>'0000005904', 'ORDER_CNT'=>1, 'RECEIVERMOBILE'=>$rs[pno1], 'USER_ID'=>$CHK, 'TR_ID'=>$CHK.getToken(3) );
			#$url = "http://tcorp.coufun.kr/b2c_api/coufunSend.do";
			#$url = "https://corp.coufun.kr:446/coupon/couponCreate.do";
			#print_r($param);exit;
			#http://db-studio.dbins-promy.com:8081/_exec.php?mode=SEND_SMS&ssn=5TR2WE6762BDI1Z&CHK=4A2ZGEDL8HZ830V&win_type=gift3

			if($dbchk && $param[GOODS_ID]!=''){
				$result		= httpPost($url, $param);

				$object		= simplexml_load_string($result);
				foreach($object as $value) {
					$RESULTCODE .= ",".$value;
				}

				$call_param = json_encode($param);

				db_query("update tbl_event_result set call_param='$call_param', result='$result', result_code='$RESULTCODE', send_date=now() where chk='$CHK' AND ssn='$ssn' ");

				echo json_encode( array('result'=>'o', 'msg'=>$RESULTCODE) );
			}
			else
			{
				echo json_encode( array('result'=>'x', 'rs'=>$rs, 'dbchk'=>$dbchk, 'param'=>$param) );

				db_query("update tbl_event_result set send_date=now(), result_code='bad access' where chk='$CHK' AND ssn='$ssn' ");
			}
		break;






		Case "LIST_REPLY":
			$reload		= anti_injection($_REQUEST['reload']);
			$clear		= anti_injection($_REQUEST['clear']);

			$g= "list";
			if(!$page) $page=1;

				$list_num=10;
				$page_num=10;
				$start_num=($page-1)*$list_num;

				if($clear=='clear'){
					$_SESSION['USER']['scroll_idx']=0;
				}else{
					$_SESSION['USER']['scroll_idx']=(!$_SESSION['USER']['scroll_idx'] ? $list_num : $_SESSION['USER']['scroll_idx']+$list_num);
				}
				$start_num= $_SESSION['USER']['scroll_idx'];
				$page= $start_num;


			#if($clear=='more' && rand()%100>90){
			if(TRUE){
				$Minfo = db_select("select pno FROM tbl_member WHERE ssn='$ssn' ");

				$where ="where b_id='016' ";

				$count=db_result("select count(*) c from tbl_board a $where");

				$i=0;
				$sql = "
					select a.*
						, b.uname, case when '$Minfo[pno]' = '' then 'N' else 'Y' end is_loginok, case when a.ssn='$ssn' then 'Y' else 'N' end is_me
					from tbl_board a left join tbl_member b on a.ssn=b.ssn
					$where
					and re_level=0
					order by notice desc, ref desc, re_step desc, idx desc
					limit {$start_num}, {$list_num}
				";
				#echo "<textarea>$sql</textarea>";
				$pRs=db_query($sql);
				while($pList=db_fetch($pRs,'assoc')){

					$comment= db_select_list("select a.*, a.name uname, case when '$Minfo[pno]' = '' then 'N' else 'Y' end is_loginok, case when a.ssn='$ssn' then 'Y' else 'N' end is_me from tbl_board a where ref='$pList[ref]' and re_level!=0 order by notice desc, ref desc, re_step desc, idx desc");
					$pList[comments]= $comment;
					$pList[reply_cnt]= count($comment);
					$pList[title]= stripslashes($pList[title]);

					$row[] = $pList;
				}

				// 좌우 버튼만 있는함수
				#$paging = page_list_onlynpbtn($page, $count, $list_num, $page_num, "window.boardLoad({page})", "", "<img src='images/section2_list_arrow_prev.png'>", "<img src='images/section2_list_arrow_next.png'>", "");

				$end = getMillisecond() - $start;
				$result= json_encode( array('result'=>'o', 'list'=>$row, 'count'=>$count, 'end'=>$end) );
				session_write_close();
				#file_put_contents("/home/dongsuh/www.oreo-event.com/upfile/{$g}_{$page}.json", $result);
			}
			else{
				$result = file_get_contents("/home/dongsuh/www.oreo-event.com/upfile/{$g}_{$page}.json");
			}
			echo $result;
		break;

		Case "INSERT_REPLY":
			$chk_decode= json_decode(base64_decode($CHK),true);
			$uname= $chk_decode[uname];
			$pno= $chk_decode[pno];
			$selectType= $chk_decode[selectType];

			$Minfo = db_select("select * FROM tbl_member WHERE ssn='$ssn' ");

			$content	= $contents;

			if($Minfo[ssn]=='' ){	//invalid_info
				echo json_encode( array('result'=>'invalid_ssn') );
			}
			//	else if($content=='' ){			//invalid_content
			//		echo json_encode( array('result'=>'invalid_content') );
			//	}
			else{
				//	$count= db_result("SELECT COUNT(1) FROM tbl_board WHERE b_id='016' AND reg_dates=LEFT(NOW(),10)  ");
				//	if($count>0){
				//		echo json_encode( array('result'=>'limit_join') );
				//	}
				//	else{

					if ($reply=='reply') {
						$org = db_select("select ref, re_level, re_step, passwd, notice from tbl_board where idx='".$idx."'");

						//댓글 순서 한 칸씩 밀기
						db_query("update tbl_board set re_step=re_step-1 where ref='".$org['ref']."' and re_step>'".$org['re_level']."'");

						$passwd=			substr($pno, -4,4);
						$content=			addslashes($content);
						$title=				cutstr($content,100);

						//글저장
						$field[passwd]=		$passwd;
						$field[notice]=		$org['notice'];
						$field[ref]=		$org['ref'];
						$field[re_level]=	$org['re_level'] + 1;
						$field[re_step]=	$org['re_step'] - 1;

						db_query("insert into tbl_board(b_id, reg_date, reg_dates, ssn, name, passwd, title, content, ip, ref, re_level, re_step, notice)
							values('016', now(), left(now(),10), '$ssn', '$uname', '$field[passwd]', '$title', '$content', '$reg_ip', '$field[ref]', '$field[re_level]', '$field[re_step]', '$field[notice]')");

						echo json_encode( array('result'=>'o') );
					}
					else{
						//글번호 생성
						$rList = db_select("select ref from tbl_board order by ref desc");
						$ref = ($rList['ref'] ? $rList['ref']+1 : "1");

						$passwd=			substr($pno, -4,4);
						$content=			addslashes($content);
						$title=				cutstr($content,100);

						db_query("insert into tbl_board(b_id, reg_date, reg_dates, ssn, name, passwd, title, content, ip, ref)
							values('016', now(), left(now(),10), '$ssn', '$uname', '$passwd', '$title', '$content', '$reg_ip', '$ref')");

						echo json_encode( array('result'=>'o') );
					}
			//	}
			}
		break;

		Case "MODIFY_REPLY":
			$contents=			addslashes($contents);
			$title=				cutstr($contents,100);
			db_query("update tbl_board set title='$title', content='$contents' where idx='$idx' ");
			echo json_encode( array('result'=>'o', 'contents'=>stripslashes($contents)) );
		break;

		Case "DELETE_REPLY":
			$count=	db_result("select count(*) c from tbl_board where idx='$idx' ");
			if($count==1){
				db_query("delete from tbl_board where idx='$idx'  ");

				echo json_encode( array('result'=>'o') );
			}
			else{
				echo json_encode( array('result'=>'x') );
			}
		break;








		Case "INSERT_LETTER":

			$rcver_name=		addslashes($rcver_name);
			$rcver_pno=			addslashes($rcver_pno);
			$to_name=			addslashes($to_name);
			$from_name=			addslashes($from_name);
			$letter_txt=		addslashes($letter_txt);
			$title=				cutstr($letter_txt,100);

			if($rcver_name=='' || $rcver_pno=='' || $to_name=='' || $from_name=='' || $letter_txt=='' ){
				echo json_encode( array('result'=>'empty_entry') );
			}
			else{
				//글번호 생성
				$rList = db_select("select ref from tbl_board order by ref desc");
				$ref = ($rList['ref'] ? $rList['ref']+1 : "1");

				db_query("insert into tbl_board(b_id, reg_date, reg_dates, ssn, name, pno, title, content, letterType, ip, ref, to_name, from_name, mobile)
					values('002', now(), left(now(),10), '$ssn', '$rcver_name', '$rcver_pno', '$title', '$letter_txt', '$letterType', '$reg_ip', '$ref', '$to_name', '$from_name', '$mobile')");

				echo json_encode( array('result'=>'o') );
			}
		break;

		Case "GET_LETTERCNT":
			$sql= "SELECT COUNT(1)+1 FROM tbl_board WHERE b_id='002'";
			$count=	db_result($sql);
			echo json_encode( array('result'=>'o', 'count'=> str_pad($count,3,"0", STR_PAD_LEFT)) );
		break;
















		Case "MY_GIFT":
			$infoM = db_select("select uname from event_oreoday_member where ssn='$ssn'");

			$sql = "
			SELECT * FROM (
				SELECT idx, LEFT(c.reg_dates,7)ym, DATE_FORMAT(c.reg_dates,'%m')m, c.reg_dates, c.ssn, c.chk, pno1, win_type
				FROM event_oreoday c 
				WHERE c.ssn='$ssn' AND win_type IN('gift','gift2','gift3','gift4')
				UNION ALL
				SELECT idx, LEFT(c.reg_dates,7)ym, DATE_FORMAT(c.reg_dates,'%m')m, c.reg_dates, c.ssn, c.chk, pno1, win_type
				FROM event_oreoday_addwinner c 
				WHERE ssn='$ssn' AND win_type IN('gift','gift2','gift3','gift4')
			)a ORDER BY idx DESC LIMIT 1
			";
			$pRs=db_query($sql);
			while($pList=db_fetch($pRs,'assoc')){
				$row[] = $pList;
			}
			echo json_encode( array('result'=>'o', 'uname'=>$infoM[uname], 'list'=>$row) );
		break;




		Case "TRACE" :
			$sql = "insert into tbl_trace(wdates, ssn, pageno, cnt) values(left(now(),10), '$ssn', '$pageno', 1)";
			#db_query($sql);
		break;
		Case "COUPANG_AD" :
			$sql = "INSERT INTO tbl_counter_coupangad (reg_date, ssn, url) select now(), '$ssn', '$url' ON DUPLICATE KEY UPDATE cnt=cnt+1 ";
			db_query($sql);
		break;

		Case "CLEAR" :
			$sql = "
				DELETE c FROM event_oreoday_CHKER c, ( SELECT paycatch_winner FROM tbl_site_config WHERE reg_dates = LEFT(NOW(), 10) AND event_gubun='oreoday' ) b
					WHERE reg_date < DATE_ADD( NOW( ) , INTERVAL paycatch_winner*-1 MINUTE )
			";
			db_query($sql);

		break;


		default:
			$org = db_select("select * from tbl_board ");

			echo getMillisecond() - $start;
		break;

	}
	include "../../event/common/dbclose.php";
	

?>