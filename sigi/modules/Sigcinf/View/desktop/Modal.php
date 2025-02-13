<?php /** modal que mostra mensagens de erro */ ?>
<div id="modalInfos" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-lg-11">
                        <h3></h3>
                    </div>
                    <div class="col-lg-1">
                        <button type="button" id="close" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fas fa-times"></i></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-body" style="padding-bottom:40px;">
            </div>
            <div class="modal-footer hidden">
                <button id="btnFechar" type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button id="btnConfirmar" type="button" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
var modalControl = {
    
    modal: {},
    
}

var viewModalControl = {
    
    show : function( titulo, msg, footer ){
        $('#modalInfos .modal-header h3').html(titulo);
        $('#modalInfos .modal-body').html(msg);
        if(footer){
            $('#modalInfos .modal-footer').removeClass('hidden');
        }else{
            $('#modalInfos .modal-footer').addClass('hidden');
        }
        $('#modalInfos').modal('show');
    },

    hide : function(){
        // ajusta a modal sempre que ela fechar
        $('#modalInfos').on('hidden.bs.modal', function (e) {
            if(!$('modalInfos .modal-footer').hasClass('hidden')){
                $('modalInfos .modal-footer').addClass('hidden');
            }
        });
    },

    configuraModal : function( itens ) {
        $.each( itens, function( key, value ) {
            if(value){
                $(key).text(value);
                $(key).show();
            }else{
                $(key).hide();
            }
        });
        
    },

    desativarModal : function(){
        $('#modalInfos').modal('hide');
        $('#modalInfos').on('hidden.bs.modal', function (e) {
            $('#btnConfirmar').on('click', function(e) {
                e.preventDefault();
                $('#modalInfos').modal('hide');
            });
        });
    }

}
</script>