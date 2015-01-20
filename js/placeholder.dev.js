/*running JS on load to make sure we find the buttons needed*/
var frm		= $("#"+window.sb,opener.document).find("form.sel");
var mrcBttn	= frm.find('.mrcList span.mSel').filter('[rel='+window.mId+']');

$(window).bind('close unload beforeunload',function(){
	mrcBttn.removeClass('mSel');
});
$(function(){
	var inp		= frm.find("input, select");
	var from	= inp.filter("[name='inp_dep_arp_cd_1'], [name='airport'], [name='city1'], [name='where']").val();
	var to		= inp.filter("[name='inp_arr_arp_cd_1'], [name='arr'], [name='cr_drp_off_cty_name']").val();
	var depdate	= inp.filter("[name='dep_date'], [name='start_date']").val();
	var retdate	= inp.filter("[name='arr_date'], [name='end_date']").val();
	var adults	= parseInt(inp.filter("[name='inp_adult_pax_cnt'], [name='adults']").val());
	var children	= parseInt(inp.filter("[name='inp_child_pax_cnt'], [name='no_child'], [name='childrens']").val());
	var seniors	= parseInt(inp.filter("[name='inp_senior_pax_cnt'], [name='seniors']").val());
	var travelers	= (isNaN(adults) ? 0 : adults) + (isNaN(children) ? 0 : children) + (isNaN(seniors) ? 0 : seniors);
	travelers	= travelers ? travelers + ' ' + (travelers>1 ? "travelers" : "traveler") : false;
	$("head").append('<style type="text/css">'+
				'body{background:#ccc;background: -ms-linear-gradient(top, #ccc 0%,#ffffff 100%);background: linear-gradient(to bottom, #ccc 0%,#ffffff 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="#ccc", endColorstr="#ffffff",GradientType=0 );}'+
				'h3.tg_div{height:auto;float:none}'+
				'h3.tg_div img{margin-top:10px}'+
				'.head{height:80px;width:453px;margin:0 auto}'+
				'#silouette{top:154px}'+
				'.cont{width:453px;position:relative;background:#fff;border-radius:5px;border:1px solid #ADD8E6;margin:0 auto;text-align:center}'+
				'.cont img{margin:10px auto}'+
				'.middle{text-align:center;font-size:13px;color:#555}'+
				'.middle strong{font-size:17px}'+
				'.middle img{margin:20px 0 15px}'+
				'.valid{display:block}'+
				'hr{border:none;font-size:0;height:1px;background:#ADD8E6;margin:20px}'+
				'.bttm{padding:0 20px 20px}'+
				'h3{color:#f63;text-align:center;margin:0 0 20px;padding:0}'+
				'a{color:#000}'+
				'.bttm p{font-size:13px;text-align:justify;margin:0 0 20px}'+
				'div.cnt{text-align:center}'+
				'span.src_bttn{background:#f63;border-radius:5px;margin-top:0;display:inline-block;padding:10px 15px;font-weight:700;cursor:pointer;color:#fff}'+
				'a.goback{text-decoration:underline}'+
				'a.goback:hover{color:#f60}'+
			'</style>');
	$("body").append('<div class="cont"><div>'+
			'<img src="http://www.travelgrove.com/images/merchants/'+window.mLogo+'" alt="'+window.mName+'"><br>'+
			'<div class="middle"><strong>You are checking rates on:</strong>'+
				'<div class="details">'+
				'<span class="from">'+from+'</span>'+(to ? ' to <span class="to">'+to+'</span>' : '')+'<br>'+
				'<span class="depDate">'+depdate+'</span><span class="ow"> - <span class="retDate">'+retdate+'</span></span>'+
				(travelers ? ', <span class="travelers">'+travelers+'</span>' : '')+
			'</div><hr></div><div class="bttm">'+
				'<h3>Find the best fares now!</h3>'+
				'<p>Get the lowest price from <strong>'+window.mName+'</strong> by clicking on the search button or return to the search box and select more sites to compare.</p>'+
				'<div class="cnt"><span class="src_bttn">Compare</span></div>'+
			'</div></div></div>');
	if (window.redirectLink) {
		$("span.src_bttn").click(function(){
			window.location.replace(window.redirectLink);
		});
	} else {
		$("span.src_bttn").hide();
	}
});