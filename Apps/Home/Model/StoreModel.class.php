<?php

namespace Home\Model;

use Think\Model;

/**
 * [店铺的注册step1].
 *
 * @author xuzan<m13265000805@163.com>
 *
 * @param  [type]    描述参数作用
 *
 * @return [type] [description]
 */

class StoreModel extends Model
{
    // 更新时候
    protected $insertFields = array('store_name', 'store_type', 'store_describe', 'store_address', 'store_phone', 'store_realname',  'store_qq', 'store_email', 'store_due','store_caid');
    // 修改时候
    protected $updateFields = array('id','store_name', 'store_type', 'store_describe', 'store_address', 'store_phone',  'store_realname',  'store_qq', 'store_email', 'store_due','store_caid');
    protected $_validate = array(
        ['store_name', 'require', '店铺名不能为空', 1, 'regex', 3],
        ['store_name', '5,24', '店铺名称不能超过10位', 1, 'length', 3],
        ['store_type', 'number', '只能注册企业和个人店铺', 1, 'regex', 3],
        ['store_type', array(0,1), '只能注册企业和个人店铺', 1, 'in', 3],
        ['store_describe', '1,200', '店铺的描述不能超过200个字', 2, 'length', 3],
        ['store_address', 'require', '地址不能为空', 1, 'regex', 3],
        ['store_phone', '/^1[34578][0-9]\d{4,8}$/', '手机号码错误！', 1, 'regex', 3],
        ['store_realname', '/^[\x{4e00}-\x{9fa5}]+$/u', '请填写真实的姓名', 1, 'regex', 3],
        ['store_qq', '/^[1-9]\d{4,12}$/', '请填写正确的QQ格式', 2, 'regex', 3],
        ['store_caid', '18', '身份证格式不正确', 1, 'length', 3],
        ['store_email', '/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i', '请填写正确的邮箱格式', 2, 'regex', 3],
    );

    public function getStoreInfoData()
    {
        $storeid=session('store_id');
        $map['id']=['eq',$storeid];
        $storelist=$this->where($map)->find();
        $startDay=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endDay=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

        $publish=M('publish');
        $pubnum=$publish->where("pubh_shopid=$storeid and pubh_time > $startDay and pubh_time < $endDay ")->select();
        $storelist['pubhnum']=count($pubnum);
        $serviceuserid=$storelist['store_userid'];
        $sermap['order_serviceuserid']=['eq',$serviceuserid];
        $orderObj=M('store_order');
        $orderlist=$orderObj->field('order_number_total')->where("order_serviceuserid=$serviceuserid and order_time > $startDay and order_time < $endDay and order_status=4")->select();
        if($orderlist){
            foreach ($orderlist as $key => $value) {
                $ordernum+=$value['order_number_total'];
            }
        }else{
            $ordernum=0;
        }
        $storelist['ordernum']=$ordernum;
        $comment=M('employer_comment');
        $conentnum=$comment->where("user_id=$serviceuserid and addtime > $startDay and addtime < $endDay")->select();
        if($conentnum){
            $comnum=count($conentnum);
        }else{
            $comnum=0;
        }
        $storelist['comnum']=$comnum;
        return ['storeinfo'=>$storelist];

    }

