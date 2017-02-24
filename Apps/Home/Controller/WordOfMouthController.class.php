<?php 
	
	namespace Home\Controller;
	use Think\Controller;

	class WordOfMouthController extends Controller{

		public function index()
		{
			$praise = D('WordofmouthPraise');
			$info = $praise->getPraiseInfo();

			if( I('get.type') !=null ){
				redirect( U('WordOfMouth/index/id/'.I('get.id').'/order/'.I('get.type') ) );
			}

			$this->assign($info);
			$this->display();
		}

		public function ajaxAddComment()
		{
			$comment = D('WordofmouthComment');
			$res = $comment->showComment();

			$data = '';

			$data .='<div class="mouth-pinlun">';

			$data .=	'<textarea name="mouth-content" id="mouth-textarea" cols="101" rows="5" placeholder="输入评论..."></textarea>';
			$data .=	'<div class="mouth-pinlun-sub"><span><span class="comment-limit">0</span>/200字</span><div onclick="submitComment(this,'. I('get.id') .');">提交评论</div></div>';

			foreach ($res as $key => $val) {
				
			$data .=	'<div class="mouth-pinlun-main">';
			$data .=		'<div>';
			$data .=			'<img src="/shop/public/image/wordofmouth/icon.png" alt="">';
			$data .=			'<p class="mouth-pinlun-name">'. $val['user_account'].'</p>';
			$data .=			'<span class="mouth-pinlun-time">'. date('Y-m-d H:i:s' , $val['appt_ctime'] ) .'</span>';
			$data .=		'</div>';
			$data .=		'<div class="mouth-pinlun-content">'. $val['appt_content'] .'</div>';
			$data .=	'</div>';
			
			}

			$data .='</div>';

			echo $data;
		}

		public function ajaxsubmitComment()
		{	
			$comment = D('WordofmouthComment');
			$res = $comment->submitComment();

			if($res){

				$newcomment = D('WordofmouthComment');
				$resule = $newcomment->getNewComment($res);

				$data = '';		
				$data .=	'<div class="mouth-pinlun-main">';
				$data .=		'<div>';
				$data .=			'<img src="/shop/public/image/wordofmouth/icon.png" alt="">';
				$data .=			'<p class="mouth-pinlun-name">'. $_SESSION['home_user_info']['user_account'] .'</p>';
				$data .=			'<span class="mouth-pinlun-time">1秒钟前</span>';
				$data .=		'</div>';
				$data .=		'<div class="mouth-pinlun-content">'. $resule['appt_content'] .'</div>';
				$data .=	'</div>';

				echo $data;
			}

		}

		public function add()
		{
			$goods = D('instrument_goods');
			$res = $goods->getGoodsName();

			$cate = D('instrument_category');
			$topcate = $cate->getTopCate();

			$data=['topcate' => $topcate];

			$this->assign($data);
			$this->display();
		}

		public function ajaxGetChlid()
		{
			$cate = D('instrument_category');
			$childcate = $cate->getSecondCate();
					
			if( !$childcate ){

				$goods = D('instrument_goods');
				$childgoods = $goods->getGoodsName();	

				if( !$childgoods ) return false;

				// 三级分类遍历仪器
				$data =	'<select class="instrument-name instru-goodsname" name="instrument_goodsname">';
				
				$data .=	'<option value="">--请选择仪器--</option>';
				
				foreach ($childgoods as $key => $val) {

				$data .=	'<option value="' . $val['appt_id'] .'">'. $val['appt_goodsname'].'</option>';

				}

				$data .=	'</select>';

			}else{

				// 三级以上继续遍历分类
				$data =	'<select class="instrument-name" name="instrument_name">';
				
				$data .=	'<option value="">--请选择--</option>';
				
				foreach ($childcate as $key => $val) {

				$data .=	'<option value="' . $val['id'] .'">'. $val['cate_name'].'</option>';

				}

				$data .=	'</select>';

			}
			echo $data;
		}

		public function toAdd()
		{
			$praise = D('WordofmouthPraise');
			if( $res = $praise->handle() ){
				$this->success('口碑已发表成功',U('Home/WordOfMouth/myPraise/uid/'.$_SESSION['home_user_info']['user_id']));
			}
		}

		public function ajaxAddBrowse()
		{
			$praise = D('WordofmouthPraise');
			$praise->addBrowse();
		}

		public function ajaxdelPraise()
		{
			$praise = D('WordofmouthPraise');
			echo $praise->delPraise();
		}
		
		public function myPraise()
		{
			$praise = D('WordofmouthPraise');

			if( I('get.type') !=null ){
				redirect(U('WordOfMouth/myPraise/uid/'.I('get.uid').'/order/'.I('get.type')));
			}
			$res = $praise->getPersonPraise();
			$this->assign($res);
			$this->display();
		}

	}