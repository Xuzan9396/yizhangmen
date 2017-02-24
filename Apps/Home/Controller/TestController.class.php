<?php
    namespace Home\Controller;

    class TestController extends SmsController
    {
    	public function test()
    	{
    		//******************** 配置信息 ********************************
			$smtpserver = "smtp.163.com";//SMTP服务器
			$smtpserverport = 25;//SMTP服务器端口
			$smtpusermail = "15818708414@163.com";//SMTP服务器的用户邮箱
			$smtpemailto = "857268853@qq.com";//发送给谁
			$smtpuser = "15818708414@163.com";//SMTP服务器的用户帐号
			$smtppass = "sj15818708414";//SMTP服务器的用户密码
			$mailtitle = '这是邮件主题';//邮件主题
			$mailcontent = "<h1>".'这是邮件内容'."</h1>";//邮件内容
			$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
			//************************ 配置信息 ****************************
			$smtp = new \Org\Util\smtp\smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
			
			dump($smtp);

			$smtp->debug = true;//是否显示发送的调试信息
			$state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);

			echo "<div style='width:300px; margin:36px auto;'>";
			if($state==""){
				echo "对不起，邮件发送失败！请检查邮箱填写是否有误。";
				exit();
			}
			echo "恭喜！邮件发送成功！！";
			echo "</div>";
    	}
		
	}