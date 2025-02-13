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
    <div id="listContainer">
        <br />
        <p id="msg">&nbsp;</p>
        <div class="page-heading">
            <div class="row">
                <div class="col-sm-6 col-xs-6">
                    <h3>Consulta de Tarefas Agendadas</h3>
                </div>
                <div class="col-sm-6 col-xs-6">
                    <button type="button" onclick="newTarefa()" class="btn btn-outline btn-primary pull-right" style="margin-top: 16px">Novo</button>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <table id="simple-record-table" class="table table-striped table-hover table-bordered tabela-servicos dt-responsive">
                <thead>
                    <tr>
                        <th class="col-sm-1" width="5%">Id</th>
                        <th class="col-sm-1" width="20%">Nome</th>
                        <th class="col-sm-4" width="15%">Endereço</th>
                        <th class="col-sm-4" width="8%">Período</th>
                        <th class="col-sm-4" width="8%">Situação</th>
                        <th class="col-sm-1" width="12%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    //Redireciona para criação de uma nova tarefa
    function newTarefa(){
        window.location.href = udev.url.getUrlController()+'/form';
    }

    //Redireciona para alteração de uma nova tarefa
    function editTarefa(id){
        window.location.href = udev.url.getUrlController()+'/form/'+id;
    }

    //Remove uma tarefa
    function removeTarefa(id, nome) {
        if (confirm("Tem certeza que deseja remover a tarefa " + id + " - '" + nome + "'?")) {
            $.get("<?= $this->url->getUrlController() ?>/remove/" + id, function(response) {
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

    //Remove uma tarefa
    function changeStatusTarefa(id, nome, situacao) {
        var situacao = (situacao) ? 'Desativar' : 'Ativar';
        if (confirm("Tem certeza que deseja "+situacao+" a tarefa " + id + " - '" + nome + "'?")) {
            $.get("<?= $this->url->getUrlController() ?>/changeStatus/" + id, function(response) {
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

    var recordTable;
    var recordList = [];

    $(document).ready(function() {
        recordTable = $("#simple-record-table").DataTable({
            searching: true,
            ajax: {
                "processing" : true,
                "format"     : "jsonp",
                "url"        : "<?= $this->url->getUrlController() ?>/getAll/",
                "dataFilter" : function(json) {
                    var json   = $.parseJSON(json);
                    json.data  = $.map(json.registros, function(e){
                        return e
                    });
                    recordList = json.registros;
                    return JSON.stringify(json);
                }
            },
            columnDefs: [
                {
                    targets: '_all',
                    className: 'text-center'
                }
            ],
            columns: [
                {"data": "id"},
                {"data": "nome"},
                {"data": "app",
                 render: function(data, type, row, meta) {
                     return row.app + "/" + row.controller + "/" + row.action + "/" + row.parametros;
                 }
                },
                {"data": "minuto", 
                  render: function(data, type, row, meta) {
                      return '<p title="Minuto - Hora - Dia do Mês - Mês - Dia da Semana">'+ row.minuto + " " + row.hora + " " + row.diaMes + " " + row.mes + " " + row.diaSemana + '</p>';
                 }
                },
                {"data": "situacao",
                 render: function(data, type, row, meta) {
                     return (row.situacao) ? 'Ativo' : 'Desativo';
                 }
                },
                {
                    sortable: false,
                    defaultContent: "",
                    createdCell: function (td, cellData, rowData, row, col) {
                        const html = '<div>' + 
                                       '<span title="Ativar/Desativar" class="btn btn-sm btn-primary glyphicon glyphicon-ok-circle" aria-hidden="true" onclick="changeStatusTarefa(' + rowData.id + ',\'' + rowData.nome + '\',' + rowData.situacao +')"></span> ' +
                                       '<span title="Editar" class="btn btn-sm btn-warning glyphicon glyphicon-edit" aria-hidden="true" onclick="editTarefa(' + rowData.id + ')"></span> ' +
                                       '<span title="Excluir" class="btn btn-sm btn-danger glyphicon  glyphicon-trash" aria-hidden="true" onclick="removeTarefa(' + rowData.id + ',\'' + rowData.nome + '\')"></span>' +
                                     '</div>' ;
                        $(td).append(html);
                    }
                }
            ]
        });
    });
</script>