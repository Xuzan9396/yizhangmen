<?php
	
	namespace Admin\Controller;

	use Think\Controller;
	
	//发布需求的控制器
	class NeedUserController extends CommonController
	{	
		/**
		 * 显示所有的需求订单
		 * 金君 <757258777@qq.com>
		 */
		public function needList ()
		{	
			// 实例化
			$need = D('need');
			//获取返回值
			$list_data = $need->needList();
			//分配数据
			$this->assign($list_data);
			//展示
			$this->display();
		}

		/**
		 * 后台更改需求信息
		 * 金君 <757258777@qq.com>
		 */
		public function needSave ()
		{	
			// 点击修改 get接受时
			if(IS_GET){
				// 实例化
				$need = D('need');
				//获取返回值
				$save_data = $need->needSaveList();
				//分配数据
				$this->assign($save_data);
				// 展示
				$this->display();
			}

			// 修改完post接收时
			if(IS_POST){
				// 实例化
				$need = D('need');
				//获取返回值
				$save_data['list'] = $need->needSave();
				// 判断是否成功
				if($save_data !== false){
					redirect(U('Admin/NeedUser/needList'));
				}else{
					$this->error('更新失败',U('Admin/NeedUser/needList'),3);
				}
			}		
		}

		/**
		 * 后台需求信息详情信息
		 * 金君 <757258777@qq.com>
		 */
		public function needDetails ()
		{	
			// get接受
			if(IS_GET){
				// 实例化
				$need = D('need');
				// 接受返回值
				$need_details = $need->needDetails();
				// 分配数据
				$this->assign($need_details);
				// 显示
				$this->display();
			}

			if(IS_POST){
				// 实例化
				$need = D('need');
				//获取返回值
				$need_details = $need->needDetailsSave();
				// 判断是否成功
				if($need_details !== false){
					redirect(U('Admin/NeedUser/needList'));
				}else{
					$this->error('操作失败',U('Admin/NeedUser/needList'),3);
				}
			}
			
		}

		/**
		 * 后台需求模板
		 * 金君 <757258777@qq.com>
		 */
		public function needModel ()
		{
			// 实例化
			$need_model = M('needmodel');
			//数据查询
			$list['list'] = $need_model->select();
			// 分配数据
			$this->assign($list);
			// 显示
			$this->display();
		}

		/**
		 * 后台需求模板添加
		 * 金君 <757258777@qq.com>
		 */
		public function needModelAdd ()
		{	
			// get接受
			if(IS_GET){
				// 实例化
				$need_model = M('needmodel');
				//查询状态为0 的个数;
				$list['num'] = $need_model->where(['ndm_status'=>0])->count();
				// 分配数据
				$this->assign($list);
				// 显示
				$this->display();
			}

			if(IS_POST){
				//接受要添加的数据
				$post = $_POST;
				// 实例化
				$need_model = M('needmodel');
				//数据查询
				$result = $need_model->add($post);
				// 判断是否成功
				if($result !== false){
					redirect(U('Admin/NeedUser/needModel'));
				}else{
					$this->error('操作失败',U('Admin/NeedUser/needModel'),3);
				}
			}
			
		}

		/**
		 * 后台需求模板删除
		 * 金君 <757258777@qq.com>
		 */
		public function needModelDelete ()
		{	
			$id = I('get.id');
			$map['ndm_id'] = ['eq' , $id];
			// 实例化
			$need_model = M('needmodel');
			//数据查询
			$result = $need_model->where($map)->delete();
			// 判断是否成功
			if($result !== false){
				redirect(U('Admin/NeedUser/needModel'));
			}else{
				$this->error('操作失败',U('Admin/NeedUser/needModel'),3);
			}
		}

		/**
		 * 后台需求模板修改
		 * 金君 <757258777@qq.com>
		 */
		public function needModelSave ()
		{
			// get接受
			if(IS_GET){
				// 接收id
				$id = I('get.id');
				$map['ndm_id'] = ['eq' , $id];
				// 实例化
				$need_model = M('needmodel');
				//数据查询
				$list['list'] = $need_model->where($map)->find();
				//查询状态为0 的个数;
				$list['num'] = $need_model->where(['ndm_status'=>0])->count();
				// 分配数据
				$this->assign($list);
				// 显示
				$this->display();
			}

			if(IS_POST){
				// 实例化
				$need_model = M('needmodel');
				//修改
				$post = $_POST;
				//提取id
				$id = $post['ndm_id'];
				$map['ndm_id'] = ['eq' , $id];
				//更新数据
				$result = $need_model->where($map)->save($post);
				// 判断是否成功
				if($result !== false){
					redirect(U('Admin/NeedUser/needModel'));
				}else{
					$this->error('操作失败',U('Admin/NeedUser/needModel'),3);
				}
			}
		}




		/**
		 * 后台需求失败原因编辑
		 * 金君 <757258777@qq.com>
		 */
		public function needReason ()
		{
			// 实例化
			$need_reason = M('needreason');
			//数据查询
			$list['list'] = $need_reason->select();
			// 分配数据
			$this->assign($list);
			// 显示
			$this->display();
		}

		/**
		 * 后台失败原因添加
		 * 金君 <757258777@qq.com>
		 */
		public function needReasonAdd ()
		{	

			if(IS_POST){
				//接受要添加的数据
				$post = I('post.');
				// 实例化
				$need_reason = M('needreason');
				//数据查询
				$result = $need_reason->add($post);
				// 判断是否成功
				if($result !== false){
					redirect(U('Admin/NeedUser/needreason'));
				}else{
					$this->error('操作失败',U('Admin/NeedUser/needreason'),3);
				}
			}

			// 显示
			$this->display();
			
		}

		/**
		 * 后台失败原因删除
		 * 金君 <757258777@qq.com>
		 */
		public function needReasonDelete ()
		{	
			$id = I('get.id');
			$map['ndr_id'] = ['eq' , $id];
			// 实例化
			$need_reason = M('needreason');
			//数据查询
			$result = $need_reason->where($map)->delete();
			// 判断是否成功
			if($result !== false){
				redirect(U('Admin/NeedUser/needreason'));
			}else{
				$this->error('操作失败',U('Admin/NeedUser/needreason'),3);
			}
		}

		/**
		 * 后台失败原因修改
		 * 金君 <757258777@qq.com>
		 */
		public function needReasonSave ()
		{
			// get接受
			if(IS_GET){
				// 接收id
				$id = I('get.id');
				$map['ndr_id'] = ['eq' , $id];
				// 实例化
				$need_reason = M('needreason');
				//数据查询
				$list['list'] = $need_reason->where($map)->find();
				// 分配数据
				$this->assign($list);
				// 显示
				$this->display();
			}

			if(IS_POST){
				// 实例化
				$need_reason = M('needreason');
				//修改
				$post = I('post.');
				//提取id
				$id = $post['ndr_id'];
				$map['ndr_id'] = ['eq' , $id];
				//更新数据
				$result = $need_reason->where($map)->save($post);
				// 判断是否成功
				if($result !== false){
					redirect(U('Admin/NeedUser/needreason'));
				}else{
					$this->error('操作失败',U('Admin/NeedUser/needreason'),3);
				}
			}
		}


	}
