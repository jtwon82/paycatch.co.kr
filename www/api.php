<?php	
	include "./event/common/function.php";
	include "./event/common/db.php";
	include "./event/common/config.php";




	/////// DB에 들어갈 값들을 변환합니다.

	$mode			= anti_injection($_REQUEST['mode']);
	$idx			= anti_injection($_REQUEST['idx']);
	$reg_ip			= anti_injection(getUserIp());

	$ssn			= anti_injection($_SESSION['SSN']);
	$CHK			= anti_injection($_COOKIE['CHK']);
	$referer		= anti_injection($_COOKIE['from']);
	$mobile			= anti_injection($_REQUEST['mobile']);
	$contents		= anti_injection($_REQUEST['contents']);
	$email			= anti_injection($_REQUEST['email']);
	$like			= anti_injection($_REQUEST['like']);
	$pno			= anti_injection($_REQUEST['pno']);
	$project_name	= anti_injection($_REQUEST['project_name']);
	$uname			= anti_injection($_REQUEST['uname']);

	/////// DB에 들어갈 값들을 정리합니다.

	switch ($mode) {
		Case "empty" :
		break;

		case "UPDATE_STOKC":
			$code			= anti_injection($_REQUEST['code']);
			$name_kr			= anti_injection($_REQUEST['name_kr']);
			//$name_kr= iconv('utf-8', 'euc-kr', urldecode($name_kr));
			$name_kr	= urldecode($name_kr);

			$cost			= anti_injection($_REQUEST['cost']);
			$tot_sika			= anti_injection($_REQUEST['tot_sika']);
			$tot_jasan			= anti_injection($_REQUEST['tot_jasan']);
			$beadang_cost			= anti_injection($_REQUEST['beadang_cost']);
			$oper_pct			= anti_injection($_REQUEST['oper_pct']);
			$foreign_pct			= anti_injection($_REQUEST['foreign_pct']);

			$sql = "
			insert into tbl_stock_info(reg_date, reg_dates, code, name_kr, cost, tot_sika, tot_jasan, beadang_cost, oper_pct, foreign_pct)
				values(now(), left(now(),10), '$code', '$name_kr', '$cost', '$tot_sika', '$tot_jasan', '$beadang_cost', '$oper_pct', '$foreign_pct')
				ON DUPLICATE KEY UPDATE
					name_kr='$name_kr', cost='$cost', tot_sika='$tot_sika', tot_jasan='$tot_jasan', beadang_cost='$beadang_cost', oper_pct='$oper_pct', foreign_pct='$foreign_pct'
			";
			db_query($sql);

			echo 1;
			break;
		
		case "GET_STOCK_DATA":
			$sql="select concat('https://finance.naver.com/item/main.nhn?code=',code,'&s=',reg_dates)LINK, a.*
				from tbl_stock_info a where beadang_cost >0 and a.cost/a.beadang_cost>5
				order by reg_dates desc, tot_sika desc, cost asc";
			$stock_data= db_select_list($sql);
			
			echo json_encode( array('result'=> $stock_data) );
			break;












		Case "GET_MAININFO" :
			$banner= db_select_list("SELECT a.title, a.sub_txt, a.sub_txt2, a.content, a.upfile, b.codes, b.pcodes, b.title cate_title
				FROM tbl_banner a, tbl_common b WHERE a.cate=b.codes and open='Y'
				order by idx desc
				");
			$banner2= db_select_list("
				SELECT 'wrapping.html'gubun, a.idx, a.info_subtxt, a.info_subtxt2, a.info_upfile4 FROM tbl_wrapping a
				UNION ALL
				SELECT 'billboard.html'gubun, a.idx, a.info_subtxt, a.info_subtxt2, a.info_upfile4 FROM tbl_billboard a
				");
			$main_config= db_select("select * from tbl_main_config");

			$edisplay= db_select_list("SELECT step+1 step, idx, info_subtxt, info_subtxt2, info_upfile
				FROM tbl_common c, tbl_edisplay a WHERE c.codes=a.cate1 and view_main='Y' ORDER BY a.idx desc");
			foreach($edisplay as $pList){
				$pList[files]= db_select_list("select pt_upfile from tbl_edisplay_ptfile where bidx='".$pList['idx']."' ");
				$edisplay_result[]= $pList;
			}

			echo json_encode( array('result'=>'o', 'banner'=>$banner, 'banner2'=>$banner2, 'main_config'=>$main_config, 'edisplay'=>$edisplay_result) );
		break;

		Case "GET_SIDEMENUINFO" :
			$main_config= db_select("select * from tbl_main_config");

			echo json_encode( array('result'=>'o', 'main_config'=>$main_config) );
		break;

		Case "CHKSTART" :
			$CHK = md5( rand() . time() );
			setcookie('CHK', ($CHK), 0);
			echo json_encode( array('result'=>'o', 'CHK'=>$CHK) );
		break;

		Case "INDEX_SEARCH" :
			$gubun= anti_injection($_REQUEST['gubun']);
			$step= anti_injection($_REQUEST['step']);
			$area1= anti_injection($_REQUEST['area1']);
			$clear= anti_injection($_REQUEST['clear']);

			if($gubun)	$where[]="c.gubun='$gubun'";
			if($step)	$where[]="c.step='$step'";
			if($area1)	$where[]="c.area1='$area1'";
			if($where)	$where = " where ".implode(" and ", $where);

			$order="c.idx";
			$list_num=8;

			if($clear){
				$_SESSION['scroll_idx']=0;
			}else{
				$_SESSION['scroll_idx']=($_SESSION['scroll_idx']==0 ? $list_num : $_SESSION['scroll_idx']+$list_num);
			}

			$sql="
				select *
				from tbl_content_motive c
				$where order by $order desc
				limit ".$_SESSION['scroll_idx'].",".$list_num;

			$pRs=db_query($sql);
			$i=0;
			while($row =db_fetch($pRs)){
				$i++;
				$files='';
				
				$files = get_files($row[idx]);
				$row[files] = $files;
				$list[] = $row;
				
			}

			echo json_encode( array('result'=>'o', 'list'=>$list, 'count'=>$i ) );
		break;

		Case "DETAIL" :
			$t['idx']			=anti_injection($_POST['idx']);
			$sql = "select *
					from tbl_content_motive c
					where idx='{$t['idx']}' and c.state=1 ";
			$rs = db_select($sql);

			$files = get_files($rs[idx]);

			$rs[files] = $files;

			echo json_encode( array('result'=>'o', 'rs'=>$rs, 'chance_info'=>$chance_info[chance_info], 'ssn'=>$ssn) );

		break;
		
		CASE "GET_DISPLAY_SIDE_LIST" :
			$main_config= db_select_list("SELECT * FROM tbl_main_config");
			$depth1= db_select_list("SELECT * FROM tbl_common WHERE pcodes=4");
			foreach($depth1 as $row){
				$row[depth2]= db_select_list("SELECT * FROM tbl_edisplay WHERE cate1=". $row[codes] ." order by idx desc");
				$category[]= $row;
			}
			echo json_encode(array('result'=>'o','main_config'=>$main_config[0],'category'=>$category));
		break;
		
		CASE "GET_DISPLAY_DETAIL" :
			$display_detail= db_select_assoc("SELECT * FROM tbl_edisplay WHERE idx='$idx'");
			$display_detail[pt_files]= db_select_list("select * from tbl_edisplay_ptfile where bidx='".$display_detail['idx']."' ");
			$display_detail[guide_files]= db_select_list("select * from tbl_edisplay_guidefile where bidx='".$display_detail['idx']."' ");
			echo json_encode(array('result'=>'o','display_detail'=>$display_detail));
		break;
		
		CASE "GET_WRAPPING_DETAIL" :
			$cnt= db_count("tbl_wrapping","idx='".$idx."'","idx");
			if($cnt<1){
				$where= "WHERE idx=(select max(idx) from tbl_wrapping )";
			} else {
				$where= "WHERE idx='$idx'";
			}
			$wrappingList= db_select_list("SELECT * FROM tbl_wrapping $where");
			foreach($wrappingList as $row){
				$row[pt_files]= db_select_list("select * from tbl_wrapping_ptfile where bidx='".$row['idx']."' ");
				$row[guide_files]= db_select_list("select * from tbl_wrapping_guidefile where bidx='".$row['idx']."' ");
				$row[info_subtxt5]= nl2br(str_replace(" ", "&nbsp;", ($row[info_subtxt5])));
				$result_list[]= $row;
			}
			echo json_encode(array('result'=>'o','list'=>$result_list[0]));
		break;
		
		CASE "GET_BILLBOARD_DETAIL" :
			$cnt= db_count("tbl_billboard","idx='".$idx."'","idx");
			if($cnt<1){
				$where= "WHERE idx=(select max(idx) from tbl_billboard )";
			} else {
				$where= "WHERE idx='$idx'";
			}
			$list= db_select_list("SELECT * FROM tbl_billboard $where");
			foreach($list as $row){
				$row[pt_files]= db_select_list("select * from tbl_billboard_ptfile where bidx='".$row['idx']."' ");
				$row[guide_files]= db_select_list("select * from tbl_billboard_guidefile where bidx='".$row['idx']."' ");
				$row[info_subtxt5]= nl2br(str_replace(" ", "&nbsp;", ($row[info_subtxt5])));
				$result_list[]= $row;
			}
			echo json_encode(array('result'=>'o','list'=>$result_list[0]));
		break;
		
		CASE "GET_DJPACKAGE_LIST" :
			$list= db_select_list("SELECT * FROM tbl_djpackage");
			foreach($list as $row){
				$result_list[]= $row;
			}
			echo json_encode(array('result'=>'o','list'=>$result_list));
		break;
		
		CASE "INSERT_CONTACTUS" :
			$title			= anti_injection($_REQUEST[title]);
			$pno			= anti_injection($_REQUEST[pno]);
			$content		= anti_injection($_REQUEST[content]);
			$content		= nl2br(htmlspecialchars($content));
			db_query("insert into tbl_contactus(title, pno, content, reg_ip, reg_date, reg_dates) values('$title', '$pno', '$content', '$reg_ip', now(), left(now(),10))");

			echo json_encode(array('result'=>'o'));

		break;

		default:
			echo 'Access denied';

	}
	include "../event/common/dbclose.php";

?>