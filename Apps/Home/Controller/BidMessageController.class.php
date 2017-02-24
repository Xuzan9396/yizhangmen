<?php
namespace Home\Controller;

use Think\Controller;

	class BidMessageController extends CommonController 
	{
		public function index()
		{
			$messageInfo=I('post.');
			$data['bidm_pid']=$messageInfo['needuser'];
			$data['bidm_uid']=$messageInfo['mesuser'];
			$data['bidm_zid']=$messageInfo['messfloo'];
			$data['bidm_pathid']=$messageInfo['messfloo'].',';
			$data['bidm_messagecont']=$messageInfo['messcont'];
			$data['bidm_messagetime']=time();
			$data['bidm_messagestatus']=0;
			$bidMessageMod=D('bidmessage');
			if($bidMessageMod->create($data))
			{
			    $result = $bidMessageMod->add(); 
			  	if($result){
			        $insertId = $result;
			        $this->success('新增成功', U('Home/Need/needDisplay',['needid'=>$data['bidm_pid']]));
			    }else{
			    	$this->error('新增失败');
			    }
			}
		}
		//
		public function answer()
		{
			$messageInfo=I('post.');
			$data['bidm_pid']=$messageInfo['needuser'];
			$data['bidm_uid']=$messageInfo['mesuser'];
			$data['bidm_zid']=$messageInfo['messfloo'];
			$data['bidm_pathid']=$messageInfo['messfloo'].',';
			$data['bidm_messagecont']=$messageInfo['messcont'];
			$data['bidm_messagetime']=time();
			$data['bidm_messagestatus']=0;
			$bidMessageMod=D('bidmessage');
			if($bidMessageMod->create($data))
			{
			    $result = $bidMessageMod->add(); 
			  	if($result){
			        $insertId = $result;
			        $this->success('新增成功', U('Home/Need/needDisplay',['needid'=>$data['bidm_pid']]));
			    }else{
			    	$this->error('新增失败');
			    }
			}
		}
	}
