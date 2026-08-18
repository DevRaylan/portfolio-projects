package com.raylan.sistemabancario.controller;

import com.raylan.sistemabancario.dto.*;
import com.raylan.sistemabancario.exception.SaldoInsuficienteException;
import com.raylan.sistemabancario.model.Conta;
import com.raylan.sistemabancario.service.ContaService;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/contas")
public class ContaController {

    private final ContaService service;

    public ContaController(ContaService service) {
        this.service = service;
    }

    @PostMapping
    public ContaResponse criar(@RequestBody CriarContaRequest request) {
        Conta conta = service.criarConta(request.nome(), request.cpf(), request.tipo());
        return ContaResponse.from(conta);
    }

    @GetMapping
    public List<ContaResponse> listar() {
        return service.listarTodas().stream().map(ContaResponse::from).toList();
    }

    @GetMapping("/{numero}")
    public ContaResponse consultar(@PathVariable int numero) {
        return ContaResponse.from(service.consultar(numero));
    }

    @PostMapping("/{numero}/depositos")
    public ContaResponse depositar(@PathVariable int numero, @RequestBody ValorRequest request) {
        service.depositar(numero, request.valor());
        return ContaResponse.from(service.consultar(numero));
    }

    @PostMapping("/{numero}/saques")
    public ContaResponse sacar(@PathVariable int numero, @RequestBody ValorRequest request) throws SaldoInsuficienteException {
        service.sacar(numero, request.valor());
        return ContaResponse.from(service.consultar(numero));
    }

    @PostMapping("/{numero}/transferencias")
    public ContaResponse transferir(@PathVariable int numero, @RequestBody TransferenciaRequest request) throws SaldoInsuficienteException {
        service.transferir(numero, request.numeroDestino(), request.valor());
        return ContaResponse.from(service.consultar(numero));
    }
}