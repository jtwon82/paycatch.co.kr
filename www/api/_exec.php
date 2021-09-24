<?
	include "../event/common/function.php";
	include "../event/common/db.php";
	include "../event/common/config.php";




	/////// DB에 들어갈 값들을 변환합니다.

	$mode			= anti_injection($_REQUEST[mode]);
	$idx			= anti_injection($_REQUEST[idx]);
	$userno			= anti_injection($_SESSION['USER']['LOGIN_NO']);
	$ssn			= anti_injection($_SESSION['USER']['LOGIN_SSN']);
	$CHK			= anti_injection($_COOKIE['CHK']);
	$tmp_chk		= anti_injection($_REQUEST[tmp_chk]);
	$referer		= anti_injection(base64_decode($_COOKIE['from']));
	$mobile			= anti_injection($_REQUEST[mobile]);
	$facebook_id	= anti_injection($_REQUEST[facebook_id]);
	$uname			= anti_injection($_REQUEST[uname]);
	$title			= anti_injection($_REQUEST[title]);
	$content		= anti_injection($_REQUEST[content]);
	$pno			= anti_injection($_REQUEST[pno]);
	$pno1			= anti_injection($_REQUEST[pno1]);
	$pno2			= anti_injection($_REQUEST[pno2]);
	$pno3			= anti_injection($_REQUEST[pno3]);
	$userType		= anti_injection($_REQUEST[userType]);
	$addr1			= anti_injection($_REQUEST[addr1]);
	$addr2			= anti_injection($_REQUEST[addr2]);
	$addr3			= anti_injection($_REQUEST[addr3]);
	$reg_ip			= anti_injection(getUserIp());
	$pageno			= anti_injection($_REQUEST[pageno]);
	$page			= anti_injection($_REQUEST['page']);
	$sns_type		= anti_injection($_REQUEST['sns_type']);
	$tmpdata		= anti_injection($_REQUEST['tmpdata']);
	$from			= anti_injection($_REQUEST['from']);
	$callback				=anti_injection($_POST['callback']);

	$folder			= explode("/",$_SERVER['PHP_SELF']);

	/////// DB에 들어갈 값들을 정리합니다.	db_query

	switch ($mode) {


		Case "MYINFO" :
			$chance_info = info_user_chance($ssn);
			echo json_encode( array('result'=>'o', 'chance_info'=>$chance_info) );
			
		break;

		Case "INDEX_SEARCH":
			$clear		= anti_injection($_REQUEST['clear']);
			$gubun		= anti_injection($_REQUEST['gubun']);
			$step		= anti_injection($_REQUEST['step']);
			$area1		= anti_injection($_REQUEST['area1']);
			$area2		= anti_injection($_REQUEST['area2']);
			$k			= anti_injection($_REQUEST['k']);
			$q			= anti_injection($_REQUEST['q']);

			if ( $k=='a' )	$k='c.title';
			if ( $k=='b' )	$k='concat(c.title,c.content)';
			if ( $k=='c' )	$k='m.uname';

			$where[] = "c.state=1 ";
			if($gubun)	$where[]="c.gubun like '%$gubun%'";
			if($step)	$where[]="c.step='$step'";
			if($area1)	$where[]="c.area1='$area1'";
			if($area2)	$where[]="c.area2='$area2'";
			if($q)		$where[]="$k like '%$q%'";
			if($where)	$where = " where ".implode(" and ",$where);

			$order		="c.reg_date";
			$list_num	=8;

			if($clear){
				$_SESSION['USER']['scroll_idx']=0;
			}else{
				$_SESSION['USER']['scroll_idx']=($_SESSION['USER']['scroll_idx']==0 ? $list_num : $_SESSION['USER']['scroll_idx']+$list_num);
			}

			$sql="
				select 
					c.idx, c.gubun, c.title, c.landing, c.reg_date, c.landing, c.descript, sdate, edate
				from tbl_content c left join tbl_member m on c.userno = m.idx
				$where
				order by $order desc
				limit ".$_SESSION['USER']['scroll_idx'].",".$list_num;
			$rs =db_query($sql);

			$i=0;
			while($row =db_fetch($rs)){
				$i++;
				$files='';
				
				$files = get_files($row[idx]);
				$row[files] = $files;
				$list[] = $row;
				
			}

			echo json_encode( array('result'=>'o', 'list'=>$list, 'count'=>$i) );
		break;

		Case "DETAIL" :
			$t['idx']			=anti_injection($_POST['idx']);
			$sql = "select
						idx, gubun, title, content, userno, landing, reg_date, sdate, edate, descript, gift_info
					from tbl_content c
					where idx='{$t['idx']}' and c.state=1 ";
			$rs = db_select($sql);

			$files = get_files($rs[idx]);

			$rs[files] = $files;

			if($from==''){
				$chance_info = charge_chance($reg_ip, $ssn, $t[idx], 'detail');
			}
			else{
				$chance_info[chance_info] = info_user_chance($ssn, $t[idx]);
			}

			echo json_encode( array('result'=>'o', 'rs'=>$rs, 'chance_info'=>$chance_info[chance_info], 'ssn'=>$ssn) );

		break;


		Case "EVENT_ACTION" :
			if ($userno==''){
				echo json_encode( array('result'=>'not_login' ) );
			}
			else{
				$field['action']		=anti_injection($_POST['action']);
				$field['bidx']			=$idx;
				$field['userno']		=$userno;
				$field['reg_date']		=db_result("select now()");
				$field['reg_dates']		=db_result("select left(now(),10)");

				db_insert("tbl_content_action", $field);

				echo json_encode( array('result'=>'o' ) );
			}
		break;












		Case "CHK_OVERLAB_USERID" :
			$field['userid']			=anti_injection($_POST['userid']);
			$uInfo = db_select("select * from tbl_member where userid='{$field['userid']}'");
			if ( strlen($field['userid'])<5 ){
				echo json_encode( array('result'=>'short_userid' ) );
			}
			else if ( $uInfo['userid'] == $field['userid'] ){
				echo json_encode( array('result'=>'overlab_userid' ) );
			}
			else {
				echo json_encode( array('result'=>'x' ) );
			}
		break;

		Case "CHK_OVERLAB_EMAIL" :
			$field['email']			=anti_injection($_POST['email']);
			$chk_email				=preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $field[email]);
			if($userno){
				$uInfo = db_select("select * from tbl_member where email='{$field['email']}' and idx<>$userno");
			}
			else{
				$uInfo = db_select("select * from tbl_member where email='{$field['email']}'");
			}
			if ( $chk_email==false ){
				echo json_encode( array('result'=>'validate_email' ) );
			}
			else if ( strlen($field['email'])<5 ){
				echo json_encode( array('result'=>'short_email' ) );
			}
			else if ( $uInfo['email'] == $field['email'] ){
				echo json_encode( array('result'=>'overlab_email' ) );
			}
			else {
				echo json_encode( array('result'=>'x' ) );
			}
		break;


		Case "MODIFY" :
			$field['update_date']	=date("Y-m-d H:i:s");
			$field['uname']			=anti_injection($_POST['uname']);
			$field['pno']			=anti_injection($_POST['pno']);
			$field['uage']			=anti_injection($_POST['uage']);
			$field['usex']			=anti_injection($_POST['usex']);
			$field['interest']		=anti_injection($_POST['interest']);
			$field['sms_yn']		=anti_injection($_POST['sms_yn']);
			$field['email_yn']		=anti_injection($_POST['email_yn']);
			$field['email']			=anti_injection($_POST['email']);

			$t['passwd']			=anti_injection($_POST['passwd']);
			$t['passwd_temp']		=anti_injection($_POST['passwd_temp']);
			if($t[passwd]!=''){
				if($t[passwd]==$t[passwd_temp]){
					$field['passwd']	= db_password($t[passwd]);
				}
				else{
					echo json_encode( array('result'=>'neq_passwd' ) );
				}
			}

			$chance_info = charge_chance($reg_ip, $ssn, $ssn, 'modify');

			db_update("tbl_member", $field, "idx='$userno'");

			echo json_encode( array('result'=>'o', 'chance_info'=>$chance_info ) );
		break;


		Case "REGIST" :
			$t['passwd']		=anti_injection($_POST['passwd']);
			$t['passwd_temp']	=anti_injection($_POST['passwd_temp']);

			if ( $t['passwd']!=$t['passwd_temp'] ){
				echo json_encode( array('result'=>'neq_passwd' ) );
			}
			else{

				$field['reg_date']		=date("Y-m-d H:i:s");
				$field['userid']		=anti_injection($_POST['userid']);
				$field['uname']			=anti_injection($_POST['uname']);
				$field['email']			=anti_injection($_POST['email']);
				$field['pno']			=anti_injection($_POST['pno']);
				$field['passwd']		=db_password(anti_injection($_POST['passwd']));
				$field['uage']			=anti_injection($_POST['uage']);
				$field['usex']			=anti_injection($_POST['usex']);
				$field['interest']		=anti_injection($_POST['interest']);
				$field['sms_yn']		=anti_injection($_POST['sms_yn']);
				$field['email_yn']		=anti_injection($_POST['email_yn']);
				$field['member_level']	=200;
				$field['visit_date']	= date("Y-m-d H:i:s");

				$cnt = db_result("select count(*)cnt from tbl_member where userid='{$field['userid']}'");
				if( $cnt>0 ){
					echo json_encode( array('result'=>'overlab_userid' ) );
				}
				else{
					$ssn = getToken(15);
					
					$field[ssn]			=$ssn;
					db_insert("tbl_member", $field);
					$idx		=mysql_insert_id();

					#charge_chance($reg_ip, $ssn, $ssn, 'regist', 10);
					charge_chance($reg_ip, $ssn, $ssn, 'login', 10);

					setcookie('SSN', $ssn, time()+(60*60*24*365), '/' );
					/* 세션처리 */
					$_SESSION['USER']['LOGIN_SSN'] = $ssn;
					$_SESSION['USER']['LOGIN_NO'] = $idx;
					$_SESSION['USER']['LOGIN_ID'] = $field['userid'];
					$_SESSION['USER']['LOGIN_NAME'] = $field['uname'];
					$_SESSION['USER']['LOGIN_EMAIL'] = $field['email'];
					$_SESSION['USER']['LOGIN_LEVEL'] = $field['member_level'];

					echo json_encode( array('result'=>'o' ) );
				}
			}

		break;

		Case "REGIST_SNS" :
			$ssn	= getToken(15);

			$field['ssn']			=$ssn;
			$field['reg_date']		=date("Y-m-d H:i:s");
			$field['userid']		=anti_injection($_POST['userid']);
			$field['email']			=anti_injection($_POST['email']);
			$field['uname']			=anti_injection($_POST['uname']);
			$field['profile_image']	=anti_injection($_POST['profile_image']);
			$field['birthday']		=anti_injection($_POST['birthday']);
			$field['usex']			=anti_injection($_POST['usex']);
			$field['sns_regist']	=anti_injection($_POST['sns_regist']);
			$field['member_level']	=200;
			$field['visit_date']	=date("Y-m-d H:i:s");

			db_insert("tbl_member", $field);
			$idx		=mysql_insert_id();
			
			#charge_chance($reg_ip, $ssn, $ssn, 'regist', 10);
			charge_chance($reg_ip, $ssn, $ssn, 'login', 10);
			
			setcookie('SSN', $ssn, time()+(60*60*24*365), '/' );
			/* 세션처리 */
			$_SESSION['USER']['LOGIN_SSN'] = $ssn;
			$_SESSION['USER']['LOGIN_NO'] = $idx;
			$_SESSION['USER']['LOGIN_ID'] = $field['userid'];
			$_SESSION['USER']['LOGIN_NAME'] = $field['uname'];
			$_SESSION['USER']['LOGIN_EMAIL'] = $field['email'];
			$_SESSION['USER']['LOGIN_LEVEL'] = $field['member_level'];

			if( $callback ){
				echo msg_page('', $callback);
			}
			else{
				echo json_encode( array('result'=>'o') );
			}
		break;

		Case "LOGIN" :
			$field['userid']		=anti_injection($_POST['userid']);
			$field['uname']			=anti_injection($_POST['uname']);
			$field['email']			=anti_injection($_POST['email']);
			$field['passwd']		=anti_injection($_POST['passwd']);
			$field['sns_regist']	=anti_injection($_POST['sns_regist']);

			if( $field['sns_regist']!='' ){
				$mInfo = db_select("select * from tbl_member where userid='{$field[userid]}' and sns_regist='{$field[sns_regist]}'");
			}
			else{
				$mInfo = db_select("select * from tbl_member where userid='{$field[userid]}'");
			}

			if( $mInfo[sns_regist] ){
				$ssn = $mInfo[ssn];
				charge_chance($reg_ip, $ssn, $ssn, 'login', 10);

				setcookie('SSN', $ssn, time()+(60*60*24*365), '/' );
				/* 세션처리 */
				$_SESSION['USER']['LOGIN_SSN']		= $mInfo['ssn'];
				$_SESSION['USER']['LOGIN_NO']		= $mInfo['idx'];
				$_SESSION['USER']['LOGIN_ID']		= $mInfo['userid'];
				$_SESSION['USER']['LOGIN_NAME']		= $mInfo['uname'];
				$_SESSION['USER']['LOGIN_EMAIL']	= $mInfo['email'];
				$_SESSION['USER']['LOGIN_LEVEL']	= $mInfo['member_level'];

				$visit[visit_date]			= date("Y-m-d H:i:s");
				db_update("tbl_member", $visit, "idx='{$mInfo['idx']}'");

				if( $callback )
					echo msg_page('', $callback);
				else
					echo json_encode( array('result'=>'o' ) );
			}
			else{
				$passwd = db_password($field[passwd]);

				if( $mInfo && $mInfo[passwd]==$passwd ){

					$ssn = $mInfo[ssn];
					charge_chance($reg_ip, $ssn, $ssn, 'login', 10);

					setcookie('SSN', $ssn, time()+(60*60*24*365), '/' );
					/* 세션처리 */
					$_SESSION['USER']['LOGIN_SSN']		= $mInfo['ssn'];
					$_SESSION['USER']['LOGIN_NO']		= $mInfo['idx'];
					$_SESSION['USER']['LOGIN_ID']		= $mInfo['userid'];
					$_SESSION['USER']['LOGIN_NAME']		= $mInfo['uname'];
					$_SESSION['USER']['LOGIN_EMAIL']	= $mInfo['email'];

					$visit[visit_date]			= date("Y-m-d H:i:s");
					db_update("tbl_member", $visit, "idx='{$mInfo['idx']}'");

					echo json_encode( array('result'=>'o' ) );
				}
				else{
					echo json_encode( array('result'=>'x' ) );
				}
			}
		break;

		Case "SEND_EMAIL_PASSWD" :
			$field['userid']			=anti_injection($_POST['userid']);

			$mInfo = db_select("select * from tbl_member where userid='{$field['userid']}'");

			if( $mInfo[sns_regist] ){
				echo json_encode( array('result'=>'sns_user', 'platform'=>$mInfo[sns_regist] ) );
			}
			else if( !$mInfo[email] ){
				echo json_encode( array('result'=>'empty_email' ) );
			}
			else{
				$tmpPasswd = getToken(5);

				$incriptPasswd = db_password($tmpPasswd);
				db_query("update tbl_member set passwd='$incriptPasswd', passwd_temp='$tmpPasswd' where userid='{$field['userid']}' ");

				$subject= "비밀번호 {$tmpPasswd}";
				$body= "비밀번호 {$tmpPasswd} <a href='http://brosent03.cafe24.com/member/passwd_form.php?userno={$mInfo[idx]}'>변경하기</a>";

				sendMail($subject, $body, 'jjansoon', $mInfo[email] );

				echo json_encode( array('result'=>'o' ) );
			}
		break;

		Case "UPDATE_PASSWD" :
			$t['passwd_old']		=anti_injection($_POST['passwd_old']);
			$t['passwd']			=anti_injection($_POST['passwd']);
			$t['passwd_temp']		=anti_injection($_POST['passwd_temp']);

			if ($t[passwd]!=$t[passwd_temp]){
				echo json_encode( array('result'=>'neq_passwd' ) );
			}
			else{
				$userno					= $_SESSION['USER']['LOGIN_NO'];
				$mPasswd		= db_select("select passwd, passwd_temp from tbl_member where idx = '$userno'");

				if($mPasswd[passwd_temp]!=$t[passwd_old]){
					echo json_encode( array('result'=>'neq_userno' ) );
				}
				else{
					$field['passwd']		= db_password($t[passwd]);
					$field['passwd_temp']	= "";

					db_update("tbl_member", $field, "idx='$userno'");
					
					echo json_encode( array('result'=>'o' ) );
				}
			}
		break;

		Case "SHARE_SNS":
			charge_chance($reg_ip, $ssn, $tmp_chk, 'sns');
		break;

		Case "FROM_SNS_INTO":
			if( $ssn != $tmp_chk ){
				charge_chance($reg_ip, $tmp_chk, $tmp_chk, 'snsinto');
			}
		break;









		Case "RANKING_AWARD":
			$sql = "INSERT INTO tbl_ranking_score(reg_date, gamecode, gubun, userid, name1, msg, session_key, score) VALUES();";
		break;

		Case "RANKING_AWARD_UPDATE":
			db_query("TRUNCATE TABLE tbl_ranking_score;");

			$sql = "
				INSERT INTO tbl_ranking_score(reg_date, gamecode, userid, score)
					SELECT NOW() reg_date, bb.chance_type gamecode, ssn
						, CASE WHEN bb.chance_type='chance' THEN total_chance-use_chance
							WHEN bb.chance_type='sns' THEN sns_chance
							WHEN bb.chance_type='use' THEN use_chance END score 
					FROM (
						SELECT a.ssn
						, COUNT(CASE WHEN chance_type='use' THEN current END) use_chance
						, IFNULL(SUM(CASE WHEN chance_type='sns' THEN current END),0)sns_chance
						, IFNULL(SUM(CASE WHEN chance_type!='use' THEN current END),0)total_chance
						FROM tbl_chance a
						GROUP BY a.ssn
					) aa, (
						SELECT 'chance'chance_type UNION ALL SELECT 'sns' UNION ALL SELECT 'use'
					) bb;
			";
			db_query($sql);

			$sql = "
				INSERT INTO tbl_ranking_score_award( reg_date, reg_dates, weeks, gamecode, userid, rank, msg, score)
					SELECT reg_date, reg_dates, weeks, gamecode, userid, rank, msg, score
					FROM (
						SELECT reg_date, LEFT(reg_date,10)reg_dates, WEEK(reg_date) weeks, gamecode, userid
						, IF(@pre <> gamecode, @rank := 1, @rank := @rank+1) rank, msg, score, @pre := gamecode pre
						FROM tbl_ranking_score a, ( SELECT @rank :=0 )b
						WHERE WEEK(reg_date)=WEEK(NOW()) 
						ORDER BY gamecode, userid, score DESC
					) aa
					WHERE rank<11 AND WEEK(reg_date)=WEEK(NOW())
					ON DUPLICATE KEY UPDATE 
						msg=aa.msg, score=aa.score;
			";
			db_query($sql);

			$sql = "
				-- 점수 누적백업
				INSERT INTO tbl_ranking_score_bak(reg_date, gamecode, gubun, msg, score)
					SELECT reg_date, gamecode, gubun, msg, score FROM tbl_ranking_score;
			";
			#db_query($sql);

			$sql = "
				-- 이전주 점수 백업 ( award 처리가 ministock 만 빠짐. 이유를 찾자)
				TRUNCATE TABLE tbl_ranking_score_before;
				INSERT INTO tbl_ranking_score_before(reg_date, gamecode, gubun, msg, score)
					SELECT reg_date, gamecode, gubun, msg, score FROM tbl_ranking_score;
			";
			#db_query($sql);

			echo json_encode( array('result'=>'o' ) );
		break;

	}
	include "./event/common/dbclose.php";



?>