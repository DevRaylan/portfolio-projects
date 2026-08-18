package com.raylan.sistemabancario.model;

import com.raylan.sistemabancario.exception.SaldoInsuficienteException;
import org.junit.jupiter.api.Test;

import java.util.List;

import static org.junit.jupiter.api.Assertions.*;

class ContaTest {

    @Test
    void deveDemonstrarPolimorfismoNoSaque() throws SaldoInsuficienteException {
        Cliente ana = new Cliente("Ana Silva", "111.111.111-11");
        Cliente joao = new Cliente("João Souza", "222.222.222-22");

        ContaCorrente cc = new ContaCorrente(ana);
        ContaPoupanca cp = new ContaPoupanca(joao);

        cc.depositar(1000);
        cp.depositar(500);

        List<Conta> contas = List.of(cc, cp);
        for (Conta conta : contas) {
            conta.sacar(100); // mesma chamada, comportamento diferente por classe concreta
        }

        assertEquals(900, cc.getSaldo());
        assertEquals(400, cp.getSaldo());
    }

    @Test
    void contaCorrenteDevePermitirChequeEspecialAteOLimite() throws SaldoInsuficienteException {
        Cliente ana = new Cliente("Ana Silva", "111.111.111-11");
        ContaCorrente cc = new ContaCorrente(ana);

        cc.sacar(300); // vai a -300, dentro do limite de 500

        assertEquals(-300, cc.getSaldo());
    }

    @Test
    void contaPoupancaNaoDevePermitirSaldoNegativo() {
        Cliente joao = new Cliente("João Souza", "222.222.222-22");
        ContaPoupanca cp = new ContaPoupanca(joao);

        assertThrows(SaldoInsuficienteException.class, () -> cp.sacar(100));
    }
}