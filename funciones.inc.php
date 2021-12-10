<?php
function retorna_cuenta_fichas($db,$run){
  $valores=array();
  $valores[]=$run;
  $sql="select 
count(*) as cuenta
from 
ficha a
where a.run=? and a.estado='D'";
  $recordset = $db->Execute($sql,$valores);
  if (!$recordset) die("ssshhh $run".$db->ErrorMsg());
  $res=0;
  while ($arr = $recordset->FetchRow()) {
    $res=$arr["cuenta"];
  }
  return $res;
}
function retorna_datos_portal($db,$us){
       $recordset = $db->Execute("select nombres,apellido_paterno ,apellido_materno from usuario where email=?",array($us));
  if (!$recordset) die("hhh".$db->ErrorMsg());  
  $nombre="";
while ($arr = $recordset->FetchRow()) {
     
     $nombre=implode(" ",$arr);
  }
 return $nombre;
  
}
function formatea_salida_LabExamen($result){
  
foreach($result as $k => $row){
  $salida = 'Sin Datos';
  if ($row['l_estado'] == 0 || $row['l_estado'] == 2) {
    $salida = 'Pendiente';
  } else if (($row['l_estado'] == 4 || $row['l_estado'] == 5) && ($row['tiporesult'] == 'C')) {
    //  $salida = $row['observacion'];
    $row['l_resultado'] = '';
  } else if ($row['l_estado'] == 4 || $row['l_estado'] == 5) {
    //    $salida = $row['l_resultado'];
    $row['observacion'] = '';
  } else if ($row['tiporesult'] == 'C') {
    //                $salida = $row['observacion'];
    $row['l_resultado'] = '';
  } else {
    // $salida = $row['l_resultado'];
    $row['observacion'] = '';
  }
  $row['l_resultado'] = trim($row['l_resultado']);
  $row['nota'] = nl2br(trim($row['nota']));
  $row['ra_texto'] = nl2br(htmlspecialchars(trim($row['ra_texto'])));
  $row['observacion'] = nl2br($row['observacion']);

  if ($row['examen'] == 'Observacion') {
    $row['l_resultado'] = trim($row['l_resultado']);
  }
  if (($row['e_label'] == null) && (trim($row['titulo_examen']) != trim($row['examen']))) {
    $row['e_label'] = 1;
  }
  if (($row['e_label'] == 1) && (trim($row['titulo_examen']) == trim($row['examen']))) {
    $row['e_label'] = 0;
  }
  $datos[$row['g_id']][$row['e_id']]['mostrar_titulo'] = $row['e_label'];
  $datos[$row['g_id']][$row['e_id']]['titulo'] = $row['titulo_examen'];
  $datos[$row['g_id']][$row['e_id']]['metodo'] = $row['e_metodo'];
  $datos[$row['g_id']][$row['e_id']]['data'][] = $row;
   
  $header['antecedente'][$row['g_id']] = array(
                                               'h_apellido1' => $row['h_apellido1'],
                                               'h_apellido2' => $row['h_apellido2'],
                                               'h_nombres' => $row['h_nombres'],
                                               'm_nombre' => $row['m_nombre'],
                                               'o_fecha_toma' => $row['o_fecha_toma'],
                                               'h_numero' => $row['h_numero'],
                                               'l_fecha_recepcion' => $row['l_fecha_recepcion'],
                                               'fecha_val' => $row['fecha_val'],
                                               's_nombre' => $row['s_nombre'],
                                               'o_edad' => $row['o_edad'],
                                               'o_numero' => $row['o_numero'],
                                               'usr_name' => $row['usr_name']);


}
$a=array('datos' => $datos, 'header' => $header);
return $a;
}


