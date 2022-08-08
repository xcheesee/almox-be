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

    //$("select").bsMultiSelect();
  </script>
