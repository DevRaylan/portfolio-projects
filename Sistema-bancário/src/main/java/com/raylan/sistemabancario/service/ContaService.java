package com.raylan.sistemabancario.service;

import com.raylan.sistemabancario.exception.ContaInvalidaException;
import com.raylan.sistemabancario.exception.SaldoInsuficienteException;
import com.raylan.sistemabancario.model.*;
import com.raylan.sistemabancario.repository.ContaRepository;
import org.springframework.stereotype.Service;

import java.util.List;

@Service
public class ContaService {

    private final ContaRepository repository;

    public ContaService(ContaRepository repository) {
        this.repository = repository;
    }

    public Conta criarConta(String nomeTitular, String cpf, TipoConta tipo) {
        Cliente titular = new Cliente(nomeTitular, cpf);
        Conta conta = switch (tipo) {
            case CORRENTE -> new ContaCorrente(titular);
            case POUPANCA -> new ContaPoupanca(titular);
        };
        titular.adicionarConta(conta);
        return repository.salvar(conta);
    }

    public void depositar(int numero, double valor) {
        buscarOuFalhar(numero).depositar(valor);
    }

    public void sacar(int numero, double valor) throws SaldoInsuficienteException {
        buscarOuFalhar(numero).sacar(valor);
    }

    public void transferir(int numeroOrigem, int numeroDestino, double valor) throws SaldoInsuficienteException {
        Conta origem = buscarOuFalhar(numeroOrigem);
        Conta destino = buscarOuFalhar(numeroDestino);
        origem.sacar(valor);
        destino.depositar(valor);
    }

    public Conta consultar(int numero) {
        return buscarOuFalhar(numero);
    }

    public List<Conta> listarTodas() {
        return repository.listarTodas();
    }

    private Conta buscarOuFalhar(int numero) {
        return repository.buscarPorNumero(numero)
                .orElseThrow(() -> new ContaInvalidaException("Conta " + numero + " não existe"));
    }
}