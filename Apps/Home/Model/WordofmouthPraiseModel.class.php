<?php 
	
	namespace Home\Model;
	use Think\Model;

	/**
	 * [仪器库口碑].
	 *
	 * @author YeWeiBin
	 *
	 * @param  [type]    描述参数作用
	 *
	 * @return [type] [description]
	 */
	class WordofmouthPraiseModel extends Model{

		protected $_validate = array(
			array('appe_gid', 'number', '仪器编号必须为数字',2,'regex',3),     
			array('appe_shopname', 'require', '经销商名称不能为空', 1, 'regex', 3),     
			array('appe_purpose', 'require', '购买目的不能为空', 1, 'regex', 3),     
			array('appe_oneword', '1,30', '一句话评价不能为空', 1, 'length', 3),     
			array('appe_satisfy', '20,3000', '最满意长度为20-3000', 1, 'length', 3),     
			array('appe_unsatisfy', '20,3000', '最不满意长度为20-3000', 1, 'length', 3),     
			array('appe_reason', '20,3000', '购买理由长度为20-3000', 1, 'length', 3)  
			);

		public function handle()
		{	
			$data['appe_uid'] = session('home_user_info.user_id');
			$data['appe_gid'] = I('post.instrument_goodsname');
			$data['appe_shopname'] = I('post.shop_name');
			$data['appe_price'] = I('post.price')+0;
			
			if( I('post.other_purpose') ){
				$data['appe_purpose'] = I('post.other_purpose');
			}else{
				$data['appe_purpose'] = I('post.purpose');
			}

			$data['appe_oneword'] = I('post.oneword');
			$data['appe_satisfy'] = I('post.most_like');
			$data['appe_unsatisfy'] = I('post.miss_like');
			$data['appe_reason'] = I('post.reason');
			$data['appe_score'] = I('post.xjb_score') .',' . I('post.syty_score') .',' . I('post.shfw_score') ;
			$data['appe_ctime'] = time();

			if( $this->create($data)){
				return $this->add();
			}
			
		}

		protected function  _before_insert(&$data,$options)
		{	
			if( $_FILES['myfiles']['name'][0] == true ){
				foreach ($_FILES['myfiles']['error'] as $key => $val) {
		            switch ($val) {
		                case 1:
		                    	$this->error = '上传的文件超过最大限度';
		                    	return false;
		                    break;
		                case 2:
		                    	$this->error = '上传文件的大小超过了规定的值';
		                    	return false;
		                    break;
		                case 3:
		                    	$this->error = '文件只有部分被上传';
		                    	return false;
		                    break;
		                case 4:
		                    	$this->error = '没有文件上传';
		                    	return false;
		                    break;
		                case 6:
		                    	$this->error = '找不到临时文件夹';
		                    	return false;
		                    break;
		                case 7:
		                    	$this->error = '文件写入失败';
		                    	return false;
		                    break;
		                default:
		                    break;
					}
	            }
	            foreach ($_FILES['myfiles']['size'] as $key => $val) {
	            	if($val['size'] > 2100000 ) return false;
	            }
	        }
		}

		protected function  _after_insert(&$data,$options)
		{
			if( $_FILES['myfiles']['name'][0] == true ){
				$picture = M('WordofmouthPicture');

				$pic_data['appe_pid'] = $data['id'];

				foreach ($_FILES['myfiles']['type'] as $key => $val) {
					if( $val != 'image/jpeg' && $val != 'image/png'){
						unset($_FILES['myfiles']['tmp_name'][$key]);
					}
				}

				$dirName = 'wordofmouth/praise';
	            $rootPath = C('IMG_rootPath');

	            $upload = new \Think\Upload(array('rootPath' => $rootPath));// 实例化上传类

	            $upload->maxSize = (int) C('IMG_praise_maxSize') * 1024 * 1024;// 设置附件上传大小
	            $upload->savePath = $dirName.'/'; // 图片二级目录的名称

	            $info = $upload->upload();
	            $image = new \Think\Image();

	            if($info){
	            	foreach ($info as $key => $val) {
	            		$pic_data['appe_pictures'] = $val['savepath'] . $val['savename'];
	            		$pic_data['appe_thumb'] = $val['savepath'] . 'thumb-' . $val['savename'];
	            		
	            		$basepath = './Public/Uploads/';

	            		$image->open( $basepath . $val['savepath'] . $val['savename'] );
						$image->thumb(120, 90)->save( $basepath . $val['savepath'] . 'thumb-' . $val['savename']);

						$picture->add($pic_data);
	            	}
	            }
	        }
		}

		// 获取口碑首页信息
		public function getPraiseInfo()
		{
			$map['appe_gid'] = I('get.id',1);
			$map['appe_status'] = ['GT',1];
 	
			$res1 = $this->where($map)->select();

			$category = M('instrument_category');
			$category_res = $category->where('id='.I('get.pid',1))->find();

			$rank_res = $this->join('app_instrument_goods ON app_wordofmouth_praise.appe_gid = app_instrument_goods.appt_id')->where('appt_category_id='.I('get.pid',1))->select();

			$rank_info=[];

			foreach ($rank_res as $key => &$val) {
				$rank_info[$val['appe_gid']]['gid'] = $val['appe_gid'];
				$rank_info[$val['appe_gid']]['appt_goodsname'] = $val['appt_goodsname'];
				$rank_info[$val['appe_gid']]['score'][] = array_sum(explode(',',$val['appe_score']) )/3;
				$rank_info[$val['appe_gid']]['count'] = count($rank_info[$val['appe_gid']]['score']);
				$rank_info[$val['appe_gid']]['rank_sum']= array_sum($rank_info[$val['appe_gid']]['score']);
				$rank_info[$val['appe_gid']]['rank_avg']= sprintf("%.2f",array_sum($rank_info[$val['appe_gid']]['score'])/$rank_info[$val['appe_gid']]['count']);

			}
		    foreach ($rank_info as $key => $row) {
				        $volume[$key]  = $row['rank_avg'];
				        $edition[$key] = $key;
			}
    		array_multisort($volume, SORT_DESC, $edition, SORT_ASC, $rank_info);

    		for ($i=0; $i < count($rank_info) ; $i++) { 
    			if( $i<4){
    				$rank_info_res[] = $rank_info[$i];
    			}
    		}

			$page = new \Think\Page( count($res1) , 3 );
			$show = $page->show();

			$goods_info['praise_count'] = count($res1);

			$order = I('get.order',1);

			switch ($order) {
				case '1':
					$order_sort='';
					break;
				case '2':
					$order_sort = 'appe_ctime desc';
					break;
				case '3':
					$order_sort = 'appe_browse desc';
					break;									
				default:
					break;
			}

			$res = $this->where($map)->order($order_sort)->limit($page->firstRow.','.$page->listRows)->select();

			$picture = M('WordofmouthPicture');
			$user = M('user');
			$instrument = M('InstrumentGoods');
			$impuser = M('impuser');
			$comment = M('WordofmouthComment');
			$goods_info['instrument_info'] = $instrument->where(['appt_id'=>I('get.id',1)])->find();

			$score_sum = 0;
			
			foreach ($res1 as $key => $val) {
				$score_sum += array_sum( explode(',', $val['appe_score']) );
			}
			$score_avg = sprintf("%.2f", $score_sum / ( count($res1)*3 ) );

			foreach ($res as $key => &$val) {
				
				$map1['appe_pid'] = $val['id'];
				$map2['user_id'] = $val['appe_uid'];
				$val['pictures'] = $picture->where($map1)->select();
				$val['appe_uid'] = $user->where($map2)->find();
				$val['appe_score'] = explode(',', $val['appe_score']);
				$val['appe_satisfy'] = '【最满意的一点】<br>'. $val['appe_satisfy'].'<br><br>';
				$val['appe_unsatisfy'] = '【最不满意的一点】<br>'. $val['appe_unsatisfy'].'<br><br>';
				$val['appe_reason'] = '【选择的理由】<br>'. $val['appe_reason'].'<br><br>';
				$val['comment'] = $comment->where('appt_pid='.$val['id'])->count();
				if($val['appe_price'] == 0) $val['appe_price'] = '保密';
				$val['appe_impuser'] = $impuser->where($map2)->find();
				$val['praise_count'] = $this->where('appe_uid='.$val['appe_uid']['user_id'])->count();
			}

			return ['info' => $res,'score_avg'=>$score_avg,'show'=>$show,'goods_info'=>$goods_info,'category_res'=>$category_res,'rank_info_res'=>$rank_info_res];
		}

		public function addBrowse()
		{
			$map['id'] = I('get.pid',1);
			return $this->where($map)->setInc('appe_browse');
		}

		// 删除口碑
		public function delPraise()
		{
			$map['id'] = I('get.id');
			
			$picture = M('WordofmouthPicture');
			$comment = M('wordofmouth_comment');

			if( $res = $this->where($map)->delete() ){

				$pics_del = $picture->where('appe_pid='.I('get.id'))->select();

				if( $pics_del ){
					foreach ( $pics_del as $key => $val ) {
						unlink('./Public/Uploads/'.$val['appe_thumb']);
						unlink('./Public/Uploads/'.$val['appe_pictures']);
					}
					rmdir ( './Public/Uploads/wordofmouth/praise/'. explode( '/' , $pics_del[0]['appe_thumb'])[2] );
				}

				 	$picture->where('appe_pid='.I('get.id',1))->delete();
					$comment->where('appt_pid='.I('get.id',1))->delete();

					return $res;
			}else{
				return false;
			}

		}		

		// 获取个人口碑列表
		public function getPersonPraise()
		{
			$praise_map['appe_uid'] = I('get.uid',1);

			$user = M('user');

			if( $_SESSION['home_user_info']['user_id'] != I('get.uid') ){
				$praise_map['appe_status'] = ['gt',1];
			}
			$praise_res = $this->where($praise_map)->select();
			$praise_rank = $this->query('SELECT appe_uid, count(2) AS counts FROM app_wordofmouth_praise GROUP BY appe_uid ORDER BY counts desc LIMIT 4');

			

			foreach ($praise_rank as $key => &$val) {
				$val['name'] = $user->where('user_id='.$val['appe_uid'])->find();
				$rank_score_sum = 0;
				$praise_rank_res = $this->where('appe_uid='.$val['appe_uid'])->select();
				foreach ($praise_rank_res as $k => $v) {
					$rank_score_sum += array_sum( explode( ',' , $v['appe_score'] ) );
				}
				$val['rank_score_avg'] = sprintf("%.2f", $rank_score_sum/ (count($praise_rank_res)*3) );
			}

			foreach ($praise_res as $key => &$val) {
				$score_sum += array_sum( explode( ',' , $val['appe_score'] ) );
			}
			$score_avg = sprintf("%.2f", $score_sum / ( count($praise_res)*3 ) );

			$goods = M('instrument_goods');
			$picture = M('WordofmouthPicture');
			$comment = M('wordofmouth_comment');

			// 分页
			$page = new \Think\Page( count($praise_res) , 4);
			$page_show = $page->show();

			$order = I('get.order',1);

			switch ($order) {
				case '1':
					$order_sort='';
					break;
				case '2':
					$order_sort = 'appe_ctime desc';
					break;
				case '3':
					$order_sort = 'appe_browse desc';
					break;									
				default:
					break;
			}
			$praise_page_res = $this->where($praise_map)->order($order_sort)->limit($page->firstRow.','.$page->listRows)->select();

			$score_sum = 0;

			foreach ($praise_page_res as $key => &$val) {
				$val['goods_info'] = $goods->where('appt_id='.$val['appe_gid'])->find();
				$val['praise_score'] = explode( ',' , $val['appe_score']);
				$val['pictures'] = $picture->where('appe_pid='.$val['id'])->select();
				$val['comment'] = $comment->where('appt_pid='.$val['id'])->count();
			}


			// $user->select()
			$user_res = $user->field()->where('user_id='.I('get.uid',1))->find();		

			$impuser = M('impuser');
			$impuser_res = $impuser->field('impr_picture')->where('user_id='.I('get.uid',1))->find();

			// echo '<pre>';
			// 	print_r($praise_page_res);
			// 	print_r($_SESSION);
			// echo '</pre>';

			return ['page_show'=> $page_show,'praise_count'=>count($praise_res),'praise_rank'=>$praise_rank,'praise_info'=>$praise_page_res,'user_info'=>$user_res,'impuser_picture'=>$impuser_res['impr_picture'] , 'score_avg' => $score_avg ];
		}
	}