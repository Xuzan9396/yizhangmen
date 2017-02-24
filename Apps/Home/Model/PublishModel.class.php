<?php
namespace Home\Model;
use Think\Model;
class PublishModel extends Model{
    /**
     * @author 胡金矿<1968346304@qq.com>
     * [dealCate 服务分类的处理]
     * @return [num] [返回标志]
     */
    public function dealCate()
    {
        $data['pubh_categoryid']=I('post.firstCate').','.I('post.secondCate').','.I('post.thirdCate');
         //获得用户id
        $user_id=session('home_user_info')['user_id'];
        $map['pubh_shopid']=session('store_id');
        $data['pubh_shopid']=session('store_id');
        $list=$this->where($map)->order('id desc')->find();
        if($list['pubh_carouselid']){
            $result=$this->add($data);
            if($result){
                session('home_service_id',$result);
                $info=2;
            }else{
                $info=0;
            }
        }else{
            $mapp['id']=['eq',$list['id']];
            p($data);
            $result=$this->where($mapp)->save($data);
            if($result === false){
                $info=1;
            }else{
                session('home_service_id',$list['id']);
                $info=3;
            }
        }
        return $info;
        
    }
    
    /**
     * @author 胡金矿<1968346304@qq.com>
     * [handler 发布服务信息的验证及文件上传]
     * @return [array] [处理信息]
     */
    public function handler()
    {
        $config=array(
            'maxSize'   =>     3145728 ,// 设置附件上传大小
            'exts'      =>     array('jpg','png','gif', 'jpeg'),// 设置附件上传类型
            'savePath'  => 'server/' // 设置附件上传目录
        );
        $rules = array(
            array('pubh_title','/^[\x{4e00}-\x{9fa5}\w]{3,30}$/u','标题必须为包含中文,数字,字母,下划线3~30个字',1,'regex'), //默认情况下用正则进行验证
            array('pubh_price','/^[\d]{1,8}(.[0-9]{1,2})?$/','价格必须为1~10位数字',1,'regex'), //
        );
        $arr = [];
        $imgaeType=['image/png','image/jpeg','image/gif','image/jpg'];
        $length=count($_FILES['file']['tmp_name']);
        for($i=0;$i<$length;$i++){
            if(in_array($type[$i]=getimagesize($_FILES['file']['tmp_name'][$i])['mime'],$imgaeType)){
                $arr[$i]=$type[$i];
            }
        }
        $allow_length=count($arr);
        if($allow_length==4){
            $upload = new \Think\Upload($config);// 实例化上传类
            $info= $upload->upload($_FILES);
            if(!$info){
                $result=$this->error($upolad()->getError());
                return $result;
            }else{
                //获得用户id
                $user_id=session('home_user_info')['user_id'];
                //获得店铺id
                $data['sere_shopid']=session('store_id');
                //获取服务id
                $data['sere_id']=session('home_service_id');
                foreach($info as $key=>$file){
                    $data['sere_pic'.$key]=$file['savepath'].$file['savename'];
                }
                $carousel=M('service_carousel');
                $insertid= $carousel->data($data)->add();//得到轮播图的id
                if($insertid){
                    $pubh_title=I("post.pubh_title");
                    $pubh_price=I("post.pubh_price");
                    $publish=M('publish');
                    $id=session('home_service_id');

                    $app['pubh_pic']=$info[0]['savepath'].$info[0]['savename'];
                    $app['pubh_carouselid']=$insertid;
                    $app['pubh_title']=$pubh_title;
                    $app['pubh_price']=$pubh_price;
                    $app['pubh_time']=time();
                    
                    if($res=$publish->validate($rules)->create($app,2)){
                        $publish->where('id='.$id)->save($app);
                    }else{
                        $result['msg']=$publish->getError();
                        $result['errornum']=1;
                        return $result;
                    }
                }else{
                    $result['msg']='轮播图上传失败,请重新上传';
                    $result['errornum']=2;
                    return $result;
                }
            }
            //处理服务详情
            $content=$_POST['content'];
            preg_match_all('/<img.*?src="(.*?)".*?>/is',$content,$array);
            $src='';
            foreach ($array[1] as $key => $value) {
                $src.=substr($value.',',20);
            }
            $detail=M('servicedetail');
            $contents['serl_pid']=session('home_service_id');
            $contents['serl_contents']=htmlspecialchars($_POST['content']);
            $contents['serl_pic']=$src;
            $res=$detail->add($contents);
            if(!$res){
                $result['msg']='服务详情在上传时出现未知错误,请重新上传';
                $result['errornum']=3;
                return $result;
            }
        }else{
            $result['msg']= '请上传4张合法的轮播图片';
            $result['errornum']=4;
            return $result;
        }
        $result['errornum']=0;
        return $result;
    }

