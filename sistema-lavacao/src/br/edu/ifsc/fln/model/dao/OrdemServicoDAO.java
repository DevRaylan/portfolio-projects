package br.edu.ifsc.fln.model.dao;

import br.edu.ifsc.fln.model.domain.Veiculo;
import br.edu.ifsc.fln.model.domain.EStatus;
import br.edu.ifsc.fln.model.domain.ItemOs;
import br.edu.ifsc.fln.model.domain.Servico;
import br.edu.ifsc.fln.model.domain.OrdemServico;
import java.sql.Connection;
import java.sql.Date;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author d.raylan
 */
public class OrdemServicoDAO {

    private Connection connection;

    public Connection getConnection() {
        return connection;
    }

    public void setConnection(Connection connection) {
        this.connection = connection;
    }

    public boolean inserir(OrdemServico ordemServico) {
        String sql = "INSERT INTO ordemservico(agenda, taxa_desconto, total, estatus, id_veiculo) VALUES(?,?,?,?,?)";
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            connection.setAutoCommit(false);
            stmt.setDate(1, Date.valueOf(ordemServico.getData()));
            stmt.setDouble(2, ordemServico.getTaxaDesconto());
            stmt.setBigDecimal(3, ordemServico.getTotal());

            if (ordemServico.getEStatus() != null) {
                stmt.setString(4, ordemServico.getEStatus().name());
            } else {
                stmt.setString(4, EStatus.ABERTA.name());
            }

            stmt.setInt(5, ordemServico.getVeiculo().getId());
            stmt.execute();
            ItemOsDAO itemOsDAO = new ItemOsDAO();
            itemOsDAO.setConnection(connection);
            ServicoDAO servicoDAO = new ServicoDAO();
            servicoDAO.setConnection(connection);
//            EstoqueDAO estoqueDAO = new EstoqueDAO();
            for (ItemOs itemOs : ordemServico.getItensDeOs()) {
                Servico servico = itemOs.getServico();
                itemOs.setOrdemServico(this.buscarUltimaOrdemServico());
                itemOsDAO.inserir(itemOs);
                servico.getId();
            }

            connection.commit();
            connection.setAutoCommit(true);
            return true;
        } catch (SQLException ex) {
            try {
                connection.rollback();
            } catch (SQLException ex1) {
                Logger.getLogger(OrdemServicoDAO.class.getName()).log(Level.SEVERE, null, ex1);
            }
            Logger.getLogger(OrdemServicoDAO.class.getName()).log(Level.SEVERE, null, ex);
            return false;
        } catch (Exception ex) {
            Logger.getLogger(OrdemServicoDAO.class.getName()).log(Level.SEVERE, null, ex);
            return false;
        } finally {

        }
    }

    public boolean alterar(OrdemServico ordemServico) {
        String sql = "UPDATE venda SET agenda=?, total=?, taxa_desconto=?, id_veiculo=?, estatus=? WHERE id=?";
        try {
            //antes de atualizar a nova venda, a anterior terá seus itens de venda removidos
            // e o estoque dos produtos da venda sofrerão um estorno
            connection.setAutoCommit(false);
            ItemOsDAO itemOsDAO = new ItemOsDAO();
            itemOsDAO.setConnection(connection);
            ServicoDAO servicoDAO = new ServicoDAO();
            servicoDAO.setConnection(connection);

            //Venda vendaAnterior = buscar(venda.getCdVenda());
            OrdemServico OsAnterior = buscar(ordemServico);
            List<ItemOs> itensDeOs = itemOsDAO.listarPorOrdemServico(OsAnterior);
//            for (ItemOs iv : itensDeOs) {
//                //Produto p = iv.getProduto(); //isto não da certo ...
//                Servico s = estoqueDAO.getEstoque(iv.getProduto());
//                p.getEstoque().repor(iv.getQuantidade());
//                estoqueDAO.atualizar(p.getEstoque());
//                itemDeVendaDAO.remover(iv);
//            }
            //atualiza os dados da venda
            PreparedStatement stmt = connection.prepareStatement(sql);
            stmt.setDate(1, Date.valueOf(ordemServico.getData()));
            stmt.setBigDecimal(2, ordemServico.getTotal());
            stmt.setDouble(3, ordemServico.getTaxaDesconto());
            if (ordemServico.getEStatus() != null) {
                stmt.setString(4, ordemServico.getEStatus().name());
            } else {
                stmt.setString(5, EStatus.ABERTA.name());
            }
            stmt.setInt(6, ordemServico.getVeiculo().getId());
            stmt.setInt(7, ordemServico.getId());
            stmt.execute();
            connection.commit();
            return true;
        } catch (SQLException ex) {
            try {
                connection.rollback();
            } catch (SQLException exc1) {
                Logger.getLogger(OrdemServicoDAO.class.getName()).log(Level.SEVERE, null, exc1);
            }
            Logger.getLogger(OrdemServicoDAO.class.getName()).log(Level.SEVERE, null, ex);
            return false;
        } catch (Exception ex) {
            Logger.getLogger(OrdemServicoDAO.class.getName()).log(Level.SEVERE, null, ex);
            return false;
        }
    }

    public void remover(OrdemServico ordemServico) throws SQLException {
        String sql = "DELETE FROM ordemservico WHERE id=?";

        PreparedStatement stmt = connection.prepareStatement(sql);
        connection.setAutoCommit(false);
        ItemOsDAO itemOsDAO = new ItemOsDAO();
        itemOsDAO.setConnection(connection);

        int quantidade = 0;
        if (ordemServico.getItensDeOs() != null) { // Verifica se a lista de itens não é nula
            for (ItemOs iv : ordemServico.getItensDeOs()) {
                itemOsDAO.remover(iv);
            }
        }

        stmt.setInt(1, ordemServico.getId());
        stmt.execute();
        connection.commit();
    }

    public List<OrdemServico> listar() {
        String sql = "SELECT o.id AS ordemservico_id, o.agenda AS ordemservico_agenda, o.total AS ordemservico_total, o.taxa_desconto AS ordemservico_taxa_desconto, o.estatus AS ordemservico_estatus, v.id AS veiculo_id, v.placa AS veiculo_placa, i.id AS itemos_id, i.observacao AS itemos_observacoes FROM ordemservico o INNER JOIN veiculo v ON o.id_veiculo = v.id INNER JOIN itemos i ON o.id = i.id_ordemservico;";
        List<OrdemServico> retorno = new ArrayList<>();
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            ResultSet resultado = stmt.executeQuery();
            while (resultado.next()) {
                OrdemServico ordemServico = populateVO(resultado);
                retorno.add(ordemServico);
            }
        } catch (SQLException ex) {
            Logger.getLogger(OrdemServicoDAO.class.getName()).log(Level.SEVERE, null, ex);
        }
        return retorno;
    }

    public OrdemServico buscar(OrdemServico ordemServico) {
        String sql = "SELECT * FROM ordemservico WHERE id=?";
        OrdemServico ordemservicoRetorno = new OrdemServico();
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            stmt.setInt(1, ordemServico.getId());
            ResultSet resultado = stmt.executeQuery();
            if (resultado.next()) {
                Veiculo veiculo = new Veiculo();
                ordemservicoRetorno.setId(resultado.getInt("id"));
                ordemservicoRetorno.setData(resultado.getDate("agenda").toLocalDate());
                ordemservicoRetorno.setEStatus(Enum.valueOf(EStatus.class, resultado.getString("estatus")));
                ordemservicoRetorno.setTotal(resultado.getBigDecimal("total"));
                ordemservicoRetorno.setTaxaDesconto(resultado.getDouble("taxa_desconto"));

                veiculo.setId(resultado.getInt("id_veiculo"));
                ordemservicoRetorno.setVeiculo(veiculo);
            }
        } catch (SQLException ex) {
            Logger.getLogger(OrdemServicoDAO.class.getName()).log(Level.SEVERE, null, ex);
        }
        return ordemservicoRetorno;
    }

    public OrdemServico buscar(int id) {
        /*
            Método necessário para evitar que a instância de retorno seja 
            igual a instância a ser atualizada.
         */
        String sql = "SELECT * FROM ordemservico WHERE id=?";
        OrdemServico ordemservicoRetorno = new OrdemServico();
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            stmt.setInt(1, id);
            ResultSet resultado = stmt.executeQuery();
            if (resultado.next()) {
                Veiculo veiculo = new Veiculo();
                ordemservicoRetorno.setId(resultado.getInt("numero"));
                ordemservicoRetorno.setData(resultado.getDate("agenda").toLocalDate());
                ordemservicoRetorno.setTotal(resultado.getBigDecimal("total"));
                ordemservicoRetorno.setTaxaDesconto(resultado.getDouble("taxa_desconto"));
                ordemservicoRetorno.setEStatus(Enum.valueOf(EStatus.class, resultado.getString("estatus")));
                veiculo.setId(resultado.getInt("id_veiculo"));
                ordemservicoRetorno.setVeiculo(veiculo);
            }
        } catch (SQLException ex) {
            Logger.getLogger(OrdemServicoDAO.class.getName()).log(Level.SEVERE, null, ex);
        }
        return ordemservicoRetorno;
    }

    public OrdemServico buscarUltimaOrdemServico() {
        String sql = "SELECT max(id) as max FROM ordemservico";

        OrdemServico retorno = new OrdemServico();
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            ResultSet resultado = stmt.executeQuery();

            if (resultado.next()) {
                retorno.setId(resultado.getInt("max"));
                return retorno;
            }
        } catch (SQLException ex) {
            Logger.getLogger(OrdemServicoDAO.class.getName()).log(Level.SEVERE, null, ex);
        }
        return retorno;
    }

    private OrdemServico populateVO(ResultSet rs) throws SQLException {
        OrdemServico ordemServico = new OrdemServico();
        Veiculo veiculo = new Veiculo();
        ItemOs itemOs = new ItemOs();

        ordemServico.setId(rs.getInt("ordemservico_id"));
        ordemServico.setData(rs.getDate("ordemservico_agenda").toLocalDate());
        ordemServico.setTotal(rs.getBigDecimal("ordemservico_total"));
        ordemServico.setTaxaDesconto(rs.getDouble("ordemservico_taxa_desconto"));
        ordemServico.setEStatus(Enum.valueOf(EStatus.class, rs.getString("ordemservico_estatus")));

        // Busca a placa do veículo usando o ID do veículo associado à ordem de serviço
        int veiculoId = rs.getInt("veiculo_id");
        veiculo = buscarVeiculoPorId(veiculoId); // Implemente este método

        ordemServico.setVeiculo(veiculo);
        
        int itemOsId = rs.getInt("itemOs_id");
        itemOs = buscarItemOsPorId(itemOsId); // Implemente este método

        ordemServico.setItemOs(itemOs);
        
        

        return ordemServico;
    }

    public Veiculo buscarVeiculoPorId(int veiculoId) {
        String sql = "SELECT * FROM veiculo WHERE id=?";
        Veiculo veiculo = new Veiculo();
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            stmt.setInt(1, veiculoId);
            ResultSet resultado = stmt.executeQuery();
            if (resultado.next()) {
                veiculo.setId(resultado.getInt("id"));
                veiculo.setPlaca(resultado.getString("placa"));
                // Preencha outras informações do veículo, se necessário
            }
        } catch (SQLException ex) {
            Logger.getLogger(OrdemServicoDAO.class.getName()).log(Level.SEVERE, null, ex);
        }
        return veiculo;
    }
    
    public ItemOs buscarItemOsPorId(int itemOsId) {
        String sql = "SELECT * FROM itemos WHERE id=?";
        ItemOs itemOs = new ItemOs();
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            stmt.setInt(1, itemOsId);
            ResultSet resultado = stmt.executeQuery();
            if (resultado.next()) {
                itemOs.setId(resultado.getInt("id"));
                itemOs.setObservacao(resultado.getString("observacoes"));
                // Preencha outras informações do veículo, se necessário
            }
        } catch (SQLException ex) {
            Logger.getLogger(OrdemServicoDAO.class.getName()).log(Level.SEVERE, null, ex);
        }
        return itemOs;
    }

}
