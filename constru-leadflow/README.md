# ConstruLink

Inteligência comercial para o setor da construção civil. Mapeie obras em fase inicial, qualifique leads, gerencie empresas construtoras e acompanhe tudo em um dashboard com mapa e analytics.

## Funcionalidades

- **Dashboard** — visão geral com estatísticas (obras cadastradas, leads de alta prioridade, valor total estimado, empresas cadastradas).
- **Leads** — listagem de obras com busca, filtros por prioridade/cidade e exportação para CSV.
- **Mapa de Obras** — visualização geográfica das obras cadastradas (Leaflet + OpenStreetMap).
- **Analytics** — gráficos de leads por prioridade e por cidade.
- **Empresas** — cadastro, edição e exclusão de empresas construtoras/empreiteiras.
- **Integrações** — painel com integrações planejadas (CRM, WhatsApp, E-mail, Webhooks/API), marcadas como "Em breve".

## Tecnologias

| Camada       | Tecnologia                              | Por quê                                                        |
| ------------ | ---------------------------------------- | --------------------------------------------------------------- |
| Frontend     | React 18 + TypeScript + Vite             | Build rápido e tipagem estática                                 |
| UI           | shadcn/ui + Tailwind CSS                 | Componentes acessíveis com estilização utilitária                |
| Dados        | Supabase (Postgres + Auth)               | Backend gerenciado com RLS por linha e autenticação pronta       |
| Cache/estado | @tanstack/react-query                    | Cache e sincronização automática após criar/editar/excluir       |
| Formulários  | react-hook-form + zod                    | Validação de formulários com tipagem                             |
| Gráficos     | recharts                                 | Gráficos de Analytics (leads por prioridade/cidade)               |
| Mapa         | Leaflet                                  | Mapa de obras com marcadores                                     |
## Pré-requisitos

- [Node.js](https://nodejs.org/) e npm
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (necessário para rodar o Supabase localmente)

Não é preciso instalar o Supabase CLI globalmente — os comandos abaixo usam `npx`.

## Rodando o projeto localmente

### 1. Clonar e instalar dependências

```sh
git clone <URL_DO_REPOSITORIO>
cd constru-leadflow
npm install
```

### 2. Subir o Supabase local

Isso cria um banco Postgres local via Docker e aplica automaticamente as migrations em `supabase/migrations/`:

```sh
npx supabase start
```

Ao final, o comando imprime as credenciais locais (`API URL`, `anon key`, `Studio URL`, etc). Guarde essas informações — elas mudam a cada `supabase start` do zero.

### 3. Configurar as variáveis de ambiente

Copie `.env.example` para `.env` e preencha com os valores impressos no passo anterior:

```sh
cp .env.example .env
```

```
VITE_SUPABASE_URL="http://127.0.0.1:54321"
VITE_SUPABASE_PUBLISHABLE_KEY="<anon key impressa pelo supabase start>"
VITE_SUPABASE_PROJECT_ID="<opcional, não é usado pelo client>"
```

> O `.env` não é versionado no git (está no `.gitignore`). Nunca commite a `service_role key` — ela ignora todas as políticas de RLS.

### 4. Rodar o servidor de desenvolvimento

```sh
npm run dev
```

A aplicação abre em `http://localhost:8080` (ou na próxima porta livre, se essa já estiver em uso).

### 5. Criar uma conta

Acesse `/auth` na aplicação e crie uma conta pela tela de cadastro. Alternativamente, é possível criar um usuário já confirmado direto pela API de admin do GoTrue local:

```sh
curl -s -X POST 'http://127.0.0.1:54321/auth/v1/admin/users' \
  -H "apikey: <service_role key>" \
  -H "Authorization: Bearer <service_role key>" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@exemplo.com","password":"SuaSenhaAqui","email_confirm":true}'
```

### Painel do banco de dados (Supabase Studio)

Com o Supabase local rodando, acesse `http://127.0.0.1:54323` para visualizar e editar as tabelas diretamente.

### Parando o ambiente local

```sh
npx supabase stop
```

## Scripts disponíveis

| Comando | Descrição |
| --- | --- |
| `npm run dev` | Inicia o servidor de desenvolvimento (Vite) |
| `npm run build` | Gera o build de produção |
| `npm run build:dev` | Gera o build em modo desenvolvimento |
| `npm run lint` | Roda o ESLint |
| `npm run preview` | Serve o build de produção localmente |

## Estrutura do banco de dados

As migrations em `supabase/migrations/` definem, nessa ordem:

- **`profiles`** — dados do usuário (nome, e-mail, empresa), criado automaticamente via trigger ao cadastrar uma conta.
- **`constructions`** — obras/leads (localização, status de prioridade, valor estimado, contatos, comprador).
- **`companies`** — empresas construtoras/empreiteiras.
- Migrations posteriores corrigem privilégios de schema (`GRANT`) e a trigger de criação de perfil para também salvar o campo empresa.

Todas as tabelas têm Row Level Security (RLS) habilitado: cada usuário só acessa os próprios registros (`auth.uid() = user_id`).

## Notas de desenvolvimento

- Toda leitura de dados (obras e empresas) passa pelos hooks `useConstructions`/`useCompanies` (`src/hooks/`), que usam `react-query` com chaves de cache compartilhadas em `src/lib/queryKeys.ts`. Mutations (criar/editar/excluir) invalidam essas chaves para atualizar a UI automaticamente, sem recarregar a página.
- Há um Error Boundary global (`src/components/ErrorBoundary.tsx`) que evita que um erro de render em um componente derrube a aplicação inteira.
- O painel de Integrações é apenas informativo/roadmap — nenhuma integração externa está implementada de fato ainda.
