import type {
  ContaResponse,
  CriarContaRequest,
  ValorRequest,
  TransferenciaRequest,
} from './types'

const BASE_URL = 'http://localhost:8081/contas'

async function tratarResposta<T>(response: Response): Promise<T> {
  if (!response.ok) {
    const mensagem = await response.text()
    throw new Error(mensagem || `Erro ${response.status}`)
  }
  return response.json()
}

export const contaApi = {
  listar: (): Promise<ContaResponse[]> =>
    fetch(BASE_URL).then((res) => tratarResposta(res)),

  consultar: (numero: number): Promise<ContaResponse> =>
    fetch(`${BASE_URL}/${numero}`).then((res) => tratarResposta(res)),

  criar: (dados: CriarContaRequest): Promise<ContaResponse> =>
    fetch(BASE_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(dados),
    }).then((res) => tratarResposta(res)),

  depositar: (numero: number, dados: ValorRequest): Promise<ContaResponse> =>
    fetch(`${BASE_URL}/${numero}/depositos`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(dados),
    }).then((res) => tratarResposta(res)),

  sacar: (numero: number, dados: ValorRequest): Promise<ContaResponse> =>
    fetch(`${BASE_URL}/${numero}/saques`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(dados),
    }).then((res) => tratarResposta(res)),

  transferir: (numero: number, dados: TransferenciaRequest): Promise<ContaResponse> =>
    fetch(`${BASE_URL}/${numero}/transferencias`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(dados),
    }).then((res) => tratarResposta(res)),
}