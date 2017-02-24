$().ready(function(){	
	// 设置状态
	var stat = true;
	// 点击事件
	$('#form-budget-a').click(
		function(){
		console.log($('#exampleInputAmount').val(''));
		//如果ture就执行下面
		if(stat){
			// css 更改
			$('#form-select').css({
				'display' : 'block'
			});

			$('#form-input').css({
				'display' : 'none'
			});
			//每次切换name改变
			$('#exampleInputAmount').attr('name','need_budget1');
			$('#jselect').attr('name','need_budget');
			//a标签内容更改
			$(this).html('有明确预算');
			// 在转为false
			stat = false;
		
		}else{
			// false 的时候以下操作
			$('#form-input').css({
				'display' : 'block'
			});

			$('#form-select').css({
				'display' : 'none'
			});
			// name改变
			$('#exampleInputAmount').attr('name','need_budget');
			$('#jselect').attr('name','need_budget1');
			//a标签内容更改
			$(this).html('无明确预算');
			// 再变为true
			stat = true;
		}

	});
	// 调用进度条js
	$(function(){
		stepBar.init("stepBar", {
			// 目标进度
			step : 2,
			// 插件是否炒作
			change : false,
			// 是否动画
			animation : false,
			// 时间
			speed : 500,
			
			
		});
	});

	// 按钮下拉
	$('#jexplain-btn').click(function(){
		$('.box-explain').slideToggle("slow");
	});

	// 日期
	layui.use('laydate', function(){

		var laydate = layui.laydate;

		// 当前年份
		var year = new Date().getFullYear();
		var month = new Date().getMonth(); 
		var day = new Date().getDate();
		var end = {
			min: laydate.now(+7)
			// 默认最大一年 当前时间加一年
			,max: year + 1 + '-'+month+'-'+day+ '23:59:59'
			,istoday: false
			,choose: function(datas){
			// start.max = datas; //结束日选好后，重置开始日的最大日期
			}
		};

		document.getElementById('LAY_demorange_e').onclick = function(){
			end.elem = this
			laydate(end);
		}

	});

});
