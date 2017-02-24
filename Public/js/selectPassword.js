$(function(){
	var timer = null;
	var cd = 59;
	var phone = null;

	// 点击验证手机号码，ajax获取验证码
	$('#user_get_code').click(function(){
		$('#error').hide(300);
		$('#error-info').hide(300,'linear');

		//正则验证手机号码
		var result = isMobile($('#user_phone').val());
		if(result){
			if(cd == 59){
				$('#user_get_code').html('发送中...');
				$.post('/shop/Home/Login/selectPasswordGetCode',{'user_phone':$('#user_phone').val()},function(data){
					if(data.status){
						// 发送短信成功
						phone = $('#user_phone').val();

						// 发送短信成功，按钮进入倒计时
						timer = setInterval(function(){
							$('#user_get_code').html('[ ' + cd + 's ]重新发送');
							cd--;

							// 倒计时结束，重启按钮
							if(cd == 0){
								clearInterval(timer);
								$('#user_get_code').html('发送验证码');
								cd = 59;
							}
						},1000);
					}else{
						errorView(data.error_info);
					}
				});
			}else{
				errorView('请求太频繁，请稍后再试');
			}
		}else{
			errorView('请输入正确的手机号码');
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

	// 验证手机号码
    function isMobile(str){   
        reg=/^(\+\d{2,3}\-)?\d{11}$/;   
        if(!reg.test(str)){   
            return false;
        }else{   
            return true;
        }
    }

	// 阻止提交默认行为
	$('#form').submit(function(){
		$('#error').hide(300);
		$('#error-info').hide(300,'linear');

		var user_phone = $('#user_phone').val();
		var user_code = $('#user_code').val();
		var one_user_password = $('#one_user_password').val();
		var re_user_password = $('#re_user_password').val();
		// 判断验证码是否发送
		if(phone){
			// 判断与发送短信验证码的手机号码是否一致
			if(user_phone == phone){
				// 判断两次密码是否一致
				if(one_user_password == re_user_password){
					$.post('/shop/Home/Login/selectPasswordAct' , {'user_phone':phone , 'user_password':one_user_password , 'user_code':user_code} , function(data){
						if(data.status){
							// 成功显示信息
							successView(data.error_info);

							// 两秒后跳转到首页
							setTimeout(function(){
								window.location = '/shop/';
							},2000);
						}else{
							// 错误显示错误提示
							errorView(data.error_info);
						}
					});
				}else{
					errorView('输入的两次密码不一致或为空，请自行核对');
				}
			}else{
				errorView('前后验证手机号码不一致，请谨慎操作');
			}
		}else{
			errorView('验证码错误');
		}

		return false;
	});
});