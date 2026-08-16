package br.edu.ifsc.fln.controller;

import br.edu.ifsc.fln.model.dao.ServicoDAO;
import br.edu.ifsc.fln.model.dao.VeiculoDAO;
import br.edu.ifsc.fln.model.database.Database;
import br.edu.ifsc.fln.model.database.DatabaseFactory;
import br.edu.ifsc.fln.model.domain.EStatus;
import br.edu.ifsc.fln.model.domain.ItemOs;
import br.edu.ifsc.fln.model.domain.OrdemServico;
import br.edu.ifsc.fln.model.domain.Servico;
import br.edu.ifsc.fln.model.domain.Veiculo;
import java.math.BigDecimal;
import java.net.URL;
import java.sql.Connection;
import java.sql.SQLException;
import java.util.List;
import java.util.Optional;
import java.util.ResourceBundle;
import java.util.logging.Level;
import java.util.logging.Logger;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.Button;
import javafx.scene.control.ComboBox;
import javafx.scene.control.ContextMenu;
import javafx.scene.control.DatePicker;
import javafx.scene.control.MenuItem;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableView;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;
import javafx.scene.control.TextInputDialog;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.input.MouseEvent;
import javafx.stage.Stage;

/**
 * FXML Controller class
 *
 * @author d.raylan
 */
public class FXMLAnchorPaneProcessoOrdemServicoDialogController implements Initializable {

    @FXML
    private ComboBox<Servico> cbServicos;

    @FXML
    private ComboBox<EStatus> cbEStatus;

    @FXML
    private ComboBox<Veiculo> cbVeiculos;

    @FXML
    private Button btAdicionar;

    @FXML
    private Button btCancelar;

    @FXML
    private Button btConfirmar;

    @FXML
    private ContextMenu contextMenu;

    @FXML
    private DatePicker dpData;

    @FXML
    private TableColumn<ItemOs, Servico> tableColumnServicos;

    @FXML
    private TableColumn<ItemOs, BigDecimal> tableColumnValores;

    @FXML
    private TableView<ItemOs> tableViewItensDeServico;

    @FXML
    private TextField tfCliente;

    @FXML
    private TextField tfDesconto;

    @FXML
    private TextArea taObservacao;

    @FXML
    private TextField tfTotal;

    @FXML
    private MenuItem contextMenuItemRemoverItem;

    private List<Veiculo> listaVeiculos;
    private List<Servico> listaServicos;
    private ObservableList<Veiculo> observableListVeiculos;
    private ObservableList<Servico> observableListServicos;
    private ObservableList<ItemOs> observableListItemOs;

    //acesso ao banco
    private final Database database = DatabaseFactory.getDatabase("mysql");
    private final Connection connection = database.conectar();
    private final VeiculoDAO veiculoDAO = new VeiculoDAO();
    private final ServicoDAO servicoDAO = new ServicoDAO();

    private Stage dialogStage;
    private boolean buttonConfirmarClicked = false;
    private OrdemServico ordemServico;

    @Override
    public void initialize(URL url, ResourceBundle rb) {
        // TODO
        veiculoDAO.setConnection(connection);
        servicoDAO.setConnection(connection);
        try {
            carregarComboBoxVeiculo();
        } catch (SQLException ex) {
            Logger.getLogger(FXMLAnchorPaneProcessoOrdemServicoDialogController.class.getName()).log(Level.SEVERE, null, ex);
        }
        carregarComboBoxServicos();
        carregarComboBoxStatus();

        tableColumnServicos.setCellValueFactory(new PropertyValueFactory<>("servico"));
        tableColumnValores.setCellValueFactory(new PropertyValueFactory<>("valor"));
    }

    private void carregarComboBoxVeiculo() throws SQLException {

        listaVeiculos = veiculoDAO.listar();
        observableListVeiculos = FXCollections.observableArrayList(listaVeiculos);
        cbVeiculos.setItems(observableListVeiculos);

        cbVeiculos.getSelectionModel().selectedItemProperty().addListener((observable, oldValue, newValue) -> {
            if (newValue != null) {
                tfCliente.setText(newValue.getCliente().getNome());
            }
        });
    }

    private void carregarComboBoxServicos() {

        listaServicos = servicoDAO.listar();
        observableListServicos = FXCollections.observableArrayList(listaServicos);
        cbServicos.setItems(observableListServicos);
    }

