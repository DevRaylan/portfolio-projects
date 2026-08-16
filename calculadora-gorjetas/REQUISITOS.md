# Requisitos — Calculadora de Gorjetas

## Contexto
Sistema para calcular gorjetas de atendentes (garçons/garçonetes), com cadastro
de atendentes e histórico das gorjetas calculadas. Uso multiusuário em rede
local, sem login por enquanto.

## Requisitos Funcionais (o que o sistema faz)

| ID | Descrição |
|---|---|
| RF01 | Cadastrar atendentes (nome, no mínimo) |
| RF02 | Calcular a gorjeta de uma conta (valor da conta + percentual) |
| RF03 | Associar cada cálculo de gorjeta a um atendente específico |
| RF04 | Guardar histórico das gorjetas calculadas (por atendente, de forma permanente) |
| RF05 *(futuro — fora do escopo inicial)* | Associar a gorjeta também a um número de mesa |

## Requisitos Não-Funcionais (como o sistema deve se comportar)

| ID | Descrição |
|---|---|
| RNF01 | Multiusuário — várias pessoas acessando ao mesmo tempo pela rede local |
| RNF02 | Sem login/autenticação por enquanto |
| RNF03 | Roda na rede local (sem hospedagem na internet ainda) |
| RNF04 | Interface via navegador web |
| RNF05 | Backend em Java com Spring Boot (Maven) |
| RNF06 | Dados persistidos em banco de dados (histórico não pode sumir ao reiniciar) |

## Decisões já tomadas
- Build tool: **Maven**
- Frontend: **API REST (JSON) + HTML/CSS/JS separado** (sem Thymeleaf)
- Banco de dados: **PostgreSQL via Docker**

## Em aberto / próximos passos
- Desenho das entidades (ex: Atendente, Gorjeta)
- Desenho dos endpoints da API REST
