<?php

namespace Home\Controller;

use Think\Controller;

    /*
    *支付中心
    *@author YeWeiBin
     */

    class PayController extends Controller
    {
        protected $order;
        protected $res;

        public function index()
        {
            $id = I('post.uid');
            $model = M('StoreOrder');
            $bool = $model->where('id='.$id)->setField('order_status', 5);
            if($bool !==false){
                session('home_user_info.order_status', 5);
                redirect(U('Home/StoreOrder/orderContract',array('id'=>$id)));
            }else{
                $this->error('支付失败');
            }
        }

        public function order()
        {
            if (IS_POST) {
                // 设置脚本的运行时间脚本执行上传后结束
                set_time_limit(0);
                $model = D('StoreOrder');
                if ($model->create(I('post.'), 2)) {
                    if ( $res = $model->add() ) {

                        $_SESSION['order_info'] = $res;
                        redirect('pay');

                        exit;
                    }
                }
                $this->error($model->getError());
            }
        }

		public function pay()
        {
            $oid = $_SESSION['order_info'];
            $order = D('store_order');
            $order_info = $order->getOrderInfo( $oid );
            $this->assign('order_info' , $order_info);
            $this->display();
        }


        /*
        *测试分类用
        *@author YeWeiBin
         */
        public function fenji()
        {
            $tree = [];

            $cate = M('store_category');

            $res = $cate->select();

            $map0['parent_id'] = ['eq', 0];

            $res0 = $cate->where($map0)->select();

            $map['cate_path'] = ['like', '0,%,%,'];

            $res3 = $cate->where($map)->select();

            foreach ($res as $key => $val) {
                foreach ($res3 as $key3 => $val3) {
                    if ($val == $val3 or $val['parent_id'] == 0) {
                        unset($res[$key]);
                    }
                }
            }

            foreach ($res as $key => $val1) {
                foreach ($res3 as $key3 => $val3) {
                    if ($val3['parent_id'] == $val1['id']) {
                        $res[$key]['chlidren'][] = $val3;
                    }
                }
            }

            foreach ($res0 as $key0 => $val0) {
                foreach ($res as $key8 => $val8) {
                    if ($res['parent_id'] == $res0['id']) {
                        $res0[$key0]['children'][] = $val8;
                    }
                }
            }
            echo '<pre>';
            print_r($res0);
            echo '</pre>';
        }
    }
