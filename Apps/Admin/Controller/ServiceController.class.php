<?php

namespace Admin\Controller;

class ServiceController extends SmsController
{
	public function serviceList()
	{
		$service=D('publish');
		$data=$service->getServiceData();
		
		$this->assign($data);
		$this->display();
	}
	
	public function serviceDetail()
	{
		if(IS_GET){
			$model = D('Home/Publish');
			$list=$model->moreService();
			$desc=$model->getDescriptionData();
			$this->assign('desc',$desc);
			$this->assign('list',$list);
			$this->display();
		}
		if(IS_POST){
			$pubh=D('publish');
			$result = $pubh->checkService();
			$errorNum=$result['check']['status'];
			$infoNum=$result['check']['infonum'];

			if($infoNum==3){
				$innerTips='站内消息发送成功';
			}elseif($infoNum==4){
				$innerTips='站内消息发送失败';
			}

			if($errorNum){
				// if($errorNum==1 || $errorNum==2){
				$boolean = $this->smtp($result['arr']);
				if($boolean){
					$eamilTips='邮件消息发送成功';
				}else{
					$eamilTips='邮件消息发送失败';
				}
			}elseif($errorNum==0){
					$this->error('由于未知原因导致审核失败',U('Admin/Service/serviceList'));
					exit;

			}
			
			$this->success('审核成功,'.$innerTips.','.$eamilTips,U('Admin/Service/serviceList'),3);
		}
	}
}
