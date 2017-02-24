<?php

namespace Home\Controller;

/**
 * 服务库 的 控制器.
 */
class ServiceController extends SmsController
{
    public function publicStoreNav()
    {
        $store = D('Store');
        $data = $store->shopHome();
        $cateList=$store->getStoreCate();
        $this->assign('cate',$cateList);

        $shopId = I('get.storeid');
         // 这家店铺所有的评论
        $modelShop = M('Store');

        $getUserId = $modelShop->where(array('id'=> array('eq', $shopId)))->getField('store_userid');
        $pp = M('StoreOrder');
        // 拿到所有店铺订单id
        $ordId = $pp->field("GROUP_CONCAT(id) order_id")->where(array('order_serviceuserid'=>array('eq', $getUserId)))->find();

        // 多少比交易
        $biTotal['bi'] = $pp->where(array(
            'order_serviceuserid'=>array('eq',$getUserId),
            'order_status'=> array('eq', 4)
        ))->count();
        $this->assign($biTotal);
        // 三个月
        $three = mktime(0,0,0,date('m')-3,1,date('y'));
        $now = time();
        $bargains['order_time']  = array('between',array($three,$now));
        // 查看所有评论分数
        if($ordId['order_id'] != ''){
            $EmployerComment = M('EmployerComment');
            $shopComment = $EmployerComment->field('sum(star_service_attitude) att, sum(star_work_speed) speed, sum(star_finish_quality) qua')->where(array('order_id' =>array('in' , $ordId['order_id'])))->find();
            //三个月收入金额
            $price=$pp->field('sum(order_number_total*order_number_price) as price')->where(array(
            'order_serviceuserid'=>array('eq',$getUserId),
            'order_status'=> array('eq', 4),
            'order_time'=>array('between',array($three,$now))
            ))->select();
            $price=$price[0]['price'];
            // 总条数
            $num = $EmployerComment->where(array('order_id' =>array('in' , $ordId['order_id']))) ->count();
            $value = round($shopComment['att']/$num , 2);
            $value2 = round($shopComment['speed']/$num , 2);
            $value3 = round($shopComment['qua']/$num , 2);

             $arrComment['att'] =  number_format($value,2,'.','');
             $arrComment['speed'] =  number_format($value2,2,'.','');
             $arrComment['qua'] =  number_format($value3,2,'.','');
             $arrComment['price'] =  number_format($price,2,'.','');

        }else{
            $arrComment['att'] =  0.00;
            $arrComment['speed'] =  0.00;
            $arrComment['qua'] = 0.00;
            $arrComment['price'] =0.00;

        }
       $this->assign($arrComment);
        $this->assign($data);
    }
    /**
     ** 服务商库首页 （所有店铺）.
     *
     *@author bairen
     */
    public function index()
    {
        // 分类
        $category = M('store_category');
        $cate = $category->where('parent_id = 0')->select();

        $Store = D('Store');
        $data = $Store->storeIndex();
        // 分配分类数据
        $this->assign('category', $cate);
        $this->assign($data);
        $this->display();
    }
    /**
     * 所有服务
     *
     *@author bairen
     */
    public function server()
    {
        // 分类
        $category = M('store_category');
        $cate = $category->where('parent_id = 0')->select();

        $publish = D('Publish');
        $data = $publish->allServer();

        // 分配分类数据
        $this->assign('category', $cate);
        $this->assign($data);
        $this->display();
    }

    /**
     * 分类显示的页面.
     *
     *@author bairen
     */
    public function category()
    {
        $cid=I('get.cate_id')+0;//分类id
        // 如果没有值，跳转到服务商库首页
        if($cid == ''){
            $this->redirect('Service/index');
        }
        $map['parent_id']=$cid;
        $category = M('store_category');
        $cate = $category->where($map)->select();
        $parent=$category->where("id= $cid")->select();


        $Store = D('Store');
        $serverall=$Store->serverAll();//所有服务
        // 分配数据
        $this->assign('category',$cate);//子分类
        $this->assign('parent',$parent[0]);//本身名称
        $this->assign($serverall);
        $this->display();
    }

    /**
     * 分类显示 服务页面
     * @author bairen
     */
    public function cateServer()
    {
        $cid=I('get.cate_id')+0;//分类id
        // 如果没有值，跳转到服务商库首页
        if($cid == ''){
            $this->redirect('Service/index');
        }
        $map['parent_id']=$cid;
        $category = M('store_category');
        $cate = $category->where($map)->select();
        $parent=$category->where("id= $cid")->select();

        $publish = D('Publish');
        $data = $publish->allCateServer();//分类 服务 方法

        // 分配数据
        $this->assign('category',$cate);//子分类
        $this->assign('parent',$parent[0]);//本身名称
        $this->assign($data);
        $this->display();
    }

