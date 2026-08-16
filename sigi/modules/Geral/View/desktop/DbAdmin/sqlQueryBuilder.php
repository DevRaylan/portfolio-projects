<style>
#tables li {
    list-style: none;
    margin-left: 0px;
    padding-left: 0px;
}

#tables {
    padding-left: 0px;
}

#tables ul {
    padding-left: 10px;
}
</style>

<div class="container">
    <h2>Consulta SQL</h2>
    <br/>
    <p>Este recurso permite o acesso direto aos dados das tabelas do Banco de Dados da aplicação e a criação/edição/execução de consultas SQLs de leitura.<br />
    Pode ser utilizado o caracter <strong>#</strong> no início de linhas para comentários no código.</p>
    <p>Formato: nome_tabela <span class="badge">Aplicação</span></p>
    <p id="applicationsList">Aplicações: </p>

    <div class="row">
        <div class="col-sm-12">
            <div class="col-sm-3">
                <h5>Tabelas do Banco de Dados</h5>

                <!-- Lista de tabelas (li) -->
                <ul id="tables" style="height: 321px; overflow: auto">

                    <?php foreach($tables as &$table) { ?>

                    <!-- A tag "class" é uilizada para ocultar/mostrar tabelas de uma aplicação. -->
                    <li class="<?= $table['application'] ?>">
                        <!-- Ao clicar, reflete nas colunas seu estado (checked=true/false) -->
                        <input type="checkbox" onclick="$(this).parent().find('ul input[name=\'chkCampo\']').prop('checked', $(this).prop('checked')); selTables()" value="<?= $table['name'] ?>" />
                        <span onclick="$(this).next().toggle()" title="<?= $table['name'] ?>"><?= (strlen($table['name']) > 20 ? substr($table['name'], 0, 20).'...' : $table['name']).' <span class="badge" title="'.$table['application'].'">'.(strlen($table['application']) > 7 ? substr($table['application'],0,7).'.' : $table['application']).'</span>' ?></span>
                        
                        <!-- Lista de colunas, oculta por padrão -->
                        <ul id="colunas" style="display: none">

                            <?php foreach($table['columns'] as $column => &$definition) {
                                $title = '';
                                
                                // Monta o texto com metadados do campo para ser utilizado no respectivo title
                                foreach($definition as $atribute => &$val) {
                                    $title .= $atribute.' : '.$val."\n";
                                }; unset($val);
                            ?>
                            <!-- Coluna da tabela -->
                            <li><label title="<?= $title ?>"><input type="checkbox" name="chkCampo" value="<?= $definition['columnName'] ?>" onclick="selTables()" /> <?= $definition['columnName'] ?></label></li>
                            
                            <?php }; unset($definition) ?>

                            <?php foreach($table['associations'] as &$definition) {
                                $tableName = $entityToTableName[$definition['table']];
                            ?>

                            <!-- Coluna da tabela com relação externa -->
                            <li><label><input type="checkbox" name="chkCampo" value="<?= $definition['from'] ?>" onclick="selTables()" /> <?= $definition['from'] ?></label></li>
                            
                            <?php foreach($tables[$tableName]['columns'] as $column) { ?>

                            <!-- Coluna da tabela externa para seleção após JOIN, possui a estrutura <coluna para join>:<tabela externa para join>:<coluna externa_join>:<coluna externa> -->
                            <li>- <label><input type="checkbox" name="chkCampo" value="<?= $definition['from'].':'.$tableName.':'.$definition['to'].":".$column['columnName'] ?>" onclick="selTables()" /> <?= $column['columnName'] ?></label></li>

                            <?php } }; unset($definition) ?>

                        </ul>
                    </li>

                    <?php }; unset($table) ?>

                </ul>

                <button type="button" class="btn btn-warning btn-md" onclick="createSql()">Gerar SQL</button>
                <div class="help-block">* Todo o conteúdo da aba selecionada será substituído.</div>
            </div>

            <div class="col-sm-9">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#tabSql1" aria-controls="home" role="tab" data-toggle="tab">SQL 1</a></li>
                    <li role="presentation"><a href="#tabSql2" aria-controls="profile" role="tab" data-toggle="tab">SQL 2</a></li>
                    <li role="presentation"><a href="#tabSql3" aria-controls="messages" role="tab" data-toggle="tab">SQL 3</a></li>
                    <li role="presentation"><a href="#tabSql4" aria-controls="messages" role="tab" data-toggle="tab">SQL 4</a></li>
                    <li role="presentation"><a href="#tabSql5" aria-controls="messages" role="tab" data-toggle="tab">SQL 5</a></li>
                </ul>

                <form class="form-horizontal" method="post" id="form-importar">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabSql1">
                            <textarea rows="15" style="width: 100%" class="sqlCommand"></textarea>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tabSql2">
                            <textarea rows="15" style="width: 100%" class="sqlCommand"></textarea>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tabSql3">
                            <textarea rows="15" style="width: 100%" class="sqlCommand"></textarea>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tabSql4">
                            <textarea rows="15" style="width: 100%" class="sqlCommand"></textarea>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tabSql5">
                            <textarea rows="15" style="width: 100%" class="sqlCommand"></textarea>
                        </div>
                    </div>

                    <div style="margin-top: 5px">
                        <button type="submit" class="btn btn-primary btn-md">Executar</button>
                        <button type="button" class="btn btn-success btn-md" onclick="saveTabs()" title="Salva o conteúdo da abas para próximos acessos">Salvar abas</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <hr />

    <div class="row">
        <!-- Tabelas com os regitros das consultas. É criada uma tabela por consulta. -->
        <div id="output"></div>
    </div>
