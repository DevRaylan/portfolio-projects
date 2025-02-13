<style>
    .msg{
        color: #dc3545;
    }
    
</style>

<div class="container">
    <div id="formContainer">
        <br /><p id="msg">&nbsp;</p>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 id="formTitle">Inserir/Atualizar Registro</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal needs-validation" method="post" id="form-cadastro">
                    <div class="form-row">
                        <input type="hidden" name="tipo" id="tipo" value="<?= $tipo ?>">
                        <input type="hidden" name="id" id="id">

                        <!----- Nome ----->
                        <div class="col-md-12">
                            <label for="nome" class="control-label">Nome:</label>
                            <input type="text" class="form-control" name="nome" id="nome" />
                            <div class="msg" id="msg_nome"> </div>
                        </div>

                        <!----- Descrição ----->
                        <div class="col-md-12">
                            <label for="descricao" class="control-label">Descrição:</label>
                            <textarea class="form-control" name="descricao" id="descricao"></textarea>
                            <div class="msg" id="msg_descricao"> </div>
                        </div>

                        <!----- App ----->
                        <div class="col-md-4 mb-3">
                            <label for="app" class="control-label">App:</label>
                            <input type="text" class="form-control" name="app" id="app" />
                            <div class="msg" id="msg_app"> </div>
                        </div>

                        <!----- Controller ----->
                        <div class="col-md-4 mb-3">
                            <label for="controller" class="control-label">Controller:</label>
                            <input type="text" class="form-control" name="controller" id="controller" />
                            <div class="msg" id="msg_controller"> </div>
                        </div>

                        <!----- Action ----->
                        <div class="col-md-4 mb-3">
                            <label for="action" class="control-label">Action:</label>
                            <input type="text" class="form-control" name="action" id="action" />
                            <div class="msg" id="msg_action"> </div>
                        </div>

                        <!----- Parâmetros ----->
                        <div class="col-md-12">
                            <label for="parametros" class="control-label">Parâmetros:</label>
                            <textarea class="form-control" name="parametros" id="parametros"></textarea>
                            <div class="msg" id="msg_parametros"> </div>
                        </div>

                        <div class="col-md-12">
                            <p>
                                Os campos "App", "Controller", "Action" e "Parâmetros" compõem o endereço que será acionado no horário configurado. Por exemplo, para os campos:
                                <br/>
                                <b>App:</b> Geral
                                <br/>
                                <b>Controller:</b>NotificacaoGeral
                                <br/>
                                <b>Action:</b>notificar
                                <br/>
                                <b>Parâmetros:</b>1
                                <br>
                                Será gerado o endereço /Geral/NotificacaoGeral/notificar/1
                                <br>
                                <br>
                                No campo "Parâmetros" também podem ser adicionados campos, por exemplo, ao informar ?valor=1 será gerado o endereço será gerado o endereço /Geral/NotificacaoGeral/notificar/?valor=1
                                <br>
                                É necessário adicionar o "?" antes do campo e "&" caso exista mais de um
                            </p>
                        </div>

                        <!----- Minuto ----->
                        <div class="col-md-2">
                            <label for="minuto" class="control-label">Minuto:</label>
                            <input type="text" class="form-control" name="minuto" id="minuto" />
                            <div class="msg" id="msg_minuto"> </div>
                        </div>

                        <!----- Hora ----->
                        <div class="col-md-2">
                            <label for="hora" class="control-label">Hora:</label>
                            <input type="text" class="form-control" name="hora" id="hora" />
                            <div class="msg" id="msg_hora"> </div>
                        </div>

                        <!----- diaMes ----->
                        <div class="col-md-2">
                            <label for="diaMes" class="control-label">Dia do Mês:</label>
                            <input type="text" class="form-control" name="diaMes" id="diaMes" />
                            <div class="msg" id="msg_diaMes"> </div>
                        </div>

                        <!----- Mês ----->
                        <div class="col-md-2">
                            <label for="mes" class="control-label">Mês:</label>
                            <input type="text" class="form-control" name="mes" id="mes" />
                            <div class="msg" id="msg_mes"> </div>
                        </div>

                        <!----- diaSemana ----->
                        <div class="col-md-2">
                            <label for="diaSemana" class="control-label">Dia da Semana:</label>
                            <input type="text" class="form-control" name="diaSemana" id="diaSemana" />
                            <div class="msg" id="msg_diaSemana"> </div>
                        </div>

                        <!----- Instruções/Períodos ----->
                        <div class="col-md-12">
                            <p>A configuração do horário é baseada no CRON dos sistemas operacionais Linux. Com isso, podem ser utilizados curingas, que permitem a criação de configurações especiais. Por exemplo: Para executar uma tarefa todos os dias, pode ser informado * (asterisco) no valor do campo "Dia do Mês". Para mais informações clique <a href="https://medium.com/totvsdevelopers/entendendo-o-crontab-607bc9f00ed3">aqui</a> </p>
                        </div>

                        <!----- Rodapé ----->
                        <div class="col-md-12" style="margin-top: 10px;">
                            <button type="submit" class="btn btn-primary btn-md">Enviar</button>
                            &nbsp;&nbsp;
                            <a onclick="cancelarTarefa()">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function cancelarTarefa(){
        window.location.href = udev.url.getUrlController();
    }

    function salvaTarefa() {
        var jsonData = {
            'id'        : $("#id").val(),
            'nome'      : $("#nome").val(),
            'descricao' : $("#descricao").val(),
            'app'       : $("#app").val(),
            'controller': $("#controller").val(),
            'action'    : $("#action").val(),
            'parametros': $("#parametros").val(),
            'minuto'    : $("#minuto").val(),
            'hora'      : $("#hora").val(),
            'diaMes'    : $("#diaMes").val(),
            'mes'       : $("#mes").val(),
            'diaSemana' : $("#diaSemana").val()
        };

        $.post(udev.url.getUrlController() + "/save", jsonData, function(response) {
            if (response.result == 'error') {
                $("#msg").addClass("alert-danger");
                $("#msg").fadeIn();
                $("#msg").html(response.msg);
                return;
            }

            $("#msg").removeClass("alert-danger");
            $("#msg").addClass("alert-success");
            $("#msg").fadeIn();
            $("#msg").html(response.msg);
            $("#msg").fadeOut(2000);

            if (response.result == 'success') {
                setTimeout(function(){
                    window.location.href = udev.url.getUrlController();
                }, 2000);
            }
        });
    }

    // Submit do form cadastro
    $("#form-cadastro").submit(function(e) {
        e.preventDefault();

        //Realiza validação dos registros obrigatórios
        const requireds = [
            'nome', 'app', 'controller', 'action', 'minuto', 'hora', 'diaMes', 'mes', 'diaSemana'
        ];

        let error = false;
        for(let i = 0; i< requireds.length; i++){
            const field = requireds[i];
            const value = $('#'+field).val();
            if(value.trim().length == 0){
                $("#msg_"+field).html("Por favor, preencha o campo. ");
                error = true;
            }
        }

        if(!error){
            //Realizar o submit o formulário
            $(".msg").html(" ");
            salvaTarefa();
        }
    });

    $(document).ready(function(e){
        //Carrega informações dos valores da tela
        const registro = udev.getVar('registro');
        if(registro){
            //Percorrer todos os registros
            let keys = Object.keys(registro);
            for (let i=0; i<keys.length; i++) {      
                const field = keys[i];
                $('#' + field).val(registro[field]);
            }
        }
    });
</script>