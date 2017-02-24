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
class StoreCaseModel extends Model
{
    // 更新时候
    protected $insertFields = array('case_orderid', 'case_shop', 'case_title', 'case_scheme', 'case_cover');
    // 修改时候
    protected $updateFields = array('id', 'case_orderid', 'case_shop', 'case_title', 'case_scheme',  'case_cover');
    protected $_validate = array(
            array('case_shop', 'require', '店铺名称不能为空', 1, 'regex', 3),
            array('case_orderid', 'number', '非法操作', 1, 'regex', 3),
            array('case_title', '5,20', '店铺案例标题在5-20个字符之间', 1, 'length', 3),
            array('case_scheme', '1,2000', '店铺描述在1-2000个字符之间', 1, 'length', 3),
    );
    /**
     * @author   胡金矿]
     * [getCaseData 案例列表]
     * @return [array] [返回案例列表数组]
     */
    public function getCaseData()
    {
      $storeid=I('storeid');
      $map['case_shop']=['eq',$storeid];
      $list=$this->where($map)->select();
      return $list;
    }

    public function getCaseDetailData()
    {
      
      $storeId=I('storeid');
      $caseId=I('caseid');
      if($storeId!=0 && $caseId!=0){
          $map['id']=['eq',$caseId];
          $map['case_shop']=['eq',$storeId];
          $list = $this->where($map)->select();
          $order=M('store_order');
          $mapo['id']=['eq',$list[0]['case_orderid']];
          $orderlist=$order->where($mapo)->find();
          $publish=M('publish');
          $pubMap['id']=['eq',$orderlist['order_serviceid']];
          $publish=$publish->where($pubMap)->getField('pubh_categoryid');
          $third=explode(',',$publish);
          $serCate=M('store_category');
          $cateMap['id']=['eq',$third[2]];
          $cateName=$serCate->where($cateMap)->getField('cate_name');
          $caseImage=M('store_caseimages');
          $mapp['case_id']=['eq',$caseId];
          $images=$caseImage->where($mapp)->select();
      }
      return ['list'=>$list,'cateName'=>$cateName,'order'=>$orderlist,'image'=>$images];
    }
    // 添加之前
    protected function _before_insert(&$data, $option)
    {
        // 上传案例logo
        // 
        if (isset($_FILES['case_cover']) && $_FILES['case_cover']['error'] == 0) {
            $ret = uploadOne('case_cover', 'store/caseLogo');

            if ($ret['ok'] == 1) {
                $data['case_cover'] = $ret['images'][0];
            } else {
                $this->error = $ret['error'];

                return false;
            }
        } else {
            switch ($_FILES['case_cover']['error']) {
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

    // 更新之前
    protected function _before_update(&$data, $option)
    {
        $map['case_orderid'] = array('eq', $data['case_orderid']);
        $field = $this->where($map)->getField('case_cover');
        if ($field) {
            @unlink(C('IMG_rootPath').$field);
        }
        if (isset($_FILES['case_cover']) && $_FILES['case_cover']['error'] == 0) {
            $ret = uploadOne('case_cover', 'store/caseLogo');

            if ($ret['ok'] == 1) {
                $data['case_cover'] = $ret['images'][0];
            } else {
                $this->error = $ret['error'];

                return false;
            }
        } else {
            switch ($_FILES['case_cover']['error']) {
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

    //添加之后
    protected function _after_insert($data, $option)
    {
        /************************* 处理商品图片的代码 ***********************/
        // 判断有没有图片
        if (hasImage('case_images')) {
            $gpModel = M('StoreCaseimages');
            // 批量上传之后的图片数组，改造成每个图片一个一维数组的形式
            $pics = array();
            foreach ($_FILES['case_images']['name'] as $k => $v) {
                if ($_FILES['case_images']['size'][$k] == 0) {
                    continue;
                }
                $pics[] = array(
                    'name' => $v,
                    'type' => $_FILES['case_images']['type'][$k],
                    'tmp_name' => $_FILES['case_images']['tmp_name'][$k],
                    'error' => $_FILES['case_images']['error'][$k],
                    'size' => $_FILES['case_images']['size'][$k],
                );
            }
            //重新复制给$_FILES
            $_FILES = $pics;
            // 循环每张图片一个一个的上传
            foreach ($pics as $k => $v) {
                $ret = uploadOne($k, 'store/caseImages');
                if ($ret['ok'] == 1) {
                    $gpModel->add(array(
                        'case_id' => $data['id'],
                        'case_images' => $ret['images'][0],     // 原图存到pic字段
                    ));
                }
            }
        }

        // 添加之后把字段变成1
        $storeOrder = M('StoreOrder');
        $map1['id'] = ['eq', $data['case_orderid']];
        $storeOrder->where($map1)->setField('order_sign', 1);
    }
    // 分配数据 
    public function caseSelect()
    {
        if (I('get.id')) {
            $map['case_orderid'] = ['eq', I('get.id')];
            $data = $this->where($map)->find();
        }

        return $data;
    }

   // 更新之后
   protected function _after_update($data, $option)
   {
       $model = M('StoreCaseimages');
       $map['case_id'] = $data['id'];
       $list = $model->field('case_images')->where($map)->select();
       if ($list) {
           $array = [];
           foreach ($list as $key => $val) {
               $array[] = $val['case_images'];
           }
           deleteImage($array);
           $sign = $model->where($map)->delete();
           if ($sign) {
               /************************* 处理商品图片的代码 ***********************/
               // 判断有没有图片
               if (hasImage('case_images')) {
                   $gpModel = M('StoreCaseimages');
                   // 批量上传之后的图片数组，改造成每个图片一个一维数组的形式
                   $pics = array();
                   foreach ($_FILES['case_images']['name'] as $k => $v) {
                       if ($_FILES['case_images']['size'][$k] == 0) {
                           continue;
                       }
                       $pics[] = array(
                           'name' => $v,
                           'type' => $_FILES['case_images']['type'][$k],
                           'tmp_name' => $_FILES['case_images']['tmp_name'][$k],
                           'error' => $_FILES['case_images']['error'][$k],
                           'size' => $_FILES['case_images']['size'][$k],
                       );
                   }
                   $_FILES = $pics;
                   // 循环每张图片一个一个的上传
                   foreach ($pics as $k => $v) {
                       $ret = uploadOne($k, 'store/caseImages');
                       if ($ret['ok'] == 1) {
                           $gpModel->add(array(
                               'case_id' => $data['id'],
                               'case_images' => $ret['images'][0],     // 原图存到pic字段
                           ));
                       }
                   }
               }
           }
       }
   }
}
