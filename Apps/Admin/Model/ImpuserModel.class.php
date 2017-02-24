<?php
	namespace Admin\Model;
	use Think\Model;

	class ImpuserModel extends Model
	{
		/**
         * 获取用户详细信息
         * @author YangJun 15818708414@163.com
         * @return array 返回信息数组
         */
		public function getImpUserInfo()
		{
			$id = I('post.user_id');
			$where['user_id'] = ['eq' , $id];

			$impuser_info = $this->where($where)->find();

			return $impuser_info;
		}
	}