    /**
     * @author 胡金矿<1968346304@qq.com>
     * [selectData 获取对应id服务的信息]
     * @return [type] [array]
     */
    public function selectData()
    {
        $id=isset($_GET['id']) ? $_GET['id']+0 : '';
        $map['id']=['eq',$id];
        $list=$this->where($map)->find();
        $detail=M('servicedetail');
        $mapp['serl_pid']=['eq',$id];
        $data=$detail->where($mapp)->find();
        return ['list'=>$list,'data'=>$data];
    }
    /**
     * @author 胡金矿<1968346304@qq.com>
     * [updataService 服务信息的更新操作]
     * @return [array] [返回处理信息及标志]
     */
    public function updataService()
    {
        $id=isset($_GET['id']) ? $_GET['id']+0 : '';
        $file=$_FILES;
        $map['id']=['eq',$id];
        $config=array(
            'maxSize'   =>     3145728 ,// 设置附件上传大小
            'exts'      =>     array('jpg','png','gif', 'jpeg'),// 设置附件上传类型
            'savePath'  => 'server/' // 设置附件上传目录
        );

        $rules = array(
            array('pubh_title','/^[\x{4e00}-\x{9fa5}\w]{3,30}$/u','标题必须为包含中文,数字,字母,下划线3~30个字',1,'regex',2), //默认情况下用正则进行验证
            array('pubh_price','/^[\d]{1,10}(.[0-9]{1,2})?$/','必须为1~10位数字',1,'regex',2), //
        );
        if(!$this->validate($rules)->create($_POST,2)){
            $result['msg']=$this->getError();
            $result['errornum']=10;
            return $result;
        }else{
            $app['pubh_title']=$_POST['pubh_title'];
            $app['pubh_price']=$_POST['pubh_price'];
            $saveRes=$this->where($map)->save($app);
            if($saveRes !== 0 && !$saveRes){
                $result['msg']='标题或价格更新失败,请重新填写';
                $result['errornum']=1;
                return $result;
            }
            $result['errornum']=0;
        }

        if(hasImage('file')){

            $flength=count($_FILES['file']['tmp_name']);
            $arr = [];
            if($flength){
                //允许上传的图片类型
                $imgaeType=['image/png','image/jpeg','image/gif','image/jpg'];
                for($i=0;$i<$flength;$i++){
                    if(in_array($type[$i]=getimagesize($_FILES['file']['tmp_name'][$i])['mime'],$imgaeType)){
                        $arr[$i]=$type[$i];
                    }else{
                        $result['msg']='含有非法上传文件类型';
                        $result['errornum']=2;
                        return $result;
                    }
                }
            }

        }
        $length=count($arr);
        //封面和轮播图都不为空,即为都更新
        if( ($file['pic']['size'][0] != 0) && ($length==4) ){
            echo '两个都要更新图片的区间';
            $upload = new \Think\Upload($config);// 实例化上传类
            $info= $upload->upload($_FILES);
            if(!$info){
                $result['msg']=$this->error($upolad()->getError());
                $result['errornum']=3;
                return $result;
            }else{
                //拼接轮播图的存储路径
                foreach($info as $key=>$file){
                    $data['sere_pic'.$key]=$file['savepath'].$file['savename'];
                }
               $serverData=$this->where($map)->field('pubh_carouselid,pubh_pic')->select();
               $carouselid=$serverData[0]['pubh_carouselid'];
               if($carouselid){
                    //获取之前上传轮播图的路径
                    $carouselMap['id']=['eq',$carouselid];
                    $carouselTab=M('ServiceCarousel');
                    $carouselData=$carouselTab->where($carouselMap)->select();
                    $carouselRes=$carouselTab->where($carouselMap)->save($data);
                    if($carouselRes){
                        $pic1=$carouselData[0]['sere_pic1'];
                        $pic2=$carouselData[0]['sere_pic2'];
                        $pic3=$carouselData[0]['sere_pic3'];
                        $pic4=$carouselData[0]['sere_pic4'];
                        @unlink('./Public/Uploads/'.$pic1);
                        @unlink('./Public/Uploads/'.$pic2);
                        @unlink('./Public/Uploads/'.$pic3);
                        @unlink('./Public/Uploads/'.$pic4);

                        $result['errornum']=0;
                    }else{
                        $result['msg']='出现未知错误,导致轮播图更新失败,请重新上传';
                        $result['errornum']=4;
                        return $result;
                    }

               }else{
                    $result['msg']='没有找到你以前上传的轮播图';
                    $result['errornum']=5;
                    return $result;
               }

            }
            $pubh['pubh_pic']=$info[0]['savepath'].$info[0]['savename'];//更新上传的路径
            $serverPic=$serverData[0]['pubh_pic'];//原有的路径
            $picRes=$this->where($map)->save($pubh);
            if($picRes){
                @unlink('./Public/Uploads/'.$serverPic);

            }else{

                $result['msg']='服务封面更新失败,请重新上传';
                $result['errornum']=6;
                return $result;
            }

        }else{
            //其他情况,不写入数据库
            $result['msg']='请上传1张服务封面图和4张服务轮播图';
            $result['errornum']=7;
            return $result;
        }

        $content=isset($_POST['content']) ? $_POST['content']: '';
        $serl=M('servicedetail');
        $serlMap['serl_pid']=['eq',$id];
        $serlPic=$serl->where($serlMap)->getField('serl_pic');//获取原有服务详情上传的图片路径
        if($content){
            preg_match_all('/<img.*?src="(.*?)".*?>/is',$content,$array);
            $src='';
            foreach ($array[1] as $key => $value) {
                $src.=substr($value.',',20);
            }
            $serlData['serl_pic']=rtrim($src,',');
            $serlData['serl_contents']=htmlspecialchars($content);
            $serlRes=$serl->where($serlMap)->save($serlData);
            if( $serlRes !== 0 && !$serlRes ){
                $result['msg']='由于未知原因导致服务详情更新失败';
                $result['errornum']=8;
                return $result;
            }else if($serlRes!==0){
                //删除原有服务详情上传的图片路径
                $serlNewPic=rtrim($serlPic,',');
                $arrPic=explode(',',$serlNewPic);
                foreach($arrPic as $val){
                     @unlink('./Public/editor/'.$val);
                }

                $result['errornum']=0;
            }

        }else{
            $result['msg']='服务详情不能为空';
            $result['errornum']=9;
            return $result;
        }

        return $result;
    }

