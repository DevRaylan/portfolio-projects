package com.raylan.calculadoragorjetas.controller;

import java.util.List;

import jakarta.validation.Valid;
import jakarta.validation.constraints.NotBlank;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PatchMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
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
        return atendenteService.listarAtivos();
    }

    @GetMapping("/todos")
    public List<Atendente> listarTodos() {
        return atendenteService.listarTodos();
    }

    @PutMapping("/{id}")
    public Atendente editar(@PathVariable Long id, @Valid @RequestBody NovoAtendenteRequest request) {
        return atendenteService.editar(id, request.nome());
    }

    @PatchMapping("/{id}/desativar")
    public Atendente desativar(@PathVariable Long id) {
        return atendenteService.desativar(id);
    }

    @PatchMapping("/{id}/reativar")
    public Atendente reativar(@PathVariable Long id) {
        return atendenteService.reativar(id);
    }

    public record NovoAtendenteRequest(
            @NotBlank(message = "Nome é obrigatório") String nome) {
    }
}