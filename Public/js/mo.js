;(function(window){


		function mo(){
			
			var div = '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span></button><h4 class="modal-title" id="myModalLabel">标题</h4></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">关闭</button><button type="button" class="btn btn-primary msave">删除</button></div></div></div></div><div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true"><div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title" id="myModalLabel"></h4></div><div class="modal-body"></div><div class="modal-footer"></div></div></div></div>';

			$('body').append(div);

			this.msg = function(msg,url){
				$('.bs-example-modal-sm .modal-body').html(msg);
				$('.bs-example-modal-sm .modal-title').html('提示');
				$('.bs-example-modal-sm').modal('show');
				setTimeout(function(){
					$('.bs-example-modal-sm').modal('hide');
				},1500)

				if(url){
					redirect('');
				}
			}

		
			this.confirm = function(msg,callBack,title,sta){
				title = title ? title : '标题';
                sta = sta ? sta : '删除';
    			$('#myModal .modal-body').html(msg);
    			$('#myModal .modal-title').html(title);
                $('.msave').html(sta);
    			$('#myModal').modal('show');   		
    			$('.msave')[0].onclick = function() {
    				$('#myModal').modal('hide');
    				callBack();

    			}
		      }

            this.mod = function(content,callBack,title,sta){
                title = title ? title : '标题';
                sta = sta ? sta : '保存';
                $('.msave').html(sta);
                $('#myModal .modal-title').html(title);
                $('#myModal .modal-body').html(content);
                $('.msave').html(sta);
                $('#myModal').modal('show');
                $('.msave')[0].onclick = function(){
                    $('#myModal').modal('hide');
                   callBack();
                 }

            }

           this.ajax = function (data,url,callBack){
                if(window.layer){
                    var timer = setTimeout(function(){
                        layer.load(3);
                    },2000)
                }
                $.ajax({
                    data:data,
                    type:'post',
                    url:url,
                    success:function(res){
                        callBack(res);
                      
                            if(window.layer){
                                layer.closeAll('loading');
                                clearTimeout(timer);
                            
                        }
                    }
                })
             }

            //ajax上传文件
             this.ajaxUp =  function(formData,url,callBack){
                if(layer){
                    var timer = setTimeout(function(){
                        layer.load(3);
                    },2000)
                }
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
                      
                            if(layer){
                                layer.closeAll('loading');
                                clearTimeout(timer);
                            }
                        
                    }
                })
            }

            function redirect(url){
                url = url ? url : '';
                setTimeout(function(){
                    window.location.href = url;
                },1000) 
            }

            this.skip = function(url,num){
                url = url ? url : '';
                num = num ? num * 1000: 1000;
                setTimeout(function(){
                    window.location.href = url;
                },num) 
            }
	}

	 window.mo = new mo();
	  

   })(window); 

        

