<?php
    namespace Home\Model;

    use Think\Model;

    class ImpuserModel extends Model
    {
    	/**
		 * [获取用户详细信息]
		 * @author yj [15818708414@163.com]
		 * @return array 返回装有用户数据的数组
		 */
    	public function getImpuserInfo()
    	{
    		$where['user_id'] = ['eq' , session('home_user_info.user_id')];
    		$impuser_info = $this->where($where)->find();

    		if($impuser_info['impr_birthday']){
	    		$impuser_info['impr_birthday'] = date('Y-m-d',$impuser_info['impr_birthday']);
    		}

    		return $impuser_info;
    	}

        /**
         * 注册成功添加信息到用户详情表
         * @author YangJun
         * @return array
         */
        public function registerActToImp()
        {
            $add['user_id'] = session('home_user_info.user_id');
            $add['impr_picture'] = "user_default.jpg";

            $this->add($add);
        }

    	/**
		 * [用户信息修改]
		 * @author yj [15818708414@163.com]
		 * @return array 返回装有用户数据的数组
		 */
    	public function modifiedData()
    	{
    		$post = I('post.');
            
    		$_validate = [
    			['impr_tel','/0\d{2,3}-\d{5,9}|0\d{2,3}-\d{5,9}/','固定电话输入格式错误,请输入xxxx-xxxxxxx格式',2,'regex'],
                ['impr_address','1,255','详细通讯地址超出限制长度',2,'length'],
    			['impr_identity','1,18','请输入正确的身份证号码',2,'length'],
    		];

    		if($data = $this->validate($_validate)->create()){
    			//查询条件
	    		$impwhere['user_id'] = ['eq' , session('home_user_info.user_id')];

	    		//将日期转换成时间戳
    			$data['impr_birthday'] = strtotime($data['impr_birthday']);

	    		//修改详情表这个用户的信息 save
    			$this->where($impwhere)->save($data);

	    		return '资料提交成功';
			}else{
				return $this->getError();
			}
    		
    	}

    	/**
		 * [用户信息修改]
		 * @author yj [15818708414@163.com]
		 * @return str 返回用户头像图片名
		 */
    	public function getHeadPortrait()
    	{
    		$where['user_id'] = session('home_user_info.user_id');

    		$result = $this->field('impr_picture')->where($where)->find();
    		
    		return $result['impr_picture'];
    	}

    	/**
		 * [用户头像修改]
		 * @author yj [15818708414@163.com]
		 * @return arr 返回图片上传状态
		 */
    	public function makeHeadAct()
    	{
    		$data['files'] = $_FILES;

	        $tmp_arr = explode('&quot;:' , I('post.avatar_data'));

	        foreach($tmp_arr as $val){
	        	if((integer)$val == 0){
	        		continue;
	        	}

	        	$data['size'][] = (integer)$val;
	        }

			// THINKPHP文件上传类，配置参数    
			$config = array(
				'maxSize' => 3145728,
				'savePath' => './headportrait/',
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
        	$where['user_id'] = ['eq' , session('home_user_info.user_id')];

        	//得到原头像信息
        	$old_pic = $this->field('impr_picture')->where($where)->find();
        	$old_pic = $old_pic['impr_picture'];

        	//修改表信息
        	$save['impr_picture'] = $info['savename'];
        	$this->where($where)->save($save);

        	//判断原头像是否是默认头像，不是就删除
        	if($old_pic != 'user_default.jpg'){
        		$del_path = './Public/Uploads/headportrait/' . $old_pic;
        		unlink($del_path);
        	}

	        return $result;
    	}
    	
    }

