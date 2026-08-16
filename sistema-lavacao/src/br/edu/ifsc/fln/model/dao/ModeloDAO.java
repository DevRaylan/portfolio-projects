package br.edu.ifsc.fln.model.dao;

import br.edu.ifsc.fln.model.domain.ECategoria;
import br.edu.ifsc.fln.model.domain.ETipoCombustivel;
import br.edu.ifsc.fln.model.domain.Marca;
import br.edu.ifsc.fln.model.domain.Modelo;
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

public class ModeloDAO {

    private Connection connection;

    public Connection getConnection() {
        return connection;
    }

    public void setConnection(Connection connection) {
        this.connection = connection;
    }

    public boolean inserir(Modelo modelo) {
        final String sql = "INSERT INTO Modelo(descricao, id_marca, categoria) VALUES(?,?,?);";
        final String sqlMotor = "INSERT INTO motor(id_modelo, potencia, tipoCombustivel) values ((SELECT max(id) FROM modelo), ?,?);";
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            //registra o modelo
            stmt.setString(1, modelo.getDescricao());
            stmt.setInt(2, modelo.getMarca().getId());
            stmt.setString(3, modelo.getECategoria().name());
            stmt.execute();
            
            stmt = connection.prepareStatement(sqlMotor);
//            stmt.setInt(1, modelo.getMotor().getPotencia());
//            stmt.setString(2, modelo.getMotor().getEtipocombustivel().getDescricao());
//            
            stmt.setInt(1, modelo.getMotor().getPotencia());
            stmt.setString(2, modelo.getMotor().getEtipocombustivel().name());
              
            
            stmt.execute();
            return true;

        } catch (SQLException ex) {
            Logger.getLogger(ModeloDAO.class.getName()).log(Level.SEVERE, null, ex);
            return false;
        }
    }

    public boolean alterar(Modelo modelo) {
        String sql = "UPDATE modelo SET descricao=?, id_marca=?, categoria=? WHERE id=?";
        String sqlMotor = "UPDATE motor SET Potencia=?, tipoCombustivel=? WHERE id_modelo=?";
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            stmt.setString(1, modelo.getDescricao());
            stmt.setInt(2, modelo.getMarca().getId());
            stmt.setString(3, modelo.getECategoria().name());
            stmt.setInt(4, modelo.getId());
            stmt.execute();
            
            stmt = connection.prepareStatement(sqlMotor);
            stmt.setInt(1, modelo.getMotor().getPotencia());
            stmt.setString(2, modelo.getMotor().getEtipocombustivel().name());
           
            stmt.setInt(3, modelo.getId());
            stmt.execute();
            return true;
        } catch (SQLException ex) {
            Logger.getLogger(ModeloDAO.class.getName()).log(Level.SEVERE, null, ex);
            return false;
        }
    }

    public boolean remover(Modelo modelo) {
        String sql = "DELETE FROM modelo WHERE id=?";
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            stmt.setInt(1, modelo.getId());
            stmt.execute();
            return true;
        } catch (SQLException ex) {
            Logger.getLogger(ModeloDAO.class.getName()).log(Level.SEVERE, null, ex);
            return false;
        }
    }

    public List<Modelo> listar() {
        String sql =  "SELECT m.id as modelo_id, m.descricao as modelo_descricao, n.id as marca_id, n.nome as marca_nome, m.categoria as categoria, mt.potencia as motor_potencia, mt.tipoCombustivel as motor_tipoCombustivel FROM modelo m INNER JOIN marca n ON n.id = m.id_marca INNER JOIN motor mt ON mt.id_modelo = m.id;";
        //String sql = "SELECT * FROM MODELO";
        List<Modelo> retorno = new ArrayList<>();
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            ResultSet resultado = stmt.executeQuery();
            while (resultado.next()) {
                Modelo modelo = populateVO(resultado);
                retorno.add(modelo);
            }
        } catch (SQLException ex) {
            Logger.getLogger(ModeloDAO.class.getName()).log(Level.SEVERE, null, ex);
        }
        return retorno;
    }

    public List<Modelo> listarPorMarca(Marca marca) {
        String sql = "SELECT m.id as modelo_id, m.descricao as modelo_descricao,n.id as marca_id, n.nome as marca_nome, m.categoria as categoria, mt.potencia as motor_potencia, mt.tipoCombustivel as motor_tipoCombustivel FROM modelo m INNER JOIN marca n ON n.id = m.id_marca INNER JOIN motor mt ON m.id = mt.id_modelo WHERE n.id = ?;";
        List<Modelo> retorno = new ArrayList<>();
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            stmt.setInt(1, marca.getId());
            ResultSet resultado = stmt.executeQuery();
            while (resultado.next()) {
                Modelo modelo = populateVO(resultado, true);
                modelo.setMarca(marca);
                retorno.add(modelo);
            }
        } catch (SQLException ex) {
            Logger.getLogger(ModeloDAO.class.getName()).log(Level.SEVERE, null, ex);
        }
        return retorno;
    }

    public Modelo buscar(Modelo modelo) {
        String sql = "SELECT m.id as modelo_id, m.descricao as modelo_descricao, n.id as marca_id, n.nome as marca_nome, m.categoria as categoria, mt.potencia as motor_potencia, mt.tipoCombustivel as motor_tipoCombustivel FROM modelo m INNER JOIN marca n ON n.id = m.id_marca INNER JOIN motor mt ON m.id = mt.id_modelo WHERE m.id = ?;";
        Modelo retorno = new Modelo();
        try {
            PreparedStatement stmt = connection.prepareStatement(sql);
            stmt.setInt(1, modelo.getId());
            ResultSet resultado = stmt.executeQuery();
            if (resultado.next()) {
                retorno = populateVO(resultado);
            }
        } catch (SQLException ex) {
            Logger.getLogger(ModeloDAO.class.getName()).log(Level.SEVERE, null, ex);
        }
        return retorno;
    }

    /**
     * Método para mapear os dados de um produto (relacional) para objeto,
     * contemplando ou não a categoria.
     *
     * @param rs
     * @param comMarca
     * @return
     * @throws SQLException
     */
    private Modelo populateVO(ResultSet rs, boolean comMarca) throws SQLException {
        Modelo modelo = new Modelo();
        //produto.setMarca(categoria);

        modelo.setId(rs.getInt("modelo_id"));
        modelo.setDescricao(rs.getString("modelo_descricao"));

        String valorCategoria = rs.getString("categoria");
        ECategoria categoria = ECategoria.valueOf(ECategoria.class, valorCategoria);
        modelo.setECategoria(categoria);

        if (comMarca) {
            Marca marca = new Marca();
            marca.setId(rs.getInt("marca_id"));
            MarcaDAO marcaDAO = new MarcaDAO();
            marcaDAO.setConnection(connection);
            marca = marcaDAO.buscar(marca);
            modelo.setMarca(marca);
        }

        return modelo;
    }

    private Modelo populateVO(ResultSet rs) throws SQLException {
        Modelo modelo = new Modelo();
        Marca marca = new Marca();
        
        modelo.setMarca(marca);


        modelo.setId(rs.getInt("modelo_id"));
        modelo.setDescricao(rs.getString("modelo_descricao"));
        modelo.setECategoria(Enum.valueOf(ECategoria.class, rs.getString("categoria")));
        //marca
        marca.setId(rs.getInt("marca_id"));
        marca.setNome(rs.getString("marca_nome"));
        //motor
        modelo.getMotor().getModelo().setId(rs.getInt("modelo_id"));
        modelo.getMotor().setPotencia(rs.getInt("motor_potencia"));  
        modelo.getMotor().setEtipocombustivel(Enum.valueOf(ETipoCombustivel.class, rs.getString("motor_tipoCombustivel")));
     
        

        return modelo;
    }

}
