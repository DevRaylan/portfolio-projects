/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/javafx/FXMLController.java to edit this template
 */
package br.edu.ifsc.fln.controller;

import br.edu.ifsc.fln.model.dao.MarcaDAO;
import br.edu.ifsc.fln.model.database.Database;
import br.edu.ifsc.fln.model.database.DatabaseFactory;
import br.edu.ifsc.fln.model.domain.ECategoria;
import br.edu.ifsc.fln.model.domain.ETipoCombustivel;
import br.edu.ifsc.fln.model.domain.Marca;
import br.edu.ifsc.fln.model.domain.Modelo;
import java.net.URL;
import java.sql.Connection;
import java.util.List;
import java.util.ResourceBundle;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.ComboBox;
import javafx.scene.control.Spinner;
import javafx.scene.control.SpinnerValueFactory;
import javafx.scene.control.TextField;
import javafx.stage.Stage;

/**
 * FXML Controller class
 *
 * @author d.raylan
 */
public class FXMLAnchorPaneCadastroModeloDialogController implements Initializable {

    @FXML
    private TextField tfModeloDescricao;
    @FXML
    private ComboBox<Marca> cbMarca;
    @FXML
    private ComboBox<ECategoria> cbCategoria;
    
    @FXML
    private Spinner<Integer> spnPotencia;
    
    int potencia;
    
    @FXML
    private ComboBox<ETipoCombustivel> cbCombustivel;
    
    @FXML
    private Button btConfirmar;
    @FXML
    private Button btCancelar;

    private final Database database = DatabaseFactory.getDatabase("mysql");
    private final Connection connection = database.conectar();
    private final MarcaDAO marcaDAO = new MarcaDAO();

    private Stage dialogStage;
    private boolean buttonConfirmarClicked = false;
    private Modelo modelo;

    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        marcaDAO.setConnection(connection);
        carregarComboBoxMarcas();
        carregarComboBoxCategorias();
        SpinnerValueFactory<Integer> spnFactory = new SpinnerValueFactory.IntegerSpinnerValueFactory(0,1000);
        
        spnFactory.setValue(0);
        
        spnPotencia.setValueFactory(spnFactory);
        carregarComboBoxCombustivel();
        setFocusLostHandle();
    }

    private void setFocusLostHandle() {
        tfModeloDescricao.focusedProperty().addListener((ov, oldV, newV) -> {
            if (!newV) { // focus lost
                if (tfModeloDescricao.getText() == null || tfModeloDescricao.getText().isEmpty()) {
                    //System.out.println("teste focus lost");
                    tfModeloDescricao.requestFocus();
                }
            }
        });
    }

    private List<Marca> listaMarcas;
    private ObservableList<Marca> observableListMarcas;

    public void carregarComboBoxMarcas() {
        listaMarcas = marcaDAO.listar();
        observableListMarcas = FXCollections.observableArrayList(listaMarcas);
        cbMarca.setItems(observableListMarcas);
    }

    private List<ECategoria> listaCategorias;
    private ObservableList<ECategoria> observableListCategorias;

    public void carregarComboBoxCategorias() {
        observableListCategorias = FXCollections.observableArrayList(ECategoria.values());
        cbCategoria.setItems(observableListCategorias);
    }
    
    public void carregarComboBoxCombustivel() {
        cbCombustivel.setItems(FXCollections.observableArrayList( ETipoCombustivel.values()));  
    }

    /**
     * @return the dialogStage
     */
    public Stage getDialogStage() {
        return dialogStage;
    }

    /**
     * @param dialogStage the dialogStage to set
     */
    public void setDialogStage(Stage dialogStage) {
        this.dialogStage = dialogStage;
    }

    /**
     * @return the buttonConfirmarClicked
     */
    public boolean isButtonConfirmarClicked() {
        return buttonConfirmarClicked;
    }

    /**
     * @param buttonConfirmarClicked the buttonConfirmarClicked to set
     */
    public void setButtonConfirmarClicked(boolean buttonConfirmarClicked) {
        this.buttonConfirmarClicked = buttonConfirmarClicked;
    }

    /**
     * @return the modelo
     */
    public Modelo getModelo() {
        return modelo;
    }

    /**
     * @param modelo the produto to set
     */
    public void setModelo(Modelo modelo) {
        this.modelo = modelo;
        tfModeloDescricao.setText(modelo.getDescricao());
        cbMarca.getSelectionModel().select(modelo.getMarca());
        cbCategoria.getSelectionModel().select(modelo.getECategoria());
        SpinnerValueFactory<Integer> spnFactory = new SpinnerValueFactory.IntegerSpinnerValueFactory(0,1000);
        spnFactory.setValue(modelo.getMotor().getPotencia());
        spnPotencia.setValueFactory(spnFactory);
        
        cbCombustivel.getSelectionModel().select(modelo.getMotor().getEtipocombustivel());
        
    }

    @FXML
    private void handleBtConfirmar() {
        if (validarEntradaDeDados()) {
            modelo.setDescricao(tfModeloDescricao.getText());
            modelo.setMarca(
                    cbMarca.getSelectionModel().getSelectedItem());
            modelo.setECategoria(
                    cbCategoria.getSelectionModel().getSelectedItem());
            modelo.getMotor().setEtipocombustivel(cbCombustivel.getSelectionModel().getSelectedItem());
            modelo.getMotor().setPotencia(spnPotencia.getValue());

            buttonConfirmarClicked = true;
            dialogStage.close();
        }
    }

    @FXML
    private void handleBtCancelar() {
        dialogStage.close();
    }

    //validar entrada de dados do cadastro
    private boolean validarEntradaDeDados() {
        String errorMessage = "";

        if (tfModeloDescricao.getText() == null || tfModeloDescricao.getText().isEmpty()) {
            errorMessage += "Nome inválido!\n";
        }

        if (cbMarca.getSelectionModel().getSelectedItem() == null) {
            errorMessage += "Selecione uma Marca!\n";
        }
        
        if (cbCategoria.getSelectionModel().getSelectedItem() == null) {
            errorMessage += "Selecione uma Categoria!\n";
        }
        if(spnPotencia.getValue() == 0)
        {
            errorMessage += "Potência de Motor inválida!\n";
        }
        
        if (cbCombustivel.getSelectionModel().getSelectedItem() == null) {
            errorMessage += "Selecione um tipo de combustível!\n";
        }

        if (errorMessage.length() == 0) {
            return true;
        } else {
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setTitle("Erro no cadastro");
            alert.setHeaderText("Campo(s) inválido(s), por favor corrija...");
            alert.setContentText(errorMessage);
            alert.show();
            return false;
        }
    }

}
