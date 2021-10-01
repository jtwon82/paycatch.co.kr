<?
	//1차 메뉴 활성화
	$cateArray = array("manager","member","contract","contents","board","count","builder");
	$folder=explode("/",$_SERVER['PHP_SELF']);
	$selLeft=array_search($folder[3],$cateArray);
	

?>
<script type="text/javascript">
	$(function(){
		$(".left_menu>ul>li>p").removeClass("depth1S").addClass("depth1");
//		$(".left_menu>ul>li>p").eq("<?=$selLeft?>").addClass("depth1S");
		$(".left_menu>ul>li.<?=$folder[3]?>>p").addClass("depth1S");
		$(".left_menu>ul>li>ul").hide();
		$(".left_menu>ul ").find(".<?=$folder[3]?>").children("ul").show();
	});
</script>

<h1 style='background:url(../images/top_bg2.gif)repeat-x; height:80px; text-align:center;'> <span style='position:absolute; top:30px; font-size:20px;'>Admin</span> </h1>
<p style="text-align: right;padding-right:13px"><a href="../logout.php"><img src="../images/btn_logout.gif" alt="로그아웃"></a></p>
<p style="text-align: center;"><img src="../images/left_menu_t.gif" /></p>
<div class="left_menu">
	<ul>
<?if($_SESSION[LOGIN_LEVEL]==100){?>
		<li class='manager'>
			<p class="depth1S"><a href="../manager/site_info.php">운영관리</a></p>
			<ul class="depth2"><!-- 
				<li><a href="../manager/popup_list.php">ㆍ팝업관리</a></li> -->
				<li><a href="../manager/site_info.php">ㆍ사이트정보관리</a></li>
				<li><a href="../manager/rec_main.php" >ㆍ코드관리</a></li>
			</ul>
		</li>
		<li class="member ">
			<p class="depth1S"><a href="../member/member_list.php">회원관리</a></p>
			<ul class="depth2">
				<li><a href="../member/member_list.php">ㆍ회원목록</a></li><!-- 
				<li><a href="../member/out_list.php">ㆍ탈퇴회원관리</a></li> -->
			</ul>
		</li>
<?}?>
		<li class="contract ">
			<p class="depth1S"><a href="../contract/contract_list.php">응모내역</a></p>
			<ul class="depth2">
				<li><a href="../contract/contract_list.php">ㆍ응모내역</a></li>
				<li><a href="../contract/stats_byday.php">ㆍ일자통계</a></li>
			</ul>
		</li>
		<li class="contents ">
			<p class="depth1S"><a href="../contents/content_list.php">컨텐츠관리</a></p>
			<ul class="depth2">
				<li><a href="../contents/content_list.php">ㆍ컨텐츠 목록</a></li>
				<li><a href="../contents/content_dom_list.php">ㆍ컨텐츠 자동수집</a></li>
			</ul>
		</li>
		<li class="board ">
			<p class="depth1"><a href="../board/board_list.php?b_id=<?=db_result("select b_id from tbl_board_config order by idx")?>">게시판관리</a></p>
			<ul class="depth2">
			<?
				$bRs=db_query("select * from tbl_board_config order by idx");
				while($bList=db_fetch($bRs)){
					$list_type=($bList['b_type']==3 ? "online" : "board");
			?>
				<li><a href="../board/<?=$list_type?>_list.php?b_id=<?=$bList['b_id']?>">ㆍ<?=$bList['b_name']?></a></li>
			<?
				}
			?>
			</ul>
		</li>
		<li class="count">
			<p class="depth1"><a href="../count/pno_list.php">접속통계</a></p>
			<ul class="depth2">
				<li><a href="../count/pno_list.php">ㆍ전화번호 통계</a></li>
				<li><a href="../count/path_list.php">ㆍ검색엔진별 통계</a></li>
				<li><a href="../count/period_list.php">ㆍ기간별 통계</a></li>
			</ul>
		</li>
		<li class="builder ">
			<p class="depth1"><a href="../builder/board_list.php">빌더관리</a></p>
			<ul class="depth2">
				<li><a href="../builder/board_list.php">ㆍBUILDER</a></li>
			</ul>
		</li>
	</ul>
</div>
<p style="text-align: center;margin-bottom: 5px;"><img src="../images/left_menu_b.gif" /></p>