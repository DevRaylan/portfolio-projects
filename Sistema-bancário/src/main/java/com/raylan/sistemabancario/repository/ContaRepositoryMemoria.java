package com.raylan.sistemabancario.repository;

import com.raylan.sistemabancario.model.Conta;
import org.springframework.stereotype.Repository;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.Optional;
import java.util.concurrent.ConcurrentHashMap;

@Repository
public class ContaRepositoryMemoria implements ContaRepository {

    private final Map<Integer, Conta> contas = new ConcurrentHashMap<>();

    @Override
    public Conta salvar(Conta conta) {
        contas.put(conta.getNumero(), conta);
        return conta;
    }

    @Override
    public Optional<Conta> buscarPorNumero(int numero) {
        return Optional.ofNullable(contas.get(numero));
    }

    @Override
    public List<Conta> listarTodas() {
        return new ArrayList<>(contas.values());
    }
}