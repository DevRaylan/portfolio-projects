var bemsempatControl = {

    bemsempat : {},

    getDados : function(){
        
        // arruma o array pra passar pro cadastro        
        var _dados = {};
        _dados['id'] = $("form[name='formBemSemPat'] input[name='bemsempat[id]']").val();
        _dados['codigo'] = $("form[name='formBemSemPat'] input[name='bemsempat[codigo]']").val();
        _dados['descricao'] = $("form[name='formBemSemPat'] input[name='bemsempat[descricao]']").val();
        _dados['status'] = $("form[name='formBemSemPat'] select[name='bemsempat[status]']").val();
        _dados['centro'] = $("form[name='formBemSemPat'] select[name='bemsempat[centro]']").val();
        _dados['setor'] = $("form[name='formBemSemPat'] select[name='bemsempat[setor]']").val();

        return _dados;
    },

    submit : function(){
        
        var _url = udev.url.getUrlController();
        var _msg = '';
            
        if ($("form[name='formBemSemPat'] input[name='bemsempat[id]']").val() > 0) {
            _url += '/atualizar/';
            _msg = 'Bem sem patrimônio alterado com sucesso';
        } else {
            _url += '/adicionar/';
            _msg = 'Bem sem patrimônio inserido com sucesso';
        };
        
        $.post(_url, this.getDados(), function(response) {
            if (response.result == 'success') {
                recordTableBemSemPat.ajax.reload();
                $("#modalformBemSemPat").modal('hide');   
                viewModalControl.configuraModal({'#btnConfirmar':false});
                viewModalControl.show(_msg,'Sua solicitação foi concluída com sucesso',true);            
            } else {
                viewModalControl.show('Confira os erros informados', response.msg);
            }
        });

    },

    getDadosQtd : function(){
        
        // arruma o array pra passar pro cadastro        
        var _dados = {};
        _dados['id'] = $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[id]']").val();
        _dados['motivo'] = $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[motivo]']").val();
        _dados['operacao'] = $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[operacao]']").val();
        _dados['qtd'] = $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[qtd]']").val();
        _dados['observacao'] = $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[observacao]']").val();

        return _dados;
    },

    submitQtd : function(){
        
        var _url = udev.url.getUrlController();            
        _url += '/atualizarQtd/';
        
        $.post(_url, this.getDadosQtd(), function(response) {
            if (response.result == 'success') {
                recordTableBemSemPat.ajax.reload();
                $("#modalformBemSemPatQtd").modal('hide');   
                viewModalControl.configuraModal({'#btnConfirmar':false});
                viewModalControl.show('Bem sem patrimônio atualizado com sucesso','Sua solicitação foi concluída com sucesso',true);            
            } else {
                viewModalControl.show('Confira os erros informados', response.msg);
            }
        });

    },

}

var viewBemSemPatControl = {

    inicializar: function(){
        
        // inicializa a lista de bemsempats
        let _url = udev.url.getUrlController() + '/getAll';

        recordTableBemSemPat = $("#tabelaBemSemPat").DataTable({

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
                    targets: [2,6],
                    className: "text-center"
                },
                {
                    targets: [0,1],
                    className: "dt-middle"
                }
            ],
            columns: [
                {
                    render: function ( row, type, data, meta ) { 
                        return data.codigo;
                    }
                },
                {
                    render: function ( row, type, data, meta ) { 
                        return data.descricao;
                    }
                },
                {
                    render: function ( row, type, data, meta ) { 
                        return data.qtd;
                    }
                },
                {
                    render: function ( row, type, data, meta ) { 
                        if  (data.status == 'A') {
                            statusBem = 'Ativo' ;    
                        } else {
                            statusBem = 'Inativo' ;
                        };
                        return statusBem;
                    }
                },
                {
                    render: function (row, type, data, meta) {
                        return data.unidade.abrev;
                    }
                },
                {
                    render: function (row, type, data, meta) {
                        return data.setor.nome;
                    }
                },
                {
                    sortable: false,
                    render: function(row, type, val, meta) {
                        return `<span 
                                    name="btAlterar" 
                                    class="btn btn-sm btn-warning glyphicon glyphicon-pencil " 
                                    title="Clique para alterar."
                                    onClick="viewBemSemPatControl.editItem(${val.id})">
                                </span>
                                <span 
                                    name="btAdd" 
                                    class="btn btn-sm btn-warning glyphicon glyphicon-plus " 
                                    title="Clique para adicionar quantidade."
                                    onClick="viewBemSemPatControl.addQtdItem(${val.id})">
                                </span>
                                <span 
                                    name="btSub" 
                                    class="btn btn-sm btn-warning glyphicon glyphicon-minus " 
                                    title="Clique para subtrair quantidade."
                                    onClick="viewBemSemPatControl.subQtdItem(${val.id})">
                                </span>
                                <span 
                                    name="btHistorico" 
                                    class="btn btn-sm btn-warning glyphicon glyphicon-search " 
                                    title="Clique para visualiza histórico."
                                    onClick="viewBemSemPatControl.listarHistorico(${val.id})">
                                </span>`
                    }
                }
            ]
        });

    },

