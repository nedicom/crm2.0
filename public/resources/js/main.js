$(document).ready(function() {
    // При клике на кнопку 'Добавить дело'
    // берем значения аттрибутов (имя клиента, id клиента) и подставляем по умолчанию в input text, input hidden
    $(".addDeal").on('click', function() {
        var clientId = $(this).attr('data-client-id');
        document.getElementById("clientId").value = clientId;
    });

    // При клике на кнопку 'Добавить задачу' из раздела 'Клиенты'
    // берем значения аттрибутов (имя клиента, id клиента) и подставляем по умолчанию в input text, input hidden
    $(".nameToForm").click(function(el) {
        // Заполнение формы при создании задачи
        if (!$(this).hasClass('edit')) {
            fillForm($(this));
        }

        if ($(this).hasClass('lead')) {
            var leadIdValue = $(this).attr('data-lead-id');
            alert(leadIdValue);
            document.getElementById('lead_id').value = leadIdValue;            
            var TaskType = $(this).attr('data-type');
            document.getElementById('tasktype').value = TaskType;
        }

        if ($(this).hasClass('clientTask')) {
            var namevalue = $(this).attr('data-client');
            var clientIdValue = $(this).attr('data-value-id');
            document.getElementById("client").value = namevalue;
            document.getElementById("clientidinput").value = clientIdValue;

            var taskIdValue = null;
            if (typeof $(this).attr('data-task-id') !== typeof undefined && $(this).attr('data-task-id') !== false) {
                taskIdValue = $(this).attr('data-task-id');
            }

            // Подгружаем список дел клиента
            $.ajax({
                url: "/tasks/get-deals",
                method: "POST",
                data: {clientId: clientIdValue, taskId: taskIdValue},
                success: function (data) {
                    $('.list-deals-block').html(data);
                }
            });
        }
    });

    // Заполнение полей формы в модальном окне "Добавить задачу" в разделе Клиенты
    function fillForm(element) {
        var type = element.attr('data-type');        
        document.getElementById("taskname").innerHTML = type;
        document.getElementById("tasktype").value = type;
        document.getElementById("nameoftask").value = type;
        document.getElementById("type").value = type;
        var collection = document.getElementsByClassName("hideme")
        for (let i = 0; i < collection.length; i++) {
            collection[i].style.display = "block";
        }
        document.getElementById("lawyer").value = element.attr('data-user-id');
        document.getElementById("soispolintel").value = element.attr('data-user-id');
        document.getElementById("soispolintel").value = element.attr('data-user-id');
        var now = new Date();
        now.setHours(23);
        now.setMinutes(00);
        document.getElementById("date").value = now.toISOString().slice(0,16);
    }

    $('[data-toggle="tooltip"]').tooltip();

    // Фильтр по дате/выбору пользователя на Главной странице в админке
    $('.input-home-filter').on('change', function () {
        $('#form-filter').submit();
    });

    // Копирование ссылки на Главной странице в админке
    $('#btnurl').on('click', function () {
        // Get the text field
        var copyText = document.getElementById("calendarurl");
        // Select the text field
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices
        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);
        // Alert the copied text
        document.getElementById("btnurl").innerText = 'ссылка скопирована';
    });

    // Datetimepicker
    if (typeof $('input#date').datetimepicker !== "undefined") { //check if function exist to avoid err
        $('input#date').datetimepicker({
            lang: 'ru',
            step: 15, //more useful for lawyers
            minTime:'8:00',
            maxTime:'22:00'
        });
    }

    // Подгрузка списка клиентов при клике на поле "Клиент" у формы Задач
    $('#client').keyup(function() {
        var query = $(this).val();
        var quantity = $(this).val().length;

        if(quantity > 2) {
            $.ajax({
                url: "/getclient",
                method: "POST",
                data: {query:query},
                success: function (data) {
                    $('#clientList').fadeIn();
                    $('#clientList').html(data);
                }
            });
        }
    });

    // Выбор клиента из выпадающего списка в форме Задачи
    $(document).on('click', '.clientAJAX', function() {
        $('#clientidinput').val($(this).val());
        $('#client').val($(this).text());
        $('#clientList').fadeOut();
    });

    // Подгрузка списка платежей при клике на поле "Выбор платежа" у формы Задач
    $(document).on('keyup', '.payment-input', function() {
        var query = $(this).val();
        var quantity = $(this).val().length;
        var elem = $(this);

        if(quantity > 2) {
            $.ajax({
                url: "/payments/list/ajax",
                method: "POST",
                data: {query: query},
                success: function (data) {
                    var list = elem.parents($('.paymentsIndex')).children('.paymentsList');
                    list.fadeIn();
                    list.html(data);
                }
            });
        }
    });

    // Динамические поля платежей
    $("#add-payment").on('click', function() {
        $("#paymentsTable").append('<tr><td width="300">' +
            '<div class="payment-input-block">' +
            '<input type="text" name="payClient[]" placeholder="Введите имя клиента" class="payment-input form-control" />' +
            '<input type="hidden" name="payID[]" class="payment-id" value="" />' +
            '<div class="paymentsList" style="display:none"></div>' +
            '</div></td>' +
            '<td class="info-payment"></td>' +
            '<td><button type="button" class="btn btn-danger remove-tr">Удалить</button>' +
            '</td></tr>'
        );
    });

    // Удаление в таблице строки платежа
    $(document).on('click', '.remove-tr', function() {
        $(this).parents('tr').remove();
    });

    // Выбор платежа из выпадающего списка в форме Задачи
    $(document).on('click', '.paymentIndex', function() {
        var parent = $(this).parents($('.payment-input-block'));
        //alert($(this).attr('data-payment-id'));
        parent.children('.payment-id').val($(this).attr('data-payment-id'));
        parent.children('input.payment-input').val($(this).find($('.name-client')).text());
        $(this).parents($('#paymentsTable')).children('tr:last').children('td.info-payment').text($(this).text());
        $('.paymentsList').fadeOut();
    });

    // Подгрузка списка услуг при клике на поле "Название задачи" у формы Задач
    $(document).on('keyup', '.field-name-task', function() {
        var value = $(this).val();

        if (value.length >= 3) {
            $.ajax({
                url: "/services/ajax/list",
                data: {query: value},
                method: "GET",
                success: function (data) {
                    var list = $('.popup-list-services');
                    list.fadeIn();
                    list.html(data.content);
                }
            });
        }
    });

    // Выбор услуги из выпадающего списка в форме Задачи
    $(document).on('click', '.serviceIndex', function() {
        var valueId = $(this).attr('data-service-id');

        $.ajax({
            url: "/services/ajax/element",
            method: "POST",
            data: {serviceId: valueId},
            success: function (data) {
                if (data.success) {
                    $('.taskModal input[name="service_id"]').val(data.id);
                    $('.taskModal .service_ref_name').fadeIn();
                    $('.taskModal .service_ref_name .service_ref_val').text(data.name);
                    if (data.duration !== null) {
                        $('.taskModal input[name="duration[hours]"]').val(data.duration.hours);
                        $('.taskModal input[name="duration[minutes]"]').val(data.duration.minutes);
                    }
                } else {
                    $('.taskModal .service_ref_name').text(data.message).css('color', 'red');
                }
            },
            error: function (err) {
                console.log(err);
            },
        });
        $('.popup-list-services').fadeOut();
    });

    // Скрыть выпадающие список услуг в форме Задач
    $(document).on('click', '.popup-list-services .close', function () {
       $(this).parent($('.popup-list-services')).fadeOut();
    })
});


