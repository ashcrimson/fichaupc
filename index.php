<?php
  include("funciones.global.inc.php");
  include("funciones.inc.php");
  verifica_sesion(false);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Fichas UPC - Hospital Naval Almirante NEF</title>
<link rel="stylesheet" href="jquery-ui/jquery-ui.min.css" />

<script src="jquery/js/jquery-1.9.0.min.js"></script>
<script src="jquery-ui/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="jquery/css/ui.jqgrid.css" />
<script src="jquery/js/i18n/grid.locale-es.js" type="text/javascript"></script>
<script src="jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script src="jquery/js/jquery.form.js"></script>
<script src="slidereveal/dist/jquery.slidereveal.min.js"></script>
<script src="fichaupc.js?dummy=<?php echo(rand()); ?>"></script>
<script type="text/javascript" src="jquery/jquery.jqplot.js"></script>
<script type="text/javascript" src="jquery/plugins/jqplot.canvasTextRenderer.js"></script>
<script type="text/javascript" src="jquery/plugins/jqplot.canvasAxisLabelRenderer.js"></script>
<script type="text/javascript" src="jquery/plugins/jqplot.dateAxisRenderer.js"></script>
<script type="text/javascript" src="jquery/plugins/jqplot.canvasAxisTickRenderer.js"></script>
<script type="text/javascript" src="jquery/plugins/jqplot.categoryAxisRenderer.js"></script>
<script type="text/javascript" src="jquery/plugins/jqplot.highlighter.js"></script>
<script type="text/javascript" src="jquery/plugins/jqplot.ohlcRenderer.js"></script>
<script type="text/javascript" src="jquery/plugins/jqplot.pointLabels.js"></script>
<link rel="stylesheet" type="text/css" href="jquery/jquery.jqplot.css" />
<link rel="stylesheet" type="text/css" href="jquery.confirm/jquery.confirm/jquery.confirm.css" />
<script src="jquery.confirm/jquery.confirm/jquery.confirm.js"></script>
<style>
html, body {
	margin: 0;			/* Remove body margin/padding */
	padding: 0;
	overflow: hidden;	/* Remove scroll bars on browser window */	
    font-size: 85%;
}
</style>
</head>
<body>
    <div id="dialog-confirm" title="Cierre de Ficha" style="display: none">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>¿Este Cierre de Ficha Considera Epicrisis?</p>
</div>
<div id="login" style="display: none">
<form id="form_login" method="post" action="login.php">
                <fieldset>
            	<table>
                <tr >
                <td>Email</td>
                <td><input id="email" name="email" value="" type="text" ></td>
                </tr>

                
                <tr >
                <td>Password</td>
                <td><input id="clave" name="clave" value="" type="password" ></td>
                </tr>
                
                 <tr><td colspan="2"><center><button id="button-login" class="boton" type="submit" >INGRESAR</button></center></td></tr> 
                
                </table>
                </fieldset>
          </form>

</div>

