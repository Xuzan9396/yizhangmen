<?php

	namespace Home\Controller;

	use Think\Controller;

	class NeedController extends SmsController
	{
		/**
		 *	用于显示需求大厅
		 *	wenzhonghua@163.com
		 */
	    public function needIndex()
	    {
	        $time=time();
	        $map['need_status'] = array('eq',3);
	        $map['_string'] = "need_prostaue=1 OR need_valid_time>$time";
	        $needMode=D('need');
	        // //查询
	        $count = $needMode->where($map)->count();
	        $Page  = new \Think\Page($count,6);
	        $show  = $Page->show();
	        //复合查询 审查通过  时间不过期 或 方案等于1
	        $needMode->CheckAndOperate();
	        $needlist= $needMode->where($map)->order('need_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
	        //-------------------------------------------------------
	        //中标方案--中标表--店铺表--需求表---
	        $bidMode=M('bid');
	        $bidcount =$bidMode->where('bid_projecwin=1')->count();
	        $bidPage= new \Think\Page($bidcount,7);
	        $bidlist=$bidMode->join('__NEED__ ON __NEED__.need_id = __BID__.bid_needid')->join('__STORE__ ON __STORE__.store_userid=__BID__.bid_serviceid')->where('bid_projecwin=1')->order('bid_id desc')->limit($bidPage->firstRow.','.$bidPage->listRows)->select();
	        //-----------------------------------------------------------
	        //分类表
	        $bidCate=M('store_category');
	        $bidCateList=$bidCate->select();
	        $bidCateLit=$this->categoryArray($bidCateList);
	        // echo "<pre>";
	        // print_r($bidCateLit);
	        // echo '</pre>';
	        $this->assign('bidcatelist',$bidCateLit);
	        $this->assign('bidlist',$bidlist);
	        $this->assign('needlist',$needlist);
	        $this->display('needIndex');
	    }
	    private  function  categoryArray($array,$id=0)
	    {
	   		$list = [];
	        foreach($array as $v) {
				if($v['parent_id'] == $id) {
					$v['son'] = $this->categoryArray($array, $v['id']);
					if(empty($v['son'])) {
					  unset($v['son']);
					}
					array_push($list, $v);
				}
	        }
	        return  $list;
		}
	    //
	    //
	    //
	    /**
	     * 展示需求大厅的每个需求的具体情况
	     * 闻中华wenzhonghua@163.com
	     */
	    public function needDisplay()
	    {
	        //获得对应ID的需求信息
	        $needid=I('get.needid');
	        $needMode=D('need');//实例化需求数据
	        $needlist= $needMode->where("need_id={$needid}")->select();
	        //根据need_userid查询个祥细信息
	        $useMode=M('impuser');//实例化个人祥细数据
	        $bidMode=M('bid');//实例化方案数据
	        //
        	$needlist['user']=$useMode->where("user_id={$needlist[0]['need_userid']}")->select();
        	//如果为空则输0
        	if(empty($needlist['user'])){
        		$needlist['user']=0;
        	}
	        //获得session中个人数据
	        //如果有则输出,如果没有则输出0;
	        $needInfo=I('session.');
	        if(!isset($needInfo['home_user_info']))
	        {
	        	$needInfo['home_user_info']=0;
	        }
	        else
	        {
	        	//根据session查找登录用户的祥细信息
	        	$useMode=M('impuser');
	        	$needInfo['user']=$useMode->where("user_id={$needInfo['home_user_info']['user_id']}")->select();
	        	if(empty($needInfo['user'])){
	        		$needInfo['user']=0;
	        	}
	        	//如果个人ID等于需求发布的个人ID则代表是需求本登录查看
	        	//修改对应bid方案的bid_projectlook为1
	        	if($needInfo['home_user_info']['user_id']==$needlist[0]['need_userid'])
	        	{
	        		$bidlist=$bidMode->where("bid_needid={$needlist[0]['need_id']}")->select();

	        		foreach ($bidlist as $key => $value) {
	        			if($value['bid_projectlook']==0)
	        			{
	        				$data['bid_projectlook']=1;
	        				$bidMode->where("bid_id={$value['bid_id']}")->save($data);
	        			}
	        		}
	        	}
	        }
	    	//方案查询 接合店铺信息查询

	        $needInfo['time']=time();
	        //查询对应ID的方案表信息

	        $bidlist=$bidMode->join('__STORE__ ON __BID__.bid_serviceid = __STORE__.store_userid')->where("bid_needid=$needid")->select();
	        if(empty($bidlist))
	        {
	        	$bid_projectnum=0;
	        }else{
	        	$bid_projectnum=count($bidlist);
	        }

	        //查询对应ID的留言表信息 接合个人祥细信息查询
	        $needMgMode=M('bidmessage');
	        $needmesg=$needMgMode->join('__IMPUSER__ ON __BIDMESSAGE__.bidm_uid = __IMPUSER__.user_id')->join('__USER__ ON __BIDMESSAGE__.bidm_uid = __USER__.user_id')->where("bidm_pid=$needid")->select();
	      //  	echo '<pre>';
	     	// print_r($needmesg);
	     	// echo '</pre>';
	     	if(empty($needmesg))
	     	{
				$bid_messagenum=0;
	     	}else{
	     		$needmesg=$this->seriArry($needmesg);//修改数组结构
	     		$bid_messagenum=count($needmesg);//统计留言数量
	     	}
	     	$this->assign('bid_projectnum',$bid_projectnum);//方案数量
	     	$this->assign('bid_messagenum',$bid_messagenum);//留言数量
	        $this->assign('needmesg',$needmesg);//需求留言
	        $this->assign('bidlist',$bidlist);//方案信息
	        $this->assign('needlist',$needlist);//需求信息
	        $this->assign('needInfo',$needInfo);//个人信息
	        $this->display('needDisplay');
	    }
	    /**
	     * 进入投标系统
	     * 闻中华wenzhonghua@163.com
	     */
	    public function needAnswer()
	    {
	    	$needInfo=I('session.');
	    	// echo '<pre>';
	    	// print_r($needInfo);
	    	// echo '</pre>';
	    	// exit();
	    	$userStoreMod=M('store');
	    	$userStorInfo=$userStoreMod->where("store_userid={$needInfo['home_user_info']['user_id']}")->select();
	    	if(empty($userStorInfo))
	    	{
	    		$this->error('你还没有店铺,注册店铺才能发布方案,请注册店铺',U('/Home/Service/storeRegister1'),5);
	    	}
	    	else{
	    		if ($userStorInfo[0]['cate_status']==0){
	    			$this->error('你的店铺已禁用,请联系管理员',U('/Home/Index/index'),5);
	    		}else
	    		{
			    	$needid=I('get.needid');
			        $needMode=D('need');
			        $areaMode=M('areas');
			        $areas=$areaMode->where("parent_id=1")->select();
			        $needlist  = $needMode->where("need_id={$needid}")->select();
			        $this->assign('userStorInfo',$userStorInfo);
			        $this->assign('needlist',$needlist);
			        $this->assign('needInfo',$needInfo);
			        $this->assign('areas',$areas);
			    	$this->display('needAnswer');
			    }
		    }

	    }
	    /**
	     * wenzhonghua@163.com
	     * [getArea 获得地区的地址]
	     * @return [type] [description]
	     */
	    public function getArea()
	    {
	    	$areaid=I('post.areaid');
	    	$areaMode=M('areas');
	    	$areas=$areaMode->where("parent_id={$areaid}")->select();
	    	//$areas=json_encode($areas);
	    	$this->ajaxReturn($areas);
	    }
	    /**
	     * wenzhonghua@163.com
		 * [seriArry 把二维数组变成前台的多维数组]
		 * @param  [type]  $array [多维数组]
		 * @param  integer $id    [关联ID]
		 * @return [type]         [返回多维数组]
		 */
		protected  function  seriArry($array,$id=0)
		{
	        $list = [];
	        foreach($array as $v) {
				if($v['bidm_zid'] == $id) {
					$v['replay'] = $this->seriArry($array, $v['bidm_id']);
					if(empty($v['replay'])) {
					  unset($v['replay']);
					}
					array_push($list, $v);
				}
	        }
	        return $list;
	    }
	    /**
	     * [forMoreNeed 更多的需求]
	     * wenzhonghua@163.com
	     * @return [type] [description]
	     */
	    public   function  forMoreNeed()
	    {
	    	
	    	$this->display('moreNeed');
	    }

		/**
		* 	用于重新发布需求
		*	金君<757258777@qq.com>
		*/
	    public function needAgain()
	    {
	    	if(session('home_user_info')['need_id'] || session('home_user_info')['need_description'] == 1){
	    		//重新提交所以要清空sess)需求信息
		    	session('home_user_info.need_id',null);
		    	session('home_user_info.need_description',null);
	    	}
	    	// 清空完直接跳转到分类页面或者没有产生需求时也跳转类目页
	    	redirect(U('Home/Need/needCate'));
	    }

	    /**
	     * 	用于发布需求是选择的类目页面
		 *	金君<757258777@qq.com>
	     */
	    public function needCate()
	    {
	    	//如果没有登录就返回登录界面
	    	if(!session('home_user_info')){
	    		redirect(U('Home/Index/index'));
	    	}
	    	//有自定义参数存在所有上一页按钮跳转的
	    	$revise = I('get.revise');
	    	if(!$revise){
	    		// 如果有发布需求提交预算并且没有提交描述,直接跳到预算页面
	    		if(session('home_user_info')['need_id'] && session('home_user_info')['need_description'] == null){
					redirect(U('Home/Need/needBudget'));
	    		}
	    		//如果已经发布过预算和描述的 就直接跳转到描述页面
		    	if(session('home_user_info')['need_id'] && session('home_user_info')['need_description'] == 1){
		    		redirect(U('Home/Need/needDescription'));
		    	}
	    	}
	    	cookie('sort.first','cate');
	  		//查询所有数据
	  		$category_list = M('store_category')->select();
	  		// 调用递归遍历
	  		$category_list = needCateList($category_list);
	    	//分配数据
	    	$this->assign('list',$category_list);
	    	// 实例化服务类目表
	        $this->display();

	    }

	    /**
	     * 	用于发布需求选择预算金和有效时间页面
		 *	金君<757258777@qq.com>
	     */
	    public function needBudget()
	    {
	    	// get接收两种情况
	    	if(IS_GET){
	    		//get接受需求类目
		    	$category = I('get.');
		    	if($category){
					//类名保存
					session('category',$category);
		    	}
	    		// 如果有需求id
		    	if(session('home_user_info')['need_id']){
					// 实例化
			    	$need = D('need');
			    	// 接收返回值
		    		$budget_list['data'] = $need->budgetList();
		    		//分配数据
		    		$this->assign($budget_list);
		    		$this->display();
				}else{
					//没有需求的时候
					// 默认有效时间为当前时间
					$list['data'] = ['need_valid_time' => time()+3600*24*7];
					//分配数据
		    		$this->assign($list);
					$this->display();
				}
			}
			// post接收两种情况
			if(IS_POST){
				// 实例化
			    $need = D('need');
				if(session('home_user_info')['need_id']){
			    	// 有需求id时(已经产生需求),再次返回下一页时,接受返回值
			    	$need_budgetSave = $need->budgetSave();
			    	// 判断成功
		    		if($need_budgetSave !== false){
		    			//成功跳转
		    			redirect(U('Home/Need/needDescription'));
					}else{
						// 失败
						$this->error('操作失败',U('Home/Need/needBudget'),3);
					}
				}else{
			    	// 开始发布需求,接收返回值
		    		$need_budget = $need->budgetAdd();
		    		// 判断添加状态
					if($need_budget['status']){
						//成功直接跳转
						redirect(U('Home/Need/needDescription'));
					}else{
						//失败
						$this->error('操作有误'.$need_budget['msg'],U('Home/Need/needBudget'),3);
					}
				}

			}


	    }

	    /**
	     * 	用于发布需求标题描述
		 *	金君<757258777@qq.com>
	     */
	    public function needDescription()
	    {
	    	// get接受时
	    	if(IS_GET){
	    		// 如果已有信息就是下一页返回来
		    	if(session('home_user_info')['need_description'] == 1){
					// 实例化
			    	$need = D('need');
			    	// 接收返回值
		    		$desc_list = $need->descriptionList();
		    		//分配数据
		    		$this->assign($desc_list);
				}
				//查询模板表
				$need_model_list['list'] = M('needmodel')->where(['ndm_status'=>0])->select();
				//分配数据
		    	$this->assign($need_model_list);
				// 显示模板
	    		$this->display();
			}
			//post接受时 更改信息
	    	if(IS_POST){
	    		// 实例化
		    	$need = D('need');
	    		// 接受返回值
		    	$need_description = $need->needDescription();
		    	// 判断成功
	    		if($need_description !== false){
	    			//成功跳转
	    			redirect(U('Home/Need/needOrderDetails'));
				}else{
					// 失败
					$this->error('操作失败',U('Home/Need/needDescription'),3);
				}

	    	}

	    }

	    /**
         * 短信接口验证
         * @author 金君
         *
         */
        public function sms()
        {
            $this->loadConfig();

            $need_sms = D('Need');

            //调用Model的sms方法，得到短信验证状态 return true/false
            $data = $need_sms->sms();

            $this->ajaxReturn($data);
        }

        /**
         * 短信接口验证码对比
         * @author 金君
         * @return false/true
         */
        public function phoneCode()
        {
        	//session 取值
        	$phone_code= I('post.');
        	$code = session('sms_code');
        	$phone = session('sms_phone');
        	$res = password_verify($phone_code['user_code'],$code);
        	if($phone == $phone_code['need_phone'] && $res){
        		$result = true;
        	}else{
        		$result = false;
        	}
        	$this->ajaxReturn($result);
        }

	    /**
	     * 	用于发布需求标题描述
		 *	金君<757258777@qq.com>
	     */
	    public function needOrderDetails()
	    {
	    	if(IS_GET){
	    		// 实例化
		    	$need = D('need');
		    	//接受数据 已经填好的信息
		    	$details = $need->detailsList();
		    	//数据分配
		    	$this->assign($details);
		    	//显示模板
		    	$this->display();
	    	}

	    	// 提交时
	    	if(IS_POST){
				// 实例化
				$need = D('need');
				//获取返回值
				$orderdetails = $need->needOrderDetails();
				// 判断是否成功
				if($orderdetails !== false){
					// 成功跳转
					redirect(U('Home/Need/needSuccess'));
				}else{
					//失败返回
					$this->error('操作失败',U('Home/Need/needOrderDetails'),3);
				}
			}
	    }

	    /**
	     * 	用于显示发布待审核
		 *	金君<757258777@qq.com>
	     */
	    public function needSuccess()
	    {
	    	$this->display();
	    }

	}
