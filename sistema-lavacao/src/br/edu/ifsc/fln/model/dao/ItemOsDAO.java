package br.edu.ifsc.fln.model.dao;

import br.edu.ifsc.fln.model.domain.ItemOs;
import br.edu.ifsc.fln.model.domain.OrdemServico;
import br.edu.ifsc.fln.model.domain.Servico;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

/**
 *
 * @author d.raylan
 */
public class ItemOsDAO {
    
    private Connection connection;

    public Connection getConnection() {
        return connection;
    }

    public void setConnection(Connection connection) {
        this.connection = connection;
    }
    
    
    public boolean inserir(ItemOs itemOs) throws SQLException{
    String sql = "INSERT INTO itemos(valor, observacao, id_servico, id_ordemservico) VALUES(?,?,?,?)";
    
    PreparedStatement stmt = connection.prepareStatement(sql);
    stmt.setBigDecimal(1, itemOs.getValor());
    stmt.setString(2, itemOs.getObservacao());
    stmt.setInt(3, itemOs.getServico().getId());
    stmt.setInt(4, itemOs.getOrdemServico().getId());

    stmt.execute();
    return false;
    }
    
    
    public boolean alterar(ItemOs itemOs) throws SQLException{
    String sql = "UPDATE itemos SET valor = ?, observacao = ?, id_servico= ?, id_ordemservico = ? WHERE id = ?";
    PreparedStatement stmt = connection.prepareStatement(sql);
    stmt.setBigDecimal(1, itemOs.getValor());
    stmt.setString(2, itemOs.getObservacao());
    stmt.setInt(3, itemOs.getServico().getId());
    stmt.setInt(4, itemOs.getOrdemServico().getId());
    stmt.setInt(5, itemOs.getId());
    stmt.execute();
    return false;
    }
    
    
    public boolean remover(ItemOs itemOs) throws SQLException{
        String sql = "DELETE FROM itemos WHERE id=?";
        
        PreparedStatement stmt = connection.prepareStatement(sql);
        stmt.setInt(1, itemOs.getId());
        stmt.execute();
        return false;
    }
    
    
    public List<ItemOs> listar() throws SQLException{
        String sql = "SELECT * FROM itemos";
        List<ItemOs> retorno = new ArrayList<>();
            PreparedStatement stmt = connection.prepareStatement(sql);
            ResultSet resultado = stmt.executeQuery();
            while (resultado.next()) {
                ItemOs itemOs = new ItemOs();
                Servico servico = new Servico();
                OrdemServico ordemServico = new OrdemServico();
                itemOs.setId(resultado.getInt("id"));
                itemOs.setValor(resultado.getBigDecimal("valor"));
                itemOs.setObservacao(resultado.getString("observacao"));
                
                servico.setId(resultado.getInt("id_servico"));
                ordemServico.setId(resultado.getInt("id_ordemservico"));
                
                ServicoDAO servicoDAO = new ServicoDAO();
                servicoDAO.setConnection(connection);
                servico = servicoDAO.buscar(servico);
                
                OrdemServicoDAO ordemServicoDAO = new OrdemServicoDAO();
                ordemServicoDAO.setConnection(connection);
                ordemServico = ordemServicoDAO.buscar(ordemServico);
                
                itemOs.setServico(servico);
                itemOs.setOrdemServico(ordemServico);
                
                retorno.add(itemOs);
            }
        return retorno;
    }
    
    
        public List<ItemOs> listarPorOrdemServico(OrdemServico ordemServico) throws SQLException{
        String sql = "SELECT * FROM itemos WHERE id_ordemservico=?";
        List<ItemOs> retorno = new ArrayList<>();
        PreparedStatement stmt = connection.prepareStatement(sql);
        stmt.setInt(1, ordemServico.getId());
        ResultSet resultado = stmt.executeQuery();
        while (resultado.next()) {
            ItemOs itemOs = new ItemOs();
            Servico servico = new Servico();
            OrdemServico os = new OrdemServico();
            itemOs.setId(resultado.getInt("id"));
            itemOs.setValor(resultado.getBigDecimal("valor"));
            itemOs.setObservacao(resultado.getString("observacao"));

            servico.setId(resultado.getInt("id_servico"));
            os.setId(resultado.getInt("id_ordemservico"));

            ServicoDAO servicoDAO = new ServicoDAO();
            servicoDAO.setConnection(connection);
            servico = servicoDAO.buscar(servico);

            itemOs.setServico(servico);
            itemOs.setOrdemServico(os);

            retorno.add(itemOs);
        }
        return retorno;
    }
    
    
    public ItemOs buscar(ItemOs itemOs) throws SQLException{
    String sql = "SELECT * FROM itemos WHERE id=?";
    ItemOs retorno = new ItemOs();
        PreparedStatement stmt = connection.prepareStatement(sql);
        stmt.setInt(1, itemOs.getId());
        ResultSet resultado = stmt.executeQuery();
        if (resultado.next()) {
            Servico servico = new Servico();
            OrdemServico ordemServico = new OrdemServico();
            itemOs.setId(resultado.getInt("id"));
            itemOs.setValor(resultado.getBigDecimal("valor"));
            itemOs.setObservacao(resultado.getString("observacao"));

            servico.setId(resultado.getInt("id_servico"));
            ordemServico.setId(resultado.getInt("id_ordemservico"));
            
            ServicoDAO servicoDAO = new ServicoDAO();
            servicoDAO.setConnection(connection);
            servico = servicoDAO.buscar(servico);

            OrdemServicoDAO ordemServicoDAO = new OrdemServicoDAO();
            ordemServicoDAO.setConnection(connection);
            ordemServico = ordemServicoDAO.buscar(ordemServico);

            itemOs.setServico(servico);
            itemOs.setOrdemServico(ordemServico);

            retorno = itemOs;
        }
        return retorno;
    }
    
    
    public void alterarTodos(OrdemServico ordemServico) throws SQLException{
        String sql = "UPDATE itemos SET valor = ?, observacao = ?, id_servico= ?, id_ordemservico = ? WHERE id = ?";
        for (ItemOs itemOs : ordemServico.getItensDeOs()) 
        {
            PreparedStatement stmt = connection.prepareStatement(sql);
            stmt.setBigDecimal(1, itemOs.getValor());
            stmt.setString(2, itemOs.getObservacao());
            stmt.setInt(3, itemOs.getServico().getId());
            stmt.setInt(4, itemOs.getOrdemServico().getId());
            stmt.setInt(5, itemOs.getId());
            stmt.execute();
        }
    }
    
    
}
