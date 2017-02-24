<?php
	namespace Admin\Model;
	use Think\Model;

	/**
	* 
	*/
	class ShowserviceModel extends Model
	{
		public $order = ['推荐服务0','推荐服务1','推荐服务2','推荐服务3','推荐服务4','推荐服务5','热门推荐0','热门推荐1'];
		public $status = ['不显示','显示中'];

		protected $_validate = [
				[ 'appe_service_id','require', '没有选中的服务'],	
				[ 'appe_service_id','','服务已选',1,'unique'],		
			
			];

		//获取数据
		public function getData()
		{

			$data = $this->join('app_publish ON app_showservice.appe_service_id = app_publish.id')->order('id desc')->select();

			

			foreach( $data as $key => $val ){
				$data[$key]['appe_order'] = $this->order[$val['appe_order']];
				$map['id'] = $val['pubh_shopid'];
				$data[$key]['store_name'] = M('Store')->where( $map )->field('store_name')->find()['store_name'];
				$data[$key]['pubh_time'] = date( 'Y/m/d/H:i:s', $val['pubh_time'] );
				$data[$key]['status'] = $this->status[$val['appe_status']];
			}


			//查找审核通过的服务
			$map1['pubh_status'] = 2;
			$service = M('Publish')->where( $map1 )->join('app_store ON app_publish.pubh_shopid = app_store.id')->field('app_publish.*,app_store.store_name')->select();
		
			foreach( $service as $key => $val ){
				$service[$key]['pubh_time'] = date( 'Y/m/d/H:i:s', $val['pubh_time'] );
				$map2['appe_service_id'] = $val['id'];
				$res = $this->where( $map2 )->find();
				if( $res ){
					unset( $service[$key] );
				}
			}

			return ['data'=>$data, 'service' => $service];
		}

		//删除推荐服务管理表
		public function del()
		{
			$map['appe_id'] = I('post.appe_id');
			$res = $this->where( $map )->delete();
			if( $res ){
				$info = ['status' => true,'info'=> '删除成功'];
			}else{
				$info = ['status' => false,'info'=> '删除失败'];
			}

			return $info;
		}

		//添加推荐服务管理
		public function addser()
		{
			$data = I('post.');
			$res = $this->create( $data );
			if( $res ){
				$this->add();
				$info = ['status'=>true,'info'=>'添加成功'];
			}else{
				$info = ['status'=>false,'info'=>$this->getError()];
			}
			
			return $info;

		}

		//修改服务管理
		public function modser()
		{
			
				$data = I('post.');
				$map['appe_id'] = I('post.appe_id');

				$order['appe_order'] = $this->where( $map )->find()['appe_order'];

				$map2['appe_order'] = I('post.appe_order');
				$id['appe_id'] = $this->where( $map2 )->find()['appe_id'];

				$res = $this->where( $map )->save( $data );
				if( $res ){
					$this->where( $id )->save( $order );
					$info = ['status' => true, 'info' => '修改成功'];
				}else{
					$info = ['status'=> false, 'info'=> '修改失败'];
				}
			

			return $info;
		}

		//查询服务位置
		public function SerPosition()
		{
			$map['appe_order'] = I('post.appe_order');
			$res = $this->where( $map )->join('app_publish ON app_showservice.appe_service_id = app_publish.id')->field('app_showservice.*,app_publish.pubh_title')->find();
			if( $res ){

				$res['appe_order'] = $this->order[ $res['appe_order'] ];
			}else{
				$res = false;
			}
			

			return $res;
		}

		//查询单条服务详细
		public function SearSer()
		{
			$map['appe_id'] = I('post.appe_id');
			$data = $this->where( $map )->find();

			$data['order'] = [];
			for( $i = 0; $i <= 8; $i++ ){
				if( $data['appe_order'] == $i ){
					$data['order'][$i] = 'selected';	
				}else{
					$data['order'][$i] = '';
				}
			}

			$data['status'] = [];
			for( $i = 0; $i <=1; $i ++ ){
				if( $data['appe_status'] == $i ){
					$data['status'][$i] = 'selected';
				}else{
					$data['status'][$i] = '';
				}
			}

			return $data;
		}
	}