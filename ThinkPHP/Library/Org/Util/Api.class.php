<?php
namespace Org\Util;

class Api
{
    protected $apikey = 'a73853940b9a0467cb2364d9ebd7b4ef';

    public function myApi($url)
    {
        $ch = curl_init();
        $header = array(
                'apikey:'.$this->apikey,
            );
            // 添加apikey到header
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // 执行HTTP请求
            curl_setopt($ch, CURLOPT_URL, $url);
        $res = curl_exec($ch);
        $result = json_decode($res, true);

        return $result;
    }

    public function idCard($identity)
    {
        $ch = curl_init();
          $url = 'http://apis.baidu.com/chazhao/idcard/idcard?idcard='.$identity;
          $header = array(
              'apikey:'.$this->apikey,
          );
          // 添加apikey到header
          curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          // 执行HTTP请求
          curl_setopt($ch , CURLOPT_URL , $url);
          $res = curl_exec($ch);
          $result = json_decode($res, true);
          return $result;
    }

    

    //服务商： 小刚刚API (个人) 所属分类： 工具类、快速开发、开发组件、翻译 更新时间： 2016-06-03
    public function hanYing( $str )
    {

        $url = 'http://apis.baidu.com/xiaogg/changetopinyin/topinyin?str='.$str.'&type=json&traditional=0&accent=0&letter=0&only_chinese=0';
       
        return $this->myApi( $url );

    }

    //获取外网IP
    public function ip()
    {
        $showapi_appid = '32402';  //替换此值,在官网的"我的应用"中找到相关值
        $showapi_secret = 'f884c8945f124a6fb9172c0323fd47a0';  //替换此值,在官网的"我的应用"中找到相关值
        $paramArr = array(
         'showapi_appid'=> $showapi_appid,
         //添加其他参数
        );
     
      //创建参数(包括签名的处理)
      function createParam ($paramArr,$showapi_secret) {
           $paraStr = "";
           $signStr = "";
           ksort($paramArr);
           foreach ($paramArr as $key => $val) {
               if ($key != '' && $val != '') {
                   $signStr .= $key.$val;
                   $paraStr .= $key.'='.urlencode($val).'&';
               }
           }
           $signStr .= $showapi_secret;//排好序的参数加上secret,进行md5
           $sign = strtolower(md5($signStr));
           $paraStr .= 'showapi_sign='.$sign;//将md5后的值作为参数,便于服务器的效验
           return $paraStr;
      }
       
      $param = createParam($paramArr,$showapi_secret);
       $url = 'http://route.showapi.com/632-1?'.$param; 

      $result = file_get_contents($url);
      $res = json_decode($result, true)['showapi_res_body'];
      $address = $res['country'].$res['city'].$res['county'].','.$res['region'].$res['isp'];
      
      return $address;
    }
}
