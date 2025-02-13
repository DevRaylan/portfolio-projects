<div class="container">

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">

            <div class="card card-profile">

                <div class="card-background">
                    <div class="text-center">
                        <img src="/imagens/<?= $this->url->getNameModule() ?>/logo_idudesc.jpg" />
                    </div>
                </div>

                <div class="row text-center">
                    <div class="card-thumb">
                        <?php if (!$meusDados['foto']) { ?>
                            <i class="fas fa-user"></i>
                        <?php } else { ?>
                            <img src="data:image/jpeg;base64,<?= $meusDados['foto']; ?>" class="img-rounded" />
                        <?php } ?>
                    </div>
                </div>

                <div class="card-header">
                    <a href="https://id.udesc.br/IdUdesc/Index/form" target="_blank" class="btn btn-sm btn-success">Editar perfil no idUdesc</a>
                </div>

                <div class="card-body">
                    <div class="text-left">
                        <p><strong>Nome: </strong><?= $meusDados['nome']; ?></p>
                        <p><strong>CPF: </strong><?= $meusDados['cpf']; ?></p>
                        <p><strong>Data de nascimento: </strong><?= $meusDados['dataNascimento']; ?></p>
                        <p><strong>Centro: </strong><?= $meusDados['unidade']; ?></p>
                        <p><strong><?= $meusDados['tipo']=='Aluno'?'Curso':'Setor' ?>: </strong><?= $meusDados['setor']; ?></p>
                        <p><strong>Vínculo: </strong><?= $meusDados['vinculo']; ?></p>
                        <p><strong>Função: </strong><?= $meusDados['funcao']; ?></p>
                        <p><strong>Escolaridade: </strong><?= $meusDados['escolaridade']; ?></p>
                        <p><strong>E-mail UDESC: </strong><?= $meusDados['email'] ?></p>
                        <p><strong>E-mail Alternativo: </strong><?= $meusDados['emailAlias'] ?></p>
                        <p><strong>E-mail para recuperação de senha: </strong><?= $meusDados['emailRecuperaSenha']; ?></p>
                        <p><strong>Celular: </strong><?= $meusDados['telefoneCelular']; ?></p>
                        <p><strong>Telefone residencial: </strong><?= $meusDados['telefoneResidencial']; ?></p>
                        <p><strong>Telefone comercial: </strong><?= $meusDados['telefoneComercial']; ?></p>
                    </div>
                </div>

            </div>

        </div>
        <div class="col-md-6"></div>
    </div>
</div>
