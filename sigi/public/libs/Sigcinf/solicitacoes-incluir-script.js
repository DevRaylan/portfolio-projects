var solicitanteControl = {

    buscarUsuarioBeneficiado: function () {

        var _url = '/Sigcinf/Usuario/getByCpf1/';

        var _dados = {};
        _dados['cpf'] = $("#formBeneficiado input[name='beneficiado[busca]']").val();

        $.post(_url, _dados, function (response) {
            if (response.result == 'success') {
                var _usuario = response.data;
                $("#formBeneficiado input[name='beneficiado[nome]']").val(_usuario.nome);
                $("#formBeneficiado input[name='beneficiado[unidade]']").val(_usuario.unidade.abrev);
            } else {
                $("#formBeneficiado input[name='beneficiado[nome]']").val('');
                $("#formBeneficiado input[name='beneficiado[unidade]']").val('');
            }
        });

    },

    buscarUsuarioResponsavel: function () {

        var _url = '/Sigcinf/Usuario/getByCpf1/';

        var _dados = {};
        _dados['cpf'] = $("#formResponsavel input[name='responsavel[busca]']").val();

        $.post(_url, _dados, function (response) {
            if (response.result == 'success') {
                var _usuario = response.data;
                $("#formResponsavel input[name='responsavel[nome]']").val(_usuario.nome);
                $("#formResponsavel input[name='responsavel[unidade]']").val(_usuario.unidade.abrev);
            } else {
                $("#formResponsavel input[name='responsavel[nome]']").val('');
                $("#formResponsavel input[name='responsavel[unidade]']").val('');
            }
        });

    },

}

var solicitacaoControl = {

    solicitacao: {},

    getDados: function () {

        // arruma o array pra passar pro cadastro        
        var _dados = {
            bemcompat: [],
            bemsempat: []
        };

        _dados['beneficiado'] = $("#formBeneficiado input[name='beneficiado[busca]'").val();
        _dados['responsavel'] = $("#formResponsavel input[name='responsavel[busca]'").val();
        _dados['especificacao'] = '';
        _dados['dtDevolucao'] = $("#formSolicitacoes input[name='dtDevolucao'").val();
        _dados['centro'] = $("#formSolicitacoes select[name='usuario[centro]'").val();
        _dados['setor'] = $("#formSolicitacoes select[name='usuario[setor]'").val();
        _dados['categoria'] = $("#formSolicitacoes select[name='categoria'").val();
        _dados['observacao'] = $("#formEnvio textarea[name='observacao']").val()

        const tabelaC = document.getElementById('tabelaBemComPat');
        const linhasC = tabelaC.querySelectorAll('tbody tr');

        linhasC.forEach((linha) => {
            const celulas = linha.querySelectorAll('td');
            var itemComPat = {
                id: ''
            };
            itemComPat['id'] = celulas[0].textContent;
            _dados.bemcompat.push(itemComPat);

        });

        const tabela = document.getElementById('tabelaBemSemPat');
        const linhas = tabela.querySelectorAll('tbody tr');

        linhas.forEach((linha) => {
            const celulas = linha.querySelectorAll('td');
            var itemSemPat = {
                id: '',
                codigo: '',
                descricao: ''
            };
            itemSemPat['id'] = celulas[0].textContent;
            itemSemPat['codigo'] = celulas[1].textContent;
            itemSemPat['descricao'] = celulas[2].textContent;
            _dados.bemsempat.push(itemSemPat);

        });

        return _dados;

    },

    submit: function () {

        var _url = udev.url.getUrlController();
        _url += '/adicionar/';

        $.post(_url, this.getDados(), function (response) {
            if (response.result == 'success') {
                viewModalControl.configuraModal({ '#btnConfirmar': false });
                viewModalControl.show('Solicitação incluída', 'Sua solicitação foi incluída com sucesso.', true);
                $('#btnFechar').on('click', function (e) {
                    e.preventDefault();
                    // redireciona pra home do site
                    window.location = '/Sigcinf/Solicitacao/index';
                });
            } else {
                viewModalControl.show('Confira os erros informados', response.msg);
            }
        });

    },

    //    redrawUnidades: function () {
    //        // monta as opções de categorias
    //        $.get('/Sigcinf/Unidades/getAll/', function (response) {
    //            if (response.result == 'success') {
    //                var _listacentros = response.data;
    //                $.each( _listacentros, function (_i, _centro) {
    //                    let _opcao = "<option value='" + _centro['id'] + "'>" + _centro['abrev'] + "</option>";
    //                    $("select[name='usuario[centro]']").append(_opcao);
    //                });
    //            }
    //        });
    //    },

    redrawCategorias: function () {
        // monta as opções de categorias
        $.get('/Sigcinf/Categorias/getAll/', function (response) {
            if (response.result == 'success') {
                var _listacategorias = response.data;
                $.each(_listacategorias, function (_i, _categoria) {
                    let _opcao = "<option value='" + _categoria['id'] + "'>" + _categoria['nome'] + "</option>";
                    $("select[name='categoria']").append(_opcao);
                });
            }
        });
    },


};