    /**
     * @author 胡金矿<1968346304@qq.com>
     * [pastHandler 搜索查询条件及分页]
     * @return [array] [分页与查询结果]
     */
    public function pastHandler()
    {
        $start = strtotime(isset( $_GET['startTime'] ) ? $_GET['startTime']:'');
        $end = strtotime(isset( $_GET['endTime'] ) ? $_GET['endTime']:'');
        //交易状态
        $tradeMoney = isset($_GET['tradeMoney']) ? $_GET['tradeMoney']+0 : 3 ;
        if(in_array($tradeMoney,array(0,1,2,3))){
            if($tradeMoney==3){
               $map['pubh_status']=['in',array('0','1','2')];
            }else{
                $map['pubh_status']=['eq',$tradeMoney];
            }
        }else{
            $map['pubh_status']=['in',array('0','1','2')];
        }
        //交易时间条件
        if($start && $end){
            $map['pubh_time']=['between',array($start,$end)];
        }else if($start){
            $map['pubh_time'] = ['egt', $start];
        }else if($end){
            $map['pubh_time'] = ['elt', $end];

        }
        //搜索框条件
        $content=I('get.pubh_title');
        if($content!=''){
            $map['pubh_title']=['like','%'.$content.'%'];
        }
        $store_id=session('store_id');
        $map['pubh_shopid']=['eq',$store_id];
        $page=myPage($this,$map,3);
        $list = $this->where($map)->order('pubh_time desc')->limit($page->pagerows(),$page->maxrows())->select();
        $show=$page->get_page();
        $status = ['审核中', '驳回','审核通过'];
        foreach ($list as &$value) {
            $value['pubh_status'] = $status[$value['pubh_status']];
        }
        return ['list'=>$list,'show'=>$show];

    }
    /**
     * 胡金矿
     * [getOtherData description]
     * @return [type] [description]
     */
    public function getOtherData()
    {
        $storeid=I('storeid');
        $map['pubh_shopid']=['eq',$storeid];
        $OtherPubhlish=$this->where($map)->order('pubh_volume desc')->limit(2)->select();
        return $OtherPubhlish;
    }
    public function getServiceData()
    {
        $thirdId=I('get.id');
        $storeId=I('get.storeid');
        $map['id']=['eq',$thirdId];
        $store=M('storeCategory');
        $secondId=$store->where($map)->getField('parent_id');
        $mapp['id']=['eq',$secondId];
        $firstId=$store->where($mapp)->getField('parent_id');
        $cateIdString=$firstId.','.$secondId.','.$thirdId;
        $storeMap['pubh_shopid']=$storeId;
        $storeMap['pubh_categoryid']=$cateIdString;
        $storeMap['pubh_status']=2;
        $pubh=M('publish');
        $list=$pubh->where($storeMap)->select();
        return $list;

    }

