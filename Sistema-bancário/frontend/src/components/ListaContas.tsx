import { useContas } from '../hooks/useContas'
import { ContaCard } from './ContaCard'

export function ListaContas() {
  const { data: contas, isLoading, isError, error } = useContas()

  if (isLoading) return <p className="text-gray-500 dark:text-neutral-400">Carregando contas...</p>
  if (isError) return <p className="text-red-600 dark:text-red-400">Erro ao carregar contas: {(error as Error).message}</p>
  if (!contas || contas.length === 0) return <p className="text-gray-500 dark:text-neutral-400">Nenhuma conta cadastrada.</p>

  return (
    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      {contas.map((conta) => (
        <ContaCard key={conta.numero} conta={conta} />
      ))}
    </div>
  )
}