<?php

namespace Admin\Model;

use Think\Model;

class CategoryModel extends Model
{
		protected $insertFields = array('cate_name','parent_id','cate_path');
		protected $updateFields = array('id','cate_name','parent_id','cate_path');
		protected $_validate = array(
			array('cate_name', 'require', '分类名称不能为空！', 1, 'regex', 3),
			array('cate_name', '1,30', '分类名称的值最长不能超过 30 个字符！', 1, 'length', 3),
			array('parent_id', 'number', '上级分类的ID，0：代表顶级必须是一个整数！', 2, 'regex', 3),
		);


		/************************************* 递归方法 *************************************/
		public function getTree()
		{
			$data = $this->select();
			return $this->_reSort($data);
		}
		private function _reSort($data, $parent_id=0, $level=0, $isClear=TRUE , $cate_path)
		{
			static $ret = array();
			if($isClear)
				$ret = array();
			foreach ($data as $k => $v)
			{
				if($v['parent_id'] == $parent_id)
				{
					//父类id parentCate
					// $pid=$parentCate['id'];
					

					$v['level']     = $level;
					// $res = $v['cate_path'];
					$v['cate_path'] = $cate_path .$v['parent_id']. ',';
					$ret[]          = $v;

					$this->_reSort($data, $v['id'], $level+1, FALSE,$v['cate_path'] );
				}
			}
			return $ret;
		}

		/**
		 * []
		 * @param  [id]
		 * @return [获得子类的id]
		 */
		public function getChildren($id)
		{
			$data = $this->select();

			return $this->_children($data, $id);
		}
		private function _children($data, $parent_id=0, $isClear=TRUE)
		{
			static $ret = array();
			if($isClear)
				$ret = array();
			foreach ($data as $k => $v)
			{
				if($v['parent_id'] == $parent_id)
				{
					$ret[] = $v['id'];
					$this->_children($data, $v['id'], FALSE);
				}
			}
			return $ret;

		}

		protected function _before_insert(&$data, $option)
		{
			// //父类id parentCate
			// $pid=$parentCate['id'];
			// // 路径
			// $path=$parentCate['path'].$pid.',';
			

			
		}

		protected function _after_insert($data)
		{
			// $data['cate_path'] = 0;
			// p($data);
			$data['cate_path'] = 0;
		}
		/**
		 * 删除之前
		 */
		public function _before_delete($option)
		{
			// 先找出所有子分类
			
			$children = $this->getChildren($option['where']['id']);
			// 如果有子分类都删掉
			// return $childern;
		}

		public function addChild(){
			$data['cate_path'] = $_GET['cate_path'] . $_GET['id'] . ',';
			$data['cate_name'] = $_GET['cate_name'];
			$data['parent_id'] = $_GET['id'];

			$this->create($data);
			return $this->add();
		}

		public function findChild(){
			$map['cate_name'] = $_GET['cate_name']; 
			return $this->where($map)->find();
		}

		public function showTopCategory(){
			$map['parent_id']=['eq',0];
			return $this->where($map)->select();
		}

		public function addParent(){
			$this->create(I('get.'));
			return $this->add();
		}

		public function delCate(){
			return $this->delete(I('get.id'));
		}
}








