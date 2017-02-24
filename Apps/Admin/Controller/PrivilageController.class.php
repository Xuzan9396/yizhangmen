<?php

namespace Admin\Controller;

use Think\Controller;

class PrivilageController extends CommonController
{
	/**
     * [增加管理员权限]
     * @author LinHao<137987537@qq.com>
     * @param         [type]
     * @return [type] [description]
     */
    public function addPrivi()
    {
        // 实例化权限控制对象
        $privilage = D('privilage');
        $res = $privilage->addPriviact();
    	$this->ajaxReturn($res);

    }

    /**
     * [删除管理员权限]
     * @author LinHao<137987537@qq.com>
     */
    public function delPrivi()
    {
        // 实例化权限控制对象
        $privilage = D('privilage');
        $res = $privilage->delPriviact();
    	$this->ajaxReturn($res);

    }
}








