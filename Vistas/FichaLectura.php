<?php
  include("../funciones.global.inc.php");
  include("../funciones.inc.php");
  $db=genera_adodb();
  verifica_sesion(false);

?>
<script src="jquery/js/jquery.form.js"></script>
<script src="tinymce/js/tinymce/tinymce.min.js"></script>

 <style>
.ui-autocomplete-loading {
background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat;
}
.enmdatos input{
  color: #fff;
  background: #B40404;
}
.enmdatos textarea{
  color: #fff;
  background: #B40404;
}
.enmdatos radio{
  color: #fff;
  background: #B40404;
}
.enmdatos select{
  color: #fff;
  background: #B40404;
}
.txt{
  width:200px;
    font-family: Arial;
   white-space: -moz-pre-wrap; /* Mozilla, supported since 1999 */
    white-space: -pre-wrap; /* Opera */
    white-space: -o-pre-wrap; /* Opera */
    white-space: pre-wrap; /* CSS3 - Text module (Candidate Recommendation) http://www.w3.org/TR/css3-text/#white-space */
    word-wrap: break-word;
}

pre {
   //display: inline-block;
    width:800px;
    font-family: Arial;
   white-space: -moz-pre-wrap; /* Mozilla, supported since 1999 */
    white-space: -pre-wrap; /* Opera */
    white-space: -o-pre-wrap; /* Opera */
    white-space: pre-wrap; /* CSS3 - Text module (Candidate Recommendation) http://www.w3.org/TR/css3-text/#white-space */
    word-wrap: break-word;
}

    
  

fieldset{
  width:1180px;
}
del{
  color:           hsl(0, 90%, 65%);
font-size:       15px;
text-decoration: line-through;
/*noinspection CssOverwrittenProperties*/
text-decoration: hsl(0, 90%, 65%) double line-through;
  
}
</style>
<script>
   function chequea_ges(id_ficha) {
		//  return;
        $.ajax({
            type: "POST",
            url: "Servicios/getCheckDiagGes.php",
	  data: { id_ficha: id_ficha},
            dataType: "json",
            async: false,
            success: function(data, textStatus) {
			  
                if (data.length > 0){
                   alert('Al menos uno de los diagnosticos ingresados debe ser notificado como GES');
                   for(j=0;j< data.length;j++){
                     
                       document.pdfges.pdf.value=data[j].pdf;
                       document.pdfges.target=data[j].coddiag;
                       document.pdfges.submit();
                   }
                   }
            }
        });
    }
	function cambia_pin() {
    $("#sn_epi").val($("#tipo_cierre").val());
	$("#spin").html("");
	
    if($("#tipo_cierre").val() =="S"){
	$("#spin").html('PIN : <input type="password" name="pin" size="4" maxlength="4"> ');
	  
	}
}
    if(document.myForm_admu.tipo_ficha.value ==2){
       $('#d_fundamentos').hide();
    $('#d_sofa').hide();
    $('#d_motivo').hide();
    $('#d_procedencia').hide();
  }


 $(function() {

jQuery("#list-diag_pos").jqGrid({
        datatype: 'clientSide',
        multiselect: true,
        width:700,height:70,
        rownumbers: true, 
        colNames:['Codigo','Descripcion'],
        colModel :[ 
        {name:'cod',index:'cod', width:55}, 
        {name:'descr',index:'descr', width:300}],
        rowNum:10,
        viewrecords: true,
        caption: 'Diagnosticos Postoperatorios'
   }); 
$("#btnDelDiagPos").button({icons: {primary: "ui-icon-del"}}); 
$("#btnDelDiagPos").click(function() {Eliminar('list-diag_pos')});

function Eliminar(id){ 
              var rowids = jQuery('#'+id).jqGrid('getGridParam', 'selarrrow');
              for (var i = rowids.length - 1; i >= 0; i--) {
                jQuery('#'+id).jqGrid('delRowData', rowids[i]);
              }
           }
function existe(id, cod ) {
var rowids = jQuery('#'+id).jqGrid('getRowData');
var encontrado =0;
              for (var i = 0; i < rowids.length; i++) {
                if(rowids[i].cod == cod)
                  encontrado =1;
              }
   return encontrado;
}

function log(id, message ) {
var data=message.split(" - ");
if (!existe(id,data[0]))
jQuery("#"+id).addRowData("1", {cod:data[0], descr:data[1]});
else
alert("El registro ya existe");
}
$( "#diag_pos_bus" ).autocomplete({
source: "Servicios/getDiagnosticos.php",
minLength: 3,
select: function( event, ui ) {
log("list-diag_pos", ui.item ?
 ui.item.value  :
"Nada seleccionado, valor buscado " + this.value );
$(this).val(''); return false;
}
});


});

          $( "#guardaraeu" ).button({icons: {primary: "ui-icon-disk"}});
</script>
<script type="text/javascript">
function recorre(id, fld ) {
var datos="";
var rowids = jQuery('#'+id).jqGrid('getRowData');
              for (var i = 0; i < rowids.length; i++) {
                if (i==0) 
                datos=datos + rowids[i].cod
                else 
                datos=datos + "," +rowids[i].cod
              }
   fld.value =datos;
}

