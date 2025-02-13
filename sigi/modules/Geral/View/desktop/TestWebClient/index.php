<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script>
    function testGetWebClients ()
    {
        var url = '<?= $this->url->getUrlModule() ?>/WebClient/getWebClients';

        $("#response").load(url);
    }

    function testGetWebClient (jsonString)
    {
        var json = JSON.parse(jsonString),
            url = '<?= $this->url->getUrlModule() ?>/WebClient/getWebClient/'+json.token;

        $("#response").load(url);
    }

    function testSaveWebClient (jsonString)
    {
        var json = JSON.parse(jsonString),
            url = '<?= $this->url->getUrlModule() ?>/WebClient/saveWebClient';

        $("#response").load(url, json);
    }

    function testGetBoletoServicesByToken (jsonString)
    {
        var json = JSON.parse(jsonString),
            url = '<?= $this->url->getUrlModule() ?>/WebClient/getBoletoServicesByToken/'+json.token;

        $("#response").load(url);
    }

    function testSaveBoletoService (data)
    {
        var url = '<?= $this->url->getUrlModule() ?>/BoletoService/saveBoletoService';

        boletoService = JSON.parse(data);
        console.log(boletoService);
        $("#response").load(url, boletoService);
    }

    function testRemoveWebClient (jsonString)
    {
        var json = JSON.parse(jsonString),
            url = '<?= $this->url->getUrlModule() ?>/WebClient/removeWebClient/'+json.token;

        $("#response").load(url);
    }

    // ENDPOINTS =======================================================================================================

    function getWebClientEndpoints (jsonString)
    {
        var json = JSON.parse(jsonString),
            url = '<?= $this->url->getUrlModule() ?>/WebClient/getWebClientEndpoints/'+json.token;

        $.ajax({
            url : url,
            method : "GET",

            success : function(data) {
                $("#response").html(JSON.stringify(data));
            }
        });
    }

    function saveWebClientEndpoints (jsonString)
    {
        var json = JSON.parse(jsonString),
        url = '<?= $this->url->getUrlModule() ?>/WebClient/saveWebClientEndpoints/'+json.token;

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


<h2>Testes relacionados ao WebClientController</h2>

<div class="container">
    <div class="col-sm-5">
        <h5>Ações</h5>
        <a href="javascript: void(0)" onclick="$(this).next().slideToggle()">GetWebClients</a>
        <div class="fechar">
            <button onclick="testGetWebClients()">Enviar</button><br />
        </div>

<!-- ============================================================================================================== -->

        <hr />

        <a href="javascript: void(0)" onclick="$(this).next().slideToggle()">GetWebClient</a>
        <div class="fechar">
            <textarea style="width: 100%; height: 50px">
                {
                    "token" : "5c4d07ad1ed788ddff1ba9eef87bd30e"
                }
            </textarea>
            <button onclick="testGetWebClient($(this).prev().val())">Enviar</button><br />
        </div>

<!-- ============================================================================================================== -->

        <hr />

        <a href="javascript: void(0)" onclick="$(this).next().slideToggle()">GetBoletoServicesByToken</a>
        <div class="fechar">
            <textarea style="width: 100%; height: 50px">
                {
                    "token" : "5c4d07ad1ed788ddff1ba9eef87bd30e"
                }
            </textarea>
            <button onclick="testGetBoletoServicesByToken($(this).prev().val())">Enviar</button><br />
        </div>

<!-- ============================================================================================================== -->

        <hr />

        <a href="javascript: void(0)" onclick="$(this).next().slideToggle()">SaveWebClient</a>
        <div class="fechar">
            Dados do serviço:
            <textarea style="width: 100%; height: 100px">
                {
                    "token" : "5c4d07ad1ed788ddff1ba9eef87bd30e",
                    "descricao" : "Teste",
                    "ips" : "",
                    "emails" : ""
                }
            </textarea>
            <button onclick="testSaveWebClient($(this).prev().val())">Enviar</button><br />
        </div>

<!-- ============================================================================================================== -->

        <hr />

        <a href="javascript: void(0)" onclick="$(this).next().slideToggle()">RemoveWebClient</a>
        <div class="fechar">
            <textarea style="width: 100%; height: 50px">
                {
                    "token" : "5c4d07ad1ed788ddff1ba9eef87bd30e"
                }
            </textarea>
            <button onclick="testRemoveWebClient($(this).prev().val())">Enviar</button><br />
        </div>

<!-- ENDPOINTS ===================================================================================================== -->

        <hr />

        <a href="javascript: void(0)" onclick="$(this).next().slideToggle()">GetWebClientEndpoints</a>
        <div class="fechar">
            Dados para envio:
            <textarea style="width: 100%; height: 100px">
                {
                    "token": ""
                }
            </textarea>
            
            <button onclick="getWebClientEndpoints($(this).prev().val())">testGetWebClientEndpoints</button><br />
        </div>

<!-- ============================================================================================================== -->

        <hr />

        <a href="javascript: void(0)" onclick="$(this).next().slideToggle()">saveWebclientEndpoints</a>
        <div class="fechar">
            Dados para envio:
            <textarea style="width: 100%; height: 200px">
                {
                    "token" : "",
                    "endpoints" : {
                        "GET" : [
                            "/Template/WSv1/user",
                            "/Template/WSv1/user/{id}"
                        ]
                    }
                }
            </textarea>
            
            <button onclick="saveWebClientEndpoints($(this).prev().val())">testSaveWebclientEndpoints</button><br />
        </div>


<!-- ============================================================================================================== -->
        </div>

    <div class="col-sm-7">
        <h5>Resposta da requisição</h5>
        <div id="response"></div>
    </div>
</div>
