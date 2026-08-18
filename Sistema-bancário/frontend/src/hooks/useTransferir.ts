import { useMutation, useQueryClient } from '@tanstack/react-query'
import { contaApi } from '../api/contaApi'
import type { TransferenciaRequest } from '../api/types'

export function useTransferir() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: ({ numero, dados }: { numero: number; dados: TransferenciaRequest }) =>
      contaApi.transferir(numero, dados),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['contas'] })
    },
  })
}