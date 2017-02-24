<?php

namespace  Admin\Controller;

/**
 * [店铺的信息类].
 *
 * @author xuzan<m13265000809@163.com>
 *
 * @param  [type]    描述参数作用
 *
 * @return [type] [description]
 */
class StoreOpenController extends  SmsController
{
    public function storeList()
    {
        if (IS_POST) {
            $id = I('post.id');
            $where['id'] = ['eq', $id];
            $model = M('store');
            $arr = $model->where($where)->find();
            // 修改的地方
            $arr['store_addtime'] = date('Y/m/d h:i:s', time());
            $this->ajaxReturn($arr);
        }
        $model = D('store');
        $store = $model->storeLst();
        $this->assign('data', $store);
        $this->display();
    }




    public function storeStatus()
    {
        $res = I('get.id');
        $model = M('store');
        $email = I('get.email');
        $data['toemail'] = $email;
        $data['title'] = '店铺审核';
        // $data['content'] = '店铺审核通过';
        $str = $model->field('cate_status')->find($res);
        $arr['id'] = array('eq', $res);
        $model->where($arr)->save(array('cate_status' => 1));


        if ($str['cate_status'] == 0) {
            $model->where($arr)->save(array('cate_status' => 1));
            $data['content'] = '店铺审核通过';
           $bool =  $this->smtp($data);

            $this->ajaxReturn(array(
                's' => '上架',
                't' => 1,
                'bool'=>$bool,
            ));
        } else {
            $model->where($arr)->save(array('cate_status' => 0));
            $data['content'] = '店铺审核不通过';
            $this->smtp($data);
            $this->ajaxReturn(array(
                's' => '下架',
                't' => 0,
            ));
        }


    }

    public function StoreOrder()
    {
        $model = M('StoreOrder');
        $list = $model->select();
        $this->assign('list', $list);
        // p($list);
        // 0：待付款，1：订单已经取消 ，2：退款中,3:退款成功，4：已付款, 5:托管, 6:线下,7:服务商发起合同,8:双方签署合同,9雇主拒签合同,10:工作完毕上传原件'
        $type = array('待托管','订单已经取消','退款中','退款成功','已付款','托管','线下','服务商发起合同','双方签署合同','雇主拒签合同','工作完毕上传原件');
        foreach ($list as $key => &$value) {
            $value['order_status'] = $type[$value['order_status']];
        }
        $this->display();
    }

}
