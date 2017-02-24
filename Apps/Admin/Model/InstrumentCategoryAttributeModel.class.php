<?php

	namespace Admin\Model;
	use Think\Model;

	/**
	*仪器库分类属性MODEL
	*
	**/
	class InstrumentCategoryAttributeModel extends Model
	{	
		//添加属性时验证规则
		protected $_validate = [
			['appt_category_id','require','分类未选择'],
			['appt_attribute_name','require','属性名称不能为空'],
		];

		//获取分类属性数据
		public function getData()
		{
			//多表联查属性相关信息
			$attribute = $this->join('app_instrument_category ON app_instrument_category_attribute.appt_category_id = app_instrument_category.id')->field('app_instrument_category_attribute.*,app_instrument_category.cate_name')->select();

			//查询分类
			$cate = M('instrument_category')->select();

			//遍历处理分类数据，最终显示结果为底级分类
			foreach( $cate as $key => $val ){
				$num = substr_count($val['cate_path'], ',');
				$cate[$key]['cate_name'] = $num . '级--' . $val['cate_name'] .'--pid:' .$val['parent_id']; 
				$map['parent_id'] = $val['id'];
				$res = M('Instrument_category')->where($map)->field('parent_id')->find();
				if( $res ){
					unset($cate[$key]);
				}
			}
			
			return ['attribute'=>$attribute,'cate'=>$cate];
		}

		//添加分类属性
		public function addAttribute()
		{
	
			$data = I('post.');
			unset($data['appt_category_id']);
			$map['parent_id'] = I('post.appt_category_id');

			//如果分类为底级才执行添加
			if(!M('Instrument_category')->where($map)->find()){

				//过滤值为空的数据
				foreach($data as $key => $val){
					if( !$val ){
						unset($data[$key]);
					}

					$attr['appt_attribute_name'] = $val;
					$attr['appt_category_id'] = I('post.appt_category_id');

					//创建数据对象，通过验证并进行添加
					$post = $this->create($attr);
					if($post){
						$this->add($attr);
						$info = ['status'=>true,'info'=>'添加成功'];
					}else{
						$info = ['status'=>false,'info'=>$this->getError()];
					}
				}
			}else{
				$info = ['status'=>false,'info'=>'该分类不是底级，不能添加属性'];
			}
			

			return $info;
		}


		// 删除属性
		public function delAttribute()
		{
			$map['id'] = I('post.id');

			//查询当前属性是否有值存在
			$map1['appt_attribute_id'] = I('post.id');
			$attributeval = M('Instrument_goods_attributeval')->where($map1)->field('appt_attribute_id')->find();

			//如果属性没有值，再进行删除
			if(!$attributeval){
				$res = $this->where($map)->delete();
				if($res){
					$info = ['status'=>true,'info'=>'删除成功'];
				}else{
					$info = ['status'=>false,'info'=>'删除失败'];
				}
			}else{
				$info = ['status'=>false,'info'=>'该属性已有值，不能删除'];
			}
			

			return $info;
		}


		//查询单条分类下属性
		public function getOne()
		{
			$map['appt_category_id'] = I('post.id');
			$data = $this->where($map)->select();
			if($data){
				$info = ['status'=>true,'data' =>$data];
			}else{
				$info = ['status'=>false,'data'=>'该分类暂无属性'];
			}
			return $info;

		}

		//修改属性
		public function modifyAttribute()
		{
			$map['id'] = I('post.id');
			$data['appt_attribute_name'] = I('appt_attribute_name');
			if( $data['appt_attribute_name'] ){
				$res = $this->where($map)->save($data);
				if($res){
					$info = ['status'=>true,'info'=>'修改成功'];
				}else{
					$info = ['status'=>false,'info'=>'修改失败'];
				}
			}else{
				$info = ['status'=>false,'info'=>'属性名称不能为空'];
			}

			return $info;
		}
	}
