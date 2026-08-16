/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package br.edu.ifsc.fln.controller;

import br.edu.ifsc.fln.model.dao.ServicoDAO;
import br.edu.ifsc.fln.model.database.Database;
import br.edu.ifsc.fln.model.database.DatabaseFactory;
import br.edu.ifsc.fln.model.domain.Servico;
import java.io.IOException;
import java.net.URL;
import java.sql.Connection;
import java.text.DecimalFormat;
import java.util.List;
import java.util.ResourceBundle;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableView;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.layout.AnchorPane;
import javafx.stage.Stage;


/**
 * FXML Controller class
 *
 * @author d.raylan
 */
public class FXMLAnchorPaneCadastroServicoController implements Initializable {

    @FXML
    private TableView<Servico> tableView;

    @FXML
    private TableColumn<Servico, String> tableColumnDescricao;

    @FXML
    private Label lbId;

    @FXML
    private Label lbDescricao;

    @FXML
    private Label lbValor;

    @FXML
    private Label lbCategoria;
    
    @FXML
    private Label lbPontos;

    @FXML
    private Button btInserir;

    @FXML
    private Button btAlterar;

    @FXML
    private Button btRemover;

    private List<Servico> listaServicos;
    private ObservableList<Servico> observableListServicos;

    //acesso ao banco de dados
    private final Database database = DatabaseFactory.getDatabase("mysql");
    private final Connection connection = database.conectar();
    private final ServicoDAO servicoDAO = new ServicoDAO();
    
    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        servicoDAO.setConnection(connection);

        carregarTableView();

        tableView.getSelectionModel().selectedItemProperty().addListener(
                (observable, oldValue, newValue) -> selecionarItemTableView(newValue));

    }

    public void carregarTableView() {
        tableColumnDescricao.setCellValueFactory(new PropertyValueFactory<>("descricao"));
        
        listaServicos = servicoDAO.listar();
        
        observableListServicos = FXCollections.observableArrayList(listaServicos);
        tableView.setItems(observableListServicos);
    }
    
    public void selecionarItemTableView(Servico servico) {
    DecimalFormat df = new DecimalFormat("0.00");
    if (servico != null) {
        servico = servicoDAO.buscar(servico);
        lbId.setText(Integer.toString(servico.getId()));
        lbDescricao.setText(servico.getDescricao());
        lbValor.setText(df.format(servico.getValor())); // Corrigindo para definir o texto formatado
        lbCategoria.setText(servico.getECategoria().getDescricao());
        lbPontos.setText(Integer.toString(servico.getPontos()));
    } else {
        lbId.setText("");
        lbDescricao.setText("");
        lbValor.setText(""); // Limpando o texto do tfValor
        lbCategoria.setText("");
        lbPontos.setText("");
    }
}

    

    @FXML
    public void handleBtInserir() throws IOException {
        Servico servico = new Servico();
        boolean buttonConfirmarClicked = showFXMLAnchorPaneCadastrosServicosDialog(servico);
        if (buttonConfirmarClicked) {
            servicoDAO.inserir(servico);
            carregarTableView();
        }
    }
    
    
    @FXML
    public void handleBtRemover() throws IOException {
        Servico servico = tableView.getSelectionModel().getSelectedItem();
        if (servico != null) {
            servicoDAO.remover(servico);
            carregarTableView();
        } else {
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setContentText("Por favor, escolha um servico na Tabela.");
            alert.show();
        }
    }
    
    public boolean showFXMLAnchorPaneCadastrosServicosDialog(Servico servico) throws IOException {
        FXMLLoader loader = new FXMLLoader();
        loader.setLocation(FXMLAnchorPaneCadastroServicoDialogController.class.getResource( 
            "../view/FXMLAnchorPaneCadastroServicoDialog.fxml"));
        AnchorPane page = (AnchorPane)loader.load();
        
        //criando um estágio de diálogo  (Stage Dialog)
        Stage dialogStage = new Stage();
        dialogStage.setTitle("Cadastro de servicos");
        Scene scene = new Scene(page);
        dialogStage.setScene(scene);
        
        //Setando o servico ao controller
        FXMLAnchorPaneCadastroServicoDialogController controller = loader.getController();
        controller.setDialogStage(dialogStage);
        controller.setServico(servico);
        
        dialogStage.showAndWait();
        
        return controller.isButtonConfirmarClicked();
    }


}
