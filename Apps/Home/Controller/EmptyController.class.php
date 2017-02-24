<?php
namespace Home\Controller;
use Think\Controller;
class EmptyController extends Controller {

    // 访问不存在的方法自动调用
    public function _empty($key,$val)
    {
        $this->redirect('Home/Empty/error');
    }

    public function error()
    {
    	$this->display();
    }


}
