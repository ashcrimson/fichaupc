<?php
include("../funciones.global.inc.php");
include("../funciones.inc.php");
verifica_sesion(false);

?>
<script src="jquery/js/jquery.form.js"></script>
<script>

$("#btngrabartraslado").button({icons: {primary: "ui-icon-plus"}});
var optionsTraslado = {

  success:       showResponseTraslado
};
function showResponseTraslado(responseText, statusText, xhr, $form) {
  resp = JSON.parse(responseText);
  if (resp.estado == 1){
    alert("Traslado Registrado Correctamente");
    $('#list-fichas').trigger( 'reloadGrid' );
    $("#vwficha").dialog('close');
  }
  else{
        alert(resp.msg);
  }
}
$('#myFormTraslado').ajaxForm(optionsTraslado);
</script>
<?php
$db=genera_adodb();
	$ficha=retorna_ficha($db,$_REQUEST["id_ficha"]);
    
print("<form id=\"myFormTraslado\" method=\"post\" onsubmit=\"$('#btngrabartraslado').button('disable');\" action=\"Servicios/setTraslado.php\">");
print("<input type=\"hidden\" name=\"id_ficha\" value=\"".$_REQUEST["id_ficha"]."\">");
print("<table width=\"100%\">");
print("<td ><select name= \"traslado\" id=\"traslado\"> <option value=\"\">Seleccionar Traslado</option>");
    $recordset = $db->Execute("select * from tipo_ficha where (subtipo_ficha <> 0) and (tipo_ficha =1)");
               if (!$recordset) die("hhh".$db->ErrorMsg());  
               while ($arr = $recordset->FetchRow()) {
				if(($arr["tipo_ficha"] !=$ficha[0]["tipo_ficha"]) ||($arr["subtipo_ficha"] !=$ficha[0]["subtipo_ficha"])) 
				 print("<option value='".$arr["tipo_ficha"]."_".$arr["subtipo_ficha"]."'>".$arr["desc_tipo_ficha"]."</option> ");
			   } 	
print("</select></td></tr>");  

print("<tr>");
print("<tr><td ><center><button type=\"submit\" id=\"btngrabartraslado\">Grabar Traslado</button></center></td></tr>");
print("</table>");
print("</form>");
$db->disconnect();  
?>