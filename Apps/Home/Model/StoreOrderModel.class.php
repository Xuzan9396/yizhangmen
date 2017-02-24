<?php

namespace Home\Model;

use Think\Model;

/**
 * [店铺的订单order].
 *
 * @author xuzan<m13265000805@163.com>
 *
 * @param  [type]    描述参数作用
 *
 * @return [type] [description]
 */
class StoreOrderModel extends Model
{
    // 添加是验证
    protected $insertFields = array('order_serviceid', 'order_number_total','order_trusteeship_price', 'order_description', 'order_phone');
    // 修改时候
    protected $updateFields = array('id', 'order_serviceid', 'order_number_total', 'order_trusteeship_price','order_description', 'order_phone');

    protected $_validate = array(
        ['order_serviceid', 'require', '服务id不正确', 1, 'regex', 3],
        ['order_serviceid', 'number', '服务id不正确', 1, 'regex', 3],
        // ['']
        ['order_number_total', 'number', '请选择正确的交易件数', 1, 'regex', 3],
        ['order_description', 'require', '请填写描述', 1, 'regex', 3],
        ['order_phone', '11', '手机号码的格式不正确', 1, 'length', 3],
    );

    protected function _before_insert(&$data, $option)
    {

        // 生成的唯一订单
        $order_number = date('Ymd').substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        // 雇主id
        $data['order_employerid'] = session('home_user_info')['user_id'];

        $data['order_number'] = $order_number;
        $data['order_time'] = time();
        $model = M('publish');
        $map['a.id'] = ['eq', $data['order_serviceid']];
        // 找出商家id
        $list = $model->field('a.pubh_price,a.pubh_shopid,b.store_userid')->alias('a')->join('LEFT JOIN app_store b ON a.pubh_shopid = b.id')->where($map)->find();

        if ($list) {
            $data['order_number_price'] = $list['pubh_price'] * $data['order_number_total'];
            $data['order_serviceuserid'] = $list['store_userid'];
        } else {
            return false;
        }


        // 上传上传附件
        if($_FILES['order_myfile']['size'] !=0){
        if (isset($_FILES['order_myfile']) && $_FILES['order_myfile']['error'] == 0) {
            $ret = uploadOne('order_myfile', 'store/order' , 1);

            if ($ret['ok'] == 1) {
                $data['order_myfile'] = $ret['images'][0];
            } else {
                $this->error = $ret['error'];

                return false;
            }
        } else {
            switch ($_FILES['order_myfile']['error']) {
                case 1:
                       $this->error = '上传的文件超过最大限度';

                       return false;
                    break;
                case 2:
                       $this->error = '上传文件的大小超过了规定的值';

                       return false;
                    break;
                case 3:
                       $this->error = '文件只有部分被上传';

                       return false;
                    break;
                case 4:
                        $this->error = '没有文件上传';

                        return false;
                     break;
                 case 6:
                         $this->error = '找不到临时文件夹';

                         return false;
                      break;
                  case 7:
                          $this->error = '文件写入失败';

                          return false;
                       break;
                default:
                    break;
            }
        }
        }
    }

    /**
     * [店铺的成功订单遍历].
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
    public function orderSelect()
    {
        $map['order_status'] = ['eq', 4];
        $map['order_serviceuserid'] = ['eq', session('home_user_info')['user_id']];
        // 调用公共函数
        if (I('get.order_number')) {
            $map['order_number'] = ['like', '%'.I('get.order_number').'%'];
        }
        $page = myPage($this, $map, 2);
        // 分页查询
        // $map['b.pubh_shopid'] = ['eq', session('store_id')];
        $list = $this->field('b.*,a.*')->alias('a')->join('LEFT JOIN app_publish b ON  a.order_serviceid=b.id')->where($map)->limit($page->pagerows(), $page->maxrows())->select();
        // 得到分页
        $show = $page->get_page();

        return ['data' => $list, 'show' => $show];
    }

    /**
     * [店铺的成功订单遍历].
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
    public function orderSelect1()
    {
        // 是否在回收站的订单
        $map['order_isdelete'] = ['eq', 0];
        $start = I('get.startTime');
        $end = I('get.endTime');
        $start1 = strtotime(I('get.startTime').'00:00:01');
        $end1 = strtotime(I('get.endTime').'23:59:59');
        if ($start && $end) {
            // 时间搜索
            $map['order_time'] = ['between', array($start1, $end1)];
        } elseif ($start) {
            // 时间搜索
            $map['order_time'] = ['egt', $start1];
        } elseif ($end) {
            // 时间搜索
            $map['order_time'] = ['elt', $end1];
        }
        $map['order_serviceuserid'] = ['eq', session('home_user_info')['user_id']];
        $map['order_serviceuserid'] = ['eq', session('home_user_info')['user_id']];
        // 订单搜索
        if (I('get.order_number')) {
            $map['order_number'] = ['like', '%'.I('get.order_number').'%'];
        }
        // 交易状态搜索

        if (I('get.order_status') !== '' && in_array($_GET['order_status'], array(0, 1, 2, 3, 4,5,6,7,8,10))) {
            $map['order_status'] = ['eq', I('get.order_status')];
        }
        // 分页查询
        $map['b.pubh_shopid'] = ['eq', session('store_id')];
        // $page = myPage($this, $map, 10);
        import('@.Class.Page'); //引入Page类
        // 查询满足要求的总记录数
        // $count = $this->where($map)->count();

        $count = $this->field('b.*,a.*')->alias('a')->join('LEFT JOIN app_publish b ON  a.order_serviceid=b.id')->where($map)->count();

        /*进行第三方分页类配置*/
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
        $list = $this->field('b.*,a.*')->alias('a')->join('LEFT JOIN app_publish b ON  a.order_serviceid=b.id')->where($map)->limit($page->pagerows(), $page->maxrows())->select();
        // 得到分页
        $show = $page->get_page();
        $order_model = ['线上交易', '线下交易'];
        $order_status = ['待付款', '订单已经取消', '退款中', '退款成功', '已付款','已托管','下线','发起合同','签合同','已完成工作'];
        $val['order_status1'] = [];
        foreach ($list as &$val) {
            $val['order_status1'] = $order_status[$val['order_status']];
        }

