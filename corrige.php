<?php
include("funciones.global.inc.php");
include("funciones.inc.php");
$db=genera_adodb();
$sql="select id_ficha,
motivo,
anamnesis,
fundamentos,
diag_pre,
plan,
procedencia,
obs_diag_pos
from ficha";
$recordset = $db->Execute($sql);
if (!$recordset) die("hhh".$db->ErrorMsg());
while ($arr = $recordset->FetchRow()) {
  print("--->".$arr["id_ficha"]);
      $sql1="update ficha set 
motivo=?,
anamnesis=?,
fundamentos=?,
diag_pre=?,
plan=?,
procedencia=?,
obs_diag_pos=?
where id_ficha=?";
      $recordset1 = $db->Execute($sql1,array(
gzdeflate($arr["motivo"]),
gzdeflate($arr["anamnesis"]),
gzdeflate($arr["fundamentos"]),
gzdeflate($arr["diag_pre"]),
gzdeflate($arr["plan"]),
gzdeflate($arr["procedencia"]),
gzdeflate($arr["obs_diag_pos"]),
$arr["id_ficha"])
);
  /*if (!$recordset1){
 echo("hhh".$db->ErrorMsg());
 }*/
}
$db->disconnect();
?>