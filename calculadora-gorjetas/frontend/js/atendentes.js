import { API_URL } from "./api.js";

const btnAbrirAtendentes = document.getElementById("btn-abrir-atendentes");
const btnFecharAtendentes = document.getElementById("btn-fechar-atendentes");
const modalAtendentes = document.getElementById("modal-atendentes");
const formNovoAtendente = document.getElementById("form-atendente");
const inputNome = document.getElementById("input-nome");
const listaGerenciarAtendentes = document.getElementById("lista-gerenciar-atendentes");

async function carregarListaGerenciarAtendentes() {
    const resposta = await fetch(`${API_URL}/atendentes/todos`);
    const atendentes = await resposta.json();

    listaGerenciarAtendentes.innerHTML = "";

    atendentes.forEach(atendente => {
        const item = document.createElement("li");
        item.className = "item-gerenciar-atendente";

        const nomeSpan = document.createElement("span");
        nomeSpan.textContent = atendente.nome;
        if (!atendente.ativo) {
            nomeSpan.classList.add("inativo");
        }

        const btnEditar = document.createElement("button");
        btnEditar.type = "button";
        btnEditar.textContent = "Editar";
        btnEditar.onclick = () => {
            const inputEdicao = document.createElement("input");
            inputEdicao.type = "text";
            inputEdicao.value = atendente.nome;
            item.replaceChild(inputEdicao, nomeSpan);
            inputEdicao.focus();

            btnEditar.textContent = "Salvar";
            btnEditar.onclick = async () => {
                await fetch(`${API_URL}/atendentes/${atendente.id}`, {
                    method: "PUT",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ nome: inputEdicao.value })
                });
                carregarListaGerenciarAtendentes();
            };
        };

        const btnStatus = document.createElement("button");
        btnStatus.type = "button";
        btnStatus.textContent = atendente.ativo ? "Desativar" : "Reativar";
        btnStatus.onclick = async () => {
            const acao = atendente.ativo ? "desativar" : "reativar";
            await fetch(`${API_URL}/atendentes/${atendente.id}/${acao}`, { method: "PATCH" });
            carregarListaGerenciarAtendentes();
        };

        item.appendChild(nomeSpan);
        item.appendChild(btnEditar);
        item.appendChild(btnStatus);
        listaGerenciarAtendentes.appendChild(item);
    });
}

btnAbrirAtendentes.addEventListener("click", () => {
    modalAtendentes.classList.remove("oculto");
    carregarListaGerenciarAtendentes();
});
btnFecharAtendentes.addEventListener("click", () => {
    modalAtendentes.classList.add("oculto");
});

formNovoAtendente.addEventListener("submit", async (evento) => {
    evento.preventDefault();
    await fetch(`${API_URL}/atendentes`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ nome: inputNome.value })
    });
    inputNome.value = "";
    carregarListaGerenciarAtendentes();
});