<style>
    #container-actions textarea {
        width: 100%;
    }
    #container-actions .ta-padding {
        padding: 10px 15px;
    }
    #response {
        white-space: pre-wrap;
        min-height: 150px;
        /* font-size: 10px; */
    }
    .panel {
        min-height: 400px;
    }
    .list-group.list-group-root {
        padding: 0;
        overflow: hidden;
    }
    .list-group.list-group-root .list-group {
        margin-bottom: 0;
    }
    .list-group.list-group-root .list-group-item {
        border-radius: 0;
        border-width: 1px 0 0 0;
    }
    .list-group.list-group-root > li:first-child > a {
        border-top: 0;
    }
    .action-toggle:before {
        font-family: 'Glyphicons Halflings';
        content: "\e114";
        float: left;
        color: grey;
        margin-right: 5px;
    }
    .action-toggle.collapsed:before {
        content: "\e080";
    }
    h1 {
        font-size: 36px;
        color: inherit;
    }
    textarea {
        resize: vertical;
    }
    button {
        margin-top: 5px;
    }
</style>
<div class="container">
    <div class="page-header">
        <h1>Testes relacionados ao <?=$controller."Controller"?></h1>
    </div>

    <div id="container-actions" class="col-sm-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Ações</h3>
            </div>
            <div class="panel-body">
            </div>
        </div>
    </div>
    <div id="container-response" class="col-sm-offset-1 col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Resposta da requisição <span id="action-response" class="glyphicon"></span></h3>
            </div>
            <div class="panel-body">
                <pre id="response">Execute a ação desejada</pre>
            </div>
        </div>
    </div>
</div>

<script>
    var obj = <?=json_encode($testConfig);?>,
        qtde = Object.keys(obj).length,
        testFunctions = [],
        index = 0, method = '', url = '', el = '', jsonString = '', keys= [],
        actionsEl = $('<ul class="list-group list-group-root well"></ul>');

    for(var i in obj) {
        el = '';
        method = i;
        for(var j in obj[i]) {
            url = j;
            jsonString = JSON.parse(obj[i][j]['data']);
            keys = obj[i][j]['dataAttrToUrl'].toString();
            testFunctions['f'+index] = new Function('data',`
                var json = JSON.parse(data), dataAttrToUrl = '';

                $("#response").html("Buscando resultados...");

                if ("`+keys+`".trim() != "") {
                    keys = "`+keys+`".split(",");
                    for (var x in keys) {
                        dataAttrToUrl += "/"+json[keys[x]];
                        delete json[keys[x]];
                    }
                }
                $.ajax({
                    url: "`+url+`"+dataAttrToUrl,
                    method: "`+method+`",
                    data: json,
                    success: function(response) {
                        decoratePanelHeading(response.result);
                        $("#response").html(JSON.stringify(response, null, 2));
                    },
                    error: function(){
                        decoratePanelHeading("warning");
                    }
                });
            `);
            el = `<li>
                <a class="list-group-item action-toggle collapsed" href="#action`+index+`" data-toggle="collapse" data-parent="#container-actions" aria-expanded="false">
                    <h4 class="list-group-item-heading">`+url.split("/")[3]+` (`+method+`)</h4>
                </a>
                <div class="list-group-item-text collapse" id="action`+index+`">
                    <div class="ta-padding">
                        <textarea rows="5">`+JSON.stringify(jsonString, null, 4)+`</textarea>
                        <button class="btn btn-default" onclick="testFunctions.f`+index+`($(this).prev().val())">Enviar</button>
                    </div>
                </div>`;
            $(actionsEl).append(el);
            index++;
        }
        $("#container-actions .panel-body").append(actionsEl);
    }
    $("button").add("textarea").click(function(event){
        event.stopPropagation();
    })

    function decoratePanelHeading(status){
        var panelClass = '';

        $("#action-response").removeClass('glyphicon-remove glyphicon-ok glyphicon-alert');
        if (status == "error") {
            panelClass = 'danger';
            $("#action-response").addClass('glyphicon-remove');
        } else {
            panelClass = status;
            if (panelClass == 'success') {
                $("#action-response").addClass('glyphicon-ok');
            } else {
                $("#action-response").addClass('glyphicon-alert');
            }

        }
        $("#container-response")
            .find('.panel')
            .removeClass('panel-success panel-warning panel-danger')
            .addClass('panel-'+panelClass);
    }
</script>
