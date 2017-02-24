<?php
	
	namespace Admin\Model;
	use Think\Model;

	class CategorymanageModel extends Model
	{
		public function getData()
		{
			$map['manage_parent_id'] = 0;
			$categorymanage = $this->where( $map )->join('app_store_category ON app_categorymanage.store_category_id = app_store_category.id')->field('app_categorymanage.*,app_store_category.id,cate_name')->select();

			foreach( $categorymanage as $key => $val ){
				$map1['manage_parent_id'] = $val['manage_id'];
				$map1['team'] = 0; 
				$map2['manage_parent_id'] = $val['manage_id'];
				$map2['team'] = 1; 
				$map3['manage_parent_id'] = $val['manage_id'];
				$map3['team'] = 2; 
				$categorymanage[$key]['first'] = $this->where( $map1 )->join('app_store_category ON app_categorymanage.store_category_id = app_store_category.id')->field('app_categorymanage.*,app_store_category.id,cate_name')->select();
				$categorymanage[$key]['second'] = $this->where( $map2 )->join('app_store_category ON app_categorymanage.store_category_id = app_store_category.id')->field('app_categorymanage.*,app_store_category.id,cate_name')->select();
				$categorymanage[$key]['third'] = $this->where( $map3 )->join('app_store_category ON app_categorymanage.store_category_id = app_store_category.id')->field('app_categorymanage.*,app_store_category.id,cate_name')->select();
			}
			
			foreach( $categorymanage as $key => $val ){
				foreach( $val['first'] as $k => $v ){
					$map4['manage_parent_id'] = $v['manage_id'];
					$categorymanage[$key]['first'][$k]['son'] = $this->where( $map4 )->join('app_store_category ON app_categorymanage.store_category_id = app_store_category.id')->field('app_categorymanage.*,app_store_category.id,cate_name')->select();
			
				}

				foreach( $val['second'] as $k => $v ){
					$map4['manage_parent_id'] = $v['manage_id'];
					$categorymanage[$key]['second'][$k]['son'] = $this->where( $map4 )->join('app_store_category ON app_categorymanage.store_category_id = app_store_category.id')->field('app_categorymanage.*,app_store_category.id,cate_name')->select();
			
				}

				foreach( $val['third'] as $k => $v ){
					$map4['manage_parent_id'] = $v['manage_id'];
					$categorymanage[$key]['third'][$k]['son'] = $this->where( $map4 )->join('app_store_category ON app_categorymanage.store_category_id = app_store_category.id')->field('app_categorymanage.*,app_store_category.id,cate_name')->select();
			
				}
			}
		
			return ['categorymanage' => $categorymanage];
		}
	}