    /**
     * 店铺首页.
     *
     *@author bairen
     */
    public function store()
    {
        $this->publicStoreNav();
        $storeObj=D('store');
        $storeManyData=$storeObj->getObjData();
        $assess=$storeObj->assessInfo();//评价信息
        $this->assign($storeManyData);
        $this->assign($assess);//分配评价信息
        $this->display();
    }
     /**
     * 服务详情.
     *
     *@author YeWeiBin
     */
    public function detail()
    {

        $this->publicStoreNav();
        $shopSid = I('get.sid');
         // 这家店铺所有的评论
        // $modelShop = M('Store');

        // $getUserId = $modelShop->where(array('id'=> array('eq', $shopId)))->getField('store_userid');
        $pp = M('StoreOrder');
        // 拿到所有店铺订单id
        $ordId = $pp->field("GROUP_CONCAT(id) order_id")->where(array('order_serviceid'=>array('eq', $shopSid)))->find();

        // 成交件数

        $sid = I('get.sid');
        $shopId = I('get.storeid');
        $user_info = $_SESSION['home_user_info'] ? $_SESSION['home_user_info'] : '123';
        $publish = D('Publish');
        $storeOther=$publish->getOtherData();
        $detail_info = $publish->getDetailInfo();
        // 服务描述
        $detailModel = M('Servicedetail');
        $detail_describe['describe'] = $detailModel->where(array('serl_pid'=>array('eq', $sid)))->find();

        $this->assign($detail_describe);


        // 评价表
        $model = M('EmployerComment');
        // xuzan(评价总条数)
        if($ordId['order_id'] != ''){
            $number = $model->where(array('order_id' =>array('in', $ordId['order_id'] )))->count();
            // 好评率个数
            $order_goods_number = $model->where(array(
                'order_id' => array('in', $ordId['order_id']),
                'comment_gmb' =>array('eq',0)
            ))->count();
        }
        if(!$number) $number = 0;
        // 分配每条服务的轮播图
        $model1 = D('Publish');
        $carousel['carousel'] = $model1-> seviceCarousel();
        $this->assign($carousel);

        $store = D('Store');
        $shop_id = $detail_info['pubh_shopid'];
        $shop_info = $store->getStoreInfo($shop_id);

        // 评论的分数
        if($number != 0 && $ordId['order_id']){
            $map1['order_id'] = ['in', $ordId['order_id']];
            $comment_number = $model->where($map1)->field("sum(star_service_attitude) a,sum(star_work_speed) b, sum(star_finish_quality) c")->find();

            // 查询好评率 满意度
            $map2 = array(
               'order_id'=>array('in', $ordId['order_id']),
               'star_service_attitude'=>array('egt', 3),
            );
            $map3 = array(
               'order_id'=>array('in', $ordId['order_id']),
               'star_work_speed'=>array('egt', 3),
            );
            $map4 = array(
               'order_id'=>array('in', $ordId['order_id']),
               'star_finish_quality'=>array('egt', 3),
            );

            $goods[] = ($model->where($map2)->count())/$number;
            $goods[] = ($model->where($map3)->count())/$number;
            $goods[] = ($model->where($map4)->count())/$number;

            // 3个平分数
            foreach ($comment_number as $key => &$value) {
               $value = round($value/$number, 2);
              $value =  number_format($value,2,'.','');
           }

           $num = 0;
           foreach($goods as $val)
           {
               $num +=$val;
           }
           $comment1  = round(($num / 3)*100 ,2);
           // 保留两位小数
           $comment_number['goods'] =  number_format($comment1,2,'.','').'%';

           $comment2 = round($order_goods_number/ $number, 2);
           $comment_number['order_goods'] =  number_format($comment2*100,2,'.','').'%';

       }else{
               $comment_number =array('a'=>3.00,'b'=>3.00,'c'=>3.00);
               foreach ($comment_number as $key => &$value) {
                    $value =  number_format($value,2,'.','');
               }
               $comment_number['goods'] = '0%';
               $comment_number['order_goods'] = '0%';
           }
        $this->assign('otherData',$storeOther);
        $this->assign('comment', $comment_number);
        $empUserId = $store->employerUserId($sid);
        $this->assign('empuser_sign', $empUserId);
        $this->assign('user_info', $user_info);
        $this->assign('detail_info', $detail_info);
        $this->assign('shop_info', $shop_info);
        // 评论数
        $this->assign('number', $number);
        $this->display();
    }

    /**
     * 交易评价.
     */
    public function assess()
    {
        $this->publicStoreNav();
        $this->display();
    }
    /**
     * 案例展示.
     */
    public function example()
    {
        $this->publicStoreNav();
        $case=D('store_case');
        $caseData=$case->getCaseData();
        $this->assign('case',$caseData);
        $this->display();
    }
    /**
     *  服务商档案资料.
     */
    public function salerinfo()
    {
        $this->publicStoreNav();
        $this->display();
    }
    public function shop()
    {
        $this->display();
    }
    /**
     * [订单列表].
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
    public function order()
    {
        $model = D('StoreOrder');
        // 服务商订单信息
        $list = $model->orderSelect1();
        $this->assign($list);
        $this->display();
    }
    /**
     * [ajax请求详情订单信息].
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
    public function order1()
    {
        $model = D('storeOrder');
        $list = $model->orderSelect2();
        echo json_encode($list);
    }
    /**
     * [店铺注册step1].
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
    public function storeRegister1()
    {
        $sign = $this->commonStore();
        if ($sign[0] == 0) {
            $sign['title'] = $sign[1];
            $this->assign('data', $sign);
            $this->display();
        } elseif ($sign[0] == 1) {
            // 修改店铺
            $model = D('store');
            $store_id = session('store_id');
            $storeList = $model->where()->find($store_id);
            $storeList['title'] = $sign[1];
            $this->assign('data', $storeList);
            $this->display();
        }
    }
    /**
     * [店铺数据的添加方法].
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
    public function storeRegister1Add()
    {
        if (I('post.id')) {
    
            // 执行更新
            if (IS_POST) {
                // 设置脚本的运行时间
                set_time_limit(0);
                $store = I('post.');
                $time = I('post.store_due');
                $_POST['store_due'] = strtotime($time);

                $model = D('Store');

                if ($model->create($data, 2)) {
                    $data = $model->updateStore();
                    $bool = $model->save($data);
                    $model1 = M('User');
                    $sessionId = session('home_user_info')['user_id'];
                    $model1->where(array('user_id' => array('eq', $sessionId)))->setField('user_type', 1);
                    $this->redirect('home/service/storeRegister2');
                }
                $this->error($model->getError());
            }
        } else {
            // 执行添加
            if (IS_POST) {
                // 设置脚本的运行时间
                set_time_limit(0);
                $store = I('post.');
                $time = I('post.store_due');
                $_POST['store_due'] = strtotime($time);

                $model = D('Store');
                if ($model->create(I('post.'), 1)) {
                    if ( $model->add()) {
                        // 添加成功后把店铺id存到session中

                        $sessionId = session('home_user_info')['user_id'];

                        $map['store_userid'] = $sessionId;
                        $list = $model->where($map)->getField('id');
                        // 把店铺id存入session中
                        session('store_id', $list);
                        $this->redirect('home/service/storeRegister2');
                        exit;
                    }
                }
                $this->error($model->getError());
            }
        }
    }
     /**
      * [短信接口验证].
      *
      * @author xuzan<m13265000805@163.com>
      *
      * @param  [type]    描述参数作用
      *
      * @return [array] [返回验证码和手机号码]
      */
     public function sms()
     {
         $this->loadConfig();

         $user = D('Store');

         //调用Model的sms方法，得到短信验证状态 return true/false
         $data = $user->sms();

         $this->ajaxReturn($data);
     }
    /**
     * [短信ajax验证step2]
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
     public function  codeRegester2()
     {
         $session = session('sms_code');
         $code2 = I('post.code2');
         $bool = password_verify($code2, $session);
         if($bool){
             session('sms_code',null);
             echo 1;
         }else{
             echo 2;
         }
     }

     /**
      * [短信ajax验证step3]
      *
      * @author xuzan<m13265000805@163.com>
      *
      * @param  [type]    描述参数作用
      *
      * @return [type] [description]
      */
      public function  codeRegester4()
      {
          $session = session('sms_code');
          $code3 = I('post.code3');
          $phone3 = I('post.phone3');
          $bool = password_verify($code3, $session);
          if($bool){
                $model = M('Store');
                $shop_session = session('store_id');
                $map['id'] = ['eq', $shop_session];
               $bool2 =  $model->where($map)->setField('store_phone', $phone3);
               if($bool2){
                   session('sms_code', null);
                   echo 1;
               }else{
                   echo 3;
               }
          }else{
              echo 2;
          }
      }