    public function getObjData()
    {
        $storeid=I('get.storeid');
        $carouselObj=M('storeCarousel');
        $showpictureObj=M('storeShowPicture');
        $map['store_shopid']=['eq',$storeid];
        $carousel=$carouselObj->field('store_pic0,store_pic1,store_pic2')->where($map)->find();
        $showMap['shopid']=['eq',$storeid];
        $showpicture=$showpictureObj->field('store_pic0,store_pic1,store_pic2,store_pic3')->where($showMap)->find();
        $publishObj=M('publish');
        $serMap['pubh_shopid']=['eq',$storeid];
        $serMap['pubh_status']=['eq',2];
        $servicData=$publishObj->field('id,pubh_shopid,pubh_price,pubh_title,pubh_volume,pubh_pic')->where($serMap)->order('id desc')->limit(4)->select();
        $caseObj=M('store_case');
        $caseMap['case_shop']=['eq',$storeid];
        $caseData=$caseObj->field('app_store_case.id,app_store_case.case_shop,app_store_case.case_title,app_store_case.case_cover,app_store_order.order_number_price')->join('LEFT JOIN app_store_order on app_store_case.case_orderid = app_store_order.id')->where($caseMap)->select();
        return ['carouselData'=>$carousel,'showpicture'=>$showpicture,'servicData'=>$servicData,'caseData'=>$caseData];
    }
    /**
     * 店铺评价信息
     * @author bairen
     */
    public function assessInfo()
    {
        $storeid=I('get.storeid');
        $Model = M();
        $userid=$Model->query("select store_userid from app_store where id=$storeid");
        $userid=$userid[0]['store_userid'];
        $oid=$Model->query("select GROUP_CONCAT(id) as oid from app_store_order where order_serviceuserid=$userid and order_status=4");
        $oid=$oid[0]['oid'];//所有订单id
        // 分页
        import('@.Class.Page'); //引入Page类
        // 查询满足要求的总记录数
        if($oid){
            $count =$Model->query("select count(id) as pagenum from app_employer_comment where order_id in($oid)");
        }
        /*进行第三方分页类配置*/
        $count=$count[0]['pagenum'];
        $page = array(
            'total' => $count,/*总数（改）*/
            'url' => !empty($param['url']) ? $param['url'] : '',/*URL配置*/
            'max' => !empty($param['max']) ? $param['max'] : 10,/*每页显示多少条记录（改）*/
            'url_model' => 1,/*URL模式*/
            'ajax' =>  !empty($param['ajax']) ? true : false,/*开启ajax分页*/
            'out' =>  !empty($param['out']) ? $param['out'] : false,/*输出设置*/
            'url_suffix' => true,/*url后缀*/
            'tags' => array('首页','上一页','下一页','尾页'),
        );
        /*实例化第三方分页类库*/
        $page = new \Page($page);
        
        $offset=$page->pagerows();//
        $endpage=$page->maxrows();
        // $list = $this->where($map)->order('pubh_time desc')->limit($page->pagerows(),$page->maxrows())->select();
        
        if($oid){
            $assessinfo=$Model->query("select e.id,e.order_id,e.content,e.addtime,e.comment_gmb,u.user_account ,i.impr_picture,o.order_number_price*o.order_number_total as price from app_employer_comment as e left join app_user as u on e.user_id=u.user_id LEFT JOIN app_impuser as i on i.user_id=u.user_id LEFT JOIN app_store_order as o on o.id=e.order_id where  order_id in($oid) ORDER BY addtime desc limit $offset,$endpage");
        }
        // 显示分页
        $show=$page->get_page();
        return array('assess'=>$assessinfo,'show'=>$show);
    }
    // 添加之前
    protected function _before_insert(&$data, $option)
    {
        // 店铺的创建时间
        $data['store_addtime'] = time();
         $data['store_userid'] = session('home_user_info')['user_id'];

        // 多文件上传
        $rootPath = C('IMG_rootPath');
        $upload = new \Think\Upload(array(
         'rootPath' => $rootPath,
        ));// 实例化上传类
        $upload->maxSize = (int) C('IMG_maxSize') * 1024 * 1024;// 设置附件上传大小
        $upload->exts = C('IMG_exts');// 设置附件上传类型
        /// $upload->rootPath = $rootPath; // 设置附件上传根目录
        $upload->savePath = 'store/'; // 图片二级目录的名称
        // 上传文件
        $info = $upload->upload($_FILES);
        if (!$info) {
            // 上传错误提示错误信息
           $info = $upload->getError();
           
           return $info;
            
        }

        // 图片遍历
       foreach ($info as $key => $val) {
           $data[$key] = $val['savepath'].$val['savename'];
       }

    }
    /**
     * [短信验证]
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
      public function sms()
        {
            // 随机生成验证码
            $code = mt_rand(111111,999999);

            // 模板
            $tplOperator = new \Org\Util\sms\TplOperator();
            $result = $tplOperator->get_default(array("tpl_id"=>'2'));

            // 发送单条短信
            $smsOperator = new \Org\Util\sms\SmsOperator();
            $data['mobile'] = I('post.store_phone'); // 手机号码
            $data['text'] = '您的验证码是' . $code; // 发送短信内容
            $result = $smsOperator->single_send($data);
            $result = json_encode($result);
            $result = json_decode($result,true);

            if($result['success']){
                $res['status'] = true;
                $res['error_info'] = $result['responseData']['msg'];

                //发送短信成功就把验证码加密后存储到session
                session('sms_code',password_hash($code , PASSWORD_DEFAULT));

            }else{
                $res['status'] = false;
                $res['error_info'] = $result['responseData']['detail'];
            }

            return $res;
        }

     /**
     * [店铺更新]
     * @author xuzan<m13265000805@163.com>
     * @param  [type]    描述参数作用
     * @return [type] [description]
     */

