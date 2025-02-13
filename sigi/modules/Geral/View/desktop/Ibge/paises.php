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
            <button type="button" onclick="addRecord()" class="btn btn-lg btn-primary pull-right">Novo</button> 
            <button type="button" onclick="importRecords()" class="btn btn-lg btn-secondary pull-right">Importar dados</button>
            País
        </h1>
    </div>
    <div class="panel-body">
    <table id="simple-record-table" class="table table-striped table-hover table-bordered tabela-servicos dt-responsive">
        <thead>
            <tr>
                <th class="col-sm-1">Id</th>
                <th class="col-sm-4">Descrição</th>
                <th class="col-sm-1">Opções</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    </div>
</div>

<!------------------ Importar arquivo -------------------->

<div id="fileContainer" class="invisible">
    <div class="panel panel-default">
    <div class="panel-heading">
        <h3>Importar Dados de Países</h3>
    </div>
    <div class="panel-body">

        <form class="form-horizontal" method="post" id="form-importar">

            <div class="form-group">
                <label for="inFile" class="col-sm-3 control-label">Arquivo com os dados:</label>
                <div class="col-sm-8">
                <input class="btn-md" type="file" name="inFile" id="inFile" />
                </div>
            </div>
            <div class="form-group" id="msg_file">
                <label class="col-sm-6 control-label">
                    Atenção: o arquivo deve ser do tipo .csv sem cabeçalho<br/> (um país por linha)
                </label>
            </div>

            <br>
            <div class="col-sm-3">
            </div>
            <div class="col-sm-8">
                <button type="submit" class="btn btn-primary btn-md">Enviar</button>
                &nbsp;&nbsp;
                <a onclick="showRecords()">Cancelar</a>
            </div>

        </form>
    </div>
    </div>
</div>


<!------------------ Formulário -------------------->

<div id="formContainer" class="invisible">
    <div class="panel panel-default">
    <div class="panel-heading">
        <h3 id="formTitle">Inserir/Atualizar Registro</h3>
    </div>
    <div class="panel-body">

        <form class="form-horizontal" method="post" id="form-cadastro">
            <input type="hidden" name="tipo" id="tipo" value="pais">
            <input type="hidden" name="id" id="id">

            <!----- Descrição ----->
            <div class="form-group">
                <label for="descricao" class="col-sm-3 control-label">Descrição:</label>
                <div class="col-sm-8">
                <input type="text" class="form-control" name="descricao" id="descricao" />
                <div id="msg_descricao"> </div>
                </div>
            </div>

            <br>
            <div class="col-sm-3">
            </div>
            <div class="col-sm-8">
                <button type="submit" class="btn btn-primary btn-md">Enviar</button>
                &nbsp;&nbsp;
                <a onclick="showRecords()">Cancelar</a>
            </div>

        </form>
    </div>
    </div>
</div>

</div>

<!--------------------- JS ----------------------->