      /**
       * [短信ajax验证添加手机号码]
       *
       * @author xuzan<m13265000805@163.com>
       *
       * @param  [type]    描述参数作用
       *
       * @return [type] [description]
       */
       public function  codeRegester5()
       {
           $session = session('sms_code');
           $code3 = I('post.code3');
           $phone3 = I('post.phone3');
           $bool = password_verify($code3, $session);
           if($bool){
                 setcookie('storePhone', $phone3, time() +86400, '/' );
                 session('sms_code', null);
                 echo 1;
           }else{
               echo 2;
           }
       }
       public function shanchu()
       {
           setcookie('storePhone', '13233334444',time() - 1, '/');;
           echo 11111;
       }
     /**
      * [判断手机号码是否存在]
      *
      * @author xuzan<m13265000805@163.com>
      *
      * @param  [type]    描述参数作用
      *
      * @return [type] [description]
      */

      public function codeRegester3()
      {
          $phone3 = I('post.phone3');

          $model = M('Store');
          $bool = $model->where('store_phone='.$phone3)->getField('store_phone');
          if(!$bool){
              echo json_encode(1);
          }else{
              echo json_encode(2);
          }
      }


    /**
     * [店铺的注册的分类step2].
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
    public function storeRegister2()
    {
        $arrCate = D('StoreCategory');
        $model = D('ShopCategory');
        $map['parent_id'] = ['eq', 0];
        $arrList = $arrCate->where($map)->select();
        $listSign = $model->shopCateId();
        // echo $listSign;
        $this->assign('sign', $listSign);
        $this->assign('data', $arrList);
        $this->display();
    }

    /**
     * [店铺的所属分类添加].
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
    public function storecateAdd()
    {
        $model = D('ShopCategory');
        $sign = $model->shopCateId();
        if (!I('post.id')) {
            if (IS_POST) {
                $list = I('post.');
                $list['cate_firstid'] = I('post.firstCate');
                $list['cate_secondid'] = I('post.firstCate2');
                $list['cate_threeid'] = implode(',', I('post.firstCate3'));
                if ($model->create($list, 1)) {
                    if ($id = $model->add()) {
                        $this->redirect('home/service/publishServiceOne');
                    }
                }
                $this->error($model->getError());
            }
        } else {
            if (IS_POST) {
                $list = I('post.');
                $list['cate_firstid'] = I('post.firstCate');
                $list['cate_secondid'] = I('post.firstCate2');
                $list['cate_threeid'] = implode(',', I('post.firstCate3'));
                if ($model->create($list, 2)) {
                    if ($id = $model->save()) {
                        $this->redirect('home/service/publishServiceOne');
                    }
                }
                $this->error($model->getError());
            }
        }
    }
   /**
    * [店铺的注册的分类ajax].
    *
    * @author xuzan<m13265000805@163.com>
    *
    * @param  [type]    描述参数作用
    *
    * @return [type] [description]
    */
   public function storeRegisterAjax2()
   {
       $arrCate = M('StoreCategory');
        // 传过来的数值进行判断，等于空不让查询语句执行
        if (I('post.id') != '') {
            $map['parent_id'] = ['eq', I('post.id')];
            $arrList = $arrCate->where($map)->select();
            $this->ajaxReturn($arrList);
        } else {
            $this->ajaxReturn('');
        }
   }

