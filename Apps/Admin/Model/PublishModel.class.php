<?php

namespace Admin\Model;

use Think\Model;

class PublishModel extends Model
{
	public function getServiceData()
	{
		$pagenum=isset( $_GET['mypage'] ) ? $_GET['mypage']+0 : 10;
		$start = strtotime(isset( $_GET['startTime'] ) ? $_GET['startTime']:'');
        $end = strtotime(isset( $_GET['endTime'] ) ? $_GET['endTime']:'');
        //交易状态
        $tradeStatus = isset($_GET['tradeStatus']) ? $_GET['tradeStatus']+0 : 3 ;
        if(in_array($tradeStatus,array(0,1,2,3))){
            if($tradeStatus==3){
               $map['pubh_status']=['in',array('0','1','2')];
            }else{
                $map['pubh_status']=['eq',$tradeStatus];
            }
        }else{
            $map['pubh_status']=['in',array('0','1','2')];
        }
        //交易时间条件
        if($start && $end){
            $map['pubh_time']=['between',array($start,$end)];
        }else if($start){
            $map['pubh_time'] = ['egt', $start];
        }else if($end){
            $map['pubh_time'] = ['elt', $end];

        }
        
        //搜索框条件
        $content=I('get.pubh_title');
        if($content!=''){
            $map['pubh_title']=['like','%'.$content.'%'];
        }
		$page=myPage($this,$map,$pagenum);
        $list = $this->where($map)->order('pubh_time desc')->limit($page->pagerows(),$page->maxrows())->select();
        $show=$page->get_page();
		$status = ['待审核', '审核不通过','审核通过'];
        foreach ($list as &$value) {
            $value['pubh_status'] = $status[$value['pubh_status']];
        }
        return ['list'=>$list,'show'=>$show];
	}
	public function checkService()
	{

		$id=I('post.service_id');
		$serviceName=I('post.service_name');
		$map['id']=['eq',$id];
		$status=I('post.pubh_status');
		$content=I('post.faie_reason');
		$data['pubh_status']=$status;

		$list=$this->where($map)->save($data);

		if($list){
			//审核成功
			$storeId=$this->where($map)->getField('pubh_shopid');
			$store=M('store');
			$storeMap['id']=['eq',$storeId];
			$detailInfo=$store->where($storeMap)->find();
			$userMap['user_id']=$detailInfo['store_userid'];
			$user=M('user');
			$userName=$user->where($userMap)->getField('user_account');//得到用户名
			$storeEmail=$detailInfo['store_email'];
			$arr['toemail']=$storeEmail;
			if($storeEmail){
				$arr['title']='服务审核结果';//邮件标题
				if($status==1){
					$arr['content']='很遗憾,您的服务:'.$serviceName.',由于'.$content.'导致审核失败';
					$check['status']=1;
				}elseif($status==2){
					$arr['content']='恭喜你,您的服务:'.$serviceName.',审核成功';
					$check['status']=2;
				}

			}else{
				//邮箱为空发送手机通知短信
				// $check['status']=3;
			}
			//发送站内消息
			$msgData['mesm_sender']='系统消息';
			$msgData['mesm_receiver']=$userName;
			$msgData['mesm_title']='服务审核结果';
			$msgData['mesm_centent']=$arr['content'];
			$msgData['mesm_type']=1;
			$msgData['mesm_sendtime']=time();
			$message=M('messagesystem');
			$msgRes=$message->add($msgData);
			if($msgRes){
				$check['infonum']=3;
			}else{
				$check['infonum']=4;
			}
		}elseif($list===false){
			// //审核失败
			$check['status']=0;
		}
		return ['arr'=>$arr,'check'=>$check];
	}
	//短信发送
	public function smsPhone($phoneData)
    {
        $tplOperator = new \Org\Util\sms\TplOperator();
        $result = $tplOperator->get_default(array("tpl_id"=>'2'));

        // 发送单条短信
        $smsOperator = new \Org\Util\sms\SmsOperator();
        // $data['mobile'] = I('post.store_phone'); // 手机号码
        // $data['text'] = '您的验证码是' . $code; // 发送短信内容
        // $result = $smsOperator->single_send($data);
        $result = $smsOperator->single_send($phoneData);
        $result = json_encode($result);
        $result = json_decode($result,true);

        if($result['success']){
            $res['status'] = true;
            $res['error_info'] = $result['responseData']['msg'];
            //发送短信成功就把验证码加密后存储到session
            // session('sms_code',password_hash($code , PASSWORD_DEFAULT));
        }else{
            $res['status'] = false;
            $res['error_info'] = $result['responseData']['detail'];
        }

        return $res;
    }
}