<script>

    $("#formContainer").removeClass("invisible");
    $("#formContainer").hide();
    $("#fileContainer").removeClass("invisible");
    $("#fileContainer").hide();

    function showRecords() {
        $("#formContainer").hide();
        $("#fileContainer").hide();
        $("#listContainer").show();
    }

    function importRecords() {
        $("#formTitle").html('Inserir País');
        $("#listContainer").hide();
        $("#fileContainer").show();
    }

    function addRecord() {
        $("#formTitle").html('Inserir País');
        $("#listContainer").hide();
        $("#id").val("");
        $("#descricao").val("");
        $("#formContainer").show();
    }

    function editRecord(id, descricao) {
        if (descricao == 'Brasil') {
            alert("O país Brasil não pode ser alterado, pois os estados e municípios sincronizados pelo IBGE estão relacionados a ele.");
        } else {        
            $("#formTitle").html('Atualizar País');
            $("#listContainer").hide();
            $("#id").val(id);
            $("#descricao").val(descricao);
            $("#formContainer").show();
        }
    }

    function removeRecord(id, descricao) {
        if (descricao == 'Brasil') {
            alert("O país Brasil não pode ser removido, pois os estados e municípios sincronizados pelo IBGE estão relacionados a ele.");
        } else {
            if (confirm("Tem certeza que deseja remover o registro "+id+" - '"+descricao+"'?")) {
                $.get(udev.url.getUrlModule() + "/RegistroSimples/remove/pais/"+id, function(response) {
                    if (response.result == 'success') {
                        $("#msg").addClass("alert-success");
                        $("#msg").fadeIn();
                        $("#msg").html(response.msg);
                        recordTable.ajax.reload();
                        $("#msg").fadeOut(3000);
                    }
                });
            }
        }
    }

    function saveRecord () {
        var jsonData = { 'tipo': $("#tipo").val(), 'id': $("#id").val(), 'descricao': $("#descricao").val() };
        $.post(udev.url.getUrlModule()+"/RegistroSimples/save", jsonData, function(response) {
            if (response.result == 'success') $("#msg").addClass("alert-success");
            if (response.result == 'error') $("#msg").addClass("alert-danger");
            $("#msg").fadeIn();
            $("#msg").html(response.msg);
            showRecords();
            recordTable.ajax.reload();
            $("#msg").fadeOut(3000);
        });
    }

    // Submit do form cadastro
    $("#form-cadastro").submit(function(e) {
        e.preventDefault();
        if (!$("#descricao").val()) {
            $("#msg_descricao").html("Por favor, preencha a descrição.");
        } else {
            $("#msg_descricao").html(" ");
            saveRecord();
        }
    });

    // Submit do form importar
    $("#form-importar").submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        ($("#inFile").val());

        if ($("#inFile").val().match(/\.csv/i)) { 
            $("#msg_file").removeClass("has-error");

            $.ajax({
                url: udev.url.getUrlController()+'/upload',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false
            }).done(function (response) {
                if (response.result == 'success') {
            
                    var jsonData = { 'tipo': 'pais', 'nomeArquivo': response.file.name };
                    $.post(udev.url.getUrlModule()+"/RegistroSimples/importarDadosDeArquivo", jsonData, function(response) {
                        if (response.result == 'success') {
                            $("#msg").addClass("alert-success");
                            $("#msg").fadeIn();
                            $("#msg").html(response.msg);
                            showRecords();
                            recordTable.ajax.reload();
                            $("#msg").fadeOut(3000);
                        }
                    });

                } else {
                    console.log(result.msg);
                }
            });
        } else {
            $("#msg_file").addClass("has-error");
        }
    });

    var recordTable, 
        recordList = [];

    $(document).ready(function() {
        recordTable = $("#simple-record-table").DataTable( {
            ajax : {
                "processing": true,
                "format": "jsonp",
                "url" : udev.url.getUrlController()+"/getAllPaises",
                "dataFilter" : function(json) {
                    var json = $.parseJSON( json );
                    json.data = $.map(json.paises, function(e) { return e });
                    recordList = json.paises;
                    return JSON.stringify( json );
                }
            },
            columnDefs: [
                {
                    targets: [0, 1],
                    className: 'dt-middle text-center'
                },
                {
                    targets: [2],
                    className: 'dt-middle'
                }    
            ],
            columns: [
                {"data": "id" },
                {"data": "descricao" },
                {
                    sortable: false,
                    render: function ( row, type, val, meta ) { 
                        return '<span class="btn btn-sm btn-warning glyphicon glyphicon-pencil" aria-hidden="true" onclick="editRecord('+val.id+',\''+val.descricao+'\')"></span> '+
                            '<span class="btn btn-sm btn-danger glyphicon glyphicon-trash" aria-hidden="true" onclick="removeRecord('+val.id+',\''+val.descricao+'\')"></span>';
                    }
                }                
            ],
            aaSorting: [[1, "asc"]]
        });
    });

</script>