    /**
     * [店铺的的案例数据分配].
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
    public function storeManagement()
    {
        // 成功案例
        $management = D('StoreOrder');
        $list = $management->orderSelect();
        $this->assign($list);
        $this->display();
    }

    /**
     * [店铺的案例方法].
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [bool] [是否插入成功]
     */
    public function storeCase()
    {
        $model = D('storeCase');
        if (!I('post.id')) {
            //执行添加
            if (IS_POST) {
                if ($model->create(I('post.'), 1)) {
                    $bool = $model->add();
                    if ($bool) {
                        // $this->success('添加成功！', U('lst?p='.I('get.p')));
                        exit;
                    }
                }
                $this->error($model->getError());
            }
        } else {
            // 执行更新
            if (IS_POST) {
                // P(I('post.'));exit;
                if ($model->create(I('post.'), 2)) {
                    $bool = $model->save();
                    if ($bool) {
                        // $this->success('添加成功！', U('lst?p='.I('get.p')));
                        exit;
                    }
                }
                $this->error($model->getError());
            }
        }
        $data = $model->caseSelect();
        $this->order_case = $data;
        $this->assign('data', $data);
        $this->display();
    }


    /**
     * [服务商前台管理 ->交易管理->评价管理]
     * @author bairen
     */
    public function storeComment()
    {
      $order = D('storeOrder');
      $data=$order->commentManage();//评价管理
      $fromHirer=$order->fromHirer();//来自雇主的评价
      $reply=$order->effect();//我对雇主的印象
      $this->assign($data);
      $this->assign($fromHirer);
      $this->assign($reply);
      $this->display();
    }
    /**
     * [回复雇主]
     */
    public function storeReyle()
    {
      $model=M('serviceer_comment');
      $assess=M('employer_comment');
      if(IS_GET){
        $id=I('get.id');//雇主评价id
        $aaa['id']=$id;
        $res=$assess->where($aaa)->find();
        if(empty($res)){
          $this->error('非法操作');
        }else{
          $status=$model->where('assess_id='.$id)->find();
          if($status){
              $this->error('你已经回复过了');
          }else{
            $this->assign('id',$id);
          }
        }
         $this->display();

         
      }
      
      
      if(IS_POST){
        $map['assess_id']=I('post.id');
        $map['star_comment']=I('post.comment');
        $map['content']=I('post.content');
        $map['add_time']=time();
        $res=$model->add($map);
        if($res){
          $this->success('回复成功', 'storeComment');
        }else{
          $this->success('回复失败', 'storeComment');
        }
      }
     
    }
     /**
      * [订单的回收站].
      *
      * @author xuzan<m13265000805@163.com>
      *
      * @param  [type]    描述参数作用
      *
      * @return [type] [description]
      */
     public function storeRecycle()
     {
         $model = D('StoreOrder');
         $data = $model->recycle();
         $this->assign($data);
         $this->display();
     }
      /**
       * [订单加入回收站].
       *
       * @author xuzan<m13265000805@163.com>
       *
       * @param  [type]    描述参数作用
       *
       * @return [type] [description]
       */
      public function storeRecycleAdd()
      {
          $id = I('post.id');
          $model = M('StoreOrder');
          $setField = $model->where('id = '.$id)->setField('order_isdelete', 1);
          if ($setField) {
              echo json_encode(1);
          }
      }

      /**
       * [订单从回收站还原].
       *
       * @author xuzan<m13265000805@163.com>
       *
       * @param  [type]    描述参数作用
       *
       * @return [type] [description]
       */
      public function storeRecycleAdd1()
      {
          $id = I('post.id');
          $model = M('StoreOrder');
          $setField = $model->where('id = '.$id)->setField('order_isdelete', 0);
          if ($setField) {
              echo json_encode(1);
          }
      }

      /**
       * [订单真正删除]
       *
       * @author xuzan<m13265000805@163.com>
       *
       * @param  [type]    描述参数作用
       *
       * @return [type] [description]
       */
       public function orderDelete()
       {
           $id = I('get.id');
           $model = M('StoreOrder');
           $setField = $model->delete($id);
           if ($setField) {
               echo json_encode(1);
           }
       }
     /**
       * [雇主对服务某条服务的评论]
       *
       * @author xuzan<m13265000805@163.com>
       *
       * @param  [type]    描述参数作用
       *
       * @return [obj] [json]
       */
       public function ajaxEemployerComment()
       {
           $serviceId = I('get.id');
           // **************
           //  先查订单表对应的服务订单
           $pp = M('StoreOrder');
           $group = $pp->field("GROUP_CONCAT(id) order_id")->where(array('order_serviceid'=>array('eq', $serviceId)))->find();
           if($group['order_id'] != ''){
           // **************'
            // 每页显示条数
           $perpage = 5;
           $p = I('get.p');
           $offset = ($p - 1) * $perpage;
           $comment = M('EmployerComment');
           $data = $comment->field('a.*,a.star_service_attitude attr,a.star_work_speed speed,a.star_finish_quality qua,b.user_account,c.impr_picture')->alias('a')->join('left join app_user b on a.user_id=b.user_id left join app_impuser c on a.user_id=c.user_id')->where(array('a.order_id'=>array('in', $group['order_id'])))->limit("$offset, $perpage")->order('a.id desc')->select();

            // 评价信息返回

            foreach ($data as $key => &$value) {
                $num = mb_strlen($value['user_account']);
                $value['user_account'] = '**'.mb_substr($value['user_account'],  2, $num,'utf-8');
               $value['addtime'] = date('Y-m-d/H:i:s', time());
               $arr=explode('/', $value['addtime']);
               $value = array_merge($value, $arr);
                $value['impr_picture'] = '/shop/Public/Uploads/headportrait/'.$value['impr_picture'];
                $value['attr'] = round(($value['attr']+$value['speed'] + $value['qua'])/3 , 2);
            }
            echo json_encode($data);

        }else{
            $this->ajaxReturn('');
        }

       }

       /**
        * [订单详情1]
        *
        * @author xuzan<m13265000805@163.com>
        *
        * @param  [type]    描述参数作用
        *
        * @return [type] [description]
        */
        public function orderTrusteeship()
        {
            $this->orderPublic();
            $this->display();
        }

