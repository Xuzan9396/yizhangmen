<?php
namespace Admin\Model;
use Think\Model;
	/**
	* ---服务商品分类
	*/
	class ServiceModel extends Model
	{

		public $serviceList=[];


		protected $_validate = array(
		     		array('sere_name','require',' 类名不合法 ！',1),
		     	 	//默认情况下用正则进行验证
		           	array('sere_pid','number',' id不合法！',1), 
		        	// 在新增的时候验证name字段是否唯一
		            //array('value',array(1,2,3),'值的范围不正确！',2,'in'),
		         	// 当值不为空的时候判断是否在一个范围内
		            //array('repassword','password','确认密码不正确',0,'confirm'), 
		         	// 验证确认密码是否和密码一致 
		            //array('password','checkPwd','密码格式不正确',0,'function'),
		            // 自定义函数验证密码格式   
		);
		
		/**
		 * ---服务商品分类浏览
		 */
		public function serviceDisplay(){
			// 实例化分页类 传入总记录数和每页显示的记录数(25)
			$Page       = new \Think\Page($this->count(),5);
			$show       = $Page->show();// 分页显示输出
			$service_list=$this->where('sere_pid=0')->limit($Page->firstRow.','.$Page->listRows)->select();
			 return  ['list'=>$service_list,'page'=>$show];
		}
		/**
		 * [getModeData 控制根据需求重数据库中查询需要的值]
		 * @param  [type] $key   [字段]
		 * @param  [type] $value [值]
		 * @return [type]        [值/boolean]
		 */
		public  function getModeData($key,$value)
		{
			$showdata=$this->where("{$key}={$value}")->select();
			return $showdata;
		}
		/**
		 * [serviceGetData 输出需要的数据用ajax删除]
		 * @return [type] [description]
		 */
		public function serviceGetData ()
		{
			$pg=I('get.pg');
			$data=I('get.getdata');
			$Page       = new \Think\Page($this->count(),5);
			$firstp=($pg-1)*5;
			$listp=$pg*5;
			$service_list=$this->limit($firstp.','.$listp)->select();
			return  $service_list[4];
		}

		public function serviceHomeManage(){

			$data = $this->select();

			return $data;
		}
	}
