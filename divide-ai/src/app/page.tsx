import { Wallet } from "lucide-react";
import Dashboard from "./dashboard";
import { accountFromSession, authConfig } from "../auth/google";

const messages: Record<string, string> = {
  expired: "O link de login expirou. Tente entrar novamente.",
  cancelled: "A entrada com Google foi cancelada.",
  failed: "Não foi possível entrar com Google. Tente novamente.",
};

export default async function Home({ searchParams }: {
  searchParams: Promise<{ login?: string; demo?: string }>;
}) {
  const config = authConfig();
  const account = config ? await accountFromSession(config) : null;
  const { login, demo } = await searchParams;
  if (account) return <Dashboard key={account.id} account={account}/>;
  if (!config && process.env.NODE_ENV === "development" && demo === "1") return <Dashboard/>;

  return <main className="login-page">
    <section className="login-card" aria-labelledby="login-title">
      <span className="brand-icon"><Wallet size={22}/></span>
      <h1 id="login-title">Divide Aí</h1>
      <p>Entre para acompanhar as despesas da sua casa.</p>
      {login && Object.hasOwn(messages, login) && <p className="error" role="alert">{messages[login]}</p>}
      {config ? <a className="primary" href="/api/auth/google/start">Entrar com Google</a>
        : <p role="status">O login com Google precisa ser configurado nesta instalação.</p>}
      {!config && process.env.NODE_ENV === "development" && <a className="secondary" href="/?demo=1">Abrir demonstração local</a>}
    </section>
  </main>;
}
