	$(function(){
		$(".list_title ul li").click(function(){
			$(".list_title ul li").removeClass("click_lia");
			$(this).addClass("click_lia");
			
		})

		var nums=0;
		var a_banner=$(".list_banner a.list_banner_a");
		var a_btn=$(".list_img_banner ul li a");
		var a_font=$(".focus_control_font h2");
		var ul_scroll=$(".list_img_banner ul");
		function scrolls(num){
			if(num!=0){
				nums++;
				if (nums==a_banner.length) {
		          nums=0;
				}
				if(nums>=5){
					if(ul_scroll.position().left>-127*parseInt(nums-4)){
						ul_scroll.css({left:-127*parseInt(nums-4)+"px"});
					}
				}else{
					if(ul_scroll.position().left<-127*parseInt(nums)){
						ul_scroll.css({left:0});
					}
				}
			}
			a_btn.removeClass("NOw_a");
			$(a_btn[nums]).addClass("NOw_a");
			a_font.hide();
			$(a_font[nums]).show();
			a_banner.fadeOut(200);
			$(a_banner[nums]).stop();
			$(a_banner[nums]).fadeIn(200);
		}
		var t=setInterval(scrolls,3000);
		a_btn.hover(function(){
			clearInterval(t);
				var index=$(this).index(".list_img_banner ul li a");
				nums=index;
				scrolls(0);
		},function(){
			t=setInterval(scrolls,3000)
		})
		$(".btn_banner").hover(function(){
			clearInterval(t);
		},function(){
			t=setInterval(scrolls,3000)
		})
		$(".btn_banner_left").click(function(){
			var lefts=ul_scroll.position().left;
			
			if(lefts<0){
				ul_scroll.css({left:lefts+127});
			}else{
				ul_scroll.css({left:-(a_btn.length-5)*127+"px"});
			}
		})
		$(".btn_banner_right").click(function(){
			var lefts=ul_scroll.position().left;
			if(lefts<=-(a_btn.length-5)*127){
				ul_scroll.css({left:0});
			}else{
				ul_scroll.css({left:lefts-127+"px"});
			}
		})


		


	})