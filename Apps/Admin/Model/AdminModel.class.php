<?php

namespace Admin\Model;

use Think\Model;

class AdminModel extends Model 
{
	protected $phone = "/^(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/";

	protected $_validate = [
			['admn_realname','require','用户名格式错误',1],
			['admn_realname','','用户名已被注册',1,'unique'],
			['admn_realname','/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~]{6,22}$/','用户名格式错误'],
			['admn_tel','/^1[34578]\d{9}$/','手机格式错误',1,'regex'],
			['admn_tel','','手机已被使用,请换一个号码注册',1,'unique'],
			['admn_password','/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~]{6,22}$/','密码必须为6-20位，可以使用字母、数字、字符',1,'regex']
		];

	protected $_auto = [
				[ 'admn_password', 'passhash', 1, 'callback' ],
			];

	public function passhash( $pass )
	{

		return password_hash( $pass, PASSWORD_DEFAULT );

	}
	
	/**
	 * [用户添加的处理]
	 * @author LinHao<137987537@qq.com>
	 */
	public function admnAdd()
	{
		// 获取提交的数据
				
		$post = $this->create(I('post.'));

		if($post)
		{
		    $this->add();
			$info = ['status' => true,'info'=>'添加成功'];
		}else{
			$info = ['status'=>false,'info'=>$this->getError()];
		}
		
		return $info;
	}

	/**
	 * [查询登录管理员的信息]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [type] [description]
	 * @return [type] [description]
	 */
	public function admnName()
	{
		// 获取管理员的ID,查询对应的名字
		$get = I('get.id');
		$id = isset(session('adminLogin')['admn_id']) ? session('adminLogin')['admn_id'] : $get;
		$map['admn_id'] = ['eq',$id];
		$admninfo = $this->where($map)->find();
		return ['admninfo'=>$admninfo];

	}

	/**
	 * [管理员列表]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [type] [description]
	 */
	public function admnList()
	{
		// 实例化分页类
		// $page = new \Think\Page($this->where($map)->count(),5);
		// 分页查询
		$list = $this->select();
		$sex = ['女','男','保密'];
		foreach( $list as $key => $val ){
			$list[$key]['admn_sex'] = $sex[$val['admn_sex']];

		}
		// 得到分页
		// $show = $page->show();
		return ['admnlist'=>$list];
	}


	/**
	 * [普通管理员登录验证]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [type] [description]
	 * @return [type] [description]
	 */
	public function logCheck()
	{
		$verify = new \Think\Verify();

		if( $verify->check( I('post.code') )){
			$phone = preg_match( $this->phone,I('post.admn_tel') );
			if( $phone ){
				$map['admn_tel'] = I('post.admn_tel');
			}else{
				$map['admn_realname'] = I('post.admn_tel');
			}

			$admin = $this->where( $map )->find();
			if( $admin ){

				if( password_verify( I('post.admn_password'), $admin['admn_password'] ) ){
					session('adminLogin',$admin);
					$info = ['status'=>true,'info'=>'验证成功'];
				}else{
					$info = ['status'=>false,'info'=>'密码错误'];
				}
			}else{
				$info = ['status'=>false,'info'=>'用户不存在'];
			}
		}else{
			$info = ['status'=>false,'info'=>'验证码错误'];
		}	
		
        return $info;
	}

	/**
	 * [验证码验证]
	 * @author LinHao<137987537@qq.com>
	 */
	public function CheckVerify($code)
	{
		$verify = new \Think\Verify();
		return $verify->check($code);
	}

	
	/**
	 * [用户名验证]
	 * @author LinHao<137987537@qq.com>
	 */
	public function userCheck()
	{
		$name = I('post.admn_tel');
		$map['admn_tel'] = ['eq',$name];
		$list = $this->where($map)->select();
		
		if($list){
			return true;
		}
		return false;
	}

	//  把该用户的信息返回到LoginContrller.class.php 写进session中
	/*public function getAdmin()
	{
		$tel = I('post.admn_tel');
		$map['admn_tel'] = ['eq',$tel];
		$list = $this->where($map)->find();
		return $list;
	}*/


	/**
	 * [密码验证]
	 * @author LinHao<137987537@qq.com>
	 */
	public function pwdCheck()
	{
		$name = I('post.admn_tel');
		$pwd = I('post.admn_password');
		$map['admn_tel'] = ['eq',$name];
		$list = $this->where($map)->select();
		if($list[0]['admn_password'] == $pwd){
			return true;
		}
		return false;
	}

	/**
	 * [删除管理员]
	 * @author LinHao<137987537@qq.com>
	 */
	public function delAdmnact()
	{
		$res = $this->delete( I('get.id') );
		return $res;
	}

	/**
	 * [修改管理员密码]
	 * @author LinHao<137987537@qq.com>
	 */
	public function editAdmnpwdact()
	{
		if( I('post.repwd') == I('post.admn_password')){
			$map['admn_id'] = I('post.admn_id');
		 	$passres = $this->where( $map )->find()['admn_password'];
		 	if( preg_match('/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~]{6,22}$/', I('post.admn_password'))){
				 if( password_verify( I('post.oldpwd'), $passres ) ){
					$data['admn_id'] = I('post.admn_id');
					$passwd = $this->passhash( I('post.admn_password') );
					$data['admn_password'] = $passwd;
					$res = $this->save($data);
					if( $res ){
						$info = ['status'=>true,'info'=>'修改成功'];
					}else{
						$info = ['status'=>false,'info'=>'修改失败'];
					}	
				 }else{
				 	$info = ['status'=>false,'info'=>'原始密码不正确'];
				 }
		 		
		 	}else{
		 		$info = ['status'=>false,'info'=>'密码格式不正确'];
		 	}

		}else{
			$info = ['status'=>false,'info'=>'重复密码不正确'];
		
		}
		return $info;
	}

