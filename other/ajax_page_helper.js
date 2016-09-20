//创建翻页导航条
function pagebar(tag,page,pagesize,pagetotal){
	var list=[];
	var ul=$(tag);
	list.push('<li><a href="javascript:void(0)" onclick="on_pagebar(\''+tag+'\','+(page-1)+')">&#8249;</a></li>');
	var n=Math.floor((pagetotal-1)/pagesize)+1;
	for(var i=0;i<n;i++){
		var p=i+1;
		if(i==page){
			list.push('<li><a class="active" href="javascript:void(0)" onclick="on_pagebar(\''+tag+'\','+p+')">'+p+'</a></li>');
		}else{
			list.push('<li><a href="javascript:void(0)" onclick="on_pagebar(\''+tag+'\','+p+')">'+p+'</a></li>');
		}
	}
	list.push('<li><a href="javascript:void(0)" onclick="on_pagebar(\''+tag+'\','+(page+1)+')">&#8250;</a></li>');
	ul.html(list.join(""));
}

//导航条翻页
function on_pagebar(tag,n){
	if(typeof on_page=='function'){
		on_page(tag,n);
	}else{
		console.log("需要定义 on_page(tag,n)");
	}
}

function table_clear(table){
	$(table+" tr:not(:first)").remove(); 
}

function table_update(table,text){
	$(table+" tr:last").after(text);
}

$(document).ready(function(){
	if(typeof on_ready==='function'){
		on_ready();
	}
});