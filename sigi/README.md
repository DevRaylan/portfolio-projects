# SIGI — Sistema Integrado de Gestão da Informática

Projeto de formação do curso técnico, desenvolvido durante estágio no setor de TI (CEART) da UDESC. É um sistema de gestão patrimonial: controla solicitações e empréstimos de bens com e sem número de patrimônio entre setores.

## Stack

- PHP 7.4, framework MVC próprio (controllers/models/views por módulo, sem depender de Laravel/Symfony)
- Doctrine ORM para persistência
- Apache + Docker
- PHPMailer, PHPSpreadsheet, dompdf, Pusher (websocket) como bibliotecas de apoio

## Estrutura

- `modules/Geral`: framework base — login, sessão, usuários, autorização
- `modules/Sigcinf`: módulo de negócio — bens, categorias, setores, solicitações de empréstimo
- `config/`: configuração por ambiente (DEV/HOM/PROD)
- `libs/`: dependências PHP já vendoradas

## Rodando localmente

O ambiente de produção original autentica via LDAP institucional e não roda fora da rede da UDESC. Para demonstração local existe um modo de login simulado (`TIPO_LOGIN=True`), já suportado pelo framework e restrito ao ambiente DEV, com um banco SQLite criado na primeira execução — não depende de nenhum serviço externo.

```bash
docker compose -f docker-compose.dev.yml up --build -d
```

Acesse `http://localhost:8080`. Na primeira visita, o sistema redireciona para a tela de instalação (`/Geral/Install`), que sincroniza o schema do banco a partir das entidades Doctrine — basta informar um CPF válido (só a validação de dígito verificador é checada) para criar o usuário administrador. Depois disso, use o mesmo CPF na tela de login com qualquer senha.

Para parar: `docker compose -f docker-compose.dev.yml down`.

> `Dockerfile` é a imagem original de produção (Apache + PHP 7.4 + drivers de SQL Server, publicada via Jenkins). `Dockerfile.dev`/`docker-compose.dev.yml` são um ambiente leve criado só para rodar e demonstrar o projeto localmente.

## Status

Projeto acadêmico/de formação, sem manutenção ativa. Credenciais e endereços internos da infraestrutura original foram removidos do código e do histórico do repositório.
