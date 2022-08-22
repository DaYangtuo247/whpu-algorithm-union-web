//表格分页显示，totalPages总页数，page当前页面，getArticle是否调用ajax获取帖子数据
function createPagination(totalPages, page, getArticle = true) {
	//隐性bug，page有可能被识别为字符串，要强制转换为数字类型
	page = Number(page);
	let aTag = '';
	let active;
	let beforePage = page - 1;
	let afterPage = page + 1;

	//确保当前页面前存在页面时，允许向上翻页
	if (page - 1 >= 1)
		aTag += `<a href="#page=${page - 1}" class="btn prev iconfont" onclick="createPagination(${totalPages}, ${page - 1})"><span>&#xe76e;</span></a>`;

	//如果页面值小于 2，则在上一个按钮后添加 1
	if (page > 2 && totalPages >= 6) {
		aTag += `<a href="#page=1" class="first numb" onclick="createPagination(${totalPages}, 1)"><span>1</span></a>`;
		//如果 当前页面page 值大于 3 同时总页数大于6，则在第一个 li添加省略号
		if (page > 3) {
			aTag += `<a class="dots"><span>···</span></a>`;
		}
	}

	// 当前 li 之前显示多少页或 li
	if (page == totalPages) {
		beforePage = beforePage - 2;
	} else if (page == totalPages - 1) {
		beforePage = beforePage - 1;
	}
	// 当前 li 之后显示多少页或 li
	if (page == 1) {
		afterPage = afterPage + 2;
	} else if (page == 2) {
		afterPage = afterPage + 1;
	}

	for (var plength = beforePage; plength <= afterPage; plength++) {
		if (plength > totalPages || plength <= 0) { //如果plength大于totalPage长度则继续
			continue;
		}
		if (plength == 0) { //如果 plength 为 0，则在 plength 值中添加 +1
			plength = plength + 1;
		}
		if (page == plength) { //如果页面等于 plength，则在活动变量中分配活动字符串
			active = " active";
		} else { //否则将活动变量留空
			active = "";
		}
		aTag += `<a href="#page=${plength}" class="numb${active}" onclick="createPagination(${totalPages}, ${plength})"><span>${plength}</span></a>`;
	}

	//如果 page 值小于 totalPage 值 -1 则显示最后一个 li 或 page
	if (page < totalPages - 1 && totalPages >= 6) {
		//如果 page 值小于 totalPage 值 -2，则在最后一个 li 或 page 之前添加此 (...)
		if (page < totalPages - 2) {
			aTag += `<a class="dots"><span>···</span></a>`;
		}
		aTag += `<a href="#page=${totalPages}" class="last numb" onclick="createPagination(${totalPages}, ${totalPages})"><span>${totalPages}</span></a>`;
	}

	//确保当前页面存在下一页，允许向下翻页
	if (page + 1 <= totalPages)
		aTag += `<a href="#page=${page + 1}" class="btn next iconfont" onclick="createPagination(${totalPages}, ${page + 1})"><span>&#xe76f;</span></a>`;

	//在 ul 标签内添加 li 标签
	// pagination.innerHTML = aTag;
	$('.pagination').html(aTag);
	//获取某一页的文章
	if (getArticle)
		getTagArticle(page);
	//滚动到最顶部
	scrollTo(0,0);
}
