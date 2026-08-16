/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package br.edu.ifsc.fln.model.domain;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;

/**
 *
 * @author d.rayan
 */
public class OrdemServico {

    private int id;
    private LocalDate agenda;
    private BigDecimal total;
    private double taxaDesconto;

    private EStatus eStatus;
    private Veiculo veiculo;
    private ItemOs itemOs;

    public ItemOs getItemOs() {
        return itemOs;
    }

    public void setItemOs(ItemOs itemOs) {
        this.itemOs = itemOs;
    }

    private List<ItemOs> itensOs;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }
    
     public String getObservacao() {
        if (itemOs != null) {
            return itemOs.getObservacao();
        }
        return null;
    }

    public void setObservacao(String observacao) {
        if (itemOs != null) {
            itemOs.setObservacao(observacao);
        }
    }

    public String getPlacaVeiculo() {
        if (veiculo != null) {
            return veiculo.getPlaca();
        }
        return null;
    }
    
    public void setPlacaVeiculo(String placaVeiculo) {
        if (veiculo != null) {
            veiculo.setPlaca(placaVeiculo);
        }
    }

    public Veiculo getVeiculo() {
        return veiculo;
    }

    public void setVeiculo(Veiculo veiculo) {
        this.veiculo = veiculo;
    }

    public LocalDate getData() {
        return agenda;
    }

    public void setData(LocalDate agenda) {
        this.agenda = agenda;
    }

    public BigDecimal getTotal() {
        return total;
    }

    public void setTotal(BigDecimal total) {
        this.total = total;
    }

    public double getTaxaDesconto() {
        return taxaDesconto;
    }

    public void setTaxaDesconto(double taxaDesconto) {
        this.taxaDesconto = taxaDesconto;
    }

    public EStatus getEStatus() {
        return eStatus;
    }

    public void setEStatus(EStatus eStatus) {
        this.eStatus = eStatus;
    }

    public List<ItemOs> getItensDeOs() {
        return itensOs;
    }

    public void setItensDeOs(List<ItemOs> itensOs) {
        this.itensOs = itensOs;
        calcularTotalOs(); // Recalcula o total ao definir a lista de itens
    }

    public void add(ItemOs itemOs) {
        if (itensOs == null) {
            itensOs = new ArrayList<>();
        }
        itensOs.add(itemOs);
        itemOs.setOrdemServico(this);
        calcularTotalOs(); // Chama o cálculo do total após adicionar um item
    }

    public void remove(ItemOs itemOS) {
        itensOs.remove(itemOS);
        calcularTotalOs(); // Chama o cálculo do total após remover um item
    }

    private void calcularTotalOs() {
        total = BigDecimal.ZERO; // Inicializa com zero
        if (itensOs != null && !itensOs.isEmpty()) {
            for (ItemOs itemOs : itensOs) {
                total = total.add(itemOs.getValor());
            }
        }

        if (taxaDesconto >= 0 && taxaDesconto <= 100) {
            BigDecimal desconto = total.multiply(BigDecimal.valueOf(taxaDesconto / 100.0));
            total = total.subtract(desconto);
        }
    }
}
