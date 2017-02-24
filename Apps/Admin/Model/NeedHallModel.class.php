<?php
namespace Admin\Model;

use Think\Model;

	class NeedHallModel extends Model
	{
		
		protected $tableName='bid';
		/**
		 * [bidSet 根据需求表的数据查询 need 表, 查询 store表,查询 user表,查询store_category表]
		 * @return [type] [description]
		 */
		public function  displayTab()
		{
			$bidlist=$this->join('__NEED__ ON  __NEED__.need_id=__BID__.bid_needid')->join('__STORE__ ON __STORE__.store_userid=__BID__.bid_serviceid ')->join('__USER__ ON __USER__.user_id=__BID__.bid_serviceid')->join('__STORE_CATEGORY__ ON __STORE_CATEGORY__.id=__NEED__.need_cateid ')->select();
			return  $bidlist;
		}
		/**
		 * [bidDel ajax 删除]
		 * wenzhonghua@163.com
		 * 删除方案及对应的附件
		 * @param [type] $[id] [被删除的需求id]
		 * @return [boolen] [成功删除返回1,失败返回0]
		 */
		public  function  bidFileDel()
		{
			$bidId=I('post.del_id');
			$data['bid_id']=$bidId;
			//选查询是否有上传附件,如果有则先删除附件,再删除对应表数据
			$result=$this->where($data)->select();
			if(!empty($result))
			{
				if(!empty($result[0]['bid_projectfile']))
				{
					if(file_exists('Public/'.$result[0]['bid_projectfile']))
					{
						if(unlink('Public/'.$result[0]['bid_projectfile']))//所删除对应的文件地址
						{
							//查询是否是空文件夹
							
							//删除表
							return ($this->delTable($data,$result));
						}else{
							return 0;
						}
					}else{
						//删除表
						return ($this->delTable($data,$result));
					}
				}else{
					//删除表
					return ($this->delTable($data,$result));
				}
			}
			else
			{
				return 0;
			}
		}
		//-------删除数据表
		//@param  $data  为传入删除的id条件
		//@param  $result 为传入查询对应需求是事还有方案的条件
		//成功返回1,失败返回0
		private function delTable($data,$res){
			$result=$this->where($data)->delete();
			if ($result)
			{
				//再查询一下对应需求还有没有方案
				//$needMode=M('need');
				$needBid=$this->where("bid_needid={$res[0]['bid_needid']}")->select();
				if(empty($needBid))
				{
					$needMode=M('need');
					$setNeedBid='';
					$setNeedBid['need_prostaue']=0;
					$setNeedBid['need_prostepe']=0;
					$setNeedS=$needMode->where("need_id={$res[0]['bid_needid']}")->save($setNeedBid);
					return 1;
				}
				else
				{
					return 1;
				}
			}
			else
			{
				return 0;
			}
		}
		/**
		 * [messageMode 查询消息]
		 * @return [type] [查询消息+user表+need表]
		 */
		public  function messageMode()
		{
			$megsMode=M('bidmessage');
			$meglist=$megsMode->join('__USER__ ON __USER__.user_id=__BIDMESSAGE__.bidm_uid')->join('__NEED__ ON __NEED__.need_id=__BIDMESSAGE__.bidm_pid')->select();
			return $meglist;
		}
		/**
		 * [bidFileDel 删除留言]
		 * @return [type] [description]
		 */
		public  function  bidmegDelFile()
		{
			$data='';
			$bidmId=I('post.del_id');
			$data['bidm_id']=$bidmId;
			$bidmegsMode=M('bidmessage');
			//选查询是否有上传附件,如果有则先删除附件,再删除对应表数据
			$result=$bidmegsMode->where($data)->select();
			if(!empty($result))
			{
				if(!empty($bidmegsMode->where("bidm_zid={$bidmId}")->select()))
				{
					$bidmegsMode->where("bidm_zid={$bidmId}")->delete();
				}
				return $bidmegsMode->where($data)->delete();
			}
			else
			{
				return 0;
			}
		}
	}
