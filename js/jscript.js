// Засекаю время
var start = new Date();


// Блок функций для определения видимой области страницы и изменения св-в таблицы
//###################################################################################
function window_height() {
	return document.documentElement.clientHeight
}
function window_width() {
	return document.documentElement.clientWidth
}

$(window).resize(function () {
	$("#main_table_tree").height(window_height()-72);
	$("#main_table_tree").width(window_width()-2);
});
//####################################################################################




//функция построения дерева каталогов
function build_tree (add_arr) {
	//задаем параметры дерева
	var arr = {
		selectMode: 1,
		checkbox: false,
		fx: { height: "toggle", duration: 200 },
		strings: {
			loading: "Загрузка…",
			loadError: "Ошибка закгрузки дерева каталогов!"
		},
		source : {
			url: "ajax/ad_ajax_tree.php",
			data: {
				act: "get_tree",
				type: "folders",
				pNode: "NULL" }
		},
		lazyload: function (e, data) {
			data.result = $.ajax ({
				url:"ajax/ad_ajax_tree.php",
				dataType:"json",
				data: {
					act: "get_tree",
					type: "folders",
					pNode: data.node.key
				}
			})
		},
		activate: function (e, data) {
			//$("#tree_objects ul").html('');
			//$("#tree_objects").fancytree("getRootNode").children =[];
			node = data.node;
			$("#tree_objects").fancytree("option", "source", {url: "ajax/ad_ajax_tree.php",	data: {act: 'get_tree', type: 'objects', pNode: node.key}});
			//$.getJSON("ajax/ad_ajax_tree.php", {act: 'get_tree', type: 'objects', pNode: node.key}, function (data) {
			//		$("#tree_objects").fancytree("getRootNode").addChildren(data);
			//})
		}
    }
	$("#tree").fancytree(arr);
}

$(function(){
    build_tree();
	$(window).resize();
	$("#main_table_tree").colResizable({
		liveDrag:true
	});
	$("#tree_objects").fancytree({
				  imagePath: "./css/img/",	
				  selectMode: 1,
				  checkbox: false,
				  strings: {
					  loading: "Загрузка…",
					  loadError: "Ошибка закгрузки объектов каталога!"
				  },
				  source: []				  
			})
});		
		
	
function obj_tree(key) {
	$("#tree_objects").fancytree({
		imagePath: "./css/img/",	
		selectMode: 1,
		checkbox: false,
		strings: {
			loading: "Загрузка…",
			loadError: "Ошибка закгрузки объектов каталога!"
		},
		source : {
			url: "ajax/ad_ajax_tree.php",
			data: {
				act: "get_tree",
				type: "objects",
				pNode: key }
		},
	
	})
}
		
		
		
 
// нажатие кнопки "создать пользователя". запрос формы для ввода.		
function create_user_get_form() {
	//очищаем рабочую область и включаем лоадинг
	$("#tree_objects").html("");
	$("#tree_objects").addClass("waiting");
	//включаем радиокнопки у дерева
	var tree_par = {
		selectMode: 1,
		checkbox: true,
		classNames: '{checkbox: "dynatree-radio"}'
	};
	build_tree(tree_par);
	//$("#tree").dynatree.checkbox = true;
	//выполняем запрос формы
	$.ajax ({
		url: "ajax/ad_create_user.html",
		success: function (data) {
			$rez = "";
			if (data) {
			  $("#tree_objects").removeClass("waiting");
			  $("#tree_objects").html(data);	
			}
		}
	});
}