// Засекаю время
var start = new Date();



//функция построения дерева каталогов
function build_tree() {
	//задаем параметры дерева
	var arr = {
        idPrefix:"ftt_",
        cookieId:"ftt_",
		extensions: ["persist"],    // расширения куки
        persist:{
            expandLazy:true,
            overrideSource: true
        },
		selectMode: 1,
		generateIds: true,
		checkbox: false,
		/*select: function (e, data) {    //ф-ция выбора нода (селектор, галочка)
			selected_node = "" + $.map(data.tree.getSelectedNodes(), function (node) {  //запоминаем выбранный нод
				return node.key;
			});
			if ($("#cr_user_form")) {   //если открыто окно нового пользователя - вставляем  путь нода в поле
				var a = selected_node.replace(/..:/g, "" ).split('__').reverse().join(" / ");
				$("#cr_user_cont_sh").attr("value", a);
				$("#cr_user_cont").attr("value", selected_node);
			}
		},*/
		toggleEffect: { height: "toggle", duration: 200 },
		strings: {
			loading: "Загрузка…",
			loadError: "Ошибка закгрузки дерева каталогов!"
		},
		source : {
			url: "index.php",
			data: {
				act: "get_tree",
				type: "folders",
				pNode: "NULL"
            }
		},
		lazyLoad: function (e, data) {
			data.result = $.ajax({
				url:"index.php",
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
            selected_node = node.key;
			obj_area = $("#tree_objects");
			obj_area.fancytree(
				"option",
				"source",
				{	url: "index.php",
					data: {act: 'get_tree', type: 'objects', pNode: node.key},
					success: function () {}
				}
			);
			//$.getJSON("index.php", {act: 'get_tree', type: 'objects', pNode: node.key}, function (data) {
			//		$("#tree_objects").fancytree("getRootNode").addChildren(data);
			//})
		}
    };
	$("#tree").fancytree(arr);
}

$(function() {
	selected_node = false;
    dial_box = $("#dialog_div");
    dial_box.dialog({autoOpen:false});
    build_tree();
    $("#left_div").resizable({
        handles:"e"
    });
    //$("#tree_objects").stickyTableHeaders();
	// строим таблицу объектов контейнера
	$("#tree_objects").fancytree({
        idPrefix:"fto_",
        cookieId:"fto_",
		//scrollParent: $("#right_div"),
		extensions: ["persist", "table", "gridnav"],    // расширения куки
		table: {
			indentation: 20,
			nodeColumnIdx: 1,
			checkboxColumnIdx: 0
		},
		gridnav: {
			autofocusInput: false,
			handleCursorKeys: true
		},
		imagePath: "./css/img/",
		selectMode: 2,
		checkbox: true,
		strings: {
			loading: "Загрузка…",
			loadError: "Ошибка закгрузки объектов каталога!"
		},
		source: []
	})
});		


// нажатие кнопки "создать пользователя". запрос формы для ввода.		
function create_user() {
    if (!selected_node) {
        alert("Сначала выберите контейнер!");
        return false;
    }
	//посылаем запрос для получения формы для ввода данных нового пользователя
    //console.log("selected: " + selected_node);
    $.get(
        "./ajax/ad_create_user.html",
        function (data) {
            $("#dialog_div").html(data);
	        // в поле "Контейнер" вставляем текущее значение переменной выбранного контейнера
	        $("#cr_user_cont_sh").attr("value",selected_node.replace(/..:/g, "" ).split('__').reverse().join(" / "));
			$("#cr_user_cont").attr("value", selected_node);
	        $("#cr_user_form").ajaxForm(function() {
		        alert("Пользователь создан успешно!");
	        });
        }
    );
	// инициируем диалоговое окно с формой
    dial_box.dialog({
        title: "Создание нового пользователя",
        modal:true,
        width: "600px",
        position: {my: "center top", at: "center top+10%", of: window},
        resizable: false,
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
    dial_box.dialog("open");
}

function check_cr_user_form() {
    return false;
}

function get_locked() {
    $.get(
        "./index.php",
        {act:"get_locked_users"},
        function (data) {
            if (data!="false") {
                var templatediv="<table id='locked_list'>" +
                    "<thead><tr>" +
                    "<th></th><th>Пользователь</th><th>Время<br />блокировки</th>" +
                    "</tr></thead>" +
                    "<tbody></tbody></div>";
                $("#dialog_div").html(templatediv);
                pdata = JSON.parse(data);
                $("#locked_list").fancytree({
                    idPrefix:"ftlu_",
                    cookieId:"ftlu_",
                    source: pdata,
                    extensions: ["table", "gridnav"],
                    table: {
                        indentation: 20,
                        nodeColumnIdx: 1,
                        checkboxColumnIdx: 0
                    },
                    gridnav: {
                        autofocusInput: false,
                        handleCursorKeys: true
                    },
                    imagePath: "./css/img/",
                    selectMode: 2,
                    checkbox: true,
                    strings: {
                        loading: "Загрузка…",
                        loadError: "Ошибка закгрузки списка пользователей!"
                    },
                    renderColumns: function (e, d) {
                        var node= d.node,
                            $tdList = $(node.tr).find(">td");
                        $tdList.eq(2).text(node.data.tstamp)
                    }
                });
                dial_box.dialog({
                    title: "Список заблокированных пользователей",
                    modal:true,
                    width: "600px",
                    position: {my: "center top", at: "center top+10%", of: window},
                    resizable: false,
                    buttons: {
                        "Разблокировать":function () {
                            $("#dialog_div").dialog("close");
                            s = $.map($("#locked_list").fancytree("getTree").getSelectedNodes(), function(node){
                                return node.key;
                            });
                            unlock_users(s);
                        },
                        "Закрыть":function (){
                            $("#dialog_div").dialog("close");
                        }
                    }
                });
                dial_box.dialog("open");
            }
            else {
                alert("Нет заблокированых пользователей")
            }
        }
    );
}

function unlock_users(users) {
    $.get(
        "./index.php",
        {
            act:"unlock_users",
            ul:users
        },
        function (data) {
            alert(data)
        }
    )
}


function exit() {
    $("#tree").fancytree("getTree").clearCookies();
    $("#tree_objects").fancytree("getTree").clearCookies();
    window.location.replace(window.location + "?exit");
}