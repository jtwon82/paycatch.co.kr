$(document).ready(function(){


	// 상단바 고정
	$(window).on('scroll', function() {
		var headerH = $('#header').height();
		var scrollH = $(window).scrollTop();

		if (scrollH >= headerH) {
			$('#header').addClass('fixed');
		} else {
			$('#header').removeClass('fixed');
		}
	});

    // 전체메뉴
    $('.btn_allmenu').on('click', 'a', function (e) {
        e.preventDefault();
        openPopup('.allmenu_wrap');
    });
    $('.allmenu_wrap').on('click', '.allmenu_close', function (e) {
        e.preventDefault();

        $(this).closest('.allmenu_wrap').hide();
        closePopup();
    });

    $('.popup_wrap .popup_close, .popup_wrap .btn_close').on('click', function (e) {
        e.preventDefault();

        $(this).closest('.popup_wrap').hide();
        closePopup();
    });

	$(".btn_allmenu").click(function() {
		$(".menu, html, .page_cover").addClass("open");
	});

	$('.popup_wrap .popup_close, .popup_wrap .btn_close').on('click', function(e) {
		e.preventDefault();

		$(this).closest('.popup_wrap').hide();
		closePopup();
	});


});

// 팝업
function openPopup(obj) {
	scrollY = $(window).scrollTop();

	$('html, body').css({top:-scrollY}).addClass('scroll-fiexd');
	$('body').append('<div class="dimm"></di>');
	$(".dimm").click(function() {
		closePopup();
	});
	$(obj).show();
}
function closePopup() {
	$('html, body').removeAttr('style').removeClass('scroll-fiexd');
	$('.dimm').remove();
	$('.allmenu_wrap').hide();

	$(window).scrollTop(scrollY);
}

