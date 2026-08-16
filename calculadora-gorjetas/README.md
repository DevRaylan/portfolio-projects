# Calculadora de Gorjetas

Sistema para calcular gorjetas de atendentes (garçons/garçonetes), com cadastro
de atendentes e histórico das gorjetas calculadas. Projeto de estudo, construído
do zero para aprender Spring Boot, Docker e PostgreSQL na prática.

Veja o levantamento completo de requisitos em [REQUISITOS.md](REQUISITOS.md).

## Tecnologias

| Camada         | Tecnologia                    | Por quê                                        |
| -------------- | ----------------------------- | ---------------------------------------------- |
| Backend        | Java 21 + Spring Boot 3.3.2   | Framework mais usado no mercado para APIs Java |
| Build          | Maven                         | Gerenciamento de dependências e build          |
| Banco de dados | PostgreSQL 16                 | Banco relacional mais usado com Spring Boot    |
| Persistência   | Spring Data JPA (Hibernate)   | Mapeamento objeto-relacional, sem SQL manual   |
| Infraestrutura | Docker / Docker Compose       | Isola o banco de dados do ambiente local       |
| Frontend       | HTML/CSS/JS (API REST + JSON) | Consome a API separadamente do backend         |

## Arquitetura

```
Controller  →  Service  →  Repository  →  Banco (PostgreSQL)
(endpoints)   (regras de     (acesso a
              negócio)        dados)
```

- `model/` — entidades JPA (`Atendente`, `Gorjeta`)
- `repository/` — interfaces Spring Data JPA
- `service/` — regras de negócio (cálculo da gorjeta)
- `controller/` — endpoints REST

## Como rodar

**Pré-requisitos:** JDK 21, Maven, Docker.

Troque por:

````markdown
## Como rodar

**Pré-requisitos:** JDK 21, Maven, Docker, Python 3.

1. Na primeira vez, dê permissão de execução ao script:
   ```bash
   chmod +x start.sh
   ```
2. Suba tudo (banco,backend e frontend):
   ./start.sh

3. A API fica disponível em http://localhost:8080 e o frontend em http://localhost:5500.

4. Para parar backend e frontend, aperte Ctrl+C no terminal onde rodou o ./start.sh (o banco de dados continua rodando em segundo plano via Docker — pra derrubar ele também, use docker compose down).

## Endpoints da API

| Método | Rota                       | Descrição                             | Corpo (JSON)                                                   |
| ------ | -------------------------- | ------------------------------------- | -------------------------------------------------------------- |
| POST   | `/atendentes`              | Cadastra um atendente                 | `{ "nome": "Maria" }`                                          |
| GET    | `/atendentes`              | Lista todos os atendentes             | —                                                              |
| POST   | `/gorjetas`                | Calcula e salva uma gorjeta           | `{ "atendenteId": 1, "valorConta": 150.00, "percentual": 10 }` |
| GET    | `/gorjetas/atendente/{id}` | Histórico de gorjetas de um atendente | —                                                              |

## Status do projeto

- [x] Backend (Spring Boot + PostgreSQL + API REST)
- [x] Interface web (HTML/CSS/JS)
- [ ] Associação de gorjeta a número de mesa (futuro)
````
