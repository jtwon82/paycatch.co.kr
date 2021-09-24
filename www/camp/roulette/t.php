<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta name="format-detection" content="telephone=no, address=no, email=no">
    <meta property="og:type" content="article">
    <meta property="og:url" content="http://paycatch.co.kr/camp/roulette/">
    <meta property="og:title" content="paycatch">
    <meta property="og:description" content="paycatch">
    <meta property="og:image" content="http://paycatch.co.kr/camp/roulette/img/thumb.jpg">
    <meta name="twitter:image" content="http://paycatch.co.kr/camp/roulette/img/thumb.jpg">
    <title>paycatch</title>
	<link rel="stylesheet" href="/css/font-awesome.css">
<!--     <link rel="stylesheet" href="css/font.css?st=<?=rand()?>"> -->
    <link rel="stylesheet" href="css/reset.css?st=<?=rand()?>">
	<link rel="stylesheet" href="/css/font-awesome.css">
	<link href="css/swiper.min.css" rel="stylesheet" type="text/css"/>

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.3.3/gsap.min.js"></script>
	<script type="text/javascript" src="/js/jquery-library.js?st=<?=rand()?>"></script>
	<script type="text/javascript" src="js/swiper.min.js"></script>
	<script type="text/javascript" src="js/common.js?st=<?=rand()?>"></script>
	<script type="text/javascript" src="js/event.lib.js?st=<?=rand()?>"></script>

	<!-- jQuery Modal -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
    <link rel="stylesheet" href="css/common.css?st=<?=rand()?>">

	<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
	<script src="https://ads-partners.coupang.com/g.js"></script>

</head>
<body>

<script>
	$.ajax({
		type: 'POST', async: false,
		url: '_exec.php?1',
		data: { 'mode' : '' },
		dataType:"json",
		success: function(req) {
			console.log( 'default', new Date().getTime() - start, req);
		}
	});
			$.ajax({
				type: 'POST', async: false,
				url: '_exec.php?2',
				data: { 'mode' : 'CHARGE_CHANCE' },
				dataType:"json",
				success: function(req) {
					console.log( 'CHARGE_CHANCE', new Date().getTime() - start, req );
				}
			});
//-->
</script>
</body>
</html>
