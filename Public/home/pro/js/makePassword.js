$(function(){
	$('#formAct').submit(function(){
		return false;
	});

	$('#submit').click(function(){
		var user_password = $('#user_password').val();
		var new_password = $('#new_password').val();
		var verify_password = $('#verify_password').val();

		$.post('/shop/home/user/makePasswordAct',{'user_password':user_password , 'new_password':new_password , 'verify_password':verify_password},function(data){
			console.log(data);

			if(data.status){
				$('#error-info').html('<td></td><td align="center" class="color555" style="text-align:left;color:green"><i class="glyphicon glyphicon-ok-circle"></i> &nbsp;' + data.error_info + '</td>');
				$('#error-info').show('normal','linear');

				setTimeout(function(){
					top.location.href = '/shop/home/';
				},1500);
				
			}else{
				$('#error-info').html('<td></td><td align="center" class="color555" style="text-align:left;color:red"><i class="glyphicon glyphicon-remove-circle"></i> &nbsp;' + data.error_info + '</td>');
				$('#error-info').show('normal','linear');
			}
		});
	});
});