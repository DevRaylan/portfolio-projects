import { API_URL } from "./api.js";
import { criarChips } from "./chips.js";
import { carregarHistorico } from "./historico.js";

const gradeMesas = document.getElementById("grade-mesas");

const modalMesaDetalhe = document.getElementById("modal-mesa-detalhe");
const btnFecharMesaDetalhe = document.getElementById("btn-fechar-mesa-detalhe");
const tituloMesaDetalhe = document.getElementById("titulo-mesa-detalhe");
const corpoMesaDetalhe = document.getElementById("corpo-mesa-detalhe");

let mesaAtual = null;

export async function carregarMesas() {
    const resposta = await fetch(`${API_URL}/mesas/todos`);
    const mesas = await resposta.json();

    gradeMesas.innerHTML = "";

    mesas.forEach(mesa => {
        const botao = document.createElement("button");
        botao.type = "button";
        botao.className = "botao-mesa";

        if (!mesa.ativa) {
            botao.classList.add("indisponivel");
        } else if (mesa.atendenteAtual) {
            botao.classList.add("ocupada");
        }

        const numero = document.createElement("span");
        numero.textContent = `Mesa ${mesa.numero}`;
        botao.appendChild(numero);

        if (mesa.ativa && mesa.atendenteAtual) {
            const nomeAtendente = document.createElement("span");
            nomeAtendente.className = "nome-atendente-mesa";
            nomeAtendente.textContent = mesa.atendenteAtual.nome;
            botao.appendChild(nomeAtendente);
        }

        if (mesa.ativa) {
            botao.onclick = () => abrirDetalheMesa(mesa);
        } else {
            botao.disabled = true;
        }

        gradeMesas.appendChild(botao);
    });
}

function abrirDetalheMesa(mesa) {
    mesaAtual = mesa;
    tituloMesaDetalhe.textContent = `Mesa ${mesa.numero}`;
    modalMesaDetalhe.classList.remove("oculto");

    if (mesa.atendenteAtual) {
        renderizarMesaOcupada();
    } else {
        renderizarMesaVazia();
    }
}

btnFecharMesaDetalhe.addEventListener("click", () => {
    modalMesaDetalhe.classList.add("oculto");
});

async function renderizarMesaVazia() {
    corpoMesaDetalhe.innerHTML = "";

    const texto = document.createElement("p");
    texto.textContent = "Escolha o atendente que vai abrir essa mesa:";
    corpoMesaDetalhe.appendChild(texto);

    const resposta = await fetch(`${API_URL}/atendentes`);
    const atendentes = await resposta.json();

    const chips = criarChips(atendentes, a => a.nome, async (atendente) => {
        await fetch(`${API_URL}/mesas/${mesaAtual.id}/abrir`, {
            method: "PATCH",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ atendenteId: atendente.id })
        });
        modalMesaDetalhe.classList.add("oculto");
        carregarMesas();
    });
    corpoMesaDetalhe.appendChild(chips);
}

function renderizarMesaOcupada() {
    corpoMesaDetalhe.innerHTML = "";

    const atual = document.createElement("p");
    atual.innerHTML = `Atendente atual: <strong>${mesaAtual.atendenteAtual.nome}</strong>`;
    corpoMesaDetalhe.appendChild(atual);

    const btnTrocar = document.createElement("button");
    btnTrocar.type = "button";
    btnTrocar.className = "botao-preenchido";
    btnTrocar.textContent = "Trocar atendente";
    btnTrocar.onclick = renderizarFormTroca;
    corpoMesaDetalhe.appendChild(btnTrocar);

        const btnCalcular = document.createElement("button");
    btnCalcular.type = "button";
    btnCalcular.className = "botao-preenchido";
    btnCalcular.textContent = "Fechar mesa";
    btnCalcular.onclick = renderizarFormCalculo;
    corpoMesaDetalhe.appendChild(btnCalcular);
}

