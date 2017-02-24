<?php

namespace Home\Controller;

/**
     * [店铺订单表].
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
    class StoreOrderController extends CommonController
    {
		// 案例
		public function storeManagement()
		{
			$this->assign();
			$this->display();
		}

        //直接购买托管页面
        // jinjun<757258777@qq.com>
        public function orderTrusteeship()
        {
            $status = session('home_user_info')['order_status'];
            if($status == 0){
                if(IS_GET){
                    // 实例化
                    $store_order = D('store_order');
                    // 接收返回值
                    $trusteeship_list = $store_order->orderTrusteeshipList();
                    //分配数据
                    $this->assign($trusteeship_list);
                    $this->display();
                }

                if(IS_POST){
                    // 实例化
                    $store_order = D('store_order');
                    //接受返回值
                    $trusteeship_save = $store_order->orderTrusteeshipSave();
                    //判断
                    if($trusteeship_save['status'] !== false){
                        // session 保存状态
                        session('home_user_info.order_status',5);
                        redirect(U('Home/StoreOrder/orderContract'));
                    }else{
                        //失败
                        $this->error('操作有误'.$trusteeship_save['error_info'],U('Home/StoreOrder/orderTrusteeship'),3);
                    }
                }
            }else{
                $this->error();
            }

        }
        //取消订单
        // jinjun<757258777@qq.com>
        public function orderRemove()
        {
            // 实例化
            $store_order = D('store_order');
            // 接收返回值
            $order_remove = $store_order->orderRemove();
            //判断
            if($order_remove !== false){
                redirect(U('Home/Index/Index'));
            }else{
                //失败
                $this->error();
            }
        }
        //直接购买合同页面
        // jinjun<757258777@qq.com>
        public function orderContract()
        {
            // session获取
            $status = session('home_user_info')['order_status'];
            if($status == 5 || $status == 7){
                //同一合同 接收参数
                if(I('get.status') == 7){
                    // 更改状态
                    $contract_save = D('store_order')->orderContractSave();
                    // 判断是否成功
                    if($contract_save['status'] !== false){
                        // 保存session
                        session('home_user_info.order_status',8);
                        redirect(U('Home/StoreOrder/orderWorked'));
                    }else{
                        //失败
                        $this->error('操作有误'.$contract_save['error_info'],U('Home/StoreOrder/orderContract'),3);
                    }
                }
                // 实例化
                $store_order = D('store_order');
                // 接收返回值
                $contract_list = $store_order->orderTrusteeshipList();
                //分配数据
                $this->assign($contract_list);
                $this->display();
            }else{
                $this->error();
            }

        }

        //合同文件下载方法
        // jinjun<757258777@qq.com>
        public function accessoryDownLoad()
        {
            $id = I('get.id');
            $map['id'] = ['eq' , $id];
            $accessory = M('accessory')->where($map)->find();
            if($accessory){
                $url =C('IMG_rootPath').$accessory['order_url'];
                down($url);
            }else{
                $this->error('暂无合同');
            }
        }

        //补充合同文件下载方法
        // jinjun<757258777@qq.com>
        public function supDownLoad()
        {
            $id = I('get.id');
            $map['sup_id'] = ['eq' , $id];
            $sup = M('supplement')->where($map)->find();
            if($sup){
                $url =C('IMG_rootPath').$sup['sup_url'];
                down($url);
            }else{
                $this->error('暂无合同');
            }
        }

        // 提醒服务商发起合同 remindContract
        // jinjun<757258777@qq.com>
        public function remindContract()
        {
            $post = I('post.');
            $post['mesm_sendtime'] = time();
            // 实例化
            $mesm = M('messagesystem')->add($post);
            //返回
            $this->ajaxReturn($mesm);
        }

        // 直接购买工作中页面
        // jinjun<757258777@qq.com>
        public function orderWorked()
        {
            //session 判断页面跳转
            $status = session('home_user_info')['order_status'];
            if($status == 8 || $status == 10){
                if(IS_GET){
                    // 实例化
                    $store_order = D('store_order');
                    if(I('get.status') == 4){
                        // 已经托管了全额不用再提交尾款
                       $worked_status_save = $store_order->orderWorkedStatusSave();
                       //判断
                       if($worked_status_save !== false){
                            session('home_user_info.order_status',4);
                            redirect(U('Home/StoreOrder/orderEvaluate'));
                       }else{
                            //失败
                            $this->error('操作有误',U('Home/StoreOrder/orderWorked'),3);
                            
                       }

                    }
                    // 接收返回值
                    $worked_list = $store_order->orderTrusteeshipList();
                    //分配数据
                    $this->assign($worked_list);
                    $this->display();
                }

                if(IS_POST){
                    //实例化
                    $store_order = D('store_order');
                    //接收返回值
                    $worked_save = $store_order->orderWorkedSave();

                    if($worked_save['status'] !== false){
                        session('home_user_info.order_status',4);
                        // 服务表的成交量
                        $model = M('Publish');
                        $model->where('id='.session('publish_id'))->setInc('pubh_volume',session('publish_total'));
                        // 跳转
                        redirect(U('Home/StoreOrder/orderEvaluate'));
                    }else{
                         //失败
                        $this->error('操作有误'.$worked_save['error_info'],U('Home/StoreOrder/orderWorked'),3);
                    }
                }
            }else{
                $this->error();
            }
        }

        //直接购买补充合同
        // jinjun<757258777@qq.com>
        public function OrderSupplement()
        {   
            $id = I('post.orderid');

            // 数据
            $config = [
                'maxSize' => 3145728,
                'savePath' => 'order/',
                'saveName' => ['uniqid',''],
                'exts' => ['jpg', 'gif', 'png', 'jpeg', 'txt', 'doc', 'docx', 'ptf'],
                'autoSub' => true,
                'subName' => ['date','Ym'],
                'rootPath' =>  './Public/Uploads/'
            ];
            // 实例化
            $upload = new \Think\Upload($config);
            // 文件上传
            $info  =  $upload->upload($_FILES);
            //拼接路径
            $post['sup_url'] = $info['supplement-file']['savepath'] . $info['supplement-file']['savename'];
            // 文件大小
            $post['sup_small'] = $info['supplement-file']['size'] / 1024 /1024;
            // 添加时间
            $post['sup_addtime'] = time();
            // 订单id
            $post['sup_orderid'] = $id;

            // 添加
            $sup_file = M('supplement')->add($post);

            //判断
            if($sup_file !== false){
                redirect(U('Home/StoreOrder/orderWorked',['id'=>$id]));
            }else{
                //失败
                $this->error('上传失败',U('Home/StoreOrder/orderWorked'),3);
            }

        }

        //服务商文件下载方法
        // jinjun<757258777@qq.com>
        public function accessoryServiceDownLoad()
        {	
        	// 接收id
            $id = I('get.id');
            $map['id'] = ['eq' , $id];
            // 查询 服务商原件表
            $ordervoucher = M('accessory_service')->where($map)->find();
            // 判断
            if($ordervoucher){
                $url =C('IMG_rootPath').$ordervoucher['order_url'];
                down($url);
            }else{
                $this->error('暂无原件');
            }
        }

        // 直接购买待评价页面
        // jinjun<757258777@qq.com>
        public function orderEvaluate()
        {
            //session 判断页面跳转
            $status = session('home_user_info')['order_status'];
            if($status == 4){
                // 实例化
                $store_order = D('store_order');
                //接收返回值
                $evaluate_list = $store_order->orderTrusteeshipList();
                $this->assign($evaluate_list);
                $this->display();
                if(IS_POST){
                    //实例化
                    $store_order = D('store_order');
                    //接收返回值
                    $evaluate_save = $store_order->orderEvaluateSave();
                    // 判断
                    if($evaluate_save['status'] !== false){
                            session('home_user_info.order_status',null);
                            session('home_user_info.order_id',null);
                            redirect(U('Home/Index/Index'));
                        }else{
                             //失败
                            $this->error('操作有误'.$evaluate_save['error_info'],U('Home/StoreOrder/orderEvaluate'),3);
                        }
                }
            }else{
                $this->error();
            }
        }

        //中标订单托管页面
        // jinjun<757258777@qq.com>
        public function bidOrderTrusteeship()
        {
            //session 判断页面跳转
            $status = session('home_user_info')['order_status'];
            if($status == 0){
                if(IS_GET){
                    // 实例化
                    $store_order = D('store_order');
                    //选择线下时
                    if(I('get.status') == 'six'){
                        // 接收返回值
                        $bid_status_save = $store_order->bidStatusSave();
                        //判断
                        if($bid_status_save !== false){
                            // session 保存状态
                            session('home_user_info.order_status',6);
                            redirect(U('Home/StoreOrder/bidorderContract'));
                        }else{
                            //失败
                            $this->error('操作有误',U('Home/StoreOrder/bidorderTrusteeship'),3);
                        }
                    }
                    // 接收返回值
                    $bid_trusteeship_list = $store_order->bidOrderTrusteeshipList();
                    //分配数据
                    $this->assign($bid_trusteeship_list);
                    $this->display();
                }

                if(IS_POST){
                    // 实例化
                    $store_order = D('store_order');
                    //接受返回值
                    $bidtrusteeship_save = $store_order->bidorderTrusteeshipSave();
                    //判断
                    if($bidtrusteeship_save['status'] !== false){
                        // session 保存状态
                        session('home_user_info.order_status',5);
                        redirect(U('Home/StoreOrder/bidorderContract'));
                    }else{
                        //失败
                        $this->error('操作有误'.$bidtrusteeship_save['error_info'],U('Home/StoreOrder/bidorderTrusteeship'),3);
                    }
                }
            }else{
                $this->error();
            }

        }

        //取消中标
        // jinjun<757258777@qq.com>
        public function bidorderRemove()
        {    
            // 获取需求表id
            $needid = I('get.order_needid');
            // 实例化
            $store_order = D('store_order');
            // 接收返回值
            $order_remove = $store_order->bidorderRemove();
            //判断
            if($order_remove['status'] !== false){
                redirect(U('Home/Need/needDisplay',['needid'=>$needid]));
            }else{
                //失败
                $this->error('操作有误'.$order_remove['error_info'],U('Home/StoreOrder/bidorderTrusteeship'),3);
            }
        }

        //中标订单合同交易类型页面
        // jinjun<757258777@qq.com>
        public function bidOrderContract()
        {   
            // session获取
            $status = session('home_user_info')['order_status'];
            if($status == 5 ||  $status == 6 || $status == 7)
            {
	            if(IS_GET){
	                // 实例化
	                $store_order = D('store_order');
	                // 接收返回值
	                $bid_trusteeship_list = $store_order->bidOrderTrusteeshipList();
	                //分配数据
	                $this->assign($bid_trusteeship_list);
	                $this->display();
	            }

	            if(IS_POST){
	                // 实例化
	                $store_order = D('store_order');
	                // 接收返回值
	                $contract_save = $store_order->bidOrderContractSave();
	                //判断
	                if($contract_save['status'] !== false){
	                    // session 保存状态
	                    session('home_user_info.order_status',8);
	                    redirect(U('Home/StoreOrder/bidorderWorked'));
	                }else{
	                    //失败
	                    $this->error('操作有误'.$contract_save['error_info'],U('Home/StoreOrder/bidOrderContract'),3);
	                }
	            }
	        }
    	}

        //中标补充合同
        // jinjun<757258777@qq.com>
        public function bidOrderSupplement()
        {   
            $id = I('post.orderid');
            // 数据
            $config = [
                'maxSize' => 3145728,
                'savePath' => 'order/',
                'saveName' => ['uniqid',''],
                'exts' => ['jpg', 'gif', 'png', 'jpeg', 'txt', 'doc', 'docx', 'ptf'],
                'autoSub' => true,
                'subName' => ['date','Ym'],
                'rootPath' =>  './Public/Uploads/'
            ];
            // 实例化
            $upload = new \Think\Upload($config);
            // 文件上传
            $info  =  $upload->upload($_FILES);
            //拼接路径
            $post['sup_url'] = $info['supplement-file']['savepath'] . $info['supplement-file']['savename'];
            // 文件大小
            $post['sup_small'] = $info['supplement-file']['size'] / 1024 / 1024;
            // 添加时间
            $post['sup_addtime'] = time();
            // 订单id
            $post['sup_orderid'] = $id;
            // 添加
            $sup_file = M('supplement')->add($post);
            //判断
            if($sup_file !== false){
                redirect(U('Home/StoreOrder/bidorderWorked',['id'=>$id]));
            }else{
                //失败
                $this->error('上传失败',U('Home/StoreOrder/bidorderWorked'),3);
            }

        }


        //中标订单确认工作付款页面
        // jinjun<757258777@qq.com>
        public function bidOrderWorked()
        {   
            //session 判断页面跳转
            $status = session('home_user_info')['order_status'];
            if($status == 8 || $status == 10){
                if(IS_GET){
                    // 实例化
                    $store_order = D('store_order');
                    if(I('get.status') == 4){
                        // 已经托管了全额不用再提交尾款
                       $worked_status_save = $store_order->bidorderWorkedStatusSave();
                       //判断
                       if($worked_status_save !== false){
                            session('home_user_info.order_status',4);
                            redirect(U('Home/StoreOrder/bidorderEvaluate'));
                       }else{
                            //失败
                            $this->error('操作有误',U('Home/StoreOrder/bidorderWorked'),3);
                       }

                    }
                    // 接收返回值
                    $bidorder_worked_list = $store_order->bidOrderTrusteeshipList();
                    //分配数据
                    $this->assign($bidorder_worked_list);
                    $this->display();
                }
                if(IS_POST){
                    // 实例化
                    $store_order = D('store_order');
                    // 接收返回值
                    $worked_save = $store_order->bidOrderWordedSave();
                    // 判断
                    if($worked_save['status'] !== false){
                        // session 保存状态
                        session('home_user_info.order_status',4);
                        redirect(U('Home/StoreOrder/bidOrderEvaluate'));
                    }else{
                        //失败
                        $this->error('操作有误'.$worked_save['error_info'],U('Home/StoreOrder/bidOrderWorked'),3);
                    }
                }
            }else{
                $this->error();
            }
        }
        //中标订单评价页面
        // jinjun<757258777@qq.com>
        public function bidOrderEvaluate()
        {   
            //session 判断页面跳转
            $status = session('home_user_info')['order_status'];
            if($status == 4){
                if(IS_GET){
                    // 实例化
                    $store_order = D('store_order');
                    // 接收返回值
                    $bidorder_evaluat_list = $store_order->bidOrderTrusteeshipList();
                    //分配数据
                    $this->assign($bidorder_evaluat_list);
                    $this->display();
                }
                if(IS_POST){
                    // 实例化
                    $store_order = D('store_order');
                    // 接收返回值
                    $bidorder_evaluat_Save = $store_order->bidOrderEvaluateSave();
                    // 判断
                    if($bidorder_evaluat_Save['status'] !== false){
                        // session 保存状态
                        session('home_user_info.order_status',null);
                        session('home_user_info.order_id',null);
                        redirect(U('Home/Index/index'));
                    }else{
                        //失败
                        $this->error('操作有误'.$bidorder_evaluat_Save['error_info'],U('Home/StoreOrder/bidOrderEvaluate'),3);
                    }
                }
            }else{
                $this->error();
            }
        }

        
    }
