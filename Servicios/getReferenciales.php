<?php
require_once('../libs/nusoap/lib/nusoap.php');
  include("../funciones.global.inc.php");
  include("../funciones.inc.php");
  $db=genera_adodb();
  verifica_sesion(false);
$client = new soapclient('http://172.25.16.18/bus/webservice/ws.php?wsdl');
$result = $client->call('buscarDetallePersona', array('run' =>$_REQUEST["run"]));
if((count($result) > 0)&&(isset($result["run"]))){
$result["resbusq"]="Paciente encontrado en referencial";
$lab = json_decode($client->call('lista_examenes', array('run' =>$result["run"]."-".$result["dv_run"])),true);
$result["hosp"] = $client->call('GetPacHospRunFicha', array('run' =>$_REQUEST["run"]));
if((count($result["hosp"]) >0) &&(count($lab)>0)){
 $fecha_comp=substr($result["hosp"][0]["ingreso"],0,8);
 
 foreach($lab as $ii => $ex){
    $fecha_ex=substr($ex["fecha_toma"],6,4).substr($ex["fecha_toma"],3,2).substr($ex["fecha_toma"],0,2);
    if($fecha_ex >= $fecha_comp)
    $result["lab"][]=$ex;  
 }
}
else{
  $result["lab"]=$lab;  
}
$result["estado"]="1";
$result["edad"]="";
if($result["fecha_nac"] != ""){
$dias = mktime(0,0,0,substr($result["fecha_nac"],5,2),substr($result["fecha_nac"],8,2),substr($result["fecha_nac"],0,4));
$result["edad"] = (int)((time()-$dias)/31556926 );
$result["fecha_nac"]=substr($result["fecha_nac"],8,2)."/".substr($result["fecha_nac"],5,2)."/".substr($result["fecha_nac"],0,4);

}
$result["fichas_vig"]=retorna_cuenta_fichas($db,$_REQUEST["run"]);
 
}
else{
$result["resbusq"]="Paciente no encontrado en referencial";
$result["estado"]="0";
}

  $db->disconnect();
echo json_encode($result);

?>
