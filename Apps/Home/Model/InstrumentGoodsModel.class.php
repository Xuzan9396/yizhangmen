<?php
	namespace Home\Model;
	use Think\Model;

	/**
	*商品MODEL处理
	*
	*/
	class InstrumentGoodsModel extends Model
	{
		//获取数据
		public function getData()
		{
			
			$filter = [];
			$map = '';
			if( I('get.gid') ){
				$map = "appt_category_id = " . I('get.gid');
			}
			
			if( I('get.aid') ){
				$aid = I('get.aid');
				$aid = explode( '$', $aid);
				$mapg = "";
				foreach( $aid as $key => $val ){
					if( $key >= 1 ){
						$mapg .= " or appt_attributeval_value like '" . $val ."'";
					}else{
						$mapg .= " appt_attributeval_value like '" . $val . "'";
					}
				}
				$attra = M('Instrument_goods_attributeval')->where( $mapg )->select();
				foreach( $attra as $key => $val ){
					$attrmap['id'] = $val['appt_attribute_id'];
					$attra[$key]['attr_name'] = M('Instrument_category_attribute')->where( $attrmap )->find()['appt_attribute_name'];
				}
				$goodsid = [];
			
				$i = 0;
				foreach( $attra as $key => $val ){
					$goodsid[] = $val['appt_goods_id'];
					$filter[$i]['attr_id'] = $val['appt_id'];
					$filter[$i]['attr_name'] = $val['attr_name'] .":" .$val['appt_attributeval_value'];
					$i ++;
				}

			}

			
			if( I('get.cid') ){
				$mapcom = '';
				$arr = explode( ',', $_GET['cid']);
				$map .= " and ";
				foreach( $arr as $key => $val ){
					if( $key >=1 ){
						$map .= " or appt_company_id =" . $val;
						$mapcom .= " or appt_id =" . $val;
					}else{
						$map .= "appt_company_id =" . $val;
						$mapcom .= "appt_id =" . $val;
					}
				}

				$filter1 = M('Instrument_company')->where( $mapcom )->field('appt_id,appt_company_name')->select();
		
				foreach( $filter1 as $key => $val ){	
					$filter1[$key]['appt_company_name'] = '制造商:' . $val['appt_company_name'];		
				}

			}
			
			if( $filter1 && $filter ){
				$filter = array_merge( $filter1, $filter );
			}

			if( !$filter ){
				$filter = $filter1;
			}
			//根据GET传过来的gid查询出相关商品信息
			$page = myPage($this,$map, 10);
			$data = $this->where( $map )->limit($page->pagerows(),$page->maxrows())->order('appt_company_id')->join(array('app_instrument_category ON app_instrument_goods.appt_category_id = app_instrument_category.id','app_instrument_company ON app_instrument_goods.appt_company_id = app_instrument_company.appt_id'))->field('app_instrument_goods.*,app_instrument_category.cate_name,app_instrument_company.appt_company_name,appt_www')->select();

			if( $goodsid ){
				$data1 = [];
				foreach( $data as $key => $val ){
					foreach( $goodsid as $K => $v ){
						if( $val['appt_id'] == $v ){
							$data1[] =$data[$key];
 						}
					}	 
				}
				
				$data = $data1;
			}
			//格式化时间
			foreach( $data as $key => $val ){
				$data[$key]['appt_create_time'] = date( 'Y/m/d/H/i/s', $val['appt_create_time']);
				if( $val['appt_mod_time'] ){
					$data[$key]['appt_mod_time'] = date( 'Y/m/d/H/i/s', $val['appt_mod_time']);
				}

				$map2['appt_goods_id'] = $val['appt_id'];
				$data[$key]['attribute'] = M('Instrument_goods_attributeval')->where($map2)->select();
			}

			//查询当前分类厂商信息
			$mape['appt_category_id'] = I('get.gid');
			$company = $this->where( $mape )->join(array('app_instrument_company ON app_instrument_goods.appt_company_id = app_instrument_company.appt_id'))->field('app_instrument_goods.appt_category_id,app_instrument_goods.appt_company_id,app_instrument_company.appt_company_name')->order('appt_company_id')->select();
	
			foreach( $company as $key => $val ){
				$mapa['appt_category_id'] = I('get.gid');
				$mapa['appt_company_id'] = $val['appt_company_id'];
				$company[$key]['count'] = $this->where( $mapa )->count();
				//截取厂商第一个字，如果为中文，通过公共函数Pinyin转换成拼音放到新的下标里
				$a = substr($val['appt_company_name'],0,1);
				$Ch = preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",$a);

				if($Ch){
					$company[$key]['appt_company_name1'] = Pinyin( $val['appt_company_name'], 1 );
				}else{
					$company[$key]['appt_company_name1'] = trim( $val['appt_company_name'] );
				}

				//删除厂商id重复的数组并只留一个
				if( $company[$key+1]['appt_company_id'] == $val['appt_company_id'] ){
					unset( $company[$key] );
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

			//遍历数组，取厂商名称首字母将分类放入相应数组里
			foreach( $company as $key => $val ){

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
			
			//把属性名称放在数组里
			foreach($data as $key => $val){
				foreach($val['attribute'] as $k => $v){
					$map3['id'] = $v['appt_attribute_id'];
					$data[$key]['attribute'][$k]['attribute_name'] = M('Instrument_category_attribute')->where($map3)->find()['appt_attribute_name'];
				}

				//查询收藏表
				if( $_SESSION['home_user_info'] ){
					$collect = M('Instrument_collect')->where( 'user_id='.$_SESSION['home_user_info']['user_id'].' and goods_id='.$val['appt_id'] )->find()['status'];
					if( $collect ){
						$data[$key]['collect'] = $collect;
					}else{
						$data[$key]['collect'] = 0;
					}
				}
				
				
			}

			//根据当前分类ID查询属性表
			$mapb['appt_category_id'] = I('get.gid');
			$cateAttr = M('Instrument_category_attribute')->where($mapb)->order('appt_category_id')->select();
			
			//根据属性ID查询属性值放回$cateAttr数组里
			foreach($cateAttr as $key => $val){
				$map1['appt_attribute_id'] = $val['id'];
				$cateAttr[$key]['attribute_val'] = M('Instrument_goods_attributeval')->where($map1)->select();

			}
			
			//统计分类属性值
			foreach( $cateAttr as $key => $val ){
				foreach( $val['attribute_val'] as $k => $v ){
					$map8['appt_goods_id'] = $v['appt_goods_id'];
					$map8['appt_attribute_id'] = $v['appt_attribute_id'];
					$cateAttr[$key]['attribute_val'][$k]['count'] = M('Instrument_goods_attributeval')->where( $map8 )->count();
					if( $cateAttr[$key]['attribute_val'][$k+1]['appt_attributeval_value'] == $v['appt_attributeval_value'] ){
						unset( $cateAttr[$key]['attribute_val'][$k] );
					}
				}
			}

			//使用showTitle方法递归获取当前分类的名称
			$title = $this->showTitle( I('get.gid') );

			//分页方法
			$show = $page->get_page();
		
			return ['title'=>$title,'goods'=>$data,'company'=>$company,'companyA'=>$companyA,'companyB'=>$companyB,'companyC'=>$companyC,'companyD'=>$companyD,'companyE'=>$companyE,'companyF'=>$companyG,'companyH'=>$companyH,'companyI'=>$companyI,'companyJ'=>$companyJ,'companyK'=>$companyK,'companyL'=>$companyL,'companyM'=>$companyM,'companyO'=>$companyO,'companyP'=>$companyP,'companyQ'=>$companyQ,'companyR'=>$companyR,'companyS'=>$companyS,'companyT'=>$companyT,'companyU'=>$companyU,'companyV'=>$companyV,'companyW'=>$companyW,'companyX'=>$companyX,'companyY'=>$companyY,'companyZ'=>$companyZ,'companyOh'=>$companyOh,'cateAttr'=>$cateAttr,'pageshow'=>$show,'filter'=>$filter];
		}

		//根据当前分类ID获取商品名称
		public function getGoodsName()
		{
			$mapc['appt_category_id'] = I('get.id');
			return $this->where($mapc)->field('appt_id,appt_goodsname')->select();
		}


		public function getOne()
		{	
			//根据当前ID查询商品信息
			$mapd['appt_id'] =I('get.id');
			$data = $this->where($mapd)->find();
			
			//使用递归方法$this->showTitle()查询当前分类名称并拼接上商品名称作为显示标题
			$title = $this->showTitle( $data['appt_category_id'] ) . ' / ' .$data['appt_goodsname'];

			//根据当前商品查询厂商信息
			$map1['appt_id'] = $data['appt_company_id'];
			$company = M('Instrument_company')->where($map1)->find();

			//查询当前商品的属性值
			$map2['appt_goods_id'] = I('get.id');
			$attrval = M('Instrument_goods_attributeval')->where($map2)->select();

			//遍历查询属性名称放入$attrval数组里
			foreach($attrval as $key => $val){
				$map3['id'] = $val['appt_attribute_id'];
				$attrval[$key]['attribute_name'] = M('Instrument_category_attribute')->where($map3)->field('appt_attribute_name')->find()['appt_attribute_name'];
			}

			//根据当前商品查询10条类似商品
			$id['appt_category_id'] = $data['appt_category_id'];
			$leishi = $this->where($id)->limit(10)->select();

			//遍历查询类似商品的分类名称和厂商名称
			foreach($leishi as $key => $val){
				$map4['id'] = $val['appt_category_id'];
				$map5['appt_id'] = $val['appt_company_id'];
				$leishi[$key]['category_name'] = M('Instrument_category')->where($map4)->field('cate_name')->find()['cate_name'];
				$leishi[$key]['company_name'] = M('Instrument_company')->where($map5)->field('appt_company_name')->find()['appt_company_name'];

				if( $val['appt_id'] == $_GET['id'] ){
					unset($leishi[$key]);
				}
			}
			
			//如果厂商信息的值为null将其设置显示为'-',(作用是占位，不然前台样式会乱(sorry))
			foreach( $company as $key => $val ){
				if( !$val ){
					$company[$key] = '-';
				}
			}

			//查询6个当前厂商的其他商品
			$map6['appt_company_id'] = $company['appt_id'];
			$oherGoods = $this->where($map6)->limit(6)->select();
			
			//查询其他商品的分类名称和厂商名
			foreach( $oherGoods as $key => $val ){
				$map7['id'] = $val['appt_category_id'];
				$map8['appt_id'] = $val['appt_company_id']; 
				$oherGoods[$key]['cate_name'] = M('Instrument_category')->where($map7)->field('cate_name')->find()['cate_name'];
				$oherGoods[$key]['company_name'] = M('Instrument_company')->where($map8)->field('appt_company_name')->find()['appt_company_name'];
				if( $val['appt_id'] == $_GET['id'] ){
					unset($oherGoods[$key]);
				}
			}
	
			return ['title'=>$title,'data'=>$data,'company'=>$company,'attr'=>$attrval,'leishi'=>$leishi,'oherGoods'=>$oherGoods];
		}

		//对比
		public function contrast()
		{
			$pid = I('get.pid');
			$pid = trim( $pid, ',' );
			if( $pid ){
				$pid = explode( ',', $pid);
				$i = '';
				$goods = [];
				foreach( $pid as $key => $val ){
					$g = M('Instrument_goods')->where( 'appt_id='.$val )->field('appt_id,appt_company_id,appt_goodsname,appt_pic')->find();
					if( $g ){
						$goods[] = $g; 					
					}
				}

				foreach( $goods as $key => $val ){
					$company = M('Instrument_company')->where('appt_id='.$val['appt_company_id'])->find();
					$goods[$key]['company'] = $company['appt_company_name'];
					$goods[$key]['www'] = $company['appt_www'];

					$attrval = M('Instrument_goods_attributeval')->where('appt_goods_id='.$val['appt_id'])->field('appt_attribute_id,appt_attributeval_value')->select();
					if( $attrval ){
						$goods[$key]['attrval'] = $attrval;
					}
				}

				foreach( $goods as $key => $val ){
					foreach( $val['attrval'] as $k => $v ){
						$attr = M('Instrument_category_attribute')->where('id='.$v['appt_attribute_id'])->find()['appt_attribute_name'];
						if( $attr ){
							$goods[$key]['attrval'][$k]['attrname'] = $attr;
						}
					}
				}

				foreach( $goods as $key => $val ){
					foreach( $val['attrval'] as $k => $v ){
						switch( $v['attrname'] ){
							case '描述':$goods[$key]['miaosu'] = $v['appt_attributeval_value'];break;
							case '波导尺寸':$goods[$key]['bodaochicun'] = $v['appt_attributeval_value'];break;
							case '频率':$goods[$key]['pinglv'] = $v['appt_attributeval_value'];break;
							case '插入损耗':$goods[$key]['shunhao'] = $v['appt_attributeval_value'];break;
							case '隔离度':$goods[$key]['gelidu'] = $v['appt_attributeval_value'];break;
							case 'VSWR':$goods[$key]['vswr'] = $v['appt_attributeval_value'];break;
							case '类型':$goods[$key]['leixin'] = $v['appt_attributeval_value'];break;
							case '通道数':$goods[$key]['tongdao'] = $v['appt_attributeval_value'];break;
							case '功率':$goods[$key]['gonglv'] = $v['appt_attributeval_value'];break;
							case '控制电压':$goods[$key]['kongzidianya'] = $v['appt_attributeval_value'];break;
							case '连接类型':$goods[$key]['lianjie'] = $v['appt_attributeval_value'];break;
							case '阻抗':$goods[$key]['zhukang'] = $v['appt_attributeval_value'];break;
							case '线缆损耗检测范围':$goods[$key]['xianlan'] = $v['appt_attributeval_value'];break;
							case '采样率':$goods[$key]['caiyanglv'] = $v['appt_attributeval_value'];break;
							case '读取速度':$goods[$key]['duqv'] = $v['appt_attributeval_value'];break;
						}
					}
				}
			}

			return $goods;
		}

		//设置一个方法递归查询分类名称
		public function showTitle( $id )
		{
			$mapa['id'] = $id;

			//查询分类名称
			$res = M('Instrument_category')->where($mapa)->field('id,parent_id,cate_name')->find();
			if($res){
				//调用本身继续查询
				$title.= $this->showTitle($res['parent_id']) . ' / ';
			}

			$title .= $res['cate_name'];

			return $title;
		}
	}