export type TipoConta = 'CORRENTE' | 'POUPANCA'

export interface TransacaoResponse {
  tipo: string
  valor: number
  data: string
}

export interface ContaResponse {
  numero: number
  tipo: string
  titular: string
  saldo: number
  historico: TransacaoResponse[]
}

export interface CriarContaRequest {
  nome: string
  cpf: string
  tipo: TipoConta
}

export interface ValorRequest {
  valor: number
}

export interface TransferenciaRequest {
  numeroDestino: number
  valor: number
}