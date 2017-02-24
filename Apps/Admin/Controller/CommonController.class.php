<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends EmptyController{
	public function _initialize(){
		// 判断是否登录
		if(empty(session('adminLogin'))){
			$this->success('请登录!',U('Admin/Login/index'));
			exit;
		}
	}
}
