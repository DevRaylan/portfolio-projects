<style>
    h1 {
        font-size: 36px;
        color: inherit;
    }
    .tab-content {
        margin-top: 10px;
    }
    .table-nowrap {
        table-layout:fixed;	
    }

    .table-nowrap td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .border-form-group {
        border: 1px solid #ccc;
    }

    .border-form-group:focus {
        border-color: #66afe9;
        outline: 0;
        -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
    }
</style>

<link rel="stylesheet" type="text/css" href="/libs/Geral/datepicker/jquery-ui.css">
<script src="/libs/Geral/datepicker/jquery-ui.js"></script>

<div class="container">   
    <div class="page-header"> 
        <h1>Logs do sistema</h1>
    </div>
    <ul class="nav nav-tabs">
        <li role="presentation" class="active"><a data-toggle="tab" href="#others"><span class="glyphicon glyphicon-ok-sign text-muted"></span> Auditoria</a></li>
        <li role="presentation"><a data-toggle="tab" href="#infos"><span class="glyphicon glyphicon-info-sign text-muted"></span> Info</a></li>
        <li role="presentation"><a data-toggle="tab" href="#errors"><span class="glyphicon glyphicon-remove-sign text-danger"></span> Erros</a></li>
    </ul>
    
    <div class="tab-content">
        <div id="others" class="tab-pane fade in active">

            <table id="tabela-others" class="table table-striped table-hover table-bordered dt-responsive">
            <!-- limite de dados pro backend -->
            <div class="form-group row">
                <div class="col-sm-8">
                <label style="font-weight: normal">
                    Consultar últimos 
                    <select id="registros" class="form-inline input-sm border-form-group" onChange="atualizaDados()"><option value="500">500</option><option value="1000">1.000</option><option value="5000">5.000</option><option value="10000">10.000</option></select>
                     registros
                </label>
                </div>

                <div class="col-sm-4" style="float: right;">
                <label style="font-weight: normal">
                    Consultar por data   
                    <input type="text" id="datepicker" class="form-inline input-sm border-form-group" onkeypress="handle(event)">
                </label>
                </div>
            </div>
            <!-- -->
                <thead>
                    <tr>
                        <th class="col-xs-4 col-sm-4 col-md-3 col-lg-2 all">Data</th>
                        <th class="col-xs-8 col-sm-4 col-md-4 col-lg-3 all">URL</th>
                        <th class="col-lg-2 dtlg">Sessão</th>
                        <th class="col-sm-4 col-md-4 col-lg-4 dtsm dtmd dtlg">Parâmetros</th>
                        <th class="col-md-1 col-lg-1 dtmd dtlg">IP</th>
                        <th class="none">Plataforma</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>                  
            </table>
        </div>
        <div id="infos" class="tab-pane fade">
            <table id="tabela-infos" class="table table-striped table-hover table-bordered dt-responsive">
                <thead>
                    <tr>
                        <th class="col-xs-4 col-sm-4 col-md-3 col-lg-2 all">Data</th>
                        <th class="col-xs-8 col-sm-4 col-md-4 col-lg-3 all">URL</th>
                        <th class="col-lg-2 dtlg">Sessão</th>
                        <th class="col-sm-4 col-md-4 col-lg-4 dtsm dtmd dtlg">Mensagem</th>
                        <th class="col-md-1 col-lg-1 dtmd dtlg">IP</th>
                        <th class="none">Plataforma</th>
                        <th class="none">Parâmetros</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div id="errors" class="tab-pane fade">
            <table id="tabela-errors" class="table table-striped table-hover table-bordered dt-responsive">
                <thead>
                    <tr>
                        <th class="col-xs-4 col-sm-4 col-md-3 col-lg-2 all">Data</th>
                        <th class="col-xs-8 col-sm-4 col-md-4 col-lg-3 all">URL</th>
                        <th class="col-lg-2 dtlg">Sessão</th>
                        <th class="col-sm-4 col-md-4 col-lg-4 dtsm dtmd dtlg">Mensagem</th>
                        <th class="col-md-1 col-lg-1 dtmd dtlg">IP</th>
                        <th class="none">Plataforma</th>
                        <th class="none">Parâmetros</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>

    if (udev.url.getNameModule() == 'Geral') {
        urlAuditLogs = udev.url.getUrlController() + '/findLogsByType/S/';
        urlInfosLogs = udev.url.getUrlController() + '/findLogsByType/I/';
        urlErrorLogs = udev.url.getUrlController() + '/findLogsByType/E/';
    } else {
        urlAuditLogs = udev.url.getUrlController() + '/findAuditByParams/S/';
        urlInfosLogs = udev.url.getUrlController() + '/findAuditByParams/I/';
        urlErrorLogs = udev.url.getUrlController() + '/findAuditByParams/E/';
    }

    var recordTableO;
    var recordTableI;
    var recordTableE;

    function atualizaDados() {
        if (!$('#datepicker').val()) {
            recordTableO.ajax.url( urlAuditLogs + $('#registros').val() ).load();
            recordTableI.ajax.url( urlInfosLogs + $('#registros').val() ).load();
            recordTableE.ajax.url( urlErrorLogs + $('#registros').val() ).load();
        } else {
            recordTableO.ajax.url( urlAuditLogs + $('#registros').val() + '/' + $('#datepicker').val() ).load();
            recordTableI.ajax.url( urlInfosLogs + $('#registros').val() + '/' + $('#datepicker').val() ).load();
            recordTableE.ajax.url( urlErrorLogs + $('#registros').val() + '/' + $('#datepicker').val() ).load();
        }
    }

    function handle(e){
        if(e.keyCode === 13){
            atualizaDados();
        }
    }
    
    $(document).ready(function() {

        $( function() {
            $("#datepicker").datepicker({   
                dateFormat: "dd/mm/yy" ,
                showOn: "button",
                buttonImage: "/imagens/Geral/calendar-25x25.png",
                buttonImageOnly: true,
                onSelect: function() {
                    //console.log( $(this).val() );
                    atualizaDados();
                }
            });
        } );
        
        recordTableO = $('#tabela-others').DataTable({
            ajax : {
                "processing": true,
                "format": "jsonp",
                "url" : urlAuditLogs,
                "dataFilter" : function(json) {
                    var json = $.parseJSON( json );
                    json.data = $.map(json.logs, function(e) { return e });
                    recordList = json.logs;
                    return JSON.stringify( json );
                }
            },
            columnDefs: [
                { 
                    render: function ( data, type, row, meta ) { return data.substring(0,19); },
                    targets: [0] 
                },
                { 
                    render: function ( data, type, row, meta ) { tmp = data.replace('GET: []',''); return tmp.replace('POST: []',''); },
                    targets: [3] 
                }
            ],
            columns: [
                {"data": "dtCriacao.date" },
                {"data": "url" },
                {"data": "sessionId" },
                {"data": "detail" },
                {"data": "ip" },
                {"data": "plataform" }
            ],
            responsive: {
                breakpoints: [
                    { name: 'dtlg', width: Infinity },
                    { name: 'dtmd', width: 1200 },
                    { name: 'dtsm', width: 992 },
                    { name: 'dtxs', width: 768 }
                ]
            },
            "autoWidth": false,
            "order": [[0, 'desc']]
        });

        recordTableI = $('#tabela-infos').DataTable({
            ajax : {
                "processing": true,
                "format": "jsonp",
                "url" : urlInfosLogs,
                "dataFilter" : function(json) {
                    var json = $.parseJSON( json );
                    json.data = $.map(json.logs, function(e) { return e });
                    recordList = json.logs;
                    return JSON.stringify( json );
                }
            },
            columnDefs: [
                { 
                    render: function ( data, type, row, meta ) { return data.substring(0,19); },
                    targets: [0] 
                }
            ],
            columns: [
                {"data": "dtCriacao.date" },
                {"data": "url" },
                {"data": "sessionId" },
                {"data": "msg" },
                {"data": "ip" },
                {"data": "plataform" },
                {"data": "detail" }
            ],

            responsive: {
                breakpoints: [
                    { name: 'dtlg', width: Infinity },
                    { name: 'dtmd', width: 1200 },
                    { name: 'dtsm', width: 992 },
                    { name: 'dtxs', width: 768 }
                ]
            },
            "autoWidth": false,
            "order": [[0, 'desc']]
        });

        recordTableE = $('#tabela-errors').DataTable({
            ajax : {
                "processing": true,
                "format": "jsonp",
                "url" : urlErrorLogs,
                "dataFilter" : function(json) {
                    var json = $.parseJSON( json );
                    json.data = $.map(json.logs, function(e) { return e });
                    recordList = json.logs;
                    return JSON.stringify( json );
                }
            },
            columnDefs: [
                { 
                    render: function ( data, type, row, meta ) { return data.substring(0,19); },
                    targets: [0] 
                }
            ],
            columns: [
                {"data": "dtCriacao.date" },
                {"data": "url" },
                {"data": "sessionId" },
                {"data": "msg" },
                {"data": "ip" },
                {"data": "plataform" },
                {"data": "detail" }
            ],

            responsive: {
                breakpoints: [
                    { name: 'dtlg', width: Infinity },
                    { name: 'dtmd', width: 1200 },
                    { name: 'dtsm', width: 992 },
                    { name: 'dtxs', width: 768 }
                ]
            },
            "autoWidth": false,
            "order": [[0, 'desc']]
        });

    } );
</script>