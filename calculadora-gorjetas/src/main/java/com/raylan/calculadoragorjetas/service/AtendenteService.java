package com.raylan.calculadoragorjetas.service;

import com.raylan.calculadoragorjetas.model.Atendente;
import com.raylan.calculadoragorjetas.repository.AtendenteRepository;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.NoSuchElementException;

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

    public List<Atendente> listarAtivos() {
        return atendenteRepository.findByAtivoTrue();
    }

    public List<Atendente> listarTodos() {
        return atendenteRepository.findAll();
    }

    public Atendente editar(Long id, String novoNome) {
        Atendente atendente = buscarPorId(id);
        atendente.setNome(novoNome);
        return atendenteRepository.save(atendente);
    }

    public Atendente desativar(Long id) {
        Atendente atendente = buscarPorId(id);
        atendente.setAtivo(false);
        return atendenteRepository.save(atendente);
    }

    public Atendente reativar(Long id) {
        Atendente atendente = buscarPorId(id);
        atendente.setAtivo(true);
        return atendenteRepository.save(atendente);
    }

    private Atendente buscarPorId(Long id) {
        return atendenteRepository.findById(id)
                .orElseThrow(() -> new NoSuchElementException("Atendente não encontrado: " + id));
    }
}