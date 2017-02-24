<?php
	
	namespace Admin\Model;
	use Think\Model;

	/**
	*仪器库分类MODEL
	*
	*/
	class InstrumentCategoryModel extends Model
	{
		//设置验证规则
		protected $_validate = [
			['cate_name','require','分类名称不能为空'],
			['cate_name','','分类名已重复',1,'unique'],
			['parent_id','require','PID未选择'],
		];

		//查询数据
		public function getData()
		{
			$cate = $this->select();
			$cate1 = $cate;

			//根据分类等级遍历拼接样式
			foreach( $cate as $key => $val){
				$num = substr_count($val['cate_path'], ',');
	    		$cate[$key]['cate_name'] =  $num . '级' . '--' .  $val['cate_name'] . '--pid:' . $val['parent_id'];		
			}

			//遍历拼接并查询如果分类已添加过属性就不显示(其作用是不能再为其添加子级分类)
			foreach( $cate1 as $key => $val){
				$map['appt_category_id'] = $val['id'];
				$num1 = substr_count($val['cate_path'], ',');

				//样式拼接
	    		$cate1[$key]['cate_name'] =  str_repeat('<> ',$num1 - 1) . $val['cate_name'];

	    		//查询分类是否存在属性		
				if(M('Instrument_category_attribute')->where($map)->field('appt_category_id')->find()){
					unset($cate1[$key]);
				}
			}
		
			return ['cate'=>$cate,'cate1'=>$cate1];
		}

		//添加分类
		public function addCategory()
		{
			$map['id'] = I('post.category_id');
			
			//实例化think上传类
			$file  = new \Think\Upload();

			//设置图片保存路径并将文件传入upload()方法里
		  	$file->savePath  = '/Instrument/cate/pic/' ; 
		   	$info  =  $file->upload($_FILES); 

			if($info){
			   	//将返回的文件路径和文件名拼接起来存入$pic变量
				$pic   =  $info['cate_pic']['savepath'].$info['cate_pic']['savename'];

				$path = $this->where($map)->field('cate_path')->find();
				$data['parent_id'] = $map['id'];
				$data['cate_name'] = I('post.cate_name');
				$data['cate_path'] = $path['cate_path'] . $map['id'] . ',';
				$data['cate_pic'] = $pic;

				$post = $this->create($data);
				if($post){
					$this->add();
					$info = ['status'=>true,'info'=>'保存成功'];
				}else{
					$info = ['status'=>false,'info'=>$this->getError()];
				}
			}else{
				$info = ['status'=>false,'info'=>$this->getError()];
			}

			return $info;
		}

		//删除分类处理
		public function delCate()
		{
			//查询当前分类是否有子分类和属性
			$parent['parent_id'] = I('post.id');
			$map['appt_category_id'] = I('post.id');
			$cate = $this->where($parent)->find();
			$res = M('Instrument_category_attribute')->where($map)->field('appt_category_id')->find();

			//如果有子分类或者有属性都不能删除
			if($cate || $res){
				$info = ['status'=>false,'info'=>'没有权限,有子分类或者已添加属性'];
			}else{
				$map['id'] = I('post.id');
				$res = $this->where($map)->delete();
				if($res){
					$info = ['status'=>true,'info'=>'删除成功'];
				}else{
					$info = ['status'=>false,'info'=>'删除失败,请稍后再试'];
				}
			}

			return $info;
		}

		//修改分类处理
		public function modifyCate()
		{
			$map['id'] = I('post.id');
			$data = I('post.');
			$post = $this->create($data);
			if($post){
				$this->where($map)->save($data);
				$info = ['status'=>true,'info'=>'修改成功'];
			}else{
				$info = ['status'=>false,'info'=>$this->getError()];
			}

			return $info;

		}
	}