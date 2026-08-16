const API_URL = "http://localhost:8080";

const form = document.getElementById("form-atendente");
const inputNome = document.getElementById("input-nome");
const formGorjeta = document.getElementById("form-gorjeta");
const inputValorConta = document.getElementById("input-valor-conta");
const inputPercentual = document.getElementById("input-percentual");
const resultadoGorjeta = document.getElementById("resultado-gorjeta");
const listaHistorico = document.getElementById("lista-historico");
const botoesAtendente = document.getElementById("botoes-atendente");
const nomeAtendenteSelecionado = document.getElementById("nome-atendente-selecionado");
let atendenteSelecionadoId = null;

async function carregarAtendentes() {
    const resposta = await fetch(`${API_URL}/atendentes`);
    const atendentes = await resposta.json();

    botoesAtendente.innerHTML = "";

    atendentes.forEach(atendente => {
        const botao = document.createElement("button");
        botao.type = "button";
        botao.textContent = atendente.nome;

        if (atendente.id === atendenteSelecionadoId) {
            botao.classList.add("selecionado");
        }

        botao.addEventListener("click", () => {
            atendenteSelecionadoId = atendente.id;
            nomeAtendenteSelecionado.textContent = atendente.nome;

            document.querySelectorAll(".botoes-atendente button")
                .forEach(b => b.classList.remove("selecionado"));
            botao.classList.add("selecionado");

            carregarHistorico(atendenteSelecionadoId);
        });

        botoesAtendente.appendChild(botao);
    });
}

async function carregarHistorico(atendenteId) {
    const resposta = await fetch(`${API_URL}/gorjetas/atendente/${atendenteId}`);
    const gorjetas = await resposta.json();

    listaHistorico.innerHTML = "";
    gorjetas.forEach(gorjeta => {
        const item = document.createElement("li");
        item.textContent = `R$ ${gorjeta.valorConta} (${gorjeta.percentual}%) → R$ ${gorjeta.valorGorjeta}`;
        listaHistorico.appendChild(item);
    });
}

formGorjeta.addEventListener("submit", async (evento) => {
    evento.preventDefault();

    if (!atendenteSelecionadoId) {
        resultadoGorjeta.textContent = "Selecione um atendente primeiro.";
        return;
    }

    const resposta = await fetch(`${API_URL}/gorjetas`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            atendenteId: atendenteSelecionadoId,
            valorConta: Number(inputValorConta.value),
            percentual: Number(inputPercentual.value)
        })
    });

    const dados = await resposta.json();

    if (!resposta.ok) {
        resultadoGorjeta.textContent = `Erro: ${dados.erro}`;
        return;
    }

    resultadoGorjeta.textContent = `Gorjeta calculada: R$ ${dados.valorGorjeta}`;
    inputValorConta.value = "";
    inputPercentual.value = "";
    carregarHistorico(atendenteSelecionadoId);
});

carregarAtendentes();