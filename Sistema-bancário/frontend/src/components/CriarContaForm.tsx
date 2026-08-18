import { useState } from 'react'
import type { FormEvent } from 'react'
import { useCriarConta } from '../hooks/useCriarConta'
import type { TipoConta } from '../api/types'

export function CriarContaForm() {
  const [nome, setNome] = useState('')
  const [cpf, setCpf] = useState('')
  const [tipo, setTipo] = useState<TipoConta>('CORRENTE')
  const criarConta = useCriarConta()

  function handleSubmit(event: FormEvent) {
    event.preventDefault()
    criarConta.mutate(
      { nome, cpf, tipo },
      {
        onSuccess: () => {
          setNome('')
          setCpf('')
          setTipo('CORRENTE')
        },
      },
    )
  }

  return (
    <form onSubmit={handleSubmit} className="mb-8 flex flex-wrap items-end gap-3 rounded-lg border border-gray-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
      <div className="flex flex-col">
        <label className="text-sm font-medium text-gray-700 dark:text-neutral-300" htmlFor="nome">Nome</label>
        <input
          id="nome"
          value={nome}
          onChange={(e) => setNome(e.target.value)}
          required
          className="rounded border border-gray-300 px-3 py-1.5 dark:border-neutral-600 dark:bg-neutral-900 dark:text-white"
        />
      </div>
      <div className="flex flex-col">
        <label className="text-sm font-medium text-gray-700 dark:text-neutral-300" htmlFor="cpf">CPF</label>
        <input
          id="cpf"
          value={cpf}
          onChange={(e) => setCpf(e.target.value)}
          required
          className="rounded border border-gray-300 px-3 py-1.5 dark:border-neutral-600 dark:bg-neutral-900 dark:text-white"
        />
      </div>
      <div className="flex flex-col">
        <label className="text-sm font-medium text-gray-700 dark:text-neutral-300" htmlFor="tipo">Tipo</label>
        <select
          id="tipo"
          value={tipo}
          onChange={(e) => setTipo(e.target.value as TipoConta)}
          className="rounded border border-gray-300 px-3 py-1.5 dark:border-neutral-600 dark:bg-neutral-900 dark:text-white"
        >
          <option value="CORRENTE">Corrente</option>
          <option value="POUPANCA">Poupança</option>
        </select>
      </div>
      <button
        type="submit"
        disabled={criarConta.isPending}
        className="rounded bg-blue-600 px-4 py-1.5 font-medium text-white hover:bg-blue-700 disabled:opacity-50"
      >
        {criarConta.isPending ? 'Criando...' : 'Criar conta'}
      </button>
      {criarConta.isError && (
        <p className="w-full text-sm text-red-600 dark:text-red-400">{(criarConta.error as Error).message}</p>
      )}
    </form>
  )
}