    /**
     * @author 胡金矿<1968346304@qq.com>
     * [moreService 获取服务的分类及轮播图]
     * @return [array] [description]
     */
    public function moreService()
    {
        $id=I('get.id');
        $map['id']=['eq',$id];
        $data=$this->where($map)->find();
        /*******************/
        //获取店铺的名称
        $storeMap['id']=['eq',$data['pubh_shopid']];
        $store=M('store');
        $storeName=$store->where($storeMap)->getField('store_name');
        $data['storeName']=$storeName;
        /*******************/
        $arr=explode(',',$data['pubh_categoryid']);
        $cate=M('StoreCategory');
        //一级服务分类名
        $mapp['id']=['eq',$arr[0]];
        $data['firstname']=$cate->where($mapp)->getField('cate_name');
        //二级服务分类名
        $mapp['id']=['eq',$arr[1]];
        $data['secondname']=$cate->where($mapp)->getField('cate_name');
        //三级服务分类名
        $mapp['id']=['eq',$arr[2]];
        $data['thirdname']=$cate->where($mapp)->getField('cate_name');
        $style = ['待审核','未通过','通过审核'];
        $data['pubh_status'] = $style[$data['pubh_status']];

        $carouselid=$data['pubh_carouselid'];
        $carousel=M('ServiceCarousel');
        $maap['id']=['eq',$carouselid];
        $data['carousel']=$carousel->where($maap)->select();
        return $data;

    }
    public function getDescriptionData()
    {
        $id=I('get.id');
        $desc=M('servicedetail');
        $map['serl_pid']=['eq',$id];
        $list=$desc->where($map)->find();
        return $list;
    }
    /*
    *查询detail页信息,用于遍历
    *@author YeWeiBin
    *
     */
    public function getDetailInfo()
    {
        return $this->where('id='.I('get.sid',1))->find();
        // xuzan(服务轮播图);
    }