     // 修改前
    public function updateStore()
    {
        $images = $this->field('file_image, store_zcaidpic, store_fcaidpic')->find( I('post.id' ) );
        deleteImage($images);


        // 店铺的创建时间
        $data['store_addtime'] = time();
        $data['store_userid'] = session('home_user_info')['user_id'];

        // 多文件上传
        $rootPath = C('IMG_rootPath');
        $upload = new \Think\Upload(array(
         'rootPath' => $rootPath,
        ));// 实例化上传类
        $upload->maxSize = (int) C('IMG_maxSize') * 1024 * 1024;// 设置附件上传大小
        $upload->exts = C('IMG_exts');// 设置附件上传类型
        /// $upload->rootPath = $rootPath; // 设置附件上传根目录
        $upload->savePath = 'store/'; // 图片二级目录的名称
        // 上传文件
        $info = $upload->upload($_FILES);
        // p($info);
        if (!$info) {
            // 上传错误提示错误信息
           $this->error = $upload->getError();
            exit;
        }

        // 图片遍历
       foreach ($info as $key => $val) {
           $data[$key] = $val['savepath'].$val['savename'];
       }
        $data = array_merge($_POST, $data);

        return $data;
    }

    /**
    * [判断是否是自己的店铺]
    * @author xuzan<m13265000805@163.com>
    * @param  [type]    描述参数作用
    * @return [type] [description]
    */
    public function employerUserId($service_id)
    {
        $model = M('publish');
        $map['a.id'] = ['eq', $service_id];
        // 找出商家id
        $listId = $model->alias('a')->join('LEFT JOIN app_store b ON a.pubh_shopid = b.id')->where($map)->getField('b.store_userid');
        // 判断雇主id是否和服务id相等
       if($listId){
           if(session('home_user_info')['user_id'] == $listId){
               return 1;
           }else{
               return 0;
           }
       }
    }


