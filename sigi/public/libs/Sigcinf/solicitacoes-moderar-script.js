var viewSolicitacaoControl = {

    inicializarDigitadas: function(){
        
        // inicializa a lista de solicitacaos
        let _url = udev.url.getUrlController() + '/getDigitadasModerar/';

        recordTableDigitadas = $("#tabelaSolicitacoesDigitadas").DataTable({

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
                    targets: [4],
                    className: "dt-middle"
                }
            ],
            columns: [
                {
                    render: function (row, type, val, meta) {
                        
                        return (val.sequencia + '/' + val.ano);

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

                    render: function ( row, type, val, meta ) { 
                        var dt = val.dtHrDevolucao;
                        return `<td>  
                                ${dt.substring(8,10) + '/' + dt.substring(5,7) + '/' + dt.substring(0,4)}
                                <br>
                                ${dt.substring(11,13) + ':' + dt.substring(14,16)}
                                </td>`;
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
                    "data": "status",
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

    inicializarAtendidas: function(){
        
        // inicializa a lista de solicitacaos
        let _url = udev.url.getUrlController() + '/getAtendidasModerar/';

        recordTableAtendidas = $("#tabelaSolicitacoesAtendidas").DataTable({

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
                    targets: [4],
                    className: "dt-middle"
                }
            ],
            columns: [

                {
                    render: function (row, type, val, meta) {
                        
                        return (val.sequencia + '/' + val.ano);

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

                    render: function ( row, type, val, meta ) { 
                        var dt = val.dtHrDevolucao;
                        return `<td>  
                                ${dt.substring(8,10) + '/' + dt.substring(5,7) + '/' + dt.substring(0,4)}
                                <br>
                                ${dt.substring(11,13) + ':' + dt.substring(14,16)}
                                </td>`;
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
                    "data": "status",
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

    inicializarDevolvidas: function(){
        
        // inicializa a lista de solicitacaos
        let _url = udev.url.getUrlController() + '/getDevolvidasModerar/';

        recordTableCanceladas = $("#tabelaSolicitacoesDevolvidas").DataTable({

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
                    targets: [4],
                    className: "dt-middle"
                }
            ],
            columns: [

                {
                    render: function (row, type, val, meta) {
                        
                        return (val.sequencia + '/' + val.ano);

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

                    render: function ( row, type, val, meta ) { 
                        var dt = val.dtHrDevolucao;
                        return `<td>  
                                ${dt.substring(8,10) + '/' + dt.substring(5,7) + '/' + dt.substring(0,4)}
                                <br>
                                ${dt.substring(11,13) + ':' + dt.substring(14,16)}
                                </td>`;
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
                    "data": "status",
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

    inicializarAtrasadas: function(){
        
        // inicializa a lista de solicitacaos
        let _url = udev.url.getUrlController() + '/getAtrasadasModerar/';

        recordTableCanceladas = $("#tabelaSolicitacoesAtrasadas").DataTable({

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
                    targets: [4],
                    className: "dt-middle"
                }
            ],
            columns: [

                {
                    render: function (row, type, val, meta) {
                        
                        return (val.sequencia + '/' + val.ano);

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

                    render: function ( row, type, val, meta ) { 
                        var dt = val.dtHrDevolucao;
                        return `<td>  
                                ${dt.substring(8,10) + '/' + dt.substring(5,7) + '/' + dt.substring(0,4)}
                                <br>
                                ${dt.substring(11,13) + ':' + dt.substring(14,16)}
                                </td>`;
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
                    "data": "status",
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

    inicializarCanceladas: function(){
        
        // inicializa a lista de solicitacaos
        let _url = udev.url.getUrlController() + '/getCanceladasModerar/';

        recordTableCanceladas = $("#tabelaSolicitacoesCanceladas").DataTable({

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
                    targets: [4],
                    className: "dt-middle"
                }
            ],
            columns: [

                {
                    render: function (row, type, val, meta) {
                        
                        return (val.sequencia + '/' + val.ano);

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

                    render: function ( row, type, val, meta ) { 
                        var dt = val.dtHrDevolucao;
                        return `<td>  
                                ${dt.substring(8,10) + '/' + dt.substring(5,7) + '/' + dt.substring(0,4)}
                                <br>
                                ${dt.substring(11,13) + ':' + dt.substring(14,16)}
                                </td>`;
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
                    "data": "status",
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

    inicializarPendentes: function(){
        
        // inicializa a lista de solicitacaos
        let _url = udev.url.getUrlController() + '/getPendentesModerar/';

        recordTableCanceladas = $("#tabelaSolicitacoesPendentes").DataTable({

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
                    targets: [4],
                    className: "dt-middle"
                }
            ],
            columns: [

                {
                    render: function (row, type, val, meta) {
                        
                        return (val.sequencia + '/' + val.ano);

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

                    render: function ( row, type, val, meta ) { 
                        var dt = val.dtHrDevolucao;
                        return `<td>  
                                ${dt.substring(8,10) + '/' + dt.substring(5,7) + '/' + dt.substring(0,4)}
                                <br>
                                ${dt.substring(11,13) + ':' + dt.substring(14,16)}
                                </td>`;
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
                    "data": "status",
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

    editItem: function editItem(_id) {

        var _url = udev.url.getUrlController()
        _url += '/alterar/' + _id;
        window.location.href = _url;

    },

}


$(document).ready(function() {

    viewSolicitacaoControl.inicializarDigitadas();
    viewSolicitacaoControl.inicializarAtendidas();
    viewSolicitacaoControl.inicializarDevolvidas();
    viewSolicitacaoControl.inicializarAtrasadas();
    viewSolicitacaoControl.inicializarPendentes();
    viewSolicitacaoControl.inicializarCanceladas();

    $("button[name='btn-adicionar-solicitacao']").on( "click", function( _evento) {
        var _url = udev.url.getUrlController()
        _url += '/incluir/';
        window.location.href = _url;
    });

});