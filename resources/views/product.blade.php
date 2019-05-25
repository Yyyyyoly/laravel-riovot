<!doctype html>
<html>
<meta charset="utf-8">
<meta name="viewport"
	content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1" />
<meta name="format-detection" content="telephone=no,email=no,date=no,address=no" />
<title>睿沃特福利专区</title>
<link type="text/css" href="{{ asset('/css/font-awesome.min.css') }}" rel="stylesheet">
<link type="text/css" href="{{ asset('/css/common.css') }}" rel="stylesheet">
<link type="text/css" href="{{ asset('/css/product.css') }}" rel="stylesheet">
<script>
	var product_list = JSON.parse(decodeURIComponent("{!! rawurlencode(json_encode($product_list)) !!}"));
	var fake_list = JSON.parse(decodeURIComponent("{!! rawurlencode(json_encode($fake_list)) !!}"));
	var hashId = '{{$admin_hash_id}}'
</script>

<body>
	<div class="product">
		<div class="product-top">
			<div class="p-head">
				<div class="p-head-desc">你选择了我，我定会尽己所能，做好我的服务！不会随便给承诺，但一旦承诺就会办到！</div>
				<div class="p-head-right">—致有资金需求的客户朋友</div>
				<div class="p-head-logen">睿沃特</div>
			</div>
			<div class="scroll-show">
				<i class="icon-bell-alt"></i>
				<div class="scroll-list" id="scroll_list">
				</div>
			</div>
			<div class="p-title">
				<div class="p-title-record">
					<div class="record-left hide">
						<i class="icon-angle-left"></i>
					</div>
				</div>
				<div class="p-title-text" id="tab_list">
					<span>无视黑白，人人有钱</span>
				</div>
				<div class="p-title-record">
					<div class="record-right hide"><i class="icon-angle-right"></i></div>
				</div>
			</div>
		</div>
		<input type="hidden" id="_token" value="{{ csrf_token() }}">
		<div id="product"></div>
		<div class="app-bottom">
			<div class="menu-active">
				<i class="icon-home"></i>
				<div>首页</div>
			</div>
			<div onclick="showInfo()">
				<i class="icon-user"></i>
				<div>个人中心</div>
			</div>
		</div>
	</div>
</body>

<script src="{{ asset("/js/zepto.min.js")}} "></script>
<script src="{{ asset("/js/product.js")}} "></script>

</html>