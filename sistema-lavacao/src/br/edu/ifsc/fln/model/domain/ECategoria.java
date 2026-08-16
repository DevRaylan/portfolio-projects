package br.edu.ifsc.fln.model.domain;

public enum ECategoria {
    PEQUENO(1, "Pequeno", "Pequeno"),
    MEDIO(2, "Médio", "Médio"),
    GRANDE(3, "Grande", "Grande"),
    MOTO(4, "Moto", "Moto"),
    PADRAO(5, "Padrão", "Padrão");

    private int id;
    private String descricao;
    private String description;

    private ECategoria(int id, String descricao, String description) {
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
