package com.raylan.sistemabancario.exception;

public class ContaInvalidaException extends RuntimeException {
    public ContaInvalidaException(String mensagem) {
        super(mensagem);
    }
}