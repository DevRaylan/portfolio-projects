#!/bin/bash
set -e

echo "==> Subindo banco de dados (Docker)..."
docker compose up -d

echo "==> Subindo backend (Spring Boot)..."
mvn spring-boot:run &
BACKEND_PID=$!

echo "==> Subindo frontend (servidor estático)..."
(cd frontend && python -m http.server 5500) &
FRONTEND_PID=$!

trap "echo; echo '==> Parando backend e frontend...'; kill $BACKEND_PID $FRONTEND_PID 2>/dev/null" EXIT INT TERM

wait