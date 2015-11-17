(function($){
    $.fn.validettaLanguage = {};
    $.validettaLanguage = {
        init : function(){
            $.validettaLanguage.messages = {
                required	: 'Поле обязятельно к заполнению',
                email		: 'Некорректный адрес',
                number		: 'Должно быть числовое значение',
                maxLength	: 'Должно быть максимум {count} символов',
                minLength	: 'Должно быть минимум {count} символов',
                maxChecked  : 'Отметьте не более {count} фложков',
                minChecked  : 'Отметьте не менее {count} флажков',
                maxSelected : 'Укажите не более {count} значений',
                minSelected : 'Укажите не менее {count} значений',
                notEqual	: 'Поля не совпадают',
                different   : 'Поля не должны совпадать',
                creditCard  : 'Неверный номер кредитной карты'
            };
        }
    };
    $.validettaLanguage.init();
})(jQuery);