function retorna_ficha($db,$id_ficha){
$valores=array();
$valores[]=$id_ficha;
        
        $sql= "select 

ID_FICHA,
RUN,
DV_RUN,
NOMBRE,
APELLIDO,
SN_IDEN,
to_char(FEC_NAC,'DD/MM/YYYY')  as  FEC_NAC,
GRADO,
EDAD,
IND_SEXO,
to_char(FEC_ING_HOSP,'DD/MM/YYYY')  as  FEC_ING_HOSP,
to_char(FEC_ING_HOSP,'YYYYMMDD')  as  FEC_ING_HOSP_COMP,

HORA_INI_HOSP,
MINUTO_INI_HOSP,
to_char(FEC_ING_UCI,'DD/MM/YYYY')  as  FEC_ING_UCI,
HORA_INI_UCI,
MINUTO_INI_UCI,
ESTADO,
ID_FIRMA_DOC,
USUARIO,
FECHA_CREACION,
FECHA_MODIF,
TIPO_FICHA,
sofa,cama,
IND_DIAG_POS, 
	OBS_DIAG_POS, 
	to_char(FEC_EGR_UCI,'DD/MM/YYYY')  as  FEC_EGR_UCI,
HORA_TER_UCI ,
	MINUTO_TER_UCI, 
	RESUMEN ,
	TRATAMIENTO,
    
	USUARIO_EGR,inghosp,codinst,to_char(fecha_creacion,'dd/mm/yyyy hh24:mi:ss')  as  FEC_creacion,
    subtipo_ficha,sn_covid
from ficha  where id_ficha=?
 ";
        $recordset = $db->Execute($sql,$valores);
  if (!$recordset) die("hhh".$db->ErrorMsg());  
  $ficha=array();
while ($arr = $recordset->FetchRow()) {
     $arr["examen_fisico"]=retorna_examen_fisico($db,$id_ficha);
     $arr["antecedentes"]=retorna_antecedentes($db,$id_ficha);
     $arr["diag_pos"]=retorna_diagnosticos_pos($db,$id_ficha);
     $arr["referencias"]=retorna_interconsultas($db,$id_ficha,"FU");

     $arr["obs_diag_pos"]=gzinflate($arr["obs_diag_pos"]);
     $arr["resumen"]=gzinflate($arr["resumen"]);
     $arr["tratamiento"]=gzinflate($arr["tratamiento"]);
       $arr["datos"]=retorna_datos($db,$id_ficha);
     $ficha[]=$arr;
  }
 return $ficha;
}
function retorna_interconsultas($db,$id,$tipo){
  $sql="SELECT
A.NRO_INTERCONSULTA,
B.DESC_ESPEC,
TO_CHAR(A.FECHA_PROPUESTA,'DD/MM/YYYY')AS FECHA_PROPUESTA,
A.FUNDAMENTOS,
A.USUARIO
FROM REFERENCIA.INTERCONSULTA A,REFERENCIA.ESPECIALIDAD B
WHERE
A.COD_ESPEC=B.COD_ESPEC AND 
A.ID=? AND
A.TIPO_ID=? ORDER BY 1";
 $recordset = $db->Execute($sql,array($id,$tipo));
  if (!$recordset) die(" retorna_datos_egreso_clinico $id".$db->ErrorMsg());  
  $res=array();
  $db_p=genera_adodb("portal");
while ($arr = $recordset->FetchRow()) {
     $arr["medico"]=retorna_datos_portal($db_p,$arr["usuario"]);
     unset($arr["usuario"]);
     $res[]=$arr;
  }
  $db_p->disconnect(); 
return $res;
}
function retorna_plantillas($db,$tipo){
$valores=array();
 $valores[]=$_SESSION["fichaoncohemato"]["usuario"] ;
 $valores[]=$tipo;
$arr=array(); 
 $sql="
select * from plantilla b where usuario=? and tipo=? order by b.nombre
";

  $recordset = $db->Execute($sql,$valores);
  if (!$recordset) die("hhh".$db->ErrorMsg());  
 
while ($arr = $recordset->FetchRow()) {
     $datos[]=$arr;
  } 
return $datos;
}
function retorna_datos($db,$id_ficha){
  $sql="select a.*,c.descr from datos_ficha a,ficha b,datos c where a.id_ficha=? and a.id_ficha=b.id_ficha  and a.cod=c.cod order by 2";
 $recordset = $db->Execute($sql,array($id_ficha));
  if (!$recordset) die(" retorna_datos_egreso_clinico $id".$db->ErrorMsg());  
  $res=array();
while ($arr = $recordset->FetchRow()) {
     $arr["valor"]=gzinflate($arr["valor"]);
     $arr["obs"]=gzinflate($arr["obs"]);
     $res[]=$arr;
  }
return $res;
}
function retorna_anexos($db,$id_ficha){
  $sql="select * from anexo where id_ficha=?  order by sec";
 $recordset = $db->Execute($sql,array($id_ficha));
  if (!$recordset) die(" retorna_datos_egreso_clinico $id".$db->ErrorMsg());  
  $res=array();
while ($arr = $recordset->FetchRow()) {
     $res[]=$arr;
  }
return $res;
}


