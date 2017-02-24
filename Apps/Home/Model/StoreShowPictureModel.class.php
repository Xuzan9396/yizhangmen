<?php

namespace Home\Model;

use Think\Model;

class StoreShowPictureModel extends Model
{
    /**
     * @author 胡金矿<1968346304@qq.com>
     * [pictureHandler 处理店铺展示图]
     * @return [type] [返回处理结果标志信息]
     */
  	public function pictureHandler()
  	{
  		$config=array(
  	        'maxSize'   =>     3145728 ,// 设置附件上传大小
  	        'exts'      =>     array('jpg','png','gif', 'jpeg'),// 设置附件上传类型
  	        'savePath'  => 'store-carousel/' // 设置附件上传目录
          );
  		$arr = [];
      $imgaeType=['image/png','image/jpeg','image/gif','image/jpg'];
      $length=count($_FILES['pic']['tmp_name']);
      for($i=0;$i<$length;$i++){
          if(in_array($type[$i]=getimagesize($_FILES['pic']['tmp_name'][$i])['mime'],$imgaeType)){
              $arr[$i]=$type[$i];
          }
      }
      
      $allow_length=count($arr);
     
      if($allow_length <= 4 && $allow_length > 0){
          $upload = new \Think\Upload($config);// 实例化上传类
          $info= $upload->upload($_FILES);
          if($info){
              $storeid=session('store_id');
              $data['shopid']=$storeid;
              foreach($info as $key=>$file){
                  $data['store_pic'.$key]=$file['savepath'].$file['savename'];
              }
              $map['store_shopid']=['eq',$storeid];
              $getRes=$this->where($map)->find();
              
              if($getRes){
                  $saveRes=$this->where($map)->save($data);
                  if($saveRes == false){
                    $result=0;
                    return $result;
                  }else{
                    for($i=0; $i< (count($data)-2); $i++){
                        @unlink('./Public/Uploads/'.$getRes['store_pic'.$i]);
                    }
                    $result=1;
                    return $result;
                  }

              }else{
                  $insertid=$this->add($data);
                  if($insertid){
                    $result=2;
                    return $result;
                  }else{
                    $result=0;
                    return $result;
                  }
              }
              
          }else{
              $result=0;
              return $result;
          }
      }else{
          $result=0;
          return $result;
      }
  	}
}
