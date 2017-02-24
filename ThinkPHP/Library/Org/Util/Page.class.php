<?php
/**
 * 分页类
 * 第三方类二次重写
 * @package     sample
 * @subpackage  classes
 * @author      Luke <www.q1q.xyz>
 * @version     v6.1 
*/
class Page{
 protected	static  $class_name='t_st_in',           //链接按钮样式
	                $just_class_name='just_st_index',//当前页面按钮样式
			        $ajax = false,     //是否采用ajax分页,默认关闭
                    $ajax_out = '',    //AJAX内容输出元素
		        	$url='',           //当前页面
			        $max_rows = 10,    //每页显示数
	                $lastnum = 0,      //最后页数	        
	                $other_url = '',   //其它URL
	                $maxpages = 8,     //页数标签最大值
					$totalrow = 0,     //总记录数
	                $url_name = 'p',   //URL名称
			        $error='',         //错误属性
					$etips=false,      //提示错误信息
					$movetop='',       //点击分页时返回顶部					
					$theme = 'A',      //样式主题
					$url_model = 1,    //URL模式
					$url_model_other = true,//采用url模式2时,是否加载其它参数
					$url_suffix = true;     //是否保留后缀
	public  $pagenum;          //URL参数
	public static $tags = array();   //分页标签数组

	

#------------------------------------------------------------------------------#
#                           【实例化类库参数】                                 #
#------------------------------------------------------------------------------#
#  内集成实例化方法：new Page(array) 或者 Page(array)	                       #
#  数组内容：array([每页显示数(max)【必须】],[记录集总数(total)【必须】],      #       
#  [当前分页值(pagenum)【必须】],[URL参数前的URL地址(url)【必须】],            #
#  [分页标签数(maxpages)【可选】],[分页URL名(url_name)【可选，默认为p】])      #	
#------------------------------------------------------------------------------#
#              实例化后调取 $p->get_page()                                     #
#------------------------------------------------------------------------------#
	
	//实例化类，$page为数组格式
	
	public function __construct($page=array()){ 
	     $maxnum    =isset($page['max'])?$page['max']:'';            //[每页显示数，必须]
	     $total     =isset($page['total'])?$page['total']:'';        //[记录集总数，必须]
	     $pagenum   =isset($page['pagenum'])?$page['pagenum']:'';    //[分页参数,默认内置]
	     $url       =isset($page['url'])?$page['url']:__ACTION__;    //[URL地址，必须]
	     $maxpages  =isset($page['maxpages'])?$page['maxpages']:'';  //[分页标签数，必须]
	     $url_name  =isset($page['url_name'])?$page['url_name']:'';  //[分页URL名]
		 $ajax      =isset($page['ajax'])?$page['ajax']:'';          //[开启ajax分页,bool值，Ajax分页]
		 $ajax_out  =isset($page['out'])?$page['out']:'';            //[AJAX内容输出元素名，Ajax分页] 
		 $errtips   =isset($page['error'])?$page['error']:'';        //错误信息开关
		 $movetop   =isset($page['top'])?$page['top']:'';            //点击分页时返回顶部,默认开启         		 
		 $theme     =isset($page['theme'])?$page['theme']:'';        //样式主题
		 $url_model =isset($page['url_model'])?$page['url_model']:1; //URL模式
		 static::$url_model_other = isset($page['url_model'])?$page['url_model_other']:true;
		 static::$tags = !empty($page['tags']) ? $page['tags'] : array('<<','<','>','>>');//标签[首页,上一页,下一页,尾页,编码]
		 static::$url_suffix = $page['url_suffix']?true:false;

		 static::$max_rows = !empty($maxnum) ? $maxnum : 10; //每页最大数为空时默认为10
		 static::$url      = !empty($url) ? $url : '';  //当前URL页
		 static::$movetop  = is_bool($movetop)&&$movetop!=true ? '' : '$("html,body").animate({scrollTop:0},400);';
		 //参数为空时，提示错误信息
		 static::$etips = $errtips==true ? true : false;
	  if(self::$etips){
		 static::$error = empty($total) ? '<font color="red">The Record Total is Empty!</font>' : '';
		 static::$error = empty($url) ? self::$error.'<font color="red">The URLaddress is Empty!</font>' : self::$error;	
		 static::$error = $ajax==true && empty($ajax_out) ? 	self::$error.'This Ajax outDom is Empty,try put out type!' :  self::$error;
		  }
		 static::$lastnum  = ceil($total/$maxnum)-1; //总页数（从0开始）         
         static::$url_name = !empty($url_name) ? $url_name : 'p';    //URL参数名，默认为p
         static::$maxpages = !empty($maxpages) && $maxpages>2 ? $maxpages : 3;//分布标签数，不能小于2
		 $this->pagenum  = !empty($pagenum) ? $pagenum : I(self::$url_name);			
			     
				//获取URL数组
				 			foreach ($_GET as $_get_name => $_get_value) {
						   
						   $_get_value=urlencode($_get_value);//中文的URL进行转码				   
							if($_get_name!="_URL_"&&$_get_name != self::$url_name){ // 去掉分页参数URL
							 if($url_model==1){

								static::$other_url .= "&{$_get_name}={$_get_value}";
							 }else{

							  static::$other_url[$_get_name]= $_get_value;	 
						   }
					     }		

			          }
		 		
						

	        
		 //分页ajax,如果为空，则关闭
		 static::$ajax = $ajax==true ? true : false;
		 static::$ajax_out = $ajax==false || empty($ajax_out) ? '' : str_replace('#','',$ajax_out);		 
		 static::$theme = !empty($coding) ? $coding : 'A';
		 static::$url_model = !empty($url_model) ? $url_model : 1;
		 static::$totalrow = !empty($total) ? $total : 0;
	 }
     
