<?
	include "../event/common/function.php";
	include "../event/common/db.php";
	include "../event/common/config.php";

	session_start();
	session_unset(); // ��� ���Ǻ����� �������� ������
	session_destroy(); // ����������

	unset_cookie('SSN');
	
	move_page("/camp/roulette/");
?>