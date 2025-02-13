<style>
    .areas {
        border: 1px grey dotted;
        min-height: 100px;
        overflow: hidden;
    }
    
    .area_opcoes {
        display: none;
    }
    
    .dashboardSalvar {
        display: none;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2>
                <?= $defaultDashboard -> getDescricao() ?>
                <span class="dashboardEditar" style="font-size: 12px">[ <a href="javascript: void(0)" onclick="$('.area_opcoes').show(); $(this).parent().hide();$('.dashboardSalvar').show()">Editar</a> ]</span>
                <span class="dashboardSalvar" style="font-size: 12px">[ <a href="javascript: void(0)" onclick="$('.area_opcoes').hide()">Salvar</a> ] [ <a href="javascript: void(0)" onclick="$('.area_opcoes').hide(); $(this).parent().hide();$('.dashboardEditar').show()"">Cancelar</a> ]</span>
            </h2>
        </div>
    </div>
    <div class="row">
        <div id="area_1" class="areas col-sm-6"><div class="area_conteudo"></div></div>
        <div id="area_2" class="areas col-sm-6"><div class="area_conteudo"></div></div>
    </div>
    <div id="htmlSelAddOns" style="display: none">
        <select id="selAddOn" onChange="controlAddOn.change($(this).parent().parent(), $(this).val())">
        </select>
    </div>
</div>

<script>
var controlAddOn = {
    addOns : {
        1 : {
            descricao : 'Addon 1',
            url : '/Geral/Usuario/addOn1',
            dados : []
        },
        2 : {
            descricao : 'Addon 2',
            url : '/Geral/Usuario/addOn2',
            dados : []
        }
    },
    
    change : function(area, idAddOn) {
        var id = this.getIdArea($(area).attr('id'));
        
        atualizarArea(id, this.addOns[idAddOn])
        
        //alert(area);
    },
    
    iniAddOns : function()
    {
        $.each(this.addOns, function(i, conf) {
            $("#selAddOn").append('<option value="'+i+'">'+conf['descricao']+'</option>');
        });
    },
    
    getIdArea : function(idArea)
    {
        var id = idArea.split('_');
        return id.pop();
    }
}

controlAddOn.iniAddOns();
</script>


<script>
    // versão de exemplo
    jQuery(document).ready(function(){
        $("#area_1 .area_conteudo").load('/Geral/Index/grafico1','');
        $("#area_2 .area_conteudo").load('/Geral/Index/grafico2','');
    });
    /*
    Função original que chama pela base de dados do dashboard
    function atualizarArea(idArea, conf)
    {
        $("#area_"+idArea+" .area_conteudo").load(conf['url'], conf['dados']);

        if(!$("#area_"+idArea+" #selAddOn").length) {
            $("#area_"+idArea).prepend('<div class="area_opcoes">'+$("#htmlSelAddOns").html()+'</div>');
        }
    }

    var areas = <?= $defaultDashboard -> getConf(true) ?>;

    $.each(areas, function(idArea, conf) {
        atualizarArea(idArea, conf);
    });
    */
</script>

<script>
/*
$.ajax({
    method: "POST",
    url: "http://reitoriaudesc.rhid.com.br/Main/wsUI.asmx/Login",
    data: {send:'', login:'04171018994', pass:'105114', keep:false, rcpt:null},
    dataType: "json",
    //contentType: "application/json",
    success: function( data ) {
      console.log(data);
    }
});*/
</script>