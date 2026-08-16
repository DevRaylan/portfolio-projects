<?php $tipoRegistro = 'Auditoria'; ?>

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
    <br /><p id="msg">&nbsp;</p>

    <div class="page-header">
        <h1>
            <?= $tipoRegistro ?>
        </h1>
    </div>
    <div class="panel-body">
    <table id="simple-record-table" class="table table-striped table-hover table-bordered tabela-servicos dt-responsive nowrap">
        <thead>
            <tr>
                <th class="col-sm-2">Data</th>
                <th class="col-sm-2">Link</th>
                <th class="col-sm-3">Mensagem</th>
                <th class="col-sm-1">Status</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    </div>
</div>

<!--------------------- JS ----------------------->

<script>

    var recordTable, 
        recordList = [];

    $(document).ready(function() {
        recordTable = $("#simple-record-table").DataTable( {
            ajax : {
                "processing": true,
                "format": "jsonp",
                "url" : "<?php echo '/'.$this->url->getNameModule().'/'.$this->url->getNamecontroller() ?>/findAuditByParams",
                "dataFilter" : function(json) {
                    var json = $.parseJSON( json );
                    json.data = $.map(json.logs, function(e) { return e });
                    recordList = json.logs;
                    return JSON.stringify( json );
                }
            },
            columnDefs: [
                {
                    targets: [0, 3],
                    className: 'dt-middle text-center'
                },
                {
                    targets: [1, 2],
                    className: 'dt-middle'
                }
            ],
            columns: [
                {"data": "dtCriacao.date" },
                {"data": "url" },
                {"data": "msg" },
                {"data": "type" }               
            ]
        });
    });

</script>