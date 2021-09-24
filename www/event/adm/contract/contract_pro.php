<?
	include "../../common/function.php";
	include "../../common/db.php";
	include "../session.php";

	if($_POST['mode']=="write"){

		$field['name']=anti_injection($_POST['name']);
		$field['hp']=anti_injection($_POST['hp']);
		$field['tel']=anti_injection($_POST['tel']);
		$field['email']=anti_injection($_POST['email']);
		$field['company']=anti_injection($_POST['company']);
		$field['kind']=anti_injection($_POST['kind']);
		$field['area']=anti_injection($_POST['area']);
		$field['category']=anti_injection($_POST['category']);
		$field['site']=anti_injection($_POST['site']);
		$field['price']=anti_injection($_POST['price']);
		$field['content']=anti_injection($_POST['content']);
		$field['memo']=anti_injection($_POST['memo']);
		$field['regdate']=time();
		$field['type']=anti_injection($_POST['type']);

		if($_FILES['upfile']['name']){
			$upfile = file_upload($_FILES['upfile']['tmp_name'], $_FILES['upfile']['name'], "../../data/online",2);
			$field['upfile']=$upfile;
			$field['orgname']=$_FILES['upfile']['name'];
		}

		db_insert("tbl_online",$field);
		move_page("contract_list.php");
	}

	if($_POST['mode']=="modify"){

		$field['name']=anti_injection($_POST['name']);
		$field['hp']=anti_injection($_POST['hp']);
		$field['tel']=anti_injection($_POST['tel']);
		$field['email']=anti_injection($_POST['email']);
		$field['company']=anti_injection($_POST['company']);
		$field['kind']=anti_injection($_POST['kind']);
		$field['area']=anti_injection($_POST['area']);
		$field['category']=anti_injection($_POST['category']);
		$field['site']=anti_injection($_POST['site']);
		$field['price']=anti_injection($_POST['price']);
		$field['content']=anti_injection($_POST['content']);
		$field['memo']=anti_injection($_POST['memo']);
		$field['type']=anti_injection($_POST['type']);

		if($_FILES['upfile']['name']){
			$upfile = file_upload($_FILES['upfile']['tmp_name'], $_FILES['upfile']['name'], "../../data/online",2);
			$field['upfile']=$upfile;
			$field['orgname']=$_FILES['upfile']['name'];
		}

		db_update("tbl_online", $field, "idx='".$_POST['idx']."'");
		move_page("contract_list.php");
	}

	if($_POST['mode']=="change"){
		$idx=$_POST['idx'];
		$field[$_POST['type']]=$_POST['val'];

		db_update("tbl_online",$field,"idx='".$idx."'");
		echo "succ";
	}

	if($_POST['mode']=="check_del"){
		foreach($_POST['idx'] as $idx){
			db_query("delete from event_oreo_1601 where idx='".$idx."'");
		}

		db_query("call update_oreo_sum(-10)");

		move_page("contract_list_oreo.php");
	}

	if($_GET['mode']=="del"){
		db_query("delete from event_oreo_1601 where idx='".$_GET['idx']."'");
		move_page("contract_list_oreo.php");
	}
?>