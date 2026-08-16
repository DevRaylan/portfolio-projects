var viewSolicitacaoControl = {

    inicializarGrid: function(){
        
        // inicializa a lista de solicitacaos
        let _url = udev.url.getUrlController() + '/getCpfResponsavelAndBeneficiado/';

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

    editItem: function editItem(_id) {

        var _url = udev.url.getUrlController()
        _url += '/detail/' + _id;
        window.location.href = _url;

    },

}

$(document).ready(function() {

    viewSolicitacaoControl.inicializarGrid();

});