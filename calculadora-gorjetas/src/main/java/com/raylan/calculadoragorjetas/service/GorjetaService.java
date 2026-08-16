package com.raylan.calculadoragorjetas.service;

import com.raylan.calculadoragorjetas.model.Atendente;
import com.raylan.calculadoragorjetas.model.Gorjeta;
import com.raylan.calculadoragorjetas.repository.AtendenteRepository;
import com.raylan.calculadoragorjetas.repository.GorjetaRepository;
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

    public GorjetaService(GorjetaRepository gorjetaRepository, AtendenteRepository atendenteRepository) {
        this.gorjetaRepository = gorjetaRepository;
        this.atendenteRepository = atendenteRepository;
    }

    public Gorjeta calcular(Long atendenteId, BigDecimal valorConta, BigDecimal percentual) {
        Atendente atendente = atendenteRepository.findById(atendenteId)
                .orElseThrow(() -> new NoSuchElementException("Atendente não encontrado: " + atendenteId));

        BigDecimal valorGorjeta = valorConta
                .multiply(percentual)
                .divide(BigDecimal.valueOf(100), 2, RoundingMode.HALF_UP);

        Gorjeta gorjeta = new Gorjeta();
        gorjeta.setAtendente(atendente);
        gorjeta.setValorConta(valorConta);
        gorjeta.setPercentual(percentual);
        gorjeta.setValorGorjeta(valorGorjeta);
        gorjeta.setDataHora(LocalDateTime.now());

        return gorjetaRepository.save(gorjeta);
    }

    public List<Gorjeta> historicoPorAtendente(Long atendenteId) {
        return gorjetaRepository.findByAtendenteId(atendenteId);
    }
}