function habilita_diag(ind,o,o_obs,id){

  

  if(ind == 1){
    o.disabled=false;
    o.value="";
    o_obs.disabled=true;
    o_obs.value="";
  }
  else{
    var cant=$('#'+id).getGridParam('records');
    for (var i =1; i <=cant; i++) {
     
     jQuery('#'+id).jqGrid('delRowData', 1);
    }
    o.disabled=true;
    o.value="";
    o_obs.disabled=false;
    o_obs.value="";
  }
   
}
function mostrar_plantilla(id_plantilla,id){


  $("#"+id).val("");
  if(id_plantilla != ""){
    $.ajax({
            type: "GET",
            url: "Servicios/getPlantilla.php",
	  data: { id_plantilla: id_plantilla},
            dataType: "json",
            async: false,
            success: function(data, textStatus) {
                $("#"+id).val(data.texto);
            }    
        });
     
  }
}

function valida_cierre(f){
   if((f.estado.value=="C")){
     if ((f.pin != null)&&(f.pin.value=="") ){
        alert('Debe ingresar su pin de firma electronica para cerrar esta ficha'); 
        return ;
     }
    
   }
  recorre("list-diag_pos",f.diag_pos);
   if((f.ind_diag_pos.value==1)&&(f.diag_pos.value =="")){
    alert('Debe ingresar al menos un diagnostico  de egreso'); 
    return false;
   }
   if((f.ind_diag_pos.value==2)&&(f.obs_diag_pos.value =="")){
    alert('Debe ingresar las observaciones de diagnostico de egreso'); 
    return false;
   } 
   var fec1=$('#fec_ing_uci').html().substr(6, 4)+$('#fec_ing_uci').html().substr(3, 2)+$('#fec_ing_uci').html().substr(0, 2)+$('#hora_ini_uci').html()+$('#minuto_ini_uci').html();
   if(f.fec_egr_uci.value ==""){
     alert('La fecha de Egreso UCI no puede ser nula'); 
      return false;
   }
   var fec2=f.fec_egr_uci.value.substr(6, 4)+f.fec_egr_uci.value.substr(3, 2)+f.fec_egr_uci.value.substr(0, 2)+f.hora_ter_uci.value+f.minuto_ter_uci.value;
   if (fec2 < fec1) {
      alert('El Egreso UCI no puede ser menor al Ingreso UCI del Paciente'); 
      return false;
   }
  
   if(f.resumen.value ==""){
     alert('El Resumen no puede ser nulo'); 
      return false;
   }
   if(f.tratamiento.value ==""){
     alert('El Tratamiento no puede ser nulo'); 
      return false;
   }
   if(f.sn_covid.value ==""){
     alert('Debe Indicar si el paciente es COVID 19 o no'); 
      return false;
   }
  /* if ((f.evolucion != null)&&(f.evolucion.value.trim() =="")&&(f.adjunto != null)&&(f.adjunto.value.trim() !="")) {
      alert('No puede subir un adjunto a una evolucion sin haber registrado algo en dicha evolucion'); 
      return false;
   }
   */
   
   
   
$( "#dummy" ).click();
    return ;
}
$.getJSON("Servicios/getFicha.php?id_ficha=<?php echo $_REQUEST['id_ficha']?>",
{
format: "json"
},
function(json) {
     
     $("#myForm_admu").reset(); 
     if(json[0]!= null){
	      j=0;
      if (json[0].datos.length > 0)
     for(j=0;j< json[0].datos.length;j++){
        $("#datos_"+json[0].datos[j].cod).html("<pre>"+json[0].datos[j].valor+"</pre>");
        $("#obs_datos_"+json[0].datos[j].cod).html("<pre>"+json[0].datos[j].obs+"</pre>");
     }
     j=0;
      
     if (json[0].examen_fisico.length > 0)
     for(j=0;j< json[0].examen_fisico.length;j++){
        $("#exa_"+json[0].examen_fisico[j].cod).html(json[0].examen_fisico[j].valor);
     }
     j=0;
      
     if (json[0].antecedentes.length > 0)
     for(j=0;j< json[0].antecedentes.length;j++){
        if(json[0].antecedentes[j].valor =="S"){
        
          $("#ant_"+json[0].antecedentes[j].cod).html('SI');
          $("#obs_ant_"+json[0].antecedentes[j].cod).html(json[0].antecedentes[j].obs);
        }
        else{
          $("#ant_"+json[0].antecedentes[j].cod).html('NO');
          $("#obs_ant_"+json[0].antecedentes[j].cod).html(json[0].antecedentes[j].obs);
        }
     }
     $('#run').html(json[0].run);
     $('#dv_run').html(json[0].dv_run);
     document.myForm_admu.id_ficha.value=json[0].id_ficha;
     document.myForm_admu.tipo_ficha.value=json[0].tipo_ficha;
	 document.myForm_admu.subtipo_ficha.value=json[0].subtipo_ficha;
	 
    
     $('#nombre').html(json[0].nombre);
     $('#apellido').html(json[0].apellido);
     $('#fec_nac').html(json[0].fec_nac);
     $('#edad').html(json[0].edad);
     $('#ind_sexo').html(json[0].ind_sexo);
     $('#fec_ing_hosp').html(json[0].fec_ing_hosp);
     $('#hora_ini_hosp').html(json[0].hora_ini_hosp);
     $('#minuto_ini_hosp').html(json[0].minuto_ini_hosp);
     $('#fec_ing_uci').html(json[0].fec_ing_uci);
     $('#hora_ini_uci').html(json[0].hora_ini_uci);
     $('#minuto_ini_uci').html(json[0].minuto_ini_uci);
      <?php
    
      if($_REQUEST["accion"]!="C"){
   
     ?>
     $('#fec_egr_uci').html(json[0].fec_egr_uci);
     $('#hora_ter_uci').html(json[0].hora_ter_uci);
     $('#minuto_ter_uci').html(json[0].minuto_ter_uci);
	 $('#sn_covid').html(((json[0].sn_covid == 'S') ? ' Si' : 'No'));
     $('#resumen').html("<pre>"+(isEmpty(json[0].resumen) ? '' : json[0].resumen)+"</pre>");
     $('#tratamiento').html("<pre>"+(isEmpty(json[0].tratamiento) ? '' : json[0].tratamiento)+"</pre>");
       j=0;
      html="<ol>";
      
     if (json[0].diag_pos != null)
     for(j=0;j< json[0].diag_pos.length;j++){
        html=html+"<li>"+json[0].diag_pos[j].cod+" - "+json[0].diag_pos[j].descrip+"</li>";
     }
     html=html+"</ol>";
     $('#diag_pos').html(html);
    
     <?php
   }
   else{
    ?>
	
     $('#fec_egr_uci').val(json[0].fec_egr_uci);
     $('#hora_ter_uci').val(json[0].hora_ter_uci);
     $('#minuto_ter_uci').val(json[0].minuto_ter_uci);
     $('#obs_diag_pos').val(json[0].obs_diag_pos);
	 $('#sn_covid').val(json[0].sn_covid);
     
     $('#resumen').val((isEmpty(json[0].resumen) ? '' : json[0].resumen));
     $('#tratamiento').val((isEmpty(json[0].tratamiento) ? '' : json[0].tratamiento));
     
       j=0;
      
     if (json[0].diag_pos != null)
     for(j=0;j< json[0].diag_pos.length;j++){
       
	    $('#list-diag_pos').addRowData(j,{cod:json[0].diag_pos[j].cod,descr:json[0].diag_pos[j].descrip});
      }
     
      
      
    <?php
   }
   


    ?>
     $('#sofa').html(json[0].sofa);
     $('#cama').html(json[0].cama);
     
     
     
     
   

   }
});




  var optionsadmu = { 
        
        success:       showResponseAdmu
  };
 
   function showResponseAdmu(responseText, statusText, xhr, $form)  { 
   resp = JSON.parse(responseText);
   if (resp.estado ==3){
     alert('Datos de Cierre Guardados');
      jQuery("#list-fichas").jqGrid('setGridParam',{datatype:'json'}).trigger('reloadGrid');
     carga_slider(); 
     $('#vwficha').dialog('close'); 
    
   }
   else if (resp.estado == 4){
     alert('Ficha Cerrada');
	 jQuery("#list-fichas").jqGrid('setGridParam',{datatype:'json'}).trigger('reloadGrid');
	 document.verreceta.id_ficha.value='<?php echo $_REQUEST['id_ficha'];?>';
	 document.verreceta.submit();
	  if(resp.id_firma_doc !="")
	 viewPdf(resp.id_firma_doc);
     chequea_ges('<?php echo $_REQUEST['id_ficha'];?>');
      
     carga_slider(); 
     $('#vwficha').dialog('close'); 
    
   }
   
   else{
     alert(resp.error);
   
   }
   
  }
  jQuery.fn.reset = function () {
  $(this).each (function() { this.reset(); });
}
$('.tabenm').hide();
$('.enmdatos').hide();