function retorna_examen_fisico($db,$id_ficha){
  $sql="select * from examen_fisico_ficha where id_ficha=?  order by 2";
 $recordset = $db->Execute($sql,array($id_ficha));
  if (!$recordset) die(" retorna_datos_egreso_clinico $id".$db->ErrorMsg());  
  $res=array();
while ($arr = $recordset->FetchRow()) {
     $res[]=$arr;
  }
return $res;
}
function retorna_antecedentes($db,$id_ficha){
  $sql="select * from antecedentes_ficha where id_ficha=?  order by 2";
 $recordset = $db->Execute($sql,array($id_ficha));
  if (!$recordset) die(" retorna_datos_egreso_clinico $id".$db->ErrorMsg());  
  $res=array();
while ($arr = $recordset->FetchRow()) {
     $arr["obs"]=gzinflate($arr["obs"]);
     $res[]=$arr;
  }
return $res;
}
function retorna_diagnosticos_pos($db,$id_ficha){
  $sql="select a.cod_diag as cod,a.desc_diag as descrip from diagnostico a,diagnostico_pos b where a.cod_diag=b.cod_diag and b.id_ficha=?  ";
 $recordset = $db->Execute($sql,array($id_ficha));
  if (!$recordset) die(" retorna_datos_egreso_clinico $id".$db->ErrorMsg());  
  $res=array();
while ($arr = $recordset->FetchRow()) {
     $res[]=$arr;
  }
return $res;
}


