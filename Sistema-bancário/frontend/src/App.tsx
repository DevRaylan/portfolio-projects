import { CriarContaForm } from './components/CriarContaForm'
import { ListaContas } from './components/ListaContas'

function App() {
  return (
    <div className="min-h-screen bg-gray-50">
      <header className="border-b border-gray-200 bg-white px-6 py-4">
        <h1 className="text-xl font-bold text-gray-900">Sistema Bancário</h1>
      </header>
      <main className="mx-auto max-w-5xl px-6 py-8">
        <CriarContaForm />
        <ListaContas />
      </main>
    </div>
  )
}

export default App