<?php 
	
	namespace Home\Model;
	use Think\Model;

	/**
	*仪器库分类Model
	*
	**/
	class InstrumentCategoryModel extends Model{

		//获取顶级分类
		public function getTopCate()
		{	
			//调用$this->filter()方法过滤，返回一个只有商品存在的分类
			return $this->filter(0);
		}

		//获取二级分类
		public function getSecondCate()
		{
			return $this->filter(I('get.id'));
		}

		//获取父级分类
		public function getParentCate()
		{
			$map['id'] = I('get.id');
			$res = $this->field('parent_id')->where($map)->find();
			return $this->filter( $res['parent_id'] );
		}

		//设置一个方法过滤子级没有商品的分类
		public function  filter( $pid )
		{
			//查出PID的数据
			$map['parent_id'] = $pid;
			$data = $this->where($map)->select();
				 
			if( $data ){
				foreach( $data as $key => $val ){
					$map['parent_id'] = $val['id']; 
					$map1['appt_category_id'] = $val['id'];

					//查出该分类有无子级或有无商品
					$son = $this->where( $map )->field('id')->find();
					$goods = M('Instrument_goods')->where( $map1 )->field('appt_id')->find();

					//如果没有子级并且没有商品，就删除当前KEY
					if( !$son && !$goods ){
						unset( $data[$key] );
					}

					//如果有子级查询子级有无商品，如果没有商品，就删除当前KEY
					if( $son ){
						if( !$this->cha( $val['id']) ){
							unset( $data[$key] );
						}
					}
				}
						
			}
			
			//返回过滤好的数组
			return $data;
			
		} 

		//过滤方法辅助方法
		public function cha( $id )
		{
			$i = 0;
			$map['parent_id'] = $id;

			//查询分类ID相关数组
			$data = $this->where( $map )->field('id')->select();
			if( $data ){

				//遍历查询子级有无商品存在
				foreach( $data as $key => $val ){
					$map1['appt_category_id'] = $val['id'];
					$res = M('Instrument_goods')->where( $map1 )->field('appt_id')->find();

					//如果有商品存在，将要返回的$i ++,如果没有商品存在，将继续调用本方法
					if( $res ){
						$i ++;
					}else{
						$i += $this->cha( $val['id'] );
					}
				}
			}
	
			//返回$i的值。如果$i还是0，说明要查询的子类没有商品存在。
			return $i;
		}

		// 查询分类厂商
		public function getCateCompany()
		{
			$map['appt_category_id'] = I('get.gid');
			$page = myPage($this,$map, 10);
			
			$goods = M('Instrument_goods')->where( $map )->select();
			
		}

	}