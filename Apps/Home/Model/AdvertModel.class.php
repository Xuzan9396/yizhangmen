<?php
	namespace Home\Model;
	use Think\Model;

	/**
	* @author chenyanghui 279029419@qq.com
	*@param [type] 轮播图(Advert)表的数据库操作
	*/
	class AdvertModel extends Model
	{
		//查询结果以数组型式返回
		public function getDate()
		{	

			//查询首页管理状态
			$homemanage = M('Homepagemanage')->order('id desc')->find();

			//读取缓存表
			$onetime = date('Ymd',time());
			$map10['addtime'] = $onetime;
			$dadea = M('Homecontent')->where( $map10 )->find();

			//访问记录
			$ip = $_SERVER['REMOTE_ADDR'];
			$todayip['ip'] = $ip;
			$todayip['view_time'] = date('Ymd');
			$view = M('Homepageview')->where( $todayip )->find();
			if( $view ){
				$times['times'] = $view['times'] +1;
				$times['view_time'] = date('Ymd');
				$viewmap['id'] = $view['id'];
				M('Homepageview')->where( $viewmap )->save( $times );
			}else{
				$api = new \Org\Util\Api();
				$todayip['address'] = $api->ip();
				M('Homepageview')->add( $todayip );
			}
		
			if( !$dadea ){

				//查询导航条是否开启
				 switch( $homemanage['navigation'] ){
			            case '2':
			                $homenavigation = false;
			            break;

			            default:
			                $homenavigation = true;
			            break;
			        }

				//查询轮播图
				$map['advert']['appt_where'] = 1;
				$advert = $this->where($map['advert'])->join('app_store ON app_advert.appt_store_id = app_store.id')->field('app_advert.*,app_store.store_describe')->select();
				//查询分类
				$map['category']['father']['parent_id'] = 0;
				$cfather = M('Store_category')->where($map['category']['father'])->field('id,cate_name,parent_id')->select(); 

				$map0['manage_parent_id'] = 0;
				$map0['team'] = 0;
	
				$category = M('Categorymanage')->where( $map0 )->join( 'app_store_category ON app_store_category.id = app_categorymanage.store_category_id')->select();

				switch( $homemanage['banner'] ){
					case '1':
						$backgroundpic = $this->where( 'appt_where=4' )->field('appt_pic')->select();
						$backgroundnum = rand(0,count($backgroundpic) -1);
						$bannerbackground = $backgroundpic[$backgroundnum]['appt_pic'];
					break;
					case '2':
						$advert = false;
						$cfather = false;
						$category = false;
					break;

					default:
					break;
				}

			
	/*************************************分类管理***********************************************/
				
				

				foreach( $category as $key => $val ){
					$map1['manage_parent_id'] = $val['manage_id'];
					$map1['team'] = 0;
					$map2['manage_parent_id'] = $val['manage_id'];
					$map2['team'] = 1;
					$map3['manage_parent_id'] = $val['manage_id'];
					$map3['team'] = 2;

					$category[$key]['first'] = M('Categorymanage')->where( $map1 )->join( 'app_store_category ON app_store_category.id = app_categorymanage.store_category_id')->select();
					$category[$key]['second'] = M('Categorymanage')->where( $map2 )->join( 'app_store_category ON app_store_category.id = app_categorymanage.store_category_id')->select();
					$category[$key]['third'] = M('Categorymanage')->where( $map3 )->join( 'app_store_category ON app_store_category.id = app_categorymanage.store_category_id')->select();

				}

				foreach( $category as $key => $val ){

					//组1
					foreach( $val['first'] as $k => $v ){
						$map5['manage_parent_id'] = $v['manage_id'];
						$category[$key]['first'][$k]['son'] = M('Categorymanage')->where( $map5 )->join( 'app_store_category ON app_store_category.id = app_categorymanage.store_category_id')->select();
					}

					//组2
					foreach( $val['second'] as $k => $v ){
						$map5['manage_parent_id'] = $v['manage_id'];
						$category[$key]['second'][$k]['son'] = M('Categorymanage')->where( $map5 )->join( 'app_store_category ON app_store_category.id = app_categorymanage.store_category_id')->select();
					}

					//组3
					foreach( $val['third'] as $k => $v ){
						$map5['manage_parent_id'] = $v['manage_id'];
						$category[$key]['third'][$k]['son'] = M('Categorymanage')->where( $map5 )->join( 'app_store_category ON app_store_category.id = app_categorymanage.store_category_id')->select();
					}
				}

	/********************************推荐服务管理******************************************************/
				//查询推荐服务管理
				

				//查询首页管理表显示状态(0为默认显示，1为管理显示)
				$showservicestatus = $homemanage['showservice'];

				//默认显示热门服务(根据成交量大小排列)
				$hot = M('Publish')->join('app_store ON app_publish.pubh_shopid = app_store.id')->field('app_publish.*,app_store.store_describe')->limit(2)->order('pubh_volume desc')->select();

				//查询默认显示排序
				$nomi = M('Publish')->join('app_store ON app_publish.pubh_shopid = app_store.id')->field('app_publish.*,app_store.store_describe')->order('pubh_volume desc')->limit(100)->select();

				switch ( $showservicestatus ) {
				
					case '1':
						$mapz['appe_status'] = 1;
						$nominate = M('Showservice')->where( $mapz )->join('app_publish ON app_showservice.appe_service_id = app_publish.id')->select();

						$nominate1 = [];
						foreach( $nominate as $key => $val ){
							$nominate1[$val['appe_order']] = $nominate[$key];

						}

						$nominate = $nominate1;

						//把默认数据过滤掉已经添加为管理显示的数据
						foreach( $nomi as $key => $val ){
							$res = M('Showservice')->where( 'appe_service_id='.$val['id'])->find();
							if( $res ){
								unset( $nomi[$key] );
							}
						}

						for( $i = 0; $i <= 5; $i++){
							if( !$nominate[$i] ){
								$nominate[$i] = $nomi[$i]; 
							}
						}

						if( $nominate[6] ){
							$hot[0] = $nominate[6];
						}

						if( $nominate[7] ){
							$hot[1] = $nominate[7];
						}

						$nominate1 = [];
						$i = 0;
						foreach( $nominate as $key => $val ){
							$nominate1[] = $nominate[$i];
							$i ++;
						}

						 $nominate = $nominate1;

						break;

						case '2':
							$nominate = false;
							$hot = false;
							break;
					default:

						$nominate = $nomi;
						foreach( $nominate as $key => $val ){
							$nominate[$key]['appe_desc'] = $nominate[$key]['store_describe'];
						}
						break;
				}
			
				foreach( $nominate as $key => $val ){
					$map7['id'] = $val['pubh_shopid'];
					$nominate[$key]['address'] = M('Store')->where( $map7 )->field('store_address')->find()['store_address'];
					if( !$val ){
						unset( $nominate[$key] );
					}
				}

				
	/****************************************品牌服务商管理***************************************/

				$brandstatus = $homemanage['goldproviders'];

				$brand = M('Store')->where('store_type=1')->field('id,store_userid,store_name,store_realname,file_image,store_address,store_describe')->select();

							// 查询店铺所属订单成功交易的数量
							foreach( $brand as $key => $val ){
								$fcom = storefcomment( $val['id'] );
								$brand[$key]['ordernum'] = $fcom['dealnum'];
								
							}

							$sort = array(
						         'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
						         'field'     => 'ordernum',       //排序字段
							 );

							 $arrSort = array();
							 foreach($brand AS $uniqid => $row){
							     foreach($row AS $key=>$value){
							         $arrSort[$key][$uniqid] = $value;
							     }
							 }
							 if($sort['direction']){
							     array_multisort($arrSort[$sort['field']], constant($sort['direction']), $brand);
							 }

				switch ( $brandstatus ) {

					case '1':
						$mapgold['apps_order'] = ['neq',0];
						//查询品牌服务商
						$brandman = M('goldproviders')->join('app_store ON app_goldproviders.apps_store_id = app_store.id')->where( $mapgold )->select();

						$brandman1 = [];
						foreach( $brandman as $key => $val ){
							$brandman1[$val['apps_order']-1] = $brandman[$key];
						}
						
						$brandman = $brandman1;
						
						//过滤
						foreach( $brand as $key => $val ){
							$res = M('goldproviders')->where('apps_store_id='.$val['id'])->find();
							if( $res ){
								unset( $brand[$key] );
							}
						}
						for( $i = 0; $i <= 9;$i++ ){
							if( !$brandman[$i] ){
								$brandman[$i] = $brand[$i];
							}
						}

						ksort( $brandman );

						$brand = $brandman;

						break;

						case '2':
							$brand = false;
						break;
					
					default:
							
						break;
				}
				
				foreach( $brand as $key => $val ){
					$fcom = storefcomment( $val['id'] );
					$brand[$key]['countorder'] = $fcom['dealnum'];
					$brand[$key]['emp'] = $fcom['fcomment'] .'%';
					$brandmap['pubh_shopid'] = $val['id'];
					$order = M('Publish')->where( $brandmap )->limit(2)->order('pubh_volume desc')->select();
					$brand[$key]['publish'] = $order;
				}			

				foreach( $brand as $key => $val ){
					if( !$val['id'] || !$val['countorder'] ){
						unset( $brand[$key] );
					}
				}


	/***********************************专家展示管理********************************************/
	//专家展示

			$porstatus = $homemanage['professional'];

			$expert = M('Store')->where('store_type = 0')->field('id,store_userid,store_name,file_image,store_describe')->select();

			foreach( $expert as $key => $val ){
				$expert[$key]['apps_storeid'] = $val['id'];
			}

			switch( $porstatus ){
				case '1':
					$mappor['apps_rank'] = ['neq',0];
					$expertman = M('Professional')->where( $mappor )->join('app_store ON app_Professional.apps_storeid=app_store.id')->field('app_professional.*,app_store.store_userid,store_name,file_image,store_describe')->select();

					foreach( $expertman as $key => $val ){
						$expertman[$key]['id'] = $val['apps_id'];
					}

					$expertman1 = [];
					foreach( $expertman as $key => $val ){
						$expertman1[$val['apps_rank']-1] = $expertman[$key]; 
					}

					//过滤
					foreach( $expert as $key => $val ){
						$res = M('Professional')->where( 'apps_storeid='.$val['id'] )->find();
						if( $res ){
							unset( $expert[$key] );
						}
					}

					$expertman = $expertman1;

					for( $i = 0; $i <= 5; $i ++ ){
						if( !$expertman[$i] ){
							$expertman[$i] = $expert[$i];
							unset( $expert[$i] );
						}
					}
					ksort( $expertman );
					$expert = $expertman;
					
				break;

				case '2':
					$expert = false;
				break;

				default:
					
				break;
			}
				
				//查询店铺下的所属服务
				foreach( $expert as $key => $val ){
					$mapexp['pubh_shopid'] = $val['id'];
					$exppub = M('Publish')->where( $mapexp )->field('id,pubh_categoryid')->order('pubh_categoryid')->select();
					if( $exppub ){
						$expert[$key]['pub'] = $exppub;
					}else{
						unset( $expert[$key] );
					}
				}

				//删除重复服务分类
				foreach( $expert as $key => $val ){
					foreach( $val['pub'] as $k => $v ){
						if( $val['pub'][$k+1]['pubh_categoryid'] == $v['pubh_categoryid'] ){
							unset( $expert[$key]['pub'][$k] );
						}
					}
				}

				//重组数组
				 foreach( $expert as $key => $val ){
				 	$p = [];
				 	$i = 0;
				 	foreach( $val['pub'] as $k => $v ){
				 		$p[$i] = $val['pub'][$k];
				 		$i ++;
				 	}
				 	$expert[$key]['pub'] = $p;
				 }
			

				//截取服务ID
				foreach( $expert as $key => $val ){
					foreach( $val['pub'] as $k => $v ){
						$expert[$key]['pub'][$k]['pubh_categoryid'] = substr( $v['pubh_categoryid'], 6, 2 );
					}
				}

				//查询服务分类名称
				foreach( $expert as $key => $val ){
					$i = '';
					foreach( $val['pub'] as $k => $v ){
						$c = M('Store_category')->where('id = '.$v['pubh_categoryid'])->field('cate_name')->find()['cate_name'] . ' ';
						
						$i .= $c;
					}

					if( !$i ){
						$i = '无';
					}

					//拼接上服务名称
					$expert[$key]['catename'] = $i;
				}			

				//计算评价
				foreach( $expert as $keys => $value ){
					$fcom = storefcomment( $value['apps_storeid'] );
					$expert[$keys]['emp'] = $fcom['fcomment'];
					$expert[$keys]['good'] = $fcom['gcom'];
					$expert[$keys]['shopper'] = $fcom['comnum'];
					
				}

				foreach( $expert as $key => $val ){
					$mapexp2['pubh_shopid'] = $val['id'];
					$pub = M('Publish')->where( $mapexp2 )->order('pubh_volume desc')->field('pubh_title,pubh_price')->select();
					if( $pub ){
						$expert[$key]['pubtitle'] = $pub[0]['pubh_title'];
						$expert[$key]['pubprice'] = '￥'.$pub[0]['pubh_price'];
					}

					if( $expert[$key]['emp'] < 60 ){
						unset( $expert[$key] );
					}
				}




	/******************************************************************************************/
	//案例展示

		$order = M('Store_order')->field('id,order_serviceid,order_myfile')->order('order_serviceid')->select();
		foreach( $order as $key => $val ){
					if( $order[$key +1]['order_serviceid'] == $val['order_serviceid'] ){
						unset( $order[$key] );
					}
				}

				foreach( $order as $key => $val ){
					$mapcase['id'] = $val['order_serviceid'];
					$mapcase['pubh_status'] = 2;
					$serv = M('Publish')->where( $mapcase )->find();
					$order[$key]['servicetitle'] = $serv['pubh_title'];
					$order[$key]['servicepic'] = $serv['pubh_pic'];
					$order[$key]['storeid'] = $serv['pubh_shopid'];

				}

				foreach( $order as $key => $val ){
					if( !$val['servicepic'] ){
						unset( $order[$key] ); 
					}
				}

				$order1 = [];
				foreach( $order as $key => $val ){
					$order1[] = $order[$key];
				}

				$order = $order1;

		switch( $homemanage['case'] ){
			case '1':
				$mapnot['rank'] = ['neq',0];
				$cases = M('Cases')->join('app_store_order ON app_cases.orderid = app_store_order.id')->field('app_cases.*,app_store_order.order_serviceid,order_myfile')->where( $mapnot )->select();

				$cases1 = [];
				foreach( $cases as $key => $val ){
					$cases1[$val['rank']-1] = $cases[$key];
				}

				$cases = $cases1;

				foreach( $cases as $key => $val ){
					$mapcase['id'] = $val['order_serviceid'];
					$mapcase['pubh_status'] = 2;
					$serv = M('Publish')->where( $mapcase )->find();
					$cases[$key]['servicetitle'] = $serv['pubh_title'];
					$cases[$key]['servicepic'] = $serv['pubh_pic'];
					$cases[$key]['storeid'] = $serv['pubh_shopid'];
				}

				for( $i = 0; $i <= 6; $i ++ ){
					if( !$cases[$i] ){
						$cases[$i] = $order[$i];
					}
				}
				
			break;
			case '2':
				$cases = false;
			break;

			default:
				$cases = $order;
				
			break;
		}
	    ksort( $cases );
		$one = $cases[0];
		unset( $cases[0] );
		
	/****************************************************************************************/
	//热门需求

			switch( $homemanage['need'] ){
				case '2':
					$need = false;
				break;

				default:
					$need = M('Need')->order('need_title')->field('need_id,need_user,need_title,need_view')->limit(500)->select();
				break;
			}
				
				foreach( $need as $key => $val ){
					if( $need[$key+1]['need_title'] == $val['need_title'] ){
						unset( $need[$key] );
					}
				}

				foreach( $need as $key => $val ){
					$ma['need_title'] = $val['need_title'];
					
					$count = M('Need')->where( $ma )->count();
					if( !$count ){
						$count = 0;
					}

					$need[$key]['num'] = $count;
				}

				$sernum = M('Publish')->count();
				
				//准备一个函数将数字转换成中文
				function change( $par ){
					$num =  strlen( $par );
					switch( $num ){
						case '1' : $num = '' ; break;
						case '2' : $num = '十'; break;
						case '3' : $num = '百'; break;
						case '4' : $num = '千'; break;
						case '5' : $num = '万'; break;
						case '6' : $num = '十万'; break;
						case '7' : $num = '百万'; break;
						case '8' : $num = '千万'; break;
						case '9' : $num = '亿'; break;
					}

					$par = substr( $par, 0, 1 );

					switch( $par ){
						case '1' : $par = '一'; break;
						case '2' : $par = '二'; break;
						case '3' : $par = '三'; break;
						case '4' : $par = '四'; break;
						case '5' : $par = '五'; break;
						case '6' : $par = '六'; break;
						case '7' : $par = '七'; break;
						case '8' : $par = '八'; break;
						case '9' : $par = '九'; break;
					}

					return $par . $num;
				}

				//调用函数转换
				$sernum = change( $sernum );

				//查询交易数量
				$deal = M('Store_order')->count();
				$deal = number_format( $deal );

	/****************************************************************************************************/
				$dataFather = [ 'homenavigation' => $homenavigation,'bannerbackground'=>$bannerbackground,'advert' => $advert,'category' =>$category,'nominate' => $nominate, 'hot' => $hot,'brand' => $brand,'expert' => $expert,'need' => $need, 'sernum' => $sernum, 'deal'=>$deal, 'successfulcase'=> $cases, 'one'=>$one];

					//设定存储缓存表
					$dataFather1 = json_encode( $dataFather );
					$dataf['content'] = $dataFather1;
					$dataf['addtime'] = $onetime;
					$res = M('Homecontent')->add( $dataf );
			}else{

				$dataFather = json_decode( $dadea['content'], true );
			}
			
			return $dataFather;


		}

	}