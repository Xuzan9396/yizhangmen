<?php
     /**
     * [打印数据，项目用于做调试用的，之后可去掉]
     * @author xuzan<m13265000805@163.com>
     * @param  [type]    描述参数作用
     * @return [type] [description]
     */

    function p($arr)
    {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }

      /**
       * [上传的方法，可以调用这个方法上传].
       *
       * @author xuzan<m13265000805@163.com>
       *
       * @param  [type]    描述参数作用
       *
       * @return [type] [description]
       */

      /**
       * 上传图片并生成缩略图
       * 使用的用法：
       * $ret = uploadOne('logo', 'Goods', array(
       *		array(600, 600),
       *			array(300, 300),
       *			array(100, 100),
       *		));
       *	返回值：
       *	if($ret['ok'] == 1)
       *		{
       *			$ret['images'][0];   // 原图地址
       *			$ret['images'][1];   // 第一个缩略图地址
       *			$ret['images'][2];   // 第二个缩略图地址
       *			$ret['images'][3];   // 第三个缩略图地址
       *		}
       *		else
       *		{
       *			$this->error = $ret['error'];
       *			return FALSE;
       *		}.
       */
      function uploadOne($imgName, $dirName,  $sign = 0 ,$thumb = array())
      {
          // 上传LOGO
          if (isset($_FILES[$imgName]) && $_FILES[$imgName]['error'] == 0) {
              $rootPath = C('IMG_rootPath');
              $upload = new \Think\Upload(array(
                  'rootPath' => $rootPath,
              ));// 实例化上传类
              $upload->maxSize = (int) C('IMG_maxSize') * 1024 * 1024;// 设置附件上传大小
              if($sign == 0){
                  $upload->exts = C('IMG_exts');// 设置附件上传类型
              } else if($sign == 1){
                  $upload->exts = C('IMG_ex');
              }
              /// $upload->rootPath = $rootPath; // 设置附件上传根目录
              $upload->savePath = $dirName.'/'; // 图片二级目录的名称
              // 上传文件
              // 上传时指定一个要上传的图片的名称，否则会把表单中所有的图片都处理，之后再想其他图片时就再找不到图片了
              $info = $upload->upload(array($imgName => $_FILES[$imgName]));
              if (!$info) {
                  return array(
                      'ok' => 0,
                      'error' => $upload->getError(),
                  );
              } else {
                  $ret['ok'] = 1;
                  $ret['images'][0] = $logoName = $info[$imgName]['savepath'].$info[$imgName]['savename'];
                  // 判断是否生成缩略图
                  if ($thumb) {
                      $image = new \Think\Image();
                      // 循环生成缩略图
                      foreach ($thumb as $k => $v) {
                          $ret['images'][$k + 1] = $info[$imgName]['savepath'].'thumb_'.$k.'_'.$info[$imgName]['savename'];
                          // 打开要处理的图片
                          $image->open($rootPath.$logoName);
                          $image->thumb($v[0], $v[1])->save($rootPath.$ret['images'][$k + 1]);
                      }
                  }

                  return $ret;
              }
          }
      }

      // 显示图片
      function showImage($url, $width = '', $height = '')
      {
          $url = '/shop/Public/Uploads/'.$url;
          if ($width) {
              $width = "width='$width'";
          }
          if ($height) {
              $height = "height='$height'";
          }
          echo "<img src='$url' $width $height/>";
      }
      // 删除图片：参数：一维数组：所有要删除的图片的路径
      function deleteImage($images)
      {
          // 先取出图片所在目录
        $rp = C('IMG_rootPath');
          foreach ($images as $v) {
              // @错误抵制符：忽略掉错误,一般在删除文件时都添加上这个
          @unlink($rp.$v);
          }
      }

      function hasImage($imgName)
      {
          foreach ($_FILES[$imgName]['error'] as $v) {
              if ($v == 0) {
                  return true;
              }
          }

          return false;
      }

      /**
       * [分页类].
       *
       * @author xuzan<m13265000805@163.com>
       *
       * @param  [type]    描述参数作用
       *
       * @return [type] [description]
       */
      function myPage($model, $map=array(), $arr = 3)
      {
          // 实例化分页类
          // import('@.Class.Page'); //引入Page类
          import('Org.Util.Page'); //引入Page类
          // 查询满足要求的总记录数
          $count = $model->where($map)->count();
          /*进行第三方分页类配置*/
          $page = array(
              'total' => $count, /*总数（改）*/
              'url' => !empty($param['url']) ? $param['url'] : '', /*URL配置*/
              'max' => !empty($param['max']) ? $param['max'] : $arr, /*每页显示多少条记录（改）*/
              'url_model' => 1, /*URL模式*/
              'ajax' => !empty($param['ajax']) ? true : false, /*开启ajax分页*/
              'out' => !empty($param['out']) ? $param['out'] : false, /*输出设置*/
              'url_suffix' => true, /*url后缀*/
              'tags' => array('首页', '上一页', '下一页', '尾页'),
          );
          /*实例化第三方分页类库*/
         return $page = new \Page($page);
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

       function down($filePath){

         // 1.指定要下载的文件
         // $filePath = './document/2.png';

         // 2.获取MIME类型
         $finfo = finfo_open(FILEINFO_MIME_TYPE);
         // var_dump($finfo);
         $mime = finfo_file($finfo , $filePath);
         finfo_close($finfo);

         // 3.指定文件下载的类型
         header('content-type:' . $mime);
         // header('content-type:image/jpeg');

         // 4.告知浏览器，本次请求带有附件，并指定客户端下载的名字
         header('Content-Disposition:attachment;filename=' . basename($filePath));

         // 5.指定文件大小
         header('content-length:' . filesize($filePath));

         // 6.直接输出
         readfile($filePath);
       }
       
      /**
       * [description].
       *
       * @author LinHao<137987537@qq.com>
       *
       * @param  array  $data         [所有权限]
       * @param  int $id           [父id]
       *
       * @return array  $list              [已经分类的权限]
       */
      function toRecursion($data, $id = 0)
      {
          $list = [];
          foreach ($data as $v) {
              if ($v['jurn_pid'] == $id) {
                  $v['son'] = toRecursion($data, $v['jurn_id']);
                  if (empty($v['son'])) {
                      unset($v['son']);
                  }
                  if (session('adminLogin')['admn_id'] && empty($v['son']) && $v['jurn_url'] == '') {
                      unset($v);
                  } else {
                      array_push($list, $v);
                  }
              }
          }

          return $list;
      }

      /**
       * [description].
       *
       * @author YangJun<15818708414@163.com>
       *
       * @return string 上传的文件名
       */
      function saveName()
      {
          $save_name = md5(time().mt_rand(1, 999999999));

          return $save_name;
      }

      /**
       * 金君<757258777@qq.com>
       * 递归遍历
       * @return 返回数组
       */
      function needCateList($data, $id=0)
      {
        $list = [];
        foreach($data as $val) {
          if($val['parent_id'] == $id) {
            $val['son'] = needCateList($data, $val['id']);
            if(empty($val['son'])) {
              unset($val['son']);
            }
            array_push($list, $val);

          }
        }
        return $list;
      }


      /**
       * 简单对称加密算法之加密
       * @param String $string 需要加密的字串
       * @param String $skey 加密EKY
       * @return String
       */
      function encode($string = '', $skey = 'cxphp') {
          $strArr = str_split(base64_encode($string));
          $strCount = count($strArr);
          foreach (str_split($skey) as $key => $value)
              $key < $strCount && $strArr[$key].=$value;
          return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
      }

      /**
       * 简单对称加密算法之解密
       * @param String $string 需要解密的字串
       * @param String $skey 解密KEY
       * @return String
       */
      function decode($string = '', $skey = 'cxphp') {
          $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
          $strCount = count($strArr);
          foreach (str_split($skey) as $key => $value)
              $key <= $strCount  && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
          return base64_decode(join('', $strArr));
      }
    /**
     * [测试是否是数字或者数字字符串]
     * wenzhonghua@163.com
     * @param  [type] $data [数字或者数字字符串]
     * @return [boolen]       [true/false]
     */
    function checkPrice($data)
    {
      if(is_numeric($data))
      {
        return true;
      }
      else
      {
        return false;
      }
    }

    /**
    *将汉字转换成拼音
    */
    function Pinyin($_String, $_Code='gb2312')
    {
      $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha".
      "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|".
      "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er".
      "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui".
      "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang".
      "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang".
      "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue".
      "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne".
      "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen".
      "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang".
      "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|".
      "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|".
      "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu".
      "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you".
      "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|".
      "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
      $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990".
      "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725".
      "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263".
      "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003".
      "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697".
      "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211".
      "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922".
      "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468".
      "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664".
      "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407".
      "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959".
      "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652".
      "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369".
      "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128".
      "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914".
      "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645".
      "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149".
      "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087".
      "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658".
      "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340".
      "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888".
      "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585".
      "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847".
      "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055".
      "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780".
      "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274".
      "|-10270|-10262|-10260|-10256|-10254";
      $_TDataKey = explode('|', $_DataKey);
      $_TDataValue = explode('|', $_DataValue);
      $_Data = (PHP_VERSION>='5.0') ? array_combine($_TDataKey, $_TDataValue) : _Array_Combine($_TDataKey, $_TDataValue);
      arsort($_Data);
      reset($_Data);
      if($_Code != 'gb2312') $_String = _U2_Utf8_Gb($_String);
      $_Res = '';
      for($i=0; $i<strlen($_String); $i++)
      {
      $_P = ord(substr($_String, $i, 1));
      if($_P>160) { $_Q = ord(substr($_String, ++$i, 1)); $_P = $_P*256 + $_Q - 65536; }
      $_Res .= _Pinyin($_P, $_Data);
      }
      return preg_replace("/[^a-z0-9]*/", '', $_Res);
      }
      function _Pinyin($_Num, $_Data)
      {
      if ($_Num>0 && $_Num<160 ) return chr($_Num);
      elseif($_Num<-20319 || $_Num>-10247) return '';
      else {
      foreach($_Data as $k=>$v){ if($v<=$_Num) break; }
      return $k;
      }
      }
      function _U2_Utf8_Gb($_C)
      {
      $_String = '';
      if($_C < 0x80) $_String .= $_C;
      elseif($_C < 0x800)
      {
      $_String .= chr(0xC0 | $_C>>6);
      $_String .= chr(0x80 | $_C & 0x3F);
      }elseif($_C < 0x10000){
      $_String .= chr(0xE0 | $_C>>12);
      $_String .= chr(0x80 | $_C>>6 & 0x3F);
      $_String .= chr(0x80 | $_C & 0x3F);
      } elseif($_C < 0x200000) {
      $_String .= chr(0xF0 | $_C>>18);
      $_String .= chr(0x80 | $_C>>12 & 0x3F);
      $_String .= chr(0x80 | $_C>>6 & 0x3F);
      $_String .= chr(0x80 | $_C & 0x3F);
      }
      return iconv('UTF-8', 'GB2312', $_String);
      }
      function _Array_Combine($_Arr1, $_Arr2)
      {
      for($i=0; $i<count($_Arr1); $i++) $_Res[$_Arr1[$i]] = $_Arr2[$i];
      return $_Res;
    }

    //设置一个方法查询好评率
   function storefcomment( $id ){
      if( $id ){
        $services = M('Publish')->where( 'pubh_shopid='.$id )->field('id')->select();
        
        //查询服务下所有订单
        $orders['order'] = [];
        foreach( $services as $key => $val ){
          $map['order_serviceid'] = $val['id'];
          $order = M('Store_order')->where( $map )->field('id')->select();

          if( $order ){
            $orders['order'][] = $order;
            $orders['ordernum'] += count($order);
            $map1['order_serviceid'] = $val['id'];
            $map1['order_status'] = 4;
            $deal = M('Store_order')->where( $map1 )->field('order_number_price')->select();
            $orders['dealnum'] += count( $deal );
            foreach( $deal as $k => $v ){
              $orders['dealprice'] += $v['order_number_price'];
            }
          }

        };
        
        //查询订单下所有评价
        foreach( $orders['order'] as $key => $val ){
          foreach( $val as $k => $v ){
            $map2['order_id'] = $v['id'];
            $employer = M('Employer_comment')->where( $map2 )->field('star_service_attitude,star_work_speed,star_finish_quality')->select();
            if( $employer ){
              $orders['employer'][] = $employer;
            }
          }
          
        }

        //统计评价

        $i = 0;
        $j = 0;
        $gcom = 0;
        foreach( $orders['employer'] as $key => $val ){
          $i += count( $val );
          foreach( $val as $k => $v ){
            $j += (($v['star_service_attitude'] + $v['star_work_speed'] + $v['star_finish_quality']) / 3) /5;
            if( $v['star_service_attitude'] >= 4 || $v['star_work_speed'] >= 4 || $v['star_finish_quality'] >= 4 ){
              $gcom ++;
            }
          }
        } 

        $fcomment = sprintf( "%.2f", $j / $i ) * 100;
      }

      //$fcomment好评率，$comnum评价个数,$gcom好评个数,$dealnum成交量，$dealprice成交总价格
      return ['id'=>$id,'fcomment'=>$fcomment,'comnum'=>$i,'gcom'=>$gcom,'dealnum'=>$orders['dealnum'],'dealprice'=>number_format(round($orders['dealprice']))];
    }

    //算出时间差
   function difference( $time ){
        if( $time ){
          $now = date('Y/m/d H:i:s',time());
          $last = date( 'Y/m/d H:i:s', $time );

          $date=floor((strtotime($now)-strtotime($last))/86400);
          $hour=floor((strtotime($now)-strtotime($last))%86400/3600);
          $minute=floor((strtotime($now)-strtotime($last))%86400/60);
          $second=floor((strtotime($now)-strtotime($last))%86400%60);
        }
        
        return ['date'=>$date,'hour'=>$hour,'minute'=>$minute,'second'=>$second];
      }