package com.raylan.calculadoragorjetas.service;

import com.raylan.calculadoragorjetas.model.Atendente;
import com.raylan.calculadoragorjetas.repository.AtendenteRepository;
import org.springframework.stereotype.Service;

import java.util.List;

@Service
public class AtendenteService {

    private final AtendenteRepository atendenteRepository;

    public AtendenteService(AtendenteRepository atendenteRepository) {
        this.atendenteRepository = atendenteRepository;
    }

    public Atendente cadastrar(String nome) {
        Atendente atendente = new Atendente();
        atendente.setNome(nome);
        return atendenteRepository.save(atendente);
    }

    public List<Atendente> listarTodos() {
        return atendenteRepository.findAll();
    }
}