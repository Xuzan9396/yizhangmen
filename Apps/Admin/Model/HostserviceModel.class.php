<?php
	namespace Admin\Model;
	use Think\Model;

	class HostserviceModel extends Model
	{

		public function getDate()
		{

			$services = M('Publish')->select();
		}

	}