<?php
namespace Home\Model;

use Think\Model;

	class BidModel extends Model
	{
		protected $_auto = array (
				//把数字字符串转换为float类型
				array('bid_projectprice','floatval',3,'function'),
		          //array('status','1'),  // 新增的时候把status字段设置为1         
		          //array('password','md5',3,'function') , // 对password字段在新增和编辑的时候使md5函数处理         
		          //array('name','getName',3,'callback'), // 对name字段在新增和编辑的时候回调getName方法         
		          //array('update_time','time',2,'function'), // 对update_time字段在更新的时候写入当前时间戳     
		          );
		protected $_validate = array(
			//array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
			//对方案表id有则验证
			array('bid_id','/^\d{1,}$/','没有些方案!',0,'regex'),
			array('bid_id','','没有些方案!',0,'unique',1),
			//需求表id验证
			array('bid_needid','/^\d{1,}$/','没有些需求!',0,'regex'),
			//方案表id验证
			array('bid_serviceid','/^\d{1,}$/','没有些提交人!',0,'regex'),
			//价格验证
			array('bid_projectprice','checkPrice','价格不合法！',0,'function'),
			//周期验证
			array('bid_projectcyc','checkPrice','周期不合法',0,'function'),
			//地点进行中文验证
			array('bid_projectplace','/^[\x{4e00}-\x{9fa5}]{1,24}+(\,)+[\x{4e00}-\x{9fa5}]{1,24}+(\,)+[\x{4e00}-\x{9fa5}]{1,24}$/u','地点输入不合法','regex'),
			//方案描述验证
			//array('bid_projectdis','/\w{2,500}/','方案描述不合法！',0,'regex'),
			//是否存在上传文件进行验证
			//array('bid_projectfile','require','验证码必须！'),
			//是否隐藏状态验证
			array('bid_projecthide',array(0,1),'是否隐藏状态不正确',0,'in'),
			);

		public function checkData()
		{
			$bidInfo=I('post.');
			$data='';
			$data['bid_needid']=$bidInfo['needuser_id'];
			$data['bid_serviceid']=$bidInfo['biduser_id'];
			$data['bid_projectprice']=$bidInfo['price'];
			$data['bid_projectcyc']=$bidInfo['protime'];
			$data['bid_projectplace']=$bidInfo['area1'].','.$bidInfo['area2'].','.$bidInfo['area3'];
			$data['bid_projectdis']=$bidInfo['prodesc'];
			//$data['bid_projectfile']=$bidInfo['profile'];
			$data['bid_projecthide']=$bidInfo['promode'];
			//--------------------------------------------
			$data['bid_projectnum']=0;//方案数量
			$data['bid_projecwin']=0;//默认不中
			$data['bid_projectlook']=0;//默认没有浏览
			//$data['bid_messagenum']
			$data['bid_mod']=0;//默认免费发布
			$data['bid_projecttime']=time();//发方案时间
			$data['bid_projectstatus']=0;//默认方案征集中
			//dump($data['bid_projectplace']);
			if($this->create($data))
			{
			    $result=$this->where("bid_serviceid={$data['bid_serviceid']} AND bid_needid={$data['bid_needid']}")->select();
			    if(empty($result)){
			    	//$data['bid_projectfile']='0';
			    	$result = $this->add();
			    	if ($result) {
			    		return $data['bid_needid'];
			    	}else{
			    		return $data['bid_needid'];
			    	}
			    }
			    else
			    {
			    	unset($data['bid_projectfile']);
			    	$result=$this->where("bid_serviceid={$data['bid_serviceid']} AND bid_needid={$data['bid_needid']}")->save($data);
			    	if ($result) {
			    		return $data['bid_needid'];
			    	}else{
			    		return $data['bid_needid'];
			    	}
			    }
			    
			}
			else
			{
				exit($this->getError());
			}
		}
		//检查文件是否存在，存在返回1,不存在返回0
		public  function checkFile(){
			$data='';
			$dataId=I('post.');
			$data['bid_serviceid']=$dataId['serviceid'];
			$data['bid_needid']=$dataId['needid'];
			$result=$this->where($data)->select();
			if(empty($result[0]['bid_projectfile'])){
				return  0;
			}else{
				return  1;
			}
		}
		//删除上传文件  返回0:没有文件,1:删除失败,2:删除成功;
		public function  deleteFile(){
			$data='';
			$dataId=I('post.');
			$data['bid_serviceid']=$dataId['serviceid'];

			$result=$this->where("bid_serviceid={$data['bid_serviceid']}")->select();
			if(empty($result)){
				return  0;
			}else{
				$result=$this->where("bid_serviceid={$data['bid_serviceid']}")->delete();
				if($result){
					return 2;
				}else{
					return 1;
				}
			}
		}
		//中标后创建一个订单号
		//订单号=时间+需求号+方案号+唯一号
		public  function creatStoreOrder($map)
		{
			$data='';
			$bidlist=$this->join('__NEED__ ON __NEED__.need_id=__BID__.bid_needid')->where($map)->select();
			$data['order_needid']=$bidlist[0]['bid_needid'];//需求id
			$data['order_bidid']=$bidlist[0]['bid_id'];//方案id
			$data['order_employerid']=$bidlist[0]['need_userid'];//买家id
			$data['order_serviceuserid']=$bidlist[0]['bid_serviceid'];//卖家id
			$data['order_number']=date('Ymd').substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);//订单编号
			$data['order_number_price']=$bidlist[0]['bid_projectprice'];//价格
			$data['order_time']=time();//下单时间
			$storeOrderMode=M('store_order');
			if($storeOrderMode->create($data))
			{
				$result=$storeOrderMode->add();
				return $result;
				
			}
		}
	}
