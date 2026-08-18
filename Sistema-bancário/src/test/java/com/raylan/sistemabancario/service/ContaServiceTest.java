package com.raylan.sistemabancario.service;

import com.raylan.sistemabancario.exception.ContaInvalidaException;
import com.raylan.sistemabancario.exception.SaldoInsuficienteException;
import com.raylan.sistemabancario.model.Conta;
import com.raylan.sistemabancario.model.TipoConta;
import com.raylan.sistemabancario.repository.ContaRepositoryMemoria;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;

import static org.junit.jupiter.api.Assertions.*;

class ContaServiceTest {

    private ContaService service;

    @BeforeEach
    void setUp() {
        service = new ContaService(new ContaRepositoryMemoria());
    }

    @Test
    void deveCriarContaComSaldoZero() {
        Conta conta = service.criarConta("Ana Silva", "111.111.111-11", TipoConta.CORRENTE);
        assertEquals(0.0, conta.getSaldo());
    }

    @Test
    void deveTransferirEntreDuasContas() throws SaldoInsuficienteException {
        Conta origem = service.criarConta("Ana Silva", "111.111.111-11", TipoConta.CORRENTE);
        Conta destino = service.criarConta("João Souza", "222.222.222-22", TipoConta.POUPANCA);

        service.depositar(origem.getNumero(), 500);
        service.transferir(origem.getNumero(), destino.getNumero(), 200);

        assertEquals(300, service.consultar(origem.getNumero()).getSaldo());
        assertEquals(200, service.consultar(destino.getNumero()).getSaldo());
    }

    @Test
    void naoDeveTransferirSeOrigemNaoTemSaldoSuficiente() {
        Conta origem = service.criarConta("Ana Silva", "111.111.111-11", TipoConta.POUPANCA);
        Conta destino = service.criarConta("João Souza", "222.222.222-22", TipoConta.POUPANCA);

        assertThrows(SaldoInsuficienteException.class,
                () -> service.transferir(origem.getNumero(), destino.getNumero(), 100));

        assertEquals(0, service.consultar(destino.getNumero()).getSaldo());
    }

    @Test
    void deveLancarExcecaoAoConsultarContaInexistente() {
        assertThrows(ContaInvalidaException.class, () -> service.consultar(9999));
    }
}