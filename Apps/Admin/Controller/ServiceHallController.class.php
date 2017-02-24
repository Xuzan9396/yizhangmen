<?php
namespace  Admin\Controller;
use Think\Controller;
	/**
	 * 需求大厅中的分类
	 */
	class ServiceHallController extends Controller
	{
		/**
		 * [index 接收数据库中的所属类别]
		 * @return [type] [浏览控制器]
		 */
	    public function index()
	    {
	    	// echo  __METHOD__.'<br>';
	    	$p='';
	    	if(!empty(I('get.p')))
	    	{
	    		$p=I('get.p')+0;
	    	}
	        $class_list= D('ServiceHall');
	        $list=$class_list->serviceDisplay();
	  //       $Page       = new \Think\Page($class_list->count(),5);
			// $show       = $Page->show();// 分页显示输出
			// $service_list=$class_list->where('sere_pid=0')->limit($Page->firstRow.','.$Page->listRows)->select();
	  //       $list['list']=$service_list;
	  //       $list['page']=$show;
	  //       
	  		// echo '<pre>';
	  		// print_r($list);
	  		// echo '</pre>';
	       	$this->assign($list);
	       	$this->assign('p',$p);
	        $this->display("index");
	    }
	    /**
	     * [add 服务分类首层添加]
	     */
	    public function addDisplay()
	    {
	    	$class_Mode= D('ServiceHall');
	    	$list=$class_Mode->getModeData(sere_pid,0);
	    	$this->assign('list',$list);
	    	$this->display("add");
	    }
	    /**
	     * [insert 添加分类]
	     * @return [type] [description]
	     */
	    public function insert ()
	    {
	    	$data=[];
	    	$post=I('post.');
	    	// echo '<pre>';
	    	// print_r($post);
	    	// echo '</pre>';
	    	// exit();
	    	$data['sere_name']=$post['class_name']; 
	    	$data['sere_pid']=$post['class_id'];
	    	//$data['sere_id']=$post['class_id'];
	    	$class_Mode=D('ServiceHall');
	    	if($class_Mode->create($data))
	    	{	//验证通过
	    		//如果$pid==0,则加载的是顶层分类
	    		//如果$pid!=0,则加载的是顶层分类以下的分类,2,3....无限级
	    		$reslute=$class_Mode->where("sere_id={$data['sere_pid']}")->select();
	    		if ($data['sere_pid']) {
	    			$data['sere_path']=$reslute[0]['sere_path'].$data['sere_pid'].',';
	    		}else{
	    			$data['sere_path']='0,';
	    		}
	    		$reslute=$class_Mode->data($data)->add();
	    		if($reslute)
	    		{
	    			 //$this->success('新增成功', U('Admin/ServiceHall/index'));
	    			 //
	    			 $this->ajaxReturn(1);
	    		}else {
	    		    //错误页面的默认跳转页面是返回前一页，通常不需要设置 
	    		   //$this->error('新增失败');
	    		    $this->ajaxReturn(0);
	    		}
	    	}
	    	else//验证不通过
	    	{
	    		exit($class_Mode->getError());
	    	}
	    }
	    /**
	     * [editDiplay 显示修改的页面]
	     * @return [type] [无返回]
	     */
	    public  function editDiplay()
	    {
	    	$saveId = I('get.sav_id');
	    	$class_Mode= D('ServiceHall');
	    	$list=$class_Mode->getModeData(sere_id,$saveId);
	    	$this->assign('list',$list);
	    	$this->display('editDiplay');
	    }
		/**
	     * [sav 修改名子]
	     * @return [type] [返回受影响行]
	     */
	    public function editsav ()
	    {
	    	$saveId = I('post.class_id');
	    	$save_Name = I('post.class_name');
	    	$class_Mode= D('ServiceHall');
	    	$data['sere_name']="$save_Name";
	    	 //echo $saveId;
	    	 //echo $save_Name;
	    	 // exit();
	    	// $reslute=$class_Mode->where("sere_id={$saveId}")->save("sere_name='{$save_Name}'");
	    	// exit();
	    	$reslute=$class_Mode->getModeData('sere_name','\''.$save_Name.'\'');
	    	if(count($reslute)!=0){
	    		$reslute=2;
	    		$this->error('重复类名');
	    	}else{
	    		$reslute=$class_Mode->where("sere_id={$saveId}")->save($data);
	    		if($reslute){
	    			$this->success('修改成功',U('Admin/ServiceHall/index'));
	    			//exit();
	    		}else{
	    			$reslute=0;
	    			$this->error('修改失败');
	    		}
	    	}
	    	
	    }
	    /**
	     * [savegetData ajax查询修改名子是否有重复]
	     * @return [type] [description]
	     */
	    public function saveGetData()
	    {
	    	$save_Name=I('post.save_name');
	    	$class_Mode= D('ServiceHall');
	    	$reslute=$class_Mode->getModeData('sere_name','\''.$save_Name.'\'');
	    	if(count($reslute)==0){
	    		$reslute=0;
	    	}else{
	    		$reslute=1;
	    	}
	    	$this->ajaxReturn($reslute);
	    }
	   

	    /**
	     * [delete 删除分类]
	     * @return [type] [ajax删除0:初始状态;1:有数据,不能删除;2:成功删除;3:删除失败]
	     */
	    public function del()
	    {
	    	$reslute=0;//初始状态
	    	$del_id=I('post.del_id');
	    	$class_list= D('ServiceHall');
	    	$reslute=$class_list->getModeData(sere_pid,$del_id);
	    	if ($reslute) {
	    		$reslute=1;//有数据,不能删除
	    	}else{
	    		$reslute=$class_list->where("sere_id={$del_id}")->delete(); 
	    		if($reslute){
	    			$reslute=2;//删除成功
	    		}else{
	    			$reslute=3;//删除失败
	    		}
	    	}
	    	$this->ajaxReturn($reslute);
	    }
	    /**
	     * [findIndex 检测并输出分类数据信息]
	     * @return [type] [输出分类数据信息]
	     * @return [type] [如果分类中有子类则返回子类,如果没有子类则返回零]
	     */
	    public function findIndex(){
	    	$sere_id=I('post.sere_id');
	    	$class_Mode=D('ServiceHall');
	    	//$sere_path=$class_Mode->field('sere_path')->where("sere_id={$sere_id}")->select();
	    	//$sere_str=$sere_path[0]['sere_path'];
	    	$sere_son=$class_Mode->where("sere_pid={$sere_id}")->select();
	    	// echo '<pre>';
	    	// dump($sere_son);
	    	// echo '</pre>';
	    	if(empty($sere_son[0])){
	    		$this->ajaxReturn(0);
	    	}else{
		    	$sere_son=json_encode($sere_son);
		    	$this->ajaxReturn($sere_son);
	    	}
	    }
	}
