<?php

namespace Admin\Controller;

use Think\Controller;

class SuperadminController extends CommonController 
{
    public function index()
    {   
        $this->display();
    }

    /**
     * [超管个人信息]
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
     * [修改管理员头像]
     * @author xwc [13434808758@163.com]
     * @author yj [15818708414@163.com]
     */
    public function makeHead ()
    {
        $superadmin = D('superadmin');
        session('adminLogin.supn_picture',$superadmin->getHeadPortrait());
        $data['adminLogin']['supn_picture'] = session('adminLogin')['supn_picture'];
        $this->assign($data);

        $this->display();
    }

    /**
     * [修改管理员头像处理]
     * @author yj [15818708414@163.com]
     */
    public function makeHeadAct ()
    {
        $superadmin = D('superadmin');

        $data = $superadmin->makeHeadAct();

        $this->ajaxReturn($data);
    }

    /**
     * [验证超管原密码]
     * @author LinHao<137987537@qq.com>
     */
    public function chkOldpwd()
    {
        $superadmin = D('superadmin');
        $data = $superadmin->chkOldpwdact();
        $this->ajaxReturn($data);
    }

    /**
     * [修改超管密码]
     * @author LinHao<137987537@qq.com>
     */
    public function editSupnpwd()
    {
        $superadmin = D('superadmin');
        $res = $superadmin->editSupnpwdatc();

        $this->ajaxReturn( $res );
    }

   
}
