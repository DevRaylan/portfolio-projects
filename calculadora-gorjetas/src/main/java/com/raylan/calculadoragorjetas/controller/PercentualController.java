package com.raylan.calculadoragorjetas.controller;

import java.math.BigDecimal;
import java.util.List;

import jakarta.validation.Valid;
import jakarta.validation.constraints.DecimalMax;
import jakarta.validation.constraints.DecimalMin;
import jakarta.validation.constraints.NotNull;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PatchMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import com.raylan.calculadoragorjetas.model.Percentual;
import com.raylan.calculadoragorjetas.service.PercentualService;

@RestController
@RequestMapping("/percentuais")
public class PercentualController {
    private final PercentualService percentualService;

    public PercentualController(PercentualService percentualService) {
        this.percentualService = percentualService;
    }

    @PostMapping
    public Percentual cadastrar(@Valid @RequestBody PercentualRequest request) {
        return percentualService.cadastrar(request.valor());
    }

    @GetMapping
    public List<Percentual> listar() {
        return percentualService.listarAtivos();
    }

    @GetMapping("/todos")
    public List<Percentual> listarTodos() {
        return percentualService.listarTodos();
    }

    @PutMapping("/{id}")
    public Percentual editar(@PathVariable Long id, @Valid @RequestBody PercentualRequest request) {
        return percentualService.editar(id, request.valor());
    }

    @PatchMapping("/{id}/desativar")
    public Percentual desativar(@PathVariable Long id) {
        return percentualService.desativar(id);
    }

    @PatchMapping("/{id}/reativar")
    public Percentual reativar(@PathVariable Long id) {
        return percentualService.reativar(id);
    }

    public record PercentualRequest(
            @NotNull(message = "Valor é obrigatório") @DecimalMin(value = "0.0", inclusive = false, message = "Valor deve ser maior que zero") @DecimalMax(value = "100.0", message = "Valor não pode ser maior que 100") BigDecimal valor) {
    }
}