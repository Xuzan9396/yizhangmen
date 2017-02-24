<?php
	namespace Admin\Model;
	use Think\Model;

	class CasesModel extends Model
	{
		protected $_validate = [
				[ 'orderid','','案例已重复',1,'unique'],
				[ 'orderid','require', '没有选中的订单'],
			];

		public function getData()
		{
			$cases = M('Store_order')->where('order_status=4')->select();

			foreach( $cases as $key => $val ){
				$res = $this->where('orderid='.$val['id'])->find();
				if( $res ){
					unset( $cases[$key] );
				}
			}

			foreach( $cases as $key => $val ){
				$cases[$key]['shopper'] = M('User')->where('user_id='.$val['order_employerid'])->find()['user_account'];
				$cases[$key]['master'] = M('User')->where( 'user_id='.$val['order_serviceuserid'])->find()['user_account'];

				$mapcase['id'] = $val['order_serviceid'];
				$mapcase['pubh_status'] = 2;
				$cases[$key]['pubname'] = M('Publish')->where( $mapcase )->find()['pubh_title'];
				$cases[$key]['order_number_price'] = number_format(round($val['order_number_price']));
			}

			$mycases = $this->join('app_store_order ON app_cases.orderid = app_store_order.id')->field('app_cases.*,app_store_order.order_bidid,order_needid,order_employerid,order_serviceuserid,order_serviceid,order_number,order_number_total,order_number_price,order_trusteeship_price,order_retainage_price,order_myfile')->select();
			foreach( $mycases as $key => $val ){
				$mycases[$key]['shopper'] = M('User')->where('user_id='.$val['order_employerid'])->find()['user_account'];
				$mycases[$key]['master'] = M('User')->where( 'user_id='.$val['order_serviceuserid'])->find()['user_account'];
				$mapcase['id'] = $val['order_serviceid'];
				$mapcase['pubh_status'] = 2;
				$mycases[$key]['pubname'] = M('Publish')->where( $mapcase )->find()['pubh_title'];
				$mycases[$key]['order_number_price'] = number_format(round($val['order_number_price']));

				for( $i = 0; $i <= 7; $i ++ ){
					if( $i == $val['rank'] ){
						$mycases[$key]['sort'][$i] = "<option value=".$i." selected>".$i."</option>";
					}else{
						$mycases[$key]['sort'][$i] = "<option value=".$i.">".$i."</option>";
					}
				}
				$mycases[$key]['sort'][0] = "<option value=0>未显示</option>";
			}

			return ['cases'=>$cases,'mycases'=>$mycases];
		}

		public function addCase()
		{
			$data['orderid'] = I('post.id');
			$post = $this->create( $data );
			if( $post ){
				$this->add();
				$info = ['status'=>true,'info'=>'添加成功'];
			}else{
				$info = ['status'=>false,'info'=>$this->getError()];
			}

			return $info;
		}

		public function delCase()
		{
			$res = $this->where('id='.I('post.id'))->delete();
			if( $res ){
				$info = ['status'=>true,'info'=>'删除成功'];
			}else{
				$info = ['status'=>false,'info'=>'删除失败'];
			}

			return $info;
		}

		public function modRank()
		{
			$rank = $this->where( 'rank='.I('post.rank') )->find();
			$now = $this->where( 'id='.I('post.id'))->find();

			$data['rank'] = I('post.rank');
			$res = $this->where('id='.I('post.id'))->save( $data );


			if( $res ){
				$info = ['status'=>true,'info'=>'修改成功'];
				if( $rank['rank'] ){
					$data1['rank'] = $now['rank'];
					$this->where('id='.$rank['id'])->save( $data1 );
				}
			}else{
				$info = ['status'=>false,'info'=>'修改失败'];
			}

			return $info;

		}
	}