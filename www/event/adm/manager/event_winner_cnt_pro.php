<?
	include "../../common/function.php";
	include "../../common/db.php";
	include "../session.php";

	$mode			= anti_injection($_REQUEST[mode]);
	$idx			= anti_injection($_REQUEST[idx]);
	$fld			= anti_injection($_REQUEST[fld]);
	$value			= anti_injection($_REQUEST[value]);

	switch ($mode) {
		Case "INSERT" :
			$event_gubun	= anti_injection($_REQUEST[event_gubun]);
			$reg_dates		= anti_injection($_REQUEST[reg_dates]);
			$winner	= anti_injection($_REQUEST[winner]);
			$ssn		= anti_injection($_REQUEST[ssn]);
			$pct		= anti_injection($_REQUEST[pct]);
			$gift		= anti_injection($_REQUEST[gift]);
			$gift2	= anti_injection($_REQUEST[gift2]);
			$gift3	= anti_injection($_REQUEST[gift3]);
			$gift4	= anti_injection($_REQUEST[gift4]);
			$gift5	= anti_injection($_REQUEST[gift5]);

			db_query("INSERT INTO tbl_gift_config(event_gubun, reg_dates, winner, ssn, pct, gift, gift2, gift3, gift4, gift5)
				VALUES('$event_gubun', '$reg_dates', '$winner', '$ssn', '$pct', '$gift', '$gift2', '$gift3', '$gift4', '$gift5')");

			move_page("event_winner_cnt.php");
		break;

		Case "UPDATE" :
			$sql = "UPDATE tbl_gift_config SET $fld='$value' WHERE idx='$idx' ";
			db_query($sql);
		break;

		Case "check_del" :
			foreach($_POST['idx'] as $idx){
				db_query("delete from tbl_gift_config where idx='".$idx."'");
			}
			move_page("event_winner_cnt.php");
		break;
	}


//	db_update("tbl_gift_config",$field);
//	move_page("site_info.php");
?>