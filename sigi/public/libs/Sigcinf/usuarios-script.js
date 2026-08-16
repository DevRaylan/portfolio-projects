var usuarioControl = {

    usuario : {},

    getDados : function(){
        
        // arruma o array pra passar pro cadastro        
        var _dados = {};
        _dados['id'] = $("form[name='formUsuarios'] input[name='usuario[id]']").val();
        _dados['centro'] = $("form[name='formUsuarios'] select[name='usuario[centro]']").val();
        _dados['setor'] = $("form[name='formUsuarios'] select[name='usuario[setor]']").val()
        _dados['nome'] = $("form[name='formUsuarios'] input[name='usuario[nome]']").val();
        _dados['cpf'] = $("form[name='formUsuarios'] input[name='usuario[cpf]']").val();
        _dados['email'] = $("form[name='formUsuarios'] input[name='usuario[email]']").val();
        
        return _dados;
    },

    submit : function(){
        
        var _url = udev.url.getUrlController();
        var _msg = '';

        console.log(this.getDados());

        if ($("form[name='formUsuarios'] input[name='usuario[id]']").val() > 0) {
            _url += '/atualizar/';
            _msg = 'Usuário alterado com sucesso';
        } else {
            _url += '/adicionar/';
            _msg = 'Usuário inserido com sucesso';
        };
        
        $.post(_url, this.getDados(), function(response) {
            if (response.result == 'success') {
                recordTable.ajax.reload();
                $("#modalformUsuarios").modal('hide');    
                viewModalControl.configuraModal({'#btnConfirmar':false});
                viewModalControl.show(_msg,'Sua solicitação foi concluída com sucesso',true);            
            } else {
                viewModalControl.show('Confira os erros informados',response.msg);
            }
        });

    },

}

var viewUsuarioControl = {

    inicializar: function(){
        
        // inicializa a lista de usuarios
        let _url = udev.url.getUrlController() + '/getAll/';

        recordTable = $("#tabelaUsuarios").DataTable({

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
                    targets: [5],
                    className: "text-center"
                },
                {
                    targets: [0,1],
                    className: "dt-middle"
                }
            ],
            columns: [
                {
                    "data": "cpf",
                },
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
                    render: function (row, type, val, meta) {

                        if (val.setor === null) {
                            return '';
                        } else {
                            return val.setor.nome;
                        }
                        
                    }
                },
                {
                    sortable: false,
                    render: function(row, type, val, meta) {
                        return `<span 
                                    name="btAlterar" 
                                    class="btn btn-sm btn-warning glyphicon glyphicon-pencil " 
                                    title="Clique para alterar."
                                    onClick="viewUsuarioControl.editItem(${val.id})">
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

        $("form[name='formUsuarios'] input[name='usuario[id]']").val( _id );
        $("form[name='formUsuarios'] input[name='usuario[nome]']").val( _rowData.nome );
        $("form[name='formUsuarios'] input[name='usuario[cpf]']").val( _rowData.cpf );
        $("form[name='formUsuarios'] input[name='usuario[email]']").val( _rowData.email );
        $("select[name='usuario[centro]']").val(_rowData.unidade.id).trigger("change");

        // Aguarda o carregamento dos setores antes de definir o valor
        App.redrawSetores(_rowData.unidade.id, function () {
            $("select[name='usuario[setor]']").val(_rowData.setor.id).trigger("change");
        });

    },

    editItem: function editItem(_id) {
        $("form[name='formUsuarios']").trigger("reset");
        viewUsuarioControl.buscar( _id );
        $("form[name='formUsuarios']").data("bootstrapValidator").resetForm();
        $("#modalformUsuarios").modal('show');
    },

}

var App = {
    redrawUnidades: function () {
        // Monta as opções de centros
        $.get('/Sigcinf/Unidades/getAll/', function (response) {
            if (response.result == 'success') {
                var _listacentros = response.data;
                var unidadeSelect = $("select[name='usuario[centro]']");
                unidadeSelect.empty(); // Limpa opções anteriores
    
                $.each(_listacentros, function (_i, _centro) {
                    let _opcao = "<option value='" + _centro['id'] + "'>" + _centro['abrev'] + "</option>";
                    unidadeSelect.append(_opcao);
                });
    
                // Configura o evento de mudança para atualizar setores
                unidadeSelect.on('change', function () {
                    var unidadeId = $(this).val();
                    App.redrawSetores(unidadeId);
                });
    
                // Dispara a carga inicial dos setores da primeira unidade
                if (_listacentros.length > 0) {
                    App.redrawSetores(_listacentros[0].id);
                }
            }
        });
    },

    redrawSetores: function (unidadeId, callback) {
        if (unidadeId) {
            $.get('/Sigcinf/Setores/getByUnidade/', { unidadeId: unidadeId }, function (response) {
                if (response.result == 'success') {
                    var _listasetores = response.data;
                    var setoresSelect = $("select[name='usuario[setor]']");
                    setoresSelect.empty(); // Limpa opções anteriores

                    // Adiciona os setores ao select
                    $.each(_listasetores, function (_i, _setor) {
                        let _opcao = "<option value='" + _setor['id'] + "'>" + _setor['nome'] + "</option>";
                        setoresSelect.append(_opcao);
                    });

                    // Executa o callback após carregar os setores
                    if (callback && typeof callback === 'function') {
                        callback();
                    }
                }
            });
        } else {
            $("select[name='usuario[setor]']").empty();
        }
    }

};


$(document).ready(function() {

    viewModalControl.hide();

    App.redrawUnidades();
    viewUsuarioControl.inicializar();

    // Aguarda um momento para garantir que os selects foram preenchidos
    setTimeout(function() {
        // Verifica se já não existe a opção "Selecione"
        if ($("select[name='usuario[centro]'] option[value='']").length === 0) {
            $("select[name='usuario[centro]']").prepend('<option value="">Selecione</option>');
        }
        if ($("select[name='usuario[setor]'] option[value='']").length === 0) {
            $("select[name='usuario[setor]']").prepend('<option value="">Selecione</option>');
        }

        // Seleciona a primeira opção dos selects
        $("select[name='usuario[centro]']").val("").trigger('change');
        $("select[name='usuario[setor]']").val("").trigger('change');
    }, 100);

    $("button[name='btNovo']").on( "click", function( _evento) {
        $("form[name='formUsuarios']").trigger("reset");
        $("form[name='formUsuarios']").data("bootstrapValidator").resetForm();
        $("form[name='formUsuarios'] input[name='usuario[id]']").val("");
        $("#modalformUsuarios").modal('show');
    });

    $("form[name='formUsuarios'] button[name='btSalvar']").on( 'click', function( _evento ) {        
        var validator = $("form[name='formUsuarios']").data("bootstrapValidator");
        validator.validate();
        if(validator.isValid()) { 
            usuarioControl.submit();
        }
    });

    $("form[name='formUsuarios'] button[name='btCancelar']").on( 'click', function( _evento ) {
        $("#modalformUsuarios").modal('hide');
    });

    $("form[name='formUsuarios']").on( 'submit', function( _evento ) {
        _evento.preventDefault();
        _evento.stopPropagation();
    });

    $("form[name='formUsuarios']").bootstrapValidator({

        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: [':disabled'], // Para abranger os campos em modal
        submitButtons: 'button[name="btSalvar"]',

        fields: {

           "usuario[nome]": {
                validators: {
                    notEmpty: {
                        message: "A nome do usuario é obrigatório."
                    }
                }
            },

            "usuario[cpf]": {
                validators: {
                    notEmpty: {
                        message: "A cpf do usuario é obrigatório."
                    }
                }
            }

        }
    });

});