var App = {

    redrawUnidades: function () {
        // Monta as opções de centros
        $.get('/Sigcinf/Unidades/getAll/', function (response) {
            if (response.result == 'success') {
                var _listacentros = response.data;
                var unidadeSelect = $("select[name='usuario[centro]']");
                unidadeSelect.empty(); // Limpa opções anteriores

                // Adiciona as unidades ao select
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

    redrawSetores: function (unidadeId) {
        $.get('/Sigcinf/Setores/getByUnidade/', { unidadeId: unidadeId }, function (response) {
            if (response.result == 'success') {
                var _listasetores = response.data;
                var setoresSelect = $("select[name='usuario[setor]']");
                setoresSelect.empty();
                $.each(_listasetores, function (_i, _setor) {
                    let _opcao = "<option value='" + _setor['id'] + "'>" + _setor['nome'] + "</option>";
                    setoresSelect.append(_opcao);
                });
            }
        });
    }

};

var bemComPatControl = {

    addItem: function addItem(_id) {
        $('#bemcompat-modal').modal('show');
    },

    buscarItem: function () {

        var _dados = {};
        _dados['busca'] = $("#buscaBemComPat input[name='bemcompat[busca]'").val();
        _dados['centro'] = $("#formSolicitacoes select[name='usuario[centro]'").val();
        _dados['setor'] = $("#formSolicitacoes select[name='usuario[setor]'").val();

        let _url = '/Sigcinf/BemComPat/getByBusca';

        if ($.fn.DataTable.isDataTable('#tabelaBemComPat2')) {
            recordTable.destroy();
        }

        console.log(_dados);

        recordTable = $("#tabelaBemComPat2").DataTable({
            dom: 'rtp',
            ajax: {
                "type": "POST",
                "processing": true,
                "format": "json",
                "url": _url,
                "data": _dados,
                "dataFilter": function (json) {
                    var json = $.parseJSON(json);
                    json.data = $.map(json.data, function (e) {
                        return e
                    });
                    return JSON.stringify(json);
                }
            },
            rowId: "id",
            columnDefs: [
                {
                    targets: [2],
                    className: "text-center"
                },
                {
                    targets: [0, 1],
                    className: "dt-middle"
                }
            ],
            columns: [
                {
                    render: function (row, type, data, meta) {
                        return data.patrimonio;
                    }
                },
                {
                    render: function (row, type, data, meta) {
                        return data.descricao;
                    }
                },
                {
                    sortable: false,
                    render: function (row, type, val, meta) {
                        return `<span 
                                name="btn-adicionar-itemGrid" 
                                class="btn btn-sm btn-success" 
                                title="Clique para selecionar este item"
                                onClick="bemComPatControl.addItemComPat(${val.id},'${val.patrimonio}','${val.descricao}')">
                                </span>`
                    }
                }
            ]
        });

    },

    addItemComPat: function (_idItem, _codItem, _nomeItem) {

        const tabela = document.getElementById('tabelaBemComPat');
        const linhas = tabela.querySelectorAll('tbody tr');

        const tbody = tabela.querySelector('tbody');
        const novaLinha = document.createElement('tr');

        const novaCelulaId = document.createElement('td');
        novaCelulaId.textContent = _idItem;
        novaLinha.appendChild(novaCelulaId);

        const novaCelulaCodigo = document.createElement('td');
        novaCelulaCodigo.textContent = _codItem;
        novaLinha.appendChild(novaCelulaCodigo);

        const novaCelulaNome = document.createElement('td');
        novaCelulaNome.textContent = _nomeItem;
        novaLinha.appendChild(novaCelulaNome);

        const botaoExcluir = document.createElement('button');
        botaoExcluir.textContent = 'Excluir';
        botaoExcluir.addEventListener('click', this.excluirLinhaComPat);
        novaLinha.appendChild(botaoExcluir);

        tbody.appendChild(novaLinha);

        $('#bemcompat-modal').modal('hide');

    },

    excluirLinhaComPat: function (_evento) {
        const botaoClicado = _evento.target;
        const linha = botaoClicado.closest('tr');
        linha.remove();
    },

}


var bemSemPatControl = {

    //    usuario : {},

    addItem: function addItem(_id) {

        $('#bemsempat-modal').modal('show');

    },

    buscarItem: function () {

        var _dados = {};
        _dados['busca'] = $("#buscaBemSemPat input[name='bemsempat[busca]'").val();
        _dados['centro'] = $("#formSolicitacoes select[name='usuario[centro]'").val();
        _dados['setor'] = $("#formSolicitacoes select[name='usuario[setor]'").val();

        let _url = '/Sigcinf/BemSemPat/getByBusca';

        if ($.fn.DataTable.isDataTable('#tabelaBemSemPat2')) {
            recordTable.destroy();
        }

        recordTable = $("#tabelaBemSemPat2").DataTable({
            dom: 'rtp',
            ajax: {
                "type": "POST",
                "processing": true,
                "format": "json",
                "url": _url,
                "data": _dados,
                "dataFilter": function (json) {
                    var json = $.parseJSON(json);
                    json.data = $.map(json.data, function (e) {
                        return e
                    });
                    return JSON.stringify(json);
                }
            },
            rowId: "id",
            columnDefs: [
                {
                    targets: [2],
                    className: "text-center"
                },
                {
                    targets: [0, 1],
                    className: "dt-middle"
                }
            ],
            columns: [
                {
                    render: function (row, type, data, meta) {
                        return data.codigo;
                    }
                },
                {
                    render: function (row, type, data, meta) {
                        return data.descricao;
                    }
                },
                {
                    sortable: false,
                    render: function (row, type, val, meta) {
                        return `<button 
                                    name="btn-adicionar-itemGrid" 
                                    class="btn btn-sm btn-success" 
                                    title="Clique para selecionar este item"
                                    onClick="bemSemPatControl.addItemSemPat(${val.id},'${val.codigo}','${val.descricao}')">
                                    <i class="fa fa-plus"></i>
                                </button>`
                    }
                }
            ]
        });

    },

    addItemSemPat: function (_idItem, _codItem, _nomeItem) {

        const tabela = document.getElementById('tabelaBemSemPat');
        const linhas = tabela.querySelectorAll('tbody tr');

        const tbody = tabela.querySelector('tbody');
        const novaLinha = document.createElement('tr');

        const novaCelulaId = document.createElement('td');
        novaCelulaId.textContent = _idItem;
        novaLinha.appendChild(novaCelulaId);

        const novaCelulaCodigo = document.createElement('td');
        novaCelulaCodigo.textContent = _codItem;
        novaLinha.appendChild(novaCelulaCodigo);

        const novaCelulaNome = document.createElement('td');
        novaCelulaNome.textContent = _nomeItem;
        novaLinha.appendChild(novaCelulaNome);

        const botaoExcluir = document.createElement('button');
        botaoExcluir.textContent = 'Excluir';
        botaoExcluir.addEventListener('click', this.excluirLinha);
        novaLinha.appendChild(botaoExcluir);

        tbody.appendChild(novaLinha);

        $('#bemsempat-modal').modal('hide');

    },

    excluirLinha: function (_evento) {
        const botaoClicado = _evento.target;
        const linha = botaoClicado.closest('tr');
        linha.remove();
    },

}


$(document).ready(function () {

    viewModalControl.hide();

    App.redrawUnidades();

    // Aguarda um momento para garantir que os selects foram preenchidos
    setTimeout(function () {
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

    solicitacaoControl.redrawCategorias();

    //    $("form[name='formSolicitacoes'] select[name='detalhamento[centro]']").val(1);
    //    $("form[name='formSolicitacoes'] select[name='detalhamento[centro]']").val(_rowData.unidade.id);
    //    $("form[name='ormSolicitacoes'] select[name='usuario[centro]']").val(1);
    //    $("#formSolicitacoes select[name='usuario[centro]']").val(2);
    //    $("select[name='usuario[centro]']").val(1);

    $(".datepicker").each(function (_indice) {
        $(this).datetimepicker({
            format: 'L'
        });
    });

    $("#dtDevolucao").mask("99/99/9999");

    ///

    $('#formBemComPat button[name="btn-aba-bemcompat-add"]').on('click', function (_evento) {
        $("#buscaBemComPat input[name='bemcompat[busca]'").val('');
        bemComPatControl.addItem();
    })

    $('#buscaBemComPat button[name="btn-buscar-bemcompat"]').on('click', function (_evento) {
        //        if ($("#buscaBemComPat input[name='bemcompat[busca]'").val() == '') {
        //            alert ('Digite o argumento');
        //        } else {
        bemComPatControl.buscarItem();
        //        }
    })

    ///

    $('#formBemSemPat button[name="btn-aba-bemsempat-add"]').on('click', function (_evento) {
        $("#buscaBemSemPat input[name='bemsempat[busca]'").val('');
        bemSemPatControl.addItem();
    })

    $('#buscaBemSemPat button[name="btn-buscar-bemsempat"]').on('click', function (_evento) {

        //        if ($("#buscaBemSemPat input[name='bemsempat[busca]'").val() == '') {
        //            alert ('Digite o argumento');
        //        } else {
        bemSemPatControl.buscarItem();
        //        }

    })

    ///   

    $("#formBeneficiado input[name='beneficiado[busca]']").parent().on("dp.change dp.show change", function (e) {
        $("#formBeneficiado").bootstrapValidator('revalidateField', 'beneficiado[busca]');
        $("#formBeneficiado input[name='beneficiado[nome]']").val('');
        $("#formBeneficiado input[name='beneficiado[unidade]']").val('');
    });

    $('#formBeneficiado button[name="btn-buscarBeneficiado"]').on('click', function (_evento) {

        _evento.preventDefault();

        var validator1 = $("#formBeneficiado").data("bootstrapValidator");
        validator1.validate();

        if (validator1.isValid()) {
            solicitanteControl.buscarUsuarioBeneficiado();
            $("#formBeneficiado").bootstrapValidator('revalidateField', 'beneficiado[busca]');
        };

    })

    $('#formBeneficiado button[name="btn-aba-beneficiado"]').on('click', function (_evento) {
        _evento.preventDefault();

        var validator1 = $("#formBeneficiado").data("bootstrapValidator");
        validator1.validate();

        if (validator1.isValid()) {
            if ($("#formBeneficiado input[name='beneficiado[nome]']").val() == '') {
                viewModalControl.show('Confira os erros informados', 'Necessário buscar os dados do beneficiado');
                $("#formBeneficiado").bootstrapValidator('revalidateField', 'beneficiado[busca]');
            } else {
                $('a[href=#tab-responsavel]').click();
            }
        }


    })

    $("#formBeneficiado").bootstrapValidator({

        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: [':disabled'], // Para abranger os campos em modal
        submitButtons: 'button[name="btn-aba-beneficiado"]',

        fields: {

            "beneficiado[busca]": {
                validators: {
                    notEmpty: {
                        message: "O beneficiado é obrigatório"
                    }
                }
            }

        }

    });

    // aba responsável
    $("#formResponsavel input[name='responsavel[busca]']").parent().on("dp.change dp.show change", function (e) {
        $("#formResponsavel").bootstrapValidator('revalidateField', 'responsavel[busca]');
        $("#formResponsavel input[name='responsavel[nome]']").val('');
        $("#formResponsavel input[name='responsavel[unidade]']").val('');
    });

    $('#formResponsavel button[name="btn-buscarResponsavel"]').on('click', function (_evento) {

        _evento.preventDefault();

        var validator1 = $("#formResponsavel").data("bootstrapValidator");
        validator1.validate();

        if (validator1.isValid()) {
            solicitanteControl.buscarUsuarioResponsavel();
            $("#formResponsavel").bootstrapValidator('revalidateField', 'responsavel[busca]');
        };

    })

    $('#formResponsavel button[name="btn-aba-responsavel"]').on('click', function (_evento) {
        _evento.preventDefault();

        var validator2 = $("#formResponsavel").data("bootstrapValidator");
        validator2.validate();

        if (validator2.isValid()) {
            if ($("#formResponsavel input[name='responsavel[nome]']").val() == '') {
                viewModalControl.show('Confira os erros informados', 'Necessário buscar os dados do responsável');
                $("#formResponsavel").bootstrapValidator('revalidateField', 'responsavel[busca]');
            } else {
                $('a[href=#tab-detalhamento]').click();
            }
        }

    })

    $("#formResponsavel").bootstrapValidator({

        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: [':disabled'], // Para abranger os campos em modal
        submitButtons: 'button[name="btn-aba-responsavel"]',

        fields: {

            "responsavel[busca]": {
                validators: {
                    notEmpty: {
                        message: "O responsavel é obrigatório"
                    }
                }
            }

        }

    });

    // aba Detalhamento
    $("#formSolicitacoes input[name='dtDevolucao']").parent().on("dp.change dp.show change", function (e) {
        $('#formSolicitacoes').bootstrapValidator('revalidateField', 'dtDevolucao');
    });

    $('#formSolicitacoes button[name="btn-aba-detalhamento"]').on('click', function (_evento) {
        _evento.preventDefault();
        var validator = $("#formSolicitacoes").data("bootstrapValidator");
        validator.validate();
        if (validator.isValid()) {
            $('a[href=#tab-bemcompat]').click();
        }
    });

    $("#formSolicitacoes").bootstrapValidator({

        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: [':disabled'], // Para abranger os campos em modal
        submitButtons: 'button[name="btn-aba-detalhamento"]',

        fields: {
            "dtDevolucao": {
                validators: {
                    date: {
                        format: "DD/MM/YYYY",
                        message: "Data inválida."
                    },
                    notEmpty: {
                        message: "Data de retorno é obrigatória"
                    },
                    callback: {
                        callback: function (_valor, _validator, _campo) {
                            if ($("input[name='dtDevolucao']").val().split('/').reverse().join('-') < new Date().toLocaleDateString('pt-BR', { timeZone: 'America/Sao_Paulo', }).split('/').reverse().join('-')) {
                                return ({ valid: false, message: "Data deve ser maior ou igual a data do dia" });
                            }
                            return ({
                                valid: true
                            })
                        }
                    }
                }
            },
        }

    });

    // aba com patrimonio
    $('#formBemComPat button[name="btn-aba-bemcompat"]').on('click', function (_evento) {
        _evento.preventDefault();
        $('a[href=#tab-bemsempat]').click();
    });

    // aba sem patrimonio
    $('#formBemSemPat button[name="btn-aba-bemsempat"]').on('click', function (_evento) {
        _evento.preventDefault();
        $('a[href=#tab-envio]').click();
    });

    // aba envio
    $('#formEnvio button[name="btn-aba-enviar"]').on('click', function (_evento) {
        _evento.preventDefault();
        solicitacaoControl.submit();
    });


});