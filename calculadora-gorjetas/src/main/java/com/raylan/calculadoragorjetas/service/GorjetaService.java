package com.raylan.calculadoragorjetas.service;

import com.raylan.calculadoragorjetas.model.Atendente;
import com.raylan.calculadoragorjetas.model.Gorjeta;
import com.raylan.calculadoragorjetas.model.Mesa;
import com.raylan.calculadoragorjetas.repository.AtendenteRepository;
import com.raylan.calculadoragorjetas.repository.GorjetaRepository;
import com.raylan.calculadoragorjetas.repository.MesaRepository;
import org.springframework.stereotype.Service;

import java.math.BigDecimal;
import java.math.RoundingMode;
import java.time.LocalDateTime;
import java.util.List;
import java.util.NoSuchElementException;

@Service
public class GorjetaService {

    private final GorjetaRepository gorjetaRepository;
    private final AtendenteRepository atendenteRepository;
    private final MesaRepository mesaRepository;

    public GorjetaService(GorjetaRepository gorjetaRepository, AtendenteRepository atendenteRepository,
            MesaRepository mesaRepository) {
        this.gorjetaRepository = gorjetaRepository;
        this.atendenteRepository = atendenteRepository;
        this.mesaRepository = mesaRepository;
    }

    public Gorjeta calcular(Long atendenteId, BigDecimal valorConta, BigDecimal percentual, Long mesaId) {
        Atendente atendente = atendenteRepository.findById(atendenteId)
                .orElseThrow(() -> new NoSuchElementException("Atendente não encontrado: " + atendenteId));

        Mesa mesa = mesaRepository.findById(mesaId)
                .orElseThrow(() -> new NoSuchElementException("Mesa não encontrada: " + mesaId));

        BigDecimal valorGorjeta = valorConta
                .multiply(percentual)
                .divide(BigDecimal.valueOf(100), 2, RoundingMode.HALF_UP);

        Gorjeta gorjeta = new Gorjeta();
        gorjeta.setAtendente(atendente);
        gorjeta.setValorConta(valorConta);
        gorjeta.setPercentual(percentual);
        gorjeta.setValorGorjeta(valorGorjeta);
        gorjeta.setDataAbertura(mesa.getDataAbertura());
        gorjeta.setDataFechamento(LocalDateTime.now());
        gorjeta.setMesa(mesa);

        Gorjeta salva = gorjetaRepository.save(gorjeta);

        mesa.setAtendenteAtual(null);
        mesa.setDataAbertura(null);
        mesaRepository.save(mesa);

        return salva;
    }
    public List<Gorjeta> historicoPorAtendente(Long atendenteId) {
        return gorjetaRepository.findByAtendenteId(atendenteId);
    }
    public List<Gorjeta> listarTodas() {
        return gorjetaRepository.findAll();
    }
}