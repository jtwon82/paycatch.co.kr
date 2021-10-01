<?
	include "../../event/common/function.php";
	include "../../event/common/db.php";
	include "../../event/common/config.php";


	$client_id = "$kakao_clientid"; // 위에서 발급받은 Client ID 입력
	$client_secret = "$kakao_secret";
	$redirectURI = urlencode("http://www.paycatch.co.kr/member/kakao/callback.php"); //자신의 Callback URL 입력
	$state = "RAMDOM_STATE";
	$apiURL = "https://kauth.kakao.com/oauth/authorize?client_id=".$client_id."&redirect_uri=".$redirectURI."&response_type=code";

	$code			=anti_injection($_REQUEST['code']);

	if($code){

		$apiURL = "https://kauth.kakao.com/oauth/token";
		$params[grant_type] = "authorization_code";
		$params[client_id] = $client_id;
		$params[client_secret] = $client_secret;
		$params[redirect_uri] = $redirectURI;
		$params[code] = $code;

		$response = httpPost2($apiURL, $params);
		$res= json_decode($response, true);

		$access_token = $res[access_token];
		$refresh_token = $res[refresh_token];
		
		$headers[] = "Authorization: Bearer ". $access_token;
		$apiURL = "https://kapi.kakao.com/v2/user/me";
		$response = httpPost2($apiURL, '', $headers);

		$uInfo = json_decode($response, true);
		//	$uInfo = $res[response];

		$uInfo[id]		=$uInfo[id];
		$uInfo[uname]	=$uInfo[properties][nickname];
		$uInfo[profile_image]	=$uInfo[properties][profile_image];
		//	$uInfo[age]		=$res[properties][custom_field1];
		//	$uInfo[gender]	=$res[properties][custom_field2];

		// 첫번째 로그인이면 회원가입으로
		if(db_count("tbl_member", "userid='{$uInfo[id]}' and sns_regist='ka'", "idx")<1){
			$field[userid]		=$uInfo[id];
			$field[email]		=$uInfo[email];
			$field[uname]		=$uInfo[uname];
			$field[uage]		=$uInfo[age];
			$field[usex]		=$uInfo[gender];
			$field[profile_image]=$uInfo[profile_image];
			$field[birthday]	=$uInfo[birthday];
			$field[mode]		="REGIST_SNS";
			$field[sns_regist]	="ka";
			$field[callback]	="/camp/roulette/";

			form_submit($field, "/api/_exec.php", "post");
		}
		else{
			$field['mode']			="LOGIN";
			$field['userid']		=$uInfo[id];
			$field['email']			=$uInfo[email];
			$field['sns_regist']	="ka";
			$field['callback']		="/camp/roulette/";

			form_submit($field, "/api/_exec.php", "post");
		}
	}
	else{
		header("Location: $apiURL");
	}

?>

