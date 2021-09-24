<?
	session_start();
	header("Content-type:text/html; charset=utf-8");
	header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

	#include($_SERVER['DOCUMENT_ROOT'] ."/admin/event/common/mysql-wrapper.php");

	#DB 접속 정보
	$db_host='118.219.232.12';
	$db_name='arteriverdb';
	$db_id='arteriverdb';
	$db_pw='rorhrl1132@';
	$connect = db_connect($db_host, $db_id, $db_pw, $db_name);
	
	if(isset($_COOKIE['SSN'])){
		$_SESSION[SSN] = $_COOKIE['SSN'];
	}else{
		$vv = getToken(15);
		setcookie('SSN', $vv, time()+(60*60*24*365), '/' );
		$_SESSION[SSN] = $vv;
	}
?>
