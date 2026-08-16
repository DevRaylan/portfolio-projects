package br.edu.ifsc.fln.model.dao;

import br.edu.ifsc.fln.model.domain.Cliente;
import br.edu.ifsc.fln.model.domain.Cor;
import br.edu.ifsc.fln.model.domain.Marca;
import br.edu.ifsc.fln.model.domain.Modelo;
import br.edu.ifsc.fln.model.domain.Veiculo;
import java.sql.Connection;
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
public class VeiculoDAO{

    private Connection connection;

    public Connection getConnection() {
        return connection;
    }

    public void setConnection(Connection connection) {
        this.connection = connection;
    }

    public boolean inserir(Veiculo veiculo) {
        String sql = "INSERT INTO veiculo(placa, observacoes, id_modelo, id_cor, id_cliente) VALUES(?,?,?,?,?)";
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            stmt.setString(1, veiculo.getPlaca());
            stmt.setString(2, veiculo.getObservacoes());
            stmt.setInt(3, veiculo.getModelo().getId());
            stmt.setInt(4, veiculo.getCor().getId());
            stmt.setInt(5, veiculo.getCliente().getId());
            stmt.execute();
            return true;
        } catch (SQLException ex) {
            Logger.getLogger(VeiculoDAO.class.getName()).log(Level.SEVERE, null, ex);
            return false;
        }
    }

    public boolean alterar(Veiculo veiculo) {
        String sql = "UPDATE veiculo SET placa=?, observacoes=?, id_modelo=?, id_cor=?, id_cliente=?  WHERE id=?";
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            stmt.setString(1, veiculo.getPlaca());
            stmt.setString(2, veiculo.getObservacoes());
            stmt.setInt(3, veiculo.getModelo().getId());
            stmt.setInt(4, veiculo.getCor().getId());
            stmt.setInt(5, veiculo.getCliente().getId());
            stmt.setInt(6, veiculo.getId());
            stmt.execute();
            return true;
        } catch (SQLException ex) {
            Logger.getLogger(VeiculoDAO.class.getName()).log(Level.SEVERE, null, ex);
            return false;
        }
    }

    public boolean remover(Veiculo veiculo) {
        String sql = "DELETE FROM veiculo WHERE id=?";
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            stmt.setInt(1, veiculo.getId());
            stmt.execute();
            return true;
        } catch (SQLException ex) {
            Logger.getLogger(VeiculoDAO.class.getName()).log(Level.SEVERE, null, ex);
            return false;
        }
    }

    public List<Veiculo> listar() {
        String sql = "SELECT v.id AS veiculo_id, v.placa AS veiculo_placa, v.observacoes AS veiculo_observacoes, m.id AS modelo_id, m.descricao AS modelo_descricao, m.id_marca AS marca_id, ma.nome AS marca_nome, c.id AS cor_id, c.nome AS cor_nome, cl.id AS cliente_id, cl.nome AS cliente_nome FROM veiculo v INNER JOIN modelo m ON m.id = v.id_modelo INNER JOIN marca ma ON ma.id = m.id_marca INNER JOIN cliente cl ON cl.id = v.id_cliente INNER JOIN cor c ON c.id = v.id_cor;";
        List<Veiculo> retorno = new ArrayList<>();
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            ResultSet resultado = stmt.executeQuery();
            while (resultado.next()) {
                Veiculo veiculo = populateVO(resultado);
                retorno.add(veiculo);
            }
        } catch (SQLException ex) {
            Logger.getLogger(VeiculoDAO.class.getName()).log(Level.SEVERE, null, ex);
        }
        return retorno;
    }

    
    private Veiculo populateVO(ResultSet rs) throws SQLException {
        Veiculo veiculo = new Veiculo();
        Modelo modelo = new Modelo();
        Marca marca = new Marca();
        Cor cor = new Cor();
        veiculo.setModelo(modelo);
        veiculo.getModelo().setMarca(marca);
        veiculo.setCor(cor);

        veiculo.setId(rs.getInt("veiculo_id"));
        veiculo.setPlaca(rs.getString("veiculo_placa"));
        veiculo.setObservacoes(rs.getString("veiculo_observacoes"));
        modelo.setId(rs.getInt("modelo_id"));
        modelo.setDescricao(rs.getString("modelo_descricao"));
        veiculo.getModelo().getMarca().setId(rs.getInt("marca_id"));
        veiculo.getModelo().getMarca().setNome(rs.getString("marca_nome"));
        
        cor.setId(rs.getInt("cor_id"));
        cor.setNome(rs.getString("cor_nome"));
        
        ClienteDAO clientedao= new ClienteDAO();
        clientedao.setConnection(connection);
        Cliente cliente = clientedao.buscar(rs.getInt("cliente_id"));

        veiculo.setCliente(cliente);
        
        
        
        return veiculo;
    }    
    
    
    public Veiculo buscar(Veiculo veiculo) {
        String sql =  "SELECT v.id AS veiculo_id, v.placa AS veiculo_placa, v.observacoes AS veiculo_observacoes, m.id AS modelo_id, m.descricao AS modelo_descricao, m.id_marca AS marca_id, ma.nome AS marca_nome, c.id AS cor_id, c.nome AS cor_nome FROM veiculo v INNER JOIN modelo m ON m.id = v.id_modelo INNER JOIN cor c ON c.id = v.id_cor INNER JOIN marca ma ON ma.id = m.id_marca WHERE v.id = ?;";
        Veiculo retorno = new Veiculo();
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            stmt.setInt(1, veiculo.getId());
            ResultSet resultado = stmt.executeQuery();
            if (resultado.next()) {
                retorno = populateSingleVO(resultado);
            }
        } catch (SQLException ex) {
            Logger.getLogger(VeiculoDAO.class.getName()).log(Level.SEVERE, null, ex);
        }
        return retorno;
    }
    
    private Veiculo populateSingleVO(ResultSet rs) throws SQLException {
        Veiculo veiculo = new Veiculo();
        Modelo modelo = new Modelo();
        Marca marca = new Marca();
        Cor cor = new Cor();
        veiculo.setModelo(modelo);
        veiculo.getModelo().setMarca(marca);
        veiculo.setCor(cor);
        
        //dados do veiculo
        veiculo.setId(rs.getInt("veiculo_id"));
        veiculo.setPlaca(rs.getString("veiculo_placa"));
        veiculo.setObservacoes(rs.getString("veiculo_observacoes"));
        
        modelo.setId(rs.getInt("modelo_id"));
        modelo.setDescricao(rs.getString("modelo_descricao"));
        
        modelo.getMarca().setId(rs.getInt("marca_id"));
        modelo.getMarca().setNome(rs.getString("marca_nome"));
        
        cor.setId(rs.getInt("cor_id"));
        cor.setNome(rs.getString("cor_nome"));
        
        ClienteDAO clientedao= new ClienteDAO();
        clientedao.setConnection(connection);
        Cliente cliente = clientedao.buscar(rs.getInt("cliente_id"));
        veiculo.setCliente(cliente);
        
        
        
        return veiculo;        
    }
    
}
