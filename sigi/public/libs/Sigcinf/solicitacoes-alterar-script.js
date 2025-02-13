var solicitacaoControl = {

    statusSolicitacao : '',

    emprestimo : {},

    getVal : function(key) {
        return this.emprestimo[key];
    },

    setVal : function(key, val) {
        this.emprestimo[key] = val;
    },

    getDados : function(){
        
        var _dados = {};
        _dados['id'] = this.getVal('id');
        _dados['dtDevolucao'] = $("form[name='modalformSolicitacaoAlterar'] input[name='dtDevolucao']").val();
//        _dados['dtDevolucao'] = $("form[name='modalformSolicitacaoAlterar'] input[name='dtDevolucao']").val();
        return _dados;
    },

    validaStatusModerar : function(){

        if ($("form[name='modalformSolicitacaoModerar'] select[name='status']").val() == this.getVal('status')) {
            alert('O status não foi alterado');
            return false;
        };

        if ($("form[name='modalformSolicitacaoModerar'] select[name='status']").val() === 'CANCELADA') {
            if ($("form[name='modalformSolicitacaoModerar'] textarea[name='observacao']").val() === '') {
                alert('Obrigatório informar motivo');
                return false;
            }
        };   

        return true;

    },

    getDadosModerar : function(){
        
        var _dados = {};
        _dados['id'] = this.getVal('id');
        _dados['status'] = $("form[name='modalformSolicitacaoModerar'] select[name='status']").val();
        _dados['observacao'] = $("form[name='modalformSolicitacaoModerar'] textarea[name='observacao']").val();
        
        return _dados;
    },

    showSolicitacao: function (_solicitacao) {

        this.statusSolicitacao = _solicitacao.status;

        var dt = _solicitacao.dtHrDevolucao;
        dt= dt.substring(8,10) + '/' + dt.substring(5,7) + '/' + dt.substring(0,4);     
        
        var seqAno = _solicitacao.sequencia;
        seqAno = seqAno + '/' + _solicitacao.ano

        solicitacaoControl.setVal('id', _solicitacao.id);
        solicitacaoControl.setVal('status', _solicitacao.status);

//        solicitacaoControl.setVal('beneficiado', _solicitacao.beneficiado.nome);
//        solicitacaoControl.setVal('centro', _solicitacao.beneficiado.unidade.abrev);
//        solicitacaoControl.setVal('seqAno', seqAno);
        solicitacaoControl.setVal('dtDevolucao', dt);
        solicitacaoControl.setVal('observacao', _solicitacao.observacao);

        $("input[name='solicitacao[seqAno]']").val(seqAno);
        $("input[name='solicitacao[status]']").val(_solicitacao.status);
        $("input[name='solicitacao[cadastrador]']").val(_solicitacao.cadastrador.nome);
        $("input[name='beneficiado[nome]']").val(_solicitacao.beneficiado.nome);
        $("input[name='beneficiado[centro]']").val(_solicitacao.beneficiado.unidade.abrev);
        $("input[name='responsavel[nome]']").val(_solicitacao.responsavel.nome);
        $("input[name='responsavel[centro]']").val(_solicitacao.responsavel.unidade.abrev);
        $("input[name='solicitacao[dtDevolucao]']").val(dt);
        $("input[name='solicitacao[centro]']").val(_solicitacao.unidade.abrev);
        $("input[name='solicitacao[setor]']").val(_solicitacao.setor.nome);
        $("input[name='solicitacao[categoria]']").val(_solicitacao.categoria.nome);
        $("textarea[name='detalhamento[observacao]']").val(_solicitacao.observacao);

//        if (this.statusSolicitacao == 'DIGITADA') {
//            $("button[name='btn-editar-solicitacao']").removeClass('hidden');
//            $("button[name='btn-editar-solicitacaoModerar']").removeClass('hidden');      
//        } else {
//            $("button[name='btn-editar-solicitacao']").addClass('hidden');
//            $("button[name='btn-adicionar-bemcompat']").addClass('hidden');
//            $("button[name='btn-adicionar-bemsempat']").addClass('hidden');
//            $("button[name='btn-editar-solicitacaoModerar']").addClass('hidden');
//        }; 

    },

    prorrogarData: function prorrogarData() {

        $("form[name='modalformSolicitacaoAlterar'] imput[name='dtDevolucao']").val(this.getVal('dtDevolucao'));
        $('#formSolicitacaoAlterar').modal('show');
                     
    },

    moderarItem: function moderarItem() {

        $("form[name='modalformSolicitacaoModerar'] textarea[name='observacao']").val('');
        $("form[name='modalformSolicitacaoModerar'] select[name='status']").val(this.getVal('status'));
        $('#formSolicitacaoModerar').modal('show');
    },


    submitAtualizar : function(){
        
        var _url = udev.url.getUrlController();
        _url += '/atualizar/';  
 
        $.post(_url, this.getDados(), function(response) {
            if (response.result == 'success') {
                $('#formSolicitacaoAlterar').modal('hide');   
                solicitacaoControl.obterDetalheSolicitacao();
                viewModalControl.configuraModal({'#btnConfirmar':false});
                viewModalControl.show('Solicitação alterada','Sua solicitação foi alterada com sucesso.',true);            
            } else {
                viewModalControl.show('Confira os erros informados',response.msg);
            }
        });

    },

    submitModerar : function(){
        
        var _url = udev.url.getUrlController();
        _url += '/atualizarStatus/';  
 
        $.post(_url, this.getDadosModerar(), function(response) {
            if (response.result == 'success') {
                $('#formSolicitacaoModerar').modal('hide');   
                solicitacaoControl.obterDetalheSolicitacao();
                viewModalControl.configuraModal({'#btnConfirmar':false});
                viewModalControl.show('Solicitação alterada','Sua solicitação foi alterada com sucesso.',true);          
                $('#btnFechar').on('click', function(e) {
                    e.preventDefault();
                    // redireciona pra home do site
                    window.location = '/Sigcinf/Solicitacao/moderar'; 
                });                                 
            } else {
                viewModalControl.show('Confira os erros informados',response.msg);
            }
        });

    },

    obterSolicitacaoBemSemPat: function(){

        // inicializa a lista 

        var _url = udev.url.getUrlController();
        _url += '/getBemSemPat/';      
        _url += udev.url.getSegment(4);

        recordTableSolicitacaoBem = $("#tabelaSolicitacaoBemSemPat").DataTable({

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
                    targets: [0],
                    className: "text-center"
                }
            ],
            columns: [
                {
                    render: function ( row, type, data, meta ) { 
                        return data.bemsempat.codigo;
                    }
                },
                {
                    render: function ( row, type, data, meta ) { 
                        return data.bemsempat.descricao;
                    }
                }
            ]
        });

    },

    obterDetalheSolicitacao : function(){
        
        $.get(udev.url.getUrlController() + '/getById/' + udev.url.getSegment(4), function(response){          
            let dadosSolicitacao = response.data;
            if(dadosSolicitacao == null){
                window.location = '/Geral/pageNotFound/' +udev.url.getSegment(4);  
            }
            else{
                solicitacaoControl.showSolicitacao(response.data);
            }
        })

    },

    imprimirTermo : function(){

        if ($("input[name='solicitacao[status]']").val() == 'ATENDIDA') {
            let id = this.getVal('id');
            location.href = udev.url.getUrlController() + '/gerarRelatorio?id=' + id
        } else {
            alert('Não é possível imprimir o termo pois a solicitação não foi atendida.')
        };
       
    },


    obterSolicitacaoBemSemPat: function(){

        // inicializa a lista

        var _url = udev.url.getUrlController();
        _url += '/getBemSemPat/';      
        _url += udev.url.getSegment(4);

        recordTableSolicitacaoBem = $("#tabelaSolicitacaoBemSemPat").DataTable({

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
                    targets: [0],
                    className: "text-center"
                }
            ],
            columns: [
                {
                    render: function ( row, type, data, meta ) { 
                        return data.bemsempat.codigo;
                    }
                },
                {
                    render: function ( row, type, data, meta ) { 
                        return data.bemsempat.descricao;
                    }
                }
            ]
        });

    },

    obterSolicitacaoBemComPat: function(){

        // inicializa a lista

        var _url = udev.url.getUrlController();
        _url += '/getBemComPat/';      
        _url += udev.url.getSegment(4);

        recordTableSolicitacaoBem = $("#tabelaSolicitacaoBemComPat").DataTable({

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
                    targets: [0],
                    className: "text-center"
                }
            ],
            columns: [
                {
                    render: function ( row, type, data, meta ) { 
                        return data.bemcompat.patrimonio;
                    }
                },
                {
                    render: function ( row, type, data, meta ) { 
                        return data.bemcompat.descricao;
                    }
                }
            ]
        });

    },


}


