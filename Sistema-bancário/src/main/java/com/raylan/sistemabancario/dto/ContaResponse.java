package com.raylan.sistemabancario.dto;

import com.raylan.sistemabancario.model.Conta;

import java.util.List;

public record ContaResponse(int numero, String tipo, String titular, double saldo, List<TransacaoResponse> historico) {
    public static ContaResponse from(Conta conta) {
        return new ContaResponse(
                conta.getNumero(),
                conta.getClass().getSimpleName(),
                conta.getTitular().getNome(),
                conta.getSaldo(),
                conta.getHistorico().stream().map(TransacaoResponse::from).toList()
        );
    }
}