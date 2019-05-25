$(function () {
    $('#login').click(function () {
        window.location.href = '/web/user/login/' + hashId + '';
    });
    $('#register').click(function () {
        window.location.href = '/web/user/login/' + hashId + '?status=register';
    });
    $('#forget').click(function () {
        window.location.href = '/web/user/login/' + hashId + '?status=forget';
    });

    function showWin(msg) {
        $('#msg').html(msg);
        $('#msg_win').show(1000);
        setTimeout(function () {
            $('#msg_win').hide(1000);
        }, 2000)
    }
    //验证手机号码准确性
    function checkTel(tel) {
        var myreg = /^[1][3,4,5,7,8][0-9]{9}$/;
        if (!myreg.test(tel)) {
            return false;
        } else {
            return true;
        }
    }
    var time = 60;
    function timeDown() {
        if (time == 0) {
            $('#r_get_code').html('获取验证码');
            clickStatus = false;
            time = 60;
            return;
        }
        $('#r_get_code').html('<span>' + time + 's</span>');
        time--;
        setTimeout(function () {
            timeDown();
        }, 1000)
    }
    var clickStatus = false;
    $('#r_get_code').click(function () {
        if (clickStatus) {
            return;
        }
        var phone = $('#phone').val();
        if (phone) {
            if (checkTel(phone)) {
                clickStatus = true;
                $.ajax({
                    type: "POST",
                    url: "/web/user/sms",
                    headers: {
                        'X-CSRF-TOKEN': $('#_token').val()
                    },
                    data: {
                        'phone': phone,
                        'type': 'register'
                    },
                    dataType: "json",
                    success: function (data) {
                        if (data.success) {
                            timeDown();
                        } else {
                            showWin(data.reason);
                        }

                    },
                    error: function (error) {
                        var reason = JSON.parse(error.responseText);
                        showWin(reason.reason);
                        clickStatus = false;
                    }
                });
            } else {
                showWin('请输入正确的手机号！');
            }
        } else {
            showWin('请输入手机号！');
        }
    });
    $('#confirm_password').blur(function () {
        if ($('#confirm_password').val() != $('#password').val()) {
            showWin('两次密码输入不一致');
        }
    })
    $('#register_click').click(function () {
        var params = {};
        if (!$('#phone').val()) {
            showWin('请输入手机号！');
            return;
        }
        params.phone = $('#phone').val();

        if (!$('#code').val()) {
            showWin('请输入验证码！');
            return;
        }
        params.sms_code = $('#code').val();

        if (!$('#name').val()) {
            showWin('请输入姓名！');
            return;
        }
        params.name = $('#name').val();

        if (!$('#password').val()) {
            showWin('请输入密码！');
            return;
        }
        if (!$('#confirm_password').val()) {
            showWin('请确认密码！');
            return;
        }
        if ($('#confirm_password').val() != $('#password').val()) {
            showWin('两次密码输入不一致');
            return;
        }
        params.password = $('#password').val();
        params.age = $('#age').val();
        params.ant_scores = $('#number').val();
        params.admin_hash_id = hashId;
        params.product_id = product_id || 0;
        console.log(params)

        $.ajax({
            type: "POST",
            url: "/web/user/register",
            headers: {
                'X-CSRF-TOKEN': $('#_token').val()
            },
            data: params,
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    window.location.href = '/web/product/info/' + hashId + '';
                } else {
                    showWin(data.reason);
                }

            },
            error: function (error) {
                var reason = JSON.parse(error.responseText);
                showWin(reason.reason);
            }
        });
    });

    $('#login_click').click(function () {
        if (!$('#login_account').val()) {
            showWin('请输入帐号！');
            return;
        }
        if (!$('#login_password').val()) {
            showWin('请确认密码！');
            return;
        }
        $.ajax({
            type: "POST",
            url: "/web/user/login",
            headers: {
                'X-CSRF-TOKEN': $('#_token').val()
            },
            data: {
                phone: $('#login_account').val(),
                password: $('#login_password').val(),
                admin_hash_id: hashId
            },
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    window.location.href = '/web/product/info/' + hashId + '';
                } else {
                    showWin(data.reason);
                }
            },
            error: function (error) {
                var reason = JSON.parse(error.responseText);
                showWin(reason.reason);
            }
        });
    });





    var s_time = 60;
    var clickStatusSet = false;
    function timeDownSet() {
        if (s_time == 0) {
            $('#s_get_code').html('获取验证码');
            clickStatusSet = false;
            s_time = 60;
            return;
        }
        $('#s_get_code').html('<span>' + s_time + 's</span>');
        s_time--;
        setTimeout(function () {
            timeDownSet();
        }, 1000)
    }

    $('#s_get_code').click(function () {
        if (clickStatusSet) {
            return;
        }
        var phone = $('#s_phone').val();
        if (phone) {
            if (checkTel(phone)) {
                clickStatusSet = true;
                $.ajax({
                    type: "POST",
                    url: "/web/user/sms",
                    headers: {
                        'X-CSRF-TOKEN': $('#_token').val()
                    },
                    data: {
                        'phone': phone,
                        'type': 'forget'
                    },
                    dataType: "json",
                    success: function (data) {
                        if (data.success) {
                            timeDownSet();
                        } else {
                            showWin(data.reason);
                        }
                    },
                    error: function (error) {
                        var reason = JSON.parse(error.responseText);
                        showWin(reason.reason);
                        clickStatusSet = false;
                    }
                });
            } else {
                showWin('请输入正确的手机号！');
            }
        } else {
            showWin('请输入手机号！');
        }
    });

    $('#s_confirm_password').blur(function () {
        if ($('#s_confirm_password').val() != $('#s_password').val()) {
            showWin('两次密码输入不一致');
        }
    });
    $('#reset_click').click(function () {
        var params = {};
        if (!$('#s_phone').val()) {
            showWin('请输入手机号！');
            return;
        }
        params.phone = $('#s_phone').val();

        if (!$('#s_code').val()) {
            showWin('请输入验证码！');
            return;
        }
        params.sms_code = $('#s_code').val();

        if (!$('#s_password').val()) {
            showWin('请输入密码！');
            return;
        }
        if (!$('#s_confirm_password').val()) {
            showWin('请确认密码！');
            return;
        }
        if ($('#s_confirm_password').val() != $('#s_password').val()) {
            showWin('两次密码输入不一致');
            return;
        }
        params.password = $('#s_password').val();

        $.ajax({
            type: "POST",
            url: "/web/user/forget",
            headers: {
                'X-CSRF-TOKEN': $('#_token').val()
            },
            data: params,
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    window.location.href = '/web/user/login/' + hashId + '';
                } else {
                    showWin(data.reason);
                }
            },
            error: function (error) {
                var reason = JSON.parse(error.responseText);
                showWin(reason.reason);
            }
        });
    });

})