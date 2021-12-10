<?php
  /*
   Funcion Script : Inicio del Sistema 
   Ultima Modificacion: 12/09/2011  
  */
include("../funciones.global.inc.php");
include("../funciones.inc.php");
$db=genera_adodb();
$valores=array();
   $sql="update ficha a set 
a.cama=?
where a.ID_ficha=?";
$valores[]=$_REQUEST["cama"] ;
$valores[]=$_REQUEST["id_ficha"] ;
$recordset = $db->Execute($sql,$valores);
   
if (!$recordset) {
  $results["estado"] = "0";
  $results["msg"] =$db->ErrorMsg();
}  
else{
  $results["estado"] = "1";
} 

$db->disconnect();
echo json_encode($results);

?>