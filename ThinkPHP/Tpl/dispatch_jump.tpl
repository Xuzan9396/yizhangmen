<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>跳转提示</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
body{ background: #fff; font-family: '微软雅黑'; color: #00AACF; font-size: 16px;}
.system-message{ padding-top:50; text-align:center;}
.system-message .jump{ padding-top: 10px}
.system-message .jump a{ color: #333; text-decoration:none;}
.system-message .success,.system-message .error{ line-height: 1.8em; font-size: 24px }
.system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display:none}
.bgimg{width:128px;height:128px;background-image:url('__PUBLIC__/image/loading/156c74154c26fd405e5477da3afe47e1.gif'); background-repeat:no-repeat;margin:0 auto;margin-top:100px;}
</style>
</head>
<body>
<div class="system-message">
<div class="bgimg"></div>
<?php if(isset($message)) {?>
<p class="success"><?php echo($message); ?></p>
<?php }else{?>
<p class="error"><?php echo($error); ?></p>
<?php }?>
<p class="detail"></p>
<p class="jump">
自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b>
</p>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>
