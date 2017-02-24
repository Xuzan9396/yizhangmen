<?php
	/**
	*专家展示MODEL
	*/

	namespace Admin\Model;
	use Think\Model;

	class ProfessionalModel extends Model
	{

		protected $_validate = [
			['apps_storeid','','店铺已存在',1,'unique'],

		];

		public function getData()
		{	
			//查询个人店铺
			$store = M('Store')->where('store_type=0')->select();

			foreach( $store as $key => $val ){
				$res = $this->where('apps_storeid='.$val['id'])->find();
				if( $res ){
					unset( $store[$key] );
				}

				$res = M('Publish')->where( 'pubh_shopid='.$val['id'] )->count();
				if( !$res ){
					unset( $store[$key] );
				}
			}

			// 查询店铺所属订单成功交易的数量
			foreach( $store as $key => $val ){
				$fcomment = storefcomment( $val['id'] );
				$store[$key]['ordernum'] = $fcomment['dealnum'];
				$store[$key]['orderprice'] = $fcomment['dealprice'];
				$store[$key]['fcomment'] = $fcomment['fcomment'] .'%';

			}
			
			$sort = array(
		         'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
		         'field'     => 'ordernum',       //排序字段
			 );

			 $arrSort = array();
			 foreach($store AS $uniqid => $row){
			     foreach($row AS $key=>$value){
			         $arrSort[$key][$uniqid] = $value;
			     }
			 }
			 if($sort['direction']){
			     array_multisort($arrSort[$sort['field']], constant($sort['direction']), $store);
			 }

			 $por = $this->join('app_store ON app_professional.apps_storeid = app_store.id')->select();

			foreach( $por as $key => $val ){
				$orderprice = M('Store_order')->where('order_serviceuserid='.$val['store_userid'] .' and  order_status=4')->field('order_number_price')->select();
				$price = 0;
				foreach( $orderprice as $k => $v ){
					$price += $v['order_number_price'];
				}

				$fcomment = storefcomment( $val['apps_storeid'] );
				$por[$key]['ordernum'] = $fcomment['dealnum'];
				$por[$key]['orderprice'] = $fcomment['dealprice'];
				$por[$key]['fcomment'] = $fcomment['fcomment'] .'%';

				$por[$key]['orderprice'] = number_format(round($price));
				if( $orderprice ){
					$por[$key]['ordernum'] = count($orderprice);
				}else{
					$por[$key]['ordernum'] = 0;
				}

				for( $i = 0; $i <= 6;$i ++ ){
					if( $i == $val['apps_rank'] ){
						$por[$key]['rank'][$i] = '<option value='.$i.' selected>'.$i.'</option>';
					}else{
						$por[$key]['rank'][$i] = '<option value='.$i.'>'.$i.'</option>';
					}
				}

				$por[$key]['rank'][0] = '<option value="0">未显示</option>';

			}

			return ['store'=>$store,'por'=>$por];
			
		}

		//添加
		public function addProf()
		{
			$data['apps_storeid'] = I('post.id');
			$res = $this->create( $data );
			if( $res ){
			 	$this->add($data);
			 	$status = true;
				$info = '添加成功';
			}else{
				$status = false;
				$info = $this->getError();
			}
			

			return ['status'=>$status,'info'=>$info];

		}

		//修改排序
		public function modRank()
		{
			$rank = $this->where('apps_rank='.I('post.rank') )->find();
			$now = $this->where( 'apps_id='.I('post.id') )->find();

			$data['apps_rank'] = I('post.rank');
			$data1['apps_rank'] = $now['apps_rank'];

			$res = $this->where( 'apps_id='.I('post.id') )->save( $data );
			if( $res ){
				$info = ['status'=>true,'info'=>'修改成功'];
				if( $rank['apps_rank'] ){
					$this->where('apps_id='.$rank['apps_id'])->save( $data1 );
				}
			}else{
				$info = ['status'=>false,'info'=>'修改失败'];
			}


			return $info;
		}
	}

?>