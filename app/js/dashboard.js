$(document).ready(function () {
    var ComponentsBootstrapSelect = function () {
        var handleBootstrapSelect = function () {
            $('.bs-select').selectpicker({
                iconBase: 'fa',
                tickIcon: 'fa-check',
                size: 10,
                dropupAuto: false
            });
        }
        return {
            //main function to initiate the module
            init: function () {
                handleBootstrapSelect();
            }
        };
    }();
    
    $(document).on("change", ".mine_change_check", function () {
        if ($(this).is(':checked')) {
            value = 1;
        } else {
            value = 0;
        }
        table = $(this).data('table');
        id = $(this).val();
        col = $(this).data('col');
        $.post('/cp/change_check/', {table: table, id: id, value: value, col : col}, function (data) {
            try {
                response = JSON.parse(data);
                if (response.status == 200) {
                } else {
                    throw new Error('status 500');
                }
            } catch (e) {
                console.log(e);
            }
        });
    });

    $('.mine_change_select').change(function () {
        table = $(this).data('table');
        id = $(this).data('id');
        value = $(this).find(':selected').val();
        col = $(this).data('col');
        $.post('/cp/change_select/', {table: table, id: id, value: value, col : col}, function (data) {
            try {
                response = JSON.parse(data);
                if (response.status == 200) {
                } else {
                    throw new Error('status 500');
                }
            } catch (e) {
                console.log(e);
            }
        });
    });

     // related items

     $(document).on('keyup', '#match', function(){
        
        var match = $(this).val();
        var controller = $(this).closest('table').data('controller');

        if(match != '') {

            $.post('/cp/'+controller+'/autocomplete/', {match: match}, function (data) {
                response = JSON.parse(data);

               console.log(response);
                try {
                    if (response.status == 200) {
                        $('.search_result').empty();
                        $.each(response.result, function (index, value) {
                            $('.search_result').append('<a class="search_result_item" id="'+value.id+'" href="/"><span class="title">'+ value.title+'</span></a><br>');

                        });
                        $('.search_result').show();

                    } else {
                        throw new Error('status 500');
                    }
                } catch (e) {
                    console.log(e);
                    alert('Произошла ошибка');
                }
            });
        
        } else {
            $('.search_result').hide(); 
        }
    });

    $(document).on('click', '.search_result_item', function(event) {
        
        var id = $(this).attr('id');
        var text = $(this).find('.title').text();
        
        $('#match').val('');
        $('#match').data('related', '');

        $('#match').val(text);
        $('#match').data('related', id);

        $('.search_result').hide(); 

        event.preventDefault();
    });

    $(document).on('click', '#add_related_to_item', function(event) {
        
        event.preventDefault();

        var ref = $(this);
        var item_id = $('#match').data('item');
        var related_id = $('#match').data('related');
        var text = $('#match').val();
        var controller = ref.closest('table').data('controller');

        $.post('/cp/'+controller+'/add_related_to_item/', {item_id: item_id, related_id: related_id}, function (data) {
            response = JSON.parse(data);

            try {
                if (response.status == 200) {
                    ref.parents('table').append(
                        '<tr>' +
                            '<td>'+text+'</td>' +
                            '<td width="100">' +
                                '<button id="delete_related_from_item" data-item="'+item_id+'" data-related="'+related_id+'" class="btn red"><i class="fa fa-trash-o"></i> Удалить</button>' +
                            '</td>' +
                        '</tr>'
                    );
                    $('#match').val('');
                } else {
                    throw new Error('status 500');
                }
            } catch (e) {
                console.log(e);
                alert('Произошла ошибка');
            }
        });

    });

    $(document).on('click', '#delete_related_from_item', function(event) {
        event.preventDefault();
        if (confirm('Вы уверены что хотите удалить?')) {
            var ref = $(this);
            var item_id = ref.data('item');
            var related_id = ref.data('related'); 
            var controller = ref.closest('table').data('controller');

            $.post('/cp/'+controller+'/delete_related_from_item/', {item_id: item_id, related_id: related_id}, function (data) {
                response = JSON.parse(data);
                console.log(response);
                try {
                    if (response.status == 200) {
                        ref.parents('tr').hide(300);
                    } else {
                        throw new Error('status 500');
                    }
                } catch (e) {
                    console.log(e);
                    alert('Во время удаления произошла ошибка');
                }
            })
        }
    });

    $('.accordion-toggle').on('click', function (event) {
        var accordion_body = $(this).attr('href');
        var status = $(accordion_body).attr('aria-expanded');
        if (status == 'true') {
            $('a.accordion-toggle>i.fa').removeClass('fa-minus');
            $('a.accordion-toggle>i.fa').addClass('fa-plus');
        } else {
            $('a.accordion-toggle>i.fa').removeClass('fa-plus');
            $('a.accordion-toggle>i.fa').addClass('fa-minus');
        }
    });

    $('body').on('click', '.mine_delete_row', function (event) {
        if (!confirm('Вы уверены?')) {
            event.preventDefault();
        }
    });

    $('.mine_delete_photo').on('click', function (event) {
        event.preventDefault();
        var self = $(this);
        if (confirm('Вы точно уверены, что хотите удалить данную фотографию?')) {
            var id = self.attr('data-id');
            var table = self.attr('data-table');
            var col = self.attr('data-col');
            $.post('/cp/delete_photo/', {table: table, id: id, col:col}, function (data) {
                response = JSON.parse(data);
                try {
                    if (response.status == 200) {
                        self.closest('.item').remove();
                    } else {
                        throw new Error('status 500');
                    }
                } catch (e) {
                    console.log(e);
                    alert('Во время удаления фотографии произошла ошибка');
                }
            })
        }
    });

    $('.mine_delete_img_row').on('click', function (event) {
        event.preventDefault();
        var self = $(this);
        if (confirm('Вы точно уверены, что хотите удалить данную фотографию?')) {
            var id = self.attr('data-id');
            var table = self.attr('data-table');
            $.post('/cp/delete_img_row/', {table: table, id: id}, function (data) {
                try {
                    response = JSON.parse(data);
                    if (response.status == 200) {
                        self.closest('.item').remove();
                    } else {
                        throw new Error('status 500');
                    }
                } catch (e) {
                    console.log(e);
                    alert('Во время удаления фотографии произошла ошибка');
                }
            });
        }
    });

    $('.mine_clear_file').on('click', function (event) {
        event.preventDefault();
        var self = $(this);
        if (confirm('Вы точно уверены, что хотите удалить данный файл?')) {
            var id = self.attr('data-id');
            var table = self.attr('data-table');
            var lang = self.attr('data-lang');
            $.post('/cp/delete_file/', {table: table, lang: lang, id: id}, function (data) {
                try {
                    response = JSON.parse(data);
                    if (response.status == 200) {
                        self.closest('.mt-element-card').remove();
                    } else {
                        throw new Error('status 500');
                    }
                } catch (e) {
                    console.log(e);
                    alert('Во время удаления файла произошла ошибка');
                }
            });
        }
    });
    $('.mine_datepicker').datepicker({
        format: 'dd.mm.yyyy',
        startDate: '+7d',
        autoclose: true
    });
    ComponentsBootstrapSelect.init();
    $('.multi_select').multiSelect({
        selectableHeader: "<input type='text' class='form-control' autocomplete='off' placeholder='Search'>",
        selectionHeader: "<input type='text' class='form-control' autocomplete='off' placeholder='Search'>",
        afterInit: function(ms){
          var that = this,
              $selectableSearch = that.$selectableUl.prev(),
              $selectionSearch = that.$selectionUl.prev(),
              selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
              selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';
      
          that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
          .on('keydown', function(e){
            if (e.which === 40){
              that.$selectableUl.focus();
              return false;
            }
          });
      
          that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
          .on('keydown', function(e){
            if (e.which == 40){
              that.$selectionUl.focus();
              return false;
            }
          });
        },
        afterSelect: function(){
          this.qs1.cache();
          this.qs2.cache();
        },
        afterDeselect: function(){
          this.qs1.cache();
          this.qs2.cache();
        }
      });
});