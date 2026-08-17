import { API_URL } from "./api.js";
import { carregarMesas } from "./mesas.js";

const btnAbrirMesas = document.getElementById("btn-abrir-mesas");
const btnFecharMesas = document.getElementById("btn-fechar-mesas");
const modalMesas = document.getElementById("modal-mesas");
const formNovaMesa = document.getElementById("form-mesa");
const inputNumeroMesaNova = document.getElementById("input-numero-mesa-nova");
const listaGerenciarMesas = document.getElementById("lista-gerenciar-mesas");

async function carregarListaGerenciarMesas() {
    const resposta = await fetch(`${API_URL}/mesas/todos`);
    const mesas = await resposta.json();

    listaGerenciarMesas.innerHTML = "";

    mesas.forEach(mesa => {
        const item = document.createElement("li");
        item.className = "item-gerenciar-atendente";

        const numeroSpan = document.createElement("span");
        numeroSpan.textContent = `Mesa ${mesa.numero}`;
        if (!mesa.ativa) {
            numeroSpan.classList.add("inativo");
        }

        const btnEditar = document.createElement("button");
        btnEditar.type = "button";
        btnEditar.textContent = "Editar";
        btnEditar.onclick = () => {
            const inputEdicao = document.createElement("input");
            inputEdicao.type = "number";
            inputEdicao.value = mesa.numero;
            item.replaceChild(inputEdicao, numeroSpan);
            inputEdicao.focus();

            btnEditar.textContent = "Salvar";
            btnEditar.onclick = async () => {
                const resposta = await fetch(`${API_URL}/mesas/${mesa.id}`, {
                    method: "PUT",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ numero: Number(inputEdicao.value) })
                });

                if (!resposta.ok) {
                    const dados = await resposta.json();
                    alert(dados.erro);
                    return;
                }

                carregarListaGerenciarMesas();
                carregarMesas();
            };
        };

        const btnStatus = document.createElement("button");
        btnStatus.type = "button";
        btnStatus.textContent = mesa.ativa ? "Desativar" : "Reativar";
        btnStatus.onclick = async () => {
            const acao = mesa.ativa ? "desativar" : "reativar";
            await fetch(`${API_URL}/mesas/${mesa.id}/${acao}`, { method: "PATCH" });
            carregarListaGerenciarMesas();
            carregarMesas();
        };

        item.appendChild(numeroSpan);
        item.appendChild(btnEditar);
        item.appendChild(btnStatus);
        listaGerenciarMesas.appendChild(item);
    });
}

btnAbrirMesas.addEventListener("click", () => {
    modalMesas.classList.remove("oculto");
    carregarListaGerenciarMesas();
});
btnFecharMesas.addEventListener("click", () => {
    modalMesas.classList.add("oculto");
});

formNovaMesa.addEventListener("submit", async (evento) => {
    evento.preventDefault();
    const resposta = await fetch(`${API_URL}/mesas`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ numero: Number(inputNumeroMesaNova.value) })
    });

    if (!resposta.ok) {
        const dados = await resposta.json();
        alert(dados.erro);
        return;
    }

    inputNumeroMesaNova.value = "";
    carregarListaGerenciarMesas();
    carregarMesas();
});