    /**
     * 所有服务
     * @author bairen
     */
    public function allServer()
    {
        $sort=I('sort','id')+0;//成交数量，好评率，收入金额 ，的排序,默认id排序
        $asc=I('asc','desc');// 降序，或升序,默认升序
        // $map['cate_status']=0;
        $map['pubh_status']=2;//服务发布成功
        // 实例化分页类
        import('@.Class.Page'); //引入Page类
        // 查询满足要求的总记录数
        $count = $this->where($map)->count();
        /*进行第三方分页类配置*/
        $page = array(
            'total' => $count,/*总数（改）*/
            'url' => !empty($param['url']) ? $param['url'] : '',/*URL配置*/
            'max' => !empty($param['max']) ? $param['max'] : 30,/*每页显示多少条记录（改）*/
            'url_model' => 1,/*URL模式*/
            'ajax' =>  !empty($param['ajax']) ? true : false,/*开启ajax分页*/
            'out' =>  !empty($param['out']) ? $param['out'] : false,/*输出设置*/
            'url_suffix' => true,/*url后缀*/
            'tags' => array('首页','上一页','下一页','尾页'),
        );
        /*实例化第三方分页类库*/
        $page = new \Page($page);
        // 默认
        if($sort == 'id'){
            $list=$this->cache(true,5)->field('p.*,s.store_name,s.store_realname')->alias('p')->join('LEFT JOIN app_store as s on p.pubh_shopid=s.id')->where($map)->order("p.pubh_volume $asc ,p.pubh_time $asc")->limit($page->pagerows(),$page->maxrows())->select();
        }
        // 成交量
        if($sort == 'sales_num'){
            $list=$this->cache(true,5)->field('p.*,s.store_name,s.store_realname')->alias('p')->join('LEFT JOIN app_store as s on p.pubh_shopid=s.id')->where($map)->order("p.pubh_volume $asc")->limit($page->pagerows(),$page->maxrows())->select();
        }
        // 价格
        if($sort == 'price'){
            $list=$this->cache(true,5)->field('p.*,s.store_name,s.store_realname')->alias('p')->join('LEFT JOIN app_store as s on p.pubh_shopid=s.id')->where($map)->order("p.pubh_price $asc")->limit($page->pagerows(),$page->maxrows())->select();
        }
        // 好评率
        if($sort == 'assess'){
            $list=$this->cache(true,5)->field('p.*,s.store_name,s.store_realname')->alias('p')->join('LEFT JOIN app_store as s on p.pubh_shopid=s.id')->where($map)->order("p.pubh_volume $asc ,p.pubh_time $asc")->limit($page->pagerows(),$page->maxrows())->select();
        }
        foreach($list as &$value){
            $price['order_status']=4;//交易状态：4：已付款',
            $price['order_serviceid']=$value['id'];
            $order=M('store_order')->cache(true,5)->field('group_concat(id) as oid')->where($price)->select();
            $value['oid']=$order[0]['oid'];//订单id
            if($order[0]['oid'] != ''){
                $ass['order_id']=array('in',$order[0]['oid']);
                $assess=M('employer_comment')->cache(true,5)->where($ass)->count();//评价总条数
                $value['num']=$assess; 

                $arr['order_id']=array('in',$order[0]['oid']);
                $arr['comment_gmb']=0;
                $res=M('employer_comment')->cache(true,5)->cache(true,5)->field('count(comment_gmb) as good')->where($arr)->select(); //好评
                // $value['good']=$res[0]['good'];
                if($assess){
                    $value['comment']=round($res[0]['good']/$assess,2)*100;//好评率

                    $map1['order_id'] = ['in', $order[0]['oid']];
                    $comment_number =M('employer_comment')->cache(true,5)->where($map1)->field("sum(star_service_attitude) a,sum(star_work_speed) b, sum(star_finish_quality) c")->find();
                    $res=($comment_number['a']/$assess) +($comment_number['b']/$assess) + ($comment_number['c']/$assess);
                    $value['assess']=round($res/$assess/3,2);//综合评价
                }   
            }
            
        }
        // 得到分页
        $show = $page->get_page();
        return ['server' => $list, 'show' => $show];
        }

