<?php

	namespace Admin\Model;

	use Think\Model;
	
	class NeedModel extends Model
	{	
		/**
		 * 显示所有需求数据处理
		 * 金君 <757258777@qq.com>
		 * 
		 */
		// 自动验证
		protected $_validate = [
			// 标题验证
			['need_title','0,50','标题不能大于50字符',2,'length'],
			// 手机验证
			['need_phone','/^1[34578]\d{9}$/','手机格式错误！',2,'regex'],
			// 时间
			['need_valid_time','require','日期不能为空',2],
			// 状态
			['need_status','require','不能为空',2]
		];
		/**
		 * @author 胡金矿<1968346304@qq.com>
		 * [getNeedData 获取需求信息]
		 * @return [type] [description]
		 */
		public function getNeedData()
		{
			$pagenum=isset( $_GET['mypage'] ) ? $_GET['mypage']+0 : 10;
			$start = strtotime(isset( $_GET['startTime'] ) ? $_GET['startTime']:'');
	        $end = strtotime(isset( $_GET['endTime'] ) ? $_GET['endTime']:'');
	        $map['need_viptype']=['eq',1];
	        $map['need_status']=['eq',3];
	        //交易时间条件
	        if($start && $end){
	            $map['need_time']=['between',array($start,$end)];
	        }else if($start){
	            $map['need_time'] = ['egt', $start];
	        }else if($end){
	            $map['need_time'] = ['elt', $end];

	        }
	        //搜索框条件
	        $content=I('get.need_title');
	        if($content!=''){
	            $map['need_title']=['like','%'.$content.'%'];
	        }
			$page=myPage($this,$map,$pagenum);
	        $list = $this->where($map)->order('need_time desc')->limit($page->pagerows(),$page->maxrows())->select();
	        $show=$page->get_page();
			
	        return ['list'=>$list,'show'=>$show];
		}
		/**
		 * @author 胡金矿<1968346304@qq.com>
		 * [getOneNeddData 获取对应需求店铺的信息]
		 * @return [type] [description]
		 */
		public function getOneNeddData()
		{
			$id=I('get.id');
			$map['need_id']=['eq',$id];
			$list=$this->where($map)->find();
			$secondid=$list['need_catepid'];
			$thirdid=$list['need_cateid'];
			$cate=M('storeCategory');
			$mapp['id']=['eq',$secondid];
			$mappp['id']=['eq',$thirdid];
			$secondName=$cate->where($mapp)->getField('cate_name');
			$thirdName=$cate->where($mappp)->getField('cate_name');
			//得到二级分类与三级分类名
			$title=$secondName.' / '.$thirdName;
			$shop=M('shopCategory');
			$secMap['cate_secondid']=['eq',$secondid];
			$shopSecondCateList=$shop->where($secMap)->select();
			$strShopId='';
			foreach ($shopSecondCateList as $key => $value) {
				$strShopId.=$value['cate_shopid'].',';
			}
			$strShopId=rtrim($strShopId,',');
			$store=M('store');
			$storeMap['id']=['in',$strShopId];
			$storeList=$store->where($storeMap)->select();
			p($storeList);
			$model = M('NeedService');
			$status = $model->field('status')->where(array('needid' => array('eq', $id)))->select();
			p($status);
			if($status){
			  $arr = array();

   			 foreach($storeList as $k=>$r){

       	 		$arr[] = array_merge($r,$status[$k]);

    		}
			$storeList = $arr; 
		}
			
			return ['title'=>$title,'storeList'=>$storeList,'need_id'=>$id];
		}
		
		/**
		 * 显示所有需求数据处理
		 * 金君 <757258777@qq.com>
		 * @return 返回查询的数据
		 */
		public function needList ()
		{	
			// 状态查询
			// get获取状态
			$status = I('get.status');
			// 不同的状态不能where条件
			switch($status){
				case 'zero':
					$map['need_status'] = ['eq', 0];
					break;
				case 'one':
					$map['need_status'] = ['eq', 1];
					break;
				case 'two':
					$map['need_status'] = ['eq', 2];
					break;
				case 'three':
					$map['need_status'] = ['eq', 3];
					break;
				case 'four':
					$map['need_status'] = ['eq', 4];
					break;
				case 'five':
					$map['need_status'] = ['eq', 5];
					break;
				case 'validity':
					$map['need_valid_status'] = ['eq', 0];
					break;
				case 'overdue':
					$map['need_valid_status'] = ['eq', 1];
					break;
				case 'viptype':
					$map['need_viptype'] = ['eq', 1];
					$map['need_valid_status'] = ['eq', 0];
					break;
			}
			//数据查询
			$list = $this->where($map)->order('need_id desc')->limit(10)->select();
			//数据转换
			// 0:未完善需求,1:已完善需求,2:审核需求,3:审核成功,4:审核失败',
			$need_status = [0 => '未完善需求',1 => '已完善需求',2 => '提交待审',3 => '审核成功发布',4 => '审核失败',5 => '已中标'];
			// 0:有效期内的 1:过期的
			$need_valid_status= [0 => '有效期内' , 1 => '过期需求'];
			// 遍历转换数据
			foreach($list as $key => &$val){
				//状态转换
				$val['need_status'] = $need_status[$val['need_status']];
				//时间转化
				//发布时间
				$val['need_time'] = date('Y-m-d H:i:s',$val['need_time']);
				// 判断需求有效
				if($val['need_valid_time'] < time()){
					// 过期的
					$map['need_valid_time'] = ['eq',$val['need_valid_time']];
					$valid_status = ['need_valid_status'=>1];	
				}else{
					// 有效内的
					$map['need_valid_time'] = ['eq',$val['need_valid_time']];
					$valid_status = ['need_valid_status'=>0];
				}
				// 更新语句
				$this->where($map)->save($valid_status);
				// 数据转化
				$val['need_valid_status'] = $need_valid_status[$val['need_valid_status']];
				//有效日期转化
				$val['need_valid_time'] = date('Y-m-d',$val['need_valid_time']);
				//类目表id
				$map_category['id'] = $val['need_cateid'];
				//查询分类表
				$category = M('store_category')->where($map_category)->find();
				//设置需求类目名
				$val['cate_name'] = $category['cate_name'];
			}
			// 返回给控制器数据
			return ['list' => $list];

		}

		/**
		 * 显示所有需求数据处理
		 * 金君 <757258777@qq.com>
		 * @return 返回要修改id查询的数据
		 */
		public function needSaveList ()
		{	
			// 接收id
			$id = I('get.id');
			$map['need_id'] = ['eq' , $id];
			// 用id查询
			$savelist_list = $this->where($map)->find();
			// 查询失败原因表启用状态的
			$map_data['ndr_status'] = ['eq' , 0];
			$savelist_data = M('needreason')->where($map_data)->select();
			//返回值
			return ['list'=>$savelist_list , 'data'=>$savelist_data];
		}

		/**
		 * 显示所有需求数据处理
		 * 金君 <757258777@qq.com>
		 * @return 返回要修改后的数据
		 */
		public function needSave ()
		{	
			// 接收id
			$post = I('post.');
			//如果审核失败
			if($post['need_status'] == 4){
				$failure = [];
				//字段
				$failure['faie_needid'] = $post['need_id'];
				$mapfailure['faie_needid'] = ['eq',$post['need_id']];
				$failure['faie_reason'] = $post['faie_reason'];
				//添加审核失败原因表
				M('needfailure')->where($mapfailure)->save($failure);
			}
			// 提取id
			$id = $post['need_id'];
			// 获取有效时间戳
			$post['need_valid_time'] = strtotime($post['need_valid_time']);	
			$map['need_id'] = ['eq' , $id];
			// 创建数据
			$post = $this->create($post);
			if($post){
				// 用id 更改
				$save_list = $this->where($map)->save($post);
			}else{
				$save_list = false;
			}	
			//返回值
			return $save_list;
		}

		/**
		 * 查询详情文件表数据处理
		 * 金君 <757258777@qq.com>
		 * @return 返回需求id下的所有文件的数据
		 */
		public function needDetails ()
		{	
			// 接收id
			$id = I('get.id');
			$map['need_id'] = ['eq' , $id];
			// 用id查询
			$details_list = $this->where($map)->select();
			//数据转换
			// 0:未完善需求,1:已完善需求,2:审核需求,3:审核成功,4:审核失败',
			$need_status = [0 => '未完善需求',1 => '已完善需求',2 => '提交待审',3 => '审核成功发布',4 => '审核失败',5 => '已中标'];
			// 0:有效期内的 1:过期的
			$need_valid_status= [0 => '有效期内' , 1 => '过期需求'];
			// 遍历转换数据
			foreach($details_list as $key => &$val){
				//状态转换
				$val['need_status'] = $need_status[$val['need_status']];
				//时间转化re
				//发布时间
				$val['need_time'] = date('Y-m-d H:i:s',$val['need_time']);
				// 数据转化
				$val['need_valid_status'] = $need_valid_status[$val['need_valid_status']];
				//有效日期
				$val['need_valid_time'] = date('Y-m-d',$val['need_valid_time']);
				//类目表id
				$map_category['id'] = $val['need_cateid'];
				//查询分类表
				$category = M('store_category')->where($map_category)->find();
				//设置需求类目名
				$val['cate_name'] = $category['cate_name'];
			}
			
			// 查询条件
			$mapfile['ndf_needid'] = ['eq',$id];
			// 查询 needfile表
			$myfile_list = M('needfile')->where($mapfile)->select();

			//查询审核失败的表
			$mapfailure['faie_needid'] = ['eq',$id];
			$failure_list = M('needfailure')->where($mapfailure)->find();

			// 查询失败原因表启用状态的
			$map_data['ndr_status'] = ['eq' , 0];
			$reason_data = M('needreason')->where($map_data)->select();

			//拼接where 条件
			$order_map['order_needid'] = ['eq' , $id];
			$order_map['order_status'] = ['eq' , 0];
			
			//查询订单
			$order = M('store_order')->where($order_map)->find();
			// 返回值
			return ['myfile'=>$myfile_list,'list'=>$details_list,'failure'=>$failure_list,'order'=>$order,'data'=>$reason_data];
		}

		/**
		 * 查询详情文件审核数据处理
		 * 金君 <757258777@qq.com>
		 * @return 返回需求id下的所有文件的数据
		 */
		public function needDetailsSave ()
		{	
			// 接收id
			$post = I('post.');
			//如果审核失败
			if($post['need_status'] == 4){
				$failure = [];
				$failure['faie_needid'] = $post['need_id'];
				$failure['faie_reason'] = $post['faie_reason'];
				M('needfailure')->add($failure);
			}
			// 提取id
			$id = $post['need_id'];	
			$map['need_id'] = ['eq' , $id];
			// 创建数据
			$post = $this->create($post);
			if($post){
				// 用id 更改
				$detailsSave_list = $this->where($map)->save($post);
			}else{
				$detailsSave_list = false;
			}
			
			//返回值
			return $detailsSave_list;
		}

	}
