<?php

namespace Admin\Model;

use Think\Model;

class JurisdictionModel extends Model 
{
	/**
	 * [权限列表]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [type] [description]
	 * @param  array $priv_id      [AdminContrller传递的参数]
	 */
	public function jurisdList($priv_id)
	{
		foreach ($priv_id as $k1 => $v1) 
		{
			$map['jurn_id'] = ['eq',$v1['jurn_id']];
			$map['jurn_url'] = ['neq',''];
			// 根据权限表ID(权限代码),查到对应的权限 
			$res[] = $this->where($map)->find();
			$not_jurnid[] = $v1['jurn_id'];
		}
		foreach ($res as $k2 => $v2) 
		{
			$list[] = $v2;
		}

		if (!empty($priv_id)) 
		{
			// 根据权限表ID(权限代码),查到未拥有的权限 
			$nmap['jurn_id'] = ['not in',$not_jurnid];
			$nmap['jurn_url'] = ['neq',''];

			$tmp[] = $this->where($nmap)->select();
			foreach ($tmp as $k3 => $v3) 
			{
				foreach ($v3 as $k4 => $v4) 
				{
					$not_list[] = $v4;
				}
			}
			return ['list'=>$list,'notList'=>$not_list];
		}else{
			// 传过来的是空数组，说明该管理员没有权限，这是可以查询所有带有URL的权限
			$nmap['jurn_url'] = ['neq',''];
			$not_list = $this->where($nmap)->select();
			return  ['notList'=>$not_list];
		}
		
	}

	/**
	 * [查询所有权限]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [array] [所有权限]
	 */
	public function allJurn()
	{
		$list = $this->select();
		return ['jurn_res'=>$list];
	}


	/**
	 * [权限删除]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [type] [description]
	 */
	public function deleteJurn()
	{
		$id = I('get.jurn_id');
		$map['jurn_id'] = ['eq' , $id];
		$pmap['jurn_pid'] = ['eq' , $id];
		$res = $this->where($pmap)->find();
		if ($res) {
			return false;
		}else{
			$deletejurn_res = $this->where($map)->delete();
			return $deletejurn_res;
		}
		
	}

	/**
	 * [增加权限]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [type] [description]
	 */
	public function adJurn()
	{
		$data['jurn_name'] = I('get.jurn_name');
		$data['jurn_url'] = I('get.jurn_url');
		$data['jurn_pid'] = I('get.jurn_id');
		$data['jurn_path'] = I('get.jurn_path').I('get.jurn_id').',';
		$path_len = substr_count($data['jurn_path'],',');
		if (empty($data['jurn_name'])) {
			return false;
		}
		if ($path_len == 3 && empty($data['jurn_url'])) {
			return false;
		}
		$map['jurn_name'] = ['eq',I('get.jurn_name')];
		$res = $this->where($map)->find();
		if ($res['jurn_name']==I('get.jurn_name')) {
			return false;
		}
		$adjurn_res = $this->data($data)->add();
		return $adjurn_res;
	}

	/**
	 * [增加顶级权限]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [type] [description]
	 */
	public function addTopjurn()
	{
		$data['jurn_name'] = I('post.jurn_name');
		$data['jurn_pid'] = 0;
		$data['jurn_path'] = '0,';
		if (empty($data['jurn_name'])) {
			return false;
		}
		$map['jurn_name'] = ['eq',I('post.jurn_name')];
		$res = $this->where($map)->find();
		if ($res['jurn_name']==I('post.jurn_name')) {
			return false;
		}
		$addtopjurn_res = $this->data($data)->add();

		return $addtopjurn_res;
	}

	/**
	 * [修改权限信息]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [type] [description]
	 */
	public function editJurninfo()
	{
		if (empty(I('get.jurn_name'))) {
			return false;
		}
		$path_level = substr_count(I('get.jurn_path'),',');
		switch ($path_level) {
			case 1:
				if (I('get.jurn_url')) {
					return false;
				}else{
					$map['jurn_id'] = ['eq',I('get.jurn_id')];
					$data['jurn_name'] = I('get.jurn_name');
					$editjurninfo_res = $this->where($map)->save($data);
					return $editjurninfo_res;
				}
				break;
			case 2:
				$pmap['jurn_pid'] = ['eq',I('get.jurn_id')];
				$res = $this->where($pmap)->find(); // 二级权限下有子权限时，不能添加URL，直接返回false
				if ($res) {
					return false;
				}
				// 二级权限下没有子权限时，可以添加URL
				$map['jurn_id'] = ['eq',I('get.jurn_id')];
				$data['jurn_name'] = I('get.jurn_name');
				$data['jurn_url'] = I('get.jurn_url');
				$editjurninfo_res = $this->where($map)->save($data);
				
				return $editjurninfo_res;
				
				
				break;
			case 3:
				if (I('get.jurn_url')) {
					$map['jurn_id'] = ['eq',I('get.jurn_id')];
					$data['jurn_name'] = I('get.jurn_name');
					$data['jurn_url'] = I('get.jurn_url');
					$editjurninfo_res = $this->where($map)->save($data);
					return $editjurninfo_res;
				}else{
					return false;
				}
				break;
		}
	}


	public function juriSel()
	{
		// 当普通管理员成功登录后执行(后台处理好后再去掉注释)
		if (session('adminLogin')['admn_realname']) {
			$admnpri_list = $this->where("jurn_url=''")->select();
			$admn_juri['admn_id'] = ['eq',session('adminLogin')['admn_id']]; 
        	$privilage = D('privilage');
        	$result = $privilage->where($admn_juri)->select();
        	foreach ($result as $k1 => $v1) {
        		$list[] = $v1['jurn_id'];
        		$map['jurn_id'] = ['eq',$v1['jurn_id']];
        		$admnpri_list[] = $this->where($map)->find();
        	}
        	
        	return $admnpri_list;

		}

		// 当超级管理员成功登录后执行
		if (session('adminLogin')['supn_realname']) {
			$result = $this->select();
			return $result;
		}
	}

}
