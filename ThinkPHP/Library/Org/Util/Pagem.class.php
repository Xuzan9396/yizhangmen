<?php
	namespace Org\Util;
	class Pagem
	{

		protected $firstRow = 0;
		protected $lastRow = 0;
		protected $totalRow = 0;
		protected $totalPage = 0;
		protected $currentUrl = '';
		protected $paramGet = array();
		public $style;

		public function __construct( $count, $lastRow )
		{

			$this->lastRow = $lastRow;
			$this->totalRow = $count;

			$_GET['p'] = isset( $_GET['p'] ) ? $_GET['p'] : $_POST['p'];
			$this->paramGet = $_GET;
			
			$this->init();
			$this->style();

		}

		protected function init()
		{

			$this->totalPage = ceil($this->totalRow / $this->lastRow);

			$p = isset( $this->paramGet['p'] ) ? $this->paramGet['p'] : 1;
			$p = max( $p, 1 );
			$p = min( $p, $this->totalPage );
			$p = ceil( $p );

			$this->paramGet['p'] = $p;
		
			$this->firstRow = ( $p - 1 ) * $this->lastRow;

		}

		protected function createUrl()
		{

			$param = explode( '/', $_SERVER['SERVER_PROTOCOL'] )[0];
			$param = strtolower( $param ) . "://";

			$this->currentUrl = $param . $_SERVER['SERVER_NAME'] . $_SERVER['REDIRECT_URL'];
	
		}

		protected function build( $p )
		{

			$http = array_merge( $this->paramGet, $p );
			$http = http_build_query( $http );

			return $this->currentUrl . '?' . $http;

		}

		public function show()
		{

			$this->createUrl();

			$btn = '';

			$btn .= $this->style; 

			$btn .= "<div class='pageshow'><div>共 " . $this->totalRow . " 条数据, ";

			$btn .= " 当前 " . $this->paramGet['p'] . '/' . $this->totalPage . " 页</div> ";

			$url = $this->build( [ 'p' => 1 ] );
			$btn .= "<div><a href = '{$url}' p = '1' > 首页 </a>";

			$p = $this->paramGet['p'] - 1;
			$p = max( $p, 1 );
			$url = $this->build( [ 'p' => $p ] );
			$btn .= "<a href = '{$url}' p = '{$p}' > 上一页 </a></div><div><ul>";

			$start = $this->paramGet['p'] - 3;
			$start = max( $start, 1 );

			$end = $this->paramGet['p'] + 3;
			$end = min( $end, $this->totalPage );

			for( $i = $start; $i <= $end; $i ++ ){
				$url = $this->build( [ 'p' => $i ] );
				if( $i == $this->paramGet['p'] ){
					$btn .= "<li><span> {$i} </span></li>";
				}else{
					$btn .= "<a href = '{$url}' p = '{$i}'><li> {$i}</li></a>";
				}
			}

			$p = $this->paramGet['p'] + 1;
			$p = min( $p, $this->totalPage );
			$url = $this->build( [ 'p' => $p ] );
			$btn .= "</ul></div><div><a href = '{$url}' p = '{$p}'>下一页 </a>";

			$url = $this->build( [ 'p' => $this->totalPage ] );
			$btn .= "<a href = '{$url}' p = '{$this->totalPage}'> 尾页 </a></div></div>";

			return $btn;

		}

		public function __get( $key )
		{
			
			return $this->$key;

		}

		protected function style()
		{

			$this->style =  '<style>.pageshow{width:800px;height: 30px;line-height: 30px;}
				.pageshow a{text-decoration: none;}
				.pageshow div:nth-of-type(1){width:180px ;height: 30px;float: left;}
				.pageshow div:nth-of-type(2){width:90px ;height: 30px;float: left;}

				.pageshow div:nth-of-type(2) a:nth-of-type(1),.pageshow div:nth-of-type(4) a:nth-of-type(2){width:35px;height:30px;background: lightgreen;display: block;border-radius: 5px;color:white;float: left;text-align: center;}
				.pageshow div:nth-of-type(2) a:nth-of-type(1):hover{text-decoration: none;background: lightblue;font-size: 15px;}
				.pageshow div:nth-of-type(4) a:nth-of-type(2):hover{text-decoration: none;background: lightblue;font-size: 15px;}
				.pageshow div:nth-of-type(2) a:nth-of-type(2),.pageshow div:nth-of-type(4) a:nth-of-type(1){width: 50px;height: 30px;background: lightgreen;display: block;border-radius: 5px;color:white;text-align: center;float: left;}
				.pageshow div:nth-of-type(2) a:nth-of-type(2):hover{text-decoration: none;background: lightblue;font-size: 15px;}
				.pageshow div:nth-of-type(4) a:nth-of-type(1):hover{text-decoration: none;background: lightblue;font-size: 15px;}

				.pageshow div:nth-of-type(3){width:auto;height: 30px;float: left;margin-right: 10px;}
				.pageshow div:nth-of-type(4){width:90px;height: 30px;float: left;}

				.pageshow ul{padding: 0;margin: 0;float: left;}
				.pageshow li{width:30px;height: 30px;line-height: 30px;list-style: none;float: left;border:1px solid orange;text-align: center;cursor: pointer;border-radius: 5px;color:white;background:#FFE800;margin-left:5px;}
				.pageshow li:hover{background:#FF6700;animation: glow 1s ease-out infinite alternate;}
				.pageshow li span{width:30px;height: 30px;line-height: 30px;text-align: center;display: block;cursor: not-allowed;border-radius: 5px;font-size: 18px;font-weight: bold;animation: glowm 2s ease-out infinite alternate;}
				@keyframes glowm{0%{box-shadow: 0 0 2px 1px pink;}100%{box-shadow: 0 0 10px 2px red;background: orange;font-size: 20px;}}</style>';

		}

		
	}
?>