// geral

$(document).ready(function() {

    viewModalControl.hide();
    
    $( ".datepicker" ).each( function( _indice ) {
        $(this).datetimepicker( {
            format: 'L'
        } );
    } );

    $("#dtDevolucao").mask("99/99/9999");  

    solicitacaoControl.obterDetalheSolicitacao();
    solicitacaoControl.obterSolicitacaoBemComPat();
    solicitacaoControl.obterSolicitacaoBemSemPat();
      
    // aba solicitacao  
    
    $("button[name='btn-editar-solicitacao']").on( "click", function( _evento) {
        $("#formSolicitacaoAlterar").trigger("reset");
        $("#formSolicitacaoAlterar").data("bootstrapValidator").resetForm();
        solicitacaoControl.prorrogarData();
    })    

    // aba solicitacao -> editar

    $('#btn-salvar-solicitacao').click(function(_evento) {
        _evento.preventDefault();
        var validator = $("#formSolicitacaoAlterar").data("bootstrapValidator");
        validator.validate();
        if(validator.isValid()) { 
            solicitacaoControl.submitAtualizar();
        }
    });

    $("#formSolicitacaoAlterar").bootstrapValidator({

        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: [':disabled'], // Para abranger os campos em modal
        submitButtons: 'button[name="btn-salvar-solicitacao"]',

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
                        callback: function( _valor, _validator, _campo ) {                           
                            if( $("input[name='dtDevolucao']").val().split('/').reverse().join('-') < new Date().toLocaleDateString('pt-BR', {timeZone: 'America/Sao_Paulo',}).split('/').reverse().join('-')) {
                                return ({ valid: false, message: "Data deve ser maior ou igual a data do dia"});
                            }                            
                            return( {
                                valid: true
                            } )
                       }
                    }
                }
            },
        }

    });

    $('#btn-salvar-solicitacaoModerar').click(function() {
        if (solicitacaoControl.validaStatusModerar()) {
            solicitacaoControl.submitModerar();
        };
    })

    $("button[name='btn-editar-solicitacaoModerar']").on( "click", function( _evento) {
        solicitacaoControl.moderarItem();
    })
    
    $("button[name='btn-imprimir']").on( "click", function( _evento) {
        solicitacaoControl.imprimirTermo();
    })
    

})