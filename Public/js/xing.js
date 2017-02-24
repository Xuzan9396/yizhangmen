window.onload = function (){

	// 第一个星星开始
	var aStar = document.getElementById("astar");
	var aLi = aStar.getElementsByTagName("li");
	var aUl = aStar.getElementsByTagName("ul")[0];
	var aSpan = aStar.getElementsByTagName("span")[0];
	var aP = aStar.getElementsByTagName("p")[0];
	var ai = iScore = iStar = 0;	
	var aMsg = [
				"很不满意|差得太离谱，与卖家描述的严重不符，非常不满",
				"不满意|部分有破损，与卖家描述的不符，不满意",
				"一般|质量一般，没有卖家描述的那么好",
				"满意|质量不错，与卖家描述的基本一致，还是挺满意的",
				"非常满意|质量非常好，与卖家描述的完全一致，非常满意"
				];

	for (ai = 1; ai <= aLi.length; ai++){
		aLi[ai - 1].index = ai;
		
		//鼠标移过显示分数
		aLi[ai - 1].onmouseover = function (){
			fnPoint1(this.index);
			//浮动层显示
			aP.style.display = "block";
			//计算浮动层位置
			aP.style.left = aUl.offsetLeft + this.index * this.offsetWidth - 104 + "px";
			//匹配浮动层文字内容
			aP.innerHTML = "<em><b>" + this.index + "</b> 分 "
		};
		
		//鼠标离开后恢复上次评分
		aLi[ai - 1].onmouseout = function (){
			fnPoint1();
			//关闭浮动层
			aP.style.display = "none"
		};
		
		//点击后进行评分处理
		aLi[ai - 1].onclick = function (){
			iStar = this.index;
			aP.style.display = "none";
			aSpan.innerHTML = "<strong>" + (this.index) + " 分</strong> (" + aMsg[this.index - 1].match(/\|(.+)/)[1] + ")";
		}
	}
	
	//评分处理
	function fnPoint1(iArg){
		//分数赋值
		iScore = iArg || iStar;
		for (ai = 0; ai < aLi.length; ai++) aLi[ai].className = ai < iScore ? "on" : "";	
	}
	// 第一个星星开结束


	// 第二个星星开始
	var bStar = document.getElementById("bstar");
	var bLi = bStar.getElementsByTagName("li");
	var bUl = bStar.getElementsByTagName("ul")[0];
	var bSpan = bStar.getElementsByTagName("span")[0];
	var bP = bStar.getElementsByTagName("p")[0];
	var bi = iScore = iStar = 0;
	var bMsg = [
				"很不满意|差得太离谱，与卖家描述的严重不符，非常不满",
				"不满意|部分有破损，与卖家描述的不符，不满意",
				"一般|质量一般，没有卖家描述的那么好",
				"满意|质量不错，与卖家描述的基本一致，还是挺满意的",
				"非常满意|质量非常好，与卖家描述的完全一致，非常满意"
				];

	for (bi = 1; bi <= bLi.length; bi++){
		bLi[bi - 1].index = bi;
		
		//鼠标移过显示分数
		bLi[bi - 1].onmouseover = function (){
			fnPoint2(this.index);
			//浮动层显示
			bP.style.display = "block";
			//计算浮动层位置
			bP.style.left = bUl.offsetLeft + this.index * this.offsetWidth - 104 + "px";
			//匹配浮动层文字内容
			bP.innerHTML = "<em><b>" + this.index + "</b> 分 ";
		};
		
		//鼠标离开后恢复上次评分
		bLi[bi - 1].onmouseout = function (){
			fnPoint2();
			//关闭浮动层
			bP.style.display = "none"
		};
		
		//点击后进行评分处理
		bLi[bi - 1].onclick = function (){
			iStar = this.index;
			bP.style.display = "none";
			bSpan.innerHTML = "<strong>" + (this.index) + " 分</strong> (" + bMsg[this.index - 1].match(/\|(.+)/)[1] + ")";
		}
	}
	
	//评分处理
	function fnPoint2(iArg){
		//分数赋值
		iScore = iArg || iStar;
		for (bi = 0; bi < bLi.length; bi++) bLi[bi].className = bi < iScore ? "on" : "";	
	}
	// 第二个星星结束


	// 第三个星星开始

	var cStar = document.getElementById("cstar");
	var cLi = cStar.getElementsByTagName("li");
	var cUl = cStar.getElementsByTagName("ul")[0];
	var cSpan = cStar.getElementsByTagName("span")[0];
	var cP = cStar.getElementsByTagName("p")[0];
	var ci = iScore = iStar = 0;
	var cMsg = [
				"很不满意|差得太离谱，与卖家描述的严重不符，非常不满",
				"不满意|部分有破损，与卖家描述的不符，不满意",
				"一般|质量一般，没有卖家描述的那么好",
				"满意|质量不错，与卖家描述的基本一致，还是挺满意的",
				"非常满意|质量非常好，与卖家描述的完全一致，非常满意"
				];

	for (ci = 1; ci <= cLi.length; ci++){
		cLi[ci - 1].index = ci;
		
		//鼠标移过显示分数
		cLi[ci - 1].onmouseover = function (){
			fnPoint3(this.index);
			//浮动层显示
			cP.style.display = "block";
			//计算浮动层位置
			cP.style.left = cUl.offsetLeft + this.index * this.offsetWidth - 104 + "px";
			//匹配浮动层文字内容
			cP.innerHTML = "<em><b>" + this.index + "</b> 分 "
		};
		
		//鼠标离开后恢复上次评分
		cLi[ci - 1].onmouseout = function (){
			fnPoint3();
			//关闭浮动层
			cP.style.display = "none"
		};
		
		//点击后进行评分处理
		cLi[ci - 1].onclick = function (){
			iStar = this.index;
			cP.style.display = "none";
			cSpan.innerHTML = "<strong>" + (this.index) + " 分</strong> (" + cMsg[this.index - 1].match(/\|(.+)/)[1] + ")";
		}
	}
	
	//评分处理
	function fnPoint3(iArg){
		//分数赋值
		iScore = iArg || iStar;
		for (ci = 0; ci < cLi.length; ci++) cLi[ci].className = ci < iScore ? "on" : "";	
	}
	// 第三个星星结束

};