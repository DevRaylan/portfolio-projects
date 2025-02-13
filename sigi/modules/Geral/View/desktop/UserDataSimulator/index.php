<div class="container">
    <div class="page-header">
        <h2>Gerenciamento de dados de usuário</h2>
    </div>

    <p>
    Este módulo é utilizado para o gerenciamento de dados de usuários. As informações sobre os usuários são simuladas de 
    acordo com os dados importados de arquivos CSV.
    </p>

    <div class="panel panel-info">
        <div class="panel-heading">
            Carregar dados de um arquivo
        </div>

        <div class="panel-body">
            <p>
            O arquivo deve ser no <strong>formato CSV</strong> com os dados <strong>separados por vírgulas</strong> e com <strong>codificação UTF-8</strong>.
            </p>
            <p>
            Deve conter as seguintes colunas: nome, cpf, matricula, sexo, dataNascimento, vinculo, situacao, unidade, setor, cargo, funcao,
            email, emailAlias, emailAlternativo, telefoneComercial, telefoneCelular, telefoneResidencial, isPrincipal, sexo,dataNascimento,foto
            </p>
            <p>
                <strong>Obs 1:  </strong>Os campos <strong>unidade</strong> e <strong>vínculo</strong> devem ter somente
                letras maiúsculas.<br />
                <strong>Obs 2:  </strong>O campo <strong>isPrincipal</strong> deve ser <strong>true</strong> em todos os
                registros. Caso o usuário tenha mais de um vínculo, somente em um deles o campo isPrincipal deve
                ser true, o restante deve ser false.
            </p>
            <label class="control-label">
                Arquivo:
                <input type="file" name="inDataFile" class="inputFile" />
            </label>
            <br />
            <button type="button" onclick="loadDataFile()" id="btnLoadDataFile" class="btn btn-primary">Carregar dados do arquivo</button>
        </div>
    </div>

    <script>
        function loadDataFile(){
            var dataFileName = $("input[name='inDataFile']").val();

            if(!dataFileName) {
                alert('Por favor, envie o arquivo com os dados.');
                return;
            }

            $("#btnLoadDataFile").prop("disabled", true);
            $("#loading-modal").show();

            $.get("<?= $this->url->getUrlController() ?>/loadDataFile/"+dataFileName, function(response) {
                if(response.result == 'success') {
                    alert(response.msg);
                    document.location = "<?= $this->url->getUrlAction() ?>";
                } else {
                    alert('Erro ao carregar dados do arquivo.');
                    $("#btnLoadDataFile").prop("disabled", false);
                }

                $("#loading-modal").hide();
            });
        }
    </script>

    <table id="listaDeUsuarios" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Id</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Vínculo</th>
                <th>Setor/Curso</th>
                <th>Opções</th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="4" class="text-center">Consultando usuários...</td></tr>
        </tbody>
    </table>

    <div id="modalFormEditar" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Editar dados do usuário</h4>
            </div>
            <div class="modal-body">
                <form id="formEditar">
                    <div class="form-group">
                        <label for="exampleInputEmail1">CPF</label>
                        <input type="text" class="form-control" name="cpf">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Matrícula</label>
                        <input type="text" class="form-control" name="matricula">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Nome</label>
                        <input type="text" class="form-control" name="nome">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Gênero</label>
                        <input type="text" class="form-control" name="sexo">
                        <div class="help-block">Formato: 1 caracter (M, F, etc)</div>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Data de nascimento</label>
                        <input type="text" class="form-control" name="dataNascimento">
                        <div class="help-block">Formato: DD/MM/YYYY</div>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Tipo</label>
                        <input type="text" class="form-control" name="vinculo">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Situacao</label>
                        <input type="text" class="form-control" name="situacao">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Centro</label>
                        <input type="text" class="form-control" name="unidade">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Setor</label>
                        <input type="text" class="form-control" name="setor">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Cargo</label>
                        <input type="text" class="form-control" name="cargo">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Função</label>
                        <input type="text" class="form-control" name="funcao">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Email</label>
                        <input type="text" class="form-control" name="email">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">EmailAlias</label>
                        <input type="text" class="form-control" name="emailAlias">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Email para recuperação de senha</label>
                        <input type="text" class="form-control" name="emailAlternativo">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Telefone Comercial</label>
                        <input type="text" class="form-control" name="telefoneComercial">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Telefone Celular</label>
                        <input type="text" class="form-control" name="telefoneCelular">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Telefone Residencial</label>
                        <input type="text" class="form-control" name="telefoneResidencial">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Vínculo principal?</label>
                        <input type="text" class="form-control" name="isPrincipal">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Sexo</label>
                        <input type="text" class="form-control" name="sexo">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Data de Nascimento</label>
                        <input type="text" class="form-control" name="dataNascimento">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Foto (em base64)</label>
                        <input type="text" class="form-control" name="foto">
                        <img id="img_foto" style="display:none;">
                    </div>

                    <input type="hidden" class="form-control" name="createdOnLogin">
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="$('#formEditar').submit()">Salvar</button>
            </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>

<script type="text/javascript">
    function editar(id)
    {
        $("#formEditar").attr('action', "<?= $this->url->getUrlController() ?>/update/"+id);

        $.post("<?= $this->url->getUrlController() ?>/getById/"+id, function(response){
            if(response.result == 'success') {
                $("#formEditar input").val('');

                $.each(response.user, function(field, value) {
                    $("#formEditar input[name='"+field+"']").val(value);
                    if(field == 'foto' && value){
                        $('#img_foto').attr('src', 'data:image/png;base64,'+value);
                        $('#img_foto').show();
                    }
                });

                $("#modalFormEditar").modal();
            } else {
                alert(response.msg);
            }
        });
    }

    function remover(ele, id)
    {
        $.post("<?= $this->url->getUrlController() ?>/remove/"+id, function(response){
            if(response.result == 'success') {
               alert(response.msg);
               $(ele).parent().parent().remove();
            } else {
                alert(response.msg);
            }
        });
    }

    function listarUsuarios()
    {
        table = $("#listaDeUsuarios").dataTable({
            "pageLength" : 50,
            "ajax" : "<?= $this->url->getUrlController() ?>/getAll",
            "columns" : [
                { "data" : "id" },
                { "data" : "nome" },
                { "data" : "cpf" },
                { "data" : "vinculo" },
                { "data" : "setor" },
                { "data" : "id", render : function(id)
                    {
                        return '[ <a href="#" onclick="editar(\''+id+'\')">Ver / Alterar</a> ] [ <a href="#" onclick="remover(this, \''+id+'\')">Excluir</a> ]';
                    }
                }
            ]
        });
    }

    $(document).ready(function() {
        listarUsuarios();

        $("#formEditar").submit(function(e) {
            e.preventDefault();

            var formData = $("#formEditar").serializeArray(),
                data = {};

            $.each(formData, function(i, _data) {
                data[_data.name] = _data.value;
            });

            $.post($(this).attr('action'), { "dados" : data }, function(response){
                if(response.result == 'success') {
                    document.location = "<?= $this->url->getUrlAction() ?>";
                } else {
                    alert(response.msg);
                }
            });
        });
    });

    
</script>