<div class="container">
    <div class="row">

    
        <div class="col-lg-12">
            <h2>SIGI - Sistema Integrado de Gestão da Informática </h2> 
        </div>

        <div class="row">
            <div class="col-lg-12">
                <h2>Empréstimos</h2>
            </div>
            <div class="col col-xs-6 col-sm-4 col-md-2 col-lg-2 text-center">
                <a href="<?=$this->url->getUrlModule().'/Solicitacao/index';?>" class="btn-xs">
                    <div>
                        <div><i class="fa-4x glyphicon glyphicon-th-list"></i></div>
                        <div>Meus empréstimos</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <?php if($this->auth->isAllowedTransactions(['dev','admin','gerente'])) {?>
            <div class="col-lg-12">
                <h2>Solicitações</h2>
            </div>                
            <div class="col col-xs-6 col-sm-4 col-md-2 col-lg-2 text-center">
                <a href="<?=$this->url->getUrlModule().'/Solicitacao/incluir';?>" class="btn-xs">
                    <div>
                        <div><i class="fa-4x glyphicon glyphicon-plus-sign"></i></div>
                        <div>Cadastrar</div>
                    </div>
                </a>
            </div>
            <div class="col col-xs-6 col-sm-4 col-md-2 col-lg-2 text-center">
                <a href="<?=$this->url->getUrlModule().'/Solicitacao/moderar';?>" class="btn-xs">
                    <div>
                        <div><i class="fa-4x glyphicon glyphicon-pencil"></i></div>
                        <div>Moderar</div>
                    </div>
                </a>
            </div>
            <?php } ?>
        </div>

        <div class="row">
        </div>
    </div>
</div>

