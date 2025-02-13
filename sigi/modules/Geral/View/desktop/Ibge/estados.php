<style>
    h1 {
        font-size: 36px;
        color: inherit;
    }
    #simple-record-table .dt-middle {
        vertical-align: middle;
    }
</style>

<div class="container">

<!------------------ Listagem dos dados -------------------->
<div id="listContainer">
    <div class="page-header">
        <h1>Estados</h1>
    </div>
    <div class="panel-body">
    <table id="simple-record-table" class="table table-striped table-hover table-bordered tabela-servicos dt-responsive">
        <thead>
            <tr>
                <th class="col-sm-1">Id</th>
                <th class="col-sm-1">Sigla</th>
                <th class="col-sm-4">Nome</th>
                <th class="col-sm-4">Municípios</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    </div>
</div>

<!--------------------- JS ----------------------->

<script>

    $("#formContainer").removeClass("invisible");
    $("#formContainer").hide();

    function verMunicipios(estadoId) {
        window.location.href = udev.url.getUrlController() + "/municipios/" + estadoId;
    }

    var recordTable, 
        recordList = [];

    $(document).ready(function() {
        $.get(udev.url.getUrlController() + "/getAllEstados", function(response) {

            if (response.result == 'success') {
        
                recordTable = $("#simple-record-table").DataTable( {
                    ajax : {
                        "processing": true,
                        "format": "jsonp",
                        "url" : udev.url.getUrlController() + "/getAllEstados",
                        "dataFilter" : function(json) {
                            var json = $.parseJSON( json );
                            json.data = $.map(json.estados, function(e) { return e });
                            recordList = json.estados;
                            return JSON.stringify( json );
                        }
                    },
                    columnDefs: [
                        {
                            targets: [0, 1, 3],
                            className: 'dt-middle text-center'
                        },
                        {
                            targets: [2],
                            className: 'dt-middle'
                        }    
                    ],
                    columns: [
                        {"data": "id" },
                        {"data": "abrev" },
                        {"data": "nome" },
                        {
                            sortable: false,
                            render: function ( row, type, val, meta ) { 
                                return '<span class="btn btn-sm btn-success glyphicon glyphicon-th-list" aria-hidden="true" onclick="verMunicipios('+val.id+')"> Municípios</span> ';
                            }
                        }         
                    ],
                    aaSorting: [[2, "asc"]]
                });

            } else {
                $("#simple-record-table").append("<tr><td colspan=4 align=center> Erro: "+response.msg+"</td></tr>");
            }
        });

    });

</script>