function retorna_tipo_ficha($db,$tipo_ficha,$subtipo_ficha){   
  
  $sql="select desc_tipo_ficha from tipo_ficha where tipo_ficha=? and subtipo_ficha=? ";
  
  $recordset = $db->Execute($sql,array($tipo_ficha,$subtipo_ficha));
  if (!$recordset) die(" retorna_datos_egreso_clinico $id".$db->ErrorMsg());  
  $res="";
while ($arr = $recordset->FetchRow()) {
  
     $res=$arr["desc_tipo_ficha"];
  }
return $res;
}
function getpdfEpicrisis($db,$id_ficha,$id_doc,$retorno){
  
  $ficha=retorna_ficha($db,$id_ficha);
$ano = date('Y');
//$hoja = 'A3';
$hoja = 'LETTER';
$tipo = 'portrait';
$tam_font = 8; 
$pdf = new Cezpdf($hoja,$tipo);
set_time_limit(3000);
$pdf ->ezSetMargins(30,50,80,30);
//$pdf ->ezSetMargins(0,0,841.89,1190.55);
$pdf ->selectFont('../libs/phppdf/fonts/Helvetica.afm');
$pdf->ezStartPageNumbers(350,20,10,'','<b>Rut Paciente:</b>'.$ficha[0]["run"]."-".$ficha[0]["dv_run"].'   '." <b>Fecha Egreso:</b>".$ficha[0]["fec_egr_uci"]." ".$ficha[0]["hora_ter_uci"].":".$ficha[0]["minuto_ter_uci"].' Pag. {PAGENUM} de {TOTALPAGENUM}',1);

$pdf->ezImage('../images/logo.jpg',3,120,90,'left');
//$pdf->line(puntos_cm(30),puntos_cm(45),puntos_cm(70),puntos_cm(3));
$pdf->ezSetDy(40);

$pdf->line(50,780,550,780);

$pdf->line(50,780,50,720);

$pdf->line(220,780,220,720);
$pdf->line(420,780,420,720);
$pdf->line(550,780,550,720);
$pdf->line(50,720,550,720);
//$pdf->ezText("<b> EPICRISIS MEDICA \n Nro : ".$id_epi."</b>",$tam_font,array('justification' => 'center','aleft'=>'50'));
$pdf->ezText("EPICRISIS MEDICA\n"."</b>",12,array('justification' => 'center','aleft'=>'50'));
$pdf->addText(450,752 ,12,"<b>Nro: ".$id_ficha."</b>");

//$pdf->line(puntos_cm(2),puntos_cm(25),puntos_cm(17),puntos_cm(25));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
//$pdf->line(puntos_cm(16),puntos_cm(25),puntos_cm(20),puntos_cm(25));
$pdf->line(50,695,550,695);

//$pdf->addJpegFromFile('../imagenes/linea1.jpg',50,550);

//$pdf->ezText("<b> Datos Personales</b>",10,array('justification' => 'center','aleft'=>'45'));
$pdf->addText(250,685 ,10,"<b> Datos Personales</b>");

$pdf->line(50,682,550,682);
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
/*$filas=array();
$filas_cabeza=array('colu1' => "",
			'colu2'  => "");
 $filas[] =  array('colu1' => "Rut : ".$ficha[0]["run"]."-".$ficha[0]["dv_run"],
                        'colu2' => "");
  
 $filas[] =  array('colu1' => "Nombre : ".$ficha[0]["nombre"],
                        'colu2' => "Apellidos : ".$ficha[0]["apellido"]);
 $filas[] =  array('colu1' => "Fecha Nacimiento : ".$ficha[0]["fec_nac"],
		   'colu2' => "Edad : ".$ficha[0]["edad"]." A".utf8_encode(chr(241)) ."os ".$ficha[0]["edad_m"]." Meses ".$ficha[0]["edad_d"]." Dias");
 $filas[] =  array('colu1' => "Direccion : ".$ficha[0]["direccion"],
                      'colu2' =>   "Telefono : ".$ficha[0]["telefono"]);
 $filas[] =  array('colu1' => "Sexo : ".$ficha[0]["ind_sexo"],
                      'colu2' =>   "");*/
 $pdf->line(50,600,550,600);
 $pdf->line(50,695,50,600);
 $pdf->line(550,695,550,600);
 
 		 $ver_dire = $ficha[0]["direccion"];
  		$calle = split("\n",$ver_dire);
  		$dir1 = $calle[0];
  		$dir2 = $calle[1];
  		$com = $calle[2];
  		$reg = $calle[3];
  		$direccion_completa = $dir1+" "+$dir2;
  		
$valor_nac = split("/",$ficha[0]["fec_nac"]);
$dia_nac = $valor_nac[0];
$mes_nac = $valor_nac[1];
$ano_nac = $valor_nac[2];
if($mes_nac == 1){
	$nom_mes_nac = 'Enero';
	}
	if($mes_nac == 2){
	$nom_mes_nac = 'Febrero';
	}if($mes_nac == 3){
	$nom_mes_nac = 'Marzo';
	}if($mes_nac == 4){
	$nom_mes_nac = 'Abril';
	}if($mes_nac == 5){
	$nom_mes_nac = 'Mayo';
	}if($mes_nac == 6){
	$nom_mes_nac = 'Junio';
	}if($mes_nac == 7){
	$nom_mes_nac = 'Julio';
	}if($mes_nac == 8){
	$nom_mes_nac = 'Agosto';
	}if($mes_nac == 9){
	$nom_mes_nac = 'Septiembre';
	}if($mes_nac == 10){
	$nom_mes_nac = 'Octubre';
	}if($mes_nac == 11){
	$nom_mes_nac = 'Noviembre';
	}if($mes_nac == 12){
	$nom_mes_nac = 'Diciembre';
	}
 
/* $pdf->line(50,583,550,583);  
 $pdf->line(50,583,50,550);
 $pdf->line(550,583,550,550);
 
 $pdf->line(50,568,550,568);  

/*                      
$pdf->ezTable($filas,
	       $filas_cabeza,null,
		      array('fontSize' => $tam_font,
			     'titleFontSize' => $tam_font,
			     'showLines' => '1',
                             'showHeadings' => '1',
			     'shaded' => '0',
                             'xPos' => '355', 
			    'shadeCol2' => array(1,1,0),
			    'cols' => array('colu1' => array('justification'=>'left' ,'width' =>300),
					    'colu2' => array('justification'=>'left', 'width' =>250)
					      )
			     )
			     );
	*/		   
$pdf->addText(65,670 ,10,"<b>Nombre</b>");
$pdf->addText(110,670 ,9,"<b>:</b> ".$ficha[0]["nombre"]." ".$ficha[0]["apellido"]);

$pdf->addText(375,670 ,10,"<b>Rut</b>");
$pdf->addText(430,670 ,9,"<b>:</b> ".$ficha[0]["run"]."-".$ficha[0]["dv_run"]);

if($ficha[0]["ind_sexo"]=='M'){
	
	$sexo = 'Masculino';
}else{
	$sexo='Femenino';
	}

$pdf->addText(65,655 ,10,"<b>Sexo</b>");
$pdf->addText(110,655 ,9,"<b>:</b> ".$sexo);

$pdf->addText(375,655 ,10,"<b>Nacimiento</b>");
$pdf->addText(430,655 ,9,"<b>:</b>".$dia_nac." de ".$nom_mes_nac." de ".$ano_nac);



$pdf->addText(375,640 ,10,"<b>Edad</b>");
$pdf->addText(430,640 ,9,"<b>:</b>".$ficha[0]["edad"]." A".utf8_encode(chr(241)) ."os ");

//$pdf->addText(385,625 ,10,"Region:");


$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
//$pdf->ezText("\n",$tam_font,array('justification'=>'left'));

$pdf->line(50,590,550,590);//linea arriba
$pdf->line(50,578,550,578);//linea medio
$pdf->line(50,590,50,485);//linea lateral izquierda
$pdf->line(550,590,550,485);//linea lateral derecha
$pdf->line(50,485,550,485);//linea abajo


$pdf->addText(250,580 ,10,"<b> Datos </b>");
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
/*$filas=array();
$filas_cabeza=array('colu1' => "",
			'colu2'  => "",
			'colu3'  => ""
);
 $filas[] =  array('colu1' => "Fecha Ingreso : ".$ficha[0]["fec_ing"] ." Hora Ingreso : ".$ficha[0]["hora_ing"].":".$ficha[0]["minuto_ing"],
		   'colu2' => "Motivo : ".$ficha[0]["motivo"],
                   'colu3' => ""
);
 $filas[] =  array('colu1' => "Fecha Egreso : ".$ficha[0]["fec_egr"] ." Hora Egreso : ".$ficha[0]["hora_egr"].":".$ficha[0]["minuto_egr"],
		   'colu2' => "Dias Hosp : ".$ficha[0]["dias"] ." dias ".$ficha[0]["horas"] ." horas " ,
                   'colu3' => ""
);
 $otro_destino="";
 if(trim($ficha[0]["otro_destino"]) !="")
   $otro_destino="\n".$ficha[0]["otro_destino"];
 $filas[] =  array('colu1' => "Servicio Procedencia : ".$servicios[$ficha[0]["cod_serv_orig"]],
                        'colu2' => "Servicio Egreso : ".$servicios[$ficha[0]["cod_serv_dest"]],
 'colu3' => "Destino Egreso: ".$destinos[$ficha[0]["cod_destino"]] .$otro_destino,
);

*/
	$valor = split("/",$ficha[0]["fec_ing_hosp"]);
$dia = $valor[0];
$mes = $valor[1];
$ano = $valor[2];
if($mes == 1){
	$nom_mes = 'Enero';
	}
	if($mes == 2){
	$nom_mes = 'Febrero';
	}if($mes == 3){
	$nom_mes = 'Marzo';
	}if($mes == 4){
	$nom_mes = 'Abril';
	}if($mes == 5){
	$nom_mes = 'Mayo';
	}if($mes == 6){
	$nom_mes = 'Junio';
	}if($mes == 7){
	$nom_mes = 'Julio';
	}if($mes == 8){
	$nom_mes = 'Agosto';
	}if($mes == 9){
	$nom_mes = 'Septiembre';
	}if($mes == 10){
	$nom_mes = 'Octubre';
	}if($mes == 11){
	$nom_mes = 'Noviembre';
	}if($mes == 12){
	$nom_mes = 'Diciembre';
	}
	$valor1 = split("/",$ficha[0]["fec_ing_uci"]);
$dia1 = $valor1[0];
$mes1 = $valor1[1];
$ano1 = $valor1[2];
if($mes1 == 1){
	$nom_mes1 = 'Enero';
	}
	if($mes1 == 2){
	$nom_mes1 = 'Febrero';
	}if($mes1 == 3){
	$nom_mes1 = 'Marzo';
	}if($mes1 == 4){
	$nom_mes1 = 'Abril';
	}if($mes1 == 5){
	$nom_mes1 = 'Mayo';
	}if($mes1 == 6){
	$nom_mes1 = 'Junio';
	}if($mes1 == 7){
	$nom_mes1 = 'Julio';
	}if($mes1 == 8){
	$nom_mes1 = 'Agosto';
	}if($mes1 == 9){
	$nom_mes1 = 'Septiembre';
	}if($mes1 == 10){
	$nom_mes1 = 'Octubre';
	}if($mes1 == 11){
	$nom_mes1 = 'Noviembre';
	}if($mes1 == 12){
	$nom_mes1 = 'Diciembre';
	}
	$valor2 = split("/",$ficha[0]["fec_egr_uci"]);
$dia2 = $valor2[0];
$mes2 = $valor2[1];
$ano2 = $valor2[2];
if($mes2 == 1){
	$nom_mes2 = 'Enero';
	}
	if($mes2 == 2){
	$nom_mes2 = 'Febrero';
	}if($mes2 == 3){
	$nom_mes2 = 'Marzo';
	}if($mes2 == 4){
	$nom_mes2 = 'Abril';
	}if($mes2 == 5){
	$nom_mes2 = 'Mayo';
	}if($mes2 == 6){
	$nom_mes2 = 'Junio';
	}if($mes2 == 7){
	$nom_mes2 = 'Julio';
	}if($mes2 == 8){
	$nom_mes2 = 'Agosto';
	}if($mes2 == 9){
	$nom_mes2 = 'Septiembre';
	}if($mes2 == 10){
	$nom_mes2 = 'Octubre';
	}if($mes2 == 11){
	$nom_mes2 = 'Noviembre';
	}if($mes2 == 12){
	$nom_mes2 = 'Diciembre';
	}
$pdf->addText(65,560 ,10,"<b>Fecha Ingreso Hospitalizacion</b>");
$pdf->addText(210,560 ,9,"<b>:</b> ".$dia." de ".$nom_mes." de ".$ano);
$pdf->addText(320,560 ,10,"<b>Hora Ingreso</b>");
$pdf->addText(420,560 ,9,"<b>:</b> ".$ficha[0]["hora_ini_hosp"].":".$ficha[0]["minuto_ini_hosp"]);
$pdf->addText(65,550 ,10,"<b>Fecha Ingreso UCI</b>");
$pdf->addText(210,550 ,9,"<b>:</b> ".$dia1." de ".$nom_mes1." de ".$ano1);
$pdf->addText(325,550 ,10,"<b>Hora Ingreso UCI</b>");
$pdf->addText(425,550 ,9,"<b>:</b> ".$ficha[0]["hora_ini_uci"].":".$ficha[0]["minuto_ini_uci"]);

$pdf->addText(65,540 ,10,"<b>Fecha Egreso UCI</b>");
$pdf->addText(210,540 ,9,"<b>:</b> ".$dia2." de ".$nom_mes2." de ".$ano2);
$pdf->addText(325,540 ,10,"<b>Hora Egreso UCI</b>");
$pdf->addText(425,540 ,9,"<b>:</b> ".$ficha[0]["hora_ter_uci"].":".$ficha[0]["minuto_ter_uci"]);



$pdf->ezText("\n",$tam_font,array('justification'=>'left'));


$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
/* 
$pdf->ezTable($filas,
	       $filas_cabeza,null,
		      array('fontSize' => $tam_font,
			     'titleFontSize' => $tam_font,
			     'showLines' => '1',
                             'showHeadings' => '1',
			     'shaded' => '0',
                             'xPos' => '370', 
			    'shadeCol2' => array(1,1,0),
			    'cols' => array('colu1' => array('justification'=>'left' ,'width' =>200),
					    'colu2' => array('justification'=>'left', 'width' =>200),
                                            'colu3' => array('justification'=>'left', 'width' =>200) 
					      )
			     )
			     );
	*/		     

$filas=array();
$filas_cabeza=array();
$filas_cabeza=array('colu1' => "                                                                       <b> Resumen</b>");

$filas[]=array('colu1' => $ficha[0]["resumen"]);
$pdf->ezTable($filas,$filas_cabeza,null,array('fontSize' => 10,
'titleFontSize' => 10,'showLines' => '1','showHeadings' => '1','shaded' => '0',
'xPos' => '305','shadeCol2' => array(1,1,0),
'cols' => array('colu1' => array('justification'=>'full' ,'width' =>500))));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));


