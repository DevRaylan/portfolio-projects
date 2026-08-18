import { useQuery } from '@tanstack/react-query'
import { contaApi } from '../api/contaApi'

export function useContas() {
  return useQuery({
    queryKey: ['contas'],
    queryFn: contaApi.listar,
  })
}