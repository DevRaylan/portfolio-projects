# Requisitos — Calculadora de Gorjetas

## Contexto
Sistema para calcular gorjetas de atendentes (garçons/garçonetes), organizado em
torno de mesas: cada mesa é aberta com um atendente, pode trocar de atendente
(com motivo registrado), e é fechada calculando a gorjeta com base no valor da
conta e um percentual pré-cadastrado — voltando a ficar disponível automaticamente.
Uso multiusuário em rede local, sem login por enquanto.

## Requisitos Funcionais (o que o sistema faz)

| ID | Descrição |
|---|---|
| RF01 | Cadastrar atendentes (nome, no mínimo) |
| RF02 | Calcular a gorjeta de uma conta (valor da conta + percentual) |
| RF03 | Associar cada cálculo de gorjeta a um atendente específico |
| RF04 | Guardar histórico das gorjetas calculadas (por atendente, de forma permanente) |
| RF05 | Cadastrar mesas (número único), com estado vazia / ocupada / indisponível (desativada) |
| RF06 | Abrir uma mesa vazia, associando um atendente a ela |
| RF07 | Trocar o atendente de uma mesa ocupada, exigindo motivo da troca (registrado em histórico separado, com atendente anterior, novo e data/hora) |
| RF08 | Fechar uma mesa (calcular a gorjeta com base no valor total da conta e um percentual pré-cadastrado), liberando a mesa automaticamente em seguida |
| RF09 | Cadastrar percentuais de gorjeta pré-definidos, usados na hora de fechar uma mesa |
| RF10 | Desativar/reativar atendentes, mesas e percentuais (exclusão lógica — histórico associado é preservado) |
| RF11 | Editar nome de atendente, número de mesa, valor de percentual |
| RF12 | Exibir histórico de gorjetas navegável por dia, com data/hora de abertura e fechamento de cada mesa |

## Requisitos Não-Funcionais (como o sistema deve se comportar)

| ID | Descrição |
|---|---|
| RNF01 | Multiusuário — várias pessoas acessando ao mesmo tempo pela rede local |
| RNF02 | Sem login/autenticação por enquanto |
| RNF03 | Roda na rede local (sem hospedagem na internet ainda) |
| RNF04 | Interface via navegador web |
| RNF05 | Backend em Java com Spring Boot (Maven) |
| RNF06 | Dados persistidos em banco de dados (histórico não pode sumir ao reiniciar) |
| RNF07 | Interface segue a linguagem visual do Material Design 3 |
| RNF08 | Frontend sem framework nem build step — JS em módulos ES nativos, CSS dividido por responsabilidade |

## Decisões já tomadas
- Build tool: **Maven**
- Frontend: **API REST (JSON) + HTML/CSS/JS separado** (sem Thymeleaf), em módulos ES nativos
- Banco de dados: **PostgreSQL via Docker**
- Exclusão lógica (soft delete) em vez de exclusão física para atendentes, mesas e percentuais — preserva o histórico de gorjetas e trocas já registrado
- Percentuais de gorjeta são pré-cadastrados (não digitados livremente na hora de calcular)
- Motivo de troca de atendente é obrigatório e fica registrado em histórico próprio (`TrocaAtendente`)

