package com.raylan.sistemabancario.dto;

import com.raylan.sistemabancario.model.Conta;

public record ContaResponse(int numero, String tipo, String titular, double saldo) {
    public static ContaResponse from(Conta conta) {
        return new ContaResponse(
                conta.getNumero(),
                conta.getClass().getSimpleName(),
                conta.getTitular().getNome(),
                conta.getSaldo()
        );
    }
}