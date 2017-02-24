
$().ready(function(){
	$('.jbox-cate-li').mouseover(function(){
		$(this).css({
			'background':'#FAF2E8',
			'color' : '#2793f7'
		});
		$('.box-hidden').eq($(this).index()).css({
			'display':'block'
		});
	}).mouseleave(function(){
		$(this).css({
			'background':'#5BC0DE',
			'color' : '#fafafa'
		});
		$('.box-hidden').eq($(this).index()).css({
			'display':'none'
		});
		
	});
	$('.box-hidden').mouseover(function(){
		$(this).css({
			'display':'block'
		});
	}).mouseleave(function(){
		$(this).css({
			'display':'none',
		});
	
	});

});
