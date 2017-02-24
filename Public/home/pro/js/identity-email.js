$(function(){
	var status = false;

	// 点击修改按钮，显示表单
	$('#update').click(function(){
		$(this).parent().fadeOut("slow");

		setTimeout(function(){
			$('#formAct').fadeIn("slow");
		},500);
	});

	// 失去焦点事件，正则匹配邮箱地址
	$('#user_mail').focusout(function(){
		var reg = /^\w{3,}@\w+(\.\w+)+$/;  

        if(!reg.test($('#user_mail').val())){
        	status = false;  
            errorView("请输入正确的邮箱地址");   
        }else{
        	status = true;
			$('#error').fadeOut("slow");
        }
	});

	// 提交修改申请，执行修改操作
	$('#formAct').submit(function(){
		if(!status){
			errorView("请输入正确的邮箱地址");
			return false;
		}else{
			successView("<i class='glyphicon glyphicon-ok'></i>&nbsp;邮件发送中...");
		}
	});

	// 错误信息显示
	function errorView(str){
		$('#error').show(300);
		setTimeout(function(){
			$('#error-info').show(300,'linear');
		},200);

		$('#error-info').css('color','red');
		$('#error-info').html(str);
	}

	// 正确信息显示
	function successView(str){
		$('#error').show(300);
		setTimeout(function(){
			$('#error-info').show(300,'linear');
		},200);

		$('#error-info').css('color','green');
		$('#error-info').css('fontSize','16px');
		$('#error-info').html(str);
	}
});