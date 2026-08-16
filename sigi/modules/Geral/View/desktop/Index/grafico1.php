<table id="grafico" style="display:none;">
<caption>Modelo de Gráfico 1</caption>
  <thead>
    <tr>
        <th>Mês</th>
        <th>Salário em reais (R$)</th>
        <th>Horas trabalhadas</th>
    </tr>
  </thead>
  <tbody>
    <tr>
        <td>Jan</td>
        <td>800</td>
        <td>160</td>
    </tr>
    <tr>
        <td>Fev</td>
        <td>600</td>
        <td>120</td>
    </tr>
  </tbody>
</table>

<div id="grafico_1"></div>

<script>
  jQuery(document).ready(function(){
    var config = createConfig("#grafico");
    iniChart(config);
  });
</script>