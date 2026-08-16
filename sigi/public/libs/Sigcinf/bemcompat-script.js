var bemcompatControl = {
    bemcompat: {},

    getDados: function () {
        // Coleta os dados do formulário para enviar ao servidor
        var _dados = {};
        _dados['id'] = $("form[name='formBemComPat'] input[name='bemcompat[id]']").val();
        _dados['patrimonio'] = $("form[name='formBemComPat'] input[name='bemcompat[patrimonio]']").val();
        _dados['descricao'] = $("form[name='formBemComPat'] input[name='bemcompat[descricao]']").val();
        _dados['status'] = $("form[name='formBemComPat'] select[name='bemcompat[status]']").val();
        _dados['centro'] = $("form[name='formBemComPat'] select[name='bemcompat[centro]']").val();
        _dados['setor'] = $("form[name='formBemComPat'] select[name='bemcompat[setor]']").val();

        return _dados;
    },

    submit: function () {
        var _url = udev.url.getUrlController();
        var _msg = '';

        // Define a URL e a mensagem com base no tipo de operação (atualizar ou adicionar)
        if ($("form[name='formBemComPat'] input[name='bemcompat[id]']").val() > 0) {
            _url += '/atualizar/';
            _msg = 'Bem com patrimônio alterado com sucesso';
        } else {
            _url += '/adicionar/';
            _msg = 'Bem com patrimônio inserido com sucesso';
        };

        // Envia os dados via AJAX
        $.post(_url, this.getDados(), function (response) {
            if (response.result == 'success') {
                recordTable.ajax.reload(); // Recarrega a tabela
                $("#modalformBemComPat").modal('hide'); // Fecha o modal
                viewModalControl.configuraModal({ '#btnConfirmar': false });
                viewModalControl.show(_msg, 'Sua solicitação foi concluída com sucesso', true);
            } else {
                viewModalControl.show('Confira os erros informados', response.msg);
            }
        });
    }
};

var viewBemComPatControl = {
    inicializar: function () {
        // Inicializa a tabela de bens patrimoniais usando DataTables
        let _url = udev.url.getUrlController() + '/getAll/';

        recordTable = $("#tabelaBemComPat").DataTable({
            ajax: {
                "type": "POST",
                "processing": true,
                "format": "json",
                "url": _url,
                "dataFilter": function (json) {
                    var json = $.parseJSON(json);
                    json.data = $.map(json.data, function (e) {
                        return e;
                    });
                    return JSON.stringify(json);
                }
            },
            rowId: "id",
            columnDefs: [
                { targets: [5], className: "text-center" },
                { targets: [0, 1], className: "dt-middle" }
            ],
            columns: [
                { "data": "patrimonio" },
                { "data": "descricao" },
                {
                    render: function (row, type, data, meta) {
                        return data.status == 'A' ? 'Ativo' : 'Inativo';
                    }
                },
                {
                    render: function (row, type, val, meta) {
                        return val.unidade.abrev;
                    }
                },
                {
                    render: function (row, type, val, meta) {
                        return val.setor.nome;
                    }
                },
                {
                    sortable: false,
                    render: function (row, type, val, meta) {
                        return `<span 
                                    name="btAlterar" 
                                    class="btn btn-sm btn-warning glyphicon glyphicon-pencil" 
                                    title="Clique para alterar."
                                    onClick="viewBemComPatControl.editItem(${val.id})">
                                </span>`;
                    }
                }
            ]
        });
    },

//    redrawSetores : function (unidadeId, callback) {
//        if (unidadeId) {
//            $.get('/Sigcinf/Setores/getByUnidade/', { unidadeId: unidadeId }, function (response) {
//                if (response.result == 'success') {
//                    var _listasetores = response.data;
//                    var setoresSelect = $("select[name='bemcompat[setor]']");
//                    setoresSelect.empty(); // Limpa opções anteriores
//    
//                    // Adiciona os setores ao dropdown
//                    $.each(_listasetores, function (_i, _setor) {
//                        let _opcao = "<option value='" + _setor['id'] + "'>" + _setor['nome'] + "</option>";
//                        setoresSelect.append(_opcao);
//                    });
//    
//                    // Executa o callback após carregar os setores
//                    if (callback && typeof callback === 'function') {
//                        callback();
//                    }
//                }
//            });
//        } else {
//            $("select[name='bemcompat[setor]']").empty();
//        }
//    },


    buscar: function buscar(_id) {
        let _rowId = "#" + _id;
        let _rowData = recordTable.row(_rowId).data();

        $("form[name='formBemComPat'] input[name='bemcompat[id]']").val(_id);
        $("form[name='formBemComPat'] input[name='bemcompat[patrimonio]']").val(_rowData.patrimonio);
        $("form[name='formBemComPat'] input[name='bemcompat[descricao]']").val(_rowData.descricao);
        $("form[name='formBemComPat'] select[name='bemcompat[status]']").val(_rowData.status);
        $("select[name='bemcompat[centro]']").val(_rowData.unidade.id).trigger("change");

        // Aguarda o carregamento dos setores antes de definir o valor
        App.redrawSetores(_rowData.unidade.id, function () {
            $("select[name='bemcompat[setor]']").val(_rowData.setor.id).trigger("change");
        });

    },

    editItem: function editItem(_id) {
        // Abre o modal para edição de um item existente
        $("form[name='formBemComPat']").trigger("reset");
        viewBemComPatControl.buscar(_id);
        $("form[name='formBemComPat']").data("bootstrapValidator").resetForm();
        $("#modalformBemComPat").modal('show');
    }
};

