package com.raylan.calculadoragorjetas.service;

import com.raylan.calculadoragorjetas.model.Atendente;
import com.raylan.calculadoragorjetas.model.Mesa;
import com.raylan.calculadoragorjetas.model.TrocaAtendente;
import com.raylan.calculadoragorjetas.repository.AtendenteRepository;
import com.raylan.calculadoragorjetas.repository.MesaRepository;
import com.raylan.calculadoragorjetas.repository.TrocaAtendenteRepository;
import org.springframework.stereotype.Service;

import java.time.LocalDateTime;
import java.util.List;
import java.util.NoSuchElementException;

@Service
public class MesaService {

    private final MesaRepository mesaRepository;
    private final AtendenteRepository atendenteRepository;
    private final TrocaAtendenteRepository trocaAtendenteRepository;

    public MesaService(MesaRepository mesaRepository, AtendenteRepository atendenteRepository,
            TrocaAtendenteRepository trocaAtendenteRepository) {
        this.mesaRepository = mesaRepository;
        this.atendenteRepository = atendenteRepository;
        this.trocaAtendenteRepository = trocaAtendenteRepository;
    }

    public Mesa cadastrar(Integer numero) {
        if (mesaRepository.existsByNumero(numero)) {
            throw new IllegalArgumentException("Já existe uma mesa com o número " + numero);
        }
        Mesa mesa = new Mesa();
        mesa.setNumero(numero);
        return mesaRepository.save(mesa);
    }

        public List<Mesa> listarAtivas() {
        return mesaRepository.findByAtivaTrueOrderByNumeroAsc();
    }

    public List<Mesa> listarTodas() {
        return mesaRepository.findAllByOrderByNumeroAsc();
    }

    public Mesa editar(Long id, Integer novoNumero) {
        if (mesaRepository.existsByNumeroAndIdNot(novoNumero, id)) {
            throw new IllegalArgumentException("Já existe uma mesa com o número " + novoNumero);
        }
        Mesa mesa = buscarPorId(id);
        mesa.setNumero(novoNumero);
        return mesaRepository.save(mesa);
    }

    public Mesa desativar(Long id) {
        Mesa mesa = buscarPorId(id);
        mesa.setAtiva(false);
        return mesaRepository.save(mesa);
    }

    public Mesa reativar(Long id) {
        Mesa mesa = buscarPorId(id);
        mesa.setAtiva(true);
        return mesaRepository.save(mesa);
    }

    public Mesa abrir(Long id, Long atendenteId) {
        Mesa mesa = buscarPorId(id);
        Atendente atendente = atendenteRepository.findById(atendenteId)
                .orElseThrow(() -> new NoSuchElementException("Atendente não encontrado: " + atendenteId));
        mesa.setAtendenteAtual(atendente);
        mesa.setDataAbertura(LocalDateTime.now());
        return mesaRepository.save(mesa);
    }

    public Mesa trocarAtendente(Long id, Long novoAtendenteId, String motivo) {
        Mesa mesa = buscarPorId(id);
        Atendente atendenteNovo = atendenteRepository.findById(novoAtendenteId)
                .orElseThrow(() -> new NoSuchElementException("Atendente não encontrado: " + novoAtendenteId));

        TrocaAtendente troca = new TrocaAtendente();
        troca.setMesa(mesa);
        troca.setAtendenteAnterior(mesa.getAtendenteAtual());
        troca.setAtendenteNovo(atendenteNovo);
        troca.setMotivo(motivo);
        troca.setDataHora(LocalDateTime.now());
        trocaAtendenteRepository.save(troca);

        mesa.setAtendenteAtual(atendenteNovo);
        return mesaRepository.save(mesa);
    }

    private Mesa buscarPorId(Long id) {
        return mesaRepository.findById(id)
                .orElseThrow(() -> new NoSuchElementException("Mesa não encontrada: " + id));
    }
}