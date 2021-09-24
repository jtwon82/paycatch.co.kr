
	var start= new Date().getTime();

$(document).ready(function() {

	mobile = false;
	var mobileKeyWords = new Array('iPhone', 'iPod', 'iPad', 'BlackBerry', 'Android', 'Windows CE', 'LG', 'MOT', 'SAMSUNG', 'SonyEricsson');
	for (var word in mobileKeyWords){
		if (navigator.userAgent.match(mobileKeyWords[word]) != null){
			mobile=true;
			break;
		}
	}

	reply.load('clear');

		Kakao.init('a0a8abba520d75cd04f3793a2aa87fee');

		Kakao.Link.createDefaultButton({
			container : '.kakao',
			objectType : 'feed',
			content : {
				title : 'PayCatch if you can!',
				description : 'NPay를 잡아라! 룰렛 이벤트 매일 10번의 기회가 주어집니다.',
				imageUrl : 'http://paycatch.co.kr/camp/roulette/img/thumb.jpg',
				link : {
					mobileWebUrl : 'http://paycatch.co.kr/camp/roulette/',
					webUrl : 'http://paycatch.co.kr/camp/roulette/'
				}
			},
			buttons : [{
				title : '이벤트 참여하기',
				link : {
					mobileWebUrl : 'http://paycatch.co.kr/camp/roulette/',
					webUrl : 'http://paycatch.co.kr/camp/roulette/'
				}
			}],
			success:function( req ){
				console.log( req );
			}
		});
		Kakao.Link.createDefaultButton({
			container : '.kakao2',
			objectType : 'feed',
			content : {
				title : 'PayCatch if you can!',
				description : 'NPay를 잡아라! 룰렛 이벤트 매일 10번의 기회가 주어집니다.',
				imageUrl : 'http://paycatch.co.kr/camp/roulette/img/thumb.jpg',
				link : {
					mobileWebUrl : 'http://paycatch.co.kr/camp/roulette/',
					webUrl : 'http://paycatch.co.kr/camp/roulette/'
				}
			},
			buttons : [{
				title : '이벤트 참여하기',
				link : {
					mobileWebUrl : 'http://paycatch.co.kr/camp/roulette/',
					webUrl : 'http://paycatch.co.kr/camp/roulette/'
				}
			}],
			success:function( req ){
				console.log( req );
			}
		});
});

	var mobile=true;
	var EVENT= function(){
		this.loginType;
		this.chk;
	};
	EVENT.prototype = {
		test : function(){

		}, AlertMsg : function(id, title, msg){
			var $id= $("#"+id);
				$id.find(".main_txt").html(title);
				$id.find(".sub_txt").html(msg);
			$("#"+id).modal({escapeClose: false, clickClose: false});

		}, OnLoad : function(option){
			$.ajax({
				type: 'POST',
				url: '_exec.php',
				data: {
					'mode' : 'OnLoad'
				},
				dataType:"json",
				success: function(req) {
				}
			});
			
		}, RegisterChk : function(option){
			if (option.loginType)
			{
				E.loginType=option.loginType;
			}

			$.ajax({
				type: 'POST', async: false,
				url: '_exec.php',
				data: { 'mode' : 'REGISTER_CHK' },
				dataType:"json",
				success: function(req) {
					try{
						$(".remain em").html(req.info.chance_cnt);
					}catch(e){}

					if (req.result=='x')
					{
						if(option.beforeLogin)
						option.beforeLogin.bind()(req);
					}
					else {
						if(option.callback)
						option.callback.bind()(req);
					}
				}
			});

		}, DoRegister: function(option){
			var f = option.form;
			var pat = /^[ㄱ-ㅎ|가-힣|a-z|A-Z|\*]+$/;
			if (/[^\s]/g.test( f.uname.value ) == 0) {
				alert("이름을 입력해주세요"); $(f.uname).focus(); return;
			}
			if ( !pat.test( f.uname.value ) ) {
				alert("이름은 한글, 영문자로 입력 가능합니다."); $(f.uname).focus(); return;
			}
			if (f.pno.value=='' || f.pno.value.length<10)
			{
				alert("전화번호를 입력해주세요"); f.pno.focus(); return;
			}
			if (!f.agree.checked)
			{
				alert("개인정보 취급방침에 동의 해주세요."); f.agree.focus(); return;
			}

			var fd = new FormData(f);
			fd.append('mode', 'DO_REGISTER');
			$.ajax({
				type: 'POST', async: false,
				url: '_exec.php',
				data : fd,
				dataType:"json",
					contentType: "application/x-www-form-urlencoded; charset=UTF-8",
					processData: false, contentType: false,
				success: function(req) {
					if (option.func)
					{
						option.func.bind()(req);
					}
				}
			});

		}, ChargeChance : function(option){
			$.ajax({
				type: 'POST', async: false,
				url: '_exec.php',
				data: { 'mode' : 'CHARGE_CHANCE'
					, 'chance_type' : option.chance_type },
				dataType:"json",
				success: function(req) {
					option.callback.bind()(req);
				}
			});

		}, StartBtn : function(option){
			$.ajax({
				type: 'POST', async: false,
				url: '_exec.php',
				data: {
					'mode' : 'CHKSTART',
					'mobile' : (mobile?'mob':'web')
				},
				dataType:"json",
				success: function(req) {
					E.chk=req.chk;
					if (req.result=='end')
					{
						if ( option.end )
						{
							option.end.bind()();
						}
					}
					else if (req.result=='o')
					{
						if ( option.start )
						{
							option.start.bind()();
						}
					}
					else if (req.result=='before')
					{
						alert( "이벤트가 시작되지 않았습니다." );
						location.reload();
					}
					else if (req.result=='x')
					{
						alert("과도한 참여로 인하여 차단 되었습니다.");
						location.reload();
					}
					else if (req.result=='lose_over_today')
					{
						alert("공유하기는 하루 10번 참여 가능하며, 매일 00시에 초기화 됩니다.");
					}
					else if (req.result=='deny')
					{
						alert("지금 참여하시는 아이피는 짧은시간에 너무 많은 응모내역이 있습니다.\n서버에 부하가되지 않는 한도 내에서 응모해주시기 바랍니다.");
					}
					else if (req.result=='9')
					{
						alert('정상적으로 이용해주세요.(브라우져를 1개 이상 열어서 참여가 불가능합니다.)\n지속적인 문제 발생시 다른 브라우져를 이용해주세요\n기타 문의사항은 이벤트 문의를 해주세요.');
					}
					if ( option.after )
					{
						//option.after.bind()();
					}
				 }
			 });

		}, ChkWinner : function(option){
			$.ajax({
				type: 'POST', async: false,
				url: '_exec.php',
				data: {
					'mode' : 'CHKWINNER',
					'user_type' : option.user_type,
					'mobile' : (mobile?'mob':'web')
				},
				dataType:"json",
				success: function(req) {
					if (req.result=='9')
					{
						alert('정상적으로 이용해주세요.(브라우져를 1개 이상 열어서 참여가 불가능합니다.)\n지속적인 문제 발생시 다른 브라우져를 이용해주세요\n기타 문의사항은 이벤트 문의를 해주세요.');
					}
					else if (req.result=='x')
					{
						alert("과도한 참여로 인하여 차단 되었습니다.");
						location.reload();
					}
					else if (req.result=='end')
					{
						alert("이벤트가 종료되었습니다.");
					}
					else if (req.result=='lose_over_today')
					{
						alert("공유하기는 하루 10번 참여 가능하며, 매일 00시에 초기화 됩니다.");
					}
					else if (req.result=='deny')
					{
						alert("지금 참여하시는 아이피는 짧은시간에 너무 많은 응모내역이 있습니다.\n서버에 부하가되지 않는 한도 내에서 응모해주시기 바랍니다.");
					}
					else if (req.result=='lose')
					{
						if ( option.lose )
						{
							option.lose.bind()(req);
						}
					}
					else if (req.result=='gift')
					{
						if ( option.gift )
						{
							option.gift.bind()(req);
						}
					}
					else if (req.result=='gift2')
					{
						if ( option.gift2 )
						{
							option.gift2.bind()(req);
						}
					}
					else if (req.result=='gift3')
					{
						if ( option.gift3 )
						{
							option.gift3.bind()(req);
						}
					}
					else if (req.result=='gift4')
					{
						if ( option.gift4 )
						{
							option.gift4.bind()(req);
						}
					}
					else
					{
						//console.log('else');
					}
					if ( option.after )
					{
						//option.after.bind()();
					}
				}
			});

		}, Gift : function(option){

			var f = option.f;
			var pat = /^[ㄱ-ㅎ|가-힣|a-z|A-Z|\*]+$/;

			if ( f.uname && /[^\s]/g.test( f.uname.value ) == 0) {
				alert("이름을 입력해주세요"); $(f.uname).focus(); return;
			}
			if ( f.uname && !pat.test( f.uname.value ) ) {
				alert("이름은 한글, 영문자로 입력 가능합니다."); $(f.uname).focus(); return;
			}
			if ( f.pno1 && (f.pno1.value=='' || f.pno1.value.length<3) )
			{
				alert("전화번호를 입력해주세요"); f.pno1.focus(); return;
			}
			if ( f.addr1 && (f.addr1.value=='' || f.addr1.value.length<5) )
			{
				alert("주소를 입력해주세요"); f.addr1.focus(); return;
			}
			if ( f.addr2 && (f.addr2.value=='' || f.addr2.value.length<1) )
			{
				alert("주소를 입력해주세요"); f.addr2.focus(); return;
			}
			if ( f.agree && !f.agree.checked)
			{
				alert("개인정보 취급방침에 동의하셔야합니다."); f.agree.focus(); return;
			}
			var obj= {};
				if(f.pno1)	obj.pno1= f.pno1.value;
				if(f.uname)	obj.uname= f.uname.value;
				if(f.addr1)	obj.addr1= f.addr1.value;
				if(f.addr2)	obj.addr2= f.addr2.value;

				//$.ajax({
				//	type: 'GET', async: false,
				//	url: '_exec.php',
				//	data: {'mode' : 'JOIN_GIFTf', 'mobile' : (mobile?'mob':'web')
				//		, uname: obj.uname, pno1:obj.pno1, addr1:obj.addr1, addr2:obj.addr2 },
				//	dataType:"json",
				//	success: function(req) {}
				//});

			$.ajax({
				type: 'POST', async: false,
				url: '_exec.php',
				data: {'mode' : 'JOIN_GIFTf', 'mobile' : (mobile?'mob':'web')
					, uname: obj.uname, pno1:obj.pno1, addr1:obj.addr1, addr2:obj.addr2 },
				dataType:"json",
				success: function(req) {
					if (req.result=='o')
					{
						$.ajax({
							type: 'POST', async: false,
							url: '_exec.php',
							data: {'mode' : 'JOIN_GIFT', 'mobile' : (mobile?'mob':'web')
								, uname: obj.uname, pno1:obj.pno1, addr1:obj.addr1, addr2:obj.addr2 },
							dataType:"json",
							success: function(req) {
								if (req.result=='o')
								{
									if ( option.succ )
									{
										option.succ.bind()();
									}
								}
								else if ( req.result=='limit3' ){
									alert('최대 당첨횟수를 초과했습니다.\n비정상적인 참여시 기존 당첨이 취소됩니다.');
								}
								else{
									alert('이용에 불편을드려 죄송합니다. 몇가지 오류로 인한 부분을 채크 부탁드립니다.\nCODE('+req.win_time+')('+req.chk+')\n1. 브라우져를 1개 이상 열어서 참여가 불가능합니다.\n2. 쿠키생성 제한에 걸려 당첨이 누락되었을 수 있습니다.\n3. 지속적인 문제 발생시 다른 브라우져를 이용해주세요.\n4. 당첨 되었지만 해당 메세지가 나온다면 캡쳐 이미지를 이벤트 문의로 보내주세요.');
								}
							}
						});
					}
					else if ( req.result=='limit3' ){
						alert('최대 당첨횟수를 초과했습니다.\n비정상적인 참여시 기존 당첨이 취소됩니다.');
					}
					else{
						alert('이용에 불편을드려 죄송합니다. 몇가지 오류로 인한 부분을 채크 부탁드립니다.\nCODE('+req.win_time+')('+req.chk+')\n1. 브라우져를 1개 이상 열어서 참여가 불가능합니다.\n2. 쿠키생성 제한에 걸려 당첨이 누락되었을 수 있습니다.\n3. 지속적인 문제 발생시 다른 브라우져를 이용해주세요.\n4. 당첨 되었지만 해당 메세지가 나온다면 캡쳐 이미지를 이벤트 문의로 보내주세요.');
					}
				}
			});


		}
	}; var E = new EVENT();


	
	
	
	
	var BOARD = function(){
		this.opt;
	};
	BOARD.prototype = {
		test : function(){

		}, List : function(p, reload){

			$.ajax({
				type: 'POST', 
				url: '_exec.php',
				data: {
					'mode' : 'LIST_REPLY',
					'page' : (p||1),
					'reload' : (reload||'')
				},
				dataType:"json",
				success: function(req){
					var $list = $("#evt2 .list ul");
					$list.empty();

					$(req.list).each(function(){
						if (!this.uname)
						{
							this.uname='DB손해보험';
						}
						var name= this.uname.substr(0,1) +'*';
						if (this.uname.length>2)
						{
							name= this.uname.substr(0,1) +'*'+ this.uname.substr(2, this.uname.length);
						}

						var html= '';
							html+='<li data-idx="'+this.idx+'">';
							html+='	<div class="box">';
							html+='		<p class="user"><em>'+name+'</em>님 · '+this.reg_dates+'</p>';
							html+='		<p class="cmt">나는 <b class="t1">'+this.answer1+'</b>에게 <b class="t2">'+this.answer2+'</b>을(를) 약속합니다</p>';
							html+='	</div>';
							html+='</li>';
						
						$list.append(html);
					});
					if (!req.list)
					{
						$list.append('<li>응모현황이 없습니다.</li>');
					}

					// paging
					$("#evt2 .paging a").prop('href', 'javascript:;');
					$("#evt2 .paging .prev a").prop('href', 'javascript:'+ req.paging.prev);
					$("#evt2 .paging .next a").prop('href', 'javascript:'+ req.paging.next);
				}
			});

		}, ListNextPrev : function(opt){

			$.ajax({
				type: 'POST',
				url: '_exec.php',
				data: {
					'mode' : 'BOARD_NEXT_PREV',
					'idx' : opt.idx,
					'direct' : opt.direct
				},
				dataType:"json",
				success: opt.proc
			});
		}

	}; var B = new BOARD();
	
	
	var reply = {e: { },
		regist: function () {
			var literal = {
				contents: { selector: $("#contents"), required: { message: "내용을 입력해주세요." } }
			};

			var checker = $.validate.rules(literal, { mode: "alert" });
			if (checker) {
				var fd = new FormData();
				fd.append('mode', 'INSERT_REPLY');
				fd.append('reply', '');
				fd.append('contents', $("#contents").val());
				//{ "id": id, "contents": $("#contents").val() }
				$.ajax({
					type: "POST",
					contentType: "application/json; charset=utf-8",
					url: "_exec.php", ///api/notice/regist",
					data: fd,
					dataType:"json",
					contentType: "application/x-www-form-urlencoded; charset=UTF-8",
					processData: false, contentType: false,
					success: function (json) {
						console.log(json);
						if (json != null) {
							if (json.result === "o") {
								reply.load('clear');
								$("#contents").val('');
							}
							else if (json.result=='invalid_ssn')
							{
								E.AlertMsg('modal-alert', '', '로그인 후 이용 가능합니다.<br>오른쪽 상단에 카카오 로그인후 이용해주세요~' );
							}
							else {
								E.AlertMsg('modal-alert', '', '오류가 발생했습니다.<br>새로고침 후 다시 시도해주세요~' );
								return false;
							}
						} else {
							E.AlertMsg('modal-alert', '', '오류가 발생했습니다.<br>새로고침 후 다시 시도해주세요~' );
							return false;
						}
					},
					error: function (jqxhr, status, error) {
						var err = "[" + jqxhr.status + "] " + jqxhr.statusText;
						E.AlertMsg('modal-alert', '', '오류가 발생했습니다.<br>새로고침 후 다시 시도해주세요~' );
					}
				});
			}
			return false;
		},
		like: function (id) {
			$.ajax({
				type: "POST",
				contentType: "application/json; charset=utf-8",
				url: "/api/notice/like",
				data: JSON.stringify({ "id": id }),
				dataType: "json",
				async: false,
				success: function (json) {
					if (json != null) {
						if (json.result === "PLUS") {
							$("#like_" + id + " > a").addClass("on");
							$("#like_" + id + " > a > img").attr("src", "/resources/img/sub/board/likeIcon2.png");
						} else {
							$("#like_" + id + " > a").removeClass("on");
							$("#like_" + id + " > a > img").attr("src", "/resources/img/sub/board/unlike.png");
						}

						$("#like_" + id + " > div > span").text(json.count);
					} else {
						E.AlertMsg('modal-alert', '', '오류가 발생했습니다.<br>새로고침 후 다시 시도해주세요~' );
						return false;
					}
				},
				error: function (jqxhr, status, error) {
					var err = "[" + jqxhr.status + "] " + jqxhr.statusText;
					E.AlertMsg('modal-alert', '', '오류가 발생했습니다.<br>새로고침 후 다시 시도해주세요~' );
				}
			});
		},
		show: function (id) {
			var $area = $("#content_"+ id);
			console.log( $area.find('div.txt').html() );
			var html = "";
			html += '<div class="write_reply">';
			html += '    <textarea placeholder="수정내용">'+ $area.find("div.txt").html().trim().replace(/<br>/gi, "\n") +'</textarea>';
			html += '    <div class="btn_area rere">';
			html += '		<span class="btn btn_gray" ><a href="javascript:reply.hide('+ id +');">취소하기</a></span>';
			html += '		<span class="btn btn_gray" ><a href="javascript:reply.modify('+ id +');">수정하기</a></span>';
			html += '    </div>';
			html += '    </div>';
			html += '</div>';

			$area.find("div.txt").hide();
			$area.find("div.iconList").hide();
			$area.find("div.view_area_info").hide();
			$area.find("div.view_area_info").after(html);

			return false;
		},
		hide: function (id) {
			var $area = $("#content_" + id);
			$area.find("div.txt").show();
			$area.find("div.iconList").show();
			$area.find("div.view_area_info").show();
			$area.find("div.view_area_info").next().remove();
		},
		modify: function (id) {
			var $area = $("#content_" + id);
			var $content = $area.find("div.write_reply > textarea");

			if ($content.val() === "") {
				alert("내용을 입력해주세요.");
				$content.focus();
				return false;
			}
			else {
				var fd = new FormData();
				fd.append('mode', 'MODIFY_REPLY');
				fd.append('idx', id);
				fd.append('contents', $content.val());

				$.ajax({
					type: "POST",
					contentType: "application/json; charset=utf-8",
					url: "_exec.php", ///api/notice/",
					data: fd,
					dataType:"json",
					contentType: "application/x-www-form-urlencoded; charset=UTF-8",
					processData: false, contentType: false,
					success: function (json) {
						if (json != null) {
							if (json.result === "o") {
								$area.find("div.txt").html(json.contents).show();
								$area.find("div.iconList").show();
								$area.find("div.view_area_info").show();
								$area.find("div.view_area_info").next().remove();
							} else {
								E.AlertMsg('modal-alert', '', '오류가 발생했습니다.<br>새로고침 후 다시 시도해주세요~' );
								return false;
							}
						} else {
							E.AlertMsg('modal-alert', '', '오류가 발생했습니다.<br>새로고침 후 다시 시도해주세요~' );
							return false;
						}
					},
					error: function (jqxhr, status, error) {
						var err = "[" + jqxhr.status + "] " + jqxhr.statusText;
						E.AlertMsg('modal-alert', '', '오류가 발생했습니다.<br>새로고침 후 다시 시도해주세요~' );
					}
				});
			}
		},
		delete: function (id) {
			if (confirm("삭제하시겠습니까?")) {
				var fd = new FormData();
				fd.append('mode', 'DELETE_REPLY');
				fd.append('idx', id);

				$.ajax({
					type: "POST",
					contentType: "application/json; charset=utf-8",
					url: "_exec.php", ///api/notice/",
					data: fd,
					dataType:"json",
					contentType: "application/x-www-form-urlencoded; charset=UTF-8",
					processData: false, contentType: false,
					success: function (json) {
						if (json != null) {
							if (json.result === "o") {
								reply.load('clear');
							} else {
								E.AlertMsg('modal-alert', '', '오류가 발생했습니다.<br>새로고침 후 다시 시도해주세요~' );
								return false;
							}
						} else {
							E.AlertMsg('modal-alert', '', '오류가 발생했습니다.<br>새로고침 후 다시 시도해주세요~' );
							return false;
						}
					},
					error: function (jqxhr, status, error) {
						var err = "[" + jqxhr.status + "] " + jqxhr.statusText;
						E.AlertMsg('modal-alert', '', '오류가 발생했습니다.<br>새로고침 후 다시 시도해주세요~' );
					}
				});
			}
		},
		answer: function (id) {
			var $area = $("#content_" + id);
			var length = $area.next("div.write_box").length;

			if (length === 0) {
				var html = "";
				$area.find("a.btn_comment_re").html("취소");
				html += '<div class="write_box" >';
				html += '    <textarea placeholder="답글을 작성해주세요."></textarea>';
				//	html += '    <div class="btn_area rere">';
				//	html += '		<div class="btn_wrap"><span class="btn btn_gray" ><a href="javascript:reply.answer_hide('+ id +');">취소하기</a></span></div>';
				html += '		<span class="btn btn_gray" ><a href="javascript:reply.add('+ id +');" style="padding:20px 0;">작성하기</a></span>';
				//	html += '	</div>';
				html += '</div>';
				$area.after(html);
			} else {
				$area.find("a.btn_comment_re").html("답글");
				$area.next("div.write_box").remove();
			}
		},
		add: function (id) {
			var $area = $("#content_" + id);
			var $content = $area.next("div.write_box").find("textarea");
			if ($content.val() === "") {
				alert("내용을 입력해주세요.");
				$content.focus();
				return false;
			}
			else {
				var fd = new FormData();
				fd.append('mode', 'INSERT_REPLY');
				fd.append('idx', id);
				fd.append('reply', 'reply');
				fd.append('contents', $content.val());

				$.ajax({
					type: "POST",
					contentType: "application/json; charset=utf-8",
					url: "_exec.php", 
					data: fd,
					dataType:"json",
					contentType: "application/x-www-form-urlencoded; charset=UTF-8",
					processData: false, contentType: false,
					success: function (json) {
						console.log(json);
						if (json != null) {
							if (json.result === "o") {
								reply.load('clear');
							}
							else if (json.result=='invalid_ssn')
							{
								E.AlertMsg('modal-alert', '', '로그인 후 이용 가능합니다.<br>오른쪽 상단에 카카오 로그인후 이용해주세요~' );
							}
							else {
								E.AlertMsg('modal-alert', '', '오류가 발생했습니다.<br>새로고침 후 다시 시도해주세요~' );
								return false;
							}
						} else {
							E.AlertMsg('modal-alert', '', '오류가 발생했습니다.<br>새로고침 후 다시 시도해주세요~' );
							return false;
						}
					},
					error: function (jqxhr, status, error) {
						var err = "[" + jqxhr.status + "] " + jqxhr.statusText;
						E.AlertMsg('modal-alert', '', '오류가 발생했습니다.<br>새로고침 후 다시 시도해주세요~' );
					}
				});
			}
		},
		load: function(clear){
			var $list = $(".board_write_wrap .view_area")

			$.ajax({
				type: 'POST', 
				url: '_exec.php',
				data: { 'mode' : 'LIST_REPLY'
					, 'clear':clear||'clear'
						},
				dataType:"json",
				beforeSend : function (xhr) {

				},
				success: function(req) {
					console.log( 'load', new Date().getTime() - start, req );

					if (clear=='clear')
					{
						$list.empty();
						//	$list.append('<span style="align:center;"><i>Loading...</i></span>');
					}
					$(req.list).each(function(id){
						var Thtml= '';

						Thtml +='<div class="content" id="content_'+this.idx+'">';
						Thtml +='	<div class="view_area_info">';
						Thtml +='		<span class="user">:uname</span>';
						Thtml +='		<div class="editBtnList">';
						Thtml +='			<a href="javascript:reply.answer('+this.idx+')" class="btn_comment_re">답글</a>';
						if(this.is_me=='Y'){
							Thtml +='			<a href="javascript:reply.show('+this.idx+')" class="btn_comment_mod"><span>|</span>수정</a>';
							Thtml +='			<a href="javascript:reply.delete('+this.idx+')" class="btn_comment_del"><span>|</span>삭제</a>';
						}
						Thtml +='		</div>';
						Thtml +='	</div>';
						Thtml +='	<div class="txt">:content</div>';
						Thtml +='	<div class="iconList">';
						Thtml +='		<div class="reply">';
						//	Thtml +='			<a href="#" class="on"><img src="img/sub/board/replyIcon2.png" alt=""></a>';
						Thtml +='			<div class="number">댓글 <span>:reply_cnt</span></div>';
						Thtml +='		</div>';
						//	Thtml +='		<div class="like">';
						//	Thtml +='			<a href="#"><img src="img/sub/board/unlike.png" alt=""></a>';
						//	Thtml +='			<div class="number">LIKE <span>56</span></div>';
						//	Thtml +='		</div>';
						Thtml +='		<span class="date">:reg_dates</span>';
						Thtml +='	</div>';
						Thtml +='</div>';

						var html= Thtml;
							html= html.replace(":uname", this.uname||'PayCatch');
							html= html.replace(":content", this.content);
							html= html.replace(":reply_cnt", this.reply_cnt);
							html= html.replace(":reg_dates", this.reg_dates);
							html= html.replace(":idx", JSON.stringify({'idx':this.idx}));

						var html_cmtArr= new Array();
						$(this.comments).each(function(id){
							var Thtml_cmt= '';
								Thtml_cmt+= '<div class="content view_reply" id="content_'+this.idx+'">';
								Thtml_cmt+= '	<div class="view_area_info">';
								//	Thtml_cmt+= '		<span class="ico"><img src="img/sub/board/replyIcon3.png" alt=""></span>';
								Thtml_cmt+= '		<span class="ico"><i class="fa fa-reply" ></i></span>';
								Thtml_cmt+= '		<span class="user">:uname_cmt</span>';
								Thtml_cmt+= '		<div class="editBtnList">';
								if(this.is_loginok=='Y'){
								//	Thtml_cmt+= '			<a href="javascript:reply.answer('+this.idx+')" class="btn_comment_re">답글</a>';
								}
								if(this.is_me=='Y'){
									Thtml_cmt+= '			<a href="javascript:reply.show('+this.idx+')" class="btn_comment_mod">수정</a>';
									Thtml_cmt+= '			<a href="javascript:reply.delete('+this.idx+')" class="btn_comment_del"><span>|</span>삭제</a>';
								}
								Thtml_cmt+= '		</div>';
								Thtml_cmt+= '	</div>';
								Thtml_cmt+= '	<div class="txt">:content_cmt</div>';
								Thtml_cmt+= '</div>';
							var html_cmt= Thtml_cmt;
								html_cmt= html_cmt.replace(":uname_cmt", this.uname);
								html_cmt= html_cmt.replace(":content_cmt", this.content);
								html_cmt= html_cmt.replace(":reg_dates", this.reg_dates);
								html_cmt= html_cmt.replace(":idx", JSON.stringify({'idx':this.idx}));
								html_cmtArr.push(html_cmt);
						});
						//	html= html.replace(":html_cmt", html_cmtArr.join(''));

						$list.append(html);
						$list.append(html_cmtArr.join(''));
					});
				}
			});
		}
	};

	
	
	
	var UTIL = function(){
		this.opt;
	};
	UTIL.prototype = {
		test : function(){


		}, is_over_byday : function(Y,M,D){
			var today= new Date(); 
			var endday= new Date();
			endday.setFullYear(Y,M-1,D);
			if( endday < today ){
				return true;
			}else{
				return false;
			}
	
		}, closeDaumPostcode : function (){
			$("#layer").hide();

		}, addressPopup : function(target){
			new daum.Postcode({
				oncomplete: function(data) {
					var fullAddr = data.address;
					var extraAddr = '';
					if(data.addressType === 'R'){
						if(data.bname !== ''){
							extraAddr += data.bname;
						}
						if(data.buildingName !== ''){
							extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
						}
						fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
					}
					
					$(target).find(" input[name=addr1]").val( "("+ data.zonecode + ") " + fullAddr ).focus();
					$(target).find(" input[name=addr2]").focus();
					$("#layer").hide();
				},
				width : '100%',
				height : '100%'
			}).embed( document.getElementById('layer') );

			$("#layer").show();

		}, createBlobData: function(item){
			var getCroppedCanvas = item.cropper('getCroppedCanvas'
				, {aspectRatio:"ignore", width:UTIL.opt.minCropBoxWidth, height:UTIL.opt.minCropBoxHeight});
			var imgData = getCroppedCanvas.toDataURL( "image/png" );
			var blobData = UTIL.processData(imgData);
			item.cropper('destroy');

			return blobData;

		}, chkWord: function(word) {
			var myregex = /씨발|씨발놈|씨발년|썅|썅놈|썅년|망할|망할놈|망할년|개새끼|미친놈|미친새끼|새끼|조까|좆까|ㅈ까|도둑놈들|염병|시발|병신|또라이|애자|개년|개놈|걸레|개또라이|지랄|제기랄|쌍판|육시럴|우라질|씹새끼|십새끼|존나|존나게|졸라|띠바|시바|개객기|돌아이|ㅂㅅ|ㅈㄲ|ㅈㄹ|씌벌|쒸이벌|ㄱㅅㄲ|ㅁㅊ|ㅅㄲ|젼나|졀라|씌벌럼|시바새끼|븅신|10새끼|섹스|빠구리|씹창|창년|창녀|창놈|스섹|색스|69|뒤치기|오르가즘|떡치기|떡치는거|SEX|쎅쓰|파워섹스|정상위|후배위|도둑놈들|일베|메갈|워마드|이명박|박근혜|홍준표|자한당|민주당|정치인|최순실|쥐명박|그네공주|순시리|순siri|문재인|노무현|대통령/gi;
			if( word.match(myregex) ){
				return false;
			}
			return true;

		}, processData : function(dataUrl) {
			var binaryString = window.atob(dataUrl.split(',')[1]);
			var arrayBuffer = new ArrayBuffer(binaryString.length);
			var intArray = new Uint8Array(arrayBuffer);
			for (var i = 0, j = binaryString.length; i < j; i++) {
				intArray[i] = binaryString.charCodeAt(i);
			}

			var data = [intArray],
				blob;

			try {
				blob = new Blob(data);
			} catch (e) {
				window.BlobBuilder = window.BlobBuilder ||
					window.WebKitBlobBuilder ||
					window.MozBlobBuilder ||
					window.MSBlobBuilder;
				if (e.name === 'TypeError' && window.BlobBuilder) {
					var builder = new BlobBuilder();
					builder.append(arrayBuffer);
					blob = builder.getBlob(imgType);
				} else {
					alert('브라우져의 버전이 낮습니다.');
				}
			}
			return blob;
		}
	}; var U = new UTIL();





	var SNS = function () {
		this.shareSns=false;
	};
	SNS.prototype = {
		ShareOk : function(e)
		{
			$.ajax({
				type: 'POST',
				url: '_exec.php',
				data: { 'mode' : 'SHARE_SNS', 'chk':E.chk, 'share_desc':e.type, 'mobile':(mobile?'mob':'web') },
				dataType:"json",
				success: function(req) {
					//closePopup();
				}
			});
		}
		, ShareFinish : function()
		{
			this.shareSns = false;
		}
		, Share : function(sns, url, txt)
		{
			var o;
			var _url = encodeURIComponent(url);
			var _txt = encodeURIComponent(txt);
			var _br  = encodeURIComponent('\r\n');
		 
			//	if (S.shareSns) return;
			switch(sns)
			{
				case 'facebook':
				//	window.open('about:blank','pop','width=500,height=700');
				//	setTimeout(function(){		S.ShareOk({'type':sns});				},3000);
					FB.ui({
					  method: 'share',
					  display: 'popup',
					  link: 'https://www.dblife-event.com/share.php',
					  href: 'https://www.dblife-event.com/share.php',
					  redirect_uri: 'http://www.dblife-event.com/fb_callback.php',
					  picture : 'https://www.dblife-event.com/images/thumb_facebook.jpg',
					  hashtag: '#DB생명 창립 32주년 기념 감사 이벤트'
					}, function(response){
						if (response && !response.error_message) {
							//공유성공
							S.ShareOk( { type:sns } );
						} else {
							
						}
					});
					break;

				case 'twitter':
					window.open('about:blank','pop','width=500,height=700');
					setTimeout(function(){		S.ShareOk({'type':sns});				},100);
					break;

				case 'story':
					window.open('about:blank','pop','width=500,height=700');
					setTimeout(function(){		S.ShareOk({'type':sns});				},100);
					break;

				case 'kakao':
					if (!mobile)
					{
						alert('모바일에서 이용해주세요.');
					}
					else {
						//	S.shareSns= true;
						//	$("#bottom .kakao").empty().html("<a data-snsname='kakao' href=\"javascript:;\" onclick=\"S.Share('kakao',''); _dbase_n_btEvent('b3');\" class=\"kakao1 btn\">카카오톡</a>");
						//	setTimeout(function(){
						//	U.bindKakao();
						//
						//	S.ShareFinish();
						//
						//	winCheck('kakao');
						//	}, 10000);

						setTimeout(function(){		S.ShareOk({'type':sns});				},3000);
					}

					break;
			}
		}
	}; var S = new SNS();

