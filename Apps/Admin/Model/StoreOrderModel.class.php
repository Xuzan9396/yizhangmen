<?php

namespace Admin\Model;

use Think\Model;

class StoreOrderModel extends Model
{
	public function orderHandle ()
	{
		$orderData = $this->select();
		return $orderData;
	}
}
