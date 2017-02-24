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
class AccessoryModel extends Model
{
    protected $_validate = array(
        ['order_id', 'require', '请填写正确的订单Id', 1, 'regex', 3],
        ['order_id', 'number', '请填写正确的订单Id', 1, 'regex', 3],
    );

    protected function _before_insert(&$data, $option)
    {

      $data['order_small'] = round($_FILES['order_file']['size']/1024/1024,4);
      $data['addtime'] = time();
        // 上传上传附件
          if (isset($_FILES['order_file']) && $_FILES['order_file']['error'] == 0) {
              $ret = uploadOne('order_file', 'store/accessory' , 1);

              if ($ret['ok'] == 1) {
                  $data['order_url'] = $ret['images'][0];
              } else {
                  $this->error = $ret['error'];

                  return false;
              }
          } else {
              switch ($_FILES['order_file']['error']) {
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
