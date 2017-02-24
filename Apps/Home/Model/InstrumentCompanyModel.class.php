<?php
	
	namespace Home\Model;
	use Think\Model;

	/**
	*仪器库厂商MODEL
	*/
	class InstrumentCompanyModel extends Model
	{	
		//查询数据
		public function getData()
		{

			$company = $this->select();

			foreach( $company as $key => $val ){

				//格式化时间
				$company[$key]['appt_create_time'] = date( 'Y/m/d/H/i/s', $val['appt_create_time']);
				if( $val['appt_mod_time'] ){
					$company[$key]['appt_mod_time'] = date( 'Y/m/d/H/i/s', $val['appt_mod_time']);
				}

				//取厂商首字母
				$a = substr($val['appt_company_name'],0,1);
				$Ch = preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",$a);

				if($Ch){
					//如果是汉字使用公共函数(Pinyin())转换成拼音格式
					$company[$key]['appt_company_name1'] = Pinyin( $val['appt_company_name'], 1 );
				}else{
					//去掉空格
					$company[$key]['appt_company_name1'] = trim( $val['appt_company_name'] );
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

			//遍历查询出来的数组，将准备好的数组接入相应数据
			foreach( $company as $key => $val ){
				//取首字母转换为大写
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

			return [ 'company' => $company, 'companyA'=>$companyA,'companyB'=>$companyB,'companyC'=>$companyC,'companyD'=>$companyD,'companyE'=>$companyE,'companyF'=>$companyF,'companyG'=>$companyG,'companyH'=>$companyH,'companyI'=>$companyI,'companyJ'=>$companyJ,'companyK'=>$companyK,'companyL'=>$companyL,'companyM'=>$companyM,'companyO'=>$companyO,'companyP'=>$companyP,'companyQ'=>$companyQ,'companyR'=>$companyR,'companyS'=>$companyS,'companyT'=>$companyT,'companyU'=>$companyU,'companyV'=>$companyV,'companyW'=>$companyW,'companyX'=>$companyX,'companyY'=>$companyY,'companyZ'=>$companyZ,'companyOh'=>$companyOh ];
		}

		//查询厂商
		public function cate_Com()
		{
			$company = $this->limit(28)->field('appt_id,appt_company_name,appt_company_pic')->select();

			return [ 'company' => $company ];
		}

		// 查询单条厂商信息
		public function getOne()
		{
			$map['appt_id'] = I('get.cid');

			$company = $this->where( $map )->find();

			//如果值为空将其显示为'-'
			foreach( $company as $key => $val ){
				if( !$val ){
					$company[$key] = '-';
				}
			}

			//查询当前厂商下的商品
			$map1['appt_company_id'] = $company['appt_id'];
			$comCate = M('Instrument_goods')->where( $map1 )->order('appt_category_id')->select();
			$allGoods = $comCate;

			//遍历处理
			foreach( $comCate as $key => $val ){

				//根据分类查询当前厂商下的商品
				$map1['appt_category_id'] = $val['appt_category_id'];
				$comCate[$key]['cate'] = M('Instrument_goods')->where( $map1 )->select();

				//调用$this->father()获取顶级分类名称
				$comCate[$key]['cate_sname'] = $this->father( $val['appt_category_id'] )['cate_name'];

				//如果厂商重复只留一条
				if( $comCate[$key + 1]['appt_category_id'] == $val['appt_category_id'] ){
					unset( $comCate[$key] );
				}

			}	

			// 处理当前厂商下所有商品
			foreach( $allGoods as $key => $val ){

				//调用$this->father()获取相关信息
				$allGoods[$key]['cate'] = $this->father( $val['appt_category_id'] );

				//如果分类相同只留一条
				if( $allGoods[$key+ 1]['appt_category_id'] == $val['appt_category_id'] ){
					unset( $allGoods[$key] );
				}
			}

			//重新排列数组
			$allGoods1 = [];
			foreach( $allGoods as $key => $val ){
				$allGoods1[] = $allGoods[$key];
			}

			// 再次过滤
			foreach( $allGoods1 as $key => $val ){
				if( $allGoods1[$key+1]['cate']['pid'] == $val['cate']['pid'] ){
					unset( $allGoods1[$key] );
				}
			}

			// 处理数据
			foreach( $allGoods1 as $key => $val ){
				$map['parent_id'] = $val['cate']['pid'];
				$son = M('Instrument_category')->where( $map )->field('id,cate_name')->select();
				foreach( $son as $k => $v ){
					$map1['appt_category_id'] = $v['id'];
					$map1['appt_company_id'] = $val['appt_company_id'];
					$allGoods1[$key]['soncate'][$k]['cate_id'] = $v['id'];
					$allGoods1[$key]['soncate'][$k]['com_id'] = $val['appt_company_id'];
					$allGoods1[$key]['soncate'][$k]['cate_name'] = $v['cate_name'];
					$allGoods1[$key]['soncate'][$k]['count'] = M('Instrument_goods')->where( $map1 )->count(); 
					if(!$allGoods1[$key]['soncate'][$k]['count']){
						unset($allGoods1[$key]['soncate'][$k]);
					}
				}

			}

			//过滤数据
			foreach( $allGoods1 as $key => $val ){
				foreach( $val['soncate'] as $k => $v ){
					if( !$v ){
						unset( $allGoods1[$key]['soncate'][$k] );
					}
				}
			}
		
			return [ 'company' => $company, 'comCate' => $comCate,'allGoods' => $allGoods1 ];
		}

		//设置一个方法查询顶级分类信息
		public function father($id)
		{	
			$cate = [];
			$map['id'] = $id;
			$data = M('Instrument_category')->where( $map )->field('cate_name,cate_path')->find();
			$map['id'] =  explode(",", $data['cate_path'])[1];
			$father = M('Instrument_category')->where( $map )->field('id,cate_name')->find();
			$cate['pid'] = $father['id'];
			$cate['pcate_name'] = $father['cate_name'];
			$cate['cate_name'] = $data['cate_name'];

			return $cate;
		}
	}
