<div id="container"></div>

<script>
    jQuery(document).ready(function(){
      // Build the chart
      Highcharts.chart('container', {
          chart: {
              plotBackgroundColor: null,
              plotBorderWidth: null,
              plotShadow: false,
              type: 'pie'
          },
          title: {
              text: 'Modelo de Gráfico 2'
          },
          tooltip: {
              pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
          },
          plotOptions: {
              pie: {
                  allowPointSelect: true,
                  cursor: 'pointer',
                  dataLabels: {
                      enabled: false
                  },
                  showInLegend: true
              }
          },
          series: [{
              name: 'Brands',
              colorByPoint: true,
              data: [{
                  name: 'Chrome',
                  y: 61.41,
                  sliced: true,
                  selected: true
              }, {
                  name: 'Internet Explorer',
                  y: 11.84
              }, {
                  name: 'Firefox',
                  y: 10.85
              }, {
                  name: 'Edge',
                  y: 4.67
              }, {
                  name: 'Safari',
                  y: 4.18
              }, {
                  name: 'Outros',
                  y: 7.05
              }]
          }]
      });
    });
</script>