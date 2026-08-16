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
        <a href="<?= $this->url->getUrlController() ?>/estados"><< Voltar para Estados</a><br/><br/>
        <h1>Municípios</h1>
    </div>
    <div class="panel-body">
    <table id="simple-record-table" class="table table-striped table-hover table-bordered tabela-servicos dt-responsive">
        <thead>
            <tr>
                <th class="col-sm-1">Id</th>
                <th class="col-sm-4">Nome</th>
                <th class="col-sm-1">Microregião</th>
                <th class="col-sm-1">Mesoregião</th>
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

    var recordTable, 
        recordList = [];

    $(document).ready(function() {
        recordTable = $("#simple-record-table").DataTable( {
            ajax : {
                "processing": true,
                "format": "jsonp",
                "url" : udev.url.getUrlController() + "/getMunicipiosPorEstado/" + <?= $estadoId ?>,
                "dataFilter" : function(json) {
                    var json = $.parseJSON( json );
                    json.data = $.map(json.municipios, function(e) { return e });
                    recordList = json.municipios;
                    return JSON.stringify( json );
                }
            },
            columnDefs: [
                {
                    targets: [0],
                    className: 'dt-middle text-center'
                },
                {
                    targets: [1, 2, 3],
                    className: 'dt-middle'
                }    
            ],
            columns: [
                {"data": "id" },
                {"data": "nome" },   
                {"data": "microregiao" },
                {"data": "mesoregiao" }
            ],
            aaSorting: [[1, "asc"]]
        });
    });

</script>