if(count($ficha[0]["diag_pos"])>0){

/*
$pdf->line(50,464,550,464);//linea arriba
$pdf->line(50,464,50,448);//linea lateral izquierda
$pdf->line(550,464,550,448);//linea lateral derecha

$pdf->addText(250,453 ,10,"<b>Diagnosticos de Egreso</b>");
*/
$pdf->ezText("<b> Diagnósticos de Egreso</b>",10,array('justification' => 'left'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));


$filas_cabeza=array('cod' => "<b>Codigo</b>",
		    'descrip'  => "<b>Descripcion</b>");
$pdf->ezTable(
	       $ficha[0]["diag_pos"],$filas_cabeza,null,
		      array('fontSize' => $tam_font,
			     'titleFontSize' => $tam_font,
			     'showLines' => '2',
                             'showHeadings' => '1',
			     'shaded' => '0',
                             'xPos' => '305', 
			    'shadeCol2' => array(1,1,0),
			    'cols' => array('cod' => array('justification'=>'left' ,'width' =>50),
					    'descrip' => array('justification'=>'left', 'width' =>450) 
                                           )
                                         
					 
			     )
			    );
}


$pdf->ezText("\n",$tam_font,array('justification'=>'left'));

 if((trim($ficha[0]["obs_diag_pos"]) !="")&&(trim($ficha[0]["obs_diag_pos"]) !="false")){
//$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
//$pdf->ezText("<b>Otros Diagnosticos de Egreso2</b>",$tam_font,array('justification'=>'left'));
//$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
$filas=array();
$filas_cabeza=array('colu1' => "                                                              <b>Otros Diagnosticos de Egreso</b>");
$filas[]=array('colu1' => $ficha[0]["obs_diag_pos"]);
$pdf->ezTable($filas,$filas_cabeza,null,
array('fontSize' => 10,'titleFontSize' => 10,'showLines' => '1',
'showHeadings' => '1','shaded' => '0','xPos' => '305',
'shadeCol2' => array(1,1,0),'cols' => array('colu1' => array('justification'=>'full' ,'width' =>500))));
}
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));