	/*
	 * @ Get Pages 
	 * @ 创建内部函数，获取分页标签
	 * @ Retrun
	 */
	protected  function get_pages(){ 
		
		switch(true){ //获得标签最小值
		  case self::$maxpages>self::$lastnum || $this->pagenum<ceil(self::$maxpages/2):
		  $a=1;
		  break;
		  case $this->pagenum + ceil(self::$maxpages/2)<self::$lastnum+1:
		  $a=  $this->pagenum + 2 - ceil(self::$maxpages/2);
		  break;
		  default:
		  $a=  self::$lastnum+2 - self::$maxpages;
		  break;
		   }
		switch(true){ //获得标签最大值
		  case  self::$maxpages>self::$lastnum || self::$maxpages > self::$lastnum: 
		  $b=self::$lastnum+1;
		  break;
		  case self::$maxpages<=self::$lastnum:
		  switch(true){ 
		  case $this->pagenum+ceil(self::$maxpages/2)>=self::$lastnum+ceil(self::$maxpages/2)-1:
          $b=self::$lastnum+1;$a=self::$lastnum+2-self::$maxpages;
		  break;
		  default:
		  $b=$a+self::$maxpages-1;
		  break;
		    }
			} 
			$pagearray="";
			for($i=$a;$i<=$b;$i++){ //将标签和URL、样式等保存成字符串
			 if($i-1==$this->pagenum){
			$pagearray .=  '<span class="'.self::$just_class_name.'">'.$i.'</span>'; 	
				}else{
				//判断是否为AJAX提交
				 $link = self::$ajax==true ? '<a onclick=page_ajax_'.self::$ajax_out.'("'.self::UrlMerge($i-1).'") >'  : '<a href="'.self::UrlMerge($i-1).'">';
			    //生成链接代码 
				  $pagearray .=  $link.'<span class="'.self::$class_name.'">'.$i.'</span></a>';	  
				  }			
				}
		  //分页类别大于最大标签栏时
		  //头部
		   if($this->pagenum - ceil(self::$maxpages/2)>1&&self::$maxpages<self::$lastnum){
			  //判断是否为AJAX提交
				 $link = self::$ajax==true ? '<a onclick=page_ajax_'.self::$ajax_out.'("'.self::UrlMerge(0).'") >'  : '<a href="'.self::UrlMerge(0).'">';
			    //生成链接代码 
				   $pepend =  $link.'<span class="'.self::$class_name.'">1</span></a>..';   
			 }
			 
		   if(($this->pagenum+ceil(self::$maxpages/2) < self::$lastnum-1)&&self::$maxpages<self::$lastnum){
			  //判断是否为AJAX提交
				 $link = self::$ajax==true ? '<a onclick=page_ajax_'.self::$ajax_out.'("'.self::UrlMerge(self::$lastnum).'") >'  : '<a href="'.self::UrlMerge(self::$lastnum).'">';
			    //生成链接代码 
				   $linkmore = $this->pagenum+ceil(self::$maxpages/2)-1<self::$lastnum-2 ? '..' : '';
				   $addpend =  $linkmore.$link.'<span class="'.self::$class_name.'">'.(self::$lastnum+1).'</span></a>';   
			 }
			return $pepend.$pagearray.$addpend;		 
		}


     //@组合URL
	 static protected function UrlMerge($value){
		if(self::$url_model==1){//参数随后
		  $url = self::$url.'?'.self::$url_name.'='.$value.self::$other_url;	
	     }else{//U模式
		   if(self::$url_model_other)$urldata = self::$other_url;
		   $urldata[self::$url_name] = $value;
		   $url = U(str_replace('.html','',self::$url),$urldata); 
		  }
		  return self::$url_suffix ? $url : str_replace('.html','/',$url);
	    }
	/*
	 * @ Get Limit Min 
	 * @ first data
	 * @ Retrun
	 */
	
	public function pagerows(){
		  return $this->pagenum*self::$max_rows; 
		}
		