var App = {
    redrawUnidades: function () {
        // Carrega as unidades (centros) e preenche o select de centro
        $.get('/Sigcinf/Unidades/getByCentroModerador/', function (response) {
            if (response.result == 'success') {
                var _listacentros = response.data;
                var unidadeSelect = $("select[name='bemcompat[centro]']");
                unidadeSelect.empty(); // Limpa opções anteriores

                // Adiciona as unidades ao select
                $.each(_listacentros, function (_i, _centro) {
                    let _opcao = "<option value='" + _centro['id'] + "'>" + _centro['abrev'] + "</option>";
                    unidadeSelect.append(_opcao);
                });

                // Configura o evento de mudança para carregar os setores
                unidadeSelect.on('change', function () {
                    var unidadeId = $(this).val();
                    App.redrawSetores(unidadeId);
                });

                // Carrega os setores da primeira unidade (se houver)
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
                    var setoresSelect = $("select[name='bemcompat[setor]']");
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
            $("select[name='bemcompat[setor]']").empty();
        }
    }
};

$(document).ready(function () {
    viewModalControl.hide();
    
    App.redrawUnidades();
    viewBemComPatControl.inicializar();

    // Aguarda um momento para garantir que os selects foram preenchidos
    setTimeout(function() {
        // Verifica se já não existe a opção "Selecione"
        if ($("select[name='bemcompat[centro]'] option[value='']").length === 0) {
            $("select[name='bemcompat[centro]']").prepend('<option value="">Selecione</option>');
        }
        if ($("select[name='bemcompat[setor]'] option[value='']").length === 0) {
            $("select[name='bemcompat[setor]']").prepend('<option value="">Selecione</option>');
        }

        // Seleciona a primeira opção dos selects
        $("select[name='bemcompat[centro]']").val("").trigger('change');
        $("select[name='bemcompat[setor]']").val("").trigger('change');
    }, 100);

    // Evento do botão "Novo"
    $("button[name='btNovo']").on("click", function (_evento) {
        $("form[name='formBemComPat']").trigger("reset");
        $("select[name='bemcompat[centro]']").val("").trigger("change"); // Reseta o select de centro
        $("select[name='bemcompat[setor]']").val("").trigger("change"); // Reseta o select de setor
        $("form[name='formBemComPat'] input[name='bemcompat[id]']").val("");
        $("form[name='formBemComPat']").data("bootstrapValidator").resetForm();
        $("#modalformBemComPat").modal('show');
    });

    // Evento do botão "Salvar"
    $("form[name='formBemComPat'] button[name='btSalvar']").on('click', function (_evento) {
        var validator = $("form[name='formBemComPat']").data("bootstrapValidator");
        validator.validate();
        if (validator.isValid()) {
            bemcompatControl.submit();
        }
    });

    // Evento do botão "Cancelar"
    $("form[name='formBemComPat'] button[name='btCancelar']").on('click', function (_evento) {
        $("#modalformBemComPat").modal('hide');
    });

    // Impede o envio padrão do formulário
    $("form[name='formBemComPat']").on('submit', function (_evento) {
        _evento.preventDefault();
        _evento.stopPropagation();
    });

    // Configura a validação do formulário
    $("form[name='formBemComPat']").bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: [':disabled'],
        submitButtons: 'button[name="btSalvar"]',
        fields: {
            "bemcompat[patrimonio]": {
                validators: {
                    notEmpty: {
                        message: "O patrimônio do bem é obrigatório."
                    }
                }
            },
            "bemcompat[descricao]": {
                validators: {
                    notEmpty: {
                        message: "A descrição do bem é obrigatória."
                    }
                }
            }
        }
    });
});
