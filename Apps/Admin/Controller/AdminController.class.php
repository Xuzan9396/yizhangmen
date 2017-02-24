<?php

namespace Admin\Controller;

use Think\Controller;

class AdminController extends CommonController 
{
    public function index()
    {   
        // 当超管登录时才执行
        if ( !empty(session('adminLogin')['supn_realname']) ) 
        {
        	// 实例化管理员对象
        	$admin = D('admin');
            // 数据处理
        	$admn_res = $admin->admnList();
        	// 分配数据
        	$this->assign($admn_res);
        	$this->display();
        }else{
            $this->error('管理员无权限查看此页面!',U('Index/index_v2'));
        }
    }

    /**
     * [添加管理员页面]
     * @author LinHao<137987537@qq.com>
     */
    public function add()
    {
        if (isset(session('adminLogin')['supn_realname'])) {
            if(IS_GET)
            {
                $this->display();
            }
        }else{
            $this->error('管理员无权限查看此页面!',U('Index/index_v2'));
        }
    	
    }

    /**
     * [管理员添加]
     * @author LinHao<137987537@qq.com>
     */
    public function addad()
    {
        $admin = D('admin');
        $result = $admin->admnAdd();
        $this->ajaxReturn($result);

    }


    
    /**
     * [显示管理拥有与未拥有的权限]
     * @author LinHao<137987537@qq.com>
     */
    public function priviLage()
    {
        if (session('adminLogin')['supn_realname']) {
            // 实例化管理员对象
            $admin = D('admin');
            // 实例化权限控制对象
            $privilage = D('privilage');
            // 实例化权限列表对象
            $jurisdiction = D('jurisdiction');

            // 数据处理
            $admninfo = $admin->admnName();
            $pri = $privilage->priageList();
            // 得到拥有的权限和未拥有的权限
            $juri_res = $jurisdiction->jurisdList($pri);

            // 分配数据
            $this->assign($juri_res);
            $this->assign($admninfo);
            $this->display();
        }else{
            $this->error('管理员无权限查看此页面!',U('Index/index_v2'));
        }
        
    }



    /**
     * [删除管理员]
     * @author LinHao<137987537@qq.com>
     * @param         [type]
     * @return [type] [description]
     * @return [type] [description]
     */
    public function delAdmn()
    {
        $admin = D('admin');
        $res = $admin->delAdmnact();
        if ($res) 
        {
            // 删除某个管理员时,同时删除该管理员所有权限
            $privilage = D('privilage');
            $delpri_res = $privilage->delAllpri( I('get.id') );
        }
        $this->ajaxReturn($res);

    }

    /**
     * [修改管理员密码]
     * @author LinHao<137987537@qq.com>
     */
    public function editAdmnpwd()
    {
        if (empty(I('post.admn_password'))) {
            return false;
        }
        $admin = D('admin');
        $res = $admin->editAdmnpwdact();
        $this->ajaxReturn($res);
    }

    /**
     * [超级管理员信息查看修改]
     * @author LinHao<137987537@qq.com>
     */
    public function supnInfo()
    {
        $superadmin = D('superadmin');
        $supn_res = $superadmin->supnInfoatc(session('adminLogin')['supn_id']);
        $supn_res['supn_birthday'] = date('Y-m-d',$supn_res['supn_birthday']);
        $this->assign('supn_res',$supn_res);
        $this->display();
    }

    /**
     * [增加管理员时验证手机是否已注册]
     * @author LinHao<137987537@qq.com>
     * @param         [type]
     * @return [type] [description]
     */
    public function chkTel()
    {
        $admin = D('admin');
        $chktel_res = $admin->chkTelact1();
        $this->ajaxReturn($chktel_res);
    }

    /**
     * [description]
     * @author LinHao<137987537@qq.com>
     */
    public function admnchkTel()
    {
        $admin = D('admin');
        $chktel_res = $admin->chkTelact2();
        $this->ajaxReturn($chktel_res);
    }


    /**
     * [修改超级管理员信息]
     * @author LinHao<137987537@qq.com>
     */
    public function editSupninfo()
    {

        $superadmin = D('superadmin');
        $editSupninfo_res = $superadmin->editSupninfoact();
        if (isset($editSupninfo_res['msg'])) {
            $this->error($editSupninfo_res['msg']);
        }else{
            $this->success('修改成功');
        }
    }

    /**
     * [管理员信息查看]
     * @author LinHao<137987537@qq.com>
     */
    public function admnInfo()
    {
        $admin = D('admin');
        $data = $admin->admnName();
        $data['admninfo']['admn_birthday'] = date('Y-m-d',$data['admninfo']['admn_birthday']);
        $this->assign($data);
        $this->display();
    } 

    /**
     * [修改管理员信息]
     * @author LinHao<137987537@qq.com>
     */
    public function editadmnInfo()
    {
        // echo '<pre>';
        //     print_r(I('post.'));
        //     print_r(session('adminLogin'));
        // echo '</pre>';
        // exit;
        $admin = D('admin');
        $editadmnInfo_res = $admin->editadmnInfoact();
        if (isset($editadmnInfo_res['msg'])) {
            $this->error($editadmnInfo_res['msg']);
        }else{
            $this->success('修改成功');
        }
    }

    /**
     * [修改用户头像]
     * @author xwc [13434808758@163.com]
     * @author yj [15818708414@163.com]
     */
    public function makeHead ()
    {
        $admin = D('admin');
        session('adminLogin.admn_picture',$admin->getHeadPortrait());
        $data['adminLogin']['admn_picture'] = session('adminLogin')['admn_picture'];
        $this->assign($data);

        $this->display();
    }

    /**
     * [修改用户头像处理]
     * @author yj [15818708414@163.com]
     */
    public function makeHeadAct ()
    {
        $admin = D('admin');

        $data = $admin->makeHeadAct();

        $this->ajaxReturn($data);
    }

}
