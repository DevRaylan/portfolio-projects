/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/javafx/FXMLController.java to edit this template
 */
package br.edu.ifsc.fln.model.domain;

/**
 * FXML Controller class
 *
 * @author d.raylan
 */

public enum ETipoCombustivel {
    GASOLINA(1, "GASOLINA", "GASOLINA"),
    ETANOL(2, "ETANOL", "ETANOL"),
    FLEX(3, "FLEX", "FLEX"),
    DIESEL(4, "DIESEL", "DIESEL"),
    GNV(5, "GNV", "GNV"),
    OUTROS(6, "OUTROS", "OUTROS");

    private int id;
    private String descricao;
    private String description;

    private ETipoCombustivel(int id, String descricao, String description) {
        this.id = id;
        this.descricao = descricao;
        this.description = description;
    }

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

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    @Override
    public String toString() {
        return descricao;
    }
}
