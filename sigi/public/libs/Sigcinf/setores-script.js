var setorControl = {

    getDados : function(){
        
        // arruma o array pra passar pro cadastro        
        var _dados = {};
        _dados['id'] = $("form[name='formSetores'] input[name='setor[id]']").val();
        _dados['centro'] = $("form[name='formSetores'] select[name='setor[centro]']").val()
        _dados['nome'] = $("form[name='formSetores'] input[name='setor[nome]']").val();
        _dados['email'] = $("form[name='formSetores'] input[name='setor[email]']").val();
        
        return _dados;
    },

    submit : function(){
        
        var _url = udev.url.getUrlController();
        var _msg = '';

        if ($("form[name='formSetores'] input[name='setor[id]']").val() > 0) {
            _url += '/atualizar/';
            _msg = 'Setor alterado com sucesso';
        } else {
            _url += '/adicionar/';
            _msg = 'Setor inserido com sucesso';
        };
        
        $.post(_url, this.getDados(), function(response) {
            if (response.result == 'success') {
                recordTable.ajax.reload();
                $("#modalformSetores").modal('hide');    
                viewModalControl.configuraModal({'#btnConfirmar':false});
                viewModalControl.show(_msg,'Sua solicitação foi concluída com sucesso',true);            
            } else {
                viewModalControl.show('Confira os erros informados',response.msg);
            }
        });

    },

}

var viewSetorControl = {

    inicializar: function(){
        
        // inicializa a lista de setores
        let _url = udev.url.getUrlController() + '/getAll/';

        recordTable = $("#tabelaSetores").DataTable({

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
                    targets: [3],
                    className: "text-center"
                },
                {
                    targets: [0,1],
                    className: "dt-middle"
                }
            ],
            columns: [
                {
                    "data": "nome",
                },
                {
                    "data": "email",
                },
                {
                    render: function (row, type, val, meta) {
                        return val.unidade.abrev;
                    }
                },
                {
                    sortable: false,
                    render: function(row, type, val, meta) {
                        return `<span 
                                    name="btAlterar" 
                                    class="btn btn-sm btn-warning glyphicon glyphicon-pencil " 
                                    title="Clique para alterar."
                                    onClick="viewSetorControl.editItem(${val.id})">
                                </span>
                               `;
                    }
                }
            ]
        });

    },

    redrawUnidades: function () {
        // monta as opções de categorias
        $.get('/Sigcinf/Unidades/getAll/', function (response) {
            if (response.result == 'success') {
                var _listacentros = response.data;
                $.each( _listacentros, function (_i, _centro) {
                    let _opcao = "<option value='" + _centro['id'] + "'>" + _centro['abrev'] + "</option>";
                    $("select[name='setor[centro]']").append(_opcao);
                });
            }
        });
    },

    buscar: function buscar( _id ) { 

        let _rowId = "#" + _id;
        let _rowData = recordTable.row(_rowId).data();

        $("form[name='formSetores'] input[name='setor[id]']").val( _id );
        $("form[name='formSetores'] input[name='setor[nome]']").val( _rowData.nome );
        $("form[name='formSetores'] input[name='setor[email]']").val( _rowData.email );
        $("form[name='formSetores'] select[name='setor[centro]']").val(_rowData.unidade.id);

    },

    editItem: function editItem(_id) {
        $("form[name='formSetores']").trigger("reset");
        viewSetorControl.buscar( _id );
        $("form[name='formSetores']").data("bootstrapValidator").resetForm();
        $("#modalformSetores").modal('show');
    },

}


$(document).ready(function() {

    viewModalControl.hide();
    viewSetorControl.redrawUnidades();
    viewSetorControl.inicializar();

    $("button[name='btNovo']").on( "click", function( _evento) {
        $("form[name='formSetores']").trigger("reset");
        $("form[name='formSetores']").data("bootstrapValidator").resetForm();
        $("form[name='formSetores'] input[name='setor[id]']").val("");
        $("#modalformSetores").modal('show');
    });

    $("form[name='formSetores'] button[name='btSalvar']").on( 'click', function( _evento ) {        
        var validator = $("form[name='formSetores']").data("bootstrapValidator");
        validator.validate();
        if(validator.isValid()) { 
            setorControl.submit();
        }
    });

    $("form[name='formSetores'] button[name='btCancelar']").on( 'click', function( _evento ) {
        $("#modalformSetores").modal('hide');
    });

    $("form[name='formSetores']").on( 'submit', function( _evento ) {
        _evento.preventDefault();
        _evento.stopPropagation();
    });

    $("form[name='formSetores']").bootstrapValidator({

        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: [':disabled'], // Para abranger os campos em modal
        submitButtons: 'button[name="btSalvar"]',

        fields: {

           "setor[nome]": {
                validators: {
                    notEmpty: {
                        message: "A nome do setor é obrigatório."
                    }
                }
            }

        }
    });

});