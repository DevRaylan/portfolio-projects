import { API_URL } from "./api.js";

const navDiasHistorico = document.getElementById("nav-dias-historico");
const listaHistorico = document.getElementById("lista-historico");

let gorjetasCarregadas = [];
let diaSelecionado = null;

function chaveDia(iso) {
    return iso ? iso.substring(0, 10) : null;
}

function formatarDiaLabel(chave) {
    const [ano, mes, dia] = chave.split("-");
    return `${dia}/${mes}/${ano}`;
}

function formatarHora(iso) {
    if (!iso) {
        return "—";
    }
    return new Date(iso).toLocaleTimeString("pt-BR", { hour: "2-digit", minute: "2-digit" });
}

export async function carregarHistorico() {
    const resposta = await fetch(`${API_URL}/gorjetas`);
    gorjetasCarregadas = await resposta.json();
    gorjetasCarregadas.sort((a, b) => new Date(b.dataFechamento) - new Date(a.dataFechamento));

    const dias = [...new Set(gorjetasCarregadas
        .map(g => chaveDia(g.dataFechamento))
        .filter(d => d !== null))]
        .sort()
        .reverse();

    if (!diaSelecionado || !dias.includes(diaSelecionado)) {
        diaSelecionado = dias[0] ?? null;
    }

    renderizarNavDias(dias);
    renderizarLista();
}

function renderizarNavDias(dias) {
    navDiasHistorico.innerHTML = "";

    dias.forEach(dia => {
        const botao = document.createElement("button");
        botao.type = "button";
        botao.textContent = formatarDiaLabel(dia);
        if (dia === diaSelecionado) {
            botao.classList.add("selecionado");
        }
        botao.onclick = () => {
            diaSelecionado = dia;
            renderizarNavDias(dias);
            renderizarLista();
        };
        navDiasHistorico.appendChild(botao);
    });
}

function renderizarLista() {
    listaHistorico.innerHTML = "";

    const gorjetasDoDia = gorjetasCarregadas.filter(g => chaveDia(g.dataFechamento) === diaSelecionado);

    gorjetasDoDia.forEach(gorjeta => {
        const item = document.createElement("li");
        item.className = "item-historico";
        item.innerHTML = `
            <div class="item-historico-topo">
                <strong>Mesa ${gorjeta.mesa.numero}</strong>
                <span>${gorjeta.atendente.nome}</span>
            </div>
            <div class="item-historico-valores">
                R$ ${gorjeta.valorConta} (${gorjeta.percentual}%) → <strong>R$ ${gorjeta.valorGorjeta}</strong>
            </div>
            <div class="item-historico-horario">
                ${formatarHora(gorjeta.dataAbertura)} — ${formatarHora(gorjeta.dataFechamento)}
            </div>
        `;
        listaHistorico.appendChild(item);
    });
}