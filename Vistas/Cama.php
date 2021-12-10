<?php
include("../funciones.global.inc.php");
include("../funciones.inc.php");
verifica_sesion(false);

?>
<script src="jquery/js/jquery.form.js"></script>
<script>

$("#btngrabarcama").button({icons: {primary: "ui-icon-plus"}});
var optionsCama = {

  success:       showResponseCama
};
function showResponseCama(responseText, statusText, xhr, $form) {
  resp = JSON.parse(responseText);
  if (resp.estado == 1){
    alert("Cama Registrada Correctamente");
    $('#list-fichas').trigger( 'reloadGrid' );
    $("#vwficha").dialog('close');
  }
  else{
        alert(resp.msg);
  }
}
$('#myFormCama').ajaxForm(optionsCama);
</script>
<?php
$db=genera_adodb();
	$ficha=retorna_ficha($db,$_REQUEST["id_ficha"]);
	 $valores=array();
  $valores[]=$_REQUEST["tipo_ficha"];
  $valores[]=$_REQUEST["subtipo_ficha"] ;
  
  $sql="select 
a.cant_cama
from 
tipo_ficha a
where a.tipo_ficha=? and a.subtipo_ficha=?";
  $recordset = $db->Execute($sql,$valores);
  if (!$recordset) die("ssshhh $run".$db->ErrorMsg());
  $lim=0;
  while ($arr = $recordset->FetchRow()) {
    $lim=$arr["cant_cama"];
  }
 
    $db->disconnect();  
print("<form id=\"myFormCama\" method=\"post\" onsubmit=\"$('#btngrabarcama').button('disable');\" action=\"Servicios/setCama.php\">");
print("<input type=\"hidden\" name=\"id_ficha\" value=\"".$_REQUEST["id_ficha"]."\">");
print("<table width=\"100%\">");
print("<td ><select name= \"cama\" id=\"cama\"> <option value=\"\">Seleccionar Nro de Cama</option>");
  
  
	
   for($i=1;$i<=$lim;$i++){
    $selected="";
      if($i ==$ficha[0]["cama"])
       $selected="selected";
      print("<option value=\"".$i."\" $selected>".$i."</option>");
   }
print("</select></td></tr>");  

print("<tr>");
print("<tr><td ><center><button type=\"submit\" id=\"btngrabarcama\">Grabar Cama</button></center></td></tr>");
print("</table>");
print("</form>");
?>