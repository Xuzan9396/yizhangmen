<?php

namespace  Admin\Controller;

use Think\Controller;

	class NeedHallController extends Controller
	{
		/**
		 * [bidSet 根据需求表的数据查询 need 表, 查询 store表,查询 user表,查询store_category表]
		 * @return [type] [description]
		 */
		public  function  bidSet(){
			$bidMode=D('NeedHall');
			$bidlist=$bidMode->displayTab();
			// echo  "<pre>";
			// print_r($bidlist);
			// echo  '</pre>';
			$this->assign('bidlist',$bidlist);
			$this->display('bidSet');
		}
		/**
		 * [bidDel ajax 删除]
		 * @param [type] $[id] [被删除的需求id]
		 * @return [boolen] [成功删除返回1,失败返回0]
		 */
		public  function  bidDel()
		{
			$bidMode=D('NeedHall');
			$this->ajaxReturn($result=$bidMode->bidFileDel());
		}
		/**
		 * [message 查询消息]
		 * @return [type] [查询消息+user表+need表]
		 */
		public  function  message()
		{
			$megMode=D('NeedHall');
			$meglist=$megMode->messageMode();
			// echo '<pre>';
			// print_r($meglist);
			// echo '</pre>';
			$this->assign('meglist',$meglist);
			$this->display('message');
		}
		/**
		 * [bidFileDel 删除留言]
		 * @return [type] [description]
		 */
		public  function  bidmegDel()
		{
			$megsMode=D('NeedHall');
			$this->ajaxReturn($result=$megsMode->bidmegDelFile());
		}

	}
