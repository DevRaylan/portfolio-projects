package com.raylan.calculadoragorjetas.controller;

import java.math.BigDecimal;
import java.util.List;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import com.raylan.calculadoragorjetas.model.Gorjeta;
import com.raylan.calculadoragorjetas.service.GorjetaService;

import jakarta.validation.Valid;
import jakarta.validation.constraints.DecimalMax;
import jakarta.validation.constraints.DecimalMin;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.DecimalMax;
import jakarta.validation.constraints.DecimalMin;

@RestController
@RequestMapping("/gorjetas")
public class GorjetaController {
    private final GorjetaService gorjetaService;

    public GorjetaController(GorjetaService gorjetaService) {
        this.gorjetaService = gorjetaService;
    }

    @PostMapping
    public Gorjeta calcular(@Valid @RequestBody CalcularGorjetaRequest request) {
        return gorjetaService.calcular(request.atendenteId(), request.valorConta(), request.percentual());
    }

    @GetMapping("/atendente/{atendenteId}")
    public List<Gorjeta> historico(@PathVariable Long atendenteId) {
        return gorjetaService.historicoPorAtendente(atendenteId);
    }

    public record CalcularGorjetaRequest(
            @NotNull(message = "Atendente é obrigatório") Long atendenteId,

            @NotNull(message = "Valor da conta é obrigatório") @DecimalMin(value = "0.0", inclusive = false, message = "Valor da conta deve ser maior que zero") BigDecimal valorConta,

            @NotNull(message = "Percentual é obrigatório") @DecimalMin(value = "0.0", inclusive = false, message = "Percentual deve ser maior que zero") @DecimalMax(value = "100.0", message = "Percentual não pode ser maior que 100") BigDecimal percentual) {
    }

}