<h2 style="font-family: Tahoma, Ubuntu;">Fichas UPC - Hospital Naval Almirante NEF</h2>
<?php if ($_SESSION["fichaupc"]["usuario"] !=""){ ?>
<input type="hidden" id="conectado" value="S">
<div id="cuadrologin" style="font-family: Tahoma, Ubuntu;font-size:12px;"><b ><?php echo $_SESSION["fichaupc"]["nombre_usuario"]; ?></b>
&nbsp;&nbsp;&nbsp;</div><br><br>

<center>
    <?php
  /*
  <div id='slider' style="box-shadow: -10px 0 15px 10px #585858;background-color: #E6E6E6;
	padding: 10px;"></div>
*/?>

<div id="content-box"><center> <b> Busqueda : </b><input type="text" name="busq" id="busq" size="20" maxlength="50" onkeyup="if(this.value.length >= 4){$('#list-fichas').setGridParam({url:'Servicios/getFichas.php?tipo_ficha='+$('#tf').val()+'&busq='+$('#busq').val()});$('#list-fichas').trigger( 'reloadGrid' );}">
<b>Tipo Ficha</b> <select id="tf" onchange="$('#list-fichas').setGridParam({url:'Servicios/getFichas.php?tipo_ficha='+$('#tf').val()+'&busq='+$('#busq').val()});$('#list-fichas').trigger( 'reloadGrid' );/*carga_slider();*/"><option value="">TODAS</option>
  <?php
			   $db=genera_adodb();
			   $recordset = $db->Execute("select * from tipo_ficha where sn_hab ='S' order by 3");
               if (!$recordset) die("hhh".$db->ErrorMsg());  
               while ($arr = $recordset->FetchRow()) {
				print("<option value='".$arr["tipo_ficha"]."_".$arr["subtipo_ficha"]."'>".$arr["desc_tipo_ficha"]."</option> ");
			   } 	
			   ?></select>
<button id="btn-limpiar" onclick="$('#tf').val('');$('#busq').val('');$('#list-fichas').setGridParam({url:'Servicios/getFichas.php'});$('#list-fichas').trigger( 'reloadGrid' );">Limpiar Filtros</button>

</center>
			<table id="list-fichas"><tr><td/></tr></table> 
			<div class="actions">
			  <?php
			  
			   if($_SESSION["fichaupc"]["tipo_usuario"] ==1){
			   $recordset = $db->Execute("select * from tipo_ficha where sn_hab ='S' order by 3");
               if (!$recordset) die("hhh".$db->ErrorMsg());  
               while ($arr = $recordset->FetchRow()) {
				print("<button id='btnAddFicha".$arr["tipo_ficha"]."_".$arr["subtipo_ficha"]."' class='btnpencil' onclick='ver_ficha(\"\",\"\",\"".$arr["tipo_ficha"]."\",\"".$arr["subtipo_ficha"]."\",\"D\",\"\");'>Crear Ficha  (".$arr["desc_tipo_ficha"].")</button>&nbsp;&nbsp;");
			   }
			   print("<button id=\"btnEditFicha\">Editar Ficha </button>&nbsp;&nbsp;");
			   print("<button id=\"btnEditCama\">Editar Cama </button>&nbsp;&nbsp;");
			   print("<button id=\"btnEditTraslado\">Traslado UPC </button>&nbsp;&nbsp;");
			  print("<button id=\"firmardoc\">Cerrar Ficha</button>&nbsp;&nbsp;");
				print("<button id=\"verdoc\">Ver Ficha</button>&nbsp;&nbsp;");
				
				print("<button id=\"printdoc\">Ver Epicrisis</button>&nbsp;&nbsp;");
				print("<button id=\"printrec\">Ver Receta</button>&nbsp;&nbsp;");
				  print("<button id=\"printhoja\">Ver Hoja de Traslado</button>&nbsp;&nbsp;");
				print("<button id=\"ref-plantillas\" class=\"boton\" onclick=\"mostrar_plantillas({modal:true,error: function() { alert('Could not load form') }});\">Mis Plantillas</button>");
                print("<button id=\"btnEditCierreFicha\">Reabrir Ficha</button>&nbsp;&nbsp;");
			   }
			   elseif($_SESSION["fichaupc"]["tipo_usuario"] ==2){
				print("<button id=\"verdoc\">Ver Ficha</button>&nbsp;&nbsp;");
				print("<button id=\"printdoc\">Ver Epicrisis</button>&nbsp;&nbsp;");
				
				print("<button id=\"printhoja\">Ver Hoja de Traslado</button>&nbsp;&nbsp;");

			   }
			   elseif($_SESSION["fichaupc"]["tipo_usuario"] ==3){
				
				
				$recordset = $db->Execute("select * from tipo_ficha where sn_hab ='S'  order by 3");
               if (!$recordset) die("hhh".$db->ErrorMsg());  
               while ($arr = $recordset->FetchRow()) {
				print("<button id='btnAddFicha".$arr["tipo_ficha"]."_".$arr["subtipo_ficha"]."' class='btnpencil' onclick='ver_ficha(\"\",\"\",\"".$arr["tipo_ficha"]."\",\"".$arr["subtipo_ficha"]."\",\"D\",\"\");'>Crear Ficha  (".$arr["desc_tipo_ficha"].")</button>&nbsp;&nbsp;");
			   }
			   print("<button id=\"btnEditFicha\">Editar Ficha </button>&nbsp;&nbsp;");
			   print("<button id=\"btnEditTraslado\">Traslado UPC </button>&nbsp;&nbsp;");
			  	print("<button id=\"verdoc\">Ver Ficha</button>&nbsp;&nbsp;");
				print("<button id=\"printdoc\">Ver Epicrisis</button>&nbsp;&nbsp;");
				print("<button id=\"printrec\">Ver Receta</button>&nbsp;&nbsp;");
				  print("<button id=\"printhoja\">Ver Hoja de Traslado</button>&nbsp;&nbsp;");
				//print("<button id=\"btnEditCierreFicha\">Reabrir Ficha</button>&nbsp;&nbsp;");
			   }
			   if(($_SESSION["portal"]["sn_enfermera_epidemiologia"] =="S")&&($_SESSION["fichaupc"]["tipo_usuario"] ==2))
			      print("<button id='btnAddEvoEpi'>Agregar Evolucion Epididemiologica</button>&nbsp;&nbsp;");
			  if(($_SESSION["portal"]["sn_subdirector_clinico"] =="S")&&($_SESSION["fichaupc"]["tipo_usuario"] ==1))
			      print("<button id='btnCierreAdm'>Cierre Administrativo</button>&nbsp;&nbsp;");
		         $db->disconnect();						
			  ?>					
			<br><input type="hidden" id="us_aux" name="us_aux" value="<?php echo $_SESSION["fichaupc"]["usuario"];?>">
		      <button type="button" id="btnRE" >Impimir Receta(s) Electronicas</button>		
			</div>
			
		</div>
</center>
<?php } ?>
<form name="verhoja" method="post" action="Servicios/getHoja.php" target="_blank">
<input type="hidden" name="id_ficha" value="">

</form>
<form name="verreceta" method="post" action="Servicios/getReceta.php" target="_blank">
<input type="hidden" name="id_ficha" value="">

</form>
</body>
</html>
