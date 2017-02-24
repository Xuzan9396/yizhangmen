<?php 
	
	namespace Admin\Model;
	use Think\Model;

	/**
	*仪器库口碑
	*@author YeWeiBin
	**/	
	class WordofmouthCommentModel extends Model{
		public function getCommentInfo()
		{
			$res = $this->select();

			$user = M('user');
			$user_info = $user->field('user_id,user_account')->select();
			foreach ($user_info as $key => &$val) {
				$user_info_res[$val['user_id']]=$val['user_account'];
			}
			return ['info'=> $res,'user_info'=>$user_info_res,'comment_status'=>['下架','正常']];

		}

		public function delComment()
		{
			return $this->where('id='.I('get.id'))->delete();
		}
	}