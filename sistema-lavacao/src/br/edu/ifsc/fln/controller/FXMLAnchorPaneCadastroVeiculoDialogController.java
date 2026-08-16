package br.edu.ifsc.fln.controller;

import br.edu.ifsc.fln.model.dao.ClienteDAO;
import br.edu.ifsc.fln.model.dao.ModeloDAO;
import br.edu.ifsc.fln.model.dao.CorDAO;
import br.edu.ifsc.fln.model.dao.MarcaDAO;
import br.edu.ifsc.fln.model.database.Database;
import br.edu.ifsc.fln.model.database.DatabaseFactory;
import br.edu.ifsc.fln.model.domain.Cliente;
import br.edu.ifsc.fln.model.domain.Veiculo;
import br.edu.ifsc.fln.model.domain.Cor;
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
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;
import javafx.stage.Stage;

public class FXMLAnchorPaneCadastroVeiculoDialogController implements Initializable {

    @FXML
    private TextField tfPlaca;
    @FXML
    private TextArea tfObservacoes;
    
    @FXML
    private ComboBox<Modelo> cbModelo;
    @FXML
    private ComboBox<Marca> cbMarca;
    @FXML
    private ComboBox<Cor> cbCor;
    @FXML
    private ComboBox<Cliente> cbCliente;
    @FXML
    private Button btConfirmar;
    @FXML
    private Button btCancelar;
    
    private final Database database = DatabaseFactory.getDatabase("mysql");
    private final Connection connection = database.conectar();
    private final ModeloDAO modeloDAO = new ModeloDAO();
    private final MarcaDAO marcaDAO = new MarcaDAO();
    private final CorDAO corDAO = new CorDAO();
    private final ClienteDAO clienteDAO = new ClienteDAO();
    
    private Stage dialogStage;
    private boolean buttonConfirmarClicked = false;
    private Veiculo veiculo;    

    @Override
    public void initialize(URL url, ResourceBundle rb) {
        modeloDAO.setConnection(connection);
        marcaDAO.setConnection(connection);
        corDAO.setConnection(connection);
        clienteDAO.setConnection(connection);
        carregarComboBoxModelos();
        carregarComboBoxMarcas();
        carregarComboBoxCores();
        carregarComboBoxClientes();
        setFocusLostHandle();
        
        // Adicione um listener ao ComboBox de Marca para carregar modelos com base na marca selecionada
        cbMarca.getSelectionModel().selectedItemProperty().addListener(
            (observable, oldValue, newValue) -> carregarModelosDaMarca(newValue));
    } 
    
    private void setFocusLostHandle() {
        tfPlaca.focusedProperty().addListener((ov, oldV, newV) -> {
            if (!newV) {
                if (tfPlaca.getText() == null || tfPlaca.getText().isEmpty()) {
                    tfPlaca.requestFocus();
                }
            }
        });
    }   
    
    private List<Modelo> listaModelos;
    private ObservableList<Modelo> observableListModelos; 
    
    public void carregarComboBoxModelos() {
        listaModelos = modeloDAO.listar();
        observableListModelos = FXCollections.observableArrayList(listaModelos);
        cbModelo.setItems(observableListModelos);
    } 
    
    private List<Marca> listaMarcas;
    private ObservableList<Marca> observableListMarcas; 
    
    public void carregarComboBoxMarcas() {
        listaMarcas = marcaDAO.listar();
        observableListMarcas = FXCollections.observableArrayList(listaMarcas);
        cbMarca.setItems(observableListMarcas);
    }
    
    private List<Cor> listaCores;
    private ObservableList<Cor> observableListCores; 
    
    public void carregarComboBoxCores() {
        listaCores = corDAO.listar();
        observableListCores = FXCollections.observableArrayList(listaCores);
        cbCor.setItems(observableListCores);
    } 
    
    private List<Cliente> listaClientes;
    private ObservableList<Cliente> observableListClientes; 
    
    public void carregarComboBoxClientes() {
        listaClientes = clienteDAO.listar();
        observableListClientes = FXCollections.observableArrayList(listaClientes);
        cbCliente.setItems(observableListClientes);
    }
    
    public Stage getDialogStage() {
        return dialogStage;
    }

    public void setDialogStage(Stage dialogStage) {
        this.dialogStage = dialogStage;
    }

    public boolean isButtonConfirmarClicked() {
        return buttonConfirmarClicked;
    }

    public void setButtonConfirmarClicked(boolean buttonConfirmarClicked) {
        this.buttonConfirmarClicked = buttonConfirmarClicked;
    }

    public Veiculo getVeiculo() {
        return veiculo;
    }

    public void setVeiculo(Veiculo veiculo) {
    this.veiculo = veiculo;
    tfPlaca.setText(veiculo.getPlaca());
    tfObservacoes.setText(veiculo.getObservacoes());
    
    Modelo modeloVeiculo = veiculo.getModelo();
    if (modeloVeiculo != null) {
        cbModelo.getSelectionModel().select(modeloVeiculo);
        Marca marcaModelo = modeloVeiculo.getMarca();
        if (marcaModelo != null) {
            cbMarca.getSelectionModel().select(marcaModelo);
        }
    }
    cbCor.getSelectionModel().select(veiculo.getCor());
    cbCliente.getSelectionModel().select(veiculo.getCliente());
}

    
    private void carregarModelosDaMarca(Marca marcaSelecionada) {
        if (marcaSelecionada != null) {
            List<Modelo> modelosDaMarca = modeloDAO.listarPorMarca(marcaSelecionada);
            observableListModelos = FXCollections.observableArrayList(modelosDaMarca);
            cbModelo.setItems(observableListModelos);
        } else {
            cbModelo.getItems().clear();
        }
    }
    
    @FXML
    private void handleBtConfirmar() {
        if (validarEntradaDeDados()) {
            veiculo.setPlaca(tfPlaca.getText());
            veiculo.setObservacoes(tfObservacoes.getText());
            veiculo.setModelo(cbModelo.getSelectionModel().getSelectedItem());
            veiculo.setCor(cbCor.getSelectionModel().getSelectedItem());
            veiculo.setCliente(cbCliente.getSelectionModel().getSelectedItem());

            buttonConfirmarClicked = true;
            dialogStage.close();
        }
    }
    
    @FXML
    private void handleBtCancelar() {
        dialogStage.close();
    }
    
    private boolean validarEntradaDeDados() {
        String errorMessage = "";
        
        if (tfPlaca.getText() == null || tfPlaca.getText().isEmpty()) {
            errorMessage += "Placa inválida!\n";
        }
        if (tfObservacoes.getText() == null || tfObservacoes.getText().isEmpty()) {
            errorMessage += "Observações inválidas!\n";
        }
        
        if (cbModelo.getSelectionModel().getSelectedItem() == null) {
            errorMessage += "Selecione um Modelo!\n";
        }
        if (cbMarca.getSelectionModel().getSelectedItem() == null) {
            errorMessage += "Selecione uma Marca!\n";
        }
        if (cbCor.getSelectionModel().getSelectedItem() == null) {
            errorMessage += "Selecione uma Cor!\n";
        }
        if (cbCliente.getSelectionModel().getSelectedItem() == null) {
            errorMessage += "Selecione uma Cliente!\n";
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
