<?
	include("../../common/function.php");
	include("../../common/db.php");
	include("../../common/config.php");
	include("./board_config.php");
	include "../session.php";

	if($mode=="write"){
		$field['b_id']		=anti_injection($b_id);
		$field['b_code']	=anti_injection($_POST['b_code']);
		$field['userno']	=$_SESSION['LOGIN_NO'];
		$field['name']		=anti_injection($_POST['name']);
		$field['title']		=addslashes(cutstr($_POST['title'],100));
		$field['content']	=addslashes($_POST['content']);
		$field['link']		=anti_injection($_POST['link']);
		$field['notice']	=($_POST['notice'] ? $_POST['notice'] : "N");
		$field['secret']	=($_POST['secret'] ? $_POST['secret'] : "N");
		$field['ip']		=$_SERVER["REMOTE_ADDR"];
		$field['reg_date']	=date('Y-m-d H:i:s');
		$field['reg_dates']	=date('Y-m-d');

		$field['from_name']		=anti_injection($_POST['from_name']);
		$field['to_name']		=anti_injection($_POST['to_name']);
		$field['letterType']		=anti_injection($_POST['letterType']);
		$field['pno']		=anti_injection($_POST['pno']);

		//글번호 생성
		$rList = db_select("select ref from tbl_board order by ref desc");
		$ref = ($rList['ref'] ? $rList['ref']+1 : "1");
		$field['ref'] = $ref;

		db_insert("tbl_board",$field);

		//저장된 글번호
		$b_idx=mysql_insert_id();

		//첨부파일 등록
		for($i=0; $i<count($_FILES['filename']['name']); $i++){

			if($_FILES['filename']['name'][$i]) {
				$upfile = file_upload($_FILES['filename']['tmp_name'][$i], $_FILES['filename']['name'][$i], $bbs_dir);
				$upfield['b_idx'] = $b_idx;
				$upfield['filename'] = $upfile;
				$upfield['filesize'] = $_FILES['filename']['size'][$i];
				$upfield['orgname'] = $_FILES['filename']['name'][$i];
				$upfield['sortnum'] = $i+1;

				db_insert("tbl_board_file",$upfield);

				if($bbs_thumbnail=="Y" && $i==0){
					thumbnail($bbs_dir."/".$upfile, $bbs_dir."/thumb_".$upfile, $bbs_thumbwidth, $bbs_thumbheight, $bbs_thumbtype);
				}
			}
		}

		move_page("board_list.php?b_id=$b_id&b_code=$b_code");
	}


	if($mode=="reply"){

		//원글 정보 불러오기
		$idx = anti_injection($_POST['idx']);
		$org = db_select("select ref, re_level, re_step, passwd, notice from tbl_board where idx='".$_POST['idx']."'");

		//댓글 순서 한 칸씩 밀기
		db_query("update tbl_board set re_step=re_step-1 where ref='".$org['ref']."' and re_step>'".$org['re_level']."'");

		//글저장
		$field['b_id']=anti_injection($b_id);
		$field['b_code']=anti_injection($_POST['b_code']);
		$field['userno']=$_SESSION['LOGIN_NO'];
		$field['name']=anti_injection($_POST['name']);
		$field['title']		=addslashes(cutstr($_POST['title'],100));
		$field['content']	=addslashes($_POST['content']);
		$field['passwd']=$org['passwd'];
		$field['notice']=$org['notice'];
		$field['secret']=($_POST['secret'] ? $_POST['secret'] : "N");
		$field['ip']=$_SERVER["REMOTE_ADDR"];
		$field['reg_date']=date('Y-m-d H:i:s');
		$field['reg_dates']	=date('Y-m-d');
		$field['ref'] = $org['ref'];
		$field['re_level'] = $org['re_level'] + 1;
		$field['re_step'] = $org['re_step'] - 1;

		$field['from_name']		=anti_injection($_POST['from_name']);
		$field['to_name']		=anti_injection($_POST['to_name']);
		$field['letterType']		=anti_injection($_POST['letterType']);
		$field['pno']		=anti_injection($_POST['pno']);

		db_insert("tbl_board",$field);

		//저장된 글번호
		$b_idx=mysql_insert_id();

		//첨부파일 등록
		for($i=0; $i<count($_FILES['filename']['name']); $i++){
			if($_FILES['filename']['name'][$i]) {
				$upfile = file_upload($_FILES['filename']['tmp_name'][$i], $_FILES['filename']['name'][$i], $bbs_dir);
				$upfield['b_idx'] = $b_idx;
				$upfield['filename'] = $upfile;
				$upfield['filensize'] = $_FILES['filename']['size'][$i];
				$upfield['orgname'] = $_FILES['filename']['name'][$i];
				$upfield['sortnum'] = $i+1;

				db_insert("tbl_board_file",$upfield);

				if($bbs_thumbnail=="Y" && $i==0){
					thumbnail($bbs_dir."/".$upfile, $bbs_dir."/thumb_".$upfile, $bbs_thumbwidth, $bbs_thumbheight, $bbs_thumbtype);
				}
			}
		}

		move_page("board_list.php?b_id=$b_id&b_code=$_POST[b_code]&page=$_POST[page]&search_key=$_POST[search_key]&search_val=$_POST[search_val]");
	}


	if($mode=="modify"){

		$field['b_code']	=anti_injection($_POST['b_code']);
		$field['name']		=anti_injection($_POST['name']);
		$field['title']		=addslashes(cutstr($_POST['title'],100));
		$field['content']	=addslashes($_POST['content']);
		$field['notice']	=($_POST['notice'] ? $_POST['notice'] : "N");
		$field['secret']	=anti_injection($_POST['secret']);

		$field['from_name']		=anti_injection($_POST['from_name']);
		$field['to_name']		=anti_injection($_POST['to_name']);
		$field['letterType']		=anti_injection($_POST['letterType']);
		$field['pno']		=anti_injection($_POST['pno']);

		db_update("tbl_board", $field, "idx='".$_POST['idx']."'");

		//첨부파일 개별 삭제
		for($i=0; $i<count($_POST['filedel']); $i++){
			$fList=db_select("select filename from tbl_board_file where b_idx='".$_POST['idx']."' and sortnum='".$_POST['filedel'][$i]."'");
			file_delete($fList['filename'], $bbs_dir);
			db_query("delete from tbl_board_file where b_idx='".$_POST['idx']."' and sortnum='".$_POST['filedel'][$i]."'");
		}

		//첨부파일 수정
		for($i=0; $i<count($_FILES['filename']['name']); $i++){

			if($_FILES['filename']['name'][$i]) {
				$upfile = file_upload($_FILES['filename']['tmp_name'][$i], $_FILES['filename']['name'][$i], $bbs_dir);
				$upfield['filename'] = $upfile;
				$upfield['filesize'] = $_FILES['filename']['size'][$i];
				$upfield['orgname'] = $_FILES['filename']['name'][$i];
				$upfield['b_idx'] = $_POST['idx'];
				$upfield['sortnum'] = $i+1;

				if($bbs_thumbnail=="Y" && $i==0){
					thumbnail($bbs_dir."/".$upfile, $bbs_dir."/thumb_".$upfile, $bbs_thumbwidth, $bbs_thumbheight, $bbs_thumbtype);
				}

				db_query("delete from tbl_board_file where b_idx='".$_POST['idx']."' and sortnum='".($i+1)."'");
				db_insert("tbl_board_file",$upfield);
			}
		}

		move_page("board_list.php?b_id=$b_id&b_code=$_POST[b_code]&page=$_POST[page]&search_key=$_POST[search_key]&search_val=$_POST[search_val]");
	}


	if($mode=="check_del"){

		foreach($_POST['idx'] as $idx){

			//삭제할 첨부파일
			$fRs=db_query("select filename from tbl_board_file where b_idx='".$idx."'");
			while($fList=db_fetch($fRs)){
				file_delete($fList['filename'], $bbs_dir);
			}
			db_query("delete from tbl_board where idx='".$idx."'");
			db_query("delete from tbl_board_file where b_idx='".$idx."'");
		}
		move_page("board_list.php?b_id=$b_id&b_code=$_POST[b_code]&page=$_POST[page]&search_key=$_POST[search_key]&search_val=$_POST[search_val]");
	}


	if($mode=="del"){

		//삭제할 첨부파일
		$fRs=db_query("select filename from tbl_board_file where b_idx='".$_GET['idx']."'");
		while($fList=db_fetch($fRs)){
			file_delete($fList['filename'], $bbs_dir);
		}
		db_query("delete from tbl_board where idx='".$_GET['idx']."'");
		db_query("delete from tbl_board_file where b_idx='".$_GET['idx']."'");

		move_page("board_list.php?b_id=$b_id&b_code=$_GET[b_code]&page=$_GET[page]&search_key=$_GET[search_key]&search_val=$_GET[search_val]");
	}


	if($mode=="comment"){

		$field['b_idx']=anti_injection($_POST['idx']);
		$field['userno']=$_SESSION['LOGIN_NO'];
		$field['name']=anti_injection($_POST['cname']);
		$field['ip']=$_SERVER["REMOTE_ADDR"];
		$field['content']=$_POST['content'];
		$field['reg_date']=time();

		db_insert("tbl_board_comment",$field);
		db_query("update tbl_board set comment=comment+1 where idx='".$field['b_idx']."'");

		move_page(urldecode($_POST['reurl']));
	}


	if($mode=="comment_del"){

		db_query("delete from tbl_board_comment where idx='".$_GET['cidx']."'");
		db_query("update tbl_board set comment=comment-1 where idx='".$_GET['idx']."'");

		move_page(urldecode($_GET['reurl']));
	}
?>