    /**
     * 服务商首页的所有店铺
     *@author bairen
     */
    public function storeIndex()
    {
        $store_sign=I('get.sign');//服务商类型
        $sort=I('sort','id');//成交数量，好评率，收入金额 ，的排序,默认id排序
        $asc=I('asc','asc');// 降序，或升序,默认升序

        if($store_sign !== ''){
            $map['store_type']=$store_sign;
        }

        $map['cate_status']=1;//状态 1:正常 0:禁用',
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
        // $list = $this->where($map)->limit($page->pagerows(),$page->maxrows())->select();
        // 分页查询
        if($sort == 'id'){
            $list = $this->cache(true,5)->field('id,store_userid,store_name,store_type,file_image,store_address,store_describe')->where($map)->order("$sort $asc")->limit($page->pagerows(),$page->maxrows())->select();
        }
        // 成交数量
        if($sort == 'sales_num'){
            // $map['']
            $list=$this->cache(true,5)->field('s.id,s.store_userid,s.store_name,s.store_type,s.file_image,s.store_address,s.store_describe,o.order_serviceuserid ,count(o.id) as bargain')->group('s.id')->alias('s')->join("LEFT JOIN app_store_order o on s.store_userid=o.order_serviceuserid and s.cate_status=1 and o.order_status=4")->where($map)->order("bargain $asc")->limit($page->pagerows(),$page->maxrows())->select();
        }
        // 收入金额
        if($sort == 'price'){
            $list=$this->cache(true,5)->field('s.id,s.store_userid,s.store_name,s.store_type,s.file_image,s.store_address,s.store_describe,o.order_serviceuserid ,sum(o.order_number_total*o.order_number_price) as money')->group('s.id')->alias('s')->join('LEFT JOIN app_store_order o on s.store_userid=o.order_serviceuserid and s.cate_status=1 and o.order_status=4')->where($map)->order("money $asc")->limit($page->pagerows(),$page->maxrows())->select();
        }
        // 好评率
        if($sort == 'assess'){
            $list = $this->cache(true,5)->field('id,store_userid,store_name,store_type,file_image,store_address,store_describe')->where($map)->limit($page->pagerows(),$page->maxrows())->select();
        }
        foreach($list as &$value) {
            $storelist = M('shop_category')->cache(true,5)->find($value['id']);
            $value['cate']=$storelist['cate_secondid'].','.$storelist['cate_threeid'];
        }
        foreach($list as &$value) {
            $map['id']  = array('in',$value['cate']);
            $category=M('store_category')->where($map)->select();
            $value['f_name']=$category;

            // 订单
            $price['order_serviceuserid']=$value['store_userid'];
            $price['order_status']=4;//交易状态：4：已付款',
            $order=M('store_order')->cache(true,5)->field('id,order_serviceuserid,order_time,group_concat(id) as oid,sum(order_number_price*order_number_total) as price')->where($price)->select();
            // $arr=M('store_order')->field('group_concat(id)')->where($price)->select();
             $value['price']=$order[0]['price'];//总收入金额
             $value['oid']=$order[0]['oid'];//店铺所有订单
            // 成交笔数
            $three = mktime(0,0,0,date('m')-3,1,date('y'));
            $now = time();
            $bargains['order_serviceuserid']=$value['store_userid'];
            $bargains['order_status']=4;//交易状态：4：已付款',
            $bargains['order_time']  = array('between',array($three,$now));
            $bargain=M('store_order')->cache(true,5)->field('id,count(order_number_total) as aaa')->where($bargains)->select();
            $value['bargain']=$bargain[0]['aaa'];
            $value['order_id']=$bargain[0]['id'];

            // 好评率
            if($order[0]['oid'] != ''){
                $ass['order_id']=array('in',$order[0]['oid']);
                $assess=M('employer_comment')->cache(true,5)->field('id,count(id) as comment')->where($ass)->select();
                // $value['com']=$assess;
                $value['tote']=$assess[0]['comment'];//总评价人数

                $arr['order_id']=array('in',$order[0]['oid']);
                $arr['comment_gmb']=0;
                $res=M('employer_comment')->cache(true,5)->field('count(comment_gmb) as good')->where($arr)->select();
                // $value['good']=$res[0]['good'];
                $value['comment']=round($res[0]['good']/$assess[0]['comment'],2)*100;

            }


        }

        // 得到分页
        $show = $page->get_page();
        return ['store_list' => $list, 'show' => $show];
    }
    /**
     * 服务商库 所有服务
     * @author bairen
     */
    public function serverAll()
    {   
        $cid=I('get.cate_id');//分类id
        $line=$this->showTitle($cid);
        $store_sign=I('get.sign');//服务商类型
        $sort=I('sort','id');//成交数量，好评率，收入金额 ，的排序,默认id排序
        $asc=I('asc','asc');// 降序，或升序,默认升序
        if($store_sign !== ''){
            $map['store_type']=$store_sign;
        }
        $map['cate_status']=1;//状态 1:正常 0:禁用'
        
        $obj=M('store_category')->field('cate_path')->where("id=$cid")->find();
        $res=substr_count($obj['cate_path'], ',');
        if($res == 1){
            $map['cate_firstid']=$cid;
        }else if($res == 2){
            $map['cate_secondid']=$cid;
        }else if($res == 3){
            $map['cate_threeid']=array('in',$cid);
        }


        import('@.Class.Page'); //引入Page类
        // 查询满足要求的总记录数
        // $count = $this->where($map)->count();
        $count = $this->field('s.id,s.store_userid,s.store_name,s.store_type,s.file_image,s.store_address,s.store_describe,c.id as cid')->alias('s')->join("LEFT JOIN app_shop_category as c on s.id=c.cate_shopid")->where($map)->count();
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
        if($sort == 'id'){
            $list=$this->field('s.id,s.store_userid,s.store_name,s.store_type,s.file_image,s.store_address,s.store_describe,c.id as cid')->alias('s')->join("LEFT JOIN app_shop_category as c on s.id=c.cate_shopid")->where($map)->order("$sort $asc")->limit($page->pagerows(),$page->maxrows())->select();
        }
        // 成交量
        if($sort == 'sales_num'){
            $list=$this->field('s.id,s.store_userid,s.store_name,s.store_type,s.file_image,s.store_address,s.store_describe,o.order_serviceuserid ,count(o.id) as bargain')->group('s.id')->alias('s')->join("LEFT JOIN app_shop_category as c on s.id=c.cate_shopid  LEFT JOIN app_store_order o on s.store_userid=o.order_serviceuserid and s.cate_status=1 and o.order_status=4")->where($map)->order("bargain $asc")->limit($page->pagerows(),$page->maxrows())->select();
        }

        // 收入金额
        if($sort == 'price'){
            $list=$this->field('s.id,s.store_userid,s.store_name,s.store_type,s.file_image,s.store_address,s.store_describe,o.order_serviceuserid ,sum(o.order_number_total*o.order_number_price) as money')->group('s.id')->alias('s')->join('LEFT JOIN app_shop_category as c on s.id=c.cate_shopid  LEFT JOIN  app_store_order o on s.store_userid=o.order_serviceuserid and s.cate_status=1 and o.order_status=4')->where($map)->order("money $asc")->limit($page->pagerows(),$page->maxrows())->select();

        }
        // 好评率（完善不够）
        if($sort == 'assess'){
            $list=$this->field('s.id,s.store_userid,s.store_name,s.store_type,s.file_image,s.store_address,s.store_describe,c.id as cid')->alias('s')->join("LEFT JOIN app_shop_category as c on s.id=c.cate_shopid")->where($map)->limit($page->pagerows(),$page->maxrows())->select();
        }
        foreach($list as &$value) {
            $storelist = M('shop_category')->cache(true,5)->find($value['id']);
            $value['cate']=$storelist['cate_secondid'].','.$storelist['cate_threeid'];
        }
        foreach($list as &$value) {
            $map['id']  = array('in',$value['cate']);
            $category=M('store_category')->where($map)->select();
            $value['f_name']=$category;

            // 订单
            $price['order_serviceuserid']=$value['store_userid'];
            $price['order_status']=4;//交易状态：4：已付款',
            $order=M('store_order')->cache(true,5)->field('id,order_serviceuserid,order_time,group_concat(id) as oid,sum(order_number_price*order_number_total) as price')->where($price)->select();
            // $arr=M('store_order')->field('group_concat(id)')->where($price)->select();
             $value['price']=$order[0]['price'];//总收入金额
             $value['oid']=$order[0]['oid'];//店铺所有订单
            // 成交笔数
            $three = mktime(0,0,0,date('m')-3,1,date('y'));  
            $now = time(); 
            $bargains['order_serviceuserid']=$value['store_userid'];
            $bargains['order_status']=4;//交易状态：4：已付款',
            $bargains['order_time']  = array('between',array($three,$now));
            $bargain=M('store_order')->cache(true,5)->field('id,count(order_number_total) as aaa')->where($bargains)->select();
            $value['bargain']=$bargain[0]['aaa'];
            $value['order_id']=$bargain[0]['id'];

            // 好评率
            if($order[0]['oid'] != ''){
                $ass['order_id']=array('in',$order[0]['oid']);
                $assess=M('employer_comment')->cache(true,5)->field('id,count(id) as comment')->where($ass)->select(); 
                // $value['com']=$assess;
                $value['tote']=$assess[0]['comment'];//总评价人数

                $arr['order_id']=array('in',$order[0]['oid']);
                $arr['comment_gmb']=0;
                $res=M('employer_comment')->cache(true,5)->field('count(comment_gmb) as good')->where($arr)->select(); 
                // $value['good']=$res[0]['good'];
                $value['comment']=round($res[0]['good']/$assess[0]['comment'],2)*100;

            }
        }
         // 得到分页
        $show = $page->get_page();
        return ['store_list' => $list, 'show' => $show,'line'=>$line];
    }


