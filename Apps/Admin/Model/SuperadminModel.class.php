<?php

namespace Admin\Model;

use Think\Model;

class SuperadminModel extends Model 
{

	protected $pass = '/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~]{6,22}$/';
	protected $phone = "/^(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/";
	/**
	 * [超管登录验证]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [boolean] [验证用户输入的账号密码是否通过]
	 */

	 protected $_auto = [
                [ 'admn_password', 'passhash', 1, 'callback' ],
            ];

    public function passhash( $pass )
    {

        return password_hash( $pass, PASSWORD_DEFAULT );

    }

	public function supnLogin()
	{
		$post = I('post.');
		$asd = [
			['supn_tel','userCheck','账号不存在',1,'callback'],
			['supn_password','pwdCheck','密码错误',1,'callback'],
			['code','checkVerify','验证码错误',1,'callback'],
		];

		$temp['status'] = true;
		$temp['info'] = '验证成功';
        if(!$this->validate($asd)->create($post))
        {
        	$temp['status'] = false;
			$temp['info'] = $this->getError();
        }else{
        	session('adminLogin',$this->getAdmin());
        }
        return $temp;
	}

	/**
	 * [超管登录验证码验证]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [boolean] [验证码输入是否正确]
	 * @param  [type] $code         [用户输入的验证码]
	 */
	public function checkVerify($code)
	{
		$verify = new \Think\Verify();
		return $verify->check($code);
	}

	/**
	 * [验证用户输入的账号是否存在]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [boolean] [验证用户输入的账号是否存在]
	 */
	public function userCheck(){

		$phone = preg_match( $this->phone, I('post.supn_tel'));
		if( $phone ){
			$map['supn_tel'] = I('post.supn_tel');
		}else{
			$map['supn_realname'] = I('post.supn_tel');
		}

		$list = $this->where($map)->find();
		
		if($list){
			return true;
		}
		return false;
	}

	/**
	 * [把该用户的信息返回到LoginContrller.class.php 写进session中]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [array] [用户的信息]
	 */
	public function getAdmin()
	{

		if( preg_match($this->phone, I('post.supn_tel'))){
			$map['supn_tel'] = I('post.supn_tel');
		}else{
			$map['supn_realname'] = I('post.supn_tel');
		}
		
		$list = $this->where($map)->find();
		return $list;
	}


	/**
	 * [验证密码是否正确]
	 * @author LinHao<137987537@qq.com>
	 * @param         [type]
	 * @return [boolean] [密码是否正确]
	 */
	public function pwdCheck()
	{
		$phone = preg_match( $this->phone, I('post.supn_tel') );

		if( $phone ){
			$map['supn_tel'] = I('post.supn_tel');
		}else{
			$map['supn_realname'] = I('post.supn_tel');
		}

		$name = I('post.supn_tel');
		$pwd = I('post.supn_password');

		$list = $this->where($map)->find();
		if( password_verify( $pwd, $list['supn_password'])){
			$status = true;
		}else{
			$status = false;
		}
		return $status;
	}

	/**
	 * [查询超级管理员信息]
	 * @author LinHao<137987537@qq.com>
	 * @param  int $supn_id      [超级管理员ID]
	 */
	public function supnInfoatc($supn_id)
	{
		$map['supn_id'] = ['eq',$supn_id];
		$res = $this->where($map)->find();
		return $res;
	}

	/**
	 * [修改超级管理员信息]
	 * @author LinHao<137987537@qq.com>
	 */
	public function editSupninfoact()
	{
		$post = I('post.');
		$_validate = [
			['supn_realname','require','用户名格式错误',1],
			['supn_tel','/^1[34578]\d{9}$/','手机格式错误',1,'regex'],
        	['supn_sex','0,1,2','非法的数据！',1,'in'],
			['supn_qq','/^[1-9][0-9]{4,13}$/','QQ号格式错误！',1],
			['supn_identity','/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/','身份证格式错误',1,'regex'],
        	['supn_email','email','邮箱格式错误！',1],
			['supn_birthday','checkBir','出生日期错误',1,'callback'],
		];

		
		$post = $this->validate($_validate)->create($post);

		$result = [];
		if($post)
		{
			$id['supn_id'] = ['eq',session('adminLogin')['supn_id']];
			$map['supn_realname'] = I('post.supn_realname');
			$map['supn_tel'] = I('post.supn_tel');
			$map['supn_sex'] = I('post.supn_sex');
			$map['supn_qq'] = I('post.supn_qq');
			$map['supn_identity'] = I('post.supn_identity');
			$map['supn_email'] = I('post.supn_email');
			$map['supn_birthday'] = strtotime(I('post.supn_birthday'));

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
	public function checkBir()
	{
		$time = time()-86400;
		$birth = strtotime(I('post.supn_birthday'));
		if ($birth>$time) {
			return false;
		}
		return ture;
	}


	/**
	 * [超级管理员头像信息修改]
	 * @author yj [15818708414@163.com]
	 * @return str 返回用户头像图片名
	 */
	public function getHeadPortrait()
	{
		$where['supn_id'] = session('adminLogin')['supn_id'];

		$result = $this->field('supn_picture')->where($where)->find();
		
		return $result['supn_picture'];
	}

	/**
	 * [超级管理员头像修改]
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
    	$where['supn_id'] = ['eq' , session('adminLogin')['supn_id']];

    	//得到原头像信息
    	$old_pic = $this->field('supn_picture')->where($where)->find();
    	$old_pic = $old_pic['supn_picture'];

    	//修改表信息
    	$save['supn_picture'] = $info['savename'];
    	$this->where($where)->save($save);

    	//判断原头像是否是默认头像，不是就删除
    	if($old_pic != 'default.jpg'){
    		$del_path = './Public/Uploads/adminheadimg/' . $old_pic;
    		unlink($del_path);
    	}

        return $result;
	}

	/**
	 * [验证超管原密码]
	 * @author LinHao<137987537@qq.com>
	 */
	public function chkOldpwdact()
	{
		$map['supn_id'] = ['eq',session('adminLogin')['supn_id']];
		$data = $this->where($map)->find();
		if ($data['supn_password']==I('post.supn_password')) {
			return true;
		}
		return false;
	}

	/**
	 * [修改超管密码]
	 * @author LinHao<137987537@qq.com>
	 */
	public function editSupnpwdatc()
	{

		if( I('post.repwd') == I('post.supn_password') ){
			$id['supn_id'] = ['eq',session('adminLogin')['supn_id']];
			$data = $this->where($id)->find();
			if ( password_verify( I('post.oldpwd'), $data['supn_password'] ) ) {
				if( preg_match( $this->pass, I('post.supn_password') )){
					$passwd = $this->passhash( I('post.supn_password')); 
					$data1['supn_password'] = $passwd;
					$res = $this->where($id)->save($data1);
					if( $res ){
						$info = ['status'=>true,'info'=>'修改成功'];
					}else{
						$info = ['status'=>false,'info'=>'修改失败'];
					}
				}else{
					$info = ['status'=>false,'info'=>'新密码格式不正确'];
				}
				
			}else{
				$info = ['status'=>false,'info'=>'原始密码不正确'];
			}
		}else{
			$info = ['status'=>false,'info'=>'重复密码不正确'];
		}
		
		return $info;
		
	}

}
