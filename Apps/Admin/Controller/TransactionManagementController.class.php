<?php
namespace Admin\Controller;

use Think\Controller;
/**
 *  [交易管理]
 */
class TransactionManagementController extends CommonController {
		protected $i = 0;
	  /**
	   *   [订单管理]
	   */
    public function OrderManagement ()
    {
			$orders = D('store_order');
			$orderList = $orders->orderHandle();
			$this->assign('orderList',$orderList);
			$this->display();
    }


}
