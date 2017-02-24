<?php

namespace Admin\Controller;

use Think\Controller;

class VipChannelController extends Controller
{
	/**
	 * @author 胡金矿<1968346304@qq.com>
	 * [newVipList 获取所有VIP推荐单]
	 */
	public function newVipList()
	{
		$needObj=D('need');
		$data=$needObj->getNeedData();
		$this->assign($data);
		$this->display();
	}
	/**
	 * @author 胡金矿<1968346304@qq.com>
	 * [recommendDetail 获取匹配推荐数据]
	 * @return [type] [description]
	 */
	
	public function recommendDetail()
	{
		if(IS_GET){
			$needObj=D('need');
			$data=$needObj->getOneNeddData();
			$this->assign($data);
			$this->display();
		}
		if(IS_POST){
			
			$shopid=I('post.id');
			$needid=I('post.needid');
			$data['shopid']=$shopid;
			$data['needid']=$needid;
			$need=D('needService');
			$map =array(
				'needid'=>array('eq', $needid),
				'shopid'=>array('eq', $shopid), 
				);
			$bool = $need->where($map)->getField('needid');
			
			if(!$bool){
			$result=$need->add($data);
				if($result){
					$errorNum=1;
					$need->where($map)->setField('status',1);
				}else{
					$errorNum=0;
				}
				$this->ajaxReturn($errorNum);
			}else{
				$need->where($map)->setField('status',1);
				$this->ajaxReturn(3);
			}
		}
	}
}
