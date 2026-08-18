# Locação

Sistema de listagem de acomodações para locação por temporada. Permite buscar acomodações por cidade, ver detalhes de cada uma e favoritar as de interesse (salvo no navegador). Projeto de estudo full-stack, com backend e frontend desacoplados.

## Tecnologias

| Camada    | Tecnologia               | Por quê                                                        |
| --------- | ------------------------- | ---------------------------------------------------------------- |
| Backend   | FastAPI (Python)          | API REST leve e rápida de escrever, com validação via Pydantic   |
| Servidor  | Uvicorn                   | Servidor ASGI para rodar a aplicação FastAPI                     |
| Dados     | JSON (`data.json`)        | Persistência simples, sem depender de banco de dados externo     |
| Frontend  | React 19 + Vite           | SPA rápida de desenvolver, com build e HMR instantâneos          |
| Roteamento| react-router-dom           | Navegação entre lista e página de detalhes de cada acomodação    |
| Estado local | localStorage            | Persiste os favoritos do usuário entre sessões, sem backend      |

## Estrutura

- `backend/`: API FastAPI (`main.py`), servindo os dados de `data.json` e as imagens em `static/`
- `frontend/`: SPA React (`src/Home.jsx` lista e filtra acomodações, `src/AccommodationDetails.jsx` exibe os detalhes de uma acomodação)

## Como rodar localmente

**Backend:**
```bash
cd backend
python -m venv venv
venv\Scripts\activate
pip install -r requirements.txt
uvicorn main:app --reload
```
Sobe em `http://127.0.0.1:8000`.

**Frontend:**
```bash
cd frontend
npm install
npm run dev
```
Sobe em `http://localhost:5173`.

## Status

Projeto de estudo, sem persistência real de dados (o "banco" é um arquivo JSON estático) — ideal para demonstrar a integração entre uma API REST e uma SPA React.
