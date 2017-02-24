<?php 
		
	namespace Admin\Model;
	
	use Think\Model;

	class NewsModel extends Model{
		public function addNews()
		{
			$data['apps_title'] = I('post.apps_title');
			$data['apps_summary'] = I('post.apps_summary');
			$data['apps_author'] = I('post.apps_author');
			$data['apps_cid'] = I('post.apps_cid');
			$data['apps_ctime'] = time();
			$data['apps_content'] = $_POST['apps_content'];

			$res = $this->add($data);

			preg_match_all("/<img([^>]*)\s*src=('|\")([^'\"]+)[.jpg]('|\")/",str_ireplace("\\","",$data['apps_content']),$arr);

			$arr = array_unique($arr[3]);

			foreach ($arr as $key => $val) {
				$pics = M('news_pics');
				$map['apps_pid'] = $res;
				$map['apps_path'] = $val.'g';
				$pics->add($map);
			};

			return $res;
		}

		public function showNews(){

			$res = $this->select();

			$cate = M('news_category')->field('appy_name')->select();
			$status = ['下线','显示中'];
			foreach ($res as $key => &$val) {
				$val['apps_cid'] = $cate[$val['apps_cid']-1]['appy_name'];
				$val['apps_status'] = $status[$val['apps_status']];
			}

			return $res;
		}
	}