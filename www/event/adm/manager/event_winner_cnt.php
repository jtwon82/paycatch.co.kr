<?
	include("../inc/head.php");
?>
<script type="text/javascript">
<!--
	$(document).ready(function(){
		$(".datepicker").datepicker({dateFormat:'yy-mm-dd'});
	});
	function setData(action, idx, fld, value){
		$.ajax({
			type: 'POST',
			url: 'event_winner_cnt_pro.php',
			data: {
				'mode' : action,
				'idx' : idx,
				'fld' : fld,
				'value' : value
			},
			dataType:"json",
			success: function(req) {
				console.log( req );
			}
		});
	}
//-->
</script>
<body>
<div id="adm_wrap">
	<!-- content -->
	<table style="width:100%;">
		<tr>
			<td id="left_area">
				<!-- 좌측메뉴 -->
				<? include "../inc/left.php"; ?>
			</td>
			<td id="content_area">
				<h1>일자별 당첨자관리</h1>
				<!-- 본문 -->
				<div class="content">

					<!-- 리스트 -->
					<div class="tableStyle1">
					<table>
							<thead>
							<tr>
								<th width="3%"> </th>
								<th width="5%">이벤트구분</th>
								<th width="10%">날짜</th>
								<th width="10%">세션</th>
								<th width="10%">당첨제한(분)</th>
								<th width="10%">당첨확율</th>
								<th width="10%">경품</th>
								<th width="10%">경품2</th>
								<th width="10%">경품3</th>
								<th width="10%">경품4</th>
								<th width="10%">경품5</th>
								<th width="2%"> </th>
							</tr>
							</thead>
							<tbody>
							<tr>
							<form name="form" id="form" action="event_winner_cnt_pro.php" method="post">
							<input type="hidden" name="mode" value="INSERT">
								<td> </td>
								<td><input type="text" class='dInput2' name="event_gubun" value="paycatch"></td>
								<td><input type="text" class='dInput2 datepicker' name="reg_dates" value=""></td>
								<td><input type="text" class='dInput2' name="ssn" value="30"></td>
								<td><input type="text" class='dInput2' name="winner" value="200"></td>
								<td><input type="text" class='dInput2' name="pct" value="40"></td>
								<td><input type="text" class='dInput2' name="gift" value="1000"></td>
								<td><input type="text" class='dInput2' name="gift2" value="2"></td>
								<td><input type="text" class='dInput2' name="gift3" value="5"></td>
								<td><input type="text" class='dInput2' name="gift4" value="1"></td>
								<td><input type="text" class='dInput2' name="gift5" value="2"></td>
								<td><span class="button black"><a href="#" onclick="document.form.submit();">추가</a></span></td>
							</form>
							</tr>
					</table>

					<!-- Button -->
					<div id="btn_top">
						<span class="button black"><a href="#" onclick="check_del(document.popup,'idx[]');return false;">선택삭제</a></span>
					</div>
					<form name="popup" id="popup" method="post" action="event_winner_cnt_pro.php">
					<input type="hidden" name="mode" value="" />
						<table>
							<thead>
							<tr>
								<th width="3%"><input type="checkbox" style="border:0" onClick="check_all(this,'idx[]')"></th>
								<th width="5%">이벤트구분</th>
								<th width="10%">날짜</th>
								<th width="10%">세션</th>
								<th width="10%">당첨제한(분)</th>
								<th width="10%">당첨확율</th>
								<th width="10%">경품</th>
								<th width="10%">경품2</th>
								<th width="10%">경품3</th>
								<th width="10%">경품4</th>
								<th width="10%">경품5</th>
								<th width="2%"> </th>
							</tr>
							</thead>
							<tbody>
						<?
							$page=$_GET['page'];

							if(!$page) $page=1;
							$list_num=15; $page_num=10;

							$start_num=($page-1)*$list_num;

							#$count=db_result("select count(idx) from tbl_gift_config ");

							$i=0;
							$pRs=db_query("select * from tbl_gift_config where event_gubun='paycatch' order by reg_dates desc ");
							while($pList=db_fetch($pRs)){
								$num = ($page-1) * $list_num + $i;
								$sortnum = $count-$num;
								$idx = $pList['idx'];
						?>
							<tr>
								<td><input name="idx[]" type="checkbox" value="<?=$pList['idx']?>" /></td>
								<td><input type="text" class='dInput2 ' name="event_gubun" value="<?=$pList['event_gubun']?>" onblur="setData('UPDATE', '<?=$pList['idx']?>', this.name, this.value)"></td>
								<td><input type="text" class='dInput2 datepicker' name="reg_dates" value="<?=$pList['reg_dates']?>" onblur="setData('UPDATE', '<?=$pList['idx']?>', this.name, this.value)"></td>
								<td><input type="text" class='dInput2' name="ssn" value="<?=$pList['ssn']?>" onblur="setData('UPDATE', '<?=$pList['idx']?>', this.name, this.value)"></td>
								<td><input type="text" class='dInput2' name="winner" value="<?=$pList['winner']?>" onblur="setData('UPDATE', '<?=$pList['idx']?>', this.name, this.value)"></td>
								<td><input type="text" class='dInput2' name="pct" value="<?=$pList['pct']?>" onblur="setData('UPDATE', '<?=$pList['idx']?>', this.name, this.value)"></td>
								<td><input type="text" class='dInput2' name="gift" value="<?=$pList['gift']?>" onblur="setData('UPDATE', '<?=$pList['idx']?>', this.name, this.value)"></td>
								<td><input type="text" class='dInput2' name="gift2" value="<?=$pList['gift2']?>" onblur="setData('UPDATE', '<?=$pList['idx']?>', this.name, this.value)"></td>
								<td><input type="text" class='dInput2' name="gift3" value="<?=$pList['gift3']?>" onblur="setData('UPDATE', '<?=$pList['idx']?>', this.name, this.value)"></td>
								<td><input type="text" class='dInput2' name="gift4" value="<?=$pList['gift4']?>" onblur="setData('UPDATE', '<?=$pList['idx']?>', this.name, this.value)"></td>
								<td><input type="text" class='dInput2' name="gift5" value="<?=$pList['gift5']?>" onblur="setData('UPDATE', '<?=$pList['idx']?>', this.name, this.value)"></td>
								<td> </td>
							</tr>
						<?
								$i++;
							}
							if($i==0){
						?>
							<tr>
								<td colspan="11">등록된 내용이 없습니다.</td>
							</tr>
						<?
							}
						?>
							</tbody>
						</table>
					</form>
					</div>

					<!-- paging -->
					<div id="paging">
						<p>
							<? #page_list($page, $count, $list_num, $page_num, $url) ?>
						</p>
					</div>
				</div>
				<!-- end -->
			</td>
		</tr>
	</table>
</div>
</body>
</html>
