<?php
	namespace Admin\Model;
	use Think\Model;

	/**
	*仪器库厂商MODEL
	**/
	class InstrumentCompanyModel extends Model
	{
		//设置添加厂商验证规则
		protected $_validate = [
				[ 'appt_company_name','','厂家名称已重复',1,'unique'],
				[ 'appt_company_name','require', '厂家名称不能为空'],
				[ 'appt_country','require', '所在国家不能为空'],
				[ 'appt_address', 'require', '厂家地址不能为空' ],
				[ 'appt_phone', 'require', '电话不能为空' ],
				[ 'appt_www', 'require', '网址不能空' ],
				[ 'appt_detail', 'require', '详情不能为空' ],
			
			]; 

		//查询数据
		public function getData()
		{
			$company = $this->select();

			//遍历处理数据
			foreach( $company as $key => $val ){

				//格式化时间
				$company[$key]['appt_create_time'] = date( 'Y/m/d/H/i/s', $val['appt_create_time']);
				if($val['appt_mod_time']){
					$company[$key]['appt_mod_time'] = date( 'Y/m/d/H/i/s', $val['appt_mod_time']);
				}

				//截取厂商名称首字母
				$a = substr($val['appt_company_name'],0,1);
				$Ch = preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",$a);

				if($Ch){
					//如果首字母为中文便转换为拼音格式(调用公共函数Pinyin()转换)
					$company[$key]['appt_company_name1'] = Pinyin( $val['appt_company_name'], 1 );
				}else{
					//去掉空格
					$company[$key]['appt_company_name1'] = trim($val['appt_company_name']);
				}

			}

			$companyA = [];
			$companyB = [];
			$companyC = [];
			$companyD = [];
			$companyE = [];
			$companyF = [];
			$companyG = [];
			$companyH = [];
			$companyI = [];
			$companyJ = [];
			$companyK = [];
			$companyL = [];
			$companyM = [];
			$companyN = [];
			$companyO = [];
			$companyP = [];
			$companyQ = [];
			$companyR = [];
			$companyS = [];
			$companyT = [];
			$companyU = [];
			$companyV = [];
			$companyW = [];
			$companyX = [];
			$companyY = [];
			$companyZ = [];
			$companyOh = [];

			//将准备好的数组接入相应数据
			foreach( $company as $key => $val ){

				//取首字母进行判断
				$b = substr($val['appt_company_name1'],0,1);
				$b = ucfirst($b);

				if( $b == 'A'){
					$companyA[] = $company[$key];	
				}elseif( $b == 'B'){
					$companyB[] = $company[$key];	
				}elseif( $b == 'C'){
					$companyC[] = $company[$key];	
				}elseif( $b == 'D'){
					$companyD[] = $company[$key];	
				}elseif( $b == 'E'){
					$companyE[] = $company[$key];	
				}elseif( $b == 'F'){
					$companyF[] = $company[$key];	
				}elseif( $b == 'G'){
					$companyG[] = $company[$key];	
				}elseif( $b == 'H'){
					$companyH[] = $company[$key];	
				}elseif( $b == 'I'){
					$companyI[] = $company[$key];	
				}elseif( $b == 'J'){
					$companyJ[] = $company[$key];	
				}elseif( $b == 'K'){
					$companyK[] = $company[$key];	
				}elseif( $b == 'L'){
					$companyL[] = $company[$key];	
				}elseif( $b == 'M'){
					$companyM[] = $company[$key];	
				}elseif( $b == 'N'){
					$companyN[] = $company[$key];	
				}elseif( $b == 'O'){
					$companyO[] = $company[$key];	
				}elseif( $b == 'P'){
					$companyP[] = $company[$key];	
				}elseif( $b == 'Q'){
					$companyQ[] = $company[$key];	
				}elseif( $b == 'R'){
					$companyR[] = $company[$key];	
				}elseif( $b == 'S'){
					$companyS[] = $company[$key];	
				}elseif( $b == 'T'){
					$companyT[] = $company[$key];	
				}elseif( $b == 'U'){
					$companyU[] = $company[$key];	
				}elseif( $b == 'V'){
					$companyV[] = $company[$key];	
				}elseif( $b == 'W'){
					$companyW[] = $company[$key];	
				}elseif( $b == 'X'){
					$companyX[] = $company[$key];	
				}elseif( $b == 'Y'){
					$companyY[] = $company[$key];	
				}elseif( $b == 'Z'){
					$companyZ[] = $company[$key];	
				}else{
					$companyOh[] = $company[$key];
				}

			}

			return ['company'=>$company,'companyA'=>$companyA,'companyB'=>$companyB,'companyC'=>$companyC,'companyD'=>$companyD,'companyE'=>$companyE,'companyF'=>$companyG,'companyH'=>$companyH,'companyI'=>$companyI,'companyJ'=>$companyJ,'companyK'=>$companyK,'companyL'=>$companyL,'companyM'=>$companyM,'companyO'=>$companyO,'companyP'=>$companyP,'companyQ'=>$companyQ,'companyR'=>$companyR,'companyS'=>$companyS,'companyT'=>$companyT,'companyU'=>$companyU,'companyV'=>$companyV,'companyW'=>$companyW,'companyX'=>$companyX,'companyY'=>$companyY,'companyZ'=>$companyZ,'companyOh'=>$companyOh];
		}

		//添加厂商
		public function addCompany()
		{
			//实例化think上传类
			$file  = new \Think\Upload();

			//设置图片保存路径并将文件传入upload()方法里
		  	$file->savePath  = '/Instrument/Company/pic/' ; 
		   	$info  =  $file->upload($_FILES); 

		   	//将返回的文件路径和文件名拼接起来存入$pic变量
			$pic  =  $info['pic']['savepath'].$info['pic']['savename'];

			if($info){

				//准备好当天日期(年月日),创建一个文件夹以当天上传日期为名的路径，以便存储略缩图
				$date  = date( 'Y-m-d' );
				$zoomPath = './Public/Uploads/Instrument/Company/zoom/' . $date . '/';
				if( !file_exists($zoomPath) ){
					mkdir( $zoomPath, 0777, true);
				}
				
				//略缩图保存名称为上一步创建的文件夹+原图名称
				//实例化think图片略缩类，将原图全路径传入，保存略缩图大小和略缩图名称
			   $zoom  = $zoomPath . $info['pic']['savename']; 
			   $image = new \Think\Image();
			   $image->open( './Public/Uploads' . $pic ); 
			   $zoomres = $image->thumb(80, 80)->save( $zoom );
			  
			  //重新拼接略缩图保存进数据库的名称
			   $zoomName = '/Instrument/Company/zoom/' . $date . '/' . $info['pic']['savename']; 

			   // 如果文件上传成功，将处理好的数据存入数据库
			   if( $zoomres ){
					$data = I('post.');
			   		$data['appt_company_pic'] = $pic;
					$data['appt_company_zoom'] = $zoomName;
					$data['appt_create_time'] = time();
					$post = $this->create( $data );
			   		if($post){
				   		$res = $this->add();
			   			$info = [ 'status' => true,'info' => '保存成功'];
			   		}else{
			   			$info = ['status' => false, 'info' => $this->getError()];
			   			unlink('./Public/Uploads' .$data['appt_company_pic']);
			   			unlink('./Public/Uploads' .$data['appt_company_zoom']);
			   		}
			   }else{
			   		$info = [ 'status' => false, 'info' => '缩略图处理失败'];
			   		unlink('./Public/Uploads' .$data['appt_company_pic']);
			   }

			}else{
				$info = ['status' => false, 'info' => '原图上传失败' ];
			}
				   
		   //返回处理结果
		   return $info;

		}

		//修改厂家信息
		public function saveCompany()
		{
			$data = I('post.');
			$data['appt_mod_time'] = time();
			$post = $this->create( $data );
			if( $post ){
				$map['appt_id'] = I('post.appt_id');
			    $this->where( $map )->save( $data );

				$info = [ 'status' => true, 'info' => '保存成功'];
			}else{
				$info = ['status' => false, 'info' => $this->getError()];
			}

			return $info;
		}

		//删除厂商
		public function delCompany()
		{
			$map['appt_id'] = I('post.appt_id');
			$map1['appt_company_id'] = I('post.appt_id');

			//查询要删除的厂商是否有商品存在
			$instrument = M('Instrument_goods')->where($map1)->field('appt_company_id')->find();
			if(!$instrument){
				//如果没有商品，将其删除
				$data = $this->where($map)->find();
				$res = $this->where( $map )->delete();
				if( $res ){
					$info = ['status' => true,'info'=>'删除成功'];
					unlink('./Public/Uploads' .$data['appt_company_pic']);
					unlink('./Public/Uploads' .$data['appt_company_zoom']);
				}else{
					$info = ['status'=>false,'info'=>'删除失败'];
				}
			}else{
				$info = ['status'=>false,'info'=>'没有权限，该厂商下面还有商品'];
			}
			

			return $info;
		}
	}