    //设置一个方法递归查询分类名称
    public function showTitle( $id )
    {
        $mapa['id'] = $id;

        //查询分类名称
        $res = M('store_category')->cache(true,60)->where($mapa)->field('id,parent_id,cate_name')->find();
        if($res){
            //调用本身继续查询
            $title.= $this->showTitle($res['parent_id']) . ' / ';
        }

        $title .= $res['cate_name'];

        return $title;
    }

    /**
     * [店铺首页 服务商档案资料]
     * @author bairen
     * @param  [type]    描述参数作用
     * @return [type] [description]
     */
    public function shopHome()
    {
        $storeid=I('storeid');
        $map['id']=$storeid;
        $shopHome=$this->where($map)->select();
        return ['shop'=>$shopHome];
    }
    public function getStoreCate()
    {
        $storeid=I('storeid');
        $map['cate_shopid']=['eq',$storeid];
        $shopCate=M('shopCategory');
        $cateThreeId=$shopCate->where($map)->getField('cate_threeid');
        $arrId=explode(',',$cateThreeId);
        $listName=[];
        $storeName=M('store_category');
        foreach ($arrId as $key => $value) {
            $mapp['id'] = ['eq',$value];
            $listName[$value] = $storeName->where($mapp)->getField('cate_name');
        }
        return $listName;
    }


    public function getStoreInfo($store_id)
    {
         $res = $this->where('id='.$store_id)->find();
         return $res;
    }

    /**
     * [评论的信息 和 收入]
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */

     public function comment()
     {
         $id= I('get.storeid');

         $map['id'] = ['eq', $id];
         $userid = $this->where($map)->getField('store_userid');
         if($userid){
             $model = M('EmployerComment');
             $map1['user_id'] =['eq', $userid];
            //  $model->where($map1)->field();
         }



     }
}
