<?php
	namespace Admin\Controller;
	use Think\Controller;

	class HomeController extends CommonController {

		/**
		*首页展示
		*/
		public function index() 
		{

			$this->display();

		}
/*******************************首页显示方式*************************************/

		//首页显示方式管理
		public function homeManage()
		{	
			$data = M('Homepagemanage')->find();

			if( !$data ){
				$data['search'] = 0;
				$data['navigation'] = 0;
				$data['banner'] = 0;
				$data['showservice'] = 0;
				$data['goldproviders'] = 0;
				$data['professional'] = 0;
				$data['case'] = 0;
				$data['need'] = 0;
				M('Homepagemanage')->add( $data );
			}
			
			$res = M('Homecontent')->order('id desc')->find();
			$count = M('Homecontent')->count();
			$len = strlen( $res['content'] );

			$res['addtime'] = $res['addtime'];
			$this->assign( ['data'=> $data,'res'=>$res, 'len'=>$len,'count'=>$count] );
			$this->display();
		}

		//ajax清除缓存表
		public function ajaxClearHome()
		{	
			$id = M('Homecontent')->order('id desc')->find()['id'];

			$res = M('Homecontent')->where($id)->delete();
			$this->ajaxReturn( $res );
		}

		//ajax修改首页显示方式式
		public function ajaxModHomePage()
		{
			$data = I('post.');
			$map['id'] = I('post.id');
			$res = M('Homepagemanage')->where( $map )->save( $data );
			if( !$res ){
				$res = '修改失败';
			}else{
				$res = '修改成功';
			}

			$this->ajaxReturn( $res );

		}

/******************************服务分类管理表****************************************/

		//服务分类
		public function serviceCategory()
	    {
	    	
	    	$category = D('Categorymanage');
	    	$data = $category->getData();
	    	
	    	$this->assign('data',$data);
			$this->display();

		}

		//顶级分类管理
		public function topCategory(){
			$cate = D('Category');
			$data = $cate->showTopCategory();
			$this->assign('data',$data);
			$this->display();

		}

		public function ajaxDelCate()
		{
			$cate = D('category');
			$data = $cate->delCate();
			$this->ajaxReturn($data);
		}

		//ajax查询服务管理表
		public function ajaxServiceSearch()
		{
			$map['manage_parent_id'] = I('post.manage_parent_id');
			$map['team'] = I('post.team');

			$data = M('Categorymanage')->where($map)->join('app_store_category ON app_categorymanage.store_category_id = app_store_category.id')->field('app_categorymanage.*,app_store_category.cate_name
				')->select();
			
			$this->ajaxReturn($data);
		}

		//ajax删除服务管理
		public function ajaxServiceDel()
		{
			$post = I('post.');
			$info = '';
			foreach( $post as $key => $val ){
				$map['manage_parent_id'] = $key;
				$res = M('Categorymanage')->where( $map )->find();
				if( !$res ){
					$map1['manage_id'] = $key;
					$result = M('Categorymanage')->where( $map1 )->delete();
					if( !$result ){
						$info .= $val ."删除失败!";
					}
				}else{
					$info .= $val . "有子级不能删除!";
				}
			}

			if( !$info ){
				$info = '删除成功';
				$status = true; 
				$a = M('Categorymanage')->field('store_category_id')->select();
				foreach( $a as $key => $val ){
					$map2['id'] = $val['store_category_id'];
					$b = M('Store_category')->where( $map2 )->find();
					if( !$b ){
						$map2['store_category_id'] = $val['store_category_id'];
						M('Categorymanage')->where( $map2 )->delete();
					}
				}
			}else{
				$status = false;
			}

			$info = ['status'=>$status,'info'=>$info];
			$this->ajaxReturn( $info );
		}

		//ajax查询服务分类表
		public function ajaxSearchCa()
		{
			$map['parent_id'] = I('post.id');
			$data = M('Store_category')->where( $map )->select();
			foreach( $data as $key => $val ){
				$map['store_category_id'] = $val['id'];
				$res = M('Categorymanage')->where( $map )->find();
				if( $res ){
					unset( $data[$key] );
				}
			}

			if( !$data ){
				$data = false;
			}

			$this->ajaxReturn( $data );
		}

		public function ajaxAddSeCa()
		{
			$post = I('post.');
			$data['team'] = I('post.team');
			$data['manage_parent_id'] = I('post.manage_id');
			$info = '';

			foreach( $post as $key => $val ){
				if( is_numeric($key) ){
					$data['store_category_id'] = $key;
					$res = M('Categorymanage')->add( $data );
					if( !$res ){
						$info .= $val . "未添加成功";
					}
				}
			}

			if( !$info ){
				$info = '添加成功';
			}

			$this->ajaxReturn( $info );
		}

/****************************轮播图管理**********************************/


		//广告轮播
		public function serviceBanner() 
		{
		    $res = D('Advert');
			$arr = $res->getdate();

			$this->assign(['arr'=>$arr['data'],'count'=>$arr['count'] ]);  
			$this->display();
		}

			//上传轮播图
		public function uploadFile()
		{
			
			if( !$_FILES || !I('post.appt_store_id')){
				$res = ['status' => false, 'info' => '内容请填完整'];
	   			$this->ajaxReturn($res);
		   			
		   		}else{
		   			$file = D('Advert');
					$info = $file->fileUp();
					$this->ajaxReturn($info);
		   		}
		}

		//修改状态
		public function ajaxSave()
		{
			$file = D('Advert');
			$res = $file->fileSave();
			$this->ajaxReturn($res);

		}

		//删除轮播图
		public function ajaxDel()
		{
			$info = D('Advert');
			$res = $info->fileDel();
			$this->ajaxReturn($res);

		}

		//ajax查询店铺信息返回给添加轮播图时所用到数据
		public function ajaxSearchStore()
		{	
			$name = I('post.data');
			$map['store_name'] = array('like',$name.'%');

			$res = M('Store')->where($map)->field('id,store_name')->select();
			$this->ajaxReturn($res);

		}

		//ajax查询单条轮播图信息
		public function ajaxSearchAdvertInfo()
		{

			$advert = D('Advert');
			$res = $advert->find();
			$this->ajaxReturn($res);

		}

	
/*******************************推荐服务管理***********************************/
	
		//服务列表
		public function serviceList()
	   {

	   		$result = D('Showservice');
	   		$data = $result->getData();

	   		$this->assign( 'data' ,$data );
			$this->display();

		} 

		//ajax删除推荐服务管理
		public function delShowservice()
		{
			$show = D('Showservice');
			$res = $show->del();
			$this->ajaxReturn( $res );
		}

		//ajax添加推荐服务管理
		public function addShowService()
		{
			$show = D('Showservice');
			$res = $show->addser();
			$this->ajaxReturn( $res );
		}

		//ajax修改服务管理
		public function ajaxmodser()
		{
			$show = D('Showservice');
			$res = $show->modser();
			$this->ajaxReturn( $res );
		}

		//ajax查询服务位置
		public function ajaxSerPosition()
		{
			$show = D('Showservice');
			$res = $show->SerPosition();
	
			$this->ajaxReturn( $res );
		}

		//ajax查询服务详细
		public function ajaxSearSer()
		{
			$show = D('Showservice');
			$res = $show->SearSer();
	
			$this->ajaxReturn( $res );
		}


/*****************************品牌服务商管理**********************************/


		//品牌服务商
		public function goldProviders() 
		{
			$prov = D('goldproviders');
			$data = $prov->getData();
	
			$this->assign('data',$data);
			$this->display();

		} 

		//删除品牌服务商
		public function delGoldProviders()
		{
			$prov = D('goldproviders');
			$res = $prov->del();
			$this->ajaxReturn( $res );
	
		}

		//添加金牌服务商
		public function addGoldProviders()
		{
			$prov = D('goldproviders');
			$res = $prov->addGoldP();
			$this->ajaxReturn( $res );
		}


		//ajax修改排序位置
		public function ajaxMoGoldOrd()
		{
			$res = M('Goldproviders')->where('apps_order='.I('post.order'))->find();
			$now = M('Goldproviders')->where('apps_id='.I('post.apps_id'))->find();
			
			$data['apps_order'] = I('post.order');
			$res1 = M('Goldproviders')->where( 'apps_id='.I('post.apps_id'))->save( $data );
			if( $res1 ){
				$info = ['status'=>true,'info'=>'修改成功'];
				if( $res['apps_order'] ){
					$data['apps_order'] = $now['apps_order'];
					M('Goldproviders')->where('apps_id='.$res['apps_id'])->save( $data );
				}
			}else{
				$info =['status'=>false,'info'=>'修改失败'];
			}


			$this->ajaxReturn( $info );
		}


/*****************************专家展示管理***************************************/


		//专家展示
		public function showProfessional() 
		{
			$store = D('Professional');

			$data = $store->getData();

			$this->assign( 'data', $data );
			$this->display();

		}

		//专家展示-查询个人店铺个人信息
		public function searchUser()
		{

			$user = M('User')->where('user_id='.I('post.user_id'))->field('user_account,user_addtime
,user_email,user_id,user_lasttime,user_phone,user_status,user_type')->find();

			$status = ['启用','禁用'];
			$type = ['A','B','C','子帐号'];
			$user['user_addtime'] = date('Y/m/d/H:i:s',$user['user_addtime']);
			$user['user_lasttime'] = date('Y/m/d/H:i:s',$user['user_lasttime']);
			$user['user_status'] = $status[$user['user_status']];
			$user['user_type'] = $type[$user['user_type']];
			$this->ajaxReturn( $user );

		}

		//专家展示-查询最近订单
		public function searchOrder()
		{	
	
			$order = M('Store_order')->where('order_serviceuserid='.I('post.user_id').' and order_status=4')->order('order_time desc')->limit(20)->select();
			foreach( $order as $key => $val ){
				$order[$key]['order_time'] = date( 'Y-m-d H:i:s', $val['order_time'] );
				if( $order[$key]['order_endtime'] ){
					$order[$key]['order_endtime'] = date( 'Y-m-d H:i:s', $val['order_endtime'] );
				}

				$order[$key]['order_employerid'] = M('User')->where('user_id='.$val['order_employerid'])->find()['user_account'];
			}
			$this->ajaxReturn( $order );
		}

		//添加专家管理显示
		public function addProfessional()
		{
			$pro = D('Professional');
			$res = $pro->addProf();
		
			$this->ajaxReturn( $res );
		}

		//专家展示查询店铺信息
		public function serProSto()
		{
			$res = M('Store')->where('id='.I('post.id'))->find();
			$type = ['个人','企业'];
			$res['store_type'] = $type[ $res['store_type'] ];
			$res['store_addtime'] = date('Y/m/d H:i:s',$res['store_addtime']);

			$this->ajaxReturn( $res );
		}

		//ajax删除专家展示
		public function ajaxDelPro()
		{
			$res = M('Professional')->where('apps_id='.I('post.apps_id'))->delete();
			if( $res ){
				$info = ['status'=>true,'info'=>'删除成功'];
			}else{
				$info = ['status'=>false,'info'=>'删除失败'];
			}

			$this->ajaxReturn( $info );
		}

		//ajax修改专家排序
		public function ajaxModProfRank()
		{
			$data = D('Professional');
			$res = $data->modRank();
			$this->ajaxReturn( $res );
		}
		



		//案例展示
		public function showCase() 
		{
			$cases = D('Cases');
			$data = $cases->getData();

			$this->assign( 'data', $data );
			$this->display();

		}

		//案例查询店铺
		public function serCasesSto()
		{
			$res = M('Store')->where('store_userid='.I('post.id'))->find();

			$type = ['个人','企业'];
			$res['store_type'] = $type[$res['store_type']];
			$res['store_addtime'] = date( 'Y/m/d H:i:s',$res['store_addtime']);
			$this->ajaxReturn( $res );
		}

		//案例查询投标信息
		public function ajaxBidDetail()
		{
			$bid = M('Bid')->where('bid_id='.I('post.id'))->find();
			if( I('post.id') && $bid ){
				$need = M('Need')->where( 'need_id='.$bid['bid_needid'])->find();

				$projectwill = ['非意向','意向可让方案提供方看到需求方的联系信息','中标已经确定,意向失效'];
				$projecthide = ['不隐藏','隐藏'];
				$projecwin = ['未中标','已中标'];
				$projectlook = ['未浏览','已浏览'];
				$mod = ['免费发布','托管模式'];
				$projectstatus = ['征集中','选择方案中','工作中','交付中','需求结束'];
				$bid['bid_projectwill'] = $projectwill[$bid['bid_projectwill']];
				$bid['bid_projecthide'] = $projecthide[$bid['bid_projecthide']];
				$bid['bid_projecwin'] = $projecwin[$bid['bid_projecwin']];
				$bid['bid_projectlook'] = $projectlook[$bid['bid_projectlook']];
				$bid['bid_mod'] = $mod[$bid['bid_mod']];
				$bid['bid_projectstatus'] = $projectstatus[$bid['bid_projectstatus']];


				$bid['bid_projecttime'] = date( 'Y/m/d H:i:s', $bid['bid_projecttime'] );

				$res = ['bid'=>$bid,'need'=>$need];
			}else{
				$res = false;
			}
			

			$this->ajaxReturn( $res );
		}

		//案例查询需求信息
		public function ajaxNeedDetail()
		{
			$need = M('Need')->where('need_id='.I('post.id'))->find();

			if( $need ){
				$status = ['未完善需求','已完善需求','待审核需求','审核成功方案征集','审核失败','中标','关闭'];
				$valid_status = ['有效期内','过期需求'];
				$office_sign = ['默认状态','官方推荐'];
				$viptype = ['非VIP','新VIP请求','已完成VIP需求'];
				$shopid = ['非推送'];
				$prostaue = ['无方案','有方案'];
				$prostepe = ['默认','选择方案中','工作中','交付中','需求结束'];
				$need['catename'] = M('Store_category')->where('id='.$need['need_cateid'])->find()['cate_name'];
				$need['need_time'] = date( 'Y/m/d H:i:s',$need['need_time'] ); 
				$need['need_valid_time'] = date( 'Y/m/d H:i:s',$need['need_valid_time'] ); 
				$need['need_status'] = $status[$need['need_status']];
				$need['need_valid_status'] = $valid_status[$need['need_valid_status']];
				$need['need_office_sign'] = $office_sign[$need['need_office_sign']];
				$need['need_viptype'] = $viptype[$need['need_viptype']];
				if( $need['need_shopid'] == 0 ){
					$need['need_shopid'] = $shopid[$need['need_shopid']];
				}else{

				}

				$need['need_prostaue'] = $prostaue[$need['need_prostaue']];
				$need['need_prostepe'] = $prostepe[$need['need_prostepe']];

			}

			$this->ajaxReturn( $need );
		}

		//案例添加
		public function ajaxAddCase()
		{
			$data = D('Cases');
			$res = $data->addCase();

			$this->ajaxReturn( $res ); 
		}

		//案例删除
		public function ajaxDelCase()
		{
			$data = D('Cases');
			$res = $data->delCase();

			$this->ajaxReturn( $res );
		}

		//案例排序修改
		public function ajaxModCaseRank()
		{
			$data = D('Cases');
			$res = $data->modRank();

			$this->ajaxReturn( $res );
		}

		//案例查找服务
		public function ajaxPubDetail()
		{
			$pub = M('Publish')->where('id='.I('post.id'))->find();

			$pic = M('Service_carousel')->where('sere_id='.$pub['id'])->select();

			$poto = [];
			foreach( $pic as $key => $val ){
				$poto[] = $val['sere_pic1']; 
				$poto[] = $val['sere_pic2']; 
				$poto[] = $val['sere_pic3']; 
				$poto[] = $val['sere_pic4']; 
			}
			
			$pub['pic'] = $poto;
			$cate = explode(',', $pub['pubh_categoryid'] );
			$catename = '';
			foreach( $cate as $key => $val ){
				$catename .=  M('Store_category')->where('id='.$val)->find()['cate_name'] .'->';
			}

			$status = ['审核中','驳回','发布成功'];
			$pub['catename'] = rtrim($catename,'->');
			$pub['pubh_time'] = date( 'Y/m/d H:i:s',$pub['pubh_time'] );
			$pub['pubh_status'] = $status[$pub['pubh_status']];
			$pub['sid'] = M('Store')->where( 'id='.$pub['pubh_shopid'])->find()['id'];
			$this->ajaxReturn( $pub );
		}

		//热门需求
		public function hotNeed() 
		{
			
			$this->display();

		} 

		public function ajaxManageShow()
		{
			$pro = D('goldproviders');
			$pro->manageShow();
		}

		
	}
