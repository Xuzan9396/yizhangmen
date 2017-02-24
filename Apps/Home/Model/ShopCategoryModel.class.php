<?php

namespace home\Model;

use Think\Model;

/**
 * [店铺的分类所属分类].
 *
 * @author xuzan<m13265000805@163.com>
 *
 * @param  [type]    描述参数作用
 *
 * @return [type] [description]
 */
class ShopCategoryModel extends Model
{
    protected $insertFields = array('cate_firstid', 'cate_secondid', 'cate_threeid');
    protected $updateFields = array('id','cate_firstid', 'cate_secondid', 'cate_threeid');
    protected $_validate = array(
    array('cate_firstid', 'number', '请合法提交！', 1, 'regex', 3),
    array('cate_secondid', 'number', '请合法提交！', 1, 'regex', 3),
    );
    protected function _before_insert(&$data, $option)
    {
       $data['cate_shopid'] = session('store_id');
    }
    /**
     * @author 胡金矿<1968346304@qq.com>
     * [shopCateId 查询店铺分类]
     * @return [type] [分类ID]
     */
    public function shopCateId()
    {
        $map['cate_shopid'] = ['eq', session('store_id')];
        $shopcateId = $this->where($map)->getField('id');
        return $shopcateId;
    }
     /**
     * @author 胡金矿<1968346304@qq.com>
     * [shopDataHandler 获取对应需求的信息]
     * @return [array] [description]
     */
    public function shopDataHandler()
    {
        $shopid=session('store_id');
        $map['cate_shopid'] = ['eq',$shopid];
        $secondid = $this->where($map)->getField('cate_secondid');//获取店铺二级分类id
        if($secondid){
            $needObj=M('need');
            $mapNeed['need_catepid']=['eq',$secondid];
            $mapNeed['need_valid_status']=['eq',0];
            $mapNeed['need_status']=['eq',3];
            $page=myPage($needObj,$mapNeed,8);
            //查询所有满足要求的需求
            $list = $needObj->where($mapNeed)->order('need_time desc')->limit($page->pagerows(),$page->maxrows())->select();
            if($list){
                $bid=M('bid');
                foreach ($list as $key => $value) {
                    $bidMap['bid_needid']=['eq',$value['need_id']];
                    $bidIdList=$bid->where($bidMap)->select();
                    if(empty($bidIdList)){
                        $list[$key]['bidNum']=0;
                    }else{
                       $list[$key]['bidNum']=count($bidIdList);
                    }
                }
                $show=$page->get_page();
           }
        }
        return ['list'=>$list,'show'=>$show];
    }

    /**
     * @author 胡金矿<1968346304@qq.com>
     * [needDataHandler 官方推荐订单列表数据]
     * @return [type] [description]
     */
    public function needDataHandler()
    {
        $shopid=session('store_id');
        $map['shopid'] = ['eq',$shopid];
        $map['status'] = ['eq',1];
        $needService=M('need_service');

        $needid=$needService->field("group_concat(needid) needid")->where($map)->select();
        if($needid[0]['needid']){
            $need=M('need');
            $mapp['need_id']=['in',$needid[0]['needid']];
            $page=myPage($need,$mapp,10);
            //查询所有满足要求的需求
            $result = $need->where($mapp)->limit($page->pagerows(),$page->maxrows())->select();
             $show=$page->get_page();
            return $result;
        }
        
    }

    /**
     * @author 胡金矿<1968346304@qq.com>
     * [officalDetail 官方推荐订单的详细数据]
     * @return [type] [description]
     */
    public function officalDetail()
    {
        $id=I('get.id');
        $map['need_id']=['eq',$id];
        $need=M('need');
        $list=$need->where($map)->find();
        if($list){
            $bid=M('bid');
            $bidMap['bid_needid']=['eq',$list['need_id']];
            $bidIdList=$bid->where($bidMap)->select();
            $cate=M('store_category');
            $cateMap['id']=['eq',$list['need_cateid']];
            $cateName=$cate->where($cateMap)->getField('cate_name');
            if(empty($bidIdList)){
                $list[$key]['bidNum']=0;
            }else{
               $list['bid_num']=count($bidIdList);

            }
            if($cateName){
                $list['cate_name']=$cateName;
            }else{
                $list['cate_name']='未知';
            }
        }
        return $list;
    }

}
