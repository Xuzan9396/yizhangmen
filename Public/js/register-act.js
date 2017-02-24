$(function(){
    //第一页的发送短信按钮
    //api返回的验证码
    var verify = '';

    $('#js_phone').focus(function(){
        $('#js_phone_error').html('');
    });

    //失去焦点验证手机号码是否已被注册
    $('#js_phone').focusout(function(){
        var phone = $('#js_phone').val();
        $.post('checkPhone' , {'user_phone':phone} , function(data){
            if(!data.status){
                $('#js_phone_error').html(data.error_info);
                $('#verifyCode').show();
                $('#verifyYz').hide();
            }else{
                $('#js_phone_error').html('<label class="icon-sucessfill blank show"></label>');
                $('#verifyCode').hide();
                $('#verifyYz').show();
            }
        });
    });

    //点击获取验证码调用api
    $('#verifyYz').click(function(){
        var phone = $('#js_phone').val();

        $.post('sms' , {'user_phone':phone} , function(data){
            if(!data.status){
                data.error_info = data.error_info + '，请稍后刷新重试';
                $('#sms_error').css('color','red');
                $('#sms_error').html(data.error_info); 
            }
        });
    });

    //第一页的确定按钮
    $("#btn_part1").click(function(){

        // 用户输入的短信验证码
        var verifyNo = $('#verifyNo').val();
        
        $.post('verifySMS' , {'sms_code':verifyNo} , function(data){
            console.log(data);
            if(!data){
                $('#js_sms_error').html('验证码错误');
            }else{
                $('#js_sms_error').html('<label class="icon-sucessfill blank show"></label>');
                $(".part1").hide();
                $(".part2").show();
                $('#two').attr('class','col-xs-4 on');
            }
        });

    });

    //失去焦点验证用户名是否已被占用
    $('#adminNo').focusout(function(){
        var account = $('#adminNo').val();
        $.post('checkaccount' , {'user_account':account} , function(data){
            if(!data.status){
                $('#account_error').html(data.error_info);
            }else{
                $('#account_error').html('<label class="icon-sucessfill blank show"></label>');
            }
        });
    });

    //第二页的确定按钮
    $("#btn_part2").click(function(){
        if(!verifyCheck._click()) return;
        
        var account = $('#adminNo').val();
        var password = $('#password').val();
        var code = $('#randCode').val();

        $.post("registerAct" , {'user_account':account , 'user_phone':$('#js_phone').val() , 'user_password':password , 'code':code} , function(data){
            //验证失败，输入有错误
            if(!data.status){
                //变成绿色勾
                $('#account_error').html('<label class="icon-sucessfill blank show"></label>');
                $('#password_error').html('<label class="icon-sucessfill blank show"></label>');
                $('#code_error').html('<label class="icon-sucessfill blank show"></label>');

                if(data.error_info == '1'){
                    $('#code_error').html('验证码错误');
                }else if(data.error_info == '2'){
                    $('#account_error').html('用户名已存在');
                }else if(data.error_info == '3'){
                    $('#password_error').html('密码长度6-20位');
                }
            }else{
                $(".part2").hide();
                $(".part3").show();

                $('#js_pass').html('恭喜'+account+'，您已注册成功，现在开始您的交易之旅吧！');

                $(".step li").eq(2).addClass("on");
                countdown({
                    maxTime:10,
                    ing:function(c){
                        $("#times").text(c);
                    },
                    after:function(){
                        window.location.href="/shop/Index/index";
                    }
                });
            }
        });
    });
});
function showoutc(){$(".m-sPopBg,.m-sPopCon").show();}
function closeClause(){
    $(".m-sPopBg,.m-sPopCon").hide();
}