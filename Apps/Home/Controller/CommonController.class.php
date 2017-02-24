<?php

namespace Home\Controller;

class CommonController extends EmptyController
{
    public function commonTest()
    {
        echo '公共控制器里的公共方法!';
        // echo __METHOD__.'<br>';
    }

    /**
     * [判断是修改还是开店店铺的].
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
    public function commonStore()
    {
        $shop_id = session('store_id');
        // 判断是否有店铺
        if ($shop_id) {
            $sign = [1, '修改店铺'];

            return $sign;
        } else {
            $sign = [0, '免费开店'];

            return $sign;
        }
    }

    /**
     * [登陆成功后把店铺id存到session].
     *
     * @author xuzan<m13265000805@163.com>
     *
     * @param  [type]    描述参数作用
     *
     * @return [type] [description]
     */
    public function _initialize()
    {
        $userId = session('home_user_info')['user_id'];
        $model = M('store');
        $map['store_userid'] = ['eq', $userId];
        $shop_id = $model->where($map)->getField('id');
        session('store_id', $shop_id);

       //查询首页管理状态
        $homemanage = M('Homepagemanage')->order('id desc')->find();

        switch( $homemanage['search'] ){
            case '2':
                $basesearch = false;
            break;

            default:
                $basesearch = true;
            break;
        }

        $this->assign( ['basesearch'=> $basesearch] );
    }
}
