<?php
  /*
   Funcion Script : Inicio del Sistema 
   Ultima Modificacion: 12/09/2011  
  */
include("../funciones.global.inc.php");
include("../funciones.inc.php");
$db=genera_adodb();
if($_REQUEST["traslado"] ==""){
  $results["estado"] = "0";
  $results["msg"] ="Debe seleccionar un traslado";
  $db->disconnect();
  echo json_encode($results);
  exit(0);
}
$valores=array();
   $sql="update ficha a set 
a.tipo_ficha=? , a.subtipo_ficha=?,a.cama=null
where a.ID_ficha=?";
$tp=explode("_",$_REQUEST["traslado"] );
$valores[]=$tp[0] ;
$valores[]=$tp[1] ;

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