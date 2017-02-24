<?php 
	
	namespace Admin\Model;
	use Think\Model;
	class GoldprovidersModel extends Model
	{

		protected $_validate = [
			['apps_store_id','','店铺已重复',1,'unique'],

		];

		public function getData()
		{
			$data = $this->join('app_store ON app_store.id = app_goldproviders.apps_store_id')->select();
		
			$type = ['个人','企业'];
			$status = ['营业','下线'];
			foreach ($data as $key => $val) {
				$map1['order_serviceuserid'] = $val['store_userid'];
				$map1['order_status'] = 4;
				$data[$key]['order'] = M('Store_order')->where( $map1 )->select();
				$fcomment = storefcomment($val['apps_store_id']);

				$data[$key]['allprice'] = $fcomment['dealprice'];
				$data[$key]['allvolume'] = $fcomment['dealnum'];
				$data[$key]['fcomment'] = $fcomment['fcomment'];
				$data[$key]['comnum'] = $fcomment['comnum'];
				$data[$key]['gcom'] = $fcomment['gcom'];
				$data[$key]['store_type']=$type[ $val['store_type'] ];
				$data[$key]['cate_status']=$status[ $val['cate_status'] ];
				
				for($i = 0;$i<=10;$i++){
					if( $i == $val['apps_order'] ){
						$data[$key]['sort'][$i] = '<option value='.$i.' selected>'.$i.'</option>';
					}else{
						$data[$key]['sort'][$i] = '<option value='.$i.'>'.$i.'</option>';
					}
				}

				$data[$key]['sort'][0] = '<option value="0" >未显示</option>';
			}
	
			foreach( $data as $key => $val ){
				
				$data[$key]['allvolume'] = count( $val['order'] );
				$j = 0;
				foreach( $val['order'] as $k => $v ){
					$j = $j + $v['order_number_price'];
				}

				$data[$key]['allprice'] = number_format(round($j));
			}
		$smap['store_type'] = 1;
		// $smap['store_status'] = 1;
		$store = M('Store')->where( $smap )->select();

		foreach( $store as $key => $val ){
			$store[$key]['store_type']=$type[ $val['store_type'] ];
			$store[$key]['cate_status']=$status[ $val['cate_status'] ];
			$map3['order_serviceuserid'] = $val['store_userid'];
			$map3['order_status'] = 4;
			$store[$key]['order'] = M('Store_order')->where( $map3 )->field('order_number_price')->select();
			$fcomment = storefcomment($val['id']);
			$store[$key]['allprice'] = $fcomment['dealprice'];
			$store[$key]['allvolume'] = $fcomment['dealnum'];
			$store[$key]['fcomment'] = $fcomment['fcomment'];
			$store[$key]['comnum'] = $fcomment['comnum'];
			$store[$key]['gcom'] = $fcomment['gcom'];
			$map4['apps_store_id'] = $val['id'];
			$res = $this->where( $map4 )->field('apps_id')->find();
			if( $res ){
				unset( $store[$key] );
			}
		}

			return ['data' => $data,'store' => $store];
		}


		//删除品牌服务商
		public function del()
		{
			$map['apps_id'] = I('post.apps_id');
			$res = $this->where( $map )->delete();
			if( $res ){
				$info = ['status'=>true,'info'=> '删除成功'];
			}else{
				$info = ['status'=>false,'info'=>'删除失败'];
			}

			return $info;
		}

		//添加金牌服务商
		public function addGoldP()
		{
			$data['apps_store_id'] = I('post.id');
			$post = $this->create( $data );
			if( $post ){
				$this->add();
				$info = ['status'=>true,'info'=>'添加成功'];
			}else{
				$info = ['status'=>false,'info'=>$this->getError()];
			}

			return $info;

		}


	}