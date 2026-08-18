package com.raylan.sistemabancario.model;

import com.raylan.sistemabancario.exception.SaldoInsuficienteException;

public class ContaPoupanca extends Conta {
    private static final double TAXA_JUROS_MENSAL = 0.005;

    public ContaPoupanca(Cliente titular) {
        super(titular);
    }

    @Override
    public void sacar(double valor) throws SaldoInsuficienteException {
        validarValorPositivo(valor);
        if (valor > saldo) {
            throw new SaldoInsuficienteException("Poupança não permite saldo negativo");
        }
        registrarSaque(valor);
    }

    public void renderJuros() {
        saldo += saldo * TAXA_JUROS_MENSAL;
    }
}