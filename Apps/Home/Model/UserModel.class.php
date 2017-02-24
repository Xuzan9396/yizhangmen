<?php
    namespace Home\Model;

    use Think\Model;

    class UserModel extends Model
    {
        // ----------------------- login action

        protected $pass = '';

        /**
         * 登陆验证处理
         * @author YangJun
         * @return array 状态和错误信息
         */
        public function loginAct()
        {
            //接收post数据，组装自动验证数组
            $user_name = I('post.user_name');
            $data['user_password'] = I('post.user_password');
            $data['code'] = I('post.code');

            //是否记住登录状态
            $login_status = I('post.login_status');
            //登录信息
            $home_login_user_name = I('post.user_name');
            $home_login_user_password = I('post.user_password');
            //账号存储到cookie
            cookie('home_login_user_name' , $home_login_user_name , 604800);

            //判断什么类型的账号
            if(is_numeric($user_name)){
                $data['user_phone'] = $user_name;
                $map['user_phone'] = $user_name;
            }elseif(stripos($user_name , '@')){
                $data['user_email'] = $user_name;
                $map['user_email'] = $user_name;
            }else{
                $data['user_account'] = $user_name;
                $map['user_account'] = $user_name;
            }

            // 禁用用户禁止登陆
            $status = $this->field('user_status')->where($map)->find();
            $data['user_status'] = $status['user_status'];

            // 自动验证规则
            $_verify = [
                ['code' , 'verifyCode' , '验证码错误,请更换一张验证码' , 1 , 'callback'],
                ['user_phone' , 'phoneCheck' , '手机号码不存在' , 2 , 'callback'],
                ['user_email' , 'emailCheck' , '邮箱号码不存在' , 2 , 'callback'],
                ['user_account' , 'accountCheck' , '用户名不存在' , 2 , 'callback'],
                ['user_password' , 'passCheck' , '账号或密码输入不正确' , 1 , 'callback'],
                ['user_status' , '1' , '该账户处于禁用中，请联系我们解除禁用' , 1 , 'equal'],
            ];

            $return_data['status'] = true;
            $return_data['error_info'] = "";

            if(!$this->validate($_verify)->create($data)){
                $return_data['status'] = false;
                $return_data['error_info'] = $this->getError();
            }else{
                //获取用户信息
                $home_user_info = $this->field('user_id,user_phone,user_account,user_email,user_type')->where($map)->find();

                //修改最近登陆时间
                $save['user_id'] = $home_user_info['user_id'];
                $save['user_lasttime'] = time();
                $this->save($save);

                //把信息存进session
                session('home_user_info' , $home_user_info);
                $return_data['user_account'] = $home_user_info['user_account'];

                //判断用户状态，选择了记住密码就把密码装进cookie里
                if($login_status){
                    cookie('home_login_status' , $login_status , 604800);
                    cookie('home_login_user_password' , $home_login_user_password , 604800);
                }else{
                    cookie('home_login_status' , null);
                    cookie('home_login_user_password' , null);
                }
            }
                    

            return $return_data;

        }

        /**
         * 验证手机号码是否存在
         * @author YangJun
         * @param $phone 用户输入的手机登录账号
         * @return boolean true/false
         */
        protected function phoneCheck($phone)
        {
            $map['user_phone'] = ['eq' , $phone];

            $phoneCheck = $this->where($map)->find();

            if($phoneCheck){
                $this->pass = $phoneCheck['user_password'];
                return true;
            }

            return false;
        }

        /**
         * 验证账号是否存在
         * @author YangJun
         * @param $phone 用户输入的用户名登录账号
         * @return boolean true/false
         */
        protected function accountCheck($account)
        {
            $map['user_account'] = ['eq' , $account];

            $accountCheck = $this->where($map)->find();

            if($accountCheck){
                $this->pass = $accountCheck['user_password'];
                return true;
            }

            return false;
        }

        /**
         * 验证邮箱是否存在
         * @author YangJun
         * @param $phone 用户输入的邮箱登录账号
         * @return boolean true/false
         */
        protected function emailCheck($email)
        {
            $map['user_email'] = ['eq' , $email];

            $emailCheck = $this->where($map)->find();

            if($emailCheck){
                $this->pass = $emailCheck['user_password'];
                return true;
            }

            return false;
        }

        /**
         * 验证密码是否正确
         * @author YangJun
         * @param $phone 用户输入的密码
         * @return boolean true/false
         */
        protected function passCheck($password)
        {
            return password_verify($password , $this->pass);
        }

        // ----------------------- login end

        // ----------------------- register action
        /**
         * 注册处理
         * @author YangJun
         * @return array 返回数组，存储验证状态和错误信息
         */
        public function registerAct()
        {
            //接收ajax传值
            $post = I('post.');
            $post['user_addtime'] = time();

            //自动验证规则
            $_verify = [
                ['code' , 'verifyCode' , '1' , 1 , 'callback'], // 验证码验证
                ['user_account' , '' , '2' , 1 , 'unique'], // 用户名验证
                ['user_password' , '/[0-9|A-Z|a-z]{6,16}/' , '3' , 1 , 'regex'], // 密码验证
                ['user_phone' , '/^1(3|4|5|7|8)\d{9}$/' , '4' , 1 , 'regex'], // 手机号码验证
            ];

            $data['status'] = true;
            $data['error_info'] = "";
            if(!$this->validate($_verify)->create($post)){
                $data['status'] = false;
                $data['error_info'] = $this->getError();
            }else{
                //密码哈希
                $this->user_password = password_hash($this->user_password, PASSWORD_DEFAULT);
                //添加到数据库
                $add_id = $this->add();
                $map['user_id'] = ['eq' , $add_id];
                $home_user_info = $this->field('user_id,user_phone,user_account,user_email,user_type')->where($map)->find();
                
                //注册成功默认登陆
                session('home_user_info' , $home_user_info);
            }

            return $data;
        }

        /**
         * 验证手机号码是否已被注册
         * @author YangJun
         * @return array 返回验证状态和错误信息
         */
        public function checkPhone()
        {
            //自动验证规则
            $_verify = [
                ['user_phone' , '' , '该手机号码已被注册' , 1 , 'unique'], // 手机号码验证
                ['user_phone' , '/^1(3|4|5|7|8)\d{9}$/' , '手机号码格式错误' , 1 , 'regex'], // 手机号码验证
            ];

            $data['status'] = ture;
            $data['error_info'] = '';
            if(!$this->validate($_verify)->create(I('post.'))){
                //验证失败，返回错误信息
                $data['status'] = false;
                $data['error_info'] = $this->getError();
            }

            return $data;
        }

        /**
         * 验证用户名是否已被占用
         * @author YangJun
         * @return array 返回验证状态和错误信息
         */
        public function checkAccount()
        {
            //自动验证规则
            $_verify = [
                ['user_account' , '' , '该用户名已被占用' , 1 , 'unique'], // 手机号码验证
            ];

            $data['status'] = ture;
            $data['error_info'] = '';
            if(!$this->validate($_verify)->create(I('post.'))){
                //验证失败，返回错误信息
                $data['status'] = false;
                $data['error_info'] = $this->getError();
            }

            return $data;
        }

        /**
         * 发送短信验证码
         * @author YangJun
         * @return array
         */
        public function sms()
        {
            // 随机生成验证码
            $code = mt_rand(111111,999999);

            // 模板
            $tplOperator = new \Org\Util\sms\TplOperator();
            $result = $tplOperator->get_default(array("tpl_id"=>'2'));
            // $result = $tplOperator->get();

            // 发送单条短信
            $smsOperator = new \Org\Util\sms\SmsOperator();
            $data['mobile'] = I('post.user_phone'); // 手机号码
            $data['text'] = '【仪掌门】您的验证码是' . $code; // 发送短信内容
            $result = $smsOperator->single_send($data);
            $result = json_encode($result);
            $result = json_decode($result,true);

            if($result['success']){
                $res['status'] = true;
                $res['error_info'] = $result['responseData']['msg'];

                //发送短信成功就把验证码加密后存储到session
                session('sms_code',password_hash($code , PASSWORD_DEFAULT));

            }else{
                $res['status'] = false;
                $res['error_info'] = $result['responseData']['detail'];
            }

            return $res;
        }

        // ----------------------- register end

        // ----------------------- personal center action

        /**
         * 获取用户详细信息
         * @author YangJun
         * @return array 返回用户具体信息
         */
        public function getHomeUserInfo()
        {
            $user_id = session('home_user_info.user_id');

            $where['user_id'] = ['eq' , $user_id];
            $data = $this->field('user_id,user_phone,user_account,user_email,user_addtime,user_lasttime,user_type')->where($where)->find();

            foreach($data as $key => &$val){
                if($key == 'user_addtime' || $key == 'user_lasttime'){
                    $val = date('Y-m-d H:i' , $val);
                }
            }

            return $data;
        }

        /**
         * 修改用户密码处理
         * @author YangJun
         * @return array 返回修改成功或失败
         */
        public function makePasswordAct()
        {
            //查询原密码
            $where['user_id'] = ['eq' , session('home_user_info.user_id')];
            $old_password = $this->field('user_password')->where($where)->find();

            //匹配用户输入的原密码
            if(!password_verify(I('post.user_password'),$old_password['user_password'])){
                $result['status'] = false;
                $result['error_info'] = '当前密码错误';
                return $result;
            }elseif(I('post.new_password') != I('post.verify_password')){
                $result['status'] = false;
                $result['error_info'] = '两次密码输入不一致';
                return $result;
            }

            //修改密码
            $data['user_password'] = password_hash(I('post.new_password') , PASSWORD_DEFAULT);
            $this->where($where)->save($data);

            //拼装返回数组
            $result['status'] = true;
            $result['error_info'] = '修改密码成功,新密码已生效,请重新登陆';

            //清空登陆session和记住密码的cookie
            unset($_SESSION['home_user_info']);
            cookie('home_login_status' , null);
            cookie('home_login_user_password' , null);

            //返回
            return $result;
        }

        // ----------------------- personal center end

        // ----------------------- selectPassword action

        /**
         * 执行找回密码
         * @author YangJun
         * @return arr 找回返回信息
         */
        public function selectPasswordAct()
        {
            // 自动验证规则
            $verify = [
                ['user_code' , 'CheckSMSCode' , '验证码错误' , 2 , 'callback'],
                ['user_phone' , 'phoneCheck' , '手机号码不存在' , 2 , 'callback'],
                ['user_password' , '/[0-9|A-Z|a-z]{6,16}/' , '密码格式错误,请输入6-16位字母数字组合的密码' , 1 , 'regex'],
            ];

            if($arr = $this->validate($verify)->create(I('post.'))){
                $where['user_phone'] = ['eq' , $arr['user_phone']];
                $save['user_password'] = password_hash($arr['user_password'], PASSWORD_DEFAULT);

                $this->where($where)->save($save);

                $data['status'] = true;
                $data['error_info'] = '修改密码成功，新密码已生效';

                // 清除session
                unset($_SESSION['home_user_info']);
            }else{
                $data['status'] = false;
                $data['error_info'] = $this->getError();
            }

            return $data;
            
            // $data['status'] = true;
            // $data['error_info'] = '修改密码成功，新密码已生效';
            // return $data;
        }

        

        // ----------------------- selectPassword end

        // ----------------------- identityMobeil action

        /**
         * 执行修改手机号码操作
         * @author YangJun
         * @return array 返回成功或失败提示信息
         */
        public function identityMobeil()
        {
            // 自动验证规则
            $verify = [
                ['user_phone' , '' , '该手机号码已存在' , 1 , 'unique'],
                ['user_code' , 'CheckSMSCode' , '验证码错误' , 2 , 'callback'],
                ['user_phone' , '/^1(3|4|5|7|8)\d{9}$/' , '请输入正确的手机号码' , 1 , 'regex'],
            ];

            if(!$arr = $this->validate($verify)->create(I('post.'))){
                // 创建数据失败
                $data['status'] = false;
                $data['error_info'] = $this->getError();
            }else{
                // 验证通过
                $where['user_id'] = ['eq' , session('home_user_info.user_id')];
                $save['user_phone'] = $arr['user_phone'];
                $this->where($where)->save($save);

                // 拼装返回信息
                $data['status'] = true;
                $data['error_info'] = "修改成功，为了保证您的账户安全，请重新登录";

                // 删除session
                unset($_SESSION['home_user_info']);
            }

            return $data;
        }

        /**
         * 查询邮箱验证内容
         * @author YangJun
         * @return string 返回绑定的邮箱
         */
        public function getEmail()
        {
            $where['user_id'] = ['eq' , session('home_user_info.user_id')];
            $email = $this->field('user_email')->where($where)->find();

            return $email['user_email'];
        }

        /**
         * 邮箱信息验证，执行数据库操作
         * @author YangJun
         * @return array 返回绑定信息，成功与否
         */
        public function emailVerifier()
        {
            $get = I('get.');

            // 解密参数
            $arr['user_id'] = decode($get['id'] , 'id');
            $arr['user_email'] = decode($get['email'] , 'email');
            $arr['user_time'] = decode($get['time'] , 'time');

            $rules = [
                ['user_id','checkUserId','未知错误，请尝试重新提交验证',1,'callback'],
                ['user_email','/^\w{3,}@\w+(\.\w+)+$/','未知错误，请尝试重新提交验证',1,'regex'],
                ['user_time','checkTime','邮件已过期，请重新提交验证并在24小时内完成验证',1,'callback'],
            ];

            if (!$this->validate($rules)->create($arr)){
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $data['status'] = false;
                $data['error_info'] = $this->getError();
            }else{
                // 验证通过 可以进行其他数据操作
                $where['user_id'] = ['eq' , $arr['user_id']];
                $save['user_email'] = $arr['user_email'];

                if($this->where($where)->save($save)){
                    $data['status'] = true;
                    $data['user_id'] = $arr['user_id'];
                    $data['error_info'] = '恭喜您完成邮箱绑定';
                }else{
                    $data['status'] = false;
                    $data['error_info'] = '您已绑定了该邮箱，请勿重复绑定';
                }
            }

            return $data;
        }

        /**
         * 邮箱信息验证，验证id是否存在
         * @author YangJun
         * @return boolean 返回是否存在 true/false
         */
        public function checkUserId($id)
        {
            $where['user_id'] = ['eq' , $id];
            if($this->where($where)->find()){
                return true;
            }else{
                return false;
            }
        }

        /**
         * 邮箱信息验证，验证时间戳是否超过24小时
         * @author YangJun
         * @return boolean 返回是否超过 true/false
         */
        public function checkTime($time)
        {
            if((time() - $time) < 86400){
                return true;
            }else{
                return false;
            }
        }
        

        // ----------------------- identityMobeil end

        // ----------------------- public action

        /**
         * 匹配短信验证码
         * @author YangJun
         * @return boolean 匹配成功或失败
         */
        protected function CheckSMSCode($code){
            if(password_verify($code,session('sms_code'))){
                return true;
            }else{
                return false;
            }
        }

        /**
         * 验证码匹配
         * @author YangJun
         * @return bool true/false
         */
        protected function verifyCode($code)
        {
            $verify = new \Think\Verify();
            return $verify->check($code);
        }

        // ----------------------- public end
    }
