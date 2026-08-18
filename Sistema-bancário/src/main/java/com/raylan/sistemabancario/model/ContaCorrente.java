package com.raylan.sistemabancario.model;

import com.raylan.sistemabancario.exception.SaldoInsuficienteException;

public class ContaCorrente extends Conta implements Tributavel {
    private static final double LIMITE_CHEQUE_ESPECIAL = 500.0;
    private static final double TAXA_IOF = 0.01;

    public ContaCorrente(Cliente titular) {
        super(titular);
    }

    @Override
    public void sacar(double valor) throws SaldoInsuficienteException {
        validarValorPositivo(valor);
        if (saldo - valor < -LIMITE_CHEQUE_ESPECIAL) {
            throw new SaldoInsuficienteException(
                "Saldo insuficiente mesmo considerando cheque especial");
        }
        registrarSaque(valor);
    }

    @Override
    public double calcularImposto() {
        return saldo > 0 ? saldo * TAXA_IOF : 0;
    }
}