        /**
         * 分类显示的服务页面
         * @author bairen
         */
        public function allCateServer()
        {
            $cid=I('get.cate_id')+0;//分类id
            $map['p.pubh_status']=2;//服务发布成功
            $sort=I('sort','id');//成交数量，好评率，收入金额 ，的排序,默认id排序
            $asc=I('asc','asc');// 降序，或升序,默认升序
            $map['_string']="FIND_IN_SET($cid, p.pubh_categoryid)";
            // 实例化分页类
            import('@.Class.Page'); //引入Page类
            // 查询满足要求的总记录数
            $count = $list=$this->field('p.*,s.id as storeid,s.store_name,s.store_realname')->alias('p')->join('LEFT JOIN app_store as s on p.pubh_shopid=s.id')->where($map)->order("p.pubh_volume $asc ,p.pubh_time $asc")->count();
            /*进行第三方分页类配置*/
            $page = array(
                'total' => $count,/*总数（改）*/
                'url' => !empty($param['url']) ? $param['url'] : '',/*URL配置*/
                'max' => !empty($param['max']) ? $param['max'] : 30,/*每页显示多少条记录（改）*/
                'url_model' => 1,/*URL模式*/
                'ajax' =>  !empty($param['ajax']) ? true : false,/*开启ajax分页*/
                'out' =>  !empty($param['out']) ? $param['out'] : false,/*输出设置*/
                'url_suffix' => true,/*url后缀*/
                'tags' => array('首页','上一页','下一页','尾页'),
            );
            /*实例化第三方分页类库*/
            $page = new \Page($page);
            // 默认
            if($sort == 'id'){
                $list=$this->field('p.*,s.id as storeid,s.store_name,s.store_realname')->alias('p')->join('LEFT JOIN app_store as s on p.pubh_shopid=s.id')->where($map)->order("p.pubh_volume $asc ,p.pubh_time $asc")->limit($page->pagerows(),$page->maxrows())->select();
            }
            // 成交量
            if($sort == 'sales_num'){
                $list=$this->field('p.*,s.id as storeid,s.store_name,s.store_realname')->alias('p')->join('LEFT JOIN app_store as s on p.pubh_shopid=s.id')->where($map)->order("p.pubh_volume $asc")->limit($page->pagerows(),$page->maxrows())->select();

            }
            if($sort == 'price'){
                $list=$this->field('p.*,s.id as storeid,s.store_name,s.store_realname')->alias('p')->join('LEFT JOIN app_store as s on p.pubh_shopid=s.id')->where($map)->order("pubh_price $asc")->limit($page->pagerows(),$page->maxrows())->select();

            }
            if($sort == 'assess'){
                $list=$this->field('p.*,s.id as storeid,s.store_name,s.store_realname')->alias('p')->join('LEFT JOIN app_store as s on p.pubh_shopid=s.id')->where($map)->order("p.pubh_volume $asc ,p.pubh_time $asc")->limit($page->pagerows(),$page->maxrows())->select();
            }
            foreach($list as &$value){
                $price['order_status']=4;//交易状态：4：已付款',
                $price['order_serviceid']=$value['id'];
                $order=M('store_order')->cache(true,5)->field('group_concat(id) as oid')->where($price)->select();
                $value['oid']=$order[0]['oid'];//订单id
                if($order[0]['oid'] != ''){
                    $ass['order_id']=array('in',$order[0]['oid']);
                    $assess=M('employer_comment')->cache(true,5)->where($ass)->count();//评价总条数
                    $value['num']=$assess; 

                    $arr['order_id']=array('in',$order[0]['oid']);
                    $arr['comment_gmb']=0;
                    $res=M('employer_comment')->cache(true,5)->cache(true,5)->field('count(comment_gmb) as good')->where($arr)->select(); //好评
                    // $value['good']=$res[0]['good'];
                    if($assess){
                        $value['comment']=round($res[0]['good']/$assess,2)*100;//好评率

                        $map1['order_id'] = ['in', $order[0]['oid']];
                        $comment_number =M('employer_comment')->cache(true,5)->where($map1)->field("sum(star_service_attitude) a,sum(star_work_speed) b, sum(star_finish_quality) c")->find();
                        $res=($comment_number['a']/$assess) +($comment_number['b']/$assess) + ($comment_number['c']/$assess);
                        $value['assess']=round($res/$assess,2);//综合评价
                    }   
                }  
            }
            // 得到分页
            $show = $page->get_page();
            return ['cate_server' => $list, 'show' => $show];

        }
        
        public function homeShowPublish()
        {
            return $this->order('pubh_volume desc')->limit(6)->select();
        }

        /**
         * [每条服务轮播图]
         *
         * @author xuzan<m13265000805@163.com>
         *
         * @param  [type]    描述参数作用
         *
         * @return [array] [4张轮播图]
         */
         public function seviceCarousel()
         {
             $model = M('ServiceCarousel');
             $sid = I('get.sid');
            $carousel = $model->field('sere_pic1,sere_pic2,sere_pic3,sere_pic4')->where('sere_id='.$sid)->find();

            foreach($carousel as $key=>&$val){
                $val = '/shop/Public/Uploads/'.$val;
                
            }
            return $carousel;
         }
    }
