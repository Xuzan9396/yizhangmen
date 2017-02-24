<?php
namespace Home\Controller;
use Think\Controller;
class TradeController extends CommonController {
    public function index()
    {
        $news = D('news');
        $res = $news->showList();
        if(IS_GET){
            $this->assign('news',$res);
            $this->display();
        }
    }

    public function ajax()
    {
        $news = D('news');
        $res = $news->ajaxshowList();
        echo $res;

    }

    public function article()
    {
        $news = D('news');
        $res = $news->showNews();
        $this->assign('news',$res);
        $this->display();
    }

    public function check()
    {
    	$code = I('post.code');
    	$id = '';
        $verify = new \Think\Verify();
        $res = $verify->check($code, $id);
        echo $res;
    }
}
