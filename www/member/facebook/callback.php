<?
	include "../../event/common/function.php";
	include "../../event/common/db.php";
	include "../../event/common/config.php";
	include_once("./facebook.php");

	/* Remove no longer needed request tokens */
	unset($_SESSION['USER']["fb_".$facebook_appId."_access_token"]);
	unset($_SESSION['USER']["fb_".$facebook_appId."_user_id"]);

	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
		'appId'  => $facebook_appId,
		'secret' => $facebook_secret,
		'cookie' => true,
	));

	// Get User ID
	$user = $facebook->getUser(); // App�� �α����� �Ǿ� �ִ��� Ȯ���ϱ� ���ؼ� ������ ������ ��û�մϴ�.

	// Login or logout url will be needed depending on current user state.
	if ($user) { // ���� ���������� �ִٸ� �ش����� ��� �Է�
print_r($user);exit;
		try {
			// Proceed knowing you have a logged in user who's authenticated.
			$user_profile = $facebook->api('/me'); // ���� �������� ���� �ɴϴ�.
			$_mb_facebook_access_token = "fb_".$facebook_appId."_access_token"; // ��ūID
			$_mb_facebook_user_id = "fb_".$facebook_appId."_user_id"; // ����ID
			// ���̽��Ͽ����� �α�������(access_token, user_id)�� fb_App ID_access_token �������� ���ǿ� ����˴ϴ�.

			//ù �α��ν� ȸ������
			if(db_count("tbl_member","email='".$user_profile[email]."'","idx")<1){

				$_SESSION['USER']['FB_NAME'] = $user_profile["name"];
				$_SESSION['USER']['FB_EMAIL'] = $user_profile["email"];

				move_page("../joinus.php");

			}else{

				$rs = db_select("select * from tbl_member where email = '".$user_profile[email]."'");

				//�α��� ���� ����
				$login_auth=db_result("select login from tbl_member_level where level_code='$rs[member_level]'");
				if($login_auth=="Y"){

					$_SESSION['USER']['LOGIN_NO'] = $rs["idx"];
					$_SESSION['USER']['LOGIN_ID'] = $rs["userid"];
					$_SESSION['USER']['LOGIN_NAME'] = $rs["name"];
					$_SESSION['USER']['LOGIN_EMAIL'] = $rs["email"];
					$_SESSION['USER']['LOGIN_LEVEL'] = $rs["member_level"];

					//�湮Ƚ�� ����
					db_query("update tbl_member set visit_time='".time()."', visit_ip='".$_SERVER['REMOTE_ADDR']."', visit_num=visit_num+1 where idx='".$rs['idx']."'");
					move_page("../../main/main.php");

				}else{
					msg_page("ȸ������ ������ �����ϴ�.","/");
				}
			}

		} catch (FacebookApiException $e) {
			//error_log($e);
			$user = null;

			$loginUrl = $facebook->getLoginUrl( array(
			'scope' => 'public_profile,email'
			));
		}
	} else { // ���� ���������� ���ٸ� ���̽��� �α��� URL�� ����
		$loginUrl = $facebook->getLoginUrl( array(
			'scope' => 'public_profile,email'
		));
		//$loginUrl="http://jungle.tendencydev.co.kr/member/joinus.php";
	}

	// �α����̵�
	header('Location: '.$loginUrl);
	exit;
?>