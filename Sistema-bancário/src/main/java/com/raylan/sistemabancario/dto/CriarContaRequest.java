package com.raylan.sistemabancario.dto;

import com.raylan.sistemabancario.model.TipoConta;

public record CriarContaRequest(String nome, String cpf, TipoConta tipo) {}