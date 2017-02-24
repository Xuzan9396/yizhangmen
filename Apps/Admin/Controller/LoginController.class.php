<?php

namespace Admin\Controller;

use Think\Controller;

class LoginController extends Controller
{

	// 显示登录模板
	public function index()
	{
		$this->display();
	}

	/**
	 * [验证码]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [type] [description]
	 */
	public function code()
	{
		$config = [
		'length' => 4, // 验证码位数
		'useNoise' => false, // 关闭验证码杂点
		'useCurve' => false, //不使用混淆曲线
		'imageW' => 200, //验证码宽度
		'imageH' => 55, //验证码高度
		'bg' => [255,255,255],

		];
		// 实例化验证码
		$Verify = new \Think\Verify($config);
		// 显示验证码
		$Verify->entry();
	}

	

	/**
	 * [普通管理员登录]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return $result['status'] [boolean值,是否通过验证]
	 */
	public function proLogin()
	{
		$admin = D('admin');
		$result = $admin->logCheck();
		if($result['status'])
		{
			$this->success($result['info'] , U('/Admin/Index/index'));
		}else{
			$this->error($result['info'],U('Admin/Login/index'),1);

		}

		$this->ajaxReturn( $result );
	}

	/**
	 * [超级管理员登录]
	 * @author LinHao<137987537@qq.com>
	 */
	public function supnLogin()
	{
		$superadmin = D('superadmin');
		$result = $superadmin->supnLogin();
		$this->ajaxReturn( $result );
	}

	public function logOut()
	{
		session_unset(session('adminLogin'));
		$this->success('退出成功',U('Admin/Login/index'),1);
	}


}
