<?php 
	
	namespace Home\Model;
	use Think\Model;

	/**
	 * [仪器库口碑评论].
	 *
	 * @author YeWeiBin
	 *
	 * @param  [type]    描述参数作用
	 *
	 * @return [type] [description]
	 */
	class WordofmouthCommentModel extends Model{

		public function submitComment()
		{	

			$data['appt_pid'] = I('get.pid');
			$data['appt_uid'] = $_SESSION['home_user_info']['user_id'];
			$data['appt_content'] = I('get.content');
			$data['appt_ctime'] = time();
			if( $this->create($data)){
				return $this->add();
			}
		}

		public function showComment()
		{
			$map['appt_pid'] = I('get.id');
			return $this->where($map)->limit(10)->join('app_user ON app_wordofmouth_comment.appt_uid = app_user.user_id')->order('appt_ctime desc')->select();
		}

		public function getNewComment( $id )
		{
			$map['id'] = $id;
			return $this->where($map)->find();
		}

	}	