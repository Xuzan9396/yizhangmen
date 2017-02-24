<?php
	namespace Home\Controller;

	use Think\Controller;

	/**
	 * [用户中心模块]
	 * @author xiaoweichao [13434808758@163.com]
	 */
	class MessageController extends CommonController
	{
			/**
			 * [我的消息]
			 * @author xwc [13434808758@163.com]
			 */
			public function userMessage ()
			{
					// echo __METHOD__;
					$this->display();
			}
			/**
			 * [我的消息]
			 * @author xwc [13434808758@163.com]
			 */
			public function meMessage ()
			{
					if(I('get.p')){
						$p = I('get.p');
					}else{
						$p = 1;
					}
					// 收件人是自己
					$account_info = I('session.');
					// 默认查询条件是(全部)
					$fx_sel = 'lt';
					$fx_num = 2;
					$return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'all'];
					//查询全部
					if(I('get.sel') == 'all'){
						$fx_sel = 'lt';
						$fx_num = 2;
						$return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'all'];
					}
					// 查询未读
					if(I('get.sel') === '0'){
						$fx_sel = 'eq';
						$fx_num = 0;
						$return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'0'];
					}
					// 查询已读
					if(I('get.sel') === '1'){
						$fx_sel = 'eq';
						$fx_num = 1;
						$return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'1'];
					}
					// 开始查询
					$map = [
						'mesm_receiver' => ['eq',$account_info['home_user_info']['user_account']],
						'mesm_type' => ['eq',0],
						'mesm_status' => [$fx_sel,$fx_num]
					];
					$mesm = M('messagesystem');
					$count = $mesm->where($map)->count();
					$page = new \Think\Page($count,10);
					$page->setConfig('prev','上一页');
					$page->setConfig('next','下一页');
					$mesm_list = $mesm->limit($page->firstRow,$page->listRows)->where($map)->select();
					$this->assign('mesm_list',$mesm_list);
					$this->assign('return_param',$return_param);
					$this->assign('return_page',$page->show());
					$this->assign('return_p',$p);
					$this->display();
			}
			public function meMessageAct ()
			{
					// -------------------
					if(IS_GET){
							// 查看单条消息详情
							if(I('get.action') == 'sel'){
									$mesm_id = I('get.mesm_id');
									$mesm = M('messagesystem');
									$map = [
										'mesm_id' =>['eq',$mesm_id],
										'mesm_status' =>['eq',0],
									];
									$findid = $mesm->where($map)->find();
									if($findid){
										$map = [
											'mesm_id' =>['eq',$mesm_id],
										];
										$data['mesm_status'] = 1;
										$saveid =	$mesm->field('mesm_status')->where($map)->save($data);
									}
									redirect(U('Home/Message/detailMeMessage',['mesm_id'=>$mesm_id,'sel'=>I('get.sel'),'p'=>I('get.p')]));

							}
							// 删除单条消息
							if(I('get.action') == 'del'){
									$mesm_id = I('get.mesm_id');
									$map=[
										'mesm_id' => ['eq',$mesm_id],
									];
									$data['mesm_status'] = 2;
									$mesm = M('messagesystem');
									$save_id = $mesm->field('mesm_status')->where($map)->save($data);
									if($save_id){
										$this->success();
										// redirect(U('Home/Message/meMessage'));
									}else{
										// redirect(U('Home/Message/meMessage'));
										$this->success();
									}
							}

							//--------------------------

							// 设为已读
							if(I('get.act') == 'markRead'){
								if(!I('get.mesm_id')){
									// echo '你没有选择任何操作项!';
									$this->success();
							  	// redirect(U('Home/Message/outboxMessage'));
								}else{
									$get_data = I('get.mesm_id');
									$str = '';
									for($i=0; $i<count($get_data);$i++){
										$str .= $get_data[$i] . ',';
									}
									$str = rtrim($str,',');
									$map=[
										'mesm_id' => ['in',$str],
									];
									$data['mesm_status'] = 1;
									$mesm = M('messagesystem');
									$save_id = $mesm->field('mesm_status')->where($map)->save($data);
									if($save_id){
										$this->success();
										// redirect(U('Home/Message/outboxMessage'));
									}else{
										$this->success();
										// redirect(U('Home/Message/outboxMessage'));
									}
								}

							}
							//选择删除
							if(I('get.act') == 'checkedDel'){
								if(!I('get.mesm_id')){
									// echo '你没有选择任何操作项!';
									$this->success();
									// redirect(U('Home/Message/outboxMessage'));
								}else{
									$get_data = I('get.mesm_id');
									$str = '';
									for($i=0; $i<count($get_data);$i++){
										$str .= $get_data[$i] . ',';
									}
									$str = rtrim($str,',');
									$map=[
										'mesm_id' => ['in',$str],
									];
									$data['mesm_status'] = 2;
									$mesm = M('messagesystem');
									$save_id = $mesm->field('mesm_status')->where($map)->save($data);
									if($save_id){
										$this->success();
										// redirect(U('Home/Message/outboxMessage'));
									}else{
										$this->success();
										// redirect(U('Home/Message/outboxMessage'));
									}
								}
							}
					}
			}
			/**
			 *
			 */
			public function detailmeMessage ()
			{
					$return_param = ['sel'=>I('get.sel')];
					$mesm_id = I('get.mesm_id');
					$map = [
						'mesm_id'=>['eq',$mesm_id],
					];
					$mesm = M('messagesystem');
					$mesm_list = $mesm->where($map)->find();
					if($mesm_list){
						$this->assign('mesm_list',$mesm_list);
						$this->assign('return_param',$return_param);
						$this->assign('return_p',I('get.p'));
						$this->display();
					}else{
						redirect(U('Home/message/meMessage'));
					}
			}
			public function estimateMessage ()
			{
					// echo __METHOD__;
					$this->display();
			}
			public function transationMessage ()
			{
					 if(I('get.p')){
						 $p = I('get.p');
					 }else{
						 $p = 1;
					 }
					 // 收件人是自己
					 $account_info = I('session.');
					 // 默认查询条件是(全部)
					 $fx_sel = 'lt';
					 $fx_num = 2;
					 $return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'all'];
					 //查询全部
					 if(I('get.sel') == 'all'){
						 $fx_sel = 'lt';
						 $fx_num = 2;
						 $return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'all'];
					 }
					 // 查询未读
					 if(I('get.sel') === '0'){
						 $fx_sel = 'eq';
						 $fx_num = 0;
						 $return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'0'];
					 }
					 // 查询已读
					 if(I('get.sel') === '1'){
						 $fx_sel = 'eq';
						 $fx_num = 1;
						 $return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'1'];
					 }
					 // 开始查询
					 $map = [
						 'mesm_receiver' => ['eq',$account_info['home_user_info']['user_account']],
						 'mesm_status' => [$fx_sel,$fx_num],
						 'mesm_type' => ['eq',2],
					 ];
					 $mesm = M('messagesystem');
					 $count = $mesm->where($map)->count();
					 $page = new \Think\Page($count,10);
					 $page->setConfig('prev','上一页');
					 $page->setConfig('next','下一页');
					 $mesm_list = $mesm->limit($page->firstRow,$page->listRows)->where($map)->select();
					 $this->assign('mesm_list',$mesm_list);
					 $this->assign('return_param',$return_param);
					 $this->assign('return_page',$page->show());
					 $this->assign('return_p',$p);
					 $this->display();

			 }
			public function transationMessageAct ()
			{
				 // -------------------
				 if(IS_GET){
						 // 查看单条消息详情
						 if(I('get.action') == 'sel'){
								 $mesm_id = I('get.mesm_id');
								 $mesm = M('messagesystem');
								 $map = [
									 'mesm_id' =>['eq',$mesm_id],
									 'mesm_status' =>['eq',0],
								 ];
								 $findid = $mesm->where($map)->find();
								 if($findid){
									 $map = [
										 'mesm_id' =>['eq',$mesm_id],
									 ];
									 $data['mesm_status'] = 1;
									 $saveid =	$mesm->field('mesm_status')->where($map)->save($data);
								 }

								 redirect(U('Home/Message/detailTransationMessage',['mesm_id'=>$mesm_id,'sel'=>I('get.sel'),'p'=>I('get.p')]));

						 }
						 // 删除单条消息
						 if(I('get.action') == 'del'){
								 $mesm_id = I('get.mesm_id');
								 $map=[
									 'mesm_id' => ['eq',$mesm_id],
								 ];
								 $data['mesm_status'] = 2;
								 $mesm = M('messagesystem');
								 $save_id = $mesm->field('mesm_status')->where($map)->save($data);
								 if($save_id){
									//  $this->success();
									 redirect(U('Home/Message/detailTransationMessage'));
								 }else{
									 redirect(U('Home/Message/transationMessage'));
									//  $this->success();
								 }
						 }

						 //--------------------------

						 if(I('get.act') == 'markRead'){
							 if(!I('get.mesm_id')){
								 // echo '你没有选择任何操作项!';
								 $this->success();
								 // redirect(U('Home/Message/outboxMessage'));
							 }else{
								 $get_data = I('get.mesm_id');
								 $str = '';
								 for($i=0; $i<count($get_data);$i++){
									 $str .= $get_data[$i] . ',';
								 }
								 $str = rtrim($str,',');
								 $map=[
									 'mesm_id' => ['in',$str],
								 ];
								 $data['mesm_status'] = 1;
								 $mesm = M('messagesystem');
								 $save_id = $mesm->field('mesm_status')->where($map)->save($data);
								 if($save_id){
									 $this->success();
									 // redirect(U('Home/Message/outboxMessage'));
								 }else{
									 $this->success();
									 // redirect(U('Home/Message/outboxMessage'));
								 }
							 }

						 }

						 if(I('get.act') == 'checkedDel'){
							 if(!I('get.mesm_id')){
								 // echo '你没有选择任何操作项!';
								 $this->success();
								 // redirect(U('Home/Message/outboxMessage'));
							 }else{
								 $get_data = I('get.mesm_id');
								 $str = '';
								 for($i=0; $i<count($get_data);$i++){
									 $str .= $get_data[$i] . ',';
								 }
								 $str = rtrim($str,',');
								 $map=[
									 'mesm_id' => ['in',$str],
								 ];
								 $data['mesm_status'] = 2;
								 $mesm = M('messagesystem');
								 $save_id = $mesm->field('mesm_status')->where($map)->save($data);
								 if($save_id){
									 $this->success();
									 // redirect(U('Home/Message/outboxMessage'));
								 }else{
									 $this->success();
									 // redirect(U('Home/Message/outboxMessage'));
								 }
							 }
						 }
				 }
			}
			public function detailTransationMessage ()
			{
					$return_param = ['sel'=>I('get.sel')];
					$mesm_id = I('get.mesm_id');
					$map = [
						'mesm_id'=>['eq',$mesm_id],
					];
					$mesm = M('messagesystem');
					$mesm_list = $mesm->where($map)->find();
					if($mesm_list){
						$this->assign('mesm_list',$mesm_list);
						$this->assign('return_param',$return_param);
						$this->assign('return_p',I('get.p'));
						$this->display();
					}else{
						redirect(U('Home/message/transationMessage'));
					}
			 }
			public function systemMessage ()
			{
					if(I('get.p')){
						$p = I('get.p');
					}else{
						$p = 1;
					}
					// 收件人是自己
					$account_info = I('session.');
					// 默认查询条件是(全部)
					$fx_sel = 'lt';
					$fx_num = 2;
					$return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'all'];
					//查询全部
					if(I('get.sel') == 'all'){
						$fx_sel = 'lt';
						$fx_num = 2;
						$return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'all'];
					}
					// 查询未读
					if(I('get.sel') === '0'){
						$fx_sel = 'eq';
						$fx_num = 0;
						$return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'0'];
					}
					// 查询已读
					if(I('get.sel') === '1'){
						$fx_sel = 'eq';
						$fx_num = 1;
						$return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'1'];
					}
					// 开始查询
					$map = [
						'mesm_receiver' => ['eq',$account_info['home_user_info']['user_account']],
						'mesm_status' => [$fx_sel,$fx_num],
						'mesm_type' => ['eq',1],
					];
					$mesm = M('messagesystem');
					$count = $mesm->where($map)->count();
					$page = new \Think\Page($count,10);
					$page->setConfig('prev','上一页');
					$page->setConfig('next','下一页');
					$mesm_list = $mesm->limit($page->firstRow,$page->listRows)->where($map)->select();
					$this->assign('mesm_list',$mesm_list);
					$this->assign('return_param',$return_param);
					$this->assign('return_page',$page->show());
					$this->assign('return_p',$p);
					$this->display();

			}
			public function systemMessageAct ()
			{
					// -------------------
					if(IS_GET){
							// 查看单条消息详情
							if(I('get.action') == 'sel'){
									$mesm_id = I('get.mesm_id');
									$mesm = M('messagesystem');
									$map = [
										'mesm_id' =>['eq',$mesm_id],
										'mesm_status' =>['eq',0],
									];
									$findid = $mesm->where($map)->find();
									if($findid){
										$map = [
											'mesm_id' =>['eq',$mesm_id],
										];
										$data['mesm_status'] = 1;
										$saveid =	$mesm->field('mesm_status')->where($map)->save($data);
									}

									redirect(U('Home/Message/detailSystemMessage',['mesm_id'=>$mesm_id,'sel'=>I('get.sel'),'p'=>I('get.p')]));

							}

							if(I('get.action') == 'del'){
									$mesm_id = I('get.mesm_id');
									$map=[
										'mesm_id' => ['eq',$mesm_id],
									];
									$data['mesm_status'] = 2;
									$mesm = M('messagesystem');
									$save_id = $mesm->field('mesm_status')->where($map)->save($data);
									if($save_id){
										// $this->success();
										redirect(U('Home/Message/systemMessage',['p'=>I('get.p')]));

									}else{
										redirect(U('Home/Message/systemMessage',['p'=>I('get.p')]));
										// $this->success();
									}
							}

							//--------------------------

							if(I('get.act') == 'markRead'){
								if(!I('get.mesm_id')){
									// echo '你没有选择任何操作项!';
									$this->success();
									// redirect(U('Home/Message/outboxMessage'));
								}else{
									$get_data = I('get.mesm_id');
									$str = '';
									for($i=0; $i<count($get_data);$i++){
										$str .= $get_data[$i] . ',';
									}
									$str = rtrim($str,',');
									$map=[
										'mesm_id' => ['in',$str],
									];
									$data['mesm_status'] = 1;
									$mesm = M('messagesystem');
									$save_id = $mesm->field('mesm_status')->where($map)->save($data);
									if($save_id){
										$this->success();
										// redirect(U('Home/Message/outboxMessage'));
									}else{
										$this->success();
										// redirect(U('Home/Message/outboxMessage'));
									}
								}

							}

							if(I('get.act') == 'checkedDel'){
								if(!I('get.mesm_id')){
									// echo '你没有选择任何操作项!';
									$this->success();
									// redirect(U('Home/Message/outboxMessage'));
								}else{
									$get_data = I('get.mesm_id');
									$str = '';
									for($i=0; $i<count($get_data);$i++){
										$str .= $get_data[$i] . ',';
									}
									$str = rtrim($str,',');
									$map=[
										'mesm_id' => ['in',$str],
									];
									$data['mesm_status'] = 2;
									$mesm = M('messagesystem');
									$save_id = $mesm->field('mesm_status')->where($map)->save($data);
									if($save_id){
										$this->success();
										// redirect(U('Home/Message/outboxMessage'));
									}else{
										$this->success();
										// redirect(U('Home/Message/outboxMessage'));
									}
								}
							}
					}
				}
			public function detailSystemMessage ()
			{
					$return_param = I('get.');
					$mesm_id = I('get.mesm_id');
					$map = [
						'mesm_id'=>['eq',$mesm_id],
					];
					$mesm = M('messagesystem');
					$mesm_list = $mesm->where($map)->find();
					if($mesm_list){
						$this->assign('mesm_list',$mesm_list);
						$this->assign('return_param',$return_param);
						$this->assign('return_p',I('get.p'));
						$this->display();
					}else{
						redirect(U('Home/message/systemMessage'));
					}
			}
			/* -- 写消息开始 -- */
			/**
			 * 	[写消息页面]
			 * 	@auther [A-xwc] [13434808758@163.com]
			 */
			public function writeMessage ()
			{
				// echo __METHOD__;
				$this->display();
			}
			/**
			 * 	[消息处理]
			 *	@auther [A-xwc] [13434808758@163.com]
			 */
			public function writeMessageAct ()
			{
				// mesm_sender(发件人) mesm_receiver(收件人) mesm_title(标题) mesm_centent(内容)
				// mesm_type(类型) mesm_sendtime(发送时间) mesm_status(状态)
				if(IS_POST){
					$mesmData = I('post.');

					$return_data['status'] = true;
					$return_data['error_type'] = '';
					$return_data['error_info'] = '';

					$account_info = I('session.home_user_info');// 会员信息

					// 1.验证(收件人不能为自己)
					if($account_info['user_account'] == $mesmData['mesm_receiver']){
							$return_data['status'] = false;
							$return_data['error_type'] = 'receiver';
							$return_data['error_info'] = '无法给自己发送消息!';
							// ajaxReturn();
							// ajaxReturn($return_data);
							$this->ajaxReturn($return_data);
							// echo json_encode(	$return_data);
							exit;
					}
					// 2.验证(标题不能小于4个字符)
					if(strlen($mesmData['mesm_title']) < 4){
						$return_data['status'] = false;
						$return_data['error_type'] = 'title';
						$return_data['error_info'] = '标题不能小于4个字符';
						// ajaxReturn();
						$this->ajaxReturn($return_data);
						exit;
					}
					// 3.判断收件人是否存在
					$user_account = M('user');
					$account = $user_account->where(['user_account'=>['eq',$mesmData['mesm_receiver']]])->find();
					if(!$account){
						$return_data['status'] = false;
						$return_data['error_type'] = 'receiver';
						$return_data['error_info'] = '收件人不存在!';
						// ajaxReturn();
						$this->ajaxReturn($return_data);
						exit;
					}
					$this->ajaxReturn($return_data);
				}

				if(IS_GET){
					$mesmData = I('get.');
					$account_info = I('session.home_user_info');// 会员信息
					// // 2拼装数据
					$mesm_sender = $account_info['user_account'];//发件人
					$mesm_receiver = $mesmData['mesm_receiver'];//收件人
					$mesm_title = $mesmData['mesm_title'];//标题
					$mesm_centent = $mesmData['mesm_centent'];// 内容
					$mesm_type = 0;//私信
					$mesm_sendtime = time();//发送时间
					$mesm_status = 0;// 未读

					$data = [
						'mesm_sender' => $mesm_sender,
						'mesm_receiver' => $mesm_receiver,
						'mesm_title' => $mesm_title,
						'mesm_centent' => $mesm_centent,
						'mesm_type' => $mesm_type,
						'mesm_sendtime' => $mesm_sendtime,
						'mesm_status' => $mesm_status,
					];

					$mesm = M('messagesystem');
					$insert_id = $mesm->add($data);

					if($insert_id){
						$return_data['status'] = true;
						redirect(U('Home/Message/outboxMessage'));
						// $this->success();
						// $this->ajaxReturn($return_data);
					}
					// else{
					// 	$return_data['status'] = false;
					// 	$return_data['error_type'] = 'noinsert';
					// 	$return_data['error_info'] = '发送失败!';
					// 	$this->ajaxReturn($return_data);
					// }
				}



			}/* --写消息结束 -- */


			public function outboxMessage ()
			{
				if(I('get.p')){
					$p = I('get.p');
				}else{
					$p = 1;
				}
				// 获取用户信息
				$account_info = I('session.home_user_info');// 会员信息
				$mesm = M('messagesystem');
				$map = [
					'mesm_sender' =>['eq',$account_info['user_account']],
					'mesm_type' =>['eq',0],
					'mesm_status' =>['neq',2],
				];
				$count = $mesm->where($map)->count();
				$page = new \Think\Page($count,10);
				$page->setConfig('prev','上一页');
				$page->setConfig('next','上一页');
				// dump($page);
				$mesm_list = $mesm->limit($page->firstRow,$page->listRows)->where($map)->select();
				$this->assign('mesm_list',$mesm_list);
				$this->assign('return_data',$this->return_data);
				$this->assign('return_page',$page->show());
				$this->assign('return_p',$p);
				$this->display();
			}
			public function outboxMessageAct ()
			{
					if(IS_GET){
							if(I('get.action') == 'sel'){
								$mesm_id = I('get.mesm_id');
								redirect(U('Home/Message/detailMessage',['mesm_id'=>$mesm_id,'p'=>I('get.p')]));
							}
							if(I('get.action') == 'del'){
								$mesm_id = I('get.mesm_id');
								$map=[
									'mesm_id' => ['eq',$mesm_id],
								];
								$data['mesm_status'] = 2;
								$mesm = M('messagesystem');
								$save_id = $mesm->field('mesm_status')->where($map)->save($data);
								if($save_id){
									// $this->success();
									redirect(U('Home/Message/outboxMessage'));
								}else{
									// $this->success();
									redirect(U('Home/Message/outboxMessage'));
								}
							}
					}

					if(IS_POST){
							// dump(I('post.'));
							// 判断 post是否有数据,
							if(!I('post.')){
								// echo '你没有选择任何操作项!';
								// $this->return_data['status'] = false;
								// $this->return_data['error_type'] = 'emptydata';
								// $this->return_data['error_info'] = '你没有选择操作项';
								// $this->success();
								redirect(U('Home/Message/outboxMessage'));
							}else{
								$post_data = I('post.');
								$str = '';
								for($i=0; $i<count($post_data['mesm_id']);$i++){
									$str .= $post_data['mesm_id'][$i] . ',';
								}
								$str = rtrim($str,',');
								// dump($str);
								$map=[
									'mesm_id' => ['in',$str],
								];
								$data['mesm_status'] = 2;
								$mesm = M('messagesystem');
								$save_id = $mesm->field('mesm_status')->where($map)->save($data);
								if($save_id){
									// $this->success();
									redirect(U('Home/Message/outboxMessage'));
								}else{
									// $this->success();
									redirect(U('Home/Message/outboxMessage'));
								}
							}
					}
			}
			public function detailMessage ()
			{
					// dump($_SERVER["HTTP_REFERER"]);
					// $account_info = I('session.');
					// $mesm_account = $account_info['home_user_info']['user_account'];
					$mesm_id = I('get.mesm_id');
					$map = [
						// 'mesm_sender'=>['eq',$mesm_account],
						'mesm_id'=>['eq',$mesm_id],
						// 'mesm_status'=>['neq',2],
					];
					$mesm = M('messagesystem');
					$mesm_list = $mesm->where($map)->find();
					if($mesm_list){
						$this->assign('mesm_list',$mesm_list);
						$this->assign('return_p',I('get.p'));
						$this->display();
					}else{
						redirect(U('Home/message/outboxMessage'));
					}

			}
			public function detailMessageAct ()
			{
					// echo __METHOD__;
					$this->display();
			}

			public function informMessage ()
			{
					// echo __METHOD__;
					$this->display();
			}
			public function benefitMessage ()
			{
					// echo __METHOD__;
					$this->display();
			}
	}
