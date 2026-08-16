package com.raylan.calculadoragorjetas.model;

import java.math.BigDecimal;
import java.time.LocalDateTime;

import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.ManyToOne;


@Entity
public class Gorjeta {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    private BigDecimal valorConta;
    private BigDecimal percentual;
    private BigDecimal valorGorjeta;
    private LocalDateTime dataHora;

    @ManyToOne
    private Atendente atendente;

    public Long getId() {
        return id;
    }
    public BigDecimal getValorConta() {
        return valorConta;
    }
    public void setValorConta(BigDecimal valorConta) {
        this.valorConta = valorConta;
    }
    public BigDecimal getPercentual() {
        return percentual;
    }
    public void setPercentual(BigDecimal percentual) {
        this.percentual = percentual;
    }
    public BigDecimal getValorGorjeta() {
        return valorGorjeta;
    }
    public void setValorGorjeta(BigDecimal valorGorjeta) {
        this.valorGorjeta = valorGorjeta;
    }
    public LocalDateTime getDataHora() {
        return dataHora;
    }
    public void setDataHora(LocalDateTime dataHora) {
        this.dataHora = dataHora;
    }
    public Atendente getAtendente() {
        return atendente;
    }
    public void setAtendente(Atendente atendente) {
        this.atendente = atendente;
    }
}
