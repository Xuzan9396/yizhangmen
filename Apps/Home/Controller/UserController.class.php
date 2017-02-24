<?php
	namespace Home\Controller;

	/**
	 * [用户中心模块]
	 * @author xiaoweichao [13434808758@163.com]
	 */
	class UserController extends SmsController
	{
		public function index(){
			$this->commonTest();
		}

		/**
		 * [用户中心主页]
		 * @author xwc [13434808758@163.com]
		 */
		public function userCenter ()
		{
			$this->display();
		}

		/**
		 * [用户资料]
		 * @author YangJun [15818708414@163.com]
		 */
		public function userInfo ()
		{
			//用户基础信息
			$user = D('user');
			$data['home_user_info'] = $user->getHomeUserInfo();

			//认证类型信息
			$approvetype = D('approvetype');
			$data['approvetype_list'] = $approvetype->getApprovetype();

			//用户认证信息
			$attestation = D('attestation');
			$data['attestation_list'] = $attestation->getAttestation($data['approvetype_list']);

			//头像
			$impuser = D('impuser');
			$data['home_user_info']['impr_picture'] = $impuser->getHeadPortrait();

			//分配数据
			$this->assign($data);

			// 4显示模板
			$this->display();
		}

		/**
		 * [用户基本信息]
		 * @author xwc [13434808758@163.com]
		 */
		public function myInfo ()
		{
			$user = D('user');

			$data['userInfo'] = $user->getHomeUserInfo();

			$impuser = D('impuser');

			$data['impuserInfo'] = $impuser->getImpuserInfo();

			$this->assign($data);
			$this->display();
		}

		/**
		 * [完善信息提交]
		 * @author yj [15818708414@163.com]
		 */
		public function modifiedData()
		{
			$impuser = D('impuser');
			$result = $impuser->modifiedData();

			if($result == '资料提交成功'){
				$this->success('资料提交成功',U('userInfo'));
			}else{
				$this->error($result);
			}
		}

		/**
		 * [修改用户头像]
		 * @author xwc [13434808758@163.com]
		 * @author yj [15818708414@163.com]
		 */
		public function makeHead ()
		{
			$impuser = D('impuser');
			$data['home_user_info']['impr_picture'] = $impuser->getHeadPortrait();
			$this->assign($data);

			$this->display();
		}

		/**
		 * [修改用户头像处理]
		 * @author yj [15818708414@163.com]
		 */
		public function makeHeadAct ()
		{
			$impuser = D('impuser');

			$data = $impuser->makeHeadAct();

	        $this->ajaxReturn($data);
		}

		/**
		 * [技能标签]
		 * @author xwc [13434808758@163.com]
		 */
		public function skillTag ()
		{
			$this->display();
		}

		/**
		 * [修改用户密码]
		 * @author xwc [13434808758@163.com]
		 */
		public function makePassword ()
		{
			$this->display();
		}

		/**
		 * [修改用户密码提交]
		 * @author yj [15818708414@163.com]
		 * @return array 返回修改成功或失败
		 */
		public function makePasswordAct()
		{
			$user = D('user');
			$data = $user->makePasswordAct();
			
			$this->ajaxReturn($data);
		}

		/**
		 * [修改支付密码]
		 * @author xwc [13434808758@163.com]
		 */
		public function payPassword ()
		{
			$this->display();
		}

		/**
		 * [用户身份认证]
		 * @author xwc [13434808758@163.com]
		 */
		public function identityAttestation ()
		{
			//认证类型信息
			$approvetype = D('approvetype');
			$data['approvetype_list'] = $approvetype->getApprovetype();

			//用户认证信息
			$attestation = D('attestation');
			$data['attestation_list'] = $attestation->getAttestation($data['approvetype_list']);

			$this->assign($data);

			$this->display();
		}

		/**
		 * [用户身份认证,认证类型区分]
		 * @author yj [15818708414@163.com]
		 */
		public function identityAttestationDetail ()
		{
			// 参数是认证表的id
			$attn_id = I('get.num');

			// 根据不同的id，调用不同的方法
			switch ($attn_id) {
				case '1':
					//手机认证
					$this->identityAttestation_mobeil($attn_id);
					break;

				case '2':
					//邮箱认证
					$this->identityAttestation_email($attn_id);
					break;

				case '3':
					//身份证认证
					$this->identityAttestation_identity($attn_id);
					break;
				
				default:
					$this->error('非法操作！');
					break;
			}
		}

		public function getApprovetypeInfo($appe_id)
		{
			$approvetype = D('approvetype');
			$data = $approvetype->getApprovetypeInfo($appe_id);
			return $data;
		}

		/**
		 * [用户身份认证,手机号码验证]
		 * @author yj [15818708414@163.com]
		 */
		public function identityAttestation_mobeil($appe_id)
		{
			// 认证类型信息
			$data['approvetype'] = $this->getApprovetypeInfo($appe_id);

			// 用户认证信息
			$attestation = D('attestation');
			$data['attestation'] = $attestation->getUserAttestation($appe_id);

			$this->assign($data);

			$this->display('identityAttestation_mobeil');
		}

		/**
         * [用户身份认证，发送短信验证码]
         * @author yj [15818708414@163.com]
         */
        public function mobeilGetCode ()
        {
            $data = $this->sms(session('home_user_info.user_phone'));
            $this->ajaxReturn($data);
        }

        /**
         * [用户身份认证，修改手机号码，发送新号码验证码]
         * @author yj [15818708414@163.com]
         */
        public function identityMobeilreCode ()
        {
			$data = $this->sms(I('post.phone'));
            $this->ajaxReturn($data);
        }

        /**
         * [用户身份认证，执行修改手机号码操作]
         * @author yj [15818708414@163.com]
         */
        public function identityMobeil ()
        {
			$user = D('user');
			$data = $user->identityMobeil();

            $this->ajaxReturn($data);
        }

        /**
         * [用户身份认证，匹配第一步短信验证码]
         * @author yj [15818708414@163.com]
         */
        public function identityMobeilCheckCode ()
        {
			$result = password_verify(I('post.sms_code'),session('sms_code'));

			if($result){
				$data['status'] = true;
				$data['error_info'] = "";
			}else{
				$data['status'] = false;
				$data['error_info'] = "验证码错误";
			}

            $this->ajaxReturn($data);
        }

        /**
         * 短信接口验证
         * @author YangJun
         * @return array 返回验证码和手机号码
         */
        public function sms($phone)
        {
            $this->loadConfig();

            $attestation = D('attestation');
            //调用Model的sms方法，得到短信验证状态 return true/false
            $data = $attestation->sms($phone);

            $this->ajaxReturn($data);
        }

		/**
		 * [用户身份认证,邮箱验证]
		 * @author yj [15818708414@163.com]
		 */
		public function identityAttestation_email($appe_id)
		{

			$data['approvetype'] = $this->getApprovetypeInfo($appe_id);

			// 用户认证信息
			$attestation = D('attestation');
			$data['attestation'] = $attestation->getUserAttestation($appe_id);

			// 如果存在认证信息
			if($data['attestation']['attn_status']){
				$user = D('user');
				$data['email'] = $user->getEmail();
			}

			$this->assign($data);

			$this->display('identityAttestation_email');
		}

		/**
		 * [用户身份认证,邮箱验证,发送邮箱验证]
		 * @author yj [15818708414@163.com]
		 */
		public function identityAttestation_emailAct()
		{
			$data['tomail'] = I('post.user_mail');

			$user = M('user');
			$where['user_email'] = ['eq' , $data['tomail']];
			$email = $user->where($where)->find();
			if($email){
				$this->error('此邮箱已存在');
			}else{
				$data['title'] = "霸气的名字项目组——邮箱认证";

				// 加密参数
				$url['id'] = encode(session('home_user_info.user_id') , 'id');
				$url['tomail'] = encode(I('post.user_mail') , 'email');
				$url['time'] = encode(time() , 'time');

				// 拼接url
				$url = '<a href="http://' . $_SERVER['HTTP_HOST'] . '/shop/home/User/emailVerifier/id/' . $url['id'] . '/email/' . $url['tomail'] . '/time/' . $url['time'] . '">是我本人操作，确认绑定</a>';
				// 拼接邮件内容
				$data['content'] = '点击链接地址绑定电子邮箱，非本人操作，请忽略此邮件 : ' . $url;

				// 执行邮件发送
				$result = $this->smtp($data);
				if($result){
					$this->success('邮件发送成功,请前往邮箱完成验证',U('Home/User/identityAttestation'),2);
				}else{
					$this->error('发送邮件失败,请重试');
				}
			}
		}

		/**
		 * [用户身份认证,邮箱验证,接收id,email,time,执行数据库操作]
		 * @author yj [15818708414@163.com]
		 */
		public function emailVerifier()
		{
			// 修改用户信息表的邮箱
			$user = D('user');
			$data = $user->emailVerifier();

			// 邮箱修改成功后修改用户认证表的状态
			if($data['status']){
				$attestation = D('attestation');
				$bool = $attestation->saveStatus($data['user_id'] , 2);
			}

			$this->assign('info' , $data['error_info']);
			$this->display();
		}

		/**
		 * [用户身份认证,身份证号码验证]
		 * @author yj [15818708414@163.com]
		 */
		public function identityAttestation_identity($appe_id)
		{

			$data['approvetype'] = $this->getApprovetypeInfo($appe_id);

			$this->assign($data);

			$this->display('identityAttestation_identity');
		}

		/**
		 * [收支明细]
		 * @author xwc [13434808758@163.com]
		 */
		public function incomeDetail ()
		{
			$this->display();
		}

// ---------------------------------------------------------

		/**
		 * [需方资料]
		 * @author xwc [13434808758@163.com]
		 */
		public function demanderInfo ()
		{
			$this->display();
		}

		/**
		 * [我是需方]
		 * @author xwc [13434808758@163.com]
		 */
		public function isDemander ()
		{
			$this->display();
		}
		public function fastDemander ()
		{
			$this->display();
		}
		public function finishDemander ()
		{
			$this->display();
		}
		public function buyingDemander ()
		{
			$this->display();
		}
		public function sponsorDemander ()
		{
			$this->display();
		}
		public function estimateDemander ()
		{
			$this->display();
		}

		public function serviceDemander ()
		{
			$this->display();
		}
		public function scheneDemander ()
		{
			$this->display();
		}

// -----------------------------------------------------

		/**
		 * [机构资料]
		 * @author xwc [13434808758@163.com]
		 */
		public function organizationInfo ()
		{
			$this->display();
		}

		/**
		 * [我是服务机构]
		 * @author xwc [13434808758@163.com]
		 */
		public function isOrganization ()
		{
			$this->display();
		}
		public function continueOrganization ()
		{
			$this->display();
		}
		public function averageOrganization ()
		{
			$this->display();
		}

		public function acceptOrganization ()
		{
			$this->display();
		}

		public function presentOrganization ()
		{
			$this->display();
		}

		public function estimateOrganization ()
		{
			$this->display();
		}

		public function fastOrganization ()
		{
			$this->display();
		}

		public function storeOrganization ()
		{
			$this->display();
		}
		public function manageOrganization ()
		{
			$this->display();
		}
		public function caseOrganization ()
		{
			$this->display();
		}
		public function collectOrganization ()
		{
			$this->display();
		}
// -----------------------------------------------------

		/**
		 * [我的消息]
		 * @author xwc [13434808758@163.com]
		 */
		public function myMessage ()
		{
			$this->display();
		}

		/**
		 * [我的消息]
		 * @author xwc [13434808758@163.com]
		 */
		public function userMessage ()
		{
			$this->display();
		}
		public function estimateMessage ()
		{
			$this->display();
		}
		public function systemMessage ()
		{
			$this->display();
		}
		public function writeMessage ()
		{
			$this->display();
		}
		public function outMessage ()
		{
			$this->display();
		}
		public function informMessage ()
		{
			$this->display();
		}
		public function benefitMessage ()
		{
			$this->display();
		}
	}
