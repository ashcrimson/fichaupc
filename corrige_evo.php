<?php
include("funciones.global.inc.php");
include("funciones.inc.php");
$db=genera_adodb();

$sql="select *
from evolucion where id_ficha=1188";
$recordset = $db->Execute($sql);
if (!$recordset) die("hhh".$db->ErrorMsg());
while ($arr = $recordset->FetchRow()) {
//  $str=str_replace('<form id="form1" action="listado_estudio.aspx?rut=4599919-K&amp;num_orden=3122346&amp;usu=consulta&amp;id=178&amp;n=1&amp;f=&amp;nom=hector&amp;ape1=viva&amp;ape2=val&amp;b=3" method="post" name="form1">','',gzinflate($arr["evolucion"]));
$str=str_replace('<form','<formresp',gzinflate($arr["evolucion"]));
$str=str_replace('</form','</formresp',$str);
  
  print($str);
  
      $sql1="update evolucion set 
evolucion=?

where id_ficha=? and sec_evo=?";
      $recordset1 = $db->Execute($sql1,array(
gzdeflate($str),
$arr["id_ficha"],
$arr["sec_evo"]
					     ));

  if (!$recordset1){
 echo("hhh".$db->ErrorMsg());
 }
}
$db->disconnect();
?>