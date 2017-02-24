<?php

	namespace Home\Controller;
	use Think\Controller;

	class InstrumentController extends CommonController
	{
		/*
		*仪器库首页分类控制
		*@author YeWeiBin
		 */
		public function index()
		{
			$cate = D('Instrument_category');
			$Ins_Cate['TopCate'] = $cate->getTopCate();

			$res = D('Instrument_company');
			$company = $res->cate_Com();

			$this->assign('company',$company);
			$this->assign($Ins_Cate);
			$this->display();
		}

		/*
		*阿贾克斯分类管理
		*@author YeWeiBin
		 */
		public function ajaxGetCate()
		{
			$cate = D('Instrument_category');
			$SecondCate = $cate->getSecondCate();

			if(!$SecondCate){
				return false;
			}

			$data  =		'<div class="instrumentfa" onclick="ajaxReCate('.I('get.id').')">';
			$data .=			'<div class="instrumentso">';
			$data .=				'<h4>返回</h4>';
			$data .=				'<span></span>';
			$data .=			'</div>';
			$data .=		'</div>';

			foreach ($SecondCate as $key => $val) {
				$map['appt_category_id'] = $val['id'];
				if(substr_count($val['cate_path'],',')  == 3){
					if(M('Instrument_goods')->where($map)->field('appt_category_id')->find()){
						$data .=		"<a href=/shop/Home/Instrument/category.html?gid=".$val['id'].">";
						$data .=		'<div class="instrumentfa">';
						$data .=			'<div class="instrumentso">';
						$data .=				'<img src="/shop/Public/Uploads'.$val['cate_pic'].'">';
						$data .=				'<h4>'.$val['cate_name'].'</h4>';
						$data .=				'<span>'.$val['id'].'</span>';
						$data .=			'</div>';
						$data .=		'</div>';
						$data .=		'</a>';	
					}	

				}else{

					$map1['parent_id'] = $val['id'];
					if(!M('Instrument_category')->where($map1)->field('parent_id')->find()){
							if(M('Instrument_goods')->where($map)->field('appt_category_id')->find()){
							$data .=		'<a href=/shop/Home/Instrument/category.html?gid='.$val['id'].'><div class="instrumentfa">';
							$data .=			'<div class="instrumentso">';
							$data .=				'<img src="/shop/Public/Uploads'.$val['cate_pic'].'">';
							$data .=				'<h4>'.$val['cate_name'].'</h4>';
							$data .=				'<span>'.$val['id'].'</span>';
							$data .=			'</div>';
							$data .=		'</div></a>';
						}
					}else{
					
							$data .=		'<div class="instrumentfa" onclick="ajaxCate('.$val['id'].')">';
							$data .=			'<div class="instrumentso">';
							$data .=				'<img src="/shop/Public/Uploads'.$val['cate_pic'].'">';
							$data .=				'<h4>'.$val['cate_name'].'</h4>';
							$data .=				'<span>'.$val['id'].'</span>';
							$data .=			'</div>';
							$data .=		'</div>';
						
					}
					
				}
			}

			$this->ajaxReturn($data);

		}

		/*
		*阿贾克斯分类返回
		*@author YeWeiBin
		 */
		public function ajaxReturnCate()
		{
			$cate = D('Instrument_category');
			$parent = $cate->getParentCate();
			$data = '';

			if( $parent[0]['parent_id'] == 0 ){

				foreach ($parent as $key => $val) {

					$data .=		'<div class="instrumentfa" onclick="ajaxCate('.$val['id'].')">';
					$data .=			'<div class="instrumentso">';
					$data .=				'<img src="/shop/Public/Uploads'.$val['cate_pic'].'">';
					$data .=				'<h4>'.$val['cate_name'].'</h4>';
					$data .=				'<span>'.$val['id'].'</span>';
					$data .=			'</div>';
					$data .=		'</div>';					
				}

			}else{
					$data .=		'<div class="instrumentfa" onclick="ajaxReCate('.$parent[0]['parent_id'].')">';
					$data .=			'<div class="instrumentso">';
					$data .=				'<h4>返回</h4>';
					$data .=				'<span></span>';
					$data .=			'</div>';
					$data .=		'</div>';

				foreach ($parent as $key => $val) {
			
						$data .=		'<div class="instrumentfa" onclick="ajaxCate('.$val['id'].')">';
						$data .=			'<div class="instrumentso">';
						$data .=				'<img src="/shop/Public/'.$val['cate_pic'].'">';
						$data .=				'<h4>'.$val['cate_name'].'</h4>';
						$data .=				'<span>'.$val['id'].'</span>';
						$data .=			'</div>';
						$data .=		'</div>';					
														
				}		
			}

			$this->ajaxReturn($data);

		}

		//厂商列表
		public function company()
		{
			$data = D('Instrument_company');
			$company = $data->getData();

			$this->assign( 'company', $company );
			$this->display();
		}

		//分类列表
		public function category()
		{	

			$instrument = D('Instrument_goods');
			$data = $instrument->getData();

			if( !$data['title'] ){
				$this->redirect('Home/Instrument/index');
				exit;
			}

			$this->assign('data',$data);
			$this->display();
		}

		//厂商详情
		public function companyDetail()
		{

			$data = D('Instrument_company');
			$company = $data->getOne();
		
			if( !$company['company'] ){
				$this->error('没有数据',U('Instrument/index'),1);
			}
			$this->assign( 'company', $company );
			$this->display();
		}

		/*
		*仪器详情页
		*@author YeWeiBin
		 */
		public function detail()
		{
			$map['appt_id'] = $_GET['id'];
			$res = M('Instrument_goods')->where($map)->find();
			if(!$res){
				$this->error('没有数据',U('Home/Instrument/index'),1);
				exit;
			}

			$instrument = D('Instrument_goods');
			$data = $instrument->getOne();

			$this->assign('data',$data);
			$this->display();
		}

		/*
		*仪器对比页
		*@author YeWeiBin
		 */
		public function compare()
		{
			$instrument = D('Instrument_goods');
			$goods = $instrument->contrast();
			
			$this->assign( 'goods', $goods );
			$this->display();
		}

		//仪器收藏
		public function collect()
		{
			
			$data['user_id'] = $_SESSION['home_user_info']['user_id'];
			$data['goods_id'] = I('post.id');
			$data['status'] = I('post.status');
		
			$res = M('Instrument_collect')->where('user_id ='.$_SESSION['home_user_info']['user_id'].' and goods_id ='.I('post.id'))->find();
			if( !$res ){
				M('Instrument_collect')->add( $data );	
			}else{
				M('Instrument_collect')->where('user_id ='.$_SESSION['home_user_info']['user_id'].' and goods_id ='.I('post.id'))->save( $data );
			}

			$res = M('Instrument_collect')->where('user_id ='.$_SESSION['home_user_info']['user_id'].' and goods_id ='.I('post.id'))->find();

			$this->ajaxReturn( $res['status'] );

		}
	}