        /**
         * [上传合同]
         *
         * @author xuzan<m13265000805@163.com>
         *
         * @param  [type]    描述参数作用
         *
         * @return [type] [description]
         */
         public function trusteeship()
         {
           $model = D('Accessory');
           if ($model->create(I('post.'), 1)) {
               if ($id = $model->add()) {
                  $order=M('store_order');                  
                  $result=$order->where('id ='.I('post.order_id'))->setField('order_status',7);
                   // 添加成功后把店铺id存到session中
                   $this->redirect('home/service/orderTrusteeship2/id/'.I('post.order_id'));
                   exit;
               }
           }
           $this->error($model->getError());
         }

         /**
          * [下载]
          *
          * @author xuzan<m13265000805@163.com>
          *
          * @param  [type]    描述参数作用
          *
          * @return [type] [description]
          */
          public function accesory1()
          {
            $id = I('get.id');
            $model = M('Accessory');
            $url = $model->where("id=$id")->getField('order_url');
            if($url){
              $url = C('IMG_rootPath').$url;
              down($url);
            }else{
              $this->error('下载失败');
            }
          }


        /**
        * [修改金额]
        *
        * @author xuzan<m13265000805@163.com>
        *
        * @param  [type]    描述参数作用
        *
        * @return [type] [description]
        */
        public function  orderUpdate()
        {
            $id = I('post.id');
            $price = I('post.order_number_price');
            $model = M('StoreOrder');
            $bool = $model->where("id=$id")->setField('order_number_price', $price);
            if($bool !==false){
                redirect(U('home/service/order', array('order_status'=>0)));

            }else {
                $this->error('修改失败！');
            }
        }




        /**
         * [订单详情2]
         *
         * @author xuzan<m13265000805@163.com>
         *
         * @param  [type]    描述参数作用
         *
         * @return [type] [description]
         */
         public function orderTrusteeship1()
         {
             $this->orderPublic();
             $this->display();
         }

         /**
          * [订单详情3]
          *
          * @author xuzan<m13265000805@163.com>
          *
          * @param  [type]    描述参数作用
          *
          * @return [type] [description]
          */
          public function orderTrusteeship2()
          {
              $this->orderPublic();
              $this->display();
          }

        /**
         * [订单详情4]
         *
         * @author xuzan<m13265000805@163.com>
         *
         * @param  [type]    描述参数作用
         *
         * @return [type] [description]
         */
         public function orderTrusteeship3()
         {
             $this->orderPublic();
             $this->display();
         }
         public function orderTrusteeship4()
         {
             $this->orderPublic();
             $this->display();
         }


        /**
         * [公共orderTrusteeship-public]
         *
         * @author xuzan<m13265000805@163.com>
         *
         * @param  [type]    描述参数作用
         *
         * @return [type] [description]
         */
         public function orderPublic()
         {
             $model = D('StoreOrder');
             $array = $model->orderService();
             $this->assign($array);
         }
    public function manageIndex()
    {
        $store=D('store');
        $storeInfo=$store->getStoreInfoData();
        $this->assign($storeInfo);
        $this->display();
      }


          public function upload()
          {

                $id = I('post.order_id');
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize   =     3145728 ;// 设置附件上传大小
                $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg', 'txt', 'doc', 'docx', 'ptf');// 设置附件上传类型
                $upload->savePath  =      'order/'; // 设置附件上传目录    // 上传文件

                $info   =   $upload->upload($_FILES);
                $small = round($_FILES['order_url']['size'], 4);
                // p($info);
                // p($_FILES);
                // p($_POST);
                // exit;
                if(!$info) {
                    // 上传错误提示错误信息
                    $this->error($upload->getError());
                 }else{
                        // 上传成功
                        $model = M('accessory_service');
                        $bool  =   $model->add(array(
                            'order_url'=> $info['order_url']['savepath'].$info['order_url']['savename'],
                            'addtime'=>time(),
                            'order_id'=>$id,
                            'order_small'=>$small,
                        ));
                        if($bool){
                        	$order=M('store_order');	
                        	$data['order_status']=10;
                        	$order_map['id']=['eq',$id];
                        	$result=$order->where($order_map)->save($data);

                        	if($result === false){
                        		$this->error('由于未知原因导致失败!');
                        		
                        	}else{
                        		redirect(U('orderTrusteeship3',array('id'=>$id)));
                        	}
	                    }else{
	                        $this->error('上次失败');
	                    }
                  }
            }




