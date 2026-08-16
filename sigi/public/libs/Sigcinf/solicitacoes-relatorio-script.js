var viewSolicitacaoControl = {


    buscarPorCpf: function(){
    
        var _dados = {};
        _dados['busca'] = $("input[name='buscar']").val();

        let _url = udev.url.getUrlController() + '/getByBuscaGeralCpf';

        if ($.fn.DataTable.isDataTable('#tabelaSolicitacoes')) {
            recordTable.destroy();
        }

        recordTable = $("#tabelaSolicitacoes").DataTable({

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
            autoWidth: false,
            columnDefs: [
                { targets: [0], width: "80px" }, 
                { targets: [1], width: "100px" }, 
                { targets: [2], width: "250px" }, 
                { targets: [3], width: "280px" }, 
                { targets: [4], width: "110px" }, 
                { targets: [5], width: "70px", className: "text-center" } 
            ],
            columns: [
                {
                    render: function (row, type, val, meta) {
                        
                        return (val.sequencia + '/' + val.ano);

                    }
                },
                {
                    render: function (row, type, val, meta) {
                        
                        return (val.status);

                    }
                },
                {
                    render: function (row, type, val, meta) {
                        
                        return `<td>  
                                ${val.beneficiado.cpf}
                                <br>
                                ${val.beneficiado.nome}
                                </td>`;

                    }
                },
                {
                    render: function (row, type, val, meta) {
                        
                        return `<td>  
                                ${val.responsavel.cpf}
                                <br>
                                ${val.responsavel.nome}
                                </td>`;

                    }
                },
                {
                    render: function ( row, type, val, meta ) { 
                        var dt = val.dtHrInclusao;
                        return `<td>  
                                ${dt.substring(8,10) + '/' + dt.substring(5,7) + '/' + dt.substring(0,4)}
                                <br>
                                ${dt.substring(11,13) + ':' + dt.substring(14,16)}
                                </td>`;
                    }
                },
                {
                    sortable: false,
                    render: function(row, type, val, meta) {
                        return `<span 
                                    name="btAlterar" 
                                    class="btn btn-sm btn-warning glyphicon glyphicon-zoom-in " 
                                    title="Clique para alterar."
                                    onClick="viewSolicitacaoControl.editItem(${val.id})">
                                </span>`;
                    }
                }
            ]
        });

    },

    buscarComPatrimonio: function(){
    
        var _dados = {};
        _dados['busca'] = $("input[name='buscar']").val();

        let _url = udev.url.getUrlController() + '/getByBuscaGeralComPatrimonio';

        if ($.fn.DataTable.isDataTable('#tabelaSolicitacoes')) {
            recordTable.destroy();
        }

        recordTable = $("#tabelaSolicitacoes").DataTable({

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
            autoWidth: false,
            columnDefs: [
                { targets: [0], width: "80px" }, 
                { targets: [1], width: "100px" }, 
                { targets: [2], width: "250px" }, 
                { targets: [3], width: "280px" }, 
                { targets: [4], width: "110px" }, 
                { targets: [5], width: "70px", className: "text-center" } 
            ],
            columns: [
                {
                    render: function (row, type, val, meta) {
                        
                        return (val.solicitacao.sequencia + '/' + val.solicitacao.ano);

                    }
                },
                {
                    render: function (row, type, val, meta) {
                        
                        return (val.solicitacao.status);

                    }
                },
                {
                    render: function (row, type, val, meta) {
                        
                        return `<td>  
                                ${val.solicitacao.beneficiado.cpf}
                                <br>
                                ${val.solicitacao.beneficiado.nome}
                                </td>`;

                    }
                },
                {
                    render: function (row, type, val, meta) {
                        
                        return `<td>  
                                ${val.solicitacao.responsavel.cpf}
                                <br>
                                ${val.solicitacao.responsavel.nome}
                                </td>`;

                    }
                },
                {
                    render: function ( row, type, val, meta ) { 
                        var dt = val.solicitacao.dtHrInclusao;
                        return `<td>  
                                ${dt.substring(8,10) + '/' + dt.substring(5,7) + '/' + dt.substring(0,4)}
                                <br>
                                ${dt.substring(11,13) + ':' + dt.substring(14,16)}
                                </td>`;
                    }
                },
                {
                    sortable: false,
                    render: function(row, type, val, meta) {
                        return `<span 
                                    name="btAlterar" 
                                    class="btn btn-sm btn-warning glyphicon glyphicon-zoom-in " 
                                    title="Clique para alterar."
                                    onClick="viewSolicitacaoControl.editItem(${val.solicitacao.id})">
                                </span>`;
                    }
                }
            ]
        });

    },

    buscarSemPatrimonio: function(){
    
        var _dados = {};
        _dados['busca'] = $("input[name='buscar']").val();

        let _url = udev.url.getUrlController() + '/getByBuscaGeralSemPatrimonio';

        if ($.fn.DataTable.isDataTable('#tabelaSolicitacoes')) {
            recordTable.destroy();
        }

        recordTable = $("#tabelaSolicitacoes").DataTable({

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
            autoWidth: false,
            columnDefs: [
                { targets: [0], width: "80px" }, 
                { targets: [1], width: "100px" }, 
                { targets: [2], width: "250px" }, 
                { targets: [3], width: "280px" }, 
                { targets: [4], width: "110px" }, 
                { targets: [5], width: "70px", className: "text-center" } 
            ],
            columns: [
                {
                    render: function (row, type, val, meta) {
                        
                        return (val.solicitacao.sequencia + '/' + val.solicitacao.ano);

                    }
                },
                {
                    render: function (row, type, val, meta) {
                        
                        return (val.solicitacao.status);

                    }
                },
                {
                    render: function (row, type, val, meta) {
                        
                        return `<td>  
                                ${val.solicitacao.beneficiado.cpf}
                                <br>
                                ${val.solicitacao.beneficiado.nome}
                                </td>`;

                    }
                },
                {
                    render: function (row, type, val, meta) {
                        
                        return `<td>  
                                ${val.solicitacao.responsavel.cpf}
                                <br>
                                ${val.solicitacao.responsavel.nome}
                                </td>`;

                    }
                },
                {
                    render: function ( row, type, val, meta ) { 
                        var dt = val.solicitacao.dtHrInclusao;
                        return `<td>  
                                ${dt.substring(8,10) + '/' + dt.substring(5,7) + '/' + dt.substring(0,4)}
                                <br>
                                ${dt.substring(11,13) + ':' + dt.substring(14,16)}
                                </td>`;
                    }
                },
                {
                    sortable: false,
                    render: function(row, type, val, meta) {
                        return `<span 
                                    name="btAlterar" 
                                    class="btn btn-sm btn-warning glyphicon glyphicon-zoom-in " 
                                    title="Clique para alterar."
                                    onClick="viewSolicitacaoControl.editItem(${val.solicitacao.id})">
                                </span>`;
                    }
                }
            ]
        });

    },

    editItem: function editItem(_id) {

        var _url = udev.url.getUrlController()
        _url += '/detail/' + _id;
        window.location.href = _url;

    },

}

$(document).ready(function() {

//    viewSolicitacaoControl.inicializarGrid();

    $("button[name='btn-buscar']").on( "click", function( _evento) {
        _evento.preventDefault();
        viewSolicitacaoControl.buscarPorCpf();

    })

    $("button[name='btn-buscar2']").on( "click", function( _evento) {
        _evento.preventDefault();
        viewSolicitacaoControl.buscarComPatrimonio();
    })

    $("button[name='btn-buscar3']").on( "click", function( _evento) {
        _evento.preventDefault();
        viewSolicitacaoControl.buscarSemPatrimonio();
    })

});