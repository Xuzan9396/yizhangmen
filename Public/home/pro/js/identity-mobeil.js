$(function(){

	var timer = null;
	var cd = 59;
	var phone = null;
	var new_phone = null;

	$('#update').click(function(){
		$(this).parent().fadeOut("slow");

		setTimeout(function(){
			$('#form').fadeIn("slow");
		},500);
	});

	// 点击验证手机号码，ajax获取验证码
	$('#user_get_code').click(function(){
		$('#error').hide(300);
		$('#error-info').hide(300,'linear');

		phone = $('#user_phone').val();

		if(cd == 59){
			$('#user_get_code').html('发送中...');
			$.post('/shop/Home/User/mobeilGetCode',{},function(data){
				if(data.status){
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

    // 点击下一步
	$('#form').submit(function(){
	    var checkStatus = true;

		$('#error').hide(300);
		$('#error-info').hide(300,'linear');

		new_phone = $('#user_phone').val();
		var user_code = $('#user_code').val();

		var res = isMobile(new_phone);
		if(!res){
			errorView('请输入正确的手机号码');
			checkStatus = false;
		}

		if(user_code == ""){
			errorView('请输入6位数字验证码');
			checkStatus = false;
		}

		if(checkStatus == true){
			// 匹配验证码
			$.post('/shop/Home/User/identityMobeilCheckCode',{'sms_code':user_code},function(data){
				if(data.status){
					$('#form').fadeOut("slow");
			
					setTimeout(function(){
						$('#update-info').html('<h4>您将修改绑定手机为：' + new_phone + ' 请核对</h4>');
						$('#affirm').fadeIn("slow");
					},500);
				}else{
					errorView('验证码错误');
				}
			});	
		}

		return false;
	});


	// 点击确认提交，发送验证码
	$('#updateAct').click(function(){

		$.post('/shop/Home/User/identityMobeilreCode',{'phone':new_phone},function(data){
			if(data.status){
				// 成功
				$('#affirm').fadeOut("slow");
			
				setTimeout(function(){
					$('#formAct').fadeIn("slow");
				},500);

			}else{
				// 错误显示错误提示
				errorView_2(data.error_info);
			}
		});
	});


	// 提交修改申请，执行修改操作
	$('#formAct').submit(function(){
		$('#error-info-2').fadeOut("slow");

		var user_code = $('#re_code').val();

		// 正则验证手机号码是否正确
		var res = isMobile(new_phone);
		
		$.post('/shop/Home/User/identityMobeil',{'user_phone':new_phone,'user_code':user_code},function(data){

			console.log(data);
			if(data.status){
				// 成功
				successView_3(data.error_info);

				// 两秒后跳转到首页
				setTimeout(function(){
					top.location = '/shop/';
				},2000);
			}else{
				// 错误显示错误提示
				errorView_3(data.error_info);

				setTimeout(function(){
					window.location = '/shop/Home/User/identityAttestation';
				},2500);
			}
		});

		return false;
		
	});

	// 错误信息显示
	function errorView_2(str){
		setTimeout(function(){
			$('#error-info-2').fadeIn("slow");
			$('#error-info-2').css('color','red');
			$('#error-info-2').html(str);
		},500);
	}

	// 正确信息显示
	function successView_2(str){
		setTimeout(function(){
			$('#error-info-2').fadeIn("slow");
			$('#error-info-2').css('color','green');
			$('#error-info-2').html(str);
		},500);
	}

	// 错误信息显示
	function errorView_3(str){
		$('#error-3').show(300);
		setTimeout(function(){
			$('#error-info-3').show(300,'linear');
		},200);

		$('#error-info-3').css('color','red');
		$('#error-info-3').html(str);
	}

	// 正确信息显示
	function successView_3(str){
		$('#error-3').show(300);
		setTimeout(function(){
			$('#error-info-3').show(300,'linear');
		},200);

		$('#error-info-3').css('color','green');
		$('#error-info-3').css('fontSize','16px');
		$('#error-info-3').html(str);
	}
});