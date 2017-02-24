<?php
    namespace Home\Controller;

    class LoginController extends SmsController
    {
        // ----------------------- Login action

        /**
         * 登陆处理
         * @author YangJun
         * @return array 返回验证状态和错误信息
         */
        public function loginAct()
        {
            $user = D('user');
            //调用Model的loginAct方法，得到布尔值判断是否登陆成功
            $data = $user->loginAct();

            // $this->ajaxReturn(I('post.'));

            $this->ajaxReturn($data);
        }


        /**
         * 获取验证码
         * @author YangJun
         * @return image 登陆验证码
         */
        public function getLoginCode()
        {
            $config = array(
                'fontSize'  =>  20,    // 验证码字体大小
                'length'    =>  4,     // 验证码位数
                'useNoise'  =>  false, // 关闭验证码杂点
                'imageW'    =>  150,
                'imageH'    =>  50,
                'useCurve'  =>  false,
                'bg'        =>  [255,255,255],
            );

            $Verify = new \Think\Verify($config);
            $Verify->entry();
        }

        // ----------------------- Login end

        // ----------------------- Register action

        //显示注册页面
        public function register()
        {
            // 如果用户在注册页面登陆，就跳转到首页
            if(session('home_user_info')){
                $this->redirect('/');
            }

            $this->display();
        }

        /**
         * 注册处理
         * @author YangJun
         * @return array 验证状态和错误信息
         */
        public function registerAct()
        {
            $user = D('user');

            //调用Model的registerAct方法，得到验证状态和错误信息
            $data = $user->registerAct();

            //注册成功循环插入认证信息表和用户详细信息表
            if($data['status']){
                //认证类型信息
                $approvetype = D('approvetype');
                $approvetype_list = $approvetype->getOneApprovetype();

                //插入到用户认证信息表
                $attestation = D('attestation');
                $attestation->addOneAttestation($approvetype_list);

                //插入用户详细信息表(头像)
                $impuser = D('impuser');
                $impuser->registerActToImp();
            }

            $this->ajaxReturn($data);
        }

        /**
         * 焦点事件验证手机是否存在
         * @author YangJun
         * @return arr 返回验证状态和错误信息
         */
        public function checkPhone()
        {
            $user = D('user');
            $data = $user->checkPhone();

            $this->ajaxReturn($data);
        }

        /**
         * 焦点事件验证用户名是否存在
         * @author YangJun
         * @return arr 返回验证状态和错误信息
         */
        public function checkAccount()
        {
            $user = D('user');
            $data = $user->checkAccount();

            $this->ajaxReturn($data);
        }

        /**
         * 短信接口验证
         * @author YangJun
         * @return array 返回验证码和手机号码
         */
        public function sms()
        {
            $this->loadConfig();

            $user = D('user');

            //调用Model的sms方法，得到短信验证状态 return true/false
            $data = $user->sms();

            $this->ajaxReturn($data);
        }

        /**
         * 匹配短信验证码
         * @author YangJun
         * @return boolan true/false
         */
        public function verifySMS()
        {
            $result = password_verify(I('post.sms_code'),session('sms_code'));

            $this->ajaxReturn($result);
        }

        /**
         * 获取验证码
         * @author YangJun
         * @return image 返回验证码图片
         */
        public function getCode()
        {
            $config = array(
                'useCurve'  =>  false,
                'bg'        =>  [255,255,255],
                'fontSize'  =>  14,    // 验证码字体大小
                'length'    =>  4,     // 验证码位数
                'useNoise'  =>  false, // 关闭验证码杂点
                'imageW'    =>  100,
                'imageH'    =>  40,
            );

            $Verify = new \Think\Verify($config);
            $Verify->entry();
        }

        /**
         * 登出
         * @author YeWeiBin
         */
        public function logOut()
        {
            unset($_SESSION['home_user_info']);
            // xuzan
            $cook = $_COOKIE['storePhone'];
             setcookie('storePhone', $cook,time() - 1, '/');;
            redirect(U('Home/Index/index'));
        }

        /**
         * [找回密码]
         * @author yj [15818708414@163.com]
         */
        public function selectPassword ()
        {
            $this->display();
        }

        /**
         * [找回密码发送验证码]
         * @author yj [15818708414@163.com]
         */
        public function selectPasswordGetCode ()
        {
            //判断该手机号码是否已经注册
            $user = D('user');
            $checkPhone = $user->checkPhone();

            //已注册，可以找回
            if(!$checkPhone['status']){
                $data = $this->sms();
                $this->ajaxReturn($data);

                // $data['status'] = true;
                // $data['error_info'] = I('post.');
                // $this->ajaxReturn($data);
            }else{
                //未注册，不可找回
                $data['status'] = false;
                $data['error_info'] = '该手机号码尚未注册，请先注册';
                $this->ajaxReturn($data);
            }
        }

        /**
         * [找回密码验证信息并执行修改密码]
         * @author yj [15818708414@163.com]
         */
        public function selectPasswordAct ()
        {
            $user = D('user');
            $data = $user->selectPasswordAct();

            $this->ajaxReturn($data);
        }
        // ----------------------- Register end


        // ----------------------- Public action

        // ----------------------- Public end

    }
