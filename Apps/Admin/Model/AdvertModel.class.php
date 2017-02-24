<?php
	namespace Admin\Model;
	use Think\Model;

	/**
	*advert类[处理轮播图数据]
	*@author 279029419@qq.com
	**/
	class AdvertModel extends Model
	{

		//从数据库获取所有advert数据并处理
		public function getDate()
		{
			//查询所有数据
			$data = $this->select();

			//准备好数据格式化
			$where = ['未启用','首页','需求大厅','服务机构','banner背景'];

			foreach($data as $key => $val){
				if($val['appt_mod_time'] == ''){
					//如果修改时间为空就不显示该字段
					unset($data[$key]['appt_mod_time']);
				}else{
					//将修改时间格式化
					$data[$key]['appt_mod_time'] = date('Y年m月d日H时i分s秒',$val['appt_mod_time']);
				}

				//如果上传图片时未存储店铺id，店铺id将显示‘未启用’
				if( $val['appt_store_id'] == '0' ){
					$data[$key]['appt_store_id'] = '未启用';
				}else{
					//根据店铺id查询该店铺的店铺名并合关到$data数组中
					$map['id'] = $val['appt_store_id'];
					$store = M('Store')->where($map)->field('id,store_name')->find();
					$data[$key]['appt_store_name'] = $store['store_name'];

				}

				//重新格式化图片启用位置0:未启用，1:首页，2:需求大厅，3:服务机构
				$data[$key]['appt_where'] = $where[$val['appt_where']];

				//格式化创建时间
				$data[$key]['appt_create_time'] = date('Y年m月d日H时i分s秒',$val['appt_create_time']);
			}

			//统计图片启用位置总数(0为未启用)
			$map0['appt_where'] = 0;
			$map1['appt_where'] = 1;
			$map2['appt_where'] = 2;
			$map3['appt_where'] = 3;
			$map4['appt_where'] = 4;

			$count = [];
			$count['0'] = $this->where($map0)->count();
			$count['1'] = $this->where($map1)->count();
			$count['2'] = $this->where($map2)->count();
			$count['3'] = $this->where($map3)->count();
			$count['4'] = $this->where($map4)->count();
		
			return ['data'=>$data,'count'=>$count];
		}

		//上传图片处理
		public function fileUp()
		{
			//实例化think上传类
			$file  = new \Think\Upload();

			//设置图片保存路径并将文件传入upload()方法里
		  	$file->savePath  = '/advert/pic/' ; 
		   	$info  =  $file->upload($_FILES); 

			if($info){
			   	//将返回的文件路径和文件名拼接起来存入$pic变量
				$pic   =  $info['pic']['savepath'].$info['pic']['savename'];

				//准备好当天日期(年月日),创建一个文件夹以当天上传日期为名的路径，以便存储略缩图
				$date  = date( 'Y-m' );
				$zoomPath = './Public/Uploads/advert/zoom/' . $date . '/';
				if( !file_exists($zoomPath) ){
					mkdir( $zoomPath, 0777, true);
				}
				
				//略缩图保存名称为上一步创建的文件夹+原图名称
				//实例化think图片略缩类，将原图全路径传入，保存略缩图大小和略缩图名称
			   $zoom  = $zoomPath . $info['pic']['savename']; 
			   $image = new \Think\Image();
			   $image->open( './Public/Uploads' . $pic ); 
			   $zoomres = $image->thumb(50, 50)->save( $zoom );
			  
			  //重新拼接略缩图保存进数据库的名称
			   $zoomName = '/advert/zoom/' . $date . '/' . $info['pic']['savename']; 

			   // 如果文件上传成功，将处理好的数据存入数据库
			   if( $zoomres ){
			   		
			   		$data['appt_pic'] = $pic;
					$data['appt_zoom'] = $zoomName;
					$data['appt_store_id'] = I('post.appt_store_id');
					$data['appt_where'] = I('post.appt_where');
					$data['appt_title'] = I('post.appt_title');
					$data['appt_desc'] = I('post.appt_desc');
					$data['appt_create_time'] = time();

					if( I('post.appt_where') == 4 ){
						$whe['appt_where'] = 4;
						$ban = $this->where( $whe )->select();
					}

			   		$res = $this->add($data);
			   		if($res){
			   			$info = [ 'status' => true,'info' => '保存成功'];
			   			if( $ban[0]['appt_where'] ){
			   				$banid['appt_where'] = 0;
			   				$whe['appt_id'] = $ban[0]['appt_id'];
			   				$this->where( $whe )->save($banid);
			   			}
			   		}else{
			   			$info = ['status' => false, 'info' => '保存数据库失败'];
			   		}
			   }else{
			   		$info = [ 'status' => false, 'info' => '缩略图处理失败'];
			   }
			}else{
				$info = ['status' => false, 'info' => '原图上传失败' ];
			}
			
		   
		   //返回处理结果
		   return $info;
		
		}

		//图片删除处理
		public function fileDel()
		{	
			//将单条信息查询出来
			$map['appt_id'] = I('post.appt_id');
			$photos = $this->where($map)->select();
			$res = $this->where($map)->delete();
			
			//如果数据库删除成功，将本地文件删除(原图和略缩图)
			if($res){
				unlink('./Public/Uploads'.$photos[0]['appt_pic']);
				unlink('./Public/Uploads'.$photos[0]['appt_zoom']);
				$status = true;
			}else{
				$status = false;
			}
			$info = ['status' => $status,'info' => $this->getError()];

			return $info;
		}

		// 图片修改处理
		public function fileSave()
		{

			$map['appt_id'] = I('post.appt_id');
			$data = $this->create(I('post.'));
			$data['appt_mod_time'] = time();
			foreach($data as $key => $val){
				if( $val == ''){
					unset($data[$key]);
				}
			}

			$res = $this->where($map)->save($data);
			if($res){
				$info = ['status' => true, 'info' => '修改成功'];
			}else{
				$info = ['status' => false, 'info' =>'修改失败'];
			}
	
			return $info;
		}


		//查询单条信息(修改信息时用)
		public function find()
		{

			$map['appt_id'] = I('post.appt_id');
			$data = $this->where($map)->select();

			foreach($data as $key => $val){
				if($val['appt_mod_time'] == ''){
					unset($data[$key]['appt_mod_time']);
				}else{
					$data[$key]['appt_mod_time'] = date('Y年m月d日H时i分s秒',$val['appt_mod_time']);
				}

				//根据店铺id查询店铺名称
				if( $val['appt_store_id'] == '0' ){
					$data[$key]['appt_store_id'] = '未启用';
				}else{
					$map['id'] = $val['appt_store_id'];
					$store = M('Store')->where($map)->field('id,store_name')->find();
					$data[$key]['appt_store_id'] = $store['store_name'];
				}

				//设置下拉菜单默认值
				for($i = 0;$i<= 4;$i++){
					if( $i == $val['appt_where']){
						$data[$key]['where'][$i] = 'selected=selected';
					}else{
						$data[$key]['where'][$i] = '';
					}
				}
	
				$data[$key]['appt_create_time'] = date('Y年m月d日H时i分s秒',$val['appt_create_time']);
			}
			
			return ['data'=>$data[0]];
		}
		
	}