    /*
	 * @ Get Limit Max 
	 * @ Last data
	 * @ Retrun
	 */
	public function maxrows(){
		  return self::$max_rows; 
	    }
		
	/*
	 * @ Get Ajax page function
	 * @ JScode require jQuery 1.6+
	 * @ Retrun
	 * @ Static $ajax_out is out put dom 
	 */
	protected static function Pajax(){
	     return  '<script>function page_ajax_'.self::$ajax_out.'(url){if(url!=""){$.ajax({type:"get",url:url,async:false,beforeSend: function(){ $("#'.self::$ajax_out.'").html("<div style=\'text-align:center;margin-top:40%\'>Loading....</div>");},success:function(data){$("#'.self::$ajax_out.'").html(data);'.self::$movetop.'},error:function(errorThrown){$("#'.self::$ajax_out.'").html(errorThrown);}})}};$(".t_st_in").hover(function(){$(this).css({border:"1px solid #3c3",background:"#c0ffc0"});},function(){$(this).css({border:"1px solid #ccc",background:"white"});});</script>';}
	
    /*
	 * @ Get Pags INTO
	 * @ 访问所有的分页标签(包括第一页、上一页、分页数、下一页、最后一页)
	 * @ Retrun
	 */  
	   
	 public function get_page(){ 

	     //如果系统有错误，跳出函数
	     if(!empty(self::$error)){
		   return self::$error;
		   exit;
		   }
		 // 获得第一页和上一页
		 if($this->pagenum>0){ 
		   //判断是否为AJAX提交
		    $na_href = self::$ajax==true ? '<a onclick=page_ajax_'.self::$ajax_out.'("'.self::UrlMerge(0).'") >' : '<a href="'.self::UrlMerge(0).'">';
			$nb_href = self::$ajax==true ? '<a onclick=page_ajax_'.self::$ajax_out.'("'.self::UrlMerge($this->pagenum-1).'") >' : '<a href="'.self::UrlMerge($this->pagenum-1).'">';
		   //生成链接代码
			$naviga = sprintf('%s<span class="%s">%s</span></a>%s<span class="%s">%s</span></a>',$na_href,self::$class_name,self::$tags[0],$nb_href,self::$class_name,self::$tags[1]);  
			 }else{ 
			 $naviga = sprintf('<span class="t_st_out">%s</span><span class="t_st_out">%s</span>',self::$tags[0],self::$tags[1]);
			 }
		 //获得下一页和最后一页
		 if($this->pagenum<self::$lastnum){
			 //判断是否为AJAX提交
			 $next_href =  self::$ajax==true ? '<a onclick=page_ajax_'.self::$ajax_out.'("'.self::UrlMerge($this->pagenum+1).'") >' : '<a href="'.self::UrlMerge($this->pagenum+1).'">';
			 $last_href =  self::$ajax==true ? '<a onclick=page_ajax_'.self::$ajax_out.'("'.self::UrlMerge(self::$lastnum).'") >' : '<a href="'.self::UrlMerge(self::$lastnum).'">';
			 //生成链接代码
			 $next = sprintf('%s<span class="%s">%s</span></a>%s<span class="%s">%s</span></a>',$next_href,self::$class_name,self::$tags[2],$last_href,self::$class_name,self::$tags[3]); 
			 }else{
			   $next = sprintf('<span class="t_st_out">%s</span><span class="t_st_out">%s</span>',self::$tags[2],self::$tags[3]);
			   }
	     //当页数大于1时，输出分页标签
		if(self::$lastnum>0){
		 $ajax_fun = self::$ajax==true ? self::Pajax() : '';//如果为AJAX分页时，输出jsAJAX函数
		 $other_style = array_search('<<',self::$tags) ? 'font-weight:bold' : '';
		 $totalrow = '<span class="t_st_out" style="color:#08c;border-color:#ccc">'.sprintf('%d/%d页&nbsp;共%d条记录',isset($this->pagenum)?$this->pagenum+1:1,self::$lastnum+1,self::$totalrow).'</span>';
		 return '<style>.t_st_in{border:1px solid #ccc;margin:2px;padding:5px 10px 5px 10px;font-size:14px;background:white;color:#666;text-align:center;'.$other_style.'}.t_st_in:hover{cursor:pointer;text-decoration:none;}.t_st_in:hover{border:1px solid #08c;color:#08c;}.just_st_index{border:1px solid #08c;background:#08c;color:#fff;margin:2px;padding:5px 10px 5px 10px;font-size:14px;font-weight:bold} a{text-decoration:none;}.t_st_out{border:1px solid #EBEBEB;margin:2px;padding:5px 10px 5px 10px;font-size:14px;color:#ccc;background:white;text-align:center;'.$other_style.'}</style>'. $naviga . $this->get_pages() .$next.$totalrow.$ajax_fun;
		  }
		 }    
  }
	 
?>
