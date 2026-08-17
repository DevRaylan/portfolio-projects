import { API_URL } from "./api.js";

const btnAbrirPercentuais = document.getElementById("btn-abrir-percentuais");
const btnFecharPercentuais = document.getElementById("btn-fechar-percentuais");
const modalPercentuais = document.getElementById("modal-percentuais");
const formNovoPercentual = document.getElementById("form-percentual");
const inputValorPercentualNovo = document.getElementById("input-valor-percentual-novo");
const listaGerenciarPercentuais = document.getElementById("lista-gerenciar-percentuais");

async function carregarListaGerenciarPercentuais() {
    const resposta = await fetch(`${API_URL}/percentuais/todos`);
    const percentuais = await resposta.json();

    listaGerenciarPercentuais.innerHTML = "";

    percentuais.forEach(percentual => {
        const item = document.createElement("li");
        item.className = "item-gerenciar-atendente";

        const valorSpan = document.createElement("span");
        valorSpan.textContent = `${percentual.valor}%`;
        if (!percentual.ativo) {
            valorSpan.classList.add("inativo");
        }

        const btnEditar = document.createElement("button");
        btnEditar.type = "button";
        btnEditar.textContent = "Editar";
        btnEditar.onclick = () => {
            const inputEdicao = document.createElement("input");
            inputEdicao.type = "number";
            inputEdicao.step = "0.01";
            inputEdicao.value = percentual.valor;
            item.replaceChild(inputEdicao, valorSpan);
            inputEdicao.focus();

            btnEditar.textContent = "Salvar";
            btnEditar.onclick = async () => {
                await fetch(`${API_URL}/percentuais/${percentual.id}`, {
                    method: "PUT",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ valor: Number(inputEdicao.value) })
                });
                carregarListaGerenciarPercentuais();
            };
        };

        const btnStatus = document.createElement("button");
        btnStatus.type = "button";
        btnStatus.textContent = percentual.ativo ? "Desativar" : "Reativar";
        btnStatus.onclick = async () => {
            const acao = percentual.ativo ? "desativar" : "reativar";
            await fetch(`${API_URL}/percentuais/${percentual.id}/${acao}`, { method: "PATCH" });
            carregarListaGerenciarPercentuais();
        };

        item.appendChild(valorSpan);
        item.appendChild(btnEditar);
        item.appendChild(btnStatus);
        listaGerenciarPercentuais.appendChild(item);
    });
}

btnAbrirPercentuais.addEventListener("click", () => {
    modalPercentuais.classList.remove("oculto");
    carregarListaGerenciarPercentuais();
});
btnFecharPercentuais.addEventListener("click", () => {
    modalPercentuais.classList.add("oculto");
});

formNovoPercentual.addEventListener("submit", async (evento) => {
    evento.preventDefault();
    await fetch(`${API_URL}/percentuais`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ valor: Number(inputValorPercentualNovo.value) })
    });
    inputValorPercentualNovo.value = "";
    carregarListaGerenciarPercentuais();
});