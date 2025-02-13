<?php $tipoRegistro = 'Arquivo'; ?>

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
        <h1>
            <button type="button" onclick="addRecord()" class="btn btn-lg btn-primary pull-right">Upload</button>
            <?= $tipoRegistro ?>s
        </h1>
        Código exemplo utilizando as constantes do framework:
        <pre>
        $filesPath = DIR_ROOT . '/' . DIR_NAME_FILES . '/' . $this->url->getNameModule() . '/';
        $tempPath = $filesPath . DIR_NAME_TEMP . '/';
        $finalPath = $filesPath . DIR_NAME_DIVERSOS . '/';</pre>
    </div>
    <div class="panel-body">
    <table id="simple-record-table" class="table table-striped table-hover table-bordered tabela-servicos dt-responsive nowrap">
        <thead>
            <tr>
                <th class="col-sm-4 ">Arquivo</th>
                <th class="col-sm-1 ">Opções</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    </div>
</div>


<!------------------ Formulário -------------------->

<div id="formContainer" class="invisible">
    <div class="panel panel-default">
    <div class="panel-heading">
        <h3 id="formTitle">Inserir/Atualizar Arquivo</h3>
    </div>
    <div class="panel-body">

        <form class="form-horizontal" method="post" id="form-cadastro" enctype="multipart/form-data">

            <!----- Arquivo ----->
            <div class="form-group">
                <label for="inFile" class="col-sm-3 control-label">Arquivo:</label>
                <div class="col-sm-8">
                <input class="btn-md" type="file" name="inFile" id="inFile" />
                <div id="msg_arq"> </div>
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

    function showRecords() {
        $("#formContainer").hide();
        $("#listContainer").show();
    }

    function addRecord() {
        $("#formTitle").html('Inserir <?= $tipoRegistro ?>');
        $("#listContainer").hide();
        $("#formContainer").show();
    }

    function editRecord(nome) {
        $("#formTitle").html('Atualizar <?= $tipoRegistro ?>');
        $("#listContainer").hide();
        $("#formContainer").show();
    }

    function removeRecord(nome) {
        if (confirm("Tem certeza que deseja remover o arquivo "+nome+"'?")) {
            $.get("<?= $this->url->getUrlController() ?>/removeUpload/"+nome, function(response) {
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

    function saveRecord () {
        var form = $('form-cadastro')[0]; // You need to use standard javascript object here
        var formData = new FormData(form);
        formData.append('inFile', $('input[type=file]')[0].files[0]); 
        $.ajax({
            url: '<?= $this->url->getUrlController() ?>/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) { 
                if (response.result == 'success') $("#msg").addClass("alert-success");
                if (response.result == 'error') $("#msg").addClass("alert-danger");
                $("#msg").fadeIn();
                $("#msg").html(response.msg);
                showRecords();
                recordTable.ajax.reload();
                $("#msg").fadeOut(3000);
            }
        });
    }

    // Submit do form cadastro
    $("#form-cadastro").submit(function(e) {
        e.preventDefault();
        if (!$("#inFile").val()) {
            $("#msg_arq").html("Por favor, selecione um arquivo.");
        } else {
            $("#msg_arq").html(" ");
            saveRecord();
        }
    });

    var recordTable, 
        recordList = [];

    $(document).ready(function() {
        recordTable = $("#simple-record-table").DataTable( {
            ajax : {
                "processing": true,
                "format": "jsonp",
                "url" : "<?php echo '/'.$this->url->getNameModule().'/'.$this->url->getNamecontroller() ?>/getFileList",
                "dataFilter" : function(json) {
                    var json = $.parseJSON( json );
                    json.data = $.map(json.files, function(e) { return e });
                    recordList = json.files;
                    return JSON.stringify( json );
                }
            },
            columnDefs: [
                {
                    targets: [0],
                    className: 'dt-middle'
                },
                {
                    targets: [1],
                    className: 'dt-middle text-center'
                }
            ],
            columns: [
                {"data": "name" },
                {
                    sortable: false,
                    render: function ( row, type, val, meta ) { 
                        //return '<span class="btn btn-sm btn-warning glyphicon glyphicon-pencil" aria-hidden="true" onclick="editRecord(\''+val.name+'\')"></span> '+
                        return '<span class="btn btn-sm btn-danger glyphicon glyphicon-trash" aria-hidden="true" onclick="removeRecord(\''+val.name+'\')"></span>';
                    }
                }                
            ]
        });
    });

</script>