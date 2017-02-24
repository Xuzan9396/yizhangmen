<?php

	namespace Admin\Model;

	use Think\Model;

	class UserModel extends Model{

		/**
         * 查询所有用户列表
         * @author YangJun 15818708414@163.com
         * @return array 用户信息数组
         */
		public function viewList()
		{
			// 查询所有用户
			$list = $this->select();

			// 用户类型数组
			$type = ['A类会员' , 'B类会员' , 'C类会员' , '店铺子帐号'];

			// 用户状态数组
			$status = ['禁用' , '启用'];

			// 格式化数据
			foreach($list as $k => $v){
				foreach($v as $key => $val){
					if($key == 'user_addtime' || $key == 'user_lasttime'){
						$list[$k][$key] = date('Y-m-d H:i:s' , $val);
					}

					if($key == 'user_type'){
						$list[$k][$key] = $type[$val];
					}

					if($key == 'user_status'){
						$list[$k][$key] = $status[$val];
					}
				}
			}

	        return $list;
		}


		/**
         * 更改用户状态
         * @author YangJun 15818708414@163.com
         * @param $id 更改的用户id
         * @param $status 将用户状态更改为$status的值
         * @return array 状态和错误信息
         */
		public function setStatus($id , $status)
		{
			// 拼接修改条件
			$where['user_id'] = $id;
			$save['user_status'] = $status;

			// 执行修改操作
			if($this->where($where)->save($save)){
				$data['status'] = true;
				$data['error_info'] = '修改成功';
			}else{
				$data['status'] = false;
				$data['error_info'] = '未知原因,修改失败';
			}

			// 返回修改操作的状态和错误信息
			return $data;
		}

		/**
         * 获取一天内新用户数量
         * @author YangJun 15818708414@163.com
         */
		public function getNewUserNumber()
		{
			$where['user_addtime'] = ['egt' , (time() - 86400)];
			return $this->where($where)->count();
		}
	}