</div>

<script>
    // Tabelas para geração do SQL.
    var tables = {};

    // Mapear o namespaca da entidade para o nome da tabela no BD.
    var entityToTableName = <?= json_encode($entityToTableName) ?>;

    // Aplicações do sistema
    var applications = <?= json_encode(array_keys($applications)) ?>;

    // Passui as abas com SQLs salvos pelo usuário.
    var sqlTabs = <?= json_encode($sqlTabs) ?>;

    /**
     * Faz o tratamento das ações na lista de tabelas e colunas.
     * Reflete nos checkbox das colunas o esta dos checkbox das tabelas (checked=true/false)
     */
    function selTables()
    {
        // Zera a variável global;
        tables = {};

        // Para cada item da tabela (li) visível. Não ocultado pelo checkbox da lista de aplicações.
        $("#tables > li:visible > input").each(function(i, liTabela) {
            var colunas = [];
                
            // Para cada coluna selecionada.
            $(liTabela).parent().find("#colunas input[name='chkCampo']:checked").each(function(j, liColuna) {
                // Adiciona na lista de colunas da tabela.
                colunas.push($(liColuna).val());
            });

            // Se existir colunas para gerar SQL, salva na variável global tabelas.
            if(colunas.length) {
                tables[$(liTabela).val()] = colunas;
                // se tem pelo menos uma coluna selecionada, então seta a tabela como selecionada (checkbox)
                $(liTabela).prop('checked', true);
            } else {
                // Sem colunas, então a tabela é deselecionada.
                $(liTabela).prop('checked', false);
            }
        });

        console.log('Tabelas: ',tables);
    }

    /**
     * Com base na variável tabelas, gera as SQLs.
     */
    function createSql()
    {
        var sqls = [];
        // Utilizado para criar alias da tabela a fim de tornar as SQLs menores. Exemplo: Aluno as t1.
        var i = 0;

        // Faz uma cópia da variável global tabelas, já que os valores são modificados tendo que manter os originais.
        var tablesCopy = $.extend(true, {}, tables);

        $("#sqlCommand").val('');

        $.each(tablesCopy, function(tableName, fields) {
            var tablePre = 't'+i,
                // Utilizado para criar o prefixo das tabelas de JOIN. Exemplo: '... LEFT JOIN Aluno as jt ...'.
                j = 0,
                // Armazena as SQLs de JOIN para posterior junção no SELECT.
                joins = [],
                // Controla a geração das SQLs JOIN, evitando repetir esta parte após a primeira colunas externa processada.
                joinCreated = {};
                
            $.each(fields, function(key, field) {
                // Se existir ":" então é uma coluna de tabela externa.
                if(field.indexOf(':') > -1) {
                    // Converte o valor em array com as partes: 0 => coluna join, 1 => tabela externa, 2 => Coluna externa join, 3 => coluna desejada.
                    field = field.split(':');
                    
                    // Se ainda não existe a SQL de JOIN criada, então faz a geração.
                    if(!joinCreated[field[1]]) {
                        j++;
                        // Cria o JOIN
                        joins.push(' INNER JOIN '+field[1]+' as jt'+j+' ON jt'+j+'.'+field[2]+'='+tablePre+'.'+field[0]);

                        // Não repete a criação para as próximas colunas da tabela externa.
                        joinCreated[field[1]] = true;
                    }

                    // Cria a parte das colunas da tabela externa do SELECT <colunas> FROM.
                    field = 'jt'+j+'.`'+field[3]+'` as `jt'+j+'.'+field[3]+'`';
                } else {
                    // Cria a parte das colunas da tabela no SELECT <colunas> FROM.
                    field = tablePre+'.`'+field+'`';
                }

                // Adiciona o nome da coluna na lista de colunas para seleção.
                fields[key] = field;
            });

            // Cria a SQL de SELECT e insere as colunas de seleção.
            sql = 'SELECT '+fields.join(',')+"\n"+'FROM `'+tableName+'` as '+tablePre;

            // Se existe JOIN, então adiciona na construção da SQL.
            if(joins.length) {
                sql += "\n    "+joins.join("\n    ");
            }

            // Desativado por enquanto. A intenção é entregar uma estrutura mais completa do SELECT já com WHERE.
            //sql += "\n"+'WHERE 1=1';

            // Salva o SQL da tabela na lista de SQLs para envio e processamento no backend.
            sqls.push(sql);
            i++;
        });
        
        // Se existir SQLs geradas, então as coloca no textares para edição.
        if(sqls.length) $(".sqlCommand:visible").val(sqls.join(";\n\n")+';');
    }

    // Mostra/Oculta tabela das aplicações na lista de tabelas.
    function loadApplicationsList()
    {
        var apps = [];

        $.each(applications, function(i, app) {
            apps.push('<label><input type="checkbox" checked="checked" onclick="$(\'.'+app+'\').toggle();selTables()" /> '+app+'</label>');
        });

        $("#applicationsList").append(apps.join('&nbsp;&nbsp;&nbsp;'));
    }

    loadApplicationsList();

