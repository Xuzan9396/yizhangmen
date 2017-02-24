<?php
	namespace Admin\Model;
	use Think\Model;

	class HomepageviewModel extends Model
	{
		public function getData()
		{
			$datalog = $this->count();

			//今天
			$map['view_time'] = date('Ymd');
			$today = $this->where( $map )->count();

			//昨天
			$map1['view_time'] = date('Ymd') - 1;
			$yesterday = $this->where( $map1 )->count();

			//今天0：00
			$tod = date('Ymd');
	        $time = strtotime( $tod.'00:00:00');

	        $todumap['user_addtime'] = ['gt', $time];
	        $toduser = M('User')->where( $todumap )->count();

	        $countuser = M('User')->count();
	        $countpub = M('Publish')->where('pubh_status=2')->count();
	        $countorder = M('Store_order')->where('order_status=4')->count(); 

	        //查询最后注册用户
	        $lastuser = M('User')->order('user_id desc')->field('user_account,user_addtime,user_type')->limit(5)->select();

	        $type = ['A类用户','B类用户','C类用户','子帐号'];
	        foreach( $lastuser as $key => $val ){
		  		$lastuser[$key]['addtime'] = date('Y/m/d H:i:s',$val['user_addtime']);
		  		$diff = difference($val['user_addtime']);
		     	if( $diff['minute'] < 60 ){
		     		$lastuser[$key]['newstime'] = $diff['minute'] . '分钟前';
		     	}

		     	if( $diff['hour'] ){
		     		$lastuser[$key]['newstime'] = $diff['hour'] . '小时前';
		     	}

		     	if( $diff['date'] ){
		     		$lastuser[$key]['newstime'] = $diff['date'] . '天前';
		     	}
		     	$lastuser[$key]['desc'] = '注册了'.$type[$val['user_type']];
	        }
	  		
	        //查询店铺
	     	$laststore = M('Store')->order('id desc')->field('store_userid,store_name,store_type,store_status,store_addtime,file_image,cate_status')->limit(5)->select();

	     	$type = ['个人店铺','企业店铺'];
	     	$status = ['还未验证','已通过验证'];
	     	foreach( $laststore as $key => $val ){
	     		$laststore[$key]['user_account'] = M('User')->where('user_id='.$val['store_userid'])->find()['user_account'];

	     		$laststore[$key]['desc'] = '开了'.$type[$val['store_type']] .'-'.$val['store_name'].','.$status[$val['cate_status']];
	     		$laststore[$key]['portrait'] = $val['file_image'];
	     		$laststore[$key]['addtime'] = date('Y/m/d H:i:s',$val['store_addtime']);

		     	$diff = difference($val['store_addtime']);
		     	if( $diff['minute'] ){
		     		$laststore[$key]['newstime'] = $diff['minute'] . '分钟前';
		     	}
		     	if( $diff['hour'] ){
		     		$laststore[$key]['newstime'] = $diff['hour'] . '小时前';
		     	}

		     	if( $diff['date'] ){
		     		$laststore[$key]['newstime'] = $diff['date'] . '天前';
		     	}

	     	}

	     	//查询服务
	     	$mappub['pubh_status'] = 2;
	     	$mappub['pubh_time'] = ['gt',$time];
	     	$lastpub = M('Publish')->order('id desc')->limit(5)->field('pubh_shopid,pubh_title,pubh_price,pubh_pic,pubh_status,pubh_time')->select();
	     	$todpub = M('Publish')->where( $mapub)->count();

	     	$status = ['审核中','驳回','发布成功'];
	     	foreach( $lastpub as $key => $val ){
	     		$userid = M('Store')->where('id='.$val['pubh_shopid'])->find()['store_userid'];
	     		$lastpub[$key]['user_account'] = M('User')->where( 'user_id='.$userid )->find()['user_account'];
	     	
     			$lastpub[$key]['desc'] = '发布了服务-'.$val['pubh_title'].',报价<span style="color:red">￥'.number_format(round($val['pubh_price'])).'</span>,'. $status[$val['pubh_status']];
     			$lastpub[$key]['addtime'] = date('Y/m/d H:i:s',$val['pubh_time']);
	     		$diff = difference($val['pubh_time']);
	     		if( $diff['minute'] ){
	     			$lastpub[$key]['newstime'] = $diff['minute'] . '分钟前';
	     		}
	     		if( $diff['hour'] ){
	     			$lastpub[$key]['newstime'] = $diff['hour'] . '小时前';
	     		}
	     		if( $diff['date'] ){
	     			$lastpub[$key]['newstime'] = $diff['date'] . '天前';
	     		}
	     	}

	     	//查询订单
	     	$maporder['order_status'] = 4;
	     	$maporder['order_endtime'] = ['gt',$time];
	     	$lastorder = M('Store_order')->where('order_status=4')->order('id desc')->limit(5)->select();

	     	$todorder = M('Store_order')->where( $maporder )->count();

	     	foreach( $lastorder as $key => $val ){
	     		$lastorder[$key]['user_account'] = M('User')->where('user_id='.$val['order_employerid'])->find()['user_account'];
	     		$pub = M('Publish')->where('id='.$val['order_serviceid'])->find();
	     		$lastorder[$key]['desc'] = '购买了'.$val['order_number_total'].'个,'.$pub['pubh_title'].',出价<span style="color:red">￥'.number_format(round($val['order_number_price'])).'</span>';
	     		$lastorder[$key]['addtime'] = date('Y/m/d H:i:s',$val['order_time']);

	     		$diff = difference($val['order_time']);
	     		if( $diff['minute']){
	     			$lastorder[$key]['newstime'] = $diff['minute'] . '分钟前';
	     		}
	     		if( $diff['hour']){
	     			$lastorder[$key]['newstime'] = $diff['hour'] . '小时前';
	     		}
	     		if( $diff['date']){
	     			$lastorder[$key]['newstime'] = $diff['date'] . '天前';
	     		}
	     	}

	     	//查询需求
	     	$lastneed = M('Need')->order('need_id desc')->limit(5)->select();
	     	$status = ['未完善需求','已完善需求','待审核需求','审核成功方案征集','审核失败','中标','关闭'];
	     	foreach( $lastneed as $key => $val ){
	     		$lastneed[$key]['user_account'] = M('User')->where('user_id='.$val['need_userid'])->find()['user_account'];
	     		$lastneed[$key]['desc'] = '发布了需求-'.$val['need_title'].',预算金:<span style="color:red">￥'.number_format(round($val['need_budget'])).'</span>,'.$status[$val['need_status']];
     			$lastneed[$key]['addtime'] = date('Y/m/d H:i:s',$val['need_time']);
	     		$diff = difference($val['need_time']);
	     		if( $diff['minute']){
	     			$lastneed[$key]['newstime'] = $diff['minute'] . '分钟前';
	     		}
	     		if( $diff['hour']){
	     			$lastneed[$key]['newstime'] = $diff['hour'] . '小时前';
	     		}
	     		if( $diff['date']){
	     			$lastneed[$key]['newstime'] = $diff['date'] . '天前';
	     		}
	     	}
	   
	     	$news = [];
	     	$news[] = $lastuser;
	     	$news[] = $laststore;
	     	$news[] = $lastpub;
	     	$news[] = $lastorder;
	     	$news[] = $lastneed;
	     	$news1 = [];

	     	foreach( $news as $key => $val ){
	     		foreach( $val as $k => $v ){
	     			$news1[] = $v;
	     		}
	     	}

	     	$news = $news1;
	     	$sort = array(
		         'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
		         'field'     => 'addtime',       //排序字段
			 );

			 $arrSort = array();
			 foreach($news AS $uniqid => $row){
			     foreach($row AS $key=>$value){
			         $arrSort[$key][$uniqid] = $value;
			     }
			 }
			 if($sort['direction']){
			     array_multisort($arrSort[$sort['field']], constant($sort['direction']), $news);
			 }

			return ['datalogv'=>$datalog,'todayv'=>$today,'yesterdayv'=>$yesterday,'toduser'=>$toduser,'countuser'=>$countuser,'countpub'=>$countpub,'countorder'=>$countorder,'todpub'=>$todpub,'todorder'=>$todorder,'news'=>$news];
		}

		
	}