<script>
    function testGetUser (jsonString)
    {
        var json = JSON.parse(jsonString),
            url = '<?= $this->url->getUrlModule() ?>/User/getUser/'+json.id;

        $.ajax({
            url : url,
            method : "GET",

            success : function(data) {
                $("#response").html(JSON.stringify(data));
            }
        });
    }

    function testAddUser (jsonString)
    {
        var json = JSON.parse(jsonString),
            url = '<?= $this->url->getUrlModule() ?>/User/add';

        $.ajax({
            url : url,
            method : "POST",
            data : json,
            success : function(data) {
                $("#response").html(JSON.stringify(data));
            }
        });
    }

    function testUpdateUser (jsonString)
    {
        var json = JSON.parse(jsonString),
            url = '<?= $this->url->getUrlModule() ?>/User/update';

        $.ajax({
            url : url,
            method : "POST",
            data : json,
            success : function(data) {
                $("#response").html(JSON.stringify(data));
            }
        });
    } 
</script>
<style>
    .fechar {
        display: none;
    }
    #response {
        white-space: pre-wrap;
        font-size: 10px;
    }
</style>

<div class="container">

    <div class="row">
        <div class="col-12">

            <h2>Testes relacionados ao FileRepositoryController</h2>
        </div>
    </div>

    <div class="row">

        <div class="col-12 col-lg-5">
            <h5>Ações</h5>

            <!-- ============================================================================================================== -->
            <hr>

            <a href="javascript: void(0)" onclick="$(this).next().slideToggle()">upload</a>
            <div class="fechar">
                <form id="formFile" class="form" method="post" target="#file" enctype="multipart/form-data" action="">
                    <div class="form-group">
                        <label for="token">Url: 
                            <input id="inUrl" />
                            <div class="help-block">Exemplo: /Template/User</div>
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="token">Arquivo: <input type="file" name="inFile" /></label>
                    </div>
                    <button class="btn btn-default">Enviar</button>
                </form>
            </div>

            <!-- ============================================================================================================== -->
        </div>
        <div class="col-12 col-lg-7">
            <h5>Resposta da requisição</h5>
            <div id="response"></div>
        </div>
    <div>
</div>

<script>
$("#formFile").submit(function(e) {
    $(this).attr('action', $("#inUrl").val()+"/upload");
});
</script>