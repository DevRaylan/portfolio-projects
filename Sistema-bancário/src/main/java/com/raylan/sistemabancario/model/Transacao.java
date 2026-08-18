package com.raylan.sistemabancario.model;

import java.time.LocalDateTime;

public class Transacao {
    private final TipoTransacao tipo;
    private final double valor;
    private final LocalDateTime data;

    public Transacao(TipoTransacao tipo, double valor) {
        this.tipo = tipo;
        this.valor = valor;
        this.data = LocalDateTime.now();
    }

    public TipoTransacao getTipo() {
        return tipo;
    }

    public double getValor() {
        return valor;
    }

    public LocalDateTime getData() {
        return data;
    }

    @Override
    public String toString() {
        return String.format("[%s] %s: R$ %.2f", data, tipo, valor);
    }
}