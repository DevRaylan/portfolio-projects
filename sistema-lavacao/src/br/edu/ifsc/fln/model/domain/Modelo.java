/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package br.edu.ifsc.fln.model.domain;

/**
 *
 * @author d.raylan
 */
public class Modelo {
    private int id;
    private String descricao;
    private Marca marca;
    private ECategoria ecategoria;
    private Motor motor;
    
     public Modelo()
    {
           this.createMotor();
           this.id = 0;
           this.descricao = null;
           this.marca= null;
    }

        
    private void createMotor() {
        //associação bidirecional - define o estoque do produto
        this.motor = new Motor();
        //atribui o produto ao estoque
        this.motor.setModelo(this);
    } 

    public ECategoria getEcategoria() {
        return ecategoria;
    }

    public void setEcategoria(ECategoria ecategoria) {
        this.ecategoria = ecategoria;
    }

    public Motor getMotor() {
        return motor;
    }

    public void setMotor(Motor motor) {
        this.motor = motor;
    }
    
   
    
    public Modelo(String descricao, Marca marca)
    {
           this.id = 0;
           this.descricao = descricao;
           this.marca= marca;
    }
    
    public Modelo(int id, String descricao, Marca marca)
    {
           this.id = id;
           this.descricao = descricao;
           this.marca= marca;
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

    public Marca getMarca() {
        return marca;
    }

    public void setMarca(Marca marca) {
        this.marca = marca;
    }

    public ECategoria getECategoria() {
        return ecategoria;
    }

    public void setECategoria(ECategoria ecategoria) {
        this.ecategoria = ecategoria;
    }

    @Override
    public String toString() {
        return this.descricao;
    }

    
}

