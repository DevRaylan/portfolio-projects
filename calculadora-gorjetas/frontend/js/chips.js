export function criarChips(itens, obterRotulo, aoSelecionar) {
    const container = document.createElement("div");
    container.className = "botoes-atendente";

    itens.forEach(item => {
        const chip = document.createElement("button");
        chip.type = "button";
        chip.textContent = obterRotulo(item);
        chip.onclick = () => aoSelecionar(item, chip, container);
        container.appendChild(chip);
    });

    return container;
}