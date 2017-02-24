<?php

namespace Admin\Controller;

use Think\Controller;

class JurisdictionController extends CommonController
{
	/**
	 * [权限列表]
	 * @author LinHao<137987537@qq.com>
	 */
	public function jurnList()
	{
		if (isset(session('adminLogin')['supn_realname'])) {
			$jurisdiction = D('jurisdiction');
			$jurn_res = $jurisdiction->allJurn();
			$this->assign($jurn_res);

			$this->display();
		}else{
            $this->error('管理员无权限查看此页面!',U('Index/index_v2'));
		}
		
	}

	/**
	 * [ajax删除权限]
	 * @author LinHao<137987537@qq.com>
	 */
	public function delJurn()
	{	
		$jurisdiction = D('jurisdiction');
		$deljurn_res = $jurisdiction->deleteJurn();
		if ($deljurn_res) {
			// 删除权限时，同时删除管理员的该权限
			$privilage = D('privilage');
			$res = $privilage->delOnePri();
		}
		$this->ajaxReturn($deljurn_res);
	}

	/**
	 * [ajax增加权限]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [type] [description]
	 */
	public function addJurn()
	{
		$jurisdiction = D('jurisdiction');
		$addjurn_res = $jurisdiction->adJurn();
		$this->ajaxReturn($addjurn_res);
	}

	
	public function addTop()
	{
		if (isset(session('adminLogin')['supn_realname'])) {
			$this->display();
		}else{
            $this->error('管理员无权限查看此页面!',U('Index/index_v2'));
		}
		
	}

	
	/**
	 * [ajax增加顶级权限]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [type] [description]
	 */
	public function addTopact()
	{
		$jurisdiction = D('jurisdiction');
		$addtop_res = $jurisdiction->addTopjurn();
		$this->ajaxReturn($addtop_res);
	}

	/**
	 * [ajax修改权限信息]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [type] [description]
	 */
	public function editJurn()
	{
		$jurisdiction = D('jurisdiction');
		$editjurn_res = $jurisdiction->editJurninfo();
		$this->ajaxReturn($editjurn_res);
	}

}








