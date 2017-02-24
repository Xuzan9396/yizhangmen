<?php 
	
	namespace Admin\Model;
	use Think\Model;

	/**
	*仪器库口碑
	*@author YeWeiBin
	**/	
	class WordofmouthPraiseModel extends Model{
		public function getPraiseInfo()
		{
			$res = $this->select();
			return ['info'=> $res];

		}
		public function changeStatus()
		{
			$status = I('get.status');

			switch ($status) {
				case '0':
					$this->appe_status = 0;
					break;
				case '1':
					$this->appe_status = 1;
					break;
				case '2':
					$this->appe_status = 2;
					break;
				case '3':
					$this->appe_status = 3;
					break;				
				default:
					break;
			}
			return $this->where('id='.I('get.id'))->save();
		}
	}