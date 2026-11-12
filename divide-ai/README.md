# Divide Aí

Aplicação web da disciplina CIN7943 — Experiência do Usuário (UFSC). A primeira versão demonstra a divisão de despesas de uma república e o fluxo de contestação mapeado na Tarefa 06.

## Executar localmente

Requer Node.js 24 ou 26.

```bash
npm ci
npm run dev
```

Abra http://localhost:3000. Sem credenciais, o servidor de desenvolvimento oferece o botão **Abrir demonstração local**. Use `npm run typecheck` para conferir os tipos e `npm run build` para gerar a versão de produção.

## Configurar o login com Google

1. No [Google Auth Platform](https://console.cloud.google.com/auth/overview), configure a marca e o público do app. Para contas fora da sua organização, escolha público externo. Se o app estiver em teste e exigir uma lista de testadores, adicione as contas que vão acessar.
2. Em **Clientes**, crie um cliente OAuth do tipo **Aplicativo da Web** e adicione este URI em **URIs de redirecionamento autorizados**:

   ```text
   http://localhost:3000/api/auth/google/callback
   ```

3. Copie `.env.example` para `.env.local` e preencha `GOOGLE_CLIENT_ID` e `GOOGLE_CLIENT_SECRET` com os valores do cliente. Gere o valor de `AUTH_SECRET` com `openssl rand -hex 32` e guarde-o no mesmo arquivo.
4. Reinicie o servidor e acesse pelo endereço exato de `APP_URL`. Clique em **Entrar com Google**. Para testar uma segunda conta no mesmo navegador, use **Sair** e escolha a outra conta.

Em uma hospedagem, defina `APP_URL` com a origem HTTPS e cadastre no Google o endereço correspondente com o caminho `/api/auth/google/callback`. Mantenha os segredos apenas nas variáveis do servidor.

O login identifica cada conta separadamente. As despesas são guardadas neste navegador, por conta. A demonstração local conserva seus próprios dados. Grupos compartilhados e sincronização entre dispositivos dependem de persistência no servidor.

## Arquitetura

- `src/domain/expense.ts` contém as classes de domínio. `Expense` valida dados, divide o valor em centavos e controla pagamentos e contestação. `Household` calcula o saldo a pagar e a receber.
- `src/app` contém o painel e os formulários em Next.js, React e TypeScript.
- A demonstração usa três moradores fictícios e guarda os dados no navegador. A resposta do responsável pela despesa é simulada na interface. O registro de pagamento é manual e não movimenta dinheiro.

Para experimentar a contestação, abra **Aluguel de novembro**, selecione **Contestar despesa**, envie uma justificativa, simule uma resposta e marque a contestação como resolvida.
