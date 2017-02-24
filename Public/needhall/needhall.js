
	/**
	 * [td_click 当类点击中出现子类]
	 * @param  {[type]} id   [点击类的ID]
	 * @param  {[type]} pid  [点击类的PID]
	 * @param  {[type]} path [点击类的PATH]
	 * @param  {[type]} obj  [this]
	 * @param  {[type]} url  [查询控制器]
	 * @return {[type]}      [void]
	 */
	// $('.panel-body').children(':eq(1)').children(':first').attr({'num':1});
	$('.panel-body').click(function(){
		$(this).next().toggle(500);
		var num=$(this).children(':eq(1)').children(':first').attr("num");
		if(num==1){
			$(this).children(':eq(1)').children(':first').attr({"num":0});
			$(this).children(':eq(1)').children(':first').attr({"class":"glyphicon glyphicon-minus"});
		}
		else
		{
			$(this).children(':eq(1)').children(':first').attr({"num":1});
			$(this).children(':eq(1)').children(':first').attr({"class":"glyphicon glyphicon-plus"});
		}
	});
	// var tmp=1;
	// function tr_click(id,obj){
		
	// 	$('#tr_'+id).slideToggle(300);
	// 	//var temp=$('#tr_'+id).css("display");
	// 	console.log(tmp);
	// 	if (tmp==1) {
	// 			tmp=2;
	// 			$(obj).children().attr({class:'glyphicon glyphicon-minus'});
	// 		}else{
	// 			$(obj).children().attr({class:'glyphicon glyphicon-plus'});
	// 			tmp=1;
	// 		}
	// }
	// $('.tr_click').click(function(){
	// 	$('.tr_onclick').slideToggle(1000);
	// });
	// $('.tr_click').click(function(){
	// 	if (tmp) {
	// 			tmp=false;
	// 			$(this).children().eq(1).children().attr({class:'glyphicon glyphicon-minus'});
	// 			$(this).next().css({display:"block"});
	// 			// $(this).paafter("<tr class="+'tr_click'+"><td>hello</td><td style="+'cursor:pointer;'+">hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td></tr>");
	// 		}else{
				
	// 			$(this).children().eq(1).children().attr({class:'glyphicon glyphicon-plus'});
	// 			$(this).next().css({display:"none"});
	// 			//$(this).next().remove();
	// 			tmp=true;
	// 		}
	// });
	// function tr_click(obj){
	// 	if (tmp) {
	// 			tmp=false;
	// 			$(obj).children().attr({class:'glyphicon glyphicon-minus'});
	// 			// $(this).next().css({visibility:"hidden"});
	// 			 $(obj).parent().after("<tr class="+'tr_click'+"><td>hello</td><td style="+'cursor:pointer;'+">hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td></tr>");
	// 		}else{
				
	// 			$(obj).children().attr({class:'glyphicon glyphicon-plus'});
	// 			// $(this).next().css({visibility:"visible"});
	// 			$(obj).parent().next().remove();
	// 			tmp=true;
	// 		}
	// }
	// $(".tr_click > td").eq(1).click(function(){
	// 	if (tmp) {
	// 			tmp=false;
	// 			$(this).children().attr({class:'glyphicon glyphicon-minus'});
	// 			// $(this).next().css({visibility:"hidden"});
	// 			 $(this).parent().after("<tr class="+'tr_click'+"><td>hello</td><td style="+'cursor:pointer;'+">hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td></tr>");
	// 		}else{
				
	// 			$(this).children().attr({class:'glyphicon glyphicon-plus'});
	// 			// $(this).next().css({visibility:"visible"});
	// 			$(this).parent().next().remove();
	// 			tmp=true;
	// 		}
	// });
	// .each(function(data){
	// 	var tmp=true;
	// 	$(this).click(function(){
	// 		if (tmp) {
	// 			tmp=false;
	// 			$(this).children().eq(1).children().attr({class:'glyphicon glyphicon-minus'});
	// 			// $(this).next().css({visibility:"hidden"});
	// 			 $(this).paafter("<tr class="+'tr_click'+"><td>hello</td><td style="+'cursor:pointer;'+">hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td></tr>");
	// 		}else{
				
	// 			$(this).children().eq(1).children().attr({class:'glyphicon glyphicon-plus'});
	// 			// $(this).next().css({visibility:"visible"});
	// 			$(this).next().remove();
	// 			tmp=true;
	// 		}
			
	// 	});
	// });
	function td_click(id,obj,url)
	{
		
		// 1.DOM 创建元素
		
	    //var odiv = document.createElement('tr');
	    // odiv.title = '动态创建的DIV';
	    // odiv.id = 'ball';
	    // // 类名
	    // odiv.className = 'c1';
	    // // 标签之间的内容
	     //odiv.innerHTML = '标签之间的内容';
	    // // 获取标签名
	    // var res = odiv.tagName;


	    // 2.添加 xxx.appendChild();
	    // var box = document.getElementById('box');
	    // // 添加一个子元素
	    // box.appendChild(odiv);

	    // 对象是引用传递
	    // odiv.style.border = '2px solid blue';

	    // 为body体添加一个子元素
	    // 对象是唯一的，这仅仅是剪切的效果
	    // document.body.appendChild(odiv);

	    // 默认添加到最后
	   // $(obj).parent().after("<tr><td>hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td><td>hello</td></tr>");
	}
	// function tr_sav(id,obj,url1,url2)
	// {
	// 	alert(1);
	// }
	/**
	 * [del 删除类]
	 * @param  {[type]} id   [description]
	 * @param  {[type]} obj  [description]
	 * @param  {[type]} url1 [description]
	 * @param  {[type]} url2 [description]
	 * @return {[type]}      [0:无效操作,1:有数据,不能删除;2:成功删除;3:删除失败]
	 */
	function tr_del(id,obj,url1,url2)
	{
			//console.log(id+':'+url1)
			
			$.post(url1,{'del_id':id},function(data){
				if(data==1){
					alert('有数据,不能删除');
				}else if(data==2){
					var rel=confirm('确定删除？');
					//var get_data='';
					if(rel){
						alert('删除成功');
						$(obj).parent().parent().remove();
					}
				}else if (data==3){
					alert('删除失败');
				}else{
					alert('无效操作');
				}
			});
				//重新查询重建保持页的数目不变
				// var page=$('#page_class').children().children('.current').html();
				// $.getJSON(url2,{'getdata':get_data,'pg':page},function(data){
				// 	if(data){
				// 		// console.log(data);
				// 	var tel='<td>'+data.sere_id+'</td><td>'+data.sere_pid+'</td><td>'+data.sere_name+'</td><td>'+data.sere_path+'</td><td><button class="btn-primary">添加</button> <button class="btn-info">修改</button> <button onclick="javascript:void( del('+data.sere_id+',this,'+'\''+url1+'\''+','+'\''+url2+'\''+'))"   class="btn-danger">删除</button></td>'; 
				// 	$('table').append('<tr>'+tel+'</tr>');
				// 	}
				// });
			//}
	}
//==============================================================================
//$(document).ready(function(){
	/**
	 * [description]
	 * @param  {[type]} ){	} [description]
	 * @return {[type]}        [description]
	 */
	//$('#inputsave').focusout(function(){
			// alert(1);
	//});
//});
	
