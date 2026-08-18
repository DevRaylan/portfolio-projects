import { CriarContaForm } from './components/CriarContaForm'
import { ListaContas } from './components/ListaContas'

function App() {
  return (
    <div className="min-h-screen bg-gray-50 dark:bg-neutral-900">
      <header className="border-b border-gray-200 bg-white px-6 py-4 dark:border-neutral-700 dark:bg-neutral-800">
        <h1 className="text-xl font-bold text-gray-900 dark:text-white">Sistema Bancário</h1>
      </header>
      <main className="mx-auto max-w-5xl px-6 py-8">
        <CriarContaForm />
        <ListaContas />
      </main>
    </div>
  )
}

export default App