// js描述需求部分

$().ready(function(){
	// 模态框
	$('#box-template-btn').click(function(){
   		$('#box-template').modal();
    });
    
    
    // 模板
	$('#modal-title-ul li').click(function(){

		$(this).css({
			'background':'#ccc'
		});

		// 获取索引
		var num = ($(this).index());
		$('#modal-con-ol').animate({
			'margin-left': '-566' * num,
		},100);

		
	}).mouseleave(function(){
		$(this).css({
			'background':'#FAF2E8'
		});
	});

	$('#modal-insert').click(function(){	
		var mar = parseInt($('#modal-con-ol').css('margin-left'));
		//获取索引
		var j = mar / '-566';
	
		var res = $('#modal-con-ol li:eq('+j+')').html();

		var title = $('#modal-title-ul span:eq('+j+')').html();

		$('#input-title').val(title);
		editor.setContent(res);
	
	});
	//手机部分
	$('#optionsRadios1').click(function(){
	
		$('#hidden-phone').css('display','none');
		$('#optionsRadios1,#optionsRadios2').attr('name','need_phone');
		$('#input-tel').attr('name','hidden_phone');
	});
	$('#optionsRadios2').click(function(){
		$('#hidden-phone').css('display','block');
		$('#optionsRadios1,#optionsRadios2').attr('name','hidden_phone');
		$('#input-tel').attr('name','need_phone');
	});

	// 协议部分
	$('#box-hidden-a').click(function(){
		
		$('#box-hidden').slideToggle("slow");

	});
	// 调用进度条js
	$(function(){
		stepBar.init("stepBar", {
			// 目标进度
			step : 3,
			// 插件是否被选中
			change : true,
			animation : true,
			// 时间
			speed : 50
		});
	});

	//当验证input获取焦点时给点击
	$('#input-code').focus(function(){
		$('#code-msg').attr('disabled',false);
	});

	//手机验证码
	$('#code-msg').click(function(){
		var timer = null;
		var cd = 59;
		//手机号码获取
		var tel = $('#input-tel').val();
		// url地址
		var telurl = $(this).attr('codeurl');
		// 正则验证号码
		var result = isMobile(tel);
		if(result){
			if(cd == 59){
				$(this).html('发送中...');
				$(this).attr('disabled',true);
				//ajax请求
				$.post(telurl,{'need_phone':tel},function(data){
					if(data.status){
						//发送短信成功倒计时
						timer = setInterval(function(){
							$('#code-msg').html('[ ' + cd + 's ]重新发送');
							cd--;

							// 倒计时结束，重启按钮
							if(cd == 0){
								clearInterval(timer);
								$('#code-msg').html('发送验证码');
								cd = 59;
								$('#code-msg').attr('disabled',false);
							}
						},1000);
					}else{
						mo.msg('发生错误,请重新获取');
					}
				},'json');
			}else{
				mo.msg('操作频繁,请稍后再试');
			}
		}else{
			mo.msg('请输入正确的手机号码');
		}

	});

	// 验证手机号码
    function isMobile(str){   
        reg=/^1[34578]\d{9}$/;   
        if(!reg.test(str)){   
            return false;
        }else{   
            return true;
        }
    }

});
