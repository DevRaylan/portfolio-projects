# Checklist de Desenvolvimento — Sistema Bancário (POO)

## 1. Levantamento de requisitos

- [x] Funcionalidades básicas: criar conta, depósito, saque, transferência
- [x] Consulta de saldo / extrato
- [x] Regras de negócio: tipos de conta (corrente/poupança), taxas, limites (cheque especial), juros
- [x] Requisitos não-funcionais: persistência (memória), interface (web via Spring Boot)

## 2. Modelagem

- [x] Identificar entidades: Cliente, Conta, ContaCorrente, ContaPoupanca, Transacao
- [x] Identificar relacionamentos entre entidades
- [x] Desenhar diagrama de classes (atributos, métodos, tipo de relação: herança, composição, associação)
- [x] Identificar hierarquias (Conta abstrata → ContaCorrente / ContaPoupança)
- [x] Identificar contratos/interfaces necessários (Transacionavel, Tributavel, ContaRepository)

## 3. Setup do ambiente

- [x] Estrutura de pastas/pacotes
- [x] Controle de versão (git — já existente na pasta portfolio-projects)

## 4. Implementação

- [x] Desenvolvimento incremental (classe por classe, compilando a cada passo)

## 5. Testes

- [x] Testes unitários (cada classe isolada) — `ContaTest`
- [x] Testes de integração (fluxo completo, ex.: transferência entre contas) — `ContaServiceTest`
- [x] Testes de casos de erro (saldo insuficiente, conta inexistente) — validado também manualmente via API (HTTP 422)

## 6. Documentação

- [ ] Comentários/Javadoc nas classes principais
- [ ] README explicando como compilar e rodar

## 7. Refatoração/revisão

- [ ] Revisar nomes, remover duplicação, checar responsabilidades de cada classe
