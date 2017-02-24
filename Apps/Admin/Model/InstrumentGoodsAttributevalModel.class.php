<?php
	namespace Admin\Model;
	use Think\Model;

	/**
	*仪器库商品属性值MODEL
	**/
	class InstrumentGoodsAttributevalModel extends Model
	{
		//设置验证规则
		protected $_validate = [
			['appt_attributeval_value','require','属性值不能为空'],
		];
		
		// 获取数据
		public function getData()
		{
			//多表联查
			$attr = $this->join(array('app_instrument_goods ON app_instrument_goods_attributeval.appt_goods_id = app_instrument_goods.appt_id','app_instrument_category_attribute ON app_instrument_goods_attributeval.appt_attribute_id = app_instrument_category_attribute.id'))->field('app_instrument_goods_attributeval.*,app_instrument_goods.appt_goodsname,app_instrument_category_attribute.appt_attribute_name')->select();
			
			return $attr;
		}

		// 修改属性值
		public function modAttrval()
		{
			$map['appt_id'] = I('post.appt_id');
			$data['appt_attributeval_value'] = I('post.appt_attributeval_value');
			$post = $this->create($data);
			if($post){
				$this->where($map)->save($data);
				$info = ['status'=>true,'info'=>'修改成功']; 
			}else{
				$info = ['status'=>false,'info'=>$this->getError()];
			}
			return $info;
		}

		//删除属性值
		public function delAttrval()
		{
			$map['appt_id'] = I('post.appt_id');
			$res = $this->where($map)->delete();
			if($res){
				$info = ['status'=>true,'info'=>'删除成功'];
			}else{
				$info = ['status'=>false,'info'=>'删除失败'];
			}
			return $info;
		}
	}