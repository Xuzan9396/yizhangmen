<?php 
	
	namespace Home\Model;
	use Think\Model;

	/**
	 * [仪器库口碑图片].
	 *
	 * @author YeWeiBin
	 *
	 * @param  [type]    描述参数作用
	 *
	 * @return [type] [description]
	 */
	class WordofmouthPictureModel extends Model{


		public function addPicture( $gid )
		{	

			$data['appe_pid'] = $gid;
			$data['appe_pictures'] = I('post.shop_name');
			$data['appe_price'] = I('post.price');
			

			if( $this->create($data)){
				return $this->add();
			}
			
		}

	}	