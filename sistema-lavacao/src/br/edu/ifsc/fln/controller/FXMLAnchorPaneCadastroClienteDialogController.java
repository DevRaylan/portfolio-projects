package br.edu.ifsc.fln.controller;

import br.edu.ifsc.fln.model.domain.Cliente;
import br.edu.ifsc.fln.model.domain.PessoaJuridica;
import br.edu.ifsc.fln.model.domain.PessoaFisica;
import java.net.URL;
import java.util.ResourceBundle;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.Group;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.DatePicker;
import javafx.scene.control.Label;
import javafx.scene.control.RadioButton;
import javafx.scene.control.TextField;
import javafx.scene.control.ToggleGroup;
import javafx.stage.Stage;

/**
 * FXML Controller class
 *
 * @author d.raylan
 */

public class FXMLAnchorPaneCadastroClienteDialogController implements Initializable {

    @FXML
    private Button btCancelar;

    @FXML
    private Button btConfirmar;

    @FXML
    private RadioButton rbJuridica;

    @FXML
    private RadioButton rbFisica;

    @FXML
    private Group gbTipo;

    @FXML
    private ToggleGroup tgTipo;

    @FXML
    private Label lbDataCadastro;

    @FXML
    private Label lbCC;

    @FXML
    private Label lbDataNInsEstadual;

    @FXML
    private TextField tfEmail;

    @FXML
    private TextField tfFone;

    @FXML
    private TextField tfNome;

    @FXML
    private DatePicker dpCadastroData;

    @FXML
    private DatePicker dpDataNascimento;

    @FXML
    private TextField tfNumFiscal;

    @FXML
    private TextField tfDataNInsEstadual;

    private Stage dialogStage;
    private boolean btConfirmarClicked = false;
    private Cliente cliente;

    @Override
    public void initialize(URL url, ResourceBundle rb) {
        lbCC.setVisible(false);
        lbDataNInsEstadual.setVisible(false);
        tfDataNInsEstadual.setVisible(false);
    }

    public boolean isBtConfirmarClicked() {
        return btConfirmarClicked;
    }

    public void setBtConfirmarClicked(boolean btConfirmarClicked) {
        this.btConfirmarClicked = btConfirmarClicked;
    }

    public Stage getDialogStage() {
        return dialogStage;
    }

    public void setDialogStage(Stage dialogStage) {
        this.dialogStage = dialogStage;
    }

    public Cliente getCliente() {
        return cliente;
    }

    public void setCliente(Cliente cliente) {
        this.cliente = cliente;

        this.tfNome.setText(this.cliente.getNome());
        this.tfEmail.setText(this.cliente.getEmail());
        this.tfFone.setText(this.cliente.getFone());
        dpCadastroData.setValue(this.cliente.getDataCadastro());

        this.gbTipo.setDisable(true);
        lbCC.setVisible(true);
        lbDataNInsEstadual.setVisible(true);

        if (cliente instanceof PessoaFisica) {
            rbFisica.setSelected(true);
            tfDataNInsEstadual.setDisable(true);
            lbCC.setText("CPF:");
            dpDataNascimento.setVisible(true);
            lbDataNInsEstadual.setText("Data de Nascimento:");

            tfNumFiscal.setText(((PessoaFisica) this.cliente).getCpf());
            dpDataNascimento.setValue(((PessoaFisica) cliente).getDataNascimento());

        } else {
            rbJuridica.setSelected(true);
            lbCC.setText("CNPJ:");
            lbDataNInsEstadual.setText("Ins. Estadual:");
            tfDataNInsEstadual.setVisible(true);
            dpDataNascimento.setVisible(false);

            tfNumFiscal.setText(((PessoaJuridica) this.cliente).getCnpj());
            tfDataNInsEstadual.setText(((PessoaJuridica) this.cliente).getInscricaoestadual());
        }
        this.tfNome.requestFocus();
    }

    @FXML
    public void handleBtConfirmar() {
        if (validarEntradaDeDados()) {
            cliente.setNome(tfNome.getText());
            cliente.setEmail(tfEmail.getText());
            cliente.setFone(tfFone.getText());
            cliente.setDataCadastro(dpCadastroData.getValue());
            if (rbFisica.isSelected()) {
                ((PessoaFisica) cliente).setCpf(tfNumFiscal.getText());
                ((PessoaFisica) cliente).setDataNascimento(dpDataNascimento.getValue());
            } else {
                ((PessoaJuridica) cliente).setCnpj(tfNumFiscal.getText());
                ((PessoaJuridica) cliente).setInscricaoestadual(tfDataNInsEstadual.getText());
            }
            btConfirmarClicked = true;
            dialogStage.close();
        }
    }

    @FXML
    public void handleBtCancelar() {
        dialogStage.close();
    }

    private boolean validarEntradaDeDados() {
        String errorMessage = "";
        if (this.tfNome.getText() == null || this.tfNome.getText().isEmpty()) {
            errorMessage += "Nome inválido.\n";
        }

        if (this.tfFone.getText() == null || this.tfFone.getText().isEmpty()) {
            errorMessage += "Telefone inválido.\n";
        }

        if (this.tfEmail.getText() == null || this.tfEmail.getText().isEmpty() || !this.tfEmail.getText().contains("@")) {
            errorMessage += "Email inválido.\n";
        }
        if (this.dpCadastroData.getValue() == null || this.dpCadastroData.getValue().toString().length() == 0) {
            errorMessage += "Data de Cadastro inválido.\n";
        }

        if (rbFisica.isSelected()) {
            if (this.tfNumFiscal.getText() == null || this.tfNumFiscal.getText().isEmpty()) {
                errorMessage += "CPF inválido.\n";
            }
            if (this.dpDataNascimento.getValue() == null || this.dpDataNascimento.getValue().toString().length() == 0) {
                errorMessage += "Data de Nascimento inválido.\n";
            }
        } else {
            if (this.tfNumFiscal.getText() == null || this.tfNumFiscal.getText().isEmpty()) {
                errorMessage += "CNPJ inválido.\n";
            }
            if (this.tfDataNInsEstadual.getText() == null || this.tfDataNInsEstadual.getText().isEmpty()) {
                errorMessage += "Informe a Inscrição Estadual.\n";
            }
        }

        if (errorMessage.isEmpty()) {
            return true;
        } else {
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setTitle("Erro no cadastro");
            alert.setHeaderText("Corrija os campos inválidos!");
            alert.setContentText(errorMessage);
            alert.show();
            return false;
        }
    }
}
