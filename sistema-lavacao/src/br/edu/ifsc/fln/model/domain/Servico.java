package br.edu.ifsc.fln.model.domain;

import java.io.Serializable;
import java.math.BigDecimal;

/**
 *
 * @author d.raylan
 */

public class Servico implements Serializable {

    private int id;
    private String descricao;
    private BigDecimal valor;
    private int pontos;
    
    private ECategoria ecategoria;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getDescricao() {
        return descricao;
    }

    public void setDescricao(String descricao) {
        this.descricao = descricao;
    }

    public BigDecimal getValor() {
        return valor;
    }

    public void setValor(BigDecimal valor) {
        this.valor = valor;
    } 

    public int getPontos() {
        return pontos;
    }

    public void setPontos(int pontos) {
        this.pontos = pontos;
    }

    public ECategoria getECategoria() {
        return ecategoria;
    }

    public void setECategoria(ECategoria ecategoria) {
        this.ecategoria = ecategoria;
    }

    
    @Override
    public String toString() {
        return descricao;
    }
    
}
