<?php

	namespace Home\Model;

	use Think\Model;

	class NeedModel extends Model
	{
		protected $_validate = [
			// 自动验证
			//预算金验证
			['need_budget','require','不能为空'],
			['need_budget','/^(([1-9]\d{1,9})(\.\d{1,2}){0,1})[\-,\+]{0,1}(([1-9]\d{1,9})(\.\d{1,2}){0,1}){0,1}$/','预算格式错误',2,'regex'],
			// 有效期验证
			['need_valid_time','require','不能为空'],
			// 验证标题
			['need_title','0,50','标题不能大于50字符',2,'length'],
			//验证内容描述
			['need_desc','20,10000','内容不少于20字节',2,'length'],
			//附件个数
			['need_upload','0,5','附件数不能超过五个',2,'between'],
			//验证电话
			['need_phone','/^1[34578]\d{9}$/','手机格式错误！',2,'regex']
		];

		/**
		 * 填写有效期和预算金
		 * 金君 <757258777@qq.com>
		 * @return 新增id
		 */
		public function budgetAdd()
		{
			// post接受数据
			$post = I('post.');
			//session 保存的需求类目
			$post['need_cateid'] = session('category')['id'];
			$post['need_catepid'] = session('category')['pid'];
			// 接受需求类待加
			$post['need_userid'] = session('home_user_info')['user_id'];
			$post['need_user'] = session('home_user_info')['user_account'];
			//时间转化
			//有效日期转化
			$post['need_valid_time'] = strtotime($post['need_valid_time']);
			// 发布时间
			$post['need_time'] = time();
			// 创建数据
			$post = $this->create($post);
			//判断是否成功
			if($post){
				//获取添加id
				$insertid = $this->add($post);
				// 保存在session
				session('home_user_info.need_id',$insertid);
				$result['status'] = $insertid;
				$result['msg'] = '添加成功';
			}else{
				$result['status'] = $post;
				$result['msg'] = $this->getError();
			}
			// 返回值
			return $result;
		}

		/**
		 * 返回预算页的时候显示原值
		 * 金君 <757258777@qq.com>
		 * @return 返回查询值
		 */
		public function budgetList()
		{
			// 接收订单id
	        if(I('get.need_id')){
	           	$need_id = I('get.need_id');
	            // 保存session
	            session('home_user_info.need_id',$need_id);
	        }else{
	            $need_id = session('home_user_info')['need_id'];
	        }

			$map['need_id'] = ['eq',$need_id];
			// 插叙查询单条数据
			$budget_list = $this->where($map)->find();
			// 返回查询值
			return $budget_list;
		}

		/**
		 * 再次填入时为更新
		 * 金君 <757258777@qq.com>
		 * @return 返回受影响行
		 */
		public function budgetSave()
		{
			// 接收id
			$id = session('home_user_info')['need_id'];
			// post接收
			$post = I('post.');
			//新的更新的session中的需求类
			$post['need_cateid'] = session('category')['id'];
			$post['need_catepid'] = session('category')['pid'];
			//有效日期转化
			$post['need_valid_time'] = strtotime($post['need_valid_time']);
			// 发布时间 每次返回更改
			$post['need_time'] = time();
			// 创建数据
		   	$post = $this->create($post);
			// 插叙查询单条数据
			$map['need_id'] = ['eq',$id];
			$budget_save= $this->where($map)->save($post);
			// 返回受影响行
			return $budget_save;
		}

		/**
		 * 标题描述附件数据处理
		 * 金君 <757258777@qq.com>
		 * @return 返回更新受影响行
		 */
		public function needDescription()
		{
			//获取需求表id
			$id = session('home_user_info')['need_id'];
			// post接收,编辑器的原因必须用全局$_POST接收
			$post = $_POST;

	    	// 数据
	    	$config = [
	    		'maxSize' => 3145728,
	    		'savePath' => 'need/',
	    		'saveName' => ['uniqid',''],
	    		'exts' => ['jpg', 'gif', 'png', 'jpeg'],
	    		'autoSub' => true,
	    		'subName' => ['date','Ym'],
	    		'rootPath' =>  './Public/Uploads/'
	    	];
	    	// 实例化
			$upload = new \Think\Upload($config);
			// 文件上传
		   	$info  =  $upload->upload($_FILES);
		   	// 判断删除,重新上传附件时,把之前文件删除掉
	   		if(session('home_user_info')['need_description']){
	   			// 需求表id
	   			$ndf_map['ndf_needid'] = ['eq',$id];
	   			$needfile = M('needfile')->where($ndf_map)->select();
	   			$dir_name = [];
	   			foreach ($needfile as $key => $value) {
	   				$ndf_path = $value['ndf_path'];
	   				$ndf_name = $value['ndf_name'];
	   				$dir_name[] = $ndf_path . $ndf_name;

	   			}
	   			//删除图片
	   			deleteImage($dir_name);

	   			// 需求id下所有删除
	   			$needfile = M('needfile')->where($ndf_map)->delete();
	   		}
		   	// 判断传没传文件
		   	if($info){
		   		// 获取文件上传个数
		   		$post['need_upload'] = count($info);
		   		// 遍历提取数据
			   	foreach ($info as $k => $val){
			   		$filepost = [];
			   		//文件名
			   		$filepost['ndf_name'] = $val['savename'];
			   		//文件路径
			   		$filepost['ndf_path'] = $val['savepath'];
			   		//需求id
			   		$filepost['ndf_needid'] = $id;
			   		//添加
			   		$needfile = M('needfile')->add($filepost);

			   	}
		   	}else{
		   		//没有上传就为0
		   		$post['need_upload'] = 0;
		   	}

		   	// 状态
		   	$post['need_status'] = 1;
		   	// 发布时间 每次返回更改
			$post['need_time'] = time();
		   	// 创建数据
		   	$post = $this->create($post);
		   	//保存session
		   	session('home_user_info.need_description',1);
		   	// id拼接
		   	$map['need_id'] = ['eq',$id];
		   	// 更新数据
		   	$description = $this->where($map)->save($post);
			// 返回值受影响行
		   	return $description;
		}

		/**
		 * 返回上一步时查询原来数据
		 * 金君 <757258777@qq.com>
		 * @return 返回查询参数
		 */
		public function descriptionList()
		{
			// 接收id
			if(I('get.need_id')){
	           	$need_id = I('get.need_id');
	            // 保存session
	            session('home_user_info.need_id',$need_id);
	        }else{
	            $need_id = session('home_user_info')['need_id'];
	        }
			$map['need_id'] = ['eq',$need_id];
			// 查询单条数据
			$description_list = $this->where($map)->find();
			//查询模板表
			$need_model_list = M('needmodel')->select();
			// 返回查询值
			return ['data'=>$description_list,'list'=>$need_model_list];
		}

		/**
		 * 确认提交 更改状态
		 * 金君 <757258777@qq.com>
		 * @return 返回已经添加的需求信息
		 */
		public function detailsList()
		{
			//获取要查询id
			if(I('get.need_id')){
	           	$need_id = I('get.need_id');
	            // 保存session
	            session('home_user_info.need_id',$need_id);
	        }else{
	            $need_id = session('home_user_info')['need_id'];
	        }
			$map['need_id'] = ['eq',$need_id];
			//执行查询
			$details_list = $this->where($map)->find();
			// 查询附件表
			$map_file['ndf_needid'] = ['eq' , $need_id];
			$file_list = M('needfile')->where($map_file)->select();
			//返回值
			return ['data'=>$details_list , 'list'=>$file_list];
		}

		/**
         * 发送短信验证码
         * @author 金君 <757258777@qq.com>
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
            $data['mobile'] = I('post.need_phone'); // 手机号码
            $data['text'] = '您的验证码是' . $code; // 发送短信内容
            $result = $smsOperator->single_send($data);
            $result = json_encode($result);
            $result = json_decode($result,true);

            if($result['success']){
                $res['status'] = true;
                $res['error_info'] = $result['responseData']['msg'];

                //发送短信成功就把验证码加密后存储到session
                session('sms_code',password_hash($code , PASSWORD_DEFAULT));
                session('sms_phone',$data['mobile']);

            }else{
                $res['status'] = false;
                $res['error_info'] = $result['responseData']['detail'];
            }

            return $res;
        }
		/**
		 * 确认提交后页面
		 * 金君 <757258777@qq.com>
		 * @return 返回更新受影响行
		 */
		public function needOrderDetails()
		{
			// session接受id
			$id = session('home_user_info')['need_id'];
			$map['need_id'] = ['eq',$id];
			$post = I('post.');
			// 状态
			$post['need_status'] = 2;
			// 发布时间
			$post['need_time'] = time();
			// 清空session
			// 需求表id
			session('home_user_info.need_id',null);
			//保存的标题 描述 电话
			session('home_user_info.need_description',null);
			//分类表清空
			session('category',null);
			//更改
			$post = $this->create($post);
			$orderdetails = $this->where($map)->save($post);
			// 返回值
			return $orderdetails;
		}

		/**
		 * [用户中心:我是需方]
		 * [xwc] [13434808758@163.com]
		 */
		 public function demanderSelectHandle ()
		 {

			 // 默认排序(倒序)
			 $order = 'need_time desc';
			 // 默认查全部需求全部
			 $user_info = I('session.home_user_info');
			 $user_id = $user_info['user_id'];// 获取用户id
			 $status = 0;
			 $time = time();
			 // 预装条件
			 $map = [];
			 $map['need_userid']     = ['eq',$user_id];// 属于该用户的需求
			 //$map['need_status']     = ['egt',$status]; // 状态>=0,需求发布
			// $map['need_valid_'] = ['gt',$time]; // 在有效时间之内

			 if(I('get.need_search')){
				 	if(!empty(I('get.need_id'))){
						$getList['need_id'] = ['eq',I('get.need_id')];
					}

				 	if(!empty(I('get.need_title'))){
						$title = I('get.need_title');
						$getList['need_title'] = ['like',"%{$title}%"];
					}

					if(I('get.need_status') !== '100'){
						$getList['need_status'] = ['eq',I('get.need_status')];
					}else{
						$getList['need_status'] = ['egt',1];
					}

					if(I('get.order') !== 'need_time desc'){
						switch(I('get.order')){
							case 'need_id desc':
								$order = 'need_id desc';
							break;
							case 'need_id asc':
								$order = 'need_id asc';
							break;
							case 'need_budget desc':
								$order = 'need_budget desc';
							break;
							case 'need_budget asc':
								$order = 'need_budget asc';
							break;
						}
					}

					$getList['need_userid']     = ['eq',$user_id];// 属于该用户的需求
					// $getList['need_valid_time'] = ['gt',$time]; // 在有效时间之内
					$map = $getList;

			 }
			 $_SESSION['map'] = $map;
			 // 分页
			 $data = [];
			 $count = $this->where($map)->order($order)->count();
			 $page = new \Think\Page($count,5);

			 $page->setConfig('prev','上一页');
			 $page->setConfig('next','下一页');
			 $list = $this->where($map)->order($order)->limit($page->firstRow,$page->listRows)->select();


			 $data['list'] = $list;
			 $data['show'] = $page->show();
			 $data['get_param'] = $map;
			 $data['get_order'] = $order;
			 $data['get_count'] = $count;
			 return $data;
		 }
		 public function demanderFindHandle ()
		 {
			 $user_info = I('session.home_user_info');
			 $user_id = $user_info['user_id'];// 获取用户id
			 $need_id = I('get.need_id');
			 $map = [
				 'user_id' => ['eq',$user_id],
				 'need_id' => ['eq',$need_id],
			 ];
			 $needs = $this->where($map)->find();
			 return $needs;

		 }
		 public function demanderDeleteHandle ()
		 {
			 $data = [];
			 $user_info = I('session.home_user_info');
			 $user_id = $user_info['user_id'];// 获取用户id
			 $need_id = I('get.need_id');
			 $map = [
				 'user_id'=>['eq',$user_id],
				 'need_id'=>['eq',$need_id],
			 ];
			 $data['need_status'] = 0;
			 $result = $this->field('need_status')->where($map)->save($data);
			 return $result;
		 }
		 public function DemanderUpdateHandle ()
		 {
			 echo 'one3';
		 }
		 //查询 操用数据库
		 //查询need表need_valid_time 过期的 and 有方案的  则改变bid表中need_prostepe为1对应需求选择中
		 //把中标的need_prostepe改为2对应工作中
		 //并把过期的有方案的需求显示出来
		 //wenzhonghua@163.com
		 public function CheckAndOperate()
		 {
		 	$time=time();
		 	$map['need_status'] = array('eq',3);
	        $map['_string'] = "need_prostaue=1 and need_valid_time<$time";
	        $needlist= $this->where($map)->select();

	        foreach ($needlist as $key => &$value) {
	        	//把所有有方案的and过期的需求的need_prostepe=0的改为1
	        	if($value['need_prostepe']==0)
	        	{
	        		$data['need_prostepe']=1;
	        		$this->where("need_id={$value['need_id']}")->save($data);
	        		$value['need_prostepe']=1;
	        	}
	        }

	        return $needlist;
		 }
	}
