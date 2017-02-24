<?php

	namespace Admin\Model;

	use Think\Model;

	class AttestationModel extends Model{

		public function handler(){
			return $userList = $this->select();
		}
		
	}
