<?php
namespace Admin\Controller;
use Think\controller;
class EmptyController extends Controller{
	 // 访问不存在的方法自动调用
    public function _empty($key,$val)
    {
         $this->redirect('Admin/Empty/error');
    }

    public function error()
    {
    	$this->display();
    }
}
