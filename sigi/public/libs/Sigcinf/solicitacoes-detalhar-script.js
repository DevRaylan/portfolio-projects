var solicitacaoControl = {

    statusSolicitacao : '',

    anuncio : {},

    getVal : function(key) {
        return this.anuncio[key];
    },

    setVal : function(key, val) {
        this.anuncio[key] = val;
    },

    showSolicitacaoDetalhar: function (_solicitacao) {

        this.statusSolicitacao = _solicitacao.status;

        solicitacaoControl.setVal('id', _solicitacao.id);

        var dt = _solicitacao.dtHrDevolucao;
        dt= dt.substring(8,10) + '/' + dt.substring(5,7) + '/' + dt.substring(0,4);

        var seqAno = _solicitacao.sequencia;
        seqAno = seqAno + '/' + _solicitacao.ano

        $("input[name='solicitacao[seqAno]']").val(seqAno);
        $("input[name='solicitacao[status]']").val(_solicitacao.status);
        $("input[name='solicitacao[beneficiado]']").val(_solicitacao.beneficiado.nome);
        $("input[name='solicitacao[responsavel]']").val(_solicitacao.responsavel.nome);
        $("input[name='solicitacao[cadastrador]']").val(_solicitacao.cadastrador.nome);
        $("input[name='solicitacao[dtDevolucao]']").val(dt);       
        $("input[name='solicitacao[centro]']").val(_solicitacao.unidade.abrev);
        $("input[name='solicitacao[setor]']").val(_solicitacao.setor.nome);
        $("input[name='solicitacao[categoria]']").val(_solicitacao.categoria.nome);
        $("textarea[name='solicitacao[observacao]']").val(_solicitacao.observacao);      

    },

    listarBemSemPatrimonio: function(){

        // inicializa a lista

        var _url = udev.url.getUrlController();
        _url += '/getBemSemPat/';      
        _url += udev.url.getSegment(4);

        recordTableSolicitacaoItemSemPat = $("#tabelaSolicitacaoBemSemPat").DataTable({

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
                    className: "dt-middle"
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

    listarBemComPatrimonio: function(){

        // inicializa a lista

        var _url = udev.url.getUrlController();
        _url += '/getBemComPat/';      
        _url += udev.url.getSegment(4);

        recordTableSolicitacaoItemComPat = $("#tabelaSolicitacaoBemComPat").DataTable({

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
                    className: "dt-middle"
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

    obterDetalheSolicitacao : function(){
        
        $.get(udev.url.getUrlController() + '/getById/' + udev.url.getSegment(4), function(response){          
            let dadosSolicitacao = response.data;
            if(dadosSolicitacao == null){
                window.location = '/Geral/pageNotFound/' +udev.url.getSegment(4);  
            }
            else{
                solicitacaoControl.showSolicitacaoDetalhar(response.data);
            }
        })

    },

}

// geral
$(document).ready(function() {

    viewModalControl.hide();
    solicitacaoControl.obterDetalheSolicitacao();
    solicitacaoControl.listarBemSemPatrimonio();
    solicitacaoControl.listarBemComPatrimonio();

})