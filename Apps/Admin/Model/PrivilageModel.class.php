<?php

namespace Admin\Model;

use Think\Model;

class PrivilageModel extends Model 
{
	/**
	 * [权限控制]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [type] session('adminLogin')['supn_realname']              [超管登录时执行的区间]
	 */
	public function priageList($id)
	{
		if (session('adminLogin')['supn_realname']) {
			$get = isset($id) ? $id : I('get.id');
			$map['admn_id'] = ['eq',$get];
			
			$res = $this->where($map)->select();
			return $res;
		}else{
            return false;
		}
	}

	/**
	 * [添加权限]
	 * @author LinHao<137987537@qq.com>
	 */
	public function addPriviact()
	{
		$get = I('post.');
		$data['admn_id'] = $get['admn_id'];
		unset($get['admn_id']);
		foreach ($get as $k1 => $v1) 
		{
			foreach ($v1 as $k2 => $v2) 
			{
				$data['jurn_id'] = $v2;
        		$res = $this->add($data);
			}
		}
		return $res;
	}

	/**
	 * [权限修改]
	 * @author LinHao<137987537@qq.com>
	 */
	public function delPriviact()
	{
		$get = I('post.');
		$data['admn_id'] = ['eq' , $get['admn_id']];
		unset($get['admn_id']);
        foreach ($get as $k1 => $v1) 
        {
        	foreach ($v1 as $k2 => $v2) {
        		$data['jurn_id'] = ['eq' , $v2];
				$res = $this->where($data)->delete();
        	}
		}
        return $res;
	}


	/**
	 * [删除某个管理员时,同时删除该管理员所有权限]
	 * @author LinHao<137987537@qq.com>
	 * @param int $admn_id      [管理员ID]
	 */
	public function delAllpri($admn_id)
	{
		$map['admn_id'] = ['eq',$admn_id];
		$res = $this->where($map)->delete();
		return $res;
	}

	/**
	 * [删除权限时，同时删除管理员的该权限]
	 * @author LinHao<137987537@qq.com>
	 */
	public function delOnePri()
	{

		$map['jurn_id'] = ['eq',I('get.jurn_id')];
		$res = $this->where($map)->delete();
		return $res;
	}

}
