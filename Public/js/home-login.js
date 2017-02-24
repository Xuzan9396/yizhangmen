$(function(){
    var login_status = $('#remember').attr('status');

    $('#js_login').submit(function(){
        return false;
    });

    $('#remember').click(function(){
        if(login_status == "1"){
            login_status = 0;
        }else{
            login_status = 1;
        }
    });


    $('#submit').click(function(){
        var user_name = $('#name').val();
        var user_password = $('#user_password').val();
        var code = $('#code').val();

        $.post("/shop/Home/Login/loginAct" , {'user_name':user_name , 'user_password':user_password , 'code':code ,'login_status':login_status} , function(data){

            if(!data.status){
                $('#js-error-info').html(data.error_info);
            }else{
                $('#js-error-info').html("");
                $('#Login').modal('hide');

		        window.location.reload();
                // $('#js_loginbtn').html(data.user_account);
            }
        });

    });

});