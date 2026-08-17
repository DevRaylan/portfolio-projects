package com.raylan.calculadoragorjetas.model;

import java.time.LocalDateTime;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.ManyToOne;

@Entity
public class Mesa {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @jakarta.persistence.Column(unique = true)
    private Integer numero;
    private boolean ativa = true;

    @ManyToOne
    private Atendente atendenteAtual;
    private LocalDateTime dataAbertura;

    public LocalDateTime getDataAbertura() {
        return dataAbertura;
    }
    public void setDataAbertura(LocalDateTime dataAbertura) {
        this.dataAbertura = dataAbertura;
    }
    public Long getId() {
        return id;
    }
    public Integer getNumero() {
        return numero;
    }
    public void setNumero(Integer numero) {
        this.numero = numero;
    }
    public boolean isAtiva() {
        return ativa;
    }
    public void setAtiva(boolean ativa) {
        this.ativa = ativa;
    }
    public Atendente getAtendenteAtual() {
        return atendenteAtual;
    }
    public void setAtendenteAtual(Atendente atendenteAtual) {
        this.atendenteAtual = atendenteAtual;
    }
}
