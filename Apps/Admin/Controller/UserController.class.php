<?php
	
	namespace Admin\Controller;

	use Admin\Controller;

	class UserController extends CommonController{

		/**
         * 查询所有用户列表
         * @author YangJun 15818708414@163.com
         * @return array 用户信息数组
         */
		public function viewList()
		{
			$user = D('user');
			$list = $user->viewList();

			$this->assign('list' , $list);

			$this->display();
		}

		/**
         * 更改用户状态
         * @author YangJun 15818708414@163.com
         * @return array ajax返回状态和错误信息
         */
		public function setStatus()
		{
			$user = D('user');
			$result = $user->setStatus(I('post.id') , I('post.status'));
			$this->ajaxReturn($result);
		}

		/**
         * 获取用户详细信息
         * @author YangJun 15818708414@163.com
         */
		public function getImpUserInfo()
		{
			$impuser = D('impuser');
			$impuser_info = $impuser->getImpUserInfo();

			if($impuser_info['impr_birthday']){
				$sex = ['女' , '男' , '保密'];
				$impuser_info['impr_birthday'] = date('Y-m-d' , $impuser_info['impr_birthday']);
			}else{
				$impuser_info['impr_birthday'] = "";
			}
			
			if($impuser_info['impr_sex']){
				$impuser_info['impr_sex'] = $sex[$impuser_info['impr_sex']];
			}else{
				$impuser_info['impr_sex'] = "";
			}

			$this->ajaxReturn($impuser_info);
		}
	}