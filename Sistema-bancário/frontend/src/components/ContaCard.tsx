import { useState } from 'react'
import type { FormEvent } from 'react'
import type { ContaResponse } from '../api/types'
import { useDepositar } from '../hooks/useDepositar'
import { useSacar } from '../hooks/useSacar'
import { useTransferir } from '../hooks/useTransferir'

type Acao = 'depositar' | 'sacar' | 'transferir' | null

const ROTULO_TIPO: Record<string, string> = {
  DEPOSITO: 'Depósito',
  SAQUE: 'Saque',
  TRANSFERENCIA: 'Transferência',
}

export function ContaCard({ conta }: { conta: ContaResponse }) {
  const [acao, setAcao] = useState<Acao>(null)
  const [valor, setValor] = useState('')
  const [numeroDestino, setNumeroDestino] = useState('')
  const [mostrarExtrato, setMostrarExtrato] = useState(false)

  const depositar = useDepositar()
  const sacar = useSacar()
  const transferir = useTransferir()

  const mutacaoAtiva =
    acao === 'depositar' ? depositar : acao === 'sacar' ? sacar : acao === 'transferir' ? transferir : null

  function fecharAcao() {
    setAcao(null)
    setValor('')
    setNumeroDestino('')
  }

  function handleSubmit(event: FormEvent) {
    event.preventDefault()
    const valorNumero = Number(valor)

    if (acao === 'depositar') {
      depositar.mutate({ numero: conta.numero, dados: { valor: valorNumero } }, { onSuccess: fecharAcao })
    } else if (acao === 'sacar') {
      sacar.mutate({ numero: conta.numero, dados: { valor: valorNumero } }, { onSuccess: fecharAcao })
    } else if (acao === 'transferir') {
      transferir.mutate(
        { numero: conta.numero, dados: { numeroDestino: Number(numeroDestino), valor: valorNumero } },
        { onSuccess: fecharAcao },
      )
    }
  }

  return (
    <div className="rounded-lg border border-gray-200 p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
      <div className="flex items-center justify-between">
        <span className="text-sm font-medium text-gray-500 dark:text-neutral-400">Conta {conta.numero}</span>
        <span className="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900 dark:text-blue-300">
          {conta.tipo === 'ContaCorrente' ? 'Corrente' : 'Poupança'}
        </span>
      </div>
      <p className="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{conta.titular}</p>
      <p className="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
        {conta.saldo.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}
      </p>

      <div className="mt-3 flex flex-wrap gap-2">
        <button type="button" onClick={() => setAcao('depositar')} className="rounded bg-green-50 px-2 py-1 text-xs font-medium text-green-700 hover:bg-green-100 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800">
          Depositar
        </button>
        <button type="button" onClick={() => setAcao('sacar')} className="rounded bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 hover:bg-amber-100 dark:bg-amber-900 dark:text-amber-300 dark:hover:bg-amber-800">
          Sacar
        </button>
        <button type="button" onClick={() => setAcao('transferir')} className="rounded bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800">
          Transferir
        </button>
        <button type="button" onClick={() => setMostrarExtrato((v) => !v)} className="rounded bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-200 dark:bg-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-600">
          {mostrarExtrato ? 'Ocultar extrato' : 'Ver extrato'}
        </button>
      </div>

      {acao && (
        <form onSubmit={handleSubmit} className="mt-3 flex flex-col gap-2 border-t border-gray-100 pt-3 dark:border-neutral-700">
          {acao === 'transferir' && (
            <input
              type="number"
              placeholder="Número da conta destino"
              value={numeroDestino}
              onChange={(e) => setNumeroDestino(e.target.value)}
              required
              className="rounded border border-gray-300 px-2 py-1 text-sm dark:border-neutral-600 dark:bg-neutral-900 dark:text-white"
            />
          )}
          <input
            type="number"
            step="0.01"
            placeholder="Valor"
            value={valor}
            onChange={(e) => setValor(e.target.value)}
            required
            className="rounded border border-gray-300 px-2 py-1 text-sm dark:border-neutral-600 dark:bg-neutral-900 dark:text-white"
          />
          <div className="flex gap-2">
            <button
              type="submit"
              disabled={mutacaoAtiva?.isPending}
              className="rounded bg-gray-900 px-3 py-1 text-sm font-medium text-white hover:bg-gray-800 disabled:opacity-50 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
            >
              {mutacaoAtiva?.isPending ? 'Enviando...' : 'Confirmar'}
            </button>
            <button type="button" onClick={fecharAcao} className="rounded px-3 py-1 text-sm text-gray-500 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700">
              Cancelar
            </button>
          </div>
          {mutacaoAtiva?.isError && (
            <p className="text-sm text-red-600 dark:text-red-400">{(mutacaoAtiva.error as Error).message}</p>
          )}
        </form>
      )}

      {mostrarExtrato && (
        <div className="mt-3 border-t border-gray-100 pt-3 dark:border-neutral-700">
          {conta.historico.length === 0 ? (
            <p className="text-sm text-gray-500 dark:text-neutral-400">Nenhuma transação ainda.</p>
          ) : (
            <ul className="flex flex-col gap-1">
              {conta.historico.map((transacao, index) => (
                <li key={index} className="flex items-center justify-between text-sm">
                  <span className="text-gray-600 dark:text-neutral-400">{ROTULO_TIPO[transacao.tipo] ?? transacao.tipo}</span>
                  <span className="text-gray-500 dark:text-neutral-500">
                    {new Date(transacao.data).toLocaleString('pt-BR')}
                  </span>
                  <span className="font-medium text-gray-900 dark:text-white">
                    {transacao.valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}
                  </span>
                </li>
              ))}
            </ul>
          )}
        </div>
      )}
    </div>
  )
}