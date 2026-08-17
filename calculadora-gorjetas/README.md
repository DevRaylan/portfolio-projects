# Calculadora de Gorjetas

Sistema para calcular gorjetas de atendentes (garçons/garçonetes), com gestão de
atendentes, mesas e percentuais de gorjeta, e histórico das gorjetas calculadas,
navegável por dia. Projeto de estudo, construído do zero para aprender Spring Boot,
Docker e PostgreSQL na prática.

Veja o levantamento completo de requisitos em [REQUISITOS.md](REQUISITOS.md).

## Tecnologias

| Camada         | Tecnologia                                | Por quê                                                           |
| -------------- | ------------------------------------------ | ------------------------------------------------------------------ |
| Backend        | Java 21 + Spring Boot 3.3.2                 | Framework mais usado no mercado para APIs Java                     |
| Build          | Maven                                       | Gerenciamento de dependências e build                              |
| Banco de dados | PostgreSQL 16                               | Banco relacional mais usado com Spring Boot                        |
| Persistência   | Spring Data JPA (Hibernate)                 | Mapeamento objeto-relacional, sem SQL manual                       |
| Infraestrutura | Docker / Docker Compose                     | Isola o banco de dados do ambiente local                           |
| Frontend       | HTML/CSS/JS (ES Modules, API REST + JSON)   | Consome a API separadamente do backend, sem framework/build step   |
| Design         | Material Design 3                           | Linguagem visual do frontend (cores, elevação, componentes)        |

## Arquitetura

```
Controller  →  Service  →  Repository  →  Banco (PostgreSQL)
(endpoints)   (regras de     (acesso a
              negócio)        dados)
```

- `model/` — entidades JPA (`Atendente`, `Mesa`, `Percentual`, `Gorjeta`, `TrocaAtendente`)
- `repository/` — interfaces Spring Data JPA
- `service/` — regras de negócio (cálculo da gorjeta, abertura/fechamento de mesa, etc.)
- `controller/` — endpoints REST

O frontend (`frontend/`) é HTML/CSS/JS puro, sem framework nem build step:
- `frontend/css/` — estilos divididos por responsabilidade (`base`, `forms`, `chips`, `modal`, `resultado`, `mesas`, `layout`)
- `frontend/js/` — lógica dividida em módulos ES (`import`/`export`), um por área (`atendentes`, `mesas`, `mesas-gerenciar`, `percentuais`, `historico`, `chips`, `api`), com `app.js` como ponto de entrada

## Como rodar

**Pré-requisitos:** JDK 21, Maven, Docker, Python (usado só pra servir o frontend estático).

1. Na primeira vez, dê permissão de execução ao script:
   ```bash
   chmod +x start.sh
   ```
2. Suba tudo (banco, backend e frontend):
   ```bash
   ./start.sh
   ```
3. A API fica disponível em http://localhost:8080 e o frontend em http://localhost:5500.
4. Para parar backend e frontend, aperte `Ctrl+C` no terminal onde rodou o `./start.sh` (o banco de dados continua rodando em segundo plano via Docker — pra derrubar ele também, use `docker compose down`).

## Endpoints da API

### Atendentes

| Método | Rota                          | Descrição                                      | Corpo (JSON)                |
| ------ | ------------------------------ | ------------------------------------------------ | ---------------------------- |
| POST   | `/atendentes`                   | Cadastra um atendente                            | `{ "nome": "Maria" }`        |
| GET    | `/atendentes`                   | Lista atendentes ativos                          | —                             |
| GET    | `/atendentes/todos`             | Lista todos os atendentes (ativos e inativos)    | —                             |
| PUT    | `/atendentes/{id}`              | Edita o nome de um atendente                     | `{ "nome": "Maria Silva" }`  |
| PATCH  | `/atendentes/{id}/desativar`    | Desativa um atendente                            | —                             |
| PATCH  | `/atendentes/{id}/reativar`     | Reativa um atendente                             | —                             |

### Mesas

| Método | Rota                            | Descrição                                                                    | Corpo (JSON)                                              |
| ------ | -------------------------------- | ------------------------------------------------------------------------------ | ------------------------------------------------------------ |
| POST   | `/mesas`                          | Cadastra uma mesa (número único)                                              | `{ "numero": 5 }`                                            |
| GET    | `/mesas`                          | Lista mesas ativas                                                            | —                                                              |
| GET    | `/mesas/todos`                    | Lista todas as mesas (ativas e inativas)                                      | —                                                              |
| PUT    | `/mesas/{id}`                     | Edita o número de uma mesa                                                    | `{ "numero": 6 }`                                             |
| PATCH  | `/mesas/{id}/desativar`           | Desativa uma mesa                                                             | —                                                              |
| PATCH  | `/mesas/{id}/reativar`            | Reativa uma mesa                                                              | —                                                              |
| PATCH  | `/mesas/{id}/abrir`               | Abre a mesa, associando um atendente                                          | `{ "atendenteId": 1 }`                                        |
| PATCH  | `/mesas/{id}/trocar-atendente`    | Troca o atendente de uma mesa ocupada (motivo obrigatório, vira histórico)     | `{ "novoAtendenteId": 2, "motivo": "Troca de turno" }`         |

### Percentuais

| Método | Rota                            | Descrição                                        | Corpo (JSON)          |
| ------ | --------------------------------- | --------------------------------------------------- | ----------------------- |
| POST   | `/percentuais`                     | Cadastra um percentual de gorjeta                  | `{ "valor": 10.00 }`    |
| GET    | `/percentuais`                     | Lista percentuais ativos                           | —                        |
| GET    | `/percentuais/todos`               | Lista todos os percentuais (ativos e inativos)     | —                        |
| PUT    | `/percentuais/{id}`                | Edita o valor de um percentual                     | `{ "valor": 12.00 }`     |
| PATCH  | `/percentuais/{id}/desativar`      | Desativa um percentual                             | —                        |
| PATCH  | `/percentuais/{id}/reativar`       | Reativa um percentual                              | —                        |

### Gorjetas

| Método | Rota                       | Descrição                                                          | Corpo (JSON)                                                                |
| ------ | --------------------------- | ---------------------------------------------------------------------- | ------------------------------------------------------------------------------ |
| POST   | `/gorjetas`                  | Calcula e salva uma gorjeta (fecha a mesa, que volta a ficar vazia)     | `{ "atendenteId": 1, "valorConta": 150.00, "percentual": 10, "mesaId": 1 }`     |
| GET    | `/gorjetas`                  | Lista todas as gorjetas calculadas                                     | —                                                                                |
| GET    | `/gorjetas/atendente/{id}`   | Histórico de gorjetas de um atendente específico                        | —                                                                                |

## Status do projeto

- [x] Backend (Spring Boot + PostgreSQL + API REST)
- [x] Interface web (HTML/CSS/JS, Material Design 3)
- [x] Gestão de atendentes (cadastrar, editar, desativar/reativar)
- [x] Gestão de mesas (cadastrar, editar, desativar/reativar, número único)
- [x] Gestão de percentuais de gorjeta (cadastrar, editar, desativar/reativar)
- [x] Fluxo de mesa: abrir (associar atendente) → trocar atendente (com motivo, registrado em histórico) → fechar (calcular gorjeta com percentual pré-cadastrado) → mesa volta a ficar vazia automaticamente
- [x] Grade de mesas na tela principal, com cores por estado (livre / em atendimento / indisponível)
- [x] Histórico de gorjetas navegável por dia, com data/hora de abertura e fechamento de cada mesa
