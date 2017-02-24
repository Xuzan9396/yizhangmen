<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
	// 显示后台首页
    public function index(){
    	$jurisdiction = D('jurisdiction');
		$result = $jurisdiction->juriSel();
        // echo '<pre>';
        //     print_r($result);
        // echo '</pre>';
        // exit;
        $result = toRecursion($result);
		$this->assign('jurn_result',$result);
   		$this->display();
    }

    public function index_v2()
    {

        $user = D('user');
        $data = $user->getNewUserNumber();

        $allprice = M('Store_order')->where('order_status=4')->field('order_number_price')->select();
        $price = 0;

        foreach( $allprice as $key => $val ){
            $price += $val['order_number_price'];
            
        }
        $price = number_format(round($price));

        $today = D('Homepageview');
        $todata = $today->getData();

        $this->assign(['sum'=>$data,'price'=>$price,'todata'=>$todata]);
    	$this->display();
    	
    }

}
