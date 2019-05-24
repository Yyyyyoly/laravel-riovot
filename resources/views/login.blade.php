<!doctype html>
<html>
<meta charset="utf-8">
<meta name="viewport"
	content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1" />
<meta name="format-detection" content="telephone=no,email=no,date=no,address=no" />
<title>睿沃特福利专区</title>
<link type="text/css" href="{{ asset('/css/font-awesome.min.css') }}" rel="stylesheet">
<link type="text/css" href="{{ asset('/css/common.css') }}" rel="stylesheet">
<link type="text/css" href="{{ asset('/css/login.css') }}" rel="stylesheet">


<body>
	<div class="app">

		<div class="app-logo">
			<img src="{{ asset('/images/logo.png') }}">
			<div>睿沃特</div>
		</div>

		<div class="app-login hide" id="login_show">
			<div class="app-login-cell">
				<i class="icon-user"></i>
				<input type="tel" placeholder="请您输入帐号" />
			</div>
			<div class="app-login-cell">
				<i class="icon-lock"></i>
				<input type="password" placeholder="请您输入密码" />
			</div>
			<div class="login-btn">登 录</div>
			<div class="login-regist">
				<div id="forget">忘记密码</div>
				<div id="register">还没帐号？去注册>></div>
			</div>
		</div>

		<div class="app-login hide" id="register_show">
			<div class="regist-login" id="login">若已注册，去登录>></div>
			<div class="app-cell-phone">
				<input type="tel" placeholder="请您输入手机号" />
				<div>获取验证码</div>
			</div>
			<div class="app-login-cell">
				<img src="{{ asset('/images/icon33.png') }}" />
				<input type="tel" placeholder="请您输入验证码" />
			</div>
			<div class="app-login-cell">
				<img src="{{ asset('/images/icon35.png') }}" />
				<input type="text" placeholder="请您输入姓名" />
			</div>
			<div class="app-login-cell">
				<img src="{{ asset('/images/icon35.png') }}" />
				<input type="password" placeholder="请设置你的密码" />
			</div>
			<div class="app-login-cell">
				<img src="{{ asset('/images/icon35.png') }}" />
				<input type="password" placeholder="请确认您的密码" />
			</div>
			<div class="app-login-cell">
				<img src="{{ asset('/images/icon37.png') }}" />
				<input type="tel" placeholder="请填写您的年龄" />
			</div>
			<div class="app-login-cell">
				<img src="{{ asset('/images/icon36.png') }}" />
				<input type="tel" placeholder="清填写您的芝麻分" />
			</div>

			<div class="login-btn">注 册</div>

		</div>

		<div class="app-login hide" id="forget_show">
			<div class="app-cell-phone">
				<input type="tel" placeholder="请您输入手机号" />
				<div>获取验证码</div>
			</div>
			<div class="app-login-cell">
				<img src="{{ asset('/images/icon33.png') }}" />
				<input type="tel" placeholder="请您输入验证码" />
			</div>
			<div class="app-login-cell">
				<img src="{{ asset('/images/icon35.png') }}" />
				<input type="password" placeholder="请设置你的密码" />
			</div>
			<div class="app-login-cell">
				<img src="{{ asset('/images/icon35.png') }}" />
				<input type="password" placeholder="请确认您的密码" />
			</div>
			<div class="login-btn">修改密码</div>

		</div>
	</div>
</body>

<script src="{{ asset("/js/zepto.min.js")}} "></script>
<script>
	var search = window.location.search;
	var show_id='#login_show';
	if(search){
		var temp = search.split('?')[1].split('&');
		var obj = {};
		for(var i=0; i<temp.length; i++){
			var _temp = temp[i].split('=');
			obj[_temp[0]] = _temp[1];
		}
		console.log(obj)
		if(obj.status){
			if(obj.status =='register'){
				show_id = '#register_show';
			}else if(obj.status =='forget'){
				show_id = '#forget_show';
			}
		} 
	}	
	$(show_id).removeClass('hide');
</script>
<script src="{{ asset("/js/login.js")}} "></script>

</html>