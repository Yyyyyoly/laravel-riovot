$(function () {
	var length = fake_list.length;
	var sum = 24 * length * 60;
	var html = '';
	for (var i = 0; i < length; i++) {
		html += '<div class="item">' + fake_list[i].time + '' + fake_list[i].title + '</div>'
	}
	$('#scroll_list').html(html);
	var count = 0
	setInterval(function () {
		document.getElementById('scroll_list').scrollTop++;
		if (count >= sum) {
			document.getElementById('scroll_list').scrollTop = 0;
			count = 0;
		}
		count += 60;
	}, 60);

	//渲染列表
	var activeIndex = 1;
	var titleHtml = '';
	var productList = '';
	// var tabCount = 0;
	// for (var i in product_list) {
	// 	tabCount++;
	// 	var showTab = tabCount == 1 ? 'tab_show' : '';
	// 	var showList = tabCount == 1 ? 'list_show' : '';
	// 	titleHtml += '<span tindex="' + tabCount + '" class="tab-cell hide ' + showTab + '">' + product_list[i].type_name + '</span>';
	// 	var listHmtl = '';
	// 	var product = product_list[i].products;
	// 	for (var j = 0; j < product.length; j++) {
	// 		listHmtl += '<div class="product-list-cell" onclick="jumpDonwload(' + product[j].id + ')">'
	// 			+ '<i class="icon-circle-blank i-right"></i>'
	// 			+ '<img class="cell-img" src="' + product[j].icon_url + '" />'
	// 			+ '<div class="title">' + product[j].name + '</div>'
	// 			+ '<div class="num"><i class="icon-download-alt"></i>' + product[j].download_nums + '</span></div>'
	// 			+ '<div class="desc">' + product[j].desc + '</div>'
	// 			+ '</div>';
	// 	}
	// 	productList += '<div class="product-list hide ' + showList + '" pindex="' + tabCount + '">' + listHmtl + '</div>';
	//
	// }
	var tabCount = 1;
	var showTab ='tab_show';
	var showList = 'list_show';
	titleHtml += '<span tindex="' + tabCount + '" class="tab-cell hide ' + showTab + '">' + '无视黑白' + '</span>';
	var listHmtl = '';
	for (var j = 0; j < product_list.length; j++) {
		listHmtl += '<div class="product-list-cell" onclick="jumpDonwload(' + product_list[j].id + ')">'
			+ '<i class="icon-circle-blank i-right"></i>'
			+ '<img class="cell-img" src="' + product_list[j].icon_url + '" />'
			+ '<div class="title">' + product_list[j].name + '</div>'
			+ '<div class="num"><i class="icon-download-alt"></i>' + product_list[j].download_nums + '</span></div>'
			+ '<div class="desc">' + product_list[j].desc + '</div>'
			+ '</div>';
	}
	productList += '<div class="product-list hide ' + showList + '" pindex="' + tabCount + '">' + listHmtl + '</div>';

	if (tabCount > 1) {
		$('.record-right').removeClass('hide');
	}
	$('#tab_list').html(titleHtml);
	$('#product').html(productList);

	$('.record-right').click(function () {
		activeIndex++;
		$('.tab-cell').removeClass('tab_show');
		$('.product-list').removeClass('list_show');
		$('[tindex="' + activeIndex + '"]').eq(0).addClass('tab_show');
		$('[pindex="' + activeIndex + '"]').eq(0).addClass('list_show');
		if (activeIndex == tabCount) {
			$('.record-left').removeClass('hide');
			$('.record-right').addClass('hide');
		} else {
			$('.record-left').removeClass('hide');
			$('.record-right').removeClass('hide');
		}
	});

	$('.record-left').click(function () {
		activeIndex--;
		$('.tab-cell').removeClass('tab_show');
		$('.product-list').removeClass('list_show');
		$('[tindex="' + activeIndex + '"]').eq(0).addClass('tab_show');
		$('[pindex="' + activeIndex + '"]').eq(0).addClass('list_show');
		if (activeIndex == 1) {
			$('.record-left').addClass('hide');
			$('.record-right').removeClass('hide');
		} else {
			$('.record-left').removeClass('hide');
			$('.record-right').removeClass('hide');
		}
	});
});
function jumpDonwload(id) {
	window.location.href = '/web/product/apply/' + hashId + '?product_id=' + id;
}
function showInfo() {
	window.location.href = '/web/user/info/' + hashId;
}
