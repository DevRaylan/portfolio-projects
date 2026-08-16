jQuery(document).ready(function($){
    // mascara de cpf
    if($('#inputCpf').length) $("#inputCpf").mask("999.999.999-99");

    // botão de fixar o menu
    $('a#fixa-menu').on('click', function(event) {

        $.get('/Geral/Index/fixaMenu/', function(response) {
            if(response.result == 'success'){
                $('.load').hide();
                if(response.fixaMenu == true){
                    $('a#fixa-menu span').addClass('active');
                }else{
                    $('a#fixa-menu span').removeClass('active');
                    $("#wrapper").toggleClass("toggled");
                    $("#page-content-wrapper").toggleClass("toggled");
                }
            }
        });
        
        return false;
    });
});

$.fn.bootstrapValidator.validators.cpf = {
    /**
     * Placeholder validator that can be used to display a custom validation message
     * returned from the server
     * Example:
     *
     * (1) a "blank" validator is applied to an input field.
     * (2) data is entered via the UI that is unable to be validated client-side.
     * (3) server returns a 400 with JSON data that contains the field that failed
     *     validation and an associated message.
     * (4) ajax 400 call handler does the following:
     *
     *      bv.updateMessage(field, 'blank', errorMessage);
     *      bv.updateStatus(field, 'INVALID');
     *
     * @see https://github.com/nghuuphuoc/bootstrapvalidator/issues/542
     * @see https://github.com/nghuuphuoc/bootstrapvalidator/pull/666
     * @param {BootstrapValidator} validator The validator plugin instance
     * @param {jQuery} $field Field element
     * @param {Object} options Can consist of the following keys:
     * - message: The invalid message
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        return TestarCPF($field.val().replace(/[\.\-]/ig,''));
    }
};

function TestarCPF(strCPF) {
    var Soma;
    var Resto;
    Soma = 0;   
    //strCPF  = RetiraCaracteresInvalidos(strCPF,11);
    if (strCPF == "00000000000")
    return false;
    for (i=1; i<=9; i++)
    Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i); 
    Resto = (Soma * 10) % 11;
    if ((Resto == 10) || (Resto == 11)) 
    Resto = 0;
    if (Resto != parseInt(strCPF.substring(9, 10)) )
    return false;
    Soma = 0;
    for (i = 1; i <= 10; i++)
       Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
    Resto = (Soma * 10) % 11;
    if ((Resto == 10) || (Resto == 11)) 
    Resto = 0;
    if (Resto != parseInt(strCPF.substring(10, 11) ) )
        return false;
    return true;
}


/**
* @fn iniInputFiles
* 
* Inicializa campo do tipo file para usar o plugin jquery file upload
* 
**/
function iniInputFile(inFile) {
    var label = $(inFile).parent(),
        inputFileHtml = $(inFile).clone();

    $(inFile).fileupload({
        url: urlUpload,
        dataType: 'json',
        singleFileUploads: true,
        autoUpload: true,
        replaceFileInput: false,
        paramName: 'inFile',
        add: function (e, data) {
            /* Implementar verificaÃ§Ã£o do tamanho do arquivo antes de realizar o upload
            if(data.originalFiles[0]['size'] > 50) {
                alert('Arquivo com tamanho maior que o permitido para envio.');
                return;
            };
            */

            var html = '<div class="progress"><div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div></div>';
            
            $(label).after(html);
            $(inFile).hide();
            
            $('<button type="button" class="btn btn-xs btn-danger glyphicon glyphicon-remove cancel" title="Cancelar envio"></button>').insertAfter($(label).next()).click(function () {
                    data.abort();
                    $(label).next().remove();
                    $(label).next().remove();
                    $(inFile).show();
            });

            data.submit();
        },
        done: function (e, data) {
            var response = data.result;

            if(response.result == 'success') {
                var inputText = $(inFile).clone().attr("value", response.file.name).attr("type", "text").prop("readonly", true).insertAfter($(inFile)).show();
                $(inFile).remove();
            }
            else {
                alert(response.msg+"\n- "+response.detail);
                $(inFile).val('').show();
            }

            $(label).next().remove(); // Progress
            $(label).next().remove(); // BotÃ£o Cancelar
            
            $('<button type="button" class="btn btn-xs btn-danger delete">Deletar</button>').insertAfter($(inputText)).click(function () {
                $.post('<?= $this->url->getUrlController() ?>/removeUpload/'+response.file.name, {}, function(data) {
                    $(inputText).next().remove();
                    $(inputText).remove();
                    $(label).append(inputFileHtml);
                    
                    iniInputFile(inputFileHtml);
                });
            });
        },
        error: (xhr) => {
            // se for erro relacionado Ã  aÃ§Ã£o do usuÃ¡rio para abortar o upload, entÃ£o nÃ£o faz nada.
            if (xhr.statusText == 'abort') {
                return;
            }

            // Erro de no processamento da requisiÃ§Ã£o pelo backend (Exemplo: url nÃ£o existe e redirecionada para 
            // a pÃ¡gina de erro padrÃ£o do framework).
            alert('Erro ao enviar arquivo para o servidor.');

            $(label).next().remove(); // Progress
            $(label).next().remove(); // BotÃ£o Cancelar
            $(inFile).val('').show();
            
            //var fileName = $(label).append(inputFileHtml);
            //iniInputFile(fileName);
        },
        progress: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $(label).next().find(".progress-bar").css(
                'width',
                progress + '%'
            );
        }
    });
}

function iniChart(config)
{
    Highcharts.chart(config.idGrafico, {
        data: {
            table: config.table
        },
        chart: {
            type: config.type
        },
        title: {
            text: config.title
        },
        yaxis: {
            allowdecimals: false,
            title: {
                text: 'units'
            }
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.series.name + '</b><br/>' +
                    this.point.y + ' ' + this.point.name.toLowerCase();
            }
        }
    });
}

function createConfig(table)
{
    return {
        type : "column",
        title : $(table).find(' > caption').text(),
        table : $(table).attr('id'),
        idGrafico : 'grafico_1'
    };
}

/**
* @function formatReais
* Retorna valor formatado em notação monetária BRL
* Acrescenta vírgula para centavos e ponto para milhar
* @param {String} x Valor sem formatação(sem . e ,)
* @returns {String} Valor formatado
**/
function formatReais(x) {
    var comprimento = x.length,
        reais = x.slice(0, comprimento -2),
        centavos = x.slice(-2),
        s = '', 
        i, j, 
        milhar = 3;
        
    if (comprimento < 3){
        reais = '0';
        centavos = ('00' + centavos).slice(-2);
    }
    i = reais.length;
    while (i > milhar) {
        j = i - milhar;
        s = '.' + reais.slice(j, i) + s;
        i = j;
    }
    s = reais.slice(0, i) + s;
    reais = s;
    return reais+','+centavos;
}

// Definição de idioma do DataTables
$.extend( true, $.fn.dataTable.defaults, {
    "language": {
        "url": '/libs/Geral/datatables/lang/Portuguese-Brasil.json'
    }
});
