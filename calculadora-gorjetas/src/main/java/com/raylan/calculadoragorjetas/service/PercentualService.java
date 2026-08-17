package com.raylan.calculadoragorjetas.service;

import com.raylan.calculadoragorjetas.model.Percentual;
import com.raylan.calculadoragorjetas.repository.PercentualRepository;
import org.springframework.stereotype.Service;

import java.math.BigDecimal;
import java.util.List;
import java.util.NoSuchElementException;

@Service
public class PercentualService {

    private final PercentualRepository percentualRepository;

    public PercentualService(PercentualRepository percentualRepository) {
        this.percentualRepository = percentualRepository;
    }

    public Percentual cadastrar(BigDecimal valor) {
        Percentual percentual = new Percentual();
        percentual.setValor(valor);
        return percentualRepository.save(percentual);
    }

    public List<Percentual> listarAtivos() {
        return percentualRepository.findByAtivoTrue();
    }

    public List<Percentual> listarTodos() {
        return percentualRepository.findAll();
    }

    public Percentual editar(Long id, BigDecimal novoValor) {
        Percentual percentual = buscarPorId(id);
        percentual.setValor(novoValor);
        return percentualRepository.save(percentual);
    }

    public Percentual desativar(Long id) {
        Percentual percentual = buscarPorId(id);
        percentual.setAtivo(false);
        return percentualRepository.save(percentual);
    }

    public Percentual reativar(Long id) {
        Percentual percentual = buscarPorId(id);
        percentual.setAtivo(true);
        return percentualRepository.save(percentual);
    }

    private Percentual buscarPorId(Long id) {
        return percentualRepository.findById(id)
                .orElseThrow(() -> new NoSuchElementException("Percentual não encontrado: " + id));
    }
}