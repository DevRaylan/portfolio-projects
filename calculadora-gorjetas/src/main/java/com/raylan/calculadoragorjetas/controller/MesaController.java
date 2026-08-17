package com.raylan.calculadoragorjetas.controller;

import java.util.List;

import jakarta.validation.Valid;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.Positive;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PatchMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import com.raylan.calculadoragorjetas.model.Mesa;
import com.raylan.calculadoragorjetas.service.MesaService;

@RestController
@RequestMapping("/mesas")
public class MesaController {
    private final MesaService mesaService;

    public MesaController(MesaService mesaService) {
        this.mesaService = mesaService;
    }

    @PostMapping
    public Mesa cadastrar(@Valid @RequestBody NovaMesaRequest request) {
        return mesaService.cadastrar(request.numero());
    }

    @GetMapping
    public List<Mesa> listar() {
        return mesaService.listarAtivas();
    }

    @GetMapping("/todos")
    public List<Mesa> listarTodas() {
        return mesaService.listarTodas();
    }

    @PutMapping("/{id}")
    public Mesa editar(@PathVariable Long id, @Valid @RequestBody NovaMesaRequest request) {
        return mesaService.editar(id, request.numero());
    }

    @PatchMapping("/{id}/desativar")
    public Mesa desativar(@PathVariable Long id) {
        return mesaService.desativar(id);
    }

    @PatchMapping("/{id}/reativar")
    public Mesa reativar(@PathVariable Long id) {
        return mesaService.reativar(id);
    }

    @PatchMapping("/{id}/abrir")
    public Mesa abrir(@PathVariable Long id, @Valid @RequestBody AbrirMesaRequest request) {
        return mesaService.abrir(id, request.atendenteId());
    }

    @PatchMapping("/{id}/trocar-atendente")
    public Mesa trocarAtendente(@PathVariable Long id, @Valid @RequestBody TrocarAtendenteRequest request) {
        return mesaService.trocarAtendente(id, request.novoAtendenteId(), request.motivo());
    }

    public record NovaMesaRequest(
            @NotNull(message = "Número da mesa é obrigatório") @Positive(message = "Número da mesa deve ser maior que zero") Integer numero) {
    }

    public record AbrirMesaRequest(
            @NotNull(message = "Atendente é obrigatório") Long atendenteId) {
    }

    public record TrocarAtendenteRequest(
            @NotNull(message = "Novo atendente é obrigatório") Long novoAtendenteId,
            @NotBlank(message = "Motivo é obrigatório") String motivo) {
    }
}