<?php
	namespace Home\Controller;

	/**
	 * [用户中心模块]
	 * @author xiaoweichao [13434808758@163.com]
	 */
	class OrganizationController extends CommonController
	{
			public function isOrganization ()
			{
				if(!I('session.home_impuser_info')){
					// 获取用户基础id
					$info = I('session.');
					$user_id = $info['home_user_info']['user_id'];

					// 用户详细信息
					$impusers = M('Impuser');
					$map = ['user_id' => ['eq',$user_id]];
					$impuserList = $impusers->where($map)->select();
					$_SESSION['home_impuser_info'] = $impuserList[0];
					//用户认证信息
					$attestations = M('Attestation');
					$map = ['user_id' => ['eq',$user_id]];
					$attestationList = $attestations->where($map)->select();
					for($i = 0; $i < count($attestationList); $i++ ){
						if($attestationList[$i]['attn_status']){
							switch($attestationList[$i]['attn_num']){
								case 1: $attestationList[$i]['attn_num'] = '手';break;
								case 2: $attestationList[$i]['attn_num'] = '邮';break;
								case 3: $attestationList[$i]['attn_num'] = '实';break;
								case 4: $attestationList[$i]['attn_num'] = '支';break;
								case 5: $attestationList[$i]['attn_num'] = '银';break;
								case 6: $attestationList[$i]['attn_num'] = '企';break;
							}
						}
					}
					// dump($attestationList);
					$_SESSION['home_attestation_info'] = $attestationList;
				}
				$this->display();

			}
			public function organizationInfo ()
			{
				// dump(I('session.'));
				$this->display();
			}
			public function acceptOrganization ()
			{
				$this->display();
			}
			public function averageOrganization ()
			{
				$this->display();
			}
			public function caseOrganization ()
			{
				$this->display();
			}
			public function continueOrganization ()
			{
				$this->display();
			}
			public function fastOrganization ()
			{
				$this->display();
			}
			public function estimateOrganization ()
			{
				$this->display();
			}
			public function manageOrganization ()
			{
				$this->display();
			}
			public function presentOrganization ()
			{
				$this->display();
			}
			public function storeOrganization ()
			{
				$this->display();
			}

	}
