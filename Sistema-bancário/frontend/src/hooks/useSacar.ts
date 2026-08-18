import { useMutation, useQueryClient } from '@tanstack/react-query'
import { contaApi } from '../api/contaApi'
import type { ValorRequest } from '../api/types'

export function useSacar() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: ({ numero, dados }: { numero: number; dados: ValorRequest }) =>
      contaApi.sacar(numero, dados),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['contas'] })
    },
  })
}