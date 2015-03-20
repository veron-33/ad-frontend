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
            obj_area.contextmenu({
                delegate: "span.fancytree-node",
                beforeOpen: function(e, ui) {
                    var node = $.ui.fancytree.getNode(ui.target);
                    node.setActive();
                    var node_type = node.data.type;
                    if (node_type == "user") {
                        obj_area.contextmenu("enableEntry", "m_del",true);
                    }
                },
                menu: [
                    {title:"Свойтсва", cmd:"m_edit", disabled:true},
                    {title:"Переименовать <kbd>[F2]</kbd>", cmd:"m_ren",
                        action:function(e,ui){
                            var node = $.ui.fancytree.getNode(ui.target);
                            node.editStart();
                        }
                    },
                    {title:"Удалить", cmd:"m_del", disabled:true,
                        action: function(e,ui){
                            if (confirm("Вы действительно хотите удалить данного пользователя?\nДанное действие необратимо!")) {
                                var node = $.ui.fancytree.getNode(ui.target);
                                var users = [];
                                users[users.length] = node.data.login;
                                delete_user(users);
                            }
                        }
                    },
                    {title:"----"},
                    {title: "Копировать", cmd: "", disabled:true},
                    {title: "Вырезать", cmd: "", disabled:true},
                    {title: "Вставить", cmd: "", disabled:true}
                ]
            });
		}
    };
	$("#tree").fancytree(arr);
    $("#tree").contextmenu({
        delegate: "span.fancytree-title",
        beforeOpen: function(e, ui) {
            var node = $.ui.fancytree.getNode(ui.target);
            node.setActive();
        },
        menu: [
            {title: "Создать", children: [{
                title: "Пользователя",
                cmd: "m_cr_user",
                action: function(e,ui){
                    create_user()
                }
            },
                {title: "Группу", cmd: "m_cr_group", disabled: true},
                {title: "Контакт", cmd: "", disabled: true},
                {title: "Контейнер", cmd: "", disabled: true}
            ]},
            {title: "Удалить", cmd: "", disabled: true},
            {title: "----"},
            {title: "Копировать", cmd: "", disabled:true},
            {title: "Вырезать", cmd: "", disabled:true},
            {title: "Вставить", cmd: "", disabled:true}
        ],
        select: function (e, ui) {
            //alert ("Выбрано: "+ ui.cmd );
        }
    });
}

$(function() {
    $(window).ajaxSuccess(function(e,xhr){check_ajax_header(xhr, false);});
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
		extensions: ["persist", "table", "gridnav", "edit"],    // расширения куки
		table: {
			indentation: 20,
			nodeColumnIdx: 1,
			checkboxColumnIdx: 0
		},
		gridnav: {
			autofocusInput: false,
			handleCursorKeys: true
		},
        renderColumns:function(e,data){
            var node = data.node,
                $tdList = $(node.tr).find(">td");
            $tdList.eq(2).text(node.data.dtype)
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
            dial_box.html(data);
	        // в поле "Контейнер" вставляем текущее значение переменной выбранного контейнера
	        $("#cr_user_cont_sh").attr("value",selected_node.replace(/..:/g, "" ).split('__').reverse().join(" / "));
			$("#cr_user_cont").attr("value", selected_node);
	        $("#cr_user_form").ajaxForm(function() {
		        alert("Пользователь создан успешно!");
	        });
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
                        //$("#tree").fancytree("option", "checkbox", false);
                        return false
                    },
                    "Отмена":function (){
                        dial_box.dialog("close");
                        dial_box.empty();
                        //$("#tree").fancytree("option", "checkbox", false);
                    }
                }
            });
            dial_box.dialog("open");
        }
    );
}

/**
 * @param users Array of users
 */
function delete_user(users) {
    if ($.isArray(users) && users.length > 0) {
        $.get(
            "index.php",
            {
                act:"del_users",
                u: users
            },
            function(data) {
                if (data) {
                    alert("Пользователь успешно удален")
                }
                else {
                    alert("Ошибка при удалении пользователя. Кури логи")
                }
            }
        )
    }
}

function check_cr_user_form() {
    return false;
}

function find_user() {
    $.get(
        "./ajax/ad_find_user.html",
        function (data) {
            dial_box.html(data);

            $("#find_user_form").ajaxForm(function() {
                alert("Пользователь создан успешно!");
            });
            dial_box.dialog({
                title: "Поиск пользователя",
                modal:true,
                width: "600px",
                position: {my: "center top", at: "center top+10%", of: window},
                resizable: false,
                buttons: {
                    "Найти":function () {
                        check_cr_user_form();
                        $("#cr_user_form").submit();
                        $("#dialog_div").dialog("close");
                        //отключаем чекбоксы у дерева
                        //$("#tree").fancytree("option", "checkbox", false);
                        return false
                    },
                    "Отмена":function (){
                        dial_box.dialog("close");
                        dial_box.empty();
                    }
                }
            });
            dial_box.dialog("open");
        }
    );
}

/**
 * @param xhr object
 * @param returned boolean
 * @returns {boolean}
 */
function check_ajax_header (xhr, returned) {
    if ((xhr.getResponseHeader("location") !== undefined)
        && (xhr.getResponseHeader("location") !== null)) {
        window.location.replace(xhr.getResponseHeader("location"));
        if (returned) return false
    }
    else if (returned) return true
}

function get_locked() {
    var xhr=$.get(
        "./index.php",
        {act:"get_locked_users"},
        function (data) {
            if (check_ajax_header(xhr, true)) {
                if (data != "false") {
                    var templatediv = "<table id='locked_list'>" +
                        "<thead><tr>" +
                        "<th></th><th>Пользователь</th><th>Время<br />блокировки</th>" +
                        "</tr></thead>" +
                        "<tbody></tbody></div>";
                    dial_box.html(templatediv);
                    pdata = JSON.parse(data);
                    $("#locked_list").fancytree({
                        idPrefix: "ftlu_",
                        cookieId: "ftlu_",
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
                            var node = d.node,
                                $tdList = $(node.tr).find(">td");
                            $tdList.eq(2).text(node.data.tstamp)
                        }
                    });
                    dial_box.dialog({
                        title: "Список заблокированных пользователей",
                        modal: true,
                        width: "600px",
                        position: {my: "center top", at: "center top+10%", of: window},
                        resizable: false,
                        buttons: {
                            "Разблокировать": function () {
                                $("#dialog_div").dialog("close");
                                s = $.map($("#locked_list").fancytree("getTree").getSelectedNodes(), function (node) {
                                    return node.key;
                                });
                                unlock_users(s);
                            },
                            "Закрыть": function () {
                                dial_box.dialog("close");
                                dial_box.empty();
                            }
                        }
                    });
                    dial_box.dialog("open");
                }
                else {
                    alert("Нет заблокированых пользователей")
                }
            }
        }
    );
}

function unlock_users(users) {
    var xhr = $.get(
        "./index.php",
        {
            act:"unlock_users",
            ul:users
        },
        function (data) {
            if (check_ajax_header(xhr, true)) {
                alert(data)
            }
        }
    )
}

function exit() {
    $("#tree").fancytree("getTree").clearCookies();
    $("#tree_objects").fancytree("getTree").clearCookies();
    window.location.replace(window.location + "?exit");
}
