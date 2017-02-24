<?php
	
	namespace Admin\Model;
	use Think\Model;

	/**
	*@author chenyanghui
	*处理仪器库数据
	*/
	class InstrumentGoodsModel extends Model
	{
		protected $_validate = [
			['appt_category_id','require','分类未选择'],
			['appt_company_id','require','厂商未选择'],
			['appt_goodsname','require','商品名称未填写'],
		];
		//获取表数据
		public function getData()
		{
			$instrument = $this->join(array('app_instrument_category ON app_instrument_goods.appt_category_id = app_instrument_category.id','app_instrument_company ON app_instrument_goods.appt_company_id = app_instrument_company.appt_id'))->field('app_instrument_company.appt_company_name,app_instrument_category.cate_name,app_instrument_goods.*')->select();
			foreach( $instrument as $key => $val ){
				if($val['appt_mod_time'] == '0'){
					unset($instrument[$key]['appt_mod_time']);
				}else{
					$instrument[$key]['appt_mod_time'] = date('Y/m/d/H/i/s',$val['appt_mod_time']);
				}

				$instrument[$key]['appt_create_time'] = date('Y/m/d/H/i/s',$val['appt_create_time']);
				$map['appt_goods_id'] = $val['appt_id'];
				$instrument[$key]['attribute'] = M('Instrument_goods_attributeval')->where($map)->select();
			}

			foreach($instrument as $key => $val){
				foreach($val['attribute'] as $k => $v){
					$map1['id'] = $v['appt_attribute_id'];
					$instrument[$key]['attribute'][$k]['attribute_name'] = M('Instrument_category_attribute')->where($map1)->find()['appt_attribute_name'];
					
				}

			}
		
			return $instrument;

		}

		public function instrumentUp()
		{
			//实例化think上传类
			$file  = new \Think\Upload();

			$post = $this->create(I('post.'));

			if($post){
	
				//设置图片保存路径并将文件传入upload()方法里
			  	$file->savePath  = '/instrument/goods/pic/' ; 
			   	$info  =  $file->upload($_FILES); 

			   	//将返回的文件路径和文件名拼接起来存入$pic变量
				$pic   =  $info['pic']['savepath'].$info['pic']['savename'];

				if($info){

					//准备好当天日期(年月日),创建一个文件夹以当天上传日期为名的路径，以便存储略缩图
					$date  = date( 'Y-m' );
					$zoomPath = './Public/Uploads/instrument/goods/zoom/' . $date . '/';
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
				   $zoomName = '/instrument/goods/zoom/' . $date . '/' . $info['pic']['savename']; 

				   // 如果文件上传成功，将处理好的数据存入数据库
				   if( $zoomres ){
				   		$data['appt_goodsname'] = I('post.appt_goodsname');
				   		$data['appt_pic'] = $pic;
						$data['appt_zoom'] = $zoomName;
						$data['appt_category_id'] = I('post.appt_category_id');
						$data['appt_company_id'] = I('post.appt_company_id');
						$data['appt_create_time'] = time();
				   		$res = $this->add($data);
				 
				   		if($res){
				   			
				   			$instrument = I('post.');
				   			unset($instrument['appt_goodsname']);
				   			unset($instrument['appt_company_id']);
				   			unset($instrument['appt_category_id']);
				  			$i = 0;
				  			$instrument1 = [];
				   			foreach($instrument as $key => $val){
					  			$instrument1[$i]['appt_goods_id'] = $res;
					  			$instrument1[$i]['appt_attribute_id'] = $key;
					  			$instrument1[$i]['appt_attributeval_value'] = $val;
					  			$i++;

				   			}

				   			foreach($instrument1 as $key => $val){
				   				M('Instrument_goods_attributeval')->add($val);
				   			}
				   			$info = [ 'status' => true,'info' => '保存成功'];
				   		}else{
				   			$info = ['status' => false, 'info' => '保存数据库失败'];
				   		}
				   }else{
				   		$info = [ 'status' => false, 'info' => '缩略图处理失败'];
				   }
				}else{
					$info = ['status' => false, 'info' => '原图上传失败' ];
				}
			}else{
				$info = ['status'=>false,'info'=>$this->getError()];
			} 
	   
		   //返回处理结果
		   return $info;
		}

		//删除商品
		public function delGoods()
		{	
			//查询出商品信息，再进行删除
			$map['appt_id'] = I('post.appt_id');
			$map1['appt_goods_id'] = I('post.appt_id');
			$data = $this->where($map)->find();

			$res = $this->where($map)->delete();
			if($res){
				//删除属性值
				M('Instrument_goods_attributeval')->where($map1)->delete();

				//删除本地保存的相关图片
				unlink('./Public/Uploads'.$data['appt_pic']);
				unlink('./Public/Uploads'.$data['appt_zoom']);
				$info = ['status'=>true,'info'=>'删除成功'];
			}else{
				$info = ['status'=>false,'info'=>'删除失败'];
			}

			return $info;
		}

	}