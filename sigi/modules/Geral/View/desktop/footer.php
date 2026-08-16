        <div id="loading-modal"></div>

        <div id="modalSelVinculo" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Seleção do vínculo</h4>
                </div>
                <div class="modal-body">
                    <div class="aler">
                        <p>Olá <strong><?= $_SESSION['usuario']['nome'] ?></strong>.</p>
                        <p>Você possui mais de um vínculo com a UDESC.</p>
                        <p>Por favor, selecione qual vínculo você deseja
                        utilizar para acessar o sistema.</p>
                    </div>

                    <div id="selVinculo" class="list-group">
                        <?php foreach($_SESSION['usuario']['vinculos'] as $vinculo) { ?>
                            <a href="#" class="list-group-item <?= $_SESSION['usuario']['vinculo'] == $vinculo['vinculo'] ? 'active' : '' ?>">
                                <h4 class="list-group-item-heading"><?= $vinculo['vinculo'] ?></h4>
                                <p class="list-group-item-text">
                                    <?= $vinculo['cargo'] ?><br />
                                    <?= $vinculo['funcao'] ?>
                                </p>
                            </a>
                        <?php } ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" onclick="document.location = '<?= $this->url->getFullUrl() ?>'">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="mudarVinculo($('#selVinculo .active h4').text())">Selecionar</button>
                </div>
                    
                </div>
            </div>
        </div>

    </div> <!-- Fecha a div "<div id="page-content-wrapper">" do header.php -->
    <script>
    $("#selVinculo .list-group-item").click(function() {$("#selVinculo .list-group-item").removeClass('active');$(this).toggleClass('active')});
    //<?= isset($_SESSION['usuario']['logado']) && empty($_SESSION['usuario']['vinculo']) ? '$("#modalSelVinculo").modal("show");' : '' ?>

    function mudarVinculo(vinculo) {
        $.get("/Geral/Index/mudarVinculo/"+vinculo, function(response) {
            if(response.result == 'success') {
                document.location = "<?= $this->url->getFullUrl() ?>";
            } else {
                alert(response.msg);
            }
        });
    }
    </script>

    <?php 
    if ($this->carregaLibs) {
    ?>    
        <script type="text/javascript" src="/libs/Geral/jquery-mask/jquery.mask.js"></script>
        <script type="text/javascript" src="/libs/Geral/bootstrap-validator/js/bootstrapValidator.min.js"></script>
        <script type="text/javascript" src="/libs/Geral/bootstrap-validator/js/languages/pt_BR.js"></script>
        <script type="text/javascript" src="/libs/Geral/jquery_file_upload/js/vendor/jquery.ui.widget.js"></script>
        <script type="text/javascript" src="/libs/Geral/datatables/js/pdfmake.min.js"></script>
        <script type="text/javascript" src="/libs/Geral/datatables/js/vfs_fonts.js"></script>
        <script type="text/javascript" src="/libs/Geral/datatables/js/jquery.dataTables.js"></script>
        <script type="text/javascript" src="/libs/Geral/datatables/js/dataTables.bootstrap.js"></script>
        <script type="text/javascript" src="/libs/Geral/datatables/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" src="/libs/Geral/datatables/js/buttons.bootstrap.min.js"></script>
        <script type="text/javascript" src="/libs/Geral/datatables/js/buttons.html5.min.js"></script>
        <script type="text/javascript" src="/libs/Geral/datatables/js/dataTables.responsive.min.js"></script>
        <script type="text/javascript" src="/libs/Geral/datatables/js/responsive.bootstrap.min.js"></script>
        <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
        <script type="text/javascript" src="/libs/Geral/jquery_file_upload/js/jquery.iframe-transport.js"></script>
        <!-- The basic File Upload plugin -->
        <script type="text/javascript" src="/libs/Geral/jquery_file_upload/js/jquery.fileupload.js"></script>
        <script type="text/javascript" src="/libs/Geral/highcharts/highcharts.js"></script>
        <script type="text/javascript" src="/libs/Geral/highcharts/data.js"></script>
        <script type="text/javascript" src="/libs/Geral/highcharts/exporting.js"></script>
        <script type="text/javascript" src="/libs/Geral/bootstrap-select/js/bootstrap-select.min.js"></script>
        <script type="text/javascript" src="/libs/Geral/bootstrap-select/js/i18n/defaults-pt_BR.min.js"></script>
        <script type="text/javascript" src="/libs/Geral/moment/js/moment.js"></script>
        <script type="text/javascript" src="/libs/Geral/moment/js/pt-br.js"></script>
        <script type="text/javascript" src="/libs/Geral/bootstrap-datepicker/js/bootstrap-datetimepicker.min.js"></script>
        <script type="text/javascript" src="/libs/Geral/jquery-jcrop/jquery.Jcrop.js"></script>
        
        <!-- Carrega a função do moment para o DataTable -->
        <script type="text/javascript">
        $.fn.dataTable.moment = function ( format, locale ) {
            var types = $.fn.dataTable.ext.type;
        
            // Add type detection
            types.detect.unshift( function ( d ) {
                return moment( d, format, locale, true ).isValid() ?
                    'moment-'+format :
                    null;
            } );
        
            // Add sorting method - use an integer for the sorting
            types.order[ 'moment-'+format+'-pre' ] = function ( d ) {
                return moment( d, format, locale, true ).unix();
            };
        };        
        </script>
    <?php 
    }
    ?>
    <script type="text/javascript" src="/libs/Geral/geral/sistema.js"></script>
     
    <?php
        if ($this->jsFilesFooter) {
            foreach ($this->jsFilesFooter as $jsFile) {
        ?>
        <script type="text/javascript" src="<?php echo DIR_PUBLIC_LIBS.'/'.$this->url->getNameModule().'/'.$jsFile ?>"></script>
        <?php
            }
        }
    ?>

    <script>
    
    var urlUpload = "<?= $this->url->getUrlController() ?>/upload";
    
    jQuery(document).ready(function(){
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            $("#page-content-wrapper").toggleClass("toggled");
        });

        // inicializa os inputs cuja classe é "inputFile"
        $(".inputFile").each(function(i) {
            iniInputFile(this);
        });
    });
    </script>
</body>
</html>
