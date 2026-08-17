package com.raylan.calculadoragorjetas.model;

import java.time.LocalDateTime;

import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.ManyToOne;

@Entity
public class TrocaAtendente {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne
    private Mesa mesa;

    @ManyToOne
    private Atendente atendenteAnterior;

    @ManyToOne
    private Atendente atendenteNovo;

    private String motivo;
    private LocalDateTime dataHora;

    public Long getId() {
        return id;
    }
    public Mesa getMesa() {
        return mesa;
    }
    public void setMesa(Mesa mesa) {
        this.mesa = mesa;
    }
    public Atendente getAtendenteAnterior() {
        return atendenteAnterior;
    }
    public void setAtendenteAnterior(Atendente atendenteAnterior) {
        this.atendenteAnterior = atendenteAnterior;
    }
    public Atendente getAtendenteNovo() {
        return atendenteNovo;
    }
    public void setAtendenteNovo(Atendente atendenteNovo) {
        this.atendenteNovo = atendenteNovo;
    }
    public String getMotivo() {
        return motivo;
    }
    public void setMotivo(String motivo) {
        this.motivo = motivo;
    }
    public LocalDateTime getDataHora() {
        return dataHora;
    }
    public void setDataHora(LocalDateTime dataHora) {
        this.dataHora = dataHora;
    }
}