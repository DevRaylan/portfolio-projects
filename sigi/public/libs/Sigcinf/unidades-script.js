var unidadeudescControl = {

    unidadeudesc : {},

    getDados : function(){
        
        // arruma o array pra passar pro cadastro        
        var _dados = {};
        _dados['id'] = $("form[name='formUnidades'] input[name='unidadeudesc[id]']").val();
        _dados['nome'] = $("form[name='formUnidades'] input[name='unidadeudesc[nome]']").val();
        _dados['abrev'] = $("form[name='formUnidades'] input[name='unidadeudesc[abrev]']").val();
        
        return _dados;
    },

    submit : function(){
        
        console.log(this.getDados())

        var _url = udev.url.getUrlController();
        var _msg = '';
            
        if ($("form[name='formUnidades'] input[name='unidadeudesc[id]']").val() > 0) {
            _url += '/atualizar/';
            _msg = 'Centro Udesc alterado com sucesso';
        } else {
            _url += '/adicionar/';
            _msg = 'Centro Udesc inserido com sucesso';
        }
        
        $.post(_url, this.getDados(), function(response) {
            if (response.result == 'success') {
                recordTable.ajax.reload();
                $("#modalformUnidades").modal('hide');  
                viewModalControl.configuraModal({'#btnConfirmar':false});
                viewModalControl.show(_msg,'Sua solicitação foi concluída com sucesso',true);              
            } else {
                viewModalControl.show('Confira os erros informados',response.msg);
            }
        });

    },

}

var viewUnidadeUdescControl = {

    inicializar: function(){
        
        // inicializa a lista de Unidades Udesc
        let _url = udev.url.getUrlController() + '/getAll/';

        recordTable = $("#tabelaUnidades").DataTable({

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
                    targets: [0,1,2],
                    className: "text-center"
                }
            ],
            columns: [

                {
                    "data": "abrev",
                },

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
                                    onClick="viewUnidadeUdescControl.editItem(${val.id})">
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

        $("form[name='formUnidades'] input[name='unidadeudesc[id]']").val( _id );
        $("form[name='formUnidades'] input[name='unidadeudesc[nome]']").val( _rowData.nome );
        $("form[name='formUnidades'] input[name='unidadeudesc[abrev]']").val( _rowData.abrev );
    },

    editItem: function editItem(_id) {
        $("form[name='formUnidades']").trigger("reset");
        viewUnidadeUdescControl.buscar( _id );
        $("form[name='formUnidades']").data("bootstrapValidator").resetForm();
        $("#modalformUnidades").modal('show');
    },

}


$(document).ready(function() {

    viewModalControl.hide();

    viewUnidadeUdescControl.inicializar();

    $("button[name='btNovo']").on( "click", function( _evento) {
        $("form[name='formUnidades']").trigger("reset");
        $("form[name='formUnidades']").data("bootstrapValidator").resetForm();
        $("form[name='formUnidades'] input[name='unidadeudesc[id]']").val("");
        $("#modalformUnidades").modal('show');
    });

    $("form[name='formUnidades'] button[name='btSalvar']").on( 'click', function( _evento ) {
        
        var validator = $("form[name='formUnidades']").data("bootstrapValidator");
        validator.validate();
        if(validator.isValid()) { 
            unidadeudescControl.submit();
        }

    });

    $("form[name='formUnidades'] button[name='btCancelar']").on( 'click', function( _evento ) {
        $("#modalformUnidades").modal('hide');
    });

    $("form[name='formUnidades']").on( 'submit', function( _evento ) {
        _evento.preventDefault();
        _evento.stopPropagation();
    });

    $("form[name='formUnidades']").bootstrapValidator({

        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: [':disabled'], // Para abranger os campos em modal
        submitButtons: 'button[name="btSalvar"]',

        fields: {

            "unidadeudesc[nome]": {
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
            },

            "unidadeudesc[abrev]": {
                validators: {
                    notEmpty: {
                        message: "A abreviatura é obrigatória."
                    }
                }
            }

        }
    });

});