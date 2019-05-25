function showProduct() {
    window.location.href = '/web/product/info/' + hashId + '';
}
$(function () {
    $('#logout').click(function () {
        $.ajax({
            type: "POST",
            url: "/web/user/logout",
            headers: {
                'X-CSRF-TOKEN': $('#_token').val()
            },
            data: {},
            dataType: "json",
            success: function (data) {
                window.location.reload();
            },
            error: function (error) {
            }
        });
    })
    $('#login').click(function () {
        window.location.href = '/web/user/login/' + hashId + '';
    });
    $('#register').click(function () {
        window.location.href = '/web/user/login/' + hashId + '?status=register';
    });


    
})