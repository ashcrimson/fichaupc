<?php

include_once '../libs/core.php';

if ($useDB) {
$medicamento = array();
	include("../funciones.global.inc.php");
	include("../funciones.inc.php");
	$db=genera_adodb();
	$valores=array();

         verifica_sesion(false);
       // $valores[]=$_SESSION["ficha"]["usuario"];         
         $sql= "select 
a.id_ficha,
a.run,
a.dv_run,
a.nombre,
a.apellido,
a.sn_iden,
to_char(a.fec_nac,'DD/MM/YYYY')  as fec_nac,
a.fec_ing_uci as fec_ing_uci_f,
to_char(a.fec_ing_uci,'DD/MM/YYYY')  as fec_ing_uci,a.cama,

decode(a.estado,'D','En Edicion','C','Cerrada','I','Ingresada por Interno') as estado,
b.tipo_ficha,a.subtipo_ficha,b.desc_tipo_ficha,a.estado as cod_estado,a.id_firma_doc,a.sn_epi
from ficha a,tipo_ficha b where a.tipo_ficha=b.tipo_ficha and a.subtipo_ficha=b.subtipo_ficha and ((a.estado not in ('C')) or((a.estado in ('C')) and (trunc( (sysdate - a.fec_egr_uci)*24 ) <=24)))";
if($_REQUEST["estado"] !=""){
	 $sql.=" and a.estado=?";
	 $valores[]=$_REQUEST["estado"];
}
if($_REQUEST["tipo_ficha"] !=""){
	 $tipo=explode("_",$_REQUEST["tipo_ficha"]);
	 if(($tipo[0] == "1")&&($tipo[1] == "1")){
	   $sql.=" and (((a.tipo_ficha=?) and (a.subtipo_ficha =?)) or ((a.tipo_ficha=1) and (a.subtipo_ficha =0))) ";
       $valores[]=$tipo[0];
	   $valores[]=$tipo[1];
	   
	 	  
	 }
	 elseif(($tipo[0] == "1")&&($tipo[1] == "2")){
	   $sql.=" and (((a.tipo_ficha=?) and (a.subtipo_ficha =?)) or ((a.tipo_ficha=2) and (a.subtipo_ficha =0))) ";
       $valores[]=$tipo[0];
	   $valores[]=$tipo[1];

	 }
	  elseif(($tipo[0] == "1")&&($tipo[1] == "3")){
	   $sql.=" and (((a.tipo_ficha=?) and (a.subtipo_ficha =?)) ) ";
       $valores[]=$tipo[0];
	   $valores[]=$tipo[1];

	 }
	   elseif(($tipo[0] == "1")&&($tipo[1] == "5")){
	   $sql.=" and (((a.tipo_ficha=?) and (a.subtipo_ficha =?)) ) ";
       $valores[]=$tipo[0];
	   $valores[]=$tipo[1];

	 }
  elseif(($tipo[0] == "1")&&($tipo[1] == "6")){
	   $sql.=" and (((a.tipo_ficha=?) and (a.subtipo_ficha =?)) ) ";
       $valores[]=$tipo[0];
	   $valores[]=$tipo[1];

	 }
	  elseif(($tipo[0] == "1")&&($tipo[1] == "4")){
	   $sql.=" and (((a.tipo_ficha=?) and (a.subtipo_ficha =?)) or ((a.tipo_ficha=3) and (a.subtipo_ficha =0))or ((a.tipo_ficha=3) and (a.subtipo_ficha =1))) ";
       $valores[]=$tipo[0];
	   $valores[]=$tipo[1];

	 }
	 
	  elseif(($tipo[0] == "3")&&($tipo[1] == "1")){
	   $sql.=" and (a.tipo_ficha=?) and (a.subtipo_ficha =?) ";
       $valores[]=$tipo[0];
	   $valores[]=$tipo[1];

	 }
}	 
/*
if($_REQUEST["tipo_ficha"] !=""){
	 $sql.=" and a.tipo_ficha=?";
	 $valores[]=$_REQUEST["tipo_ficha"];
}
if($_REQUEST["subtipo_ficha"] !=""){
	 $sql.=" and a.subtipo_ficha=?";
	 $valores[]=$_REQUEST["subtipo_ficha"];
}
*/

if($_REQUEST["busq"] !=""){
	 $sql.=" and ((to_char(a.run) like '%'||?||'%') or(upper(a.nombre) like '%'||?||'%') or(upper(a.apellido) like '%'||?||'%'))";
	 $valores[]=$_REQUEST["busq"];
	 $valores[]=strtoupper($_REQUEST["busq"]);
	 $valores[]=strtoupper($_REQUEST["busq"]);
	 
	 
}

if($_REQUEST["sidx"] == "run")
$sql.=" order by 2 ".$_REQUEST["sord"];
elseif($_REQUEST["sidx"] == "nombre")
$sql.=" order by 4 ".$_REQUEST["sord"];
elseif($_REQUEST["sidx"] == "fec_ing_uci")
$sql.=" order by 8 ".$_REQUEST["sord"];
elseif($_REQUEST["sidx"] == "apellido")
$sql.=" order by 5 ".$_REQUEST["sord"];
elseif($_REQUEST["sidx"] == "desc_tipo_ficha")
$sql.=" order by 13 ".$_REQUEST["sord"];
elseif($_REQUEST["sidx"] == "estado")
$sql.=" order by 11 ".$_REQUEST["sord"];
elseif($_REQUEST["sidx"] == "cama")
$sql.=" order by 10 ".$_REQUEST["sord"];
        $recordset = $db->Execute($sql,$valores);
  if (!$recordset) die("hhh".$db->ErrorMsg());  
  $ficha=array();
while ($arr = $recordset->FetchRow()) {
     $arr["tipo_usuario"]=$_SESSION["fichaupc"]["tipo_usuario"]; 
     $ficha[]=$arr;
  } 
	
       
        $db->disconnect();  
} else {
	
	$medicamento = array();
	
	$valores = array();
	$valores['medicamento'] = 'Sed ut perspiciatis unde omnis iste natus error';
	$valores['dosis'] = '200mg';
	$valores['cuandoEjecutar'] = 'ASAP';
	$valores['ejecutado'] = 'S??';
	
	$medicamento[] = $valores;
	
	$valores = array();
	$valores['medicamento'] = 'Lorem ipsum dolor sit amet, consectetur';
	$valores['dosis'] = '100mg';
	$valores['cuandoEjecutar'] = '10/03/2013 08:00';
	$valores['ejecutado'] = 'No';
		
	$medicamento[] = $valores;
	
}

$result = array();
$result["records"] = count($ficha);
$result["rows"] = $ficha;

echo json_encode($result);
?>