$(".buttabenm").button({icons: {primary: "ui-icon-pencil"}});
 $( "#cerraraeu" ).button({icons: {primary: "ui-icon-close"}});
   <?php
   if($_REQUEST["accion"]=="C"){
   ?>
 $('#fec_egr_uci').datepicker({changeMonth:true,changeYear:true,yearRange:"-110:+0",maxDate: new Date(),dateFormat:"dd/mm/yy"});
<?php
   }
   ?>

  $('#myForm_admu').ajaxForm(optionsadmu);
  </script>
<form id="myForm_admu" name="myForm_admu"  onsubmit="return valida_cierre(this);" action="Servicios/setFicha.php" method="post"> 

<fieldset>
<?php
if($_REQUEST["id_ficha"] != ""){
 $dbh=genera_adodb("portal");
 $creador="Creada por ";
 $ficha=retorna_ficha($db,$_REQUEST["id_ficha"] );
 $creador.=utf8_encode(retorna_datos_portal($dbh,$ficha[0]["usuario"]));
  $creador.=" con fecha : ".$ficha[0]["fec_creacion"];
 $dbh->disconnect();
}
?>  
<legend><b>FICHA UCI (<?php echo retorna_tipo_ficha($db,$_REQUEST["tipo_ficha"],$_REQUEST["subtipo_ficha"]) .") ". $creador ;?></b></legend>
<fieldset>
<legend><b>Identificacion</b></legend>


  <table><tr><td><b>RUT: </b><span id="run" ></span>-<span id="dv_run"></span> </td></tr></table>


</fieldset>
<input type="hidden" name="trans" value="cierre">
<input type="hidden" id="sn_epi" name="sn_epi" value="<?php echo $_REQUEST["sn_epi"]; ?>">

<input type="hidden" name="id_ficha" >
<input type="hidden" name="diag_pos" >
<input type="hidden" name="tipo_ficha" value="<?php echo $_REQUEST["tipo_ficha"]; ?>">
<input type="hidden" name="subtipo_ficha" value="<?php echo $_REQUEST["subtipo_ficha"]; ?>">

     <input type="hidden" name="estado" value="D">
<fieldset>
<legend><b>Datos Personales</b></legend>
<table>
<tr>
 <td><b>Nombre:</b></td>
 <td><span id="nombre" /></td>
 <td><b>Apellidos:</b></td>
 <td><span id="apellido" /></td>
</tr>
<tr>
 <td><b>Fecha Nacimiento:</b></td>
 <td><span id="fec_nac"/></td>
 <td><b>Edad:</b></td>
 <td><span id="edad" /></td>
</tr>
<tr>
 <td><b>Sexo:</td>
 <td><span id="ind_sexo" /></span></td>
 <td colspan="2">&nbsp;</td>
</tr>
 <tr><td><b>Fecha y Hora Hospitalizacion: </b> </td><td colspan="3"><span id="fec_ing_hosp" /> <span id="hora_ini_hosp" />:<span id="minuto_ini_hosp" /> 
 </td>
</tr>
  <tr><td><b>Fecha y Hora Ingreso UCI:</b>     </td><td colspan="3"><span id="fec_ing_uci" /> <span id="hora_ini_uci" />:<span id="minuto_ini_uci" /> 
 
 </td>
</tr>
<tr id="d_sofa">
 <td><b>Sofa Ingreso:</b></td>
 <td colspan="3"><span id="sofa" />
 </td>
</tr>
<tr id="d_cama">
 <td><b>Nro Cama:</b></td>
 <td colspan="3"><span id="cama" />
 </td>
</tr>

</table>
</fieldset>
<?php
     $valores=array();
     $valores[]=$_REQUEST["tipo_ficha"];
     
     $recordset = $db->Execute("select * from seccion where cod_seccion=1 and tipo_ficha=? order by 1",$valores);
  if (!$recordset) die("hhh".$db->ErrorMsg());

  while ($arr = $recordset->FetchRow()) {
    print("<fieldset>");
    print("<legend><b id='lbl'>".utf8_encode($arr["desc_seccion"])."</b></legend>");
   
   
	
 
     $valores=array();
    
     $valores[]=$arr["cod_seccion"];
	 $valores[]=$_REQUEST["tipo_ficha"];
     $recordset_d = $db->Execute("select * from datos where  cod_seccion=? and tipo_ficha=?  order by 1",$valores);
     if (!$recordset_d) die("hhh".$db->ErrorMsg());
     print("<table border='0' width=\"80%\">");
     while ($arr_d = $recordset_d->FetchRow()) {
      $arr_d["descr"]=utf8_encode($arr_d["descr"]);
      $required=($arr_d["mand"]=='S')?'required':'';
      if($_REQUEST["id_ficha"] ==""){
      if($arr_d["tipo_entrada"] =="TA"){
       $rtf=($arr_d["rtf"]=='S')?'rtf':''; 
       print("<tr><td><textarea name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  cols='200' rows='4'  $required class='$rtf'>".$arr_d["prellenado"]."</textarea></td></tr>");
      }
      elseif($arr_d["tipo_entrada"] =="T"){
       print("<tr><td><input type='text' name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  size='".$arr_d["largo"]."' maxlength='".$arr_d["largo"]."'  $required value='".$arr_d["prellenado"]."'></td></tr>");
      }
      	  elseif($arr_d["tipo_entrada"] =="D"){
       print("<tr><td><b id='lbl'>".$arr_d["descr"]."</b></td><td><input type='text' name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  size='10' maxlength='10'  $required value='".$arr_d["prellenado"]."' readonly class='fecha'></td></tr>");
      }
	  elseif($arr_d["tipo_entrada"] =="R"){
      $rang=explode(",",$arr_d["rango"]);
      print("<tr><td width=\"250px\"><b id='lbl'>".$arr_d["descr"]."</b></td><td>");
      foreach($rang as $kk => $j)
      
        print("<input type='radio' name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."' value='".$j."'  $required > ".$j."");
		if($arr_d["sn_obs"] =="S")
		  print(" <input type='text' size='40' maxlength='40' name='obs_datos_".$arr_d["cod"]."' id='obs_datos_".$arr_d["cod"]."'>");
		
        print("</td></tr>");
      }
      elseif($arr_d["tipo_entrada"] =="S"){
      $rang=explode("-",$arr_d["rango"]);
      print("<tr><td><select name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  $required>");
      for($j=$rang[0];$j<=$rang[1];$j++)
        print("<option value='$j'>$j</option>");
      print("</select></td></tr>");
       
      }
      }
      else{
        $class=array();
        $clase="";
        if($required !="")
        $class[]=$required;
      if($arr_d["tipo_entrada"] =="TA"){
       $rtf=($arr_d["rtf"]=='S')?'rtf':'';
       if($rtf !="")
        $class[]=$rtf;
       $clase=implode(",",$class); 
       print("<tr><td colspan='2'><b id='lbl'>".$arr_d["descr"]."</b><br><div id='datos_".$arr_d["cod"]."'></div></td></tr><tr class=\"enmdatos\"><td><textarea name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."'  cols='200' rows='4'  class='$clase'>".$arr_d["prellenado"]."</textarea></td></tr>");
      }
      elseif($arr_d["tipo_entrada"] =="T"){
       $clase=implode(",",$class); 
       
       print("<tr><td><b id='lbl'>".$arr_d["descr"]."</b><br><span id='datos_".$arr_d["cod"]."'></span></td></tr><tr class=\"enmdatos\"><td><input type='text' name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."'  size='".$arr_d["largo"]."' maxlength='".$arr_d["largo"]."'  class='$clase' value='".$arr_d["prellenado"]."'></td></tr>");
      }
       elseif($arr_d["tipo_entrada"] =="D"){
		$class[]="fecha";
       $clase=implode(",",$class); 
       
       print("<tr><td><b id='lbl'>".$arr_d["descr"]."</b><br><span id='datos_".$arr_d["cod"]."'></span></td></tr><tr class=\"enmdatos\"><td><input type='text' name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."'  size='8' maxlength='8'  class='$clase' value='".$arr_d["prellenado"]."'></td></tr>");
      }
      	  elseif($arr_d["tipo_entrada"] =="R"){
      $clase=implode(",",$class); 
       
      $rang=explode(",",$arr_d["rango"]);
      print("<tr><td ><b id='lbl'>".$arr_d["descr"]."</b><br><span id='datos_".$arr_d["cod"]."'></span> <div id='obs_datos_".$arr_d["cod"]."'></div></td></tr><tr class=\"enmdatos\"><td>");
      foreach($rang as $kk => $j)
      
        print(" <input type='radio' name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."' value='".$j."' class='$clase'> ".$j."");
      if($arr_d["sn_obs"] =="S")
		  print("<input type='text' size='40' maxlength='40' name='obs_enmdatos_".$arr_d["cod"]."' id='obs_enmdatos_".$arr_d["cod"]."'>");

      print("</td></tr>");
       
      }
      elseif($arr_d["tipo_entrada"] =="S"){
      $clase=implode(",",$class); 
       
      $rang=explode("-",$arr_d["rango"]);
      print("<tr><td><b id='lbl'>".$arr_d["descr"]."</b><br><span id='datos_".$arr_d["cod"]."'></span></td></tr><tr class=\"enmdatos\"><td><select name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."' class='$clase'>");
      for($j=$rang[0];$j<=$rang[1];$j++)
        print("<option value='$j'>$j</option>");
      print("</select></td></tr>");
       
      }  
      }
     }
      print("</table>");
    
    print("</fieldset>");
    
  }
  ?>
<fieldset>
<legend><b>Antecedentes</b></legend>
 <?php
     $valores=array();
     $valores[]=$_REQUEST["tipo_ficha"];
     $recordset = $db->Execute("select * from antecedentes where tipo_ficha=? order by 2",$valores);
  if (!$recordset) die("hhh".$db->ErrorMsg());
      print("<table border='0' >");

  while ($arr = $recordset->FetchRow()) {
    $arr["descr"]=utf8_encode($arr["descr"]);
    print("<tr><td>".$arr["descr"]." : </td><td><span id='ant_".$arr["cod"]."' class='ant'>NO.&nbsp;</span></td><td><span id='obs_ant_".$arr["cod"]."' class='ant,pre' ></span></td></tr>");
 
  }
 print("</table>");

  ?>
</fieldset>
<fieldset>
<legend><b>Examen Fisico</b></legend>

    <?php
     $valores=array();
     $valores[]=$_REQUEST["tipo_ficha"];
     $recordset = $db->Execute("select * from examen_fisico where tipo_ficha=? order by 2,3",$valores);
  if (!$recordset) die("hhh".$db->ErrorMsg());
  $datos_ex=array();
  while ($arr = $recordset->FetchRow()) {
    $arr["descr"]=utf8_encode($arr["descr"]);
    $datos_ex[$arr["tipo"]][]=$arr;
  }
  if(count($datos_ex["G"])>0){
    print("<b>General</b><table border='0'>");
    foreach($datos_ex["G"] as $k => $fila){
        print("<tr><td>".$fila["descr"]." : </td><td><span id='exa_".$fila["cod"]."'  class='exa' /></td></tr>");
   }
    print("</table>");
    
  }
  if(count($datos_ex["S"])>0){
    print("<b>Segmentario</b><table border='0'>");
    foreach($datos_ex["S"] as $k => $fila){
      print("<tr><td>".$fila["descr"]." : </td><td><span id='exa_".$fila["cod"]."' class='exa' /></td></tr>");
    }
    print("</table>");
    
  }
  ?>
</fieldset>
<?php
     $valores=array();
     $valores[]=$_REQUEST["tipo_ficha"];
     
     $recordset = $db->Execute("select * from seccion where cod_seccion=2 and tipo_ficha=? order by 1",$valores);
  if (!$recordset) die("hhh".$db->ErrorMsg());

  while ($arr = $recordset->FetchRow()) {
    print("<fieldset>");
    print("<legend><b id='lbl'>".utf8_encode($arr["desc_seccion"])."</b></legend>");
   
   
	
 
     $valores=array();
    
     $valores[]=$arr["cod_seccion"];
	 $valores[]=$_REQUEST["tipo_ficha"];
     $recordset_d = $db->Execute("select * from datos where  cod_seccion=? and tipo_ficha=?  order by 1",$valores);
     if (!$recordset_d) die("hhh".$db->ErrorMsg());
     print("<table border='0' width=\"80%\">");
     while ($arr_d = $recordset_d->FetchRow()) {
      $arr_d["descr"]=utf8_encode($arr_d["descr"]);
      $required=($arr_d["mand"]=='S')?'required':'';
      if($_REQUEST["id_ficha"] ==""){
      if($arr_d["tipo_entrada"] =="TA"){
       $rtf=($arr_d["rtf"]=='S')?'rtf':''; 
       print("<tr><td><textarea name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  cols='200' rows='4'  $required class='$rtf'>".$arr_d["prellenado"]."</textarea></td></tr>");
      }
      elseif($arr_d["tipo_entrada"] =="T"){
       print("<tr><td><input type='text' name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  size='".$arr_d["largo"]."' maxlength='".$arr_d["largo"]."'  $required value='".$arr_d["prellenado"]."'></td></tr>");
      }
      	  elseif($arr_d["tipo_entrada"] =="D"){
       print("<tr><td><b id='lbl'>".$arr_d["descr"]."</b></td><td><input type='text' name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  size='10' maxlength='10'  $required value='".$arr_d["prellenado"]."' readonly class='fecha'></td></tr>");
      }
	  elseif($arr_d["tipo_entrada"] =="R"){
      $rang=explode(",",$arr_d["rango"]);
      print("<tr><td width=\"250px\"><b id='lbl'>".$arr_d["descr"]."</b></td><td>");
      foreach($rang as $kk => $j)
      
        print("<input type='radio' name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."' value='".$j."'  $required > ".$j."");
		if($arr_d["sn_obs"] =="S")
		  print(" <input type='text' size='40' maxlength='40' name='obs_datos_".$arr_d["cod"]."' id='obs_datos_".$arr_d["cod"]."'>");
		
        print("</td></tr>");
      }
      elseif($arr_d["tipo_entrada"] =="S"){
      $rang=explode("-",$arr_d["rango"]);
      print("<tr><td><select name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  $required>");
      for($j=$rang[0];$j<=$rang[1];$j++)
        print("<option value='$j'>$j</option>");
      print("</select></td></tr>");
       
      }
      }
      else{
        $class=array();
        $clase="";
        if($required !="")
        $class[]=$required;
      if($arr_d["tipo_entrada"] =="TA"){
       $rtf=($arr_d["rtf"]=='S')?'rtf':'';
       if($rtf !="")
        $class[]=$rtf;
       $clase=implode(",",$class); 
       print("<tr><td colspan='2'><b id='lbl'>".$arr_d["descr"]."</b><br><div id='datos_".$arr_d["cod"]."'></div></td></tr><tr class=\"enmdatos\"><td><textarea name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."'  cols='200' rows='4'  class='$clase'>".$arr_d["prellenado"]."</textarea></td></tr>");
      }
      elseif($arr_d["tipo_entrada"] =="T"){
       $clase=implode(",",$class); 
       
       print("<tr><td><b id='lbl'>".$arr_d["descr"]."</b><br><span id='datos_".$arr_d["cod"]."'></span></td></tr><tr class=\"enmdatos\"><td><input type='text' name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."'  size='".$arr_d["largo"]."' maxlength='".$arr_d["largo"]."'  class='$clase' value='".$arr_d["prellenado"]."'></td></tr>");
      }
       elseif($arr_d["tipo_entrada"] =="D"){
		$class[]="fecha";
       $clase=implode(",",$class); 
       
       print("<tr><td><b id='lbl'>".$arr_d["descr"]."</b><br><span id='datos_".$arr_d["cod"]."'></span></td></tr><tr class=\"enmdatos\"><td><input type='text' name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."'  size='8' maxlength='8'  class='$clase' value='".$arr_d["prellenado"]."'></td></tr>");
      }
      	  elseif($arr_d["tipo_entrada"] =="R"){
      $clase=implode(",",$class); 
       
      $rang=explode(",",$arr_d["rango"]);
      print("<tr><td ><b id='lbl'>".$arr_d["descr"]."</b><br><span id='datos_".$arr_d["cod"]."'></span> <div id='obs_datos_".$arr_d["cod"]."'></div></td></tr><tr class=\"enmdatos\"><td>");
      foreach($rang as $kk => $j)
      
        print(" <input type='radio' name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."' value='".$j."' class='$clase'> ".$j."");
      if($arr_d["sn_obs"] =="S")
		  print("<input type='text' size='40' maxlength='40' name='obs_enmdatos_".$arr_d["cod"]."' id='obs_enmdatos_".$arr_d["cod"]."'>");

      print("</td></tr>");
       
      }
      elseif($arr_d["tipo_entrada"] =="S"){
      $clase=implode(",",$class); 
       
      $rang=explode("-",$arr_d["rango"]);
      print("<tr><td><b id='lbl'>".$arr_d["descr"]."</b><br><span id='datos_".$arr_d["cod"]."'></span></td></tr><tr class=\"enmdatos\"><td><select name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."' class='$clase'>");
      for($j=$rang[0];$j<=$rang[1];$j++)
        print("<option value='$j'>$j</option>");
      print("</select></td></tr>");
       
      }  
      }
     }
      print("</table>");
    
    print("</fieldset>");
    
  }
  ?>




<?php
   if ($_REQUEST["id_ficha"] != ""){
?>
<fieldset>
<legend><b>Evolucion</b></legend>
 
     <?php
     $valores=array();
     $valores[]=$_REQUEST["id_ficha"];
     $recordset = $db->Execute("select to_char(a.fecha,'DD/MM/YYYY HH24:mi:ss') as fecha_f,fecha,a.id_ficha,a.sec_evo,a.evolucion,a.usuario,(nvl((select max(x.sec) from enmienda x where x.id_ficha=a.id_ficha and x.sec_evo=a.sec_evo),0)) as enm,a.sn_interconsulta,a.cod_especialidad  from evolucion a where a.id_ficha=? order by fecha",$valores);
  if (!$recordset) die("hhh".$db->ErrorMsg());  
  $dbh=genera_adodb("portal");
while ($arr = $recordset->FetchRow()) {
  
  $nombre_us=retorna_datos_portal($dbh,$arr["usuario"]);
  
  $sec_evo=$arr["sec_evo"];
   $interconsulta ="&nbsp;";
  if($arr["sn_interconsulta"] =="S")
      $interconsulta ="<span> Es Interconsulta</span>";
  print("<fieldset ><legend><b>".$nombre_us." - " .$arr["fecha_f"] ." ". $interconsulta ."</b></legend>");
  //if($_SESSION["fichaupc"]["usuario"]==$arr["usuario"])
  //print("<textarea class='editor' id='evolucion_".$arr["sec_evo"]."' name='evolucion_".$arr["sec_evo"]."' style=\"width:100%;height:300px;\">".gzinflate($arr["evolucion"])."</textarea>");
  //else
  if($arr["enm"] > 0){
  print("<del style=\"width:600px !important;max-width:600px !important;display:inline-block !important;\">");
  print(str_ireplace("width","widtddh",gzinflate($arr["evolucion"])));
  }
  else{
  print("<div style=\"width:600px !important;max-width:600px !important;display:inline-block; !important;\">".str_ireplace("width","widtddh",gzinflate($arr["evolucion"]))."</div>");
  }
  $valores=array();
  $valores[]=$_REQUEST["id_ficha"];
  $valores[]=$sec_evo;
  $recordset_img = $db->Execute("select id_ficha,sec_evo,sec,enmienda,fecha,to_char(fecha,'DD/MM/YYYY HH24:mi:ss') as fecha_f from enmienda where id_ficha=? and sec_evo=? order by fecha",$valores);
  if (!$recordset_img) die("hhh".$db->ErrorMsg());
  $cont=0;
  while ($arr_img = $recordset_img->FetchRow()) {
      if($arr["enm"] ==$arr_img["sec"])
       print("</del>");

    print("<b>".$arr_img["fecha_f"]."</b><br>".gzinflate($arr_img["enmienda"]));
  }
  
  $valores=array();
  $valores[]=$_REQUEST["id_ficha"];
  $valores[]=$sec_evo;
  $recordset_img = $db->Execute("select id_ficha,sec_evo,sec from anexo where id_ficha=? and sec_evo=?",$valores);
  if (!$recordset_img) die("hhh".$db->ErrorMsg());
  $cont=0;
  while ($arr_img = $recordset_img->FetchRow()) {
    $cont++;
    if($cont ==1){
      print("<table border='1'><tr><th>Adjunto</th></tr>");
     
    }
      print("<tr><td><img src='Servicios/getAdjunto.php?id_ficha=".$_REQUEST["id_ficha"]."&sec_evo=".$sec_evo."&sec=".$arr_img["sec"]."' width='750px'  height='300px' ></td></tr>");
    
  }
  if($cont >0)
    print("</table>");
  print("</fieldset>");
  
    }
    $dbh->disconnect();
    ?>

</fieldset>
<?php
   }
   ?>
   <?php
   if($_REQUEST["accion"]=="C"){
   ?>
   <fieldset>
    <legend><b>Datos Cierre Ficha</b></legend>
   <table>
	<tr><td colspan="2">Paciente COVID 19:  <select id="sn_covid" name="sn_covid" ><option value="">Elegir</option><option value="S">Si</option><option value="N">No</option></select> </td></tr>

    <tr><td colspan="2">Diagnostico Egreso CIE10 <input type="hidden" name="ind_diag_pos" value="1"> <input id="diag_pos_bus" name="diag_pos_bus"  size="100"></td></tr>
<tr><td colspan="2">Observaciones Diagnostico Egreso<br><input type="text" id="obs_diag_pos" name="obs_diag_pos"  size="100" maxlength="300" ></td></tr>
<tr><td colspan="2"><table id="list-diag_pos"><tr><td/></tr></table><br><button type="button" id="btnDelDiagPos">Eliminar Diagnosticos Egreso</button></td></tr>

  <tr><td colspan="2"><center>Fecha Egreso UCI:  <input type="text" id="fec_egr_uci" name="fec_egr_uci" size="10" maxlength="10" readonly></center>
 </td>
 <td>Hora :</td>
 <td><select name="hora_ter_uci" id="hora_ter_uci">
<?php
  for($i=0;$i<=23;$i++){
  if($i<=9)
  $hora ="0".$i;
  else
  $hora =$i;
   
  print("<option value=\"$hora\">$hora</option>");
  }
?>
 </select> : <select name="minuto_ter_uci" id="minuto_ter_uci">
<?php
  for($i=0;$i<=59;$i++){
  if($i<=9)
  $minuto ="0".$i;
  else
  $minuto =$i;
   
  print("<option value=\"$minuto\">$minuto</option>");
  }
?>
 </select> 
 </td>
</tr>
</table>
<fieldset>
<legend><b>Resumen</b></legend>
<textarea id="resumen" name="resumen" cols="200" rows="4"></textarea>

</fieldset>
<fieldset>
<legend><b>Tratamiento</b></legend>
<?php
 $plantillas=retorna_plantillas($db,"M");
print("<center>Plantilla de Medicamentos : <select name='plantilla_m' onchange='mostrar_plantilla(this.value,\"tratamiento\");'>");
print("<option value=''>Seleccione Plantilla</option>");

foreach($plantillas as $k => $fila){
print("<option value='".$fila["id"]."'>".$fila["nombre"]."</option>");
}
print("</select>");
?>
<br>
<textarea id="tratamiento" name="tratamiento" cols="200" rows="4"></textarea>
<br><button type="button" onclick="mostrar_embebido('<?php echo $_REQUEST["id_ficha"]; ?>','P','<?php echo $_SESSION["fichaupc"]["usuario"];?>',$('#run').html(),$('#dv_run').html(),'N');">Crear Receta</button>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" onclick="mostrar_embebido('<?php echo $_REQUEST["id_ficha"]; ?>','P','<?php echo $_SESSION["fichaupc"]["usuario"];?>',$('#run').html(),$('#dv_run').html(),'M');">Ver Receta(s)</button>
<br><button type="button" onclick="mostrar_embebido_referencia('<?php echo $_REQUEST["id_ficha"]; ?>','FU','<?php echo $_SESSION["fichaupc"]["usuario"];?>',$('#run').html(),$('#dv_run').html());">Crear Interconsulta</button>

</fieldset>
  

</fieldset>
<table width="100%">
  <tr> 
	<td colspan="2"><center> Tipo de Cierre : <select name="tipo_cierre" id="tipo_cierre" onchange="cambia_pin()">
	  <option value="S" <?php if ($_REQUEST["sn_epi"] =="S") { ?>selected<?php }?>>CON EPICRISIS</option>
	  <option value="N" <?php if ($_REQUEST["sn_epi"] =="N") { ?>selected<?php }?>>SIN EPICRISIS</option>
	  
	</select></center></td>
  </tr>
  <tr>
   <td width="50%"> 
   <center><button type="button" onclick="document.myForm_admu.estado.value='D';valida_cierre(document.myForm_admu);" id="guardaraeu">Guardar Datos de Cierre</button>  </center>
   </td>
   <td width="50%"> 
   <center><span id="spin"><?php if ($_REQUEST["sn_epi"] =="S") { ?>PIN : <input type="password" name="pin" size="4" maxlength="4"> <?php } ?></span><button type="button" onclick="document.myForm_admu.estado.value='C';valida_cierre(document.myForm_admu);" id="cerraraeu">Cerrar Ficha</button>  </center>
   </td>
  </tr> 
</table>
<button type="submit" id="dummy" style="display: none">Dummy</button>

<?php
   }
   else{
   ?>
    <fieldset>
    <legend><b>Datos Cierre Ficha</b></legend>
    <table>
	  <tr><td>Paciente COVID 19:     </td><td colspan="3"><span id="sn_covid" /> </td></tr>
 
      <tr><td>Diagnosticos de Egreso:     </td><td colspan="3"><span id="diag_pos" /> 
 
 </td>
</tr>
       <tr><td>Fecha y Hora Egreso UCI:     </td><td colspan="3"><span id="fec_egr_uci" /> <span id="hora_ter_uci" />:<span id="minuto_ter_uci" /> 
 
 </td>
</tr>
    </table>
    <fieldset>
    <legend><b>Resumen</b></legend>
<span id="resumen"></span>

</fieldset>
<fieldset>
<legend><b>Tratamiento</b></legend>
<span id="tratamiento"></span>

</fieldset>
  
    </fieldset>
<?php
   }
   ?>

</form>
</fieldset>
<form name="pdfges" method='post' action="Servicios/getPDFGes.php">
        <input type='hidden' name="pdf">
        
    </form>
<?php
 $db->disconnect();
?>