async function renderizarFormTroca() {
    corpoMesaDetalhe.innerHTML = "";

    const texto = document.createElement("p");
    texto.textContent = "Escolha o novo atendente:";
    corpoMesaDetalhe.appendChild(texto);

    const resposta = await fetch(`${API_URL}/atendentes`);
    const atendentes = await resposta.json();

    let novoAtendenteSelecionado = null;

    const chips = criarChips(atendentes, a => a.nome, (atendente, chipClicado) => {
        novoAtendenteSelecionado = atendente;
        [...chips.children].forEach(b => b.classList.remove("selecionado"));
        chipClicado.classList.add("selecionado");
    });
    corpoMesaDetalhe.appendChild(chips);

    const inputMotivo = document.createElement("input");
    inputMotivo.type = "text";
    inputMotivo.placeholder = "Motivo da troca";
    corpoMesaDetalhe.appendChild(inputMotivo);

    const btnConfirmar = document.createElement("button");
    btnConfirmar.type = "button";
    btnConfirmar.className = "botao-preenchido";
    btnConfirmar.textContent = "Confirmar troca";
    btnConfirmar.onclick = async () => {
        if (!novoAtendenteSelecionado || !inputMotivo.value) {
            return;
        }
        await fetch(`${API_URL}/mesas/${mesaAtual.id}/trocar-atendente`, {
            method: "PATCH",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ novoAtendenteId: novoAtendenteSelecionado.id, motivo: inputMotivo.value })
        });
        modalMesaDetalhe.classList.add("oculto");
        carregarMesas();
    };
    corpoMesaDetalhe.appendChild(btnConfirmar);

    const btnVoltar = document.createElement("button");
    btnVoltar.type = "button";
    btnVoltar.className = "botao-texto";
    btnVoltar.textContent = "Voltar";
    btnVoltar.onclick = renderizarMesaOcupada;
    corpoMesaDetalhe.appendChild(btnVoltar);
}

async function renderizarFormCalculo() {
    corpoMesaDetalhe.innerHTML = "";

    const inputValorConta = document.createElement("input");
    inputValorConta.type = "number";
    inputValorConta.step = "0.01";
    inputValorConta.min = "0";
    inputValorConta.placeholder = "Valor da conta";
    corpoMesaDetalhe.appendChild(inputValorConta);

    const textoPercentual = document.createElement("p");
    textoPercentual.textContent = "Escolha o percentual:";
    corpoMesaDetalhe.appendChild(textoPercentual);

    const resposta = await fetch(`${API_URL}/percentuais`);
    const percentuais = await resposta.json();

    let percentualSelecionado = null;

    const chips = criarChips(percentuais, p => `${p.valor}%`, (percentual, chipClicado) => {
        percentualSelecionado = percentual;
        [...chips.children].forEach(b => b.classList.remove("selecionado"));
        chipClicado.classList.add("selecionado");
    });
    corpoMesaDetalhe.appendChild(chips);

    const resultado = document.createElement("p");
    corpoMesaDetalhe.appendChild(resultado);

    const btnConfirmar = document.createElement("button");
    btnConfirmar.type = "button";
    btnConfirmar.className = "botao-preenchido";
    btnConfirmar.textContent = "Fechar mesa";
    btnConfirmar.onclick = async () => {
        if (!inputValorConta.value || !percentualSelecionado) {
            return;
        }

        const respostaCalculo = await fetch(`${API_URL}/gorjetas`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                atendenteId: mesaAtual.atendenteAtual.id,
                valorConta: Number(inputValorConta.value),
                percentual: Number(percentualSelecionado.valor),
                mesaId: mesaAtual.id
            })
        });

        const dados = await respostaCalculo.json();

        if (!respostaCalculo.ok) {
            resultado.textContent = `Erro: ${dados.erro}`;
            return;
        }

        resultado.textContent = `Gorjeta calculada: R$ ${dados.valorGorjeta}`;
        carregarMesas();
        carregarHistorico();

        setTimeout(() => {
            modalMesaDetalhe.classList.add("oculto");
        }, 1500);
    };
    corpoMesaDetalhe.appendChild(btnConfirmar);

    const btnVoltar = document.createElement("button");
    btnVoltar.type = "button";
    btnVoltar.className = "botao-texto";
    btnVoltar.textContent = "Voltar";
    btnVoltar.onclick = renderizarMesaOcupada;
    corpoMesaDetalhe.appendChild(btnVoltar);
}