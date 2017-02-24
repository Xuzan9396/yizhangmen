$('#store-view').click(function(){
	$('#store-view').hide();
	$('#store-selector').show();
});

$('#stock_area_item').click(function(){
	$('#store-selector').hide();
	$('#store-view').val($('#store-show').attr('title'));
	$('#store-view').show();
});