    public void carregarComboBoxStatus() {
        cbEStatus.setItems(FXCollections.observableArrayList(EStatus.values()));
    }

    public Stage getDialogStage() {
        return dialogStage;
    }

    //nao entendi essas
    public void setDialogStage(Stage dialogStage) {
        this.dialogStage = dialogStage;
    }

    public boolean isButtonConfirmarClicked() {
        return buttonConfirmarClicked;
    }

    public void setButtonConfirmarClicked(boolean buttonConfirmarClicked) {
        this.buttonConfirmarClicked = buttonConfirmarClicked;
    }

    public OrdemServico getOrdemServico() {
        return ordemServico;
    }

    public void setOrdemServico(OrdemServico ordemServico) {
        this.ordemServico = ordemServico;
        if (ordemServico.getId() != 0) {
            cbVeiculos.getSelectionModel().select(this.ordemServico.getVeiculo());
            dpData.setValue(this.ordemServico.getData());
            observableListItemOs = FXCollections.observableArrayList(
                    this.ordemServico.getItensDeOs());
            tableViewItensDeServico.setItems(observableListItemOs);
            tfTotal.setText(String.format("%.2f", this.ordemServico.getTotal()));
//            tfDesconto.setText(String.format("%.2f", this.ordemServico.getTaxaDesconto()));

           taObservacao.setText(this.ordemServico.getObservacao());

            cbEStatus.getSelectionModel().select(this.ordemServico.getEStatus());
        }
    }

    @FXML
    public void handleBtAdicionar() {
        Servico servico;
        ItemOs itemOS = new ItemOs();
        if (cbServicos.getSelectionModel().getSelectedItem() != null) {

            servico = cbServicos.getSelectionModel().getSelectedItem();
            servico = servicoDAO.buscar(servico);

            itemOS.setServico(servico);
            itemOS.setObservacao(taObservacao.getText());
            itemOS.setValor(servico.getValor());
            itemOS.setOrdemServico(ordemServico);
            ordemServico.getItensDeOs().add(itemOS);
            observableListItemOs = FXCollections.observableArrayList(ordemServico.getItensDeOs());
            tableViewItensDeServico.setItems(observableListItemOs);
            tfTotal.setText(String.format("%.2f", ordemServico.getTotal()));

        }
    }

    @FXML
    void handleBtCancelar(ActionEvent event) {
        dialogStage.close();
    }

    @FXML
    void handleBtConfirmar(ActionEvent event) {
        ordemServico.setVeiculo(cbVeiculos.getSelectionModel().getSelectedItem());
        ordemServico.setData(dpData.getValue());
        ordemServico.setTaxaDesconto(Double.parseDouble(tfDesconto.getText()));
        ordemServico.setEStatus((EStatus) cbEStatus.getSelectionModel().getSelectedItem());
        ordemServico.setItensDeOs(observableListItemOs);
        buttonConfirmarClicked = true;
        dialogStage.close();
    }

    @FXML
    void handleTableViewMouseClicked(MouseEvent event) {
        ItemOs itemOS
                = tableViewItensDeServico.getSelectionModel().getSelectedItem();
        if (itemOS == null) {

            contextMenuItemRemoverItem.setDisable(true);
        } else {

            contextMenuItemRemoverItem.setDisable(false);
        }

    }

    private String inputDialog(String value) {
        TextInputDialog dialog = new TextInputDialog(value);
        dialog.setTitle("Entrada de dados.");
        dialog.setHeaderText("Atualização do campo de observação");
        dialog.setContentText("Observação: ");

        Optional<String> result = dialog.showAndWait();

        if (result.isPresent()) {
            return result.get();
        } else {
            return "Cancelado";
        }
    }

    @FXML
    private void handleContextMenuItemRemoverItem() {
        ItemOs itemOs
                = tableViewItensDeServico.getSelectionModel().getSelectedItem();
        int index = tableViewItensDeServico.getSelectionModel().getSelectedIndex();
        ordemServico.getItensDeOs().remove(index);
        observableListItemOs = FXCollections.observableArrayList(ordemServico.getItensDeOs());
        tableViewItensDeServico.setItems(observableListItemOs);

        tfTotal.setText(String.format("%.2f", ordemServico.getTotal()));
    }

}
