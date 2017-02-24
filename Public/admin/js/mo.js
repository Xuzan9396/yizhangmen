;(function(window){


		function mo(){
			
			var div = '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span></button><h4 class="modal-title" id="myModalLabel">标题</h4></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">关闭</button><button type="button" class="btn btn-primary" id="msave">删除</button></div></div></div></div><div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true"><div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title" id="myModalLabel"></h4></div><div class="modal-body"></div><div class="modal-footer"></div></div></div></div>';

			$('body').append(div);
			this.msg = function(msg,title,url){
				title = title ? title : '提示';
				$('.bs-example-modal-sm .modal-body').html(msg);
				$('.bs-example-modal-sm .modal-title').html(title);
				$('.bs-example-modal-sm').modal('show');
				setTimeout(function(){
					$('.bs-example-modal-sm').modal('hide');
				},1500)

				if(url){
					redirect(url);
				}
			}

		
			this.confirm = function(msg,callBack,title,but){
                but = but ? but : '删除';
				title = title ? title : '标题';
    			$('#myModal .modal-body').html(msg);
    			$('#myModal .modal-title').html(title);
                $('#msave').html(but);
    			$('#myModal').modal('show');   		
    			$('#msave')[0].onclick = function() {
    				$('#myModal').modal('hide');
    				callBack();

    			}
		      }

            this.mod = function(content,callBack,title,but){
                but = but ? but : '保存';
                title = title ? title : '标题';
                $('#myModal .modal-title').html(title);
                $('#myModal .modal-body').html(content);
                $('#msave').html(but);
                $('#myModal').modal('show');
                $('#msave')[0].onclick = function(){
                    $('#myModal').modal('hide');
                   callBack();
                 }

            }

           this.ajax = function ajax(data,url,callBack){
                var timer = setTimeout(function(){
                    layer.load(3);
                },2000)
                $.ajax({
                    data:data,
                    type:'post',
                    url:url,
                    success:function(res){
                        callBack(res);
                        if(res){
                            layer.closeAll('loading');
                            clearTimeout(timer);
                        }
                    }
                })
             }

            //ajax上传文件
             this.ajaxUp =  function ajaxUp(formData,url,callBack){
                $.ajax({
                    data:formData,
                    type:'post',
                    async:true,
                    cache:false,
                    dataType:'JSON',
                    contentType:false,
                    processData:false,
                    url:url,
                    success:function(res){
                       callBack(res);
                    }
                })
            }

            function redirect(url,time){
                time = time ? time : 1;
                time = time * 1000;
                url = url ? url : '';
                setTimeout(function(){
                    window.location.href = url;
                },time) 
            }

	}

	 window.mo = new mo();
	  

   })(window); 

        

