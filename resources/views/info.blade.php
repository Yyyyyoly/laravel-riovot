<!doctype html>
<html>
<meta charset="utf-8">
<meta name="viewport"
	content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1" />
<meta name="format-detection" content="telephone=no,email=no,date=no,address=no" />
<title>睿沃特福利专区</title>
<link type="text/css" href="{{ asset('/css/font-awesome.min.css') }}" rel="stylesheet">
<link type="text/css" href="{{ asset('/css/common.css') }}" rel="stylesheet">
<link type="text/css" href="{{ asset('/css/user.css') }}" rel="stylesheet">
<script>
	var hashId = '{{$admin_hash_id}}';
</script>

<body>
	<div class="app">
		<div class="user-top">
			<div class="user-back">
				<i class="icon-angle-left" onclick="javascript:history.go(-1)"></i>
			</div>
			<div>个人中心</div>
			@if($is_login)
			<div class="login-status" id="logout">注销</div>
			@else
			<div class="login-status" id="login">登录</div>
			@endif
		</div>
		<div class="user-detail">
			@if($is_login)
			<div>
				<div class="user-head">
					<img src="{{ asset("/images/icon29.png")}}" />
				</div>
				<div class="user-name">{{$user_name}}</div>、
				<div><i class="icon-chevron-right"></i></div>
			</div>
			@else
			<div id="register">
				<div class="user-head">
					<img src="{{ asset("/images/icon29.png")}}" />
				</div>
				<div class="user-name">未注册</div>
				<div><i class="icon-chevron-right"></i></div>
			</div>
			@endif
		</div>
		<div class="about-us">
			<div class="about-us-cell">
				<div>客服热线</div>
				<div><i class="icon-chevron-right"></i></div>
			</div>
			<div class="about-us-cell">
				<div>商务合作</div>
				<div><i class="icon-chevron-right"></i></div>
			</div>
			<div class="about-us-cell">
				<div>关于我们</div>
				<div><i class="icon-chevron-right"></i></div>
			</div>
		</div>
		<div class="app-bottom">
			<div onclick="showProduct()">
				<i class="icon-home"></i>
				<div>首页</div>
			</div>
			<div class="menu-active" ">
				<i class=" icon-user"></i>
				<div>个人中心</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="_token" value="{{ csrf_token() }}">
</body>

<script src="{{ asset("/js/zepto.min.js")}} "></script>
<script src="{{ asset("/js/user.js")}} "></script>

</html>