//    redrawUnidades: function () {
//        // monta as opções de centros
//        $.get('/Sigcinf/Unidades/getAll/', function (response) {
//            if (response.result == 'success') {
//                var _listacentros = response.data;
//                $.each( _listacentros, function (_i, _centro) {
//                    let _opcao = "<option value='" + _centro['id'] + "'>" + _centro['abrev'] + "</option>";
//                    $("select[name='bemsempat[centro]']").append(_opcao);
//                });
//            }
//        });
//    },

//    redrawSetores: function () {
//        $.get('/Sigcinf/Setores/getAll/', function (response) {
//            if (response.result == 'success') {
//                var _listasetores = response.data;
//                $.each( _listasetores, function (_i, _setor) {
//                    let _opcao = "<option value='" + _setor['id'] + "'>" + _setor['nome'] + "</option>";
//                    $("select[name='bemsempat[setor]']").append(_opcao);
//                });
//            }
//        });
//    },

    buscar: function buscar( _id ) { 

        let _rowId = "#" + _id;
        let _rowData = recordTableBemSemPat.row(_rowId).data();

        $("form[name='formBemSemPat'] input[name='bemsempat[id]']").val( _id );
        $("form[name='formBemSemPat'] input[name='bemsempat[codigo]']").val( _rowData.codigo );
        $("form[name='formBemSemPat'] input[name='bemsempat[descricao]']").val( _rowData.descricao );
        $("form[name='formBemSemPat'] select[name='bemsempat[status]']").val( _rowData.status );
        $("select[name='bemsempat[centro]']").val(_rowData.unidade.id).trigger("change");

        // Aguarda o carregamento dos setores antes de definir o valor
        App.redrawSetores(_rowData.unidade.id, function () {
            $("select[name='bemsempat[setor]']").val(_rowData.setor.id).trigger("change");
        });

    },

    editItem: function editItem(_id) {
        // Abre o modal para edição de um item existente
        $("form[name='formBemSemPat']").trigger("reset");
        viewBemSemPatControl.buscar( _id );
        $("form[name='formBemSemPat']").data("bootstrapValidator").resetForm();
        $("#modalformBemSemPat").modal('show');
    },

    addQtdItem: function addQtdItem(_id) {
        $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[id]']").val( _id );
        $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[operacao]']").val("Entrada");
        $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[qtd]']").val('');
        $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[motivo]']").val('');
        $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[observacao]']").val('');
        $("#modalformBemSemPatQtd").modal('show');
    },

    subQtdItem: function subQtdItem(_id) {
        $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[id]']").val( _id );
        $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[operacao]']").val("Saída");
        $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[qtd]']").val('');
        $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[motivo]']").val('');
        $("form[name='formBemSemPatQtd'] input[name='bemsempatqtd[observacao]']").val('');
        $("#modalformBemSemPatQtd").modal('show');
    },

    listarHistorico: function listarHistorico(_id) {
        viewBemSemPatHistControl.buscarHistorico(_id)
        $("#modalformBemSemPatHist").modal('show');
    },

}


var viewBemSemPatHistControl = {

    buscarHistorico: function(_id){
        
        var _dados = {};
        _dados['busca'] = _id;

        let _url = '/Sigcinf/BemSemPatHistorico/getByBemSemPat';

        if ($.fn.DataTable.isDataTable('#tabelaBemSemPatHist')) {
            recordTableHist.destroy();
        }

        recordTableHist = $("#tabelaBemSemPatHist").DataTable({

            ajax: {
                "type": "POST",
                "processing": true,
                "format": "json",
                "url": _url,
                "data": _dados,
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
                    targets: [0],
                    className: "text-center"
                },
                {
                    targets: [0,1],
                    className: "dt-middle"
                }
            ],
            columns: [
                {
                    render: function ( row, type, data, meta ) { 
                        return data.dtHrInclusao;
                    }
                },
                {
                    render: function ( row, type, data, meta ) { 
                        return data.operacao;
                    }
                },
                {
                    render: function ( row, type, data, meta ) { 
                        return data.qtd;
                    }
                },
                {
                    render: function ( row, type, data, meta ) { 
                        return data.motivo;
                    }
                },
                {
                    render: function ( row, type, data, meta ) { 
                        return data.observacao;
                    }
                }
            ]
        });
    },

}


