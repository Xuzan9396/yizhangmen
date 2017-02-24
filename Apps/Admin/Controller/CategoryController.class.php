<?php

namespace Admin\Controller;

use Think\Controller;

class CategoryController extends CommonController
{
	public function add()
	{

		if(IS_POST) {
			$model = D('Admin/Category');
			$a = I('post.');
			if($a['parent_id'] == 0) {
				$a['cate_path'] = '0,';
			}else{
				$res            = $model->field('cate_path')->find($a['parent_id']);
				$res            = $res['cate_path']. $a['parent_id'] .',';
				$a['cate_path'] = $res;
			}
			if($model -> create($a, 1)) {

				if($id = $model -> add()) {
					$this->success('添加成功!', U('lst'));
					exit;
				}
			}else{
				$this->error($model->getError());
			}
		}
		$parentModel = D('Admin/Category');			
		$parentData  = $parentModel->getTree();
		$this->assign('parentData',$parentData);
		$this->display();
	}

	public function lst()
	{
		$model  = D('Admin/Category');
		$data   = $model->getTree();
		$this->assign(
		['data' => $data]
		);
		if(IS_POST) {
			$model = D('Admin/Category');
			$a = I('post.');
			if($a['parent_id'] == 0) {
				$a['cate_path'] = '0,';
			}else{
				$res            = $model -> field('cate_path') -> find($a['parent_id']);
				$res            = $res['cate_path']. $a['parent_id'] .',';
				$a['cate_path'] = $res;
			}
			if($model -> create($a, 1)) {
				if($id = $model -> add()) {
					$this -> success('添加成功!', U('lst'));
					exit;
				}
			}else{
					$this->error($model -> getError());
			}
		}
		$parentModel = D('Admin/Category');
		// 返回二维遍历数组
		$parentData  = $parentModel->getTree();
		$this -> assign(array('parentData' => $parentData));
		$this -> display();
	}
	
	/*编辑分类*/
	public function edit()
	{
		// 接收编辑的ID
		$id = I('get.id');
    	if(IS_POST) {
    		$model = D('Admin/Category');
    		if($model -> create(I('post.'), 2)) {
    			if($model -> save() !== FALSE) {
    				$this->success('修改成功！', U('lst', array('p' => I('get.p', 1))));
    				exit;
    			}
    		}
    		$this->error($model->getError());
    	}
			$model       = M('Category');
			$data        = $model->find($id);
			$this->assign('data', $data);
			$parentModel = D('Admin/Category');
			$parentData  = $parentModel->getTree();
			/*$parentData返回一个二维数组*/
			$children    = $parentModel->getChildren($id);
			/*$children返回子类的ID*/
			$this->assign(array(
			'parentData' => $parentData,
			'children'   => $children,
			));
			$this->display();
	}

		/*删除分类*/
	public function delete()
	{
		$model = D('category');
		if($model->delete(I('get.id', 0)) !== false) {
			$this->ajaxReturn(1);
		}else{
			$this->ajaxReturn(0);
		}
	}


}








