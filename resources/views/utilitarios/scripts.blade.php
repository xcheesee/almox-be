<script>
    var maskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
    options = {onKeyPress: function(val, e, field, options) {
            field.mask(maskBehavior.apply({}, arguments), options);
        }
    };

    $(document).ready(function(){
        $('.date').mask('00/00/0000');
        $('.time').mask('00:00:00');
        $('.date_time').mask('00/00/0000 00:00:00');
        $('.cep').mask('00000-000');
        $('.phone').mask(maskBehavior,options);
        $('.phone_with_ddd').mask('(00) 0000-0000');
        $('.phone_us').mask('(000) 000-0000');
        $('.rf').mask('000000-0');
        $('.cpf').mask('000.000.000-00', {reverse: true});
        $('.money').mask('000.000.000.000.000,00', {reverse: true});
        $('.numerico').mask('0000000000000000.00', {reverse: true});
        $('.processo_sei').mask('0000.0000/0000000-0');
        $('.contrato').mask('000/SSSS/0000');
        //$('.mixed').mask('AAA 000-S0S'); //para exemplo de definição de custom masks

        $('.jmulti').multiSelect({
            selectableHeader: "<input type='text' class='search-input form-control' autocomplete='off' placeholder='Clique na lista abaixo para selecionar, digite aqui para filtrar'>",
            selectionHeader: "<input type='text' class='search-input form-control' autocomplete='off' placeholder='Clique na lista abaixo para remover, digite aqui para filtrar'>",
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

    var loadPreviewFoto = function(event) {
      var previewFoto = document.getElementById('previewFoto');
      previewFoto.src = URL.createObjectURL(event.target.files[0]);
      previewFoto.onload = function() {
        URL.revokeObjectURL(previewFoto.src) // free memory
      }
    };

    function iniciarDatePicker(divId){
        //verificar no bloco de notas do Portal NDTIC como criar o elemento
        new tempusDominus.TempusDominus(document.getElementById(divId), {
            localization: {
            today: 'Hoje',
            clear: 'Limpar',
            close: 'Fechar',
            selectMonth: 'Selecione o Mês',
            previousMonth: 'Mês Anterior',
            nextMonth: 'Próximo Mês',
            selectYear: 'Selecione o Ano',
            previousYear: 'Ano Anterior',
            nextYear: 'Próximo Ano',
            selectDecade: 'Selecione a Década',
            previousDecade: 'Década Anterior',
            nextDecade: 'Próxima Década',
            previousCentury: 'Século Anterior',
            nextCentury: 'Próximo Século',
            pickHour: 'Selecione a Hora',
            incrementHour: 'Incrementar Hora',
            decrementHour: 'Decrementar hora',
            pickMinute: 'Selecione o Minuto',
            incrementMinute: 'Incrementar Minuto',
            decrementMinute: 'Decrementar Minuto',
            pickSecond: 'Selecione o Segundo',
            incrementSecond: 'Incrementar Segundo',
            decrementSecond: 'Decrementar Segundo',
            toggleMeridiem: 'Alternar Meridiano',
            selectTime: 'Selecione Horário',
            selectDate: 'Selecione Data',
            dayViewHeaderFormat: 'short',
            locale: 'pt-BR'
            },
            display: {
                components: {
                    date: true,
                    decades: true,
                    month: true,
                    year: true,
                    hours: false,
                    seconds: false,
                    minutes: false,
                    useTwentyfourHour: true,
                },
                buttons: {
                    close: true,
                },
            },
            hooks: {
                inputParse: (context, value) => {
                    var arrData = value.split('/');
                    var stringFormatada = arrData[1] + '-' + arrData[0] + '-' + arrData[2];
                    return new tempusDominus.DateTime(stringFormatada);
                }
            }
        });
    }

    function consultaCEP(cep,id_endereco,id_bairro,id_cidade){
        numcep = parseInt(cep.replace('-',''));

        $('#spinner-div').show(); //Exibe spinner de loading (template base)
        jQuery.ajax({
            url : "https://viacep.com.br/ws/"+cep+"/json/",
            type : "GET",
            dataType : "json",
            success:function(data)
            {
                console.log(data);
                $('#'+id_endereco).val(data.logradouro);
                $('#'+id_bairro).val(data.bairro);
                //$('#'+id_uf).val(data.uf);
                $('#'+id_cidade).val(data.localidade);
            },
            complete: function () {
                $('#spinner-div').hide(); //Oculta o spinner ao completar a request
            }
        });
    }

    function carregaLocais(dptcampo, divid){
        var val = jQuery(dptcampo).val();
        jQuery('select[name="'+divid+'"]').empty();
        if(divid)
        {
            $('#spinner-div').show();
            jQuery.ajax({
                url : '{{ env("APP_URL") }}/locais/' +val+'/filtrar',
                type : "GET",
                dataType : "json",
                success:function(data)
                {
                    //console.log(data);
                    jQuery('select[name="'+divid+'"]').prop('disabled', false);
                    $('select[name="'+divid+'"]').append('<option value="">--Selecione--</option>');
                    jQuery.each(data.locais, function(key,value){
                        $('select[name="'+divid+'"]').append('<option value="'+ value.id +'">'+ value.nome +'</option>');
                    });
                },
                complete: function () {
                    $('#spinner-div').hide(); //Oculta o spinner ao completar a request
                }
            });
        }
    }

    function carregaItems(id){
        $('#spinner-div').show();
        jQuery('select[name="items_procura"]').empty();
        jQuery.ajax({
            url : '{{ env("APP_URL") }}/api/items/tipo/'+id,
            type : "GET",
            dataType : "json",
            success:function(data)
            {
                //console.log(data);
                jQuery('select[name="items_procura"]').prop('disabled', false);
                $('select[name="items_procura"]').append('<option value="">--Selecione--</option>');
                jQuery.each(data.data, function(key,value){
                    $('select[name="items_procura"]').append('<option data-medida="'+value.medida+'" value="'+ value.id +'">'+ value.nome +'</option>');
                });
            },
            complete: function () {
                $('#spinner-div').hide(); //Oculta o spinner ao completar a request
            }
        });
    }

    function adicionarItem(tabela){
        var idval = $('select[name="items_procura"]').val();
        if(idval) {
            var txt = $('select[name="items_procura"] option:selected').text();
            const index = items_adicionados.indexOf(parseInt(idval));
            if (index < 0){
                $('#lista_materiais').append(
                    '<div class="row m-3" id="item_'+idval+'">'+
                    '<div class="form-group col col-6">'+
                        '<label class="control-label"><strong>Material: </strong></label>'+
                        '<input type="text" name="'+tabela+'['+idval+'][txt]" class="form-control" readonly value="'+txt.replaceAll("\"", "'")+'">'+
                        '<input type="hidden" name="'+tabela+'['+idval+'][id]" value="'+idval+'">'+
                        '<input type="hidden" name="'+tabela+'['+idval+'][key]" value="0">'+
                    '</div>'+
                    '<div class="form-group col col-3">'+
                        '<label class="control-label"><strong>Qtd: </strong></label>'+
                        '<input type="text" name="'+tabela+'['+idval+'][quantidade]" class="form-control required">'+
                    '</div>'+
                    '<div class="form-group col col-2">'+
                        '<a onclick="removerItem('+idval+')" class="btn btn-primary mt-4"><i class="far fa-trash-alt"></i></a>'+
                    '</div>'+
                    '</div>'
                );
                items_adicionados.push(parseInt(idval));
                console.log(items_adicionados);
            }else{
                console.log("já tem esse item na lista");
            }
        }else{
            console.log("Favor escolher um item");
        }
    }

    function removerItem(id){
        $('#item_'+id).remove();

        const index = items_adicionados.indexOf(id);
        if (index > -1) {
            items_adicionados.splice(index, 1);
        }
        console.log(items_adicionados);
    }

    //$("select").bsMultiSelect();
  </script>
