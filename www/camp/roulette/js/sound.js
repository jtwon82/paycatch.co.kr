$(document).ready(function() {
	
	var isHtml5Compatible = document.createElement('canvas').getContext != undefined;

	if (!isHtml5Compatible) {
		alert("브라우저 버전이 낮습니다.\n익스플로러11 이상, 크롬, 파이어폭스로 참여 가능하며\n모바일 참여를 권장합니다.");
		sound_play = function(sound) {
		}
	} else {
		
		
		var playGame = false;
		var sound_start= new Audio();

		sound_start = document.getElementById("sound_start");

		var soundEnable = true;
		sound_play = function(sound) {
			if (sound == "sound_start") {
				sound_start.currentTime = 0;
				sound_start.volume = 0.3;
				sound_start.play();
				playGame = true;
			}
		}
		setVolume = function(vol) {
			if (vol == 0) {
				sound_start.pause();
				playGame = false;
			} else {
				sound_start.play();
				sound_start.volume = 0.3;
			}

		}
		
		function handleVisibilityChange() {
			if (document.hidden) {
				soundEnable = false;
				setVolume(0);
			} else {
				if (playGame == true) {
					soundEnable = true;
					setVolume(1);
				}
			}
		}
		
		playGameFn = function(str){
			if(str == 0)playGame = false;
			if(str == 1)playGame = true;
		}
		
		
		
		document.addEventListener("visibilitychange", handleVisibilityChange, false);

		$(window).on("blur focus", function(e) {
			var prevType = $(this).data("prevType");
			if (prevType != e.type) {
				switch (e.type) {
				case "blur":
					soundEnable = false;
					setVolume(0);
					break;
				case "focus":
					if (playGame == true) {
						soundEnable = true;
						setVolume(1);
					}
					break;
				}
			}
			$(this).data("prevType", e.type);
		})
	}
	
	
	
	

})

