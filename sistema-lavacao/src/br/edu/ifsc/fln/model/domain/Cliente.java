/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package br.edu.ifsc.fln.model.domain;

import java.time.LocalDate;

/**
 *
 * @author d.raylan
 */
public abstract class Cliente extends Object {

    protected int id;
    protected String nome;
    protected String fone;
    protected String email;
    private LocalDate dataCadastro;
    



    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getNome() {
        return nome;
    }

    public void setNome(String nome) {
        this.nome = nome;
    }

    public String getFone() {
        return fone;
    }

    public void setFone(String fone) {
        this.fone = fone;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public LocalDate getDataCadastro() {
        return dataCadastro;
    }

    public void setDataCadastro(LocalDate dataCadastro) {
        this.dataCadastro = dataCadastro;
    }

    

    public String getDados() {
        StringBuilder sb = new StringBuilder();
        sb.append("Dados do Cliente ").append(this.getClass().getSimpleName()).append("\n");
        sb.append("Id........: ").append(id).append("\n");
        sb.append("Nome......: ").append(nome).append("\n");
        sb.append("Fone......: ").append(fone).append("\n");
        sb.append("Email.....: ").append(email).append("\n");
        sb.append("Data do cadastro.....: ").append(dataCadastro).append("\n");
        return sb.toString();
    }

    @Override
    public String toString() {
        return nome;
    }
    
    

}