//$pdf->ezText("<b> Resumen Hospitalizacion</b>",10,array('justification' => 'center'));
//$pdf->ezText("\n",$tam_font,array('justification'=>'left'));

$filas=array();
$filas_cabeza=array('colu1' => "                                                              <b>Tratamiento</b>");

$filas[]=array('colu1' => $ficha[0]["tratamiento"]);
$pdf->ezTable($filas,$filas_cabeza,null,array('fontSize' => 10,
'titleFontSize' => 10,'showLines' => '1','showHeadings' => '1','shaded' => '0',
'xPos' => '305','shadeCol2' => array(1,1,0),
'cols' => array('colu1' => array('justification'=>'full' ,'width' =>500))));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
if(count($ficha[0]["referencias"])>0){
  foreach($ficha[0]["referencias"] as $k =>$fila)
    unset($ficha[0]["referencias"][$k]["fecha_propuesta"]);
//Referencias
$pdf->ezText("\n<b> Indicaciones de Interconsultas</b>",10,array('justification' => 'left'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
$filas_cabeza=array('nro_interconsulta' => "<b>Nro</b>",
		    'desc_espec'  => "<b>Especialidad</b>",
		    /*'fecha_propuesta' => "<b>Fecha Prop.</b>",*/
			'medico'=>"<b>Medico</b>",
			'fundamentos'=>"<b>Fundamentos</b>");
$pdf->ezTable(
	       $ficha[0]["referencias"],$filas_cabeza,null,
		      array('fontSize' => $tam_font,
			     'titleFontSize' => $tam_font,
			     'showLines' => '2',
                             'showHeadings' => '1',
			     'shaded' => '0',
                             'xPos' => '340', 
			    'shadeCol2' => array(1,1,0),
			    'cols' => array(
					    'fundamentos' => array('justification'=>'left', 'width' =>250) 
                                           )
			     )
			    );

$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
//Referencias
}



$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
 $pdf->ezText("<b>______________________________</b>",10,array('justification' => 'center'));
 $pdf->ezText("<b>Paciente/Familiar</b>",10,array('justification' => 'center'));
$pdf->ezText("\n",$tam_font,array('justification'=>'left'));
 
 $pdf->ezText("\n",8,array('justification'=>'left'));
 $pdf->ezText("<b> ". $retorno["line1"] ."</b>",10,array('justification' => 'center'));
 $pdf->ezText("<b> ". $retorno["line2"] ."</b>",10,array('justification' => 'center'));
 $pdf->ezText("<b> Codigo de Verificacion : ". $id_doc ."</b>",10,array('justification' => 'center'));
 $pdf->ezText("<b>Usted puede verificar la veracidad de este documento en la siguiente direccion web : \n http://172.25.16.18/DocumentSigner/DocumentManagerServlet?method=download&docId=".$id_doc."</b>",10,array('justification'=>'center'));
 $pdf->ezText("\n",8,array('justification'=>'left'));
 if($ficha[0]["sn_covid"]=="S")
 $pdf->ezText("* Epicrisis es entregada sin firma de paciente y/o familiar, por motivos aislamiento. En paciente con compromiso de conciencia, se entregan indicaciones a familiar y/o cuidador vía teléfonica\n",8,array('justification'=>'left'));
 $doc=$pdf->ezOutput();
 return $doc;
}
?>
