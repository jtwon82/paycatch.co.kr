<?php

// ���� ���� ������ �ݵ�� 777 �������� �Ǿ��־�� ������ �ö󰩴ϴ�.
$up_url =  'http://' . $_SERVER['HTTP_HOST'] .  '/data/ckeditor'; 						// �⺻ ���ε� URL
$up_dir = '../../data/ckeditor'; 			// �⺻ ���ε� ����
$datename =  time();								//�ߺ��� ������ ������ �ϱ� ���ؼ�

// ���ε� DIALOG ���� ���۵� ��
$funcNum = $_GET['CKEditorFuncNum'] ;
$CKEditor = $_GET['CKEditor'] ;
$langCode = $_GET['langCode'] ;

if(isset($_FILES['upload']['tmp_name']))
{
	$file_name = $_FILES['upload']['name'];
	$ext = strtolower(substr($file_name, (strrpos($file_name, '.') + 1)));
	$uploadfile = $file_dir . $datename . "." . $ext;
	/*
	������ �����̸��� ��� (�ѱ� ���� ������ ���� �����̸� ����Ͻú��� �� ����)
	�����̸��� �ߺ��� ���ϱ� ���ؼ� date �Լ��� ����Ͽ� �����̸���� ����Ͻú��ʷ� �����̸��� �����߽��ϴ� !
	����Ͻ÷��� �Ʒ� $file_name �κп� $uploadfile �� ����Ͻø� �˴ϴ� ! �����̸��� ������� ���� ���� !
	*/

	if ('jpg' != $ext && 'jpeg' != $ext && 'gif' != $ext && 'png' != $ext)
	{
		echo "<script> alert('�̹����� �����մϴ�. �ٽ� ���ε� ���ּ���.'); </script>";
		exit;
	}

	$save_dir = sprintf('%s/%s', $up_dir, $uploadfile);
	$save_url = sprintf('%s/%s', $up_url, $uploadfile);


	if (move_uploaded_file($_FILES["upload"]["tmp_name"],$save_dir))
	{
		//chmod($save_dir, 0777);			//���� ������ ����� ������� ���ּ��� !
		echo "<script>window.parent.CKEDITOR.tools.callFunction($funcNum, '$save_url', '');</script>";
	}
}
?>