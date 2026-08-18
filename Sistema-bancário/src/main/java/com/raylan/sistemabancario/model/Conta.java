package com.raylan.sistemabancario.model;

import com.raylan.sistemabancario.exception.SaldoInsuficienteException;
import java.util.ArrayList;
import java.util.List;

public abstract class Conta implements Transacionavel {
    private static int proximoNumero = 1000;

    protected final int numero;
    protected final Cliente titular;
    protected double saldo;
    protected final List<Transacao> historico = new ArrayList<>();

    public Conta(Cliente titular) {
        if (titular == null) {
            throw new IllegalArgumentException("Titular não pode ser nulo");
        }
        this.numero = proximoNumero++;
        this.titular = titular;
        this.saldo = 0.0;
    }

    @Override
    public void depositar(double valor) {
        if (valor <= 0) {
            throw new IllegalArgumentException("Valor de depósito deve ser positivo");
        }
        saldo += valor;
        historico.add(new Transacao(TipoTransacao.DEPOSITO, valor));
    }

    // cada subclasse decide a regra de saque — é aqui que entra o polimorfismo
    @Override
    public abstract void sacar(double valor) throws SaldoInsuficienteException;

    public int getNumero() {
        return numero;
    }

    public Cliente getTitular() {
        return titular;
    }

    public double getSaldo() {
        return saldo;
    }

    public List<Transacao> getHistorico() {
        return historico;
    }

    @Override
    public String toString() {
        return String.format("Conta %d [%s] - Titular: %s - Saldo: R$ %.2f",
                numero, getClass().getSimpleName(), titular.getNome(), saldo);
    }
}