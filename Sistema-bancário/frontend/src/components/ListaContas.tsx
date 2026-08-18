import { useContas } from '../hooks/useContas'

export function ListaContas() {
  const { data: contas, isLoading, isError, error } = useContas()

  if (isLoading) return <p className="text-gray-500">Carregando contas...</p>
  if (isError) return <p className="text-red-600">Erro ao carregar contas: {(error as Error).message}</p>
  if (!contas || contas.length === 0) return <p className="text-gray-500">Nenhuma conta cadastrada.</p>

  return (
    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      {contas.map((conta) => (
        <div key={conta.numero} className="rounded-lg border border-gray-200 p-4 shadow-sm">
          <div className="flex items-center justify-between">
            <span className="text-sm font-medium text-gray-500">Conta {conta.numero}</span>
            <span className="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">
              {conta.tipo === 'ContaCorrente' ? 'Corrente' : 'Poupança'}
            </span>
          </div>
          <p className="mt-2 text-lg font-semibold text-gray-900">{conta.titular}</p>
          <p className="mt-1 text-2xl font-bold text-gray-900">
            {conta.saldo.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}
          </p>
        </div>
      ))}
    </div>
  )
}