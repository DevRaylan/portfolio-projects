var categoriasControl = {

    getDados : function(){
        
        // arruma o array pra passar pro cadastro        
        var _dados = {};
        _dados['id'] = $("form[name='formCategorias'] input[name='categorias[id]']").val();
        _dados['nome'] = $("form[name='formCategorias'] input[name='categorias[nome]']").val();
        
        return _dados;
    },

    submit : function(){
        
        var _url = udev.url.getUrlController();
        var _msg = '';
            
        if ($("form[name='formCategorias'] input[name='categorias[id]']").val() > 0) {
            _url += '/atualizar/';
            _msg = 'Categoria alterada com sucesso';
        } else {
            _url += '/adicionar/';
            _msg = 'Categoria inserida com sucesso';
        }
        
        $.post(_url, this.getDados(), function(response) {
            if (response.result == 'success') {
                recordTable.ajax.reload();
                $("#modalformCategorias").modal('hide');  
                viewModalControl.configuraModal({'#btnConfirmar':false});
                viewModalControl.show(_msg,'Sua solicitação foi concluída com sucesso',true);              
            } else {
                viewModalControl.show('Confira os erros informados',response.msg);
            }
        });

    },

}

var viewCategoriasControl = {

    inicializar: function(){
        
        let _url = udev.url.getUrlController() + '/getAll/';

        recordTable = $("#tabelaCategorias").DataTable({

            ajax: {
                "type": "POST",
                "processing": true,
                "format": "json",
                "url": _url,
                "dataFilter": function(json) {
                    var json = $.parseJSON(json);
                    json.data = $.map(json.data, function(e) {
                        return e
                    });
                    return JSON.stringify(json);
                }
            },
            rowId: "id",
            columnDefs: [
                {
                    targets: [0,1],
                    className: "text-center"
                }
            ],
            columns: [

                {
                    "data": "nome",
                },

                {
                    sortable: false,
                    render: function(row, type, val, meta) {
                        return `<span 
                                    name="btAlterar" 
                                    class="btn btn-sm btn-warning glyphicon glyphicon-pencil " 
                                    title="Clique para alterar."
                                    onClick="viewCategoriasControl.editItem(${val.id})">
                                </span>
                               `;
                    }
                }
            ]
        });

    },

    buscar: function buscar( _id ) { 

        let _rowId = "#" + _id;
        let _rowData = recordTable.row(_rowId).data();

        $("form[name='formCategorias'] input[name='categorias[id]']").val( _id );
        $("form[name='formCategorias'] input[name='categorias[nome]']").val( _rowData.nome );
    },

    editItem: function editItem(_id) {
        $("form[name='formCategorias']").trigger("reset");
        viewCategoriasControl.buscar( _id );
        $("form[name='formCategorias']").data("bootstrapValidator").resetForm();
        $("#modalformCategorias").modal('show');
    },

}


$(document).ready(function() {

    viewModalControl.hide();

    viewCategoriasControl.inicializar();

    $("button[name='btNovo']").on( "click", function( _evento) {
        $("form[name='formCategorias']").trigger("reset");
        $("form[name='formCategorias']").data("bootstrapValidator").resetForm();
        $("form[name='formCategorias'] input[name='categorias[id]']").val("");
        $("#modalformCategorias").modal('show');
    });

    $("form[name='formCategorias'] button[name='btSalvar']").on( 'click', function( _evento ) {
        
        var validator = $("form[name='formCategorias']").data("bootstrapValidator");
        validator.validate();
        if(validator.isValid()) { 
            categoriasControl.submit();
        }

    });

    $("form[name='formCategorias'] button[name='btCancelar']").on( 'click', function( _evento ) {
        $("#modalformCategorias").modal('hide');
    });

    $("form[name='formCategorias']").on( 'submit', function( _evento ) {
        _evento.preventDefault();
        _evento.stopPropagation();
    });

    $("form[name='formCategorias']").bootstrapValidator({

        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: [':disabled'], // Para abranger os campos em modal
        submitButtons: 'button[name="btSalvar"]',

        fields: {

            "categorias[nome]": {
                validators: {
                    notEmpty: {
                        message: "O Nome UDESC é obrigatório."
                    },
                    callback: {
                        callback: function( _valor, _validator, _campo ) {

                            // Tirar espaÃ§os excedentes.
                            _valor = _valor.trim().replace(/\s{2,}/g, ' ');

                            // Tirar caracteres invÃ¡lidos.
                            _valor = _valor.replace(/\"/g, '');

                            if( _valor.length < 4 || _valor.length > 50 ) {

                                return( {
                                    valid: false,
                                    message: 'Mínimo de 4 letras, máximo de 50.'
                                } );
                            }

                            return( {
                                valid: true
                            } );
                        }
                    }
                }
            }

        }
    });

});