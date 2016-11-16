var twidth;
function tts(zdt){
	twidth = document.body.clientWidth-26;
	if(twidth>500){twidth=500;}
	var ig = new Image();
	ig.src = zdt.src;	
	if(zdt.width==twidth){
		zdt.width = ig.width;
		zdt.height = ig.height;
		zdt.src = ig.src;
	}else{
		if(zdt.width>twidth){
			zdt.width = twidth;
			zdt.height = twidth/ig.width * ig.height;
		}
	}
}

function ttsv(zdt){
	if ( $(zdt).attr("srcloaded") ){
		return false;
	}
	$(zdt).attr("srcloaded","1");
	var hsrc = $(zdt).attr("src");
	if (! hsrc ){ hsrc = $(zdt).attr("data-src"); }
	if (hsrc){
		twidth = document.body.clientWidth-26;
		if(twidth>500){twidth=500;}
		var fwidth = $(zdt).width();
		var fheight = $(zdt).height();
		if(twidth!=fwidth){
			var theight = parseInt(twidth*2/3);
			$(zdt).height(theight);
			$(zdt).width(twidth);	
			hsrc = hsrc.replace(/width=[\d.]+/,"width=" + twidth).replace(/height=[\d.]+/,"height=" + theight);
			$(zdt).attr("src",hsrc);
		}else{
			var theight = fheight;
			hsrc = hsrc.replace(/width=[\d.]+/,"width=" + twidth).replace(/height=[\d.]+/,"height=" + theight);
			$(zdt).attr("src",hsrc);
		}
	}
}