<?php
    namespace Home\Model;

    use Think\Model;

    class NewsModel extends Model{

        public function showNews()
        {   
            $map['apps_id']=['eq',I('get.id')];

            $res = $this->where($map)->select();

            $cate = M('news_category')->field('appy_name')->select();

            $res[0]['apps_cid'] = $cate[$res[0]['apps_cid']-1]['appy_name'];

            return $res[0];
        }

        public function showList(){

            $cid = I('get.cid',1);

            $map['apps_cid']=['eq',$cid];

            $res = $this->order('apps_ctime desc')->limit(18)->where($map)->select();

            return $res;

        }

        public function ajaxshowList(){

            $res1 = $this->showList();
            $res ='<dl class="list-body">';

            foreach ($res1 as $key => $val) {
            
            $res.=        '<dd class="list-item">';
            $res.=            '<ul class="list-item-body">';
            $res.=                "<li class='w8'><a href='article/id/".$val['apps_id']."' class='list-title' title='"."{$val['apps_summary']}'>";
            $res.=                $val['apps_title'].'</a></li>';
            $res.=                '<li class="w2 text-right"><time class="mr_10"></time></li>';
            $res.=           '</ul>';
            $res.=        '</dd>';

            }
            $res.=    '</dl>';
            return $res;
        }
    }