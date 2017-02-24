<?php
	namespace Admin\Controller;
	use Think\Controller;
	
	/**
	*仪器库控制器
	*@author chenyanghui
	**/
	class InstrumentController extends CommonController
	{
		public function index()
		{

			$this->display();

		}

		//显示所有厂家信息
		public function allCompany()
		{
			$data = D('Instrument_company');
			$company = $data->getData();
			$this->assign( 'company', $company );
			$this->display();
		}

		//新增厂家
		public function addCompany()
		{
			if(!$_FILES){
				$res = ['status'=>false,'info'=>'未选择文件'];
				$this->ajaxReturn( $res );
			}else{
				$instrument = D('Instrument_company');
				$res = $instrument->addCompany();
				$this->ajaxReturn( $res );
			}
			
		}

		//修改厂家信息
		public function saveCompany()
		{
			$instrument = D('Instrument_company');
			$res = $instrument->saveCompany();
			$this->ajaxReturn( $res );
		}


		//删除厂家
		public function delCompany()
		{
			$instrument = D('Instrument_company');
			$res = $instrument->delCompany();
			$this->ajaxReturn( $res );

		}

		//所有仪器信息
		public function allInstrument()
		{

			$data = D('Instrument_company');
			$company = $data->getData();

			$res = D('Instrument_category_attribute');
			$cate = $res->getData();

			$data = D('Instrument_goods');
			$instrument = $data->getData();

			$this->assign(['company'=>$company,'cate'=>$cate['cate'],'instrument'=>$instrument]); 
			$this->display();
		}


		//ajax搜索厂家
		public function ajaxSearchCompany()
		{
			$name = I('post.data');
			$map['appt_company_name'] = array('like',$name.'%');

			$res = M('Instrument_company')->where($map)->field('appt_id,appt_company_name')->select();
			$this->ajaxReturn($res);
		}

		public function addGoods()
		{
			if(!$_FILES){
				$res = ['status'=>false,'info'=>'未选择文件'];
				$this->ajaxReturn( $res );
			}else{
				$instrument = D('Instrument_goods');
				$res = $instrument->addGoods();
				$this->ajaxReturn( $res );
			}
		}

		//分类管理
		public function category()
		{
			$data = D('Instrument_category');
			$cate = $data->getData();

			$this->assign('cate',$cate);
			$this->display();
		}

		//添加分类
		public function addCategory()
		{
			if( !$_FILES ){
				$res = ['status'=>false,'info'=>'没有选择分类图标'];
			}else{
				$cate = D('Instrument_category');
				$res = $cate->addCategory();
			}
			$this->ajaxReturn($res);

		}

		//ajax删除分类
		public function ajaxDelCate()
		{
			$cate = D('Instrument_category');
			$res = $cate->delCate();
			$this->ajaxReturn($res);
		}

		//ajax查询分类
		public function ajaxShowCateOne()
		{	
			$map['id'] = I('post.id');
			$cate = M('Instrument_category')->where($map)->field('cate_name')->find();
			$this->ajaxReturn($cate);
		}

		//ajax修改分类
		public function ajaxModifyCate()
		{
			$cate = D('Instrument_category');
			$res = $cate->modifyCate();
			$this->ajaxReturn($res);
		}

		//分类属性管理
		public function categoryAttribute()
		{	
			$attribute = D('Instrument_category_attribute');
			$data = $attribute->getData();

			$this->assign('data',$data);
			$this->display();
		}

		//ajax添加属性
		public function addAttribute()
		{
			$attribute = D('Instrument_category_attribute');
			$res = $attribute->addAttribute();
			$this->ajaxReturn($res);
		}

		//ajax删除属性
		public function delAttribute()
		{
			$attribute = D('Instrument_category_attribute');
			$res = $attribute->delAttribute();
			$this->ajaxReturn($res);
		}

		//ajax查询显示分类属性
		public function showAttribute()
		{
			$attribute = D('Instrument_category_attribute');
			$res = $attribute->getOne();
			$this->ajaxReturn($res);
		}

		//ajax修改属性
		public function ajaxModifyAttribute()
		{
			$attribute = D('Instrument_category_attribute');
			$res = $attribute->modifyAttribute();
			$this->ajaxReturn($res);
		}

		//ajax添加仪器
		public function instrumentUp()
		{
			if($_FILES){
				$instrument = D('Instrument_goods');
				$res = $instrument->instrumentUp();
				
			}else{
				$res = ['status'=>false,'info'=>'未选择文件'];
			}
			$this->ajaxReturn($res);
		}

		//ajax删除仪器
		public function ajaxDelGoods()
		{
			$goods = D('Instrument_goods');
			$res = $goods->delGoods();
			$this->ajaxReturn($res);
		}

		//仪器属性值管理
		public function goodsAttr()
		{	
			$attribute = D('Instrument_goods_attributeval');
			$attr = $attribute->getData();

			$this->assign('attr',$attr);
			$this->display();
		}

		//ajax修改属性值
		public function ajaxModAttrval()
		{
			$attribute = D('Instrument_goods_attributeval');
			$res = $attribute->modAttrval();
			$this->ajaxReturn($res); 
		}

		//ajax删除属性值
		public function delAttrval()
		{
			$attribute = D('Instrument_goods_attributeval');
			$res = $attribute->delAttrval();
			$this->ajaxReturn($res);
		}

		// 口碑管理
		public function WordOfMouth()
		{
			$praise = D('Wordofmouth_praise');
			$res = $praise->getPraiseInfo();

			$goods = M('InstrumentGoods');
			$goods_info = $goods->field('appt_id,appt_goodsname')->select();
			
			$goods_name = [];
			foreach ($goods_info as $key => $val) {
				$goods_name[$val['appt_id']] = $val['appt_goodsname'];
			}

			$user = M('User');
			$user_info = $user->field('user_id,user_account')->select();

			$picture = M('wordofmouth_picture');

			$user_name = [];
			foreach ($user_info as $key => $val) {
				$user_name[$val['user_id']] = $val['user_account'];
			}

			$status = ['下架','审核中','正常显示','精华贴'];

			foreach ($res['info'] as $key => &$val) {
				$val['appe_status'] = $status[ $val['appe_status'] ];
				$val['appe_gid'] = $goods_name[ $val['appe_gid'] ];
				$val['appe_uid'] = $user_name[ $val['appe_uid'] ];
				$val['pictures'] = json_encode( $picture->field('appe_pictures')->where('appe_pid='.$val['id'])->select() );
				if( !$val['appe_price'] ) $val['appe_price'] = '保密';
			}
			$res['status'] = $status;

			$this->assign($res);
			$this->display();
		}

		public function ajaxChangeStatus()
		{
			$praise = D('Wordofmouth_praise');
			echo $praise->changeStatus();
		}

		public function Comment()
		{
			$comment = D('Wordofmouth_comment');
			$res = $comment->getCommentInfo();

			$this->assign($res);
			$this->display();
		}
		public function ajaxDelComment()
		{
			$comment = D('Wordofmouth_comment');
			echo $comment->delComment();
		}
	}
