package com.raylan.sistemabancario.dto;

import com.raylan.sistemabancario.model.Transacao;

import java.time.LocalDateTime;

public record TransacaoResponse(String tipo, double valor, LocalDateTime data) {
    public static TransacaoResponse from(Transacao transacao) {
        return new TransacaoResponse(
                transacao.getTipo().name(),
                transacao.getValor(),
                transacao.getData()
        );
    }
}