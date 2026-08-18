package com.raylan.sistemabancario.model;

import com.raylan.sistemabancario.exception.SaldoInsuficienteException;

public interface Transacionavel {
    void depositar(double valor);
    void sacar(double valor) throws SaldoInsuficienteException;
}