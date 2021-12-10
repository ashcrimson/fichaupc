<?php
include("funciones.global.inc.php");
include("funciones.inc.php");
$db=genera_adodb();
$sql="select id_ficha,
cod,
obs
from antecedentes_ficha";
$recordset = $db->Execute($sql);
if (!$recordset) die("hhh".$db->ErrorMsg());
while ($arr = $recordset->FetchRow()) {
  print("--->".$arr["id_ficha"]);
      $sql1="update antecedentes_ficha set 
obs=?

where id_ficha=? and cod=?";
      $recordset1 = $db->Execute($sql1,array(
gzdeflate($arr["obs"]),
$arr["id_ficha"],
$arr["cod"]
)
);
  /*if (!$recordset1){
 echo("hhh".$db->ErrorMsg());
 }*/
}
$db->disconnect();
?>