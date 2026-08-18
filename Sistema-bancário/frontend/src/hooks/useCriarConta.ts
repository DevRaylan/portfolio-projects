import { useMutation, useQueryClient } from '@tanstack/react-query'
import { contaApi } from '../api/contaApi'

export function useCriarConta() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: contaApi.criar,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['contas'] })
    },
  })
}