	/**
	 * [增加管理员时验证手机号是否注册]
	 * @author LinHao<137987537@qq.com>
	 */
	public function chkTelact1()
	{
		$map['admn_tel'] = ['eq',I('post.admn_tel')];
		$res = $this->where($map)->find();
		return $res;
		
	}

	/**
	 * [查看管理员个人信息时执行的验证手机号是否注册]
	 * @author LinHao<137987537@qq.com>
	 */
	public function chkTelact2()
	{
		$map['admn_tel'] = ['eq',I('post.admn_tel')];
		$res = $this->where($map)->find();
		if (I('post.admn_id')==$res['admn_id'] && I('post.admn_tel')==$res['admn_tel']) {
			return true;
		}else if (I('post.admn_id')!=$res['admn_id'] && I('post.admn_tel')==$res['admn_tel']) {
			return false;
		}
		return true;
		
	}

	/**
	 * [修改管理员信息]
	 * @author LinHao<137987537@qq.com>
	 */
	public function editadmnInfoact()
	{
		$post = I('post.');
		$_validate = [
			['admn_realname','require','用户名格式错误',1],
        	['admn_sex','0,1,2','非法的数据！',1,'in'],
			['admn_qq','/^[1-9][0-9]{4,13}$/','QQ号格式错误！',1],
			['admn_identity','/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/','身份证格式错误',1],
        	['admn_email','email','邮箱格式错误！',1],
			['admn_birthday','checkBirth','出生日期错误',1,'callback'],
		];

		
		$post = $this->validate($_validate)->create($post);

		$result = [];
		if($post)
		{
			$id['admn_id'] = ['eq',I('post.admn_id')];
			$map['admn_realname'] = I('post.admn_realname');
			$map['admn_sex'] = I('post.admn_sex');
			$map['admn_qq'] = I('post.admn_qq');
			$map['admn_identity'] = I('post.admn_identity');
			$map['admn_email'] = I('post.admn_email');
			$map['admn_birthday'] = strtotime(I('post.admn_birthday'));

			$updateid = $this->where($id)->save($map);
			return $updateid;

		}else{
			$result['$status'] = $post;
			$result['msg'] = $this->getError();
			return $result;
		}
	}

	/**
	 * [检查管理员输入的生日是否符合规范]
	 * @author LinHao<137987537@qq.com>
	 */
	public function checkBirth()
	{
		$time = time()-86400;
		$birth = strtotime(I('post.admn_birthday'));
		if ($birth>$time) {
			return false;
		}
		return ture;
	}


	/**
	 * [管理员头像信息修改]
	 * @author yj [15818708414@163.com]
	 * @return str 返回用户头像图片名
	 */
	public function getHeadPortrait()
	{
		$where['admn_id'] = session('adminLogin')['admn_id'];

		$result = $this->field('admn_picture')->where($where)->find();
		
		return $result['admn_picture'];
	}

	/**
	 * [管理员头像修改]
	 * @author yj [15818708414@163.com]
	 * @return arr 返回图片上传状态
	 */
	public function makeHeadAct()
	{
		$data['files'] = $_FILES;

        $tmp_arr = explode('&quot;:' , I('post.avatar_data'));

        foreach($tmp_arr as $val){
        	// if((integer)$val == 0){
        	// 	continue;
        	// }

        	$data['size'][] = (integer)$val;
        }
        array_shift($data['size']);
        array_pop($data['size']);

		// THINKPHP文件上传类，配置参数    
		$config = array(
			'maxSize' => 3145728,
			'savePath' => './adminheadimg/',
			'saveName' => array('saveName',''),
			'exts' => array('jpg', 'gif', 'png', 'jpeg'),
			'autoSub' => false,
		);

		$upload = new \Think\Upload($config);// 实例化上传类

        // 上传单个文件     
        $info = $upload->uploadOne($_FILES['avatar_file']);

        if(!$info) {
        	// 上传错误提示错误信息      
        	$result['status'] = false;  
        	$result['error_info'] = $this->error($upload->getError());    
        	return $result;
        }
        	
    	// 上传成功 获取上传文件信息 
    	$result['status'] = true;  
    	$result['error_info'] = '上传成功';

    	//拼接裁剪参数
        $tmp_name = $info['savepath'] . $info['savename'];
		$save_path = './Public/Uploads' . ltrim($tmp_name , '.');

		//实例化图片处理类
        $image = new \Think\Image(); 
    	$image->open($save_path);

    	//执行裁剪操作
    	$size = $data['size'];
    	$image->crop($size[2],$size[3],$size[0],$size[1])->save($save_path);

    	//组成查询条件
    	$where['admn_id'] = ['eq' , session('adminLogin')['admn_id']];

    	//得到原头像信息
    	$old_pic = $this->field('admn_picture')->where($where)->find();
    	$old_pic = $old_pic['admn_picture'];

    	//修改表信息
    	$save['admn_picture'] = $info['savename'];
    	$this->where($where)->save($save);

    	//判断原头像是否是默认头像，不是就删除
    	if($old_pic != 'default.jpg'){
    		$del_path = './Public/Uploads/adminheadimg/' . $old_pic;
    		unlink($del_path);
    	}

        return $result;
	}
	
}