</script>

<!--------------------- JS ----------------------->

<script>

    function sqlCommandImport() {
        // Máximo de letras para mostrar a SQL na tabela.
        var qtdMaxCharsInSql = 150;
        var sqlCommandsPreFilter = $(".sqlCommand:visible").val().split("\n");
        
        var j = 0;
        // Comandos SQL pós filtro.
        var sqlCommandsPosFilter = [];

        $.each(sqlCommandsPreFilter, function(i, sqlCommand) {
            // Ignora linhas de comentários (começa com #).
            if(sqlCommand.indexOf('#') === 0) return true;
            
            // Cada linha em branco termina uma consulta SQL.
            if(!sqlCommand) {
                j++;
                return true;
            }

            // Cria o elemento do array se não existe.
            if(!sqlCommandsPosFilter[j]) sqlCommandsPosFilter[j] = '';

            // Salva o comando SQL.
            sqlCommandsPosFilter[j] += sqlCommand+' ';
        });
        
        $("#output").empty('');

        // Faz um Ajax por comando SELECT.
        $.each(sqlCommandsPosFilter, function(iTable, sqlCommand) {
            if(!sqlCommand) return true;

            var jsonData = {
                'comandosSql': sqlCommand
            };

            // Faz o recorte da SQL caso tenha mais letras que o permitido na visualização da mesma no cabeçalho da tabela.
            if(sqlCommand.length > qtdMaxCharsInSql) {
                var sqlText = sqlCommand.substr(0,(qtdMaxCharsInSql/2));
                var sqlText = sqlText + ' ... '+sqlCommand.slice(-(qtdMaxCharsInSql/2));
            } else {
                sqlText = sqlCommand;
            }
            
            // Gera a tabela com um ID para inserção dos regitros no retorno do Ajax.
            var table = '\
                <table id="output_'+iTable+'" class="table table-striped">\
                <caption><strong>'+sqlText+'</strong></caption>\
                    <thead>\
                    </thead>\
                    <tbody>\
                    </tbody>\
                </table>\
                <br />\
                <hr />\
                <br />';

            
            $("#output").append(table);

            $("#output_"+iTable+" thead").html('<tr><th>Mensagem</th></tr>');
            $("#output_"+iTable+" tbody").html('<tr><td><div class="alert alert-info text-center">Realizando consulta...</div></td></tr>');
            
            // Ajax para a SQL.
            $.post("<?= $this->url->getUrlController() ?>/executarComandosSQL", jsonData, function(response) {
                if (response.result == 'success') {
                    // Armazena as colunas convertidas em HTML Table TH.
                    var thead = '';

                    $("#output_"+iTable+" tbody").empty();

                    if(response.registros.length) {
                        $.each(response.registros, function(i, registro) {
                            // Armazena os registros em HTML Table TD.
                            var tbody = '';

                            $.each(registro, function(key, val) {
                                if(i === 0) {
                                    // Cria o TH na tabela para cada campo somente do primeiro registro.
                                    thead += '<th>'+key+'</th>';
                                }

                                // Cria o TD na tabela para cada campo do registro.
                                tbody += '<td>'+val+'</td>';
                            });

                            $("#output_"+iTable+" tbody").append('<tr>'+tbody+'</tr>');
                        });

                        $("#output_"+iTable+" thead").append('<tr>'+thead+'</tr>');

                        // Instancia datatable.
                        $("#output_"+iTable).DataTable({
                            dom: 'Bfrtip',
                            buttons: [
                                {
                                    extend: 'csv',
                                    text: 'CSV',
                                    fieldSeparator: ';' // with ';' we can export the file in csv and each column is in one column. Without ';' everything is in one column
                                },
                                'pdf'
                            ]
                        });
                    } else {
                        $("#output_"+iTable+" tbody").append('<tr><td>Nenhum registro encontrado.</td></tr>');
                    }
                } else {
                    $("#output_"+iTable+" thead").html('<tr><th>Erro na consulta</th></tr>');
                    $("#output_"+iTable+" tbody").html('<tr><td><div class="alert alert-danger text-center">'+response.msg+'</div></td></tr>');
                    //$("#output_"+iTable).DataTable();
                }
            });
        });
    }

    // Carrega o conteúdo salvo das abas.
    function loadSqlTabs()
    {
        console.log('sqlTabs', sqlTabs);

        $(".sqlCommand").each(function(i, command) {
            if(sqlTabs[i]) {
                $(command).val(sqlTabs[i]);
            }
        });
    }

    // Salva conteúdo das abas nas preferências do usuário.
    function saveTabs()
    {
        var tabs = [];

        $(".sqlCommand").each(function(i, command) {
            tabs.push($(command).val());
        });

        $.post(udev.url.getUrlController(true)+'/saveTabs',{"tabs": tabs}, function(response) {
            alert(response.msg);
        });
    }

    // Submit do form
    $("#form-importar").submit(function(e) {
        e.preventDefault();
        sqlCommandImport();
    });

    selTables();
    createSql();
    loadSqlTabs();
</script>