/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/javafx/FXMLController.java to edit this template
 */
package br.edu.ifsc.fln.controller;

import br.edu.ifsc.fln.model.database.Database;
import br.edu.ifsc.fln.model.database.DatabaseFactory;
import br.edu.ifsc.fln.model.domain.ECategoria;
import br.edu.ifsc.fln.model.domain.Servico;
import java.math.BigDecimal;
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
public class FXMLAnchorPaneCadastroServicoDialogController implements Initializable {

    @FXML
    private TextField tfDescricao;

    @FXML
    private TextField tfValor;

    @FXML
    private ComboBox<ECategoria> cbCategoria;

    @FXML
    private Spinner<Integer> spnPontos;

    int pontos;

    @FXML
    private Button btConfirmar;
    @FXML
    private Button btCancelar;

    private final Database database = DatabaseFactory.getDatabase("mysql");
    private final Connection connection = database.conectar();

    private Stage dialogStage;
    private boolean buttonConfirmarClicked = false;
    private Servico servico;

    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        carregarComboBoxCategorias();
        SpinnerValueFactory<Integer> spnFactory = new SpinnerValueFactory.IntegerSpinnerValueFactory(0, 1000);

        spnFactory.setValue(0);

        spnPontos.setValueFactory(spnFactory);
        setFocusLostHandle();
    }

    private void setFocusLostHandle() {
        tfDescricao.focusedProperty().addListener((ov, oldV, newV) -> {
            if (!newV) { // focus lost
                if (tfDescricao.getText() == null || tfDescricao.getText().isEmpty()) {
                    //System.out.println("teste focus lost");
                    tfDescricao.requestFocus();
                }
            }
        });
    }

    private List<ECategoria> listaCategorias;
    private ObservableList<ECategoria> observableListCategorias;

    public void carregarComboBoxCategorias() {
        observableListCategorias = FXCollections.observableArrayList(ECategoria.values());
        cbCategoria.setItems(observableListCategorias);
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
     * @return the servico
     */
    public Servico getServico() {
        return servico;
    }

    /**
     * @param servico the produto to set
     */
    public void setServico(Servico servico) {
        this.servico = servico;
        tfDescricao.setText(servico.getDescricao());
        tfValor.setText(servico.getValor().toString());
        cbCategoria.getSelectionModel().select(servico.getECategoria());
        SpinnerValueFactory<Integer> spnFactory = new SpinnerValueFactory.IntegerSpinnerValueFactory(0, 1000);
        spnFactory.setValue(servico.getPontos());
        spnPontos.setValueFactory(spnFactory);

    }

    @FXML
    private void handleBtConfirmar() {
        if (validarEntradaDeDados()) {
            servico.setDescricao(tfDescricao.getText());
            servico.setECategoria(
                    cbCategoria.getSelectionModel().getSelectedItem());
            BigDecimal valor = BigDecimal.valueOf(Double.parseDouble(tfValor.getText()));
            servico.setValor(valor);
            servico.setPontos(spnPontos.getValue());
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

        if (tfDescricao.getText() == null || tfDescricao.getText().isEmpty()) {
            errorMessage += "Nome inválido!\n";
        }
        if (tfValor.getText() == null || tfValor.getText().isEmpty()) {
            errorMessage += "Valor inválido!\n";
        }

        if (cbCategoria.getSelectionModel().getSelectedItem() == null) {
            errorMessage += "Selecione uma Categoria!\n";
        }
        if (spnPontos.getValue() == 0) {
            errorMessage += "Pontos inválidos!\n";
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
