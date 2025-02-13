<div class="container">
    <h2>Diretórios</h2>
    <p>Lista de arquivos dos diretórios com escrita para o sistema</p>

    <ul class="root-dir">
    <?php foreach($dirs as $dir) { ?>
        <li class="dir"><a href="javascript: void(0)" onclick="listFiles($(this))"><?= $dir ?></a></li>
    <?php } ?>
    </ul>
</div>

<script>
function listFiles(itemA) {
    var data = {
        'dir' : ''
    };
    
    // Montar caminho do diretório para listagem dos arquivos
    $(itemA).parents("li.dir").each(function(i, dir) {
        data['dir'] = $(dir).find('>a').text()+'/'+data['dir'];
    });
    
    // Limpar listagem antiga
    $(itemA).parent().find('>ul').remove();
    
    // Pegar JSON do servidor
    $.getJSON("<?= $this->url->getUrlController() ?>/getFileList", data, function(response){
        var ul = $('<ul>');
        
        if(response.result == 'success') {
            $.each(response.files, function(fileName, isDir) {
                if(isDir) {
                    $(ul).append('<li class="dir"><a href="javascript: void(0)" onclick="listFiles($(this))">'+fileName+'</a></li>');
                } else {
                    $(ul).append('<li class="file"><a href="javascript: void(0)" onclick="downloadFile($(this))">'+fileName+'</a></li>');
                }
            });
        } else {
            $(ul).append('<li>Nenhum arquivo encontrado.</li>');
        }
        
        $(ul).appendTo($(itemA).parent());
    });
}

function downloadFile(itemA)
{
    var file = $(itemA).text(),
        url = "<?= $this->url->getUrlController() ?>/downloadFile/?file=";
    
    $(itemA).parents("li.dir").each(function(i, dir) {
        file = $(dir).find('>a').text()+'/'+file;
    });
    
    url += file;
    
    document.location = url;
}
</script>