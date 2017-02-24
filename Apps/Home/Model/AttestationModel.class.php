<?php
    namespace Home\Model;

    use Think\Model;

    class AttestationModel extends Model
    {
        /**
         * 获取用户认证信息
         * @author YangJun
         * @param array 查询到的认证类型信息
         * @return array 返回用户认证信息
         */
        public function getAttestation($approvetype_list)
        {
            $where['user_id'] = session('home_user_info.user_id');

            $data = $this->where($where)->select();

            for($i=0 ; $i<count($data) ; $i++){
                for($j=0 ; $j<count($approvetype_list) ; $j++){
                    if($data[$i]['attn_num'] == $approvetype_list[$j]['appe_id']){
                        $data[$i]['appe_type'] = $approvetype_list[$j]['appe_type'];
                    }
                }
            }
            
            return $data;
        }

        /**
         * 用户注册时自动生成验证数据
         * @author YangJun
         * @param array 用户认证信息
         */
        public function addOneAttestation($approvetype_list)
        {

            foreach($approvetype_list as $key => $val){
                if($val['appe_id'] == 1){
                    $data['attn_status'] = 1;
                }else{
                    $data['attn_status'] = 0;
                }
                $data['user_id'] = session('home_user_info.user_id');
                $data['attn_num'] = $val['appe_id'];

                $this->add($data);
            }

        }

        /**
         * 获取用户认证信息
         * @author YangJun
         * @param int 认证类型id
         * @return array 返回该用户指定认证类型的信息数组
         */
        public function getUserAttestation($appe_id)
        {

            $where['attn_num'] = ['eq' , $appe_id];
            $where['user_id'] = ['eq' , session('home_user_info.user_id')];

            $data = $this->where($where)->find();

            return $data;
        }

        /**
         * 用户绑定邮箱成功，修改用户认证表状态
         * @author YangJun
         * @param int 用户id
         * @param int 认证类型id
         * @return boolean true/false
         */
        public function saveStatus($user_id , $appe_id)
        {

            $atteWhere['user_id'] = ['eq' , $user_id];
            $atteWhere['attn_num'] = ['eq' , $appe_id];
            $atteSave['attn_status'] = '1';
            if($this->where($atteWhere)->save($atteSave)){
                return true;
            }else{
                return false;
            }
        }

        /**
         * 发送短信验证码
         * @author YangJun
         * @return array
         */
        public function sms($phone)
        {
            // 随机生成验证码
            $code = mt_rand(111111,999999);

            // 模板
            $tplOperator = new \Org\Util\sms\TplOperator();
            $result = $tplOperator->get_default(array("tpl_id"=>'2'));
            // $result = $tplOperator->get();

            // 发送单条短信
            $smsOperator = new \Org\Util\sms\SmsOperator();
            $data['mobile'] = $phone;
            $data['text'] = '您的验证码是' . $code;
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
    }
