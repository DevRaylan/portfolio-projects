package com.raylan.calculadoragorjetas.controller;

import java.util.List;

import jakarta.validation.Valid;
import jakarta.validation.constraints.NotBlank;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import com.raylan.calculadoragorjetas.model.Atendente;
import com.raylan.calculadoragorjetas.service.AtendenteService;

@RestController
@RequestMapping("/atendentes")
public class AtendenteController {
    private final AtendenteService atendenteService;

    public AtendenteController(AtendenteService atendenteService) {
        this.atendenteService = atendenteService;
    }

    @PostMapping
    public Atendente cadastrar(@Valid @RequestBody NovoAtendenteRequest request) {
        return atendenteService.cadastrar(request.nome());
    }

    @GetMapping
    public List<Atendente> listar() {
        return atendenteService.listarTodos();
    }

    public record NovoAtendenteRequest(
            @NotBlank(message = "Nome é obrigatório") String nome) {
    }
}