    /**
     * @author 胡金矿<1968346304@qq.com>
     * [storeDescription 店铺轮播图]
     * @return [type] [num]
     */
    public function storeDescription()
    {
      $desc=D('store_carousel');
      $result=$desc->descHandler();
      if($result){
        $this->ajaxReturn($result);
      }else{
        $this->ajaxReturn(0);
      }
    }
    /**
     * @author 胡金矿<1968346304@qq.com>
     * [storeShowPic 店铺展示图]
     * @return [type] [description]
     */
    public function storeShowPic()
    {
      $show=D('store_show_picture');
      $result=$show->pictureHandler();
      if($result){
        $this->ajaxReturn($result);
      }else{
        $this->ajaxReturn(0);
      }
    }
    /**
     * @author 胡金矿<1968346304@qq.com>
     * [publishServiceOne 发布服务第一步]
     *
     * @return [bool] [数据是否插入成功]
     */
    public function publishServiceOne()
    {
        if (IS_GET) {

        	  session('home_publish_signOne',null);
            $map['cate_shopid'] = ['eq', session('store_id')];
            $cateService = M('shop_category');
            $list_id = $cateService->where($map)->select();
            $cate_firstid = $list_id[0]['cate_firstid'];

            $storeMap['id'] = ['eq', $cate_firstid];
            $storeCategory = M('store_category');
            $list['cate_firstid'] = $storeCategory->where($storeMap)->find();

            $cate_secondid = $list_id[0]['cate_secondid'];
            $mapp['id'] = ['eq', $cate_secondid];
            $list['cate_secondid'] = $storeCategory->where($mapp)->find();

            $cate_threeid = $list_id[0]['cate_threeid'];
            $mapt['id'] = ['in', $cate_threeid];
            $list['cate_threeid'] = $storeCategory->where($mapt)->select();
            $this->assign('list', $list);
            $this->display();
        }
        if (IS_POST) {
            //1.把接收到的数据插入到数据库
            //2.执行下第二部步跳转

            $cate = D('publish');
            $res = $cate->dealCate();
            if ($res>1) {
            	  session('home_publish_signOne',1);
                $this->redirect('Home/Service/publishServiceTwo');
            }else {
            	  session('home_publish_signOne',null);
                $this->redirect('Home/Service/publishServiceOne');
            }
        }
    }
    /**
     * @author 胡金矿<1968346304@qq.com>
     * [publishServiceTwo 发布服务第二部]
     *
     * @return [bool] [数据是否更新到数据表中]
     */
    public function publishServiceTwo()
    {
        if(session('home_publish_signOne')===1){

        	if(IS_GET){
        		session('home_publish_signTwo',null);
        		
        		$this->display();
        	}

	        if (IS_POST) {
	            $publish = D('publish');
	            $result = $publish->handler();
	            $this->assign('result', $result);
	            if ( !($result['errornum'] > 0 )) {
	            	session('home_publish_signTwo',2);
	                $this->redirect('Home/Service/publishServiceThird');
	            }else{
	            	$this->error($result['msg']);
	            	$this->display();
	            }

	        }
        }else{
        	session('home_publish_signOne',null);
        	$this->redirect('Home/Service/publishServiceOne');
        }
    }
    /**
     * @author 胡金矿 <1968346304@qq.com>
     * [publishServiceThird 显示第三步发布页面]
     *
     * @return [type] [description]
     */
    public function publishServiceThird()
    {
        if(session('home_publish_signTwo')===2){
        	session('home_publish_signTwo',null);
        	$this->display();
        }else{
        	session('home_publish_signTwo',null);
        	$this->redirect('Home/Service/publishServiceOne');
        }
    }
    /**
     * @author 胡金矿<1968346304@qq.com>
     * [pastService 查询发布的服务]
     *
     * @return [bool] [是否查询到服务]
     */
    public function pastService()
    {
        $past = D('publish');
        $data = $past->pastHandler();
        $this->assign($data);
        $this->display();
    }
    /**
     * @author 胡金矿<1968346304@qq.com>
     * 查看更多服务信息.
     */
    public function moreService()
    {
        $more = D('publish');
        $list = $more->moreService();
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * @author 胡金矿<1968346304@qq.com>
     * [updateService 更新服务信息]
     *
     * @return [type] [description]
     */
    public function updateService()
    {
        if (IS_POST) {
            $update = D('publish');
            $listError = $update->updataService();
            p($list);
            if ($listError['errornum']) {
                $dataList=$update->selectData();
                $this->assign('listError',$listError);
                $this->assign($dataList);
                $this->redirect('Home/Service/updateService/id/'.$_GET['id']);

            }else{
                $this->success('更新成功 !',U('Home/Service/pastService'),2);
            }
        }
        if (IS_GET) {
            $publish = D('publish');
            $dataList = $publish->selectData();
            $this->assign($dataList);
            $this->display();
        }
    }
    /**
     * @author 胡金矿<1968346304@qq.com>
     * [forMyOrder 获取适合服务商的需求]
     *
     * @return [type] [description]
     */

    public function forMyOrder()
    {
        $shop = D('shop_category');
        $data = $shop->shopDataHandler();
        $this->assign($data);
        $this->display();
    }
    /**
     * @author 胡金矿<1968346304@qq.com>
     * [officalOrder 获取所有官方推荐的需求]
     *
     * @return [array] [description]
     */
    public function officalOrder()
    {
        $shop = D('shop_category');
        $data = $shop->needDataHandler();
        $this->assign('data',$data);
        $this->display();
    }

    /**
     * @author 胡金矿<1968346304@qq.com>
     * [officalOrderDetail 获取官方推荐订单的详细数据]
     * @return [type] [description]
     */
    public function officalOrderDetail()
    {
        $needData = D('shop_category');
        $data = $needData->officalDetail();
        $this->assign('data', $data);
        $this->display();
    }
    public function storeEmployerDetail()
    {
      $this->display();
    }

    /**
     * @author 胡金矿<1968346304@qq.com>
     * [categoryDetail 获取店铺分类的详细数据]
     * @return [type] [description]
     */
    public function categoryDetail()
    {
      $this->publicStoreNav();
      $publish=D('publish');
      $service=$publish->getServiceData();
      $this->assign('service',$service);
      $this->display();
    }
    public function caseDetail()
    {
      $this->publicStoreNav();
      $storecase=D('store_case');
      $listData=$storecase->getCaseDetailData();
      $this->assign('listData',$listData);
      // $store=D('store');
      // $data = $store->shopHome();
      // $this->assign('data',$data);
      // $cateList=$store->getStoreCate();
      // $this->assign('cate',$cateList);
      $this->display();

    }

    /**
    * [收件箱: 接收消息]
    * [xwc] [13434808758@163.com]
    */
    public function storeComeboxMessage ()
    {
      if(I('get.p')){
        $p = I('get.p');
      }else{
        $p = 1;
      }
      // 收件人是自己
      $account_info = I('session.');
      // 默认查询条件是(全部)
      $fx_sel = 'lt';
      $fx_num = 2;
      $return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'all'];
      //查询全部
      if(I('get.sel') == 'all'){
        $fx_sel = 'lt';
        $fx_num = 2;
        $return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'all'];
      }
      // 查询未读
      if(I('get.sel') === '0'){
        $fx_sel = 'eq';
        $fx_num = 0;
        $return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'0'];
      }
      // 查询已读
      if(I('get.sel') === '1'){
        $fx_sel = 'eq';
        $fx_num = 1;
        $return_param = ['action'=>'sel','mesm_id'=>$val['mesm_id'],'sel'=>'1'];
      }
      // 开始查询
      $map = [
        'mesm_receiver' => ['eq',$account_info['home_user_info']['user_account']],
        'mesm_status' => [$fx_sel,$fx_num]
      ];
      $mesm = M('messagesystem');
      $count = $mesm->where($map)->count();
      $page = new \Think\Page($count,10);
      $page->setConfig('prev','上一页');
      $page->setConfig('next','下一页');
      $mesm_list = $mesm->limit($page->firstRow,$page->listRows)->where($map)->select();
      $this->assign('mesm_list',$mesm_list);
      $this->assign('return_param',$return_param);
      $this->assign('return_page',$page->show());
      $this->assign('return_p',$p);
      $this->display();
    }
    public function storeComeboxMessageAct ()
    {
        // -------------------
        if(IS_GET){
            // 查看单条消息详情
            if(I('get.action') == 'sel'){
                $mesm_id = I('get.mesm_id');
                $mesm = M('messagesystem');
                $map = [
                  'mesm_id' =>['eq',$mesm_id],
                  'mesm_status' =>['eq',0],
                ];
                $findid = $mesm->where($map)->find();
                if($findid){
                  $map = [
                    'mesm_id' =>['eq',$mesm_id],
                  ];
                  $data['mesm_status'] = 1;
                  $saveid =	$mesm->field('mesm_status')->where($map)->save($data);
                }
                redirect(U('Home/Service/detailComeboxMessage',['mesm_id'=>$mesm_id,'sel'=>I('get.sel'),'p'=>I('get.p')]));

            }
            // 删除单条消息
            if(I('get.action') == 'del'){
                $mesm_id = I('get.mesm_id');
                $map=[
                  'mesm_id' => ['eq',$mesm_id],
                ];
                $data['mesm_status'] = 2;
                $mesm = M('messagesystem');
                $save_id = $mesm->field('mesm_status')->where($map)->save($data);
                if($save_id){
                  $this->success();
                  // redirect(U('Home/Message/meMessage'));
                }else{
                  // redirect(U('Home/Message/meMessage'));
                  $this->success();
                }
            }

            //--------------------------

            // 设为已读
            if(I('get.act') == 'markRead'){
              if(!I('get.mesm_id')){
                // echo '你没有选择任何操作项!';
                $this->success();
                // redirect(U('Home/Message/outboxMessage'));
              }else{
                $get_data = I('get.mesm_id');
                $str = '';
                for($i=0; $i<count($get_data);$i++){
                  $str .= $get_data[$i] . ',';
                }
                $str = rtrim($str,',');
                $map=[
                  'mesm_id' => ['in',$str],
                ];
                $data['mesm_status'] = 1;
                $mesm = M('messagesystem');
                $save_id = $mesm->field('mesm_status')->where($map)->save($data);
                if($save_id){
                  $this->success();
                  // redirect(U('Home/Message/outboxMessage'));
                }else{
                  $this->success();
                  // redirect(U('Home/Message/outboxMessage'));
                }
              }

            }
            //选择删除
            if(I('get.act') == 'checkedDel'){
              if(!I('get.mesm_id')){
                // echo '你没有选择任何操作项!';
                $this->success();
                // redirect(U('Home/Message/outboxMessage'));
              }else{
                $get_data = I('get.mesm_id');
                $str = '';
                for($i=0; $i<count($get_data);$i++){
                  $str .= $get_data[$i] . ',';
                }
                $str = rtrim($str,',');
                $map=[
                  'mesm_id' => ['in',$str],
                ];
                $data['mesm_status'] = 2;
                $mesm = M('messagesystem');
                $save_id = $mesm->field('mesm_status')->where($map)->save($data);
                if($save_id){
                  $this->success();
                  // redirect(U('Home/Message/outboxMessage'));
                }else{
                  $this->success();
                  // redirect(U('Home/Message/outboxMessage'));
                }
              }
            }
        }
    }
    public function detailComeboxMessage ()
    {
        $return_param = ['sel'=>I('get.sel')];
        $mesm_id = I('get.mesm_id');
        $map = [
          'mesm_id'=>['eq',$mesm_id],
        ];
        $mesm = M('messagesystem');
        $mesm_list = $mesm->where($map)->find();
        if($mesm_list){
          $this->assign('mesm_list',$mesm_list);
          $this->assign('return_param',$return_param);
          $this->assign('return_p',I('get.p'));
          $this->display();
        }else{
          redirect(U('Home/message/meMessage'));
        }
    }
    /**
     * [写消息: ]
     * [xwc] [13434808758@163.com]
     */
    public function storeWriteMessage ()
    {
      $this->display();
    }
    public function storeWriteMessageAct ()
    {
      // mesm_sender(发件人) mesm_receiver(收件人) mesm_title(标题) mesm_centent(内容)
      // mesm_type(类型) mesm_sendtime(发送时间) mesm_status(状态)
      if(IS_POST){

        $mesmData = I('post.');

        $return_data['status'] = true;
        $return_data['error_type'] = '';
        $return_data['error_info'] = '';

        $account_info = I('session.home_user_info');// 会员信息

        // 1.验证(收件人不能为自己)
        if($account_info['user_account'] == $mesmData['mesm_receiver']){
            $return_data['status'] = false;
            $return_data['error_type'] = 'receiver';
            $return_data['error_info'] = '无法给自己发送消息!';
            // ajaxReturn();
            // ajaxReturn($return_data);
            $this->ajaxReturn($return_data);
            // echo json_encode(	$return_data);
            exit;
        }
        // 2.验证(标题不能小于4个字符)
        if(strlen($mesmData['mesm_title']) < 4){
          $return_data['status'] = false;
          $return_data['error_type'] = 'title';
          $return_data['error_info'] = '标题不能小于4个字符';
          // ajaxReturn();
          $this->ajaxReturn($return_data);
          exit;
        }
        // 3.判断收件人是否存在
        $user_account = M('user');
        $account = $user_account->where(['user_account'=>['eq',$mesmData['mesm_receiver']]])->find();
        if(!$account){
          $return_data['status'] = false;
          $return_data['error_type'] = 'receiver';
          $return_data['error_info'] = '收件人不存在!';
          // ajaxReturn();
          $this->ajaxReturn($return_data);
          exit;
        }
        $this->ajaxReturn($return_data);
      }

      if(IS_GET){
        $mesmData = I('get.');
        $account_info = I('session.home_user_info');// 会员信息
        // // 2拼装数据
        $mesm_sender = $account_info['user_account'];//发件人
        $mesm_receiver = $mesmData['mesm_receiver'];//收件人
        $mesm_title = $mesmData['mesm_title'];//标题
        $mesm_centent = $mesmData['mesm_centent'];// 内容
        $mesm_type = 0;//私信
        $mesm_sendtime = time();//发送时间
        $mesm_status = 0;// 未读

        $data = [
          'mesm_sender' => $mesm_sender,
          'mesm_receiver' => $mesm_receiver,
          'mesm_title' => $mesm_title,
          'mesm_centent' => $mesm_centent,
          'mesm_type' => $mesm_type,
          'mesm_sendtime' => $mesm_sendtime,
          'mesm_status' => $mesm_status,
        ];

        $mesm = M('messagesystem');
        $insert_id = $mesm->add($data);

        if($insert_id){
          $return_data['status'] = true;
          redirect(U('Home/Service/storeOutboxMessage'));
        }

      }



    }/* --写消息结束 -- */
    /**
    *   [发件箱: 发送消息]
    *   [xwc] [13434808758@163.com]
    */
    public function storeOutboxMessage ()
    {
      if(I('get.p')){
        $p = I('get.p');
      }else{
        $p = 1;
      }
      // 获取用户信息
      $account_info = I('session.home_user_info');// 会员信息
      $mesm = M('messagesystem');
      $map = [
        'mesm_sender' =>['eq',$account_info['user_account']],
        'mesm_type' =>['eq',0],
        'mesm_status' =>['neq',2],
      ];
      $count = $mesm->where($map)->count();
      $page = new \Think\Page($count,2);
      $page->setConfig('prev','上一页');
      $page->setConfig('next','上一页');
      // dump($page);
      $mesm_list = $mesm->limit($page->firstRow,$page->listRows)->where($map)->select();
      $this->assign('mesm_list',$mesm_list);
      $this->assign('return_data',$this->return_data);
      $this->assign('return_page',$page->show());
      $this->assign('return_p',$p);
      $this->display();
    }
    public function storeOutboxMessageAct ()
    {
        if(IS_GET){
            if(I('get.action') == 'sel'){
              $mesm_id = I('get.mesm_id');
              redirect(U('Home/Service/detailOutboxMessage',['mesm_id'=>$mesm_id,'p'=>I('get.p')]));
            }
            if(I('get.action') == 'del'){
              $mesm_id = I('get.mesm_id');
              $map=[
                'mesm_id' => ['eq',$mesm_id],
              ];
              $data['mesm_status'] = 2;
              $mesm = M('messagesystem');
              $save_id = $mesm->field('mesm_status')->where($map)->save($data);
              if($save_id){
                // $this->success();
                redirect(U('Home/Service/storeOutboxMessage'));
              }else{
                // $this->success();
                redirect(U('Home/Service/storeOutboxMessage'));
              }
            }
        }

        if(IS_POST){
            dump(I('post.'));
            if(!I('post.')){
              redirect(U('Home/Service/storeOutboxMessage'));
            }else{
              $post_data = I('post.');
              $str = '';
              for($i=0; $i<count($post_data['mesm_id']);$i++){
                $str .= $post_data['mesm_id'][$i] . ',';
              }
              $str = rtrim($str,',');
              // dump($str);
              $map=[
                'mesm_id' => ['in',$str],
              ];
              $data['mesm_status'] = 2;
              $mesm = M('messagesystem');
              $save_id = $mesm->field('mesm_status')->where($map)->save($data);
              if($save_id){
                // $this->success();
                redirect(U('Home/Service/storeOutboxMessage'));
              }else{
                // $this->success();
                redirect(U('Home/Service/storeOutboxMessage'));
              }
            }
        }
    }
    public function detailOutboxMessage ()
    {
        // dump($_SERVER["HTTP_REFERER"]);
        // $account_info = I('session.');
        // $mesm_account = $account_info['home_user_info']['user_account'];
        $mesm_id = I('get.mesm_id');
        $map = [
          // 'mesm_sender'=>['eq',$mesm_account],
          'mesm_id'=>['eq',$mesm_id],
          // 'mesm_status'=>['neq',2],
        ];
        $mesm = M('messagesystem');
        $mesm_list = $mesm->where($map)->find();
        if($mesm_list){
          $this->assign('mesm_list',$mesm_list);
          $this->assign('return_p',I('get.p'));
          $this->display();
        }else{
          redirect(U('Home/message/outboxMessage'));
        }

    }
    public function detailOutboxMessageAct ()
    {
        // echo __METHOD__;
        $this->display();
    }




}
