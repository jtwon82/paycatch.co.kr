<?
	session_start();
	include "event/common/function.php";
	include "event/common/db.php";
	if($_REQUEST['ssn'])$_SESSION['referer']= $_REQUEST['ssn'];
	include "event/common/counter.php";
?>
<!DOCTYPE html>
<html lang="ko">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=1200"/>
		<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>

		<title>PayCatch if you can!</title>
		<meta name="title" content="PayCatch if you can!" />
		<meta name="description" content="NPay를 잡아라! 매일 주어지는 룰렛 참여 기회 이번엔 내가 가져간다!" />
		<meta property="og:title" content="PayCatch if you can!"/>
		<meta property="og:description" content="NPay를 잡아라! 매일 주어지는 룰렛 참여 기회 이번엔 내가 가져간다!">
		<meta property="og:url" content="http://www.paycatch.co.kr/camp/roulette/">
		<meta name="twitter:card" content="summary">
		<meta name="twitter:title" content="PayCatch if you can!">
		<meta name="twitter:description" content="NPay를 잡아라! 매일 주어지는 룰렛 참여 기회 이번엔 내가 가져간다!">

	</head>
	<body>
<script type="text/javascript">
<!--
	location.replace("/camp/roulette/");
//-->
</script>
	</body>
</html>