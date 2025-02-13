<div class="container">
<h2>Importar SQL</h2>
<br/>
<form class="form-horizontal" method="post" id="form-importar">

    <div class="form-group">
        <label for="inFile" class="col-sm-3 control-label">Arquivo com o SQL:</label>
        <div class="col-sm-8">
            <input class="btn-md" type="file" name="iniFile" id="iniFile" />
            <small id="msg_file" class="form-text text-muted">
                Atenção: o arquivo deve ser do tipo .txt
            </small>
        </div>
    </div>
    <div class="form-group">
        <label for="comandosSql" class="col-sm-3 control-label">ou comandos SQL:</label>
        <div class="col-sm-6">
            <textarea rows="5" cols="60" id="comandosSql"></textarea>
            <div id="msg" class="alert"> </div>
        </div>
    </div>

    <div class="col-sm-3">
    </div>
    <div class="col-sm-8">
        <button type="submit" class="btn btn-primary btn-md">Importar</button>
        &nbsp;&nbsp;<a href="<?= $this->url->getUrlController() ?>">Limpar</a>
    </div>

</form>

<!--------------------- JS ----------------------->

<script>

    function importaComandosSql() {
        var jsonData = {
            'comandosSql': $("#comandosSql").val()
        };
        $.post("<?= $this->url->getUrlController() ?>/executarComandosSQL", jsonData, function(response) {
            if (response.result == 'success') {
                $("#msg").addClass("alert-success");
                $("#msg").html(response.msg);
            } else {
                $("#msg").addClass("alert-danger");
                $("#msg").html("Comandos SQL não aprovado, por favor verifique!");                
            }
        });
    }

    function importaArquivoSql(formData) {
        $.ajax({
            url: '<?= $this->url->getUrlController() ?>/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false
        }).done(function(response) {
            if (response.result == 'success') {
                var jsonData = {
                    'fileName': response.file.name
                };
                console.log(response.file.name);
                $.post("<?= $this->url->getUrlController() ?>/executarArquivoSQL", jsonData, function(response2) {
                    if (response2.result == 'success') {
                        $("#msg").addClass("alert-success");
                        $("#msg").html(response2.msg);
                    } else {
                        $("#msg").addClass("alert-danger");
                        $("#msg").html("Comandos SQL não aprovado, por favor verifique!");                
                    }
                });
            } else {
                console.log(result.msg);
                $("#msg").addClass("alert-danger");
                $("#msg").html("Erro ao importar o arquivo.");                
            }
        });
    }

    // Submit do form
    $("#form-importar").submit(function(e) {
        e.preventDefault();
        var cont = 0;
        var msg_validacao = '';

        if ($("#iniFile").val()) {
            cont = 1;
        }
        if ($("#comandosSql").val()) {
            cont += 2;
        }

        //$("#msg").removeClass("alert-info");
        $("#msg").removeClass("alert-danger");
        $("#msg").removeClass("alert-success");
        console.log(cont);

        switch (cont) {
            case 0:
                msg_validacao = "Por favor, informe o arquivo ou digite os comandos SQL.";
                $("#msg").addClass("alert-danger");
                $("#msg").html(msg_validacao);
                break;
            case 1:
                if ($("#iniFile").val().match(/\.txt/i)) {
                    var formData = new FormData(this);
                    ($("#iniFile").val());
                    importaArquivoSql(formData);                    
                } else {
                    msg_validacao = "Verifique o tipo de arquivo permitido (txt)";
                    $("#msg").addClass("alert-danger");
                    $("#msg").html(msg_validacao);
                }
                break;
            case 2:
                importaComandosSql();
                break;
            case 3:
                $("#msg").addClass("alert-danger");
                msg_validacao = "Por favor, informe APENAS um: o arquivo ou os comandos SQL.";
                $("#msg").html(msg_validacao);
                break;
        }
        
    });
</script>