var App = {
    redrawUnidades: function () {
        // Carrega as unidades (centros) e preenche o select de centro
        $.get('/Sigcinf/Unidades/getByCentroModerador/', function (response) {
            if (response.result == 'success') {
                var _listacentros = response.data;
                var unidadeSelect = $("select[name='bemsempat[centro]']");
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
                    var setoresSelect = $("select[name='bemsempat[setor]']");
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
            $("select[name='bemsempat[setor]']").empty();
        }
    }
};


$(document).ready(function() {
    viewModalControl.hide();
    
    App.redrawUnidades();
    viewBemSemPatControl.inicializar();

    // Aguarda um momento para garantir que os selects foram preenchidos
    setTimeout(function() {
        // Verifica se já não existe a opção "Selecione"
        if ($("select[name='bemsempat[centro]'] option[value='']").length === 0) {
            $("select[name='bemsempat[centro]']").prepend('<option value="">Selecione</option>');
        }
        if ($("select[name='bemsempat[setor]'] option[value='']").length === 0) {
            $("select[name='bemsempat[setor]']").prepend('<option value="">Selecione</option>');
        }

        // Seleciona a primeira opção dos selects
        $("select[name='bemsempat[centro]']").val("").trigger('change');
        $("select[name='bemsempat[setor]']").val("").trigger('change');
    }, 100);

    $("button[name='btNovo']").on( "click", function( _evento) {
        $("form[name='formBemSemPat']").trigger("reset");
        $("select[name='bemsempat[centro]']").val("").trigger("change"); // Reseta o select de centro
        $("select[name='bemsempat[setor]']").val("").trigger("change"); // Reseta o select de setor  
        $("form[name='formBemSemPat'] input[name='bemsempat[id]']").val("");      
        $("form[name='formBemSemPat']").data("bootstrapValidator").resetForm();
        $("#modalformBemSemPat").modal('show');
    });

    $("form[name='formBemSemPat'] button[name='btn-salvar']").on( 'click', function( _evento ) {
        _evento.preventDefault();
//        var validator = $("form[name='formBemSemPat']").data("bootstrapValidator");
//        validator.validate();
//        if (validator.isValid()) {

            if ($("form[name='formBemSemPat'] input[name='bemsempat[id]']").val() > 0) {
                bemsempatControl.submit();
            } else {
                var validator = $("form[name='formBemSemPat']").data("bootstrapValidator");
                validator.validate();
                if (validator.isValid()) {
                    bemsempatControl.submit();
                };
            };

//        }       
    });

    $("form[name='formBemSemPat'] button[name='btn-cancelar']").on( 'click', function( _evento ) {
        $("#modalformBemSemPat").modal('hide');
    });

    $("form[name='formBemSemPat']").on( 'submit', function( _evento ) {
        _evento.preventDefault();
        _evento.stopPropagation();
    });

    $("form[name='formBemSemPat']").bootstrapValidator({

        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: [':disabled'], // Para abranger os campos em modal
        submitButtons: 'button[name="btn-salvar"]',

        fields: {

            "bemsempat[codigo]": {
                validators: {
                    notEmpty: {
                        message: "O codigo do bem é obrigatório."
                    }
                }
            },

            "bemsempat[descricao]": {
                validators: {
                    notEmpty: {
                        message: "A descrição do bem é obrigatório."
                    },
                    callback: {
                        callback: function( _valor, _validator, _campo ) {

                            // Tirar espaços excedentes.
                            _valor = _valor.trim().replace(/\s{2,}/g, ' ');

                            // Tirar caracteres inválidos.
                            _valor = _valor.replace(/\"/g, '');

                            if( _valor.length < 4 ) {

                                return( {
                                    valid: false,
                                    message: 'descrição tem que ter mais de 3 caracteres'
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

// form qtd

    $("form[name='formBemSemPatQtd'] button[name='btn-cancelar']").on( 'click', function( _evento ) {
        $("#modalformBemSemPatQtd").modal('hide');
    });

    $("form[name='formBemSemPatQtd'] button[name='btn-salvar']").on( 'click', function( _evento ) {
        _evento.preventDefault();
        bemsempatControl.submitQtd();
    });

// form hist

    $("form[name='formBemSemPatHist'] button[name='btn-fechar-hist']").on( 'click', function( _evento ) {
        $("#modalformBemSemPatHist").modal('hide');
    });
    



});