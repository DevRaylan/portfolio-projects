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
    private LocalDateTime dataAbertura;
    private LocalDateTime dataFechamento;

    @ManyToOne
    private Atendente atendente;

    @ManyToOne
    private Mesa mesa;

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
    public LocalDateTime getDataAbertura() {
        return dataAbertura;
    }
    public void setDataAbertura(LocalDateTime dataAbertura) {
        this.dataAbertura = dataAbertura;
    }
    public LocalDateTime getDataFechamento() {
        return dataFechamento;
    }
    public void setDataFechamento(LocalDateTime dataFechamento) {
        this.dataFechamento = dataFechamento;
    }
       public Mesa getMesa() {
        return mesa;
    }
    public void setMesa(Mesa mesa) {
        this.mesa = mesa;
    }
    public Atendente getAtendente() {
        return atendente;
    }
    public void setAtendente(Atendente atendente) {
        this.atendente = atendente;
    }
}
