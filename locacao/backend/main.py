from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from fastapi.staticfiles import StaticFiles
from typing import List, Optional
from pydantic import BaseModel
import json

app = FastAPI()

# Monta a pasta 'static' para servir arquivos estáticos
app.mount("/static", StaticFiles(directory="static"), name="static")

# Configurar CORS para permitir chamadas do frontend (ajuste conforme sua necessidade)
app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:5173", "http://127.0.0.1:5173"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Definição do modelo de acomodação
class Acomodacao(BaseModel):
    id: int
    nome: str
    cidade: str
    preco: float
    imagem: Optional[str] = None
    descricao: Optional[str] = None

# Função para carregar os dados do arquivo JSON
def carregar_acomodacoes():
    try:
        with open("data.json", "r", encoding="utf-8") as f:
            return json.load(f)
    except FileNotFoundError:
        return []

# Carrega os dados na inicialização do servidor
acomodacoes = carregar_acomodacoes()

@app.get("/", tags=["Raiz"])
def home():
    return {"mensagem": "Bem-vindo à API de acomodações!"}

@app.get("/acomodacoes", response_model=List[Acomodacao], tags=["Acomodações"])
def listar_acomodacoes(cidade: Optional[str] = None):
    """
    Retorna todas as acomodações. Se uma cidade for informada, retorna apenas as acomodações dessa cidade.
    """
    if cidade:
        cidade_normalizada = cidade.strip().lower()
        resultado = [
            a for a in acomodacoes
            if a["cidade"].strip().lower() == cidade_normalizada
        ]
        if not resultado:
            raise HTTPException(status_code=404, detail="Nenhuma acomodação encontrada para essa cidade.")
        return resultado
    return acomodacoes

@app.get("/acomodacoes/{id}", response_model=Acomodacao, tags=["Acomodações"])
def get_acomodacao(id: int):
    """
    Retorna os detalhes de uma acomodação com base no seu ID.
    """
    for a in acomodacoes:
        if a["id"] == id:
            return a
    raise HTTPException(status_code=404, detail="Acomodação não encontrada")
