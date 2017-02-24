<?php
namespace Home\Controller;

use Think\Controller;

	class BidController extends CommonController 
	{
		public function index(){
			//$needEmail=I('post.bidneedemail');
			//$needPhone=I('post.bidneedphone');
			// $data='';
	  // 		$data['toemail']='250730506@qq.com';
	  // 		$data['title']='您的需求有新的方案';
	  // 		$data['content']='请登录查看';
	  // 		$this->sendEmail($data);
			$needid=I('post.needcid');
			$bidMod=D('bid');
			//dump($result);
			if($result=$bidMod->checkData())
			{
		  		//发送邮件
		  		$cid='';
		  		$cid['user_id']=$needid;
		  		$needEmail=$this->sendUserInformation($cid,'user_email');
		  		if($needEmail !='A' )
		  		{
		  			$data='';
			  		$data['toemail']="$needEmail";
			  		$data['title']='您的需求有新的方案';
			  		$data['content']='请登录查看';
			  		$this->sendEmail($data);
		  		}
		  		//设置需求表中的need_prostaue状态为有方案
		  		$data='';
		  		$data['bid_needid']=$result;
		  		$this->setNeedStatuse($data['bid_needid'],'need_prostaue',1);//设置需求表有方案标志
		        $insertId = $result;
		        $this->success('发送成功', U('Home/Need/needDisplay',['needid'=>$data['bid_needid']]));
			}
			else
			{
				$this->error('新增失败');
			}
			
		}
		/**
		 * [getBidstatus 根据needid获取对应方案状态 0 无方案 1有方案]
		 * 用在需求记时判断
		 * @return [type] [description]
		 */
		public function  getBidstatus()
		{
			$bidInfo=I('post.');
			$bidMod=D('bid');
			$data['bid_needid']=$bidInfo['needid'];
			$result=$bidMod->where($data)->select();
			if(empty($result)){
				$this->ajaxReturn(0);
			}else{
				$this->ajaxReturn(1);
			}
		}
		//设置意向
		//设置意向以后方案发布者可以看到需求方的电话
		/**
		 * [setBidWill 需求发布方意向]
		 * @param [type] $[bid_id] [bid_projectwill]
		 */
		public function setBidWill()
		{
			$map='';
			$data='';
			$bidInfo=I('post.');
			$bidMod=D('bid');
			$map['bid_id']=array('eq',$bidInfo['setId']);
			$data['bid_projectwill']=1;
			$result=$bidMod->where($map)->save($data);
			if(empty($result)){
				$this->ajaxReturn(0);
			}else{
				//给有意向的方案提供者发送邮件
				$cid='';
		  		$cid['store_userid']=$result[0]['bid_serviceid'];
		  		$needEmail=$this->sendUserInformation($cid,'store_email','store');
		  		if($needEmail !='A' )
		  		{
		  			$data='';
			  		$data['toemail']="$needEmail";
			  		$data['title']='您的方案需求方已经查看';
			  		$data['content']='需求方对您的方案感兴趣，请登录查看对方的联系方式';
			  		$this->sendEmail($data);
		  		}
				//改变意向状态
				$data='';
				$bidlist=$bidMod->where($map)->select();
				$data['bid_needid']=$bidlist[0]['bid_needid'];
				$this->setNeedStatuse($data['bid_needid'],'need_prostepe',1);
				$this->ajaxReturn(1);
			}
		}
		//中标
		//中标以后意向，中标按钮都会失效
		/**
		 * @param [type] $[bid_id] [projecwin]
		 */

		public function setBidWin()
		{
			$map='';
			$data='';
			$bidInfo=I('get.');
			$bidMod=D('bid');
			$map['bid_id']=array('eq',$bidInfo['setId']);
			$data['bid_projecwin']=1;
			$result=$bidMod->where($map)->save($data);
			if(empty($result)){
				$this->ajaxReturn(0);
			}else{
				//给中标的方案提供者发送邮件
				$cid='';
		  		$cid['store_userid']=$result[0]['bid_serviceid'];
		  		$needEmail=$this->sendUserInformation($cid,'store_email','store');
		  		if($needEmail !='A' )
		  		{
		  			$data='';
			  		$data['toemail']="$needEmail";
			  		$data['title']='您的方案需求方已经查看';
			  		$data['content']='需求方对您的方案感兴趣，请登录查看对方的联系方式';
			  		$this->sendEmail($data);
		  		}
		  		//中标返回
				$data='';
				$bidlistone=$bidMod->where($map)->select();
				$data['bid_needid']=$bidlistone[0]['bid_needid'];
				$this->setNeedStatuse($data['bid_needid'],'need_prostepe',2);
				$this->setNeedStatuse($data['bid_needid'],'need_status',5);
				$map='';
				$data='';
				$map['bid_needid']=array('eq',$bidlistone[0]['bid_needid']);
				$data['bid_projectwill']=2;
				$list=$bidMod->where($map)->save($data);
				//创建 订单
		  		$bidN=$bidMod->creatStoreOrder($map);
		  		if($bidN)
		  		{
		  			session('home_user_info.order_status',0);
		  			$this->redirect('Home/StoreOrder/bidorderTrusteeship', array('id' =>$bidN));
		  		}
				
				//$this->ajaxReturn(1);
			}
		}
		//设置需求表need_prostaue和need_prostepe
		/**
		 * [setNeedStatuse description]
		 * @param [type] $id      [需求表ID]
		 * @param [type] $statuse [需求表字段]
		 * @param [type] $da      [设置的值]
		 */
		public function setNeedStatuse($id,$statuse,$da)
		{
			$map='';
			$data='';
			$needMode=M('need');
			$map['need_id']=array('eq',$id);
			$data["$statuse"]=$da;
			$result=$needMode->where($map)->select();
			if($result[0]["$statuse"]==$da)
			{
				return true;
			}
			else
			{
				$result=$needMode->where($map)->save($data);
				if(empty($result)){
					$this->error('操作失败');
				}else{
					return $result;
				}
			}	
		}
		//
		//
		public function upload()
		{
		    $targetFolder = 'bid/'; 
		    $data='';
		    $dataId=I('post.');
		    $data['bid_needid']=$dataId['needid'];
		    $data['bid_serviceid']=$dataId['serviceid'];
		    if (!file_exists($targetFolder)) {
		    	mkdir("$targetFolder",0755);
		    	//echo "not exist!";
		    }//else{echo 'exist!';}
			$verifyToken = md5('unique_salt' . $dataId['timestamp']);
			if (!empty($_FILES) && $dataId['token'] == $verifyToken)
			{
				$tempFile = $_FILES['Filedata']['tmp_name'];
				//echo $tempFile;
				$targetPath = $targetFolder;
				//$_SERVER['DOCUMENT_ROOT'] . 
				$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
				// Validate the file type
				$fileTypes = array('jpg','jpeg','gif','png','pdf'); // File extensions
				$fileParts = pathinfo($_FILES['Filedata']['name']);
				if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
					//----------------------------------
				    $upload = new \Think\Upload();// 实例化上传类
			        $upload->maxSize   = 4*1024*1024 ;// 设置附件上传大小    
			        $upload->exts      = array('jpg','pdf');// 设置附件上传类型
			        $upload->saveName = time().'_'.mt_rand(0,99999);//.$fileParts['filename'];
			        $upload->savePath  ="$targetFolder"; // 设置附件上传目录    // 上传文件
			        $info   =  $upload->upload();
			        if(!$info)
			        {
			        	// 上传错误提示错误信息
			            $error=$this->error($upload->getError());
			            $result=array('result'=>'error','message'=>$error);
						$this->ajaxReturn($result);
			        }else{
			        	// 上传成功       
			         	//$this->success('上传成功！');
			         	//---------------------------
			         	$filepath='Uploads'.'/'.$info['Filedata']['savepath'].$info['Filedata']['savename'];
			         	$data['bid_projectfile']=$filepath;
			         	$Down=M('bid');
			         	if($Down->create($data))
			         	{
			         		$result=$Down->add();
			         		if($result)
			         		{
			         			//返回上传成功
			         			$result=array('result'=>'ok',);
			         			$this->ajaxReturn($result);
			         		}else{
			         			$result=array('result'=>'error','message'=>'上传失败');
			         			$this->ajaxReturn($result);
			         		}
			         	}
			         	else
			         	{
			         		$result=array('result'=>'error','message'=>'上传失败');
			         		$this->ajaxReturn($result);
			         	}
			        }
				}
				else
				{
					//echo 'Invalid file type.';
					$result=array('result'=>'error','message'=>'文件不匹配');
					$this->ajaxReturn($result);
				}
			}
		}
		//检测是否已经上传
		public  function checkBidFile(){
			$DownMode=D('bid');
			if($DownMode->checkFile())
			{
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
		}
		//删除上传文件
		public function  deleteBidFile(){
			$DownMode=D('bid');
			$result=$DownMode->deleteFile();
			if($result==1){
				$this->ajaxReturn(1);
			}else if($result==2){
				$this->ajaxReturn(2);
			}else{
				$this->ajaxReturn(0);
			}
		}
		//文件下载
		public  function  downLoad(){
			// echo '<pre>';
			// print_r($_SERVER);
			// echo '</pre>';
			$filePath=I('get.downfile');
			$filePath=implode('/',explode("And", $filePath));
			//$filePath=rtrim($filePath,'.html');
			
			$filePath='Public'.'/'.$filePath;
			//dump($filePath);
			//$filePath=rtrim($filePath,'/')
			// 1.指定要下载的文件
			// $filePath = './document/2.png';
			// 2.获取MIME类型
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			// var_dump($finfo);
			$mime = finfo_file($finfo , $filePath);
			finfo_close($finfo);

			// 3.指定文件下载的类型
			header('content-type:' . $mime);
			// header('content-type:image/jpeg');

			// 4.告知浏览器，本次请求带有附件，并指定客户端下载的名字
			header('Content-Disposition:attachment;filename=' . basename($filePath));

			// 5.指定文件大小
			header('content-length:' . filesize($filePath));
			// 6.直接输出
			readfile($filePath);
		}
		//发送邮件
		//1.给对有意向的方案提供者发送邮件
		//2.给中标者发送邮件
		//3.给需求提供者发送邮件
		protected   function  sendEmail($data)
		{
			//******************** smtp邮件发送 ********************************
			$smtpserver = "smtp.163.com";//SMTP服务器
			$smtpserverport = 25;//SMTP服务器端口
			$smtpusermail = "15818708414@163.com";//SMTP服务器的用户邮箱
			$smtpemailto = $data['toemail'];//发送给谁
			$smtpuser = "15818708414@163.com";//SMTP服务器的用户帐号
			$smtppass = "sj15818708414";//SMTP服务器的用户密码
			$mailtitle = $data['title'];//邮件主题
			$mailcontent = "<h2>".$data['content']."</h2>";//邮件内容
			$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
			//************************ 配置信息 ****************************
			$smtp = new \Org\Util\smtp\smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.

			$smtp->debug = false;//是否显示发送的调试信息
			$state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);

			if($state){
				return true;
			}else{
				return false;
			}
		}
		//发送短消息
		//1.给对有意向的方案提供者发送短消息
		//2.给中标者发送短消息
		//3.给需求提供者发送消息
		public  function  sendInformation()
		{

		}
		//根据needid获取对应方案状态查询对应人的信息
		/**
		 * @param [input] $[user_id] [用户id]
		 * @param [input] $[state] [需要的字段]
		 * @param [return] $[user_] [用户信息]
		 */
		protected function sendUserInformation($id,$state,$tab='user')
		{
			// $id=39;
			// $state='user_email';
			$userMode=M("$tab");
			$result=$userMode->where($id)->select();
			if (!empty($result)) {
				return   $result[0]["$state"];
			}
			else
			{
				return  'A';
			}
		}
	}
