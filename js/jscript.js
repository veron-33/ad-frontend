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
	$("#main_table_tree").height(window_height()-80);
	$("#main_table_tree").width(window_width()-5);
});
//####################################################################################




//функция построения дерева каталогов
function build_tree (add_arr) {
	//задаем параметры дерева
	var arr = {
		extensions: ["persist"],    // расширения куки
		selectMode: 1,
		checkbox: false,
		select: function (e, data) {    //ф-ция выбора нода (селектор, галочка)
			selected_node = "" + $.map(data.tree.getSelectedNodes(), function (node) {  //запоминаем выбранный нод
				return node.key;
			});
			if ($("#cr_user_cont")) {   //если открыто окно нового пользователя - вставляем  путь нода в поле
				var a = selected_node.split('__').reverse().join(" / ");
				$("#cr_user_cont").attr("value",a);
			}
		},
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
			var node = data.node;
			$("#tree_objects").fancytree("option", "source", {url: "ajax/ad_ajax_tree.php",	data: {act: 'get_tree', type: 'objects', pNode: node.key}});
			//$.getJSON("ajax/ad_ajax_tree.php", {act: 'get_tree', type: 'objects', pNode: node.key}, function (data) {
			//		$("#tree_objects").fancytree("getRootNode").addChildren(data);
			//})
		}
    };
	$("#tree").fancytree(arr);
}

$(function(){
	selected_node = "Выберите контейнер в дереве каталогов";
    $("#dialog_div").dialog({autoOpen:false});
    build_tree();
	$(window).resize();
	$("#main_table_tree").colResizable({
		liveDrag:true
	});
	// строим таблицу объектов контейнера
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


// нажатие кнопки "создать пользователя". запрос формы для ввода.		
function create_user() {
    $("#tree").fancytree("option", "checkbox", true);
	//посылаем запрос для получения формы для ввода данных нового пользователя
    $.get(
        "./ajax/ad_create_user.html",
        function (data) {
            $("#dialog_div").html(data);
	        // в поле "Контейнер" вставляем текущее значение переменной выбранного контейнера
	        $("#cr_user_cont").attr("value",selected_node.split('__').reverse().join(" / "));
	        $("#cr_user_form").ajaxForm(function() {
		        alert("Пользователь создан успешно!");
	        });
        }
    );
	// инициируем диалоговое окно с формой
    $("#dialog_div").dialog({
        title: "Создание нового пользователя",
        //modal:true,
	    position: ["center", 100],
        width: "600px",
        buttons: {
            "Создать":function () {
	            check_cr_user_form();
	            $("#cr_user_form").submit();
	            $("#dialog_div").dialog("close");
	            //отключаем чекбоксы у дерева
	            $("#tree").fancytree("option", "checkbox", false);
	            return false
            },
            "Отмена":function (){
	            $("#dialog_div").dialog("close");
	            $("#tree").fancytree("option", "checkbox", false);
            }
        }
    });
    $("#dialog_div").dialog("open");
}



function check_cr_user_form() {
 return false;
}