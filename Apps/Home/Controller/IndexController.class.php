<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index()
    {

        $result = D('Advert');
        $data = $result->getDate();
        
        $this->assign('data',$data); 
    	$this->display();

    }


    public function needIndex(){
        
        $this->display();
    }

    
}