        return ['data' => $list, 'show' => $show];
    }

    /**
     * [雇主信息ajax方法,拼接表格].
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
    public function orderSelect2()
    {
        $id = I('post.id');
        if ($id) {
            $map['a.id'] = $id;
        }
        $list = $this->field('a.order_description,a.order_myfile,a.order_phone,b.user_account,b.user_type')->alias('a')->join('LEFT JOIN app_user b ON a.order_employerid=b.user_id')->where($map)->find();
        $userType = ['A类会员', 'B类会员', 'C类会员'];
        $list['user_type'] = $userType[$list['user_type']];
        $res = "<table class='table'>
                    <tr>
                        <td>雇主会员名</td>
                        <td>{$list['user_account']}</td>
                    </tr>
                    <tr>
                        <td>雇主的联系电话</td>
                        <td>{$list['order_phone']}</td>
                    </tr>
                    <tr>
                        <td>雇主的会员类型</td>
                        <td>{$list['user_type']}</td>
                    </tr>
                    <tr>
                        <td>雇主描述</td>
                        <td>{$list['order_description']}</td>
                     </tr>
                </table>";

        return $res;
    }

    /**
     * [查找订单信息].
     *
     * @author YeWeiBin
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
    public function getOrderInfo($oid)
    {
        $id = I('get.oid', $oid);
        $map['id'] = $id;
        $res1 = $this->where($map)->find();
        $publish = M('publish');
        $map1['id'] = $res1['order_serviceid'];
        $res2 = $publish->field('pubh_title,pubh_price,pubh_pic')->where($map1)->find();

        $res = array_merge($res1, $res2);

        if ($res['order_employerid'] != $_SESSION['home_user_info']['user_id']) {
            return false;
        } else {
            return $res;
        }
    }

    /*
     * [回收站]
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
     public function recycle()
     {
         // 是否在回收站的订单
         $map['order_isdelete'] = ['eq', 1];
         $start = I('get.startTime');
         $end = I('get.endTime');
         $start1 = strtotime(I('get.startTime').'00:00:01');
         $end1 = strtotime(I('get.endTime').'23:59:59');
         if ($start && $end) {
             // 时间搜索
             $map['order_time'] = ['between', array($start1, $end1)];
         } elseif ($start) {
             // 时间搜索
             $map['order_time'] = ['egt', $start1];
         } elseif ($end) {
             // 时间搜索
             $map['order_time'] = ['elt', $end1];
         }
         $map['order_serviceuserid'] = ['eq', session('home_user_info')['user_id']];
         $map['order_serviceuserid'] = ['eq', session('home_user_info')['user_id']];
         // 订单搜索
         if (I('get.order_number')) {
             $map['order_number'] = ['like', '%'.I('get.order_number').'%'];
         }
         // 交易状态搜索

             if (I('get.order_status') != '' && in_array($_GET['order_status'], array(0, 1, 2, 3, 4))) {
                 $map['order_status'] = ['eq', I('get.order_status')];
             }

         $page = myPage($this, $map, 2);
         // 分页查询
         $map['b.pubh_shopid'] = ['eq', session('store_id')];
         $list = $this->field('b.*,a.*')->alias('a')->join('LEFT JOIN app_publish b ON  a.order_serviceid=b.id')->where($map)->limit($page->pagerows(), $page->maxrows())->select();
         // 得到分页
         $show = $page->get_page();
         $order_model = ['线上交易', '线下交易'];
         $order_status = ['待付款', '订单已经取消', '退款中', '退款成功', '已付款'];
         $val['order_status1'] = [];
         foreach ($list as &$val) {
             $val['order_model'] = $order_model[$val['order_model']];
             $val['order_status1'] = $order_status[$val['order_status']];
         }
         return ['data' => $list, 'show' => $show];
     }

        /**
      * [服务商托管页面]
      *
      * @author xuzan<m13265000805@163.com>
      *
      * @param  [type]    描述参数作用
      *
      * @return [array] [数组]
      */
      public function orderService()
      {
          $order_id = I('get.id');
          // 订单id拿到用户名
          $list = $this->alias('a')->field('a.order_iscomment,a.order_employerid,a.order_status,a.id,a.order_trusteeship_price,a.order_number_price,a.order_number_total,a.order_number,a.order_number_total,a.order_serviceuserid,a.order_description,a.order_time,a.order_serviceid,a.order_endtime,b.pubh_title')->join('left join app_publish b on a.order_serviceid = b.id')->where(array('a.id'=>array('eq', $order_id)))->find();
          $user_id = $list['order_employerid'];

          $needModel = M('Need');
          $need = $needModel->field('need_id, need_title')->where(array('need_userid' => array('eq', $user_id)))->limit('5')->order('need_valid_time desc')->select();

          // 查询雇主的信息
          $userModel = M('User');
          $userList = $userModel->field('a.user_phone, a.user_account, a.user_email,b.impr_picture')->alias('a')->where(array('a.user_id'=>array('eq', $user_id)))->join('left join app_impuser b on a.user_id = b.user_id ')->find();

          // 需求的服务分类
          // 服务id
          $serviceid = $list['order_serviceid'];
          $publishModel = M('Publish');
          $cateId = $publishModel->getField('pubh_categoryid');
          if($cateId){
             $cateModel = M('StoreCategory');
             $cate = $cateModel->field('GROUP_CONCAT(cate_name SEPARATOR "--->") catename')->where(array('id'=>array('in',$cateId)))->find();
          }

          // 合同信息
          $StoreModel = M('Store');
          $iduser = $list['order_serviceuserid'];
          $realName = $StoreModel->where(array('store_userid' => array('eq', $iduser)))->getField('store_realname');
          $storeName = $StoreModel->where(array('store_userid' => array('eq', $iduser)))->getField('store_name');
          // 合同表
          $accessoryModel = M('Accessory');
          $accessoryList = $accessoryModel->where(array('order_id'=>array('eq', $order_id)))->select();

          return array('list'=>$list, 'userList'=>$userList, 'need'=>$need , 'cate' =>$cate, 'realname' => $realName , 'storename' => $storeName ,'accessory' => $accessoryList);
      }


    /*
     * [直接购买 查询方法查询]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回数据查询
     */
    public function orderTrusteeshipList()
    {
        //订单,用户,服务商,服务,服务商店铺成功案例,合同表
        // 接收订单id
        if(I('get.id')){
            $order_id = I('get.id');
            // 保存session
            session('home_user_info.order_id',$order_id);
        }else{
            $order_id = session('home_user_info')['order_id'];
        }
        // 查询
        // 订单查询
        $order_map['id'] = ['eq' , $order_id];
        $order_list = $this->where($order_map)->find();
        //如果有尾款需要付的时候;自动计算
        $order_list['order_retainage_price'] = $order_list['order_number_price'] - $order_list['order_trusteeship_price'];

        //保存session
        session('publish_total', $order_list['order_number_total']);
        session('home_user_info.order_status',$order_list['order_status']);
        // 用户表查询
        //用户id登录已有session中 查询雇主信息
        $employer_id = session('home_user_info')['user_id'];
        $employer_map['i.user_id'] = ['eq' , $employer_id];
        // 查询
        $user_list = M('user')->where($employer_map)->alias('u')->join('LEFT JOIN app_impuser as i ON u.user_id = i.user_id')->find();
        //服务商id 服务商信息
        $service_userid = $order_list['order_serviceuserid'];
        $servicei_map['i.user_id'] = ['eq' , $service_userid ];
        $userb_list = M('user')->where($servicei_map)->alias('u')->join('LEFT JOIN app_impuser as i ON u.user_id = i.user_id')->find();
        //服务商id 查询店铺信息
        $service_userid = $order_list['order_serviceuserid'];
        $service_map['store_userid'] = ['eq' , $service_userid ];
        // 查询
        $store = M('store')->where($service_map)->find();

        //订单的服务id 查服务表
        $publish_id = $order_list['order_serviceid'];
        $publish_map['id'] = ['eq' , $publish_id];
        session('publish_id', $publish_id);
        $publish = M('publish')->where($publish_map)->find();
        //服务分类表服务信息 找服务类
        $cate_path = $publish['pubh_categoryid'];
        $category_map['id'] = ['in' , $cate_path];
        // 查询 返回拼接后的一维数组
        $store_category = M('store_category')->field("GROUP_CONCAT(cate_name) cate_name")->where($category_map)->find();
        // 替换面包线
        $store_category = str_replace(',', '-->', $store_category['cate_name']);

        //查询合同表
        $accessory_map['order_id'] = ['eq' , $order_id];
        $accessory = M('accessory')->where($accessory_map)->find();
        //店铺id 查询服务商成功案例
        $case_map['case_shop'] = ['eq' , $store['id']];
        $case = M('store_case')->where($case_map)->order('id desc')->limit(10)->select();

        //查询服务商源文件表
        $accessory_service_map['order_id'] = ['eq', $order_id];
        $accessory_service = M('accessory_service')->where($accessory_service_map)->select();

        //补充合同表
        $sup_map['sup_orderid'] = ['eq' , $order_id];
        $sup = M('supplement')->where($sup_map)->select();

        // 返回数据
        return ['order'=>$order_list,'user'=>$user_list,'store'=>$store,'publish'=>$publish,'store_category'=>$store_category,'case'=>$case,'accessory'=>$accessory,'accessory_service'=>$accessory_service,'userb'=>$userb_list,'sup'=>$sup];
    }

    /*
     * [托管页面更新]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回受影响行
     */
    public function orderTrusteeshipSave()
    {
        //自动验证规则

        $config= [
        ['order_trusteeship_price','/^[1-9]\d{1,9}$/','预算格式错误',1,'regex'],
        ];
        //post接受数据
        $post = I('post.');
        $post['order_status'] = 5;

        // 初始化
        $result['status'] = true;
        $result['error_info'] = '';
        // 判断托管金
        if($post['order_trusteeship_price'] < ($post['order_number_price'] * 3 / 10)){
            // 低于30%时
            $result['error_info'] = '赏金不可低于中标价的30%';
            $result['status'] = false;
        }elseif($post['order_trusteeship_price'] > ($post['order_number_price'])){
            //高于总金额时
            $result['error_info'] = '赏金高于总金额';
            $result['status'] = false;
        }else{

            $verify = M('store_order')->validate($config)->create($post);

            //判断
            if($verify){
                $map['id'] = ['eq',session('home_user_info')['order_id']];
                $result['status'] = $this->where($map)->save($verify);
            }else{
                $result['status'] = false;
                $result['error_info'] = M('store_order')->getError();
            }
        }
    }

    /*
     * [取消订单]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回受影响行
     */
    public function orderRemove()
    {
        //获取id
        $order_id = session('home_user_info')['order_id'];
        $map['id'] = ['eq',$order_id];
        //更改状态
        $result = $this->where($map)->setField('order_status',1);
        return $result;
    }

    /*
     * [合同页面更新]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回受影响行
     */
    public function orderContractSave()
    {
        // session 里的id
        $id =  session('home_user_info')['order_id'];
        // 更新合同表
        $accessory_map['order_id'] = ['eq' , $id];
        // 初始化
        $result['status'] = true;
        $result['error_info'] = '';
        //更新合同表 签约时间
        $result['status'] = M('accessory')->where($accessory_map)->setField('signtime',time());
        // 更新订单表状态
        if($result !== false){
            $map['id'] = ['eq' , $id];
            $result['status'] = $this->where($map)->setField('order_status',8);
        }else{
            $result['status'] = false;
            $result['error_info'] = '签约失败';
        }
        //返回值
        return $result;
    }

    /*
     * [工作页面更新]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回受影响行
     */
    public function orderWorkedSave()
    {
        //post接受数据
        $post = I('post.');
        // 有存在尾款时
        if($post['order_retainage_price'] > 0){
            // 初始化$result
            $result['status'] = true;
            $result['error_info'] = '';
            // 托管金
            $trusteeship = $post['order_trusteeship_price'];
            //总金额
            $number = $post['order_number_price'];
            // 尾款
            $retainage = $post['order_retainage_price'];
            // 尾款 = 总金额 - 托管金 不等于就错误
            if($retainage == ($number - $trusteeship)){
                // 自动验证
                //自动验证规则
                $_verify = [
                    ['order_retainage_price','/^[1-9]\d{1,9}$/','尾款格式错误',1,'regex']
                ];
                //状态改变
                $post['order_status'] = 4;
                // 付款时间
                $post['order_endtime'] = time();
                $verify = M('store_order')->validate($_verify)->create($post);
                //判断验证是否通过
                if($verify){
                    $map['id'] = ['eq',session('home_user_info')['order_id']];
                    $result['status'] = M('store_order')->where($map)->save($verify);
                }else{
                    // 验证失败
                    $result['status'] = false;
                    $result['error_info'] = M('store_order')->getError();
                }
            }else{
                $result['status'] = false;
                $result['error_info'] = '尾款金额不准确';
            }
        }
        return $result;
    }

    /*
     * [工作页面托管全额时更新]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回受影响行
     */
    public function orderWorkedStatusSave()
    {
        //获取参数
        $id = session('home_user_info')['order_id'];
        $map['id'] = ['eq' , $id];
        //更改状态
        $result = $this->where($map)->setField('order_status' , 4);
        return $result;
    }

    /*
     * [评价页面更新]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回受影响行
     */
    public function orderEvaluateSave()
    {
        //post接受数据
        $post = I('post.');
        $post['order_id'] = session('home_user_info')['order_id'];
        $post['addtime'] = time();
        //如果是默认没有写评价留言 就清空
        if($post['content'] == '评价描述最多60字节'){
            $post['content'] = '';
        }
        // 初始化$result
        $result['status'] = true;
        $result['error_info'] = '';
        //添加评价表
        $result['status'] = M('employer_comment')->add($post);
        if($result){
            // 状态更改
            $map['id'] = ['eq',session('home_user_info')['order_id']];
            //更改状态
            $result['status'] = $this->where($map)->setField('order_iscomment',1);
        }else{
            // 初始化$result
            $result['status'] = false;
            $result['error_info'] = '评价失败';
        }
        return $result;
    }

    /*
     * [中标 查询方法查询]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回数据查询
     */
    public function bidOrderTrusteeshipList()
    {
        //订单,用户,服务商,服务,服务商店铺成功案例,合同表
        // 接收订单id
        if(I('get.id')){
            $order_id = I('get.id');
            // 保存session
            session('home_user_info.order_id',$order_id);
        }else{
            $order_id = session('home_user_info')['order_id'];
        }
        // 查询
        // 订单查询
        $order_map['id'] = ['eq' , $order_id];
        $order_list = $this->where($order_map)->find();
        //如果有尾款需要付的时候;自动计算
        $order_list['order_retainage_price'] = $order_list['order_number_price'] - $order_list['order_trusteeship_price'];
        //保存session
        session('home_user_info.order_status',$order_list['order_status']);

        // 查询需求表
        $need_id = $order_list['order_needid'];
        $need_map['need_id'] = ['eq' , $need_id];
        //开始查询
        $need_list = M('need')->where($need_map)->find();

        // 用户表查询
        //用户id登录已有session中 查询雇主信息
        $employer_id = session('home_user_info')['user_id'];
        $employer_map['i.user_id'] = ['eq' , $employer_id];
        // 查询
        $user_list = M('user')->where($employer_map)->alias('u')->join('LEFT JOIN app_impuser as i ON u.user_id = i.user_id')->find();
        //服务商id 服务商信息
        $service_userid = $order_list['order_serviceuserid'];
        $servicei_map['i.user_id'] = ['eq' , $service_userid ];
        $userb_list = M('user')->where($servicei_map)->alias('u')->join('LEFT JOIN app_impuser as i ON u.user_id = i.user_id')->find();

        //服务商id 查询店铺信息
        $service_userid = $order_list['order_serviceuserid'];
        $service_map['store_userid'] = ['eq' , $service_userid ];
        // 查询
        $store = M('store')->where($service_map)->find();

        //查询合同表
        $accessory_map['order_id'] = ['eq' , $order_id];
        $accessory = M('accessory')->where($accessory_map)->find();

        //查询服务商源文件表
        $accessory_service_map['order_id'] = ['eq', $order_id];
        $accessory_service = M('accessory_service')->where($accessory_service_map)->select();
        //店铺id 查询服务商成功案例
        $case_map['case_shop'] = ['eq' , $store['id']];
        $case = M('store_case')->where($case_map)->order('id desc')->limit(10)->select();
        //补充合同表
        $sup_map['sup_orderid'] = ['eq' , $order_id];
        $sup = M('supplement')->where($sup_map)->select();
        // 返回数据
        return ['order'=>$order_list,'need'=>$need_list,'user'=>$user_list,'store'=>$store,'case'=>$case,'accessory'=>$accessory,'accessory_service'=>$accessory_service,'userb'=>$userb_list,'sup'=>$sup];
    }


    /*
     * [中标 取消方法]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回数据查询
     */
    public function bidorderRemove()
    {

        $get = I('get.');

        // 初始化$result
        $result['status'] = true;
        $result['error_info'] = '';
        //获取id
        $order_id = $get['order_id'];
        $order_bidid = $get['order_bidid'];
        $order_needid = $get['order_needid'];

        // 订单id
        $order_map['id'] = ['eq',$order_id];
        // 投标id
        $bid_map['bid_id'] = ['eq',$order_bidid];
        // 需求id
        $need_map['need_id'] = ['eq',$order_needid];
        //更改状态
        $result['status'] = $this->where($order_map)->setField('order_status',1);

        if($result['status'] !== false){
            // 成功就更改bid表状态
            $bid_field = [];
            $bid_field['bid_projectwill'] = 0;
            $bid_field['bid_projecwin'] = 0;
            $result['status'] = M('bid')->where($bid_map)->save($bid_field);

            if($result['status'] !== false){
                //更改需求表状态为中标
                dump($need_map);
                $result['status'] = M('Need')->where($need_map)->setField('need_status',3);
            }else{
                //失败
                $result['status'] = false;
                $result['error_info'] = '取消失败';
            }
        }else{
            // 失败
            $result['status'] = false;
            $result['error_info'] = '取消失败';
        }
        return $result;
    }

    /*
     * [中标 状态线下方法]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回数据查询
     */
    public function bidStatusSave()
    {
        // session 获取id
        $order_id = session('home_user_info')['order_id'];
        $map['id'] = ['eq' , $order_id];
        //更新状态 线下
        $result = $this->where($map)->setField('order_status' , 6);

        return $result;
    }

    /*
     * [中标 状态托管线上方法]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回数据查询
     */
    public function bidorderTrusteeshipSave()
    {
        //自动验证规则
        $config= [
        ['order_trusteeship_price','/^[1-9]\d{1,9}$/','预算格式错误',1,'regex']
        ];
        //post接受数据
        $post = I('post.');
        $post['order_status'] = 5;
        // 初始化$result
        $result['status'] = true;
        $result['error_info'] = '';
        if($post['order_trusteeship_price'] < ($post['order_number_price'] * 3 / 10)){
            $result['error_info'] = '赏金不可低于中标价的30%';
            $result['status'] = false;
        }elseif($post['order_trusteeship_price'] > ($post['order_number_price'])){
            //高于总金额时
            $result['error_info'] = '赏金高于总金额';
            $result['status'] = false;
        }else{
            $verify = M('store_order')->validate($config)->create($post);
            //判断
            if($verify){
                $map['id'] = ['eq',session('home_user_info')['order_id']];
                $result['status'] = $this->where($map)->save($verify);
            }else{
                $result['status'] = false;
                $result['error_info'] = M('store_order')->getError();
            }
        }
        return $result;
    }

    /*
     * [中标 同意合同方法]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回数据查询
     */
    public function bidOrderContractSave()
    {
        // 接收post提交
        $post = I('post.');
        //状态改变
        $post['status'] = 8;
        // 初始化$result
        $result['status'] = true;
        $result['error_info'] = '';
        //判断验证是否通过
        if($post){
            //id 合同表
            $acc_map['id'] = ['eq' , $post['id']];
            $post['signtime'] = time();
            $result['status'] = M('accessory')->where($acc_map)->save($post);
            if($result !== false){
                // 合同表添加成功就更新状态
                $order_map['id'] = ['eq' , $post['order_id']];
                $result['status'] =  $this->where($order_map)->setField('order_status' , 8);
            }else{
                // 更新失败
                $result['status'] = false;
                $result['error_info'] = M('store_order')->getError();
            }

        }else{
            // 验证失败
            $result['status'] = false;
            $result['error_info'] = M('accessory')->getError();
        }

        return $result;
    }

    /*
     * [中标 工作完成已托管全部金额方法]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回数据查询
     */
    public function bidorderWorkedStatusSave()
    {
        //获取参数
        $id = session('home_user_info')['order_id'];
        $map['id'] = ['eq' , $id];
        //更改状态
        $result = $this->where($map)->setField('order_status' , 4);
        return $result;
    }

    /*
     * [中标 工作完成方法]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回数据查询
     */
    public function bidOrderWordedSave()
    {
        $post = I('post.');
        // 线上交易时
        if($post['order_retainage_price'] > 0){
            // 初始化$result
            $result['status'] = true;
            $result['error_info'] = '';
            // 托管金
            $trusteeship = $post['order_trusteeship_price'];
            //总金额
            $number = $post['order_number_price'];
            // 尾款
            $retainage = $post['order_retainage_price'];
            // 尾款 = 总金额 - 托管金 不等于就错误
            if($retainage == ($number - $trusteeship)){
                // 自动验证
                //自动验证规则
                $_verify = [
                    ['order_retainage_price','/^[1-9]\d{1,9}$/','尾款格式错误',1,'regex']
                ];
                //状态改变
                $post['order_status'] = 4;
                // 付款时间
                $post['order_endtime'] = time();
                $verify = M('store_order')->validate($_verify)->create($post);
                //判断验证是否通过
                if($verify){
                    $map['id'] = ['eq',session('home_user_info')['order_id']];
                    $result['status'] = M('store_order')->where($map)->save($verify);
                }else{
                    // 验证失败
                    $result['status'] = false;
                    $result['error_info'] = M('store_order')->getError();
                }
            }else{
                $result['status'] = false;
                $result['error_info'] = '尾款金额不准确';
            }

        }
        // 线下交易付款凭证
        if($_FILES){
            // 获取id
            $id = session('home_user_info')['order_id'];
            // 数据
            $config = [
                'maxSize' => 3145728,
                'savePath' => 'order/',
                'saveName' => ['uniqid',''],
                'exts' => ['jpg' , 'png', 'jpeg'],
                'autoSub' => true,
                'subName' => ['date','Ym'],
                'rootPath' =>  './Public/Uploads/'
            ];
            // 实例化
            $upload = new \Think\Upload($config);
            // 文件上传
            $info  =  $upload->upload($_FILES);
            // 判断存不存在
            if($info){
                // 限制上传件数
                if(count($info) > 3){
                    $result['status'] = false;
                    $result['error_info'] = '上传超出限制';
                }else{
                    // 遍历提取数据
                    foreach ($info as $k => $val){
                        $filepost = [];
                        //文件名
                        $filepost['orer_name'] = $val['savename'];
                        //文件路径
                        $filepost['orer_path'] = $val['savepath'];
                        //订单id
                        $filepost['orer_orderid'] = $id;
                        //添加
                        $ordervoucher = M('ordervoucher')->add($filepost);
                        if($ordervoucher){
                            // 订单id
                            $order_map['id'] = ['eq' , $id];
                            // 付款时间
                            $order_post = [];
                            $order_post['order_endtime'] = time();
                            //更新状态
                            $order_post['order_status'] = 4;
                            // 更改状态
                            $return['status'] = M('store_order')->where($order_map)->save($order_post);
                        }else{
                            $result['status'] = false;
                            $result['error_info'] = '上传失败';
                        }
                    }
                }
            }else{
                $result['status'] = false;
                $result['error_info'] = '上传失败';
            }
        }
        return $result;
    }

    /*
     * [中标 工作评价方法]
     *
     * @author jinjun<757258777@qq.com>
     *
     * return 返回数据查询
     */
    public function bidOrderEvaluateSave()
    {
        //post接受数据
        $post = I('post.');
        $post['order_id'] = session('home_user_info')['order_id'];
        $post['addtime'] = time();
        //如果是默认没有写评价留言 就清空
        if($post['content'] == '评价描述最多60字节'){
            $post['content'] = '';
        }
        // 初始化$result
        $result['status'] = true;
        $result['error_info'] = '';
        //添加评价表
        $result['status'] = M('employer_comment')->add($post);
        if($result){
            // 状态更改
            $map['id'] = ['eq',session('home_user_info')['order_id']];
            //更改状态
            $result['status'] = $this->where($map)->setField('order_iscomment',1);
        }else{
            // 初始化$result
            $result['status'] = false;
            $result['error_info'] = '评价失败';
        }
        return $result;
    }

    /*
     * [个人中 买入的服务]
     *
     * @author xwc<13434808758@163.com>
     *
     * return 返回数据查询
     */
     public function buyingDemanderList ()
     {

       //获取userid
       $userInfo = I('session.');
       $userid =  $userInfo['home_user_info']['user_id'];
       $map = [
         'order_employerid' => ['eq',$userid],
       ];
       $desc = 'order_time desc';

       if(I('get.id')){
         $map['id'] = ['eq',I('get.id')];
       }
       if(I('get.order_status') !== 'all' && I('get.order_status') !== ''){
         $map['order_status'] = ['eq',I('get.order_status')];
       }
       if(I('get.orderby') == 'asc'){
         $desc = 'id asc';
       }

       // 查找所有订单
       $count = $this->where($map)->order($desc)->count();
       $page = new \Think\Page($count,5);
       $page->setConfig('prev','上一页');
       $page->setConfig('next','下一页');
       $orderList = $this->where($map)->order($desc)->limit($page->firstRow,$page->listRows)->select();

       return [
         'orderList'=>$orderList,
         'orderPage'=>$page->show(),
         'orderCount'=>$count,
       ];
     }
    /**
      * [评价管理]
      * @author bairen
      */
     public function commentManage()
     {
        $uid=session('home_user_info');//卖家id
        $uid=$uid['user_id'];//卖家id
        $map['order_serviceuserid']=$uid;//卖家id
        $map['order_status']=4;//订单交易状态，4:已付款，为成功状态
        $oid=$this->field('GROUP_CONCAT(id) as oid')->where($map)->select();
        $oid=$oid[0]['oid'];//所有订单id
        $assess=M('employer_comment');//实例化评价表
        $map1['order_id']=array('in',$oid);
        if($oid){
            $total=$assess->where($map1)->count();//所有评价总数
            $res=$assess->field('sum(star_service_attitude)as attitude,sum(star_work_speed) as speed,sum(star_finish_quality) as quality')->where($map1)->select();
            $data=array();
            $attitude=($res[0]['attitude'])/$total;
            $speed=($res[0]['speed'])/$total;
            $quality=($res[0]['quality'])/$total;
            $data['attitude'] = number_format($attitude,2,'.','');//态度
            $data['speed'] = number_format($speed,2,'.','');//速度
            $data['quality'] = number_format($quality,2,'.','');//质量
            $data['total']=$total;//总人数

            // 累计评价
            //最近一周
            $weekstart=mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
            $weekend=mktime(0,0,0,date('m'),date('d')-date('w')+8,date('Y'))-1;
            $condition1['comment_gmb']=0;//好评
            $condition1['order_id']=array('in',$oid);
            $condition1['addtime']=array('between',array($weekstart,$weekend));//最近一周的
            $good1=$assess->where($condition1)->count();//最近一周的好评个数
            // 最近一周中评
            $condition2['comment_gmb']=1;//中评
            $condition2['order_id']=array('in',$oid);
            $condition2['addtime']=array('between',array($weekstart,$weekend));//最近一周的
            $centre1=$assess->where($condition2)->count();//最近一周的中评个数
            // 最近一周差评
            $condition3['comment_gmb']=2;//中评
            $condition3['order_id']=array('in',$oid);
            $condition3['addtime']=array('between',array($weekstart,$weekend));//最近一周的
            $difference1=$assess->where($condition3)->count();//最近一周的差评个数

            // 最近一个月
            // 好评
            $monthstart=mktime(0,0,0,date('m'),date('d')-30,date('Y'));
            $monthend=mktime(0,0,0,date('m'),date('d'),date('Y'));
            $month1['comment_gmb']=0;//好评
            $month1['order_id']=array('in',$oid);
            $month1['addtime']=array('between',array($monthstart,$monthend));//最近一个月的
            $good2=$assess->where($month1)->count();//最近一个月的好评个数
            // 中评
            $month2['comment_gmb']=1;//中评
            $month2['order_id']=array('in',$oid);
            $month2['addtime']=array('between',array($monthstart,$monthend));//最近一个月的
            $centre2=$assess->where($month2)->count();//最近一个月的中评个数
            // 差评
            $month3['comment_gmb']=2;//差评
            $month3['order_id']=array('in',$oid);
            $month3['addtime']=array('between',array($monthstart,$monthend));//最近一个月的
            $difference2=$assess->where($month3)->count();//最近一个月的差评个数

            // 最近三个月
            // 好评
            $monthstart3=mktime(0,0,0,date('m')-2,date('d')-30,date('Y'));
            $monthend3=mktime(0,0,0,date('m'),date('d'),date('Y'));
            $trimester1['comment_gmb']=0;//好评
            $trimester1['order_id']=array('in',$oid);
            $trimester1['addtime']=array('between',array($monthstart3,$monthend3));//最近三个月的
            $good3=$assess->where($trimester1)->count();//最近三个月的好评个数
            // 中评
            $trimester2['comment_gmb']=1;//中评
            $trimester2['order_id']=array('in',$oid);
            $trimester2['addtime']=array('between',array($monthstart3,$monthend3));//最近三个月的
            $centre3=$assess->where($trimester2)->count();//最近三个月的中评个数
            // 差评
            $trimester3['comment_gmb']=2;//中评
            $trimester3['order_id']=array('in',$oid);
            $trimester3['addtime']=array('between',array($monthstart3,$monthend3));//最近三个月的
            $difference3=$assess->where($trimester3)->count();//最近三个月的差评个数

            // 总计
            $hebdomadTotal=$good1+$centre1+$difference1;//最近一周总计
            $oneMonthTotal=$good2+$centre2+$difference2;//最近一个月总计
            $trimesterTotal=$good3+$centre3+$difference3;//最近三个月总计
            $census=array();
            $census['good1']=$good1;
            $census['good2']=$good2;
            $census['good3']=$good3;
            $census['centre1']=$centre1;
            $census['centre2']=$centre2;
            $census['centre3']=$centre3;
            $census['difference1']=$difference1;
            $census['difference2']=$difference2;
            $census['difference3']=$difference3;

            $census['hebdomadTotal']=$hebdomadTotal;
            $census['oneMonthTotal']=$oneMonthTotal;
            $census['trimesterTotal']=$trimesterTotal;
        }
        return array('assess'=>$data,'census'=>$census);

    }
    /**
     * [来自雇主的评价]
     */
    public function fromHirer()
    {
       $uid=session('home_user_info');//卖家id
        $uid=$uid['user_id'];//卖家id
        $map['order_serviceuserid']=$uid;//卖家id
        $map['order_status']=4;//订单交易状态，4:已付款，为成功状态
        $oid=$this->field('GROUP_CONCAT(id) as oid')->where($map)->select();
        $oid=$oid[0]['oid'];//所有订单id
        $assess=M('employer_comment');//实例化评价表
        $map1['order_id']=array('in',$oid);


        if($oid){
            $total=$assess->where($map1)->count();//所有评价总数
             import('@.Class.Page'); //引入Page类
            /*进行第三方分页类配置*/
            $page = array(
                'total' => $total,/*总数（改）*/
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
            $star=$page->pagerows();
            $end=$page->maxrows();
            $Model=M();
            $info=$Model->query("select e.id, e.order_id,e.content,e.addtime,o.order_serviceid,o.order_employerid,o.order_number_total*o.order_number_price as price from app_employer_comment as e LEFT JOIN app_store_order as o on e.order_id=o.id LEFT JOIN app_serviceer_comment as c on c.assess_id=e.id where e.order_id in($oid) order by e.addtime desc limit $star,$end");
            foreach ($info as &$value){
                $map2['id']=$value['order_serviceid'];
                $publish=M('publish')->where($map2)->select();
                $value['pubh_title']=$publish[0]['pubh_title'];
                // 用户名
                $map3['user_id']=$value['order_employerid'];
                $user=M('user')->where($map3)->select();
                $value['name']=$user[0]['user_account'];
                // 头像
                $impuser=M('impuser')->where($map3)->select();
                $value['pictrue']=$impuser[0]['impr_picture'];
            }
        }

        // 得到分页
        $show = $page->get_page();
        return array('fromHirer'=>$info,'show'=>$show);
    }
    /**
     * [我对雇主的评价]
     */
    public function effect(){
        $uid=session('home_user_info');//卖家id
        $uid=$uid['user_id'];//卖家id
        $map['order_serviceuserid']=$uid;//卖家id
        $map['order_status']=4;//订单交易状态，4:已付款，为成功状态
        $oid=$this->field('GROUP_CONCAT(id) as oid')->where($map)->select();
        $oid=$oid[0]['oid'];//所有订单id
        $assess=M('employer_comment');//实例化评价表
        $map1['order_id']=array('in',$oid);


        if($oid){
            $total=$assess->where($map1)->count();//所有评价总数
             import('@.Class.Page'); //引入Page类
            /*进行第三方分页类配置*/
            $page = array(
                'total' => $total,/*总数（改）*/
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
            $star=$page->pagerows();
            $end=$page->maxrows();
            $Model=M();
            $info=$Model->query("select e.id, e.order_id,e.content,c.add_time,c.content as reply,o.order_serviceid,o.order_employerid,o.order_number_total*o.order_number_price as price from app_serviceer_comment as c LEFT JOIN app_employer_comment as e on c.assess_id=e.id LEFT JOIN app_store_order as o on e.order_id=o.id  where e.order_id in($oid) GROUP BY c.id order by c.add_time desc  limit $star,$end");
            foreach ($info as &$value){
                $map2['id']=$value['order_serviceid'];
                $publish=M('publish')->where($map2)->select();
                $value['pubh_title']=$publish[0]['pubh_title'];
                // 用户名
                $map3['user_id']=$value['order_employerid'];
                $user=M('user')->where($map3)->select();
                $value['name']=$user[0]['user_account'];
                // 头像
                $impuser=M('impuser')->where($map3)->select();
                $value['pictrue']=$impuser[0]['impr_picture'];
            }
        }
        // 得到分页
        $show = $page->get_page();
        return array('reply'=>$info,'replyshow'=>$show);

    }
}
