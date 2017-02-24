<?php

namespace Admin\Model;

// namespace Org\Util;

use Think\Model;

/**
 * [店铺数据分配].
 *
 * @author xuzan<m13265000805@163.com>
 *
 * @param  [type]    描述参数作用
 *
 * @return [type] [description]
 */
class StoreModel extends Model
{
    protected $insertFields = array('store_name', 'store_type', 'store_describe', 'store_address', 'store_phone', 'store_caid', 'store_realname', 'store_due', 'store_qq', 'store_email');
    protected $_validate = array(
        ['store_name', 'require', '店铺名不能为空', 1, 'regex', 3],
        ['store_name', '1,10', '店铺名称不能超过10位', 1, 'length', 3],
        ['store_type', 'number', '只能注册企业和个人店铺', 1, 'regex', 3],
        ['store_describe', '1,200', '店铺的描述不能超过200个字', 2, 'length', 3],
        ['store_address', 'require', '地址不能为空', 1, 'regex', 3],
        ['store_phone', '/^1[34578][0-9]\d{4,8}$/', '手机号码错误！', 1, 'regex', 3],
        // ['store_caid']
        // array('mobile','/^1[3|4|5|8][0-9]\d{4,8}$/','手机号码错误！','0','regex',1)

    );
    public function storeLst()
    {
        $model = $this->select();
        $listType = ['个人', '企业'];
        $listStastus = ['下架', '上架'];

        foreach ($model as $key => &$value) {
            $value['store_type'] = $listType[$value['store_type']];
            $value['cate_status'] = $listStastus[$value['cate_status']];
        }

        return $model;
    }
}
