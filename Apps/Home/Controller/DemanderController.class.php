<?php
	namespace Home\Controller;

	/**
	 * [我是需方]
	 * @author xiaoweichao [13434808758@163.com]
	 */
	class DemanderController extends CommonController
	{
			public function isDemander ()
			{

				if(!I('session.home_impuser_info')){
					// 获取用户基础id
					$info = I('session.');
					$user_id = $info['home_user_info']['user_id'];

					// 用户详细信息
					$impusers = M('Impuser');
					$map = ['user_id' => ['eq',$user_id]];
					$impuserList = $impusers->where($map)->select();
					$_SESSION['home_impuser_info'] = $impuserList[0];
					//用户认证信息
					$attestations = M('Attestation');
					$map = ['user_id' => ['eq',$user_id]];
					$attestationList = $attestations->where($map)->select();
					for($i = 0; $i < count($attestationList); $i++ ){
						if($attestationList[$i]['attn_status']){
							switch($attestationList[$i]['attn_num']){
								case 1: $attestationList[$i]['attn_num'] = '手';break;
								case 2: $attestationList[$i]['attn_num'] = '邮';break;
								case 3: $attestationList[$i]['attn_num'] = '实';break;
								case 4: $attestationList[$i]['attn_num'] = '支';break;
								case 5: $attestationList[$i]['attn_num'] = '银';break;
								case 6: $attestationList[$i]['attn_num'] = '企';break;
							}
						}
					}
					// dump($attestationList);
					$_SESSION['home_attestation_info'] = $attestationList;
				}

				$this->display();
			}

			public function demanderInfo ()
			{
				// dump(I('session.'));
				$this->display();
			}
			public function estimateDemander ()
			{
				$this->display();
			}
			public function fastDemander ()
			{
				$this->display();
			}
			//------------------------------
			public function finishDemander ()
			{
				$this->getList = I('get.');
				$Needs = D('Need');
				$needsList = $Needs->demanderSelectHandle();
				// dump($needsList);
				$this->assign('needsList',$needsList);
				$this->display();
			}

			public function finishDemanderAct ()
			{
				// if(I('get.action') == 'sel'){
				// 	$Needs = D('Need');
				// 	$needsList = $Needs->demanderFindHandle();
				// 	// dump($needsList);
				// 	$this->assign('needsList',$needsList);
				// 	$this->display();
				// }
				// if(I('get.action') == 'del'){
				// 	$Needs = D('Need');
				//   $result = $Needs->demanderDeleteHandle();
				// 	$this->ajaxReturn($result);
				// }

				// dump(I('get.need_status'));
				$needs = I('get.');
				if(I('get.need_status') == 0){
					$_SESSION['home_user_info']['need_id'] = $needs['need_id'];
					redirect(U('Home/Need/needBudget'));
				}

				if(I('get.need_status') == 1){
					$_SESSION['home_user_info']['need_id'] = $needs['need_id'];
					$_SESSION['home_user_info']['need_description'] = 1;
					redirect(U('Home/Need/needDescription'));
				}

				if(I('get.need_status') == 3){
					redirect(U('Home/Need/needDisplay',['needid'=>$needs['need_id']]));
				}

				if(I('get.need_status') == 5){
					redirect(U('Home/Demander/buyingDemander'));
				}

			}

			//------------------------------------
			public function finishDetailDemander ()
			{
				if(I('get.action') == 'sel'){
					$Needs = D('Need');
					$needsList = $Needs->demanderFindHandle();
					// dump($needsList);
					$this->assign('needsList',$needsList);
					$this->display();
					 
				}
			}
			// public function finishDetailDemanderAct ()
			// {
			// 	if(I('get.action') == 'bak'){
			// 		$this->success();
			// 	}
			// 	if(I('get.action') == 'detailDel'){
			// 		$Needs = D('Need');
			// 		$result = $Needs->demanderDeleteHandle();
			// 		if($result){
			// 			dump($this->getList);
			// 		}
			// 	}
			// }
			//------------------------------
			public function schemeDemander ()
			{
				$this->display();
			}
			public function serviceDemander ()
			{
				$this->display();
			}
			public function sponsorDemander ()
			{
				$this->display();
			}
			/**
			 *  [我买入的服务]
			 */
			public function buyingDemander ()
			{

				$orders = D('store_order');
				$services = M('');
				$orderList = $orders->buyingDemanderList();
				// foreach($orderList['orderList'] as $key => $val){
				// 	dump($val['order_serviceid']);
				//
				// }
				// 分配数据
				$this->assign('orderList',$orderList);
				// 显示模板
				$this->display();
			}
			public function buyingDemanderAct ()
			{

				$data = I('get.');
				if($data['need_id'] > 0){
					//中标订单
					// 待付款 去...
					if($data['order_status'] == 0){
						$_SESSION['home_user_info']['order_status'] = $data['order_status'];
						redirect(U('Home/StoreOrder/bidorderTrusteeship',['id'=>$data['id']]));
					}
					// 以托管 或 发起合同 去...
					if($data['order_status'] == 5 || $data['order_status'] == 6 ||$data['order_status'] == 7){
						$_SESSION['home_user_info']['order_status'] = $data['order_status'];
						redirect(U('Home/StoreOrder/bidorderContract',['id'=>$data['id']]));
					}
					//
					if($data['order_status'] == 4){
						$_SESSION['home_user_info']['order_status'] = $data['order_status'];
						redirect(U('Home/StoreOrder/bidorderEvaluate',['id'=>$data['id']]));
					}

					//
					if($data['order_status'] == 8 || $data['order_status'] == 10){
						$_SESSION['home_user_info']['order_status'] = $data['order_status'];
						redirect(U('Home/StoreOrder/bidorderWorked',['id'=>$data['id']]));
					}

				}else{
					//直接购买的订单
					// 待付款 去...
					if($data['order_status'] == 0){
						$_SESSION['home_user_info']['order_status'] = $data['order_status'];
						redirect(U('Home/StoreOrder/orderTrusteeship',['id'=>$data['id']]));
					}
					// 以托管 或 发起合同 去...
					if($data['order_status'] == 5 || $data['order_status'] == 7){
						$_SESSION['home_user_info']['order_status'] = $data['order_status'];
						redirect(U('Home/StoreOrder/orderContract',['id'=>$data['id']]));
					}
					//
					if($data['order_status'] == 4){
						$_SESSION['home_user_info']['order_status'] = $data['order_status'];
						redirect(U('Home/StoreOrder/orderEvaluate',['id'=>$data['id']]));
					}
					//
					if($data['order_status'] == 8 || $data['order_status'] == 10){
						$_SESSION['home_user_info']['order_status'] = $data['order_status'];
						redirect(U('Home/StoreOrder/orderWorked',['id'=>$data['id']]));
					}

				}

			}
			/**
			 * 	 [个人中心 我是需方 : 订单详情]
			 * 		[xwc] [13434808758@163.com]
			 */
			public function buyingDetailDemander ()
			{
				$id = I('get.id');
				$map = [
					'id' =>['eq',$id]
				];
				$orders = M('store_order');
				$orderOneList = $orders->where($map)->find();
				$this->assign('orderOneList',$orderOneList);
				$this->display();
			}


	}
