package com.raylan.sistemabancario.repository;

import com.raylan.sistemabancario.model.Conta;

import java.util.List;
import java.util.Optional;

public interface ContaRepository {
    Conta salvar(Conta conta);
    Optional<Conta> buscarPorNumero(int numero);
    List<Conta> listarTodas();
}