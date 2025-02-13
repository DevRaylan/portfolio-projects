@echo off
echo Iniciando o Backend e o Frontend...

REM Inicia o backend em uma nova janela
start "Backend" cmd /k "cd /d C:\DEV PESSOA\Api-locacao\backend && call venv\Scripts\activate && python -m uvicorn main:app --reload"

REM Aguarda 5 segundos para o backend iniciar (ajuste se necessário)
timeout /t 5

REM Inicia o frontend em uma nova janela
start "Frontend" cmd /k "cd /d C:\DEV PESSOA\Api-locacao\frontend && npm install && npm run dev"

REM Aguarda mais 5 segundos para o frontend iniciar (ajuste se necessário)
timeout /t 5

REM Abre o navegador na URL do frontend
start "" "http://localhost:5173"

echo Todos os servidores foram iniciados.
pause
