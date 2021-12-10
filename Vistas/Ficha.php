<?php
  include("../funciones.global.inc.php");
  include("../funciones.inc.php");
  $db=genera_adodb();
  verifica_sesion(false);

?>
<script src="jquery/js/jquery.form.js"></script>
<script src="tinymce/js/tinymce/tinymce.min.js"></script>

 <style>
.ui-autocomplete-loading {
background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat;

}
.enmdatos input{
  color: #fff;
  background: #B40404;
}
.enmdatos textarea{
  color: #fff;
  background: #B40404;
}
.enmdatos radio{
  color: #fff;
  background: #B40404;
}
.enmdatos select{
  color: #fff;
  background: #B40404;
}
.txt{
  width:200px;
    font-family: Arial;
   white-space: -moz-pre-wrap; /* Mozilla, supported since 1999 */
    white-space: -pre-wrap; /* Opera */
    white-space: -o-pre-wrap; /* Opera */
    white-space: pre-wrap; /* CSS3 - Text module (Candidate Recommendation) http://www.w3.org/TR/css3-text/#white-space */
    word-wrap: break-word;
}
pre {
   //display: inline-block;
   width:800px;
    font-family: Arial;
   white-space: -moz-pre-wrap; /* Mozilla, supported since 1999 */
    white-space: -pre-wrap; /* Opera */
    white-space: -o-pre-wrap; /* Opera */
    white-space: pre-wrap; /* CSS3 - Text module (Candidate Recommendation) http://www.w3.org/TR/css3-text/#white-space */
    word-wrap: break-word;
}
del{
  color:           hsl(0, 90%, 65%);
font-size:       15px;
text-decoration: line-through;
/*noinspection CssOverwrittenProperties*/
text-decoration: hsl(0, 90%, 65%) double line-through;
  
}
</style>
<script>
  if(document.myForm_admu.tipo_ficha.value ==2){
 
    $('#d_sofa').hide();
    
  }

tinymce.init({
  selector: '.editor',
  height: 300,
  menubar: false,
  plugins: [
    'advlist autolink lists link image charmap print preview anchor',
    'searchreplace visualblocks code fullscreen',
    'insertdatetime media table contextmenu paste code'
  ],
  toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ',
 
});
  $(".validar").button({icons: {primary: "ui-icon-pencil"}}); 
function valida_evolucion(id_ficha,sec_evo) {
  tinyMCE.triggerSave();
  var evolucion=document.getElementById("evolucion_"+id_ficha+"_"+sec_evo).value;
  	
  $.ajax({
  type: "POST",
  url: "Servicios/setFicha.php",
  data: {id_ficha:id_ficha,sec_evo:sec_evo,trans:'valevo',evolucion:evolucion},
  success: function( data ) {
  if (data.estado == 5) {
    alert("Evolucion validada");
	ver_ficha('<?php echo $_REQUEST["id_ficha"]; ?>',"",'<?php echo $_REQUEST["tipo_ficha"]; ?>','<?php echo $_REQUEST["subtipo_ficha"]; ?>','D','');
  }
  else{
	alert(data.error);
  }
  },
  dataType: "json"
});
}
function muestra_examen(run,orden) {
     $("#vw_lab").remove();
  var tag = $("<div id='vw_lab'></div>"); //This tag will the hold the dialog content.
  $.ajax({
    url: 'Vistas/Resultados_Examenes.php?run='+run+'&nro_orden='+orden,
    type: 'GET',
    async:false,
    success: function(data, textStatus, jqXHR) {
      if(typeof data == "object" && data.html) { //response is assumed to be JSON
        tag.html(data.html).dialog({modal: true, title: 'Resultados Examen',height:500,width:1000,resizable:false,close: function(event, ui) {  $("#vw_lab").remove(); }}).dialog('open');
      } else { //response is assumed to be HTML
        tag.html(data).dialog({modal: true, title: 'Resultados Examen',height:500,width:1000,resizable:false,close: function(event, ui) {  $("#vw_lab").remove(); }}).dialog('open');
      }
     
    }
  });
}
function muestra_tendencia(run,codigo,p_id) {
     $("#vw_gralab").remove();
  var tag = $("<div id='vw_gralab'></div>"); //This tag will the hold the dialog content.
  $.ajax({
    url: 'Vistas/Grafico_Examenes.php?run='+run+'&codigo='+codigo+'&p_id='+p_id,
    type: 'GET',
    async:false,
    success: function(data, textStatus, jqXHR) {
      if(typeof data == "object" && data.html) { //response is assumed to be JSON
        tag.html(data.html).dialog({modal: true, title: 'Tendencia Examen',height:500,width:'100%',resizable:false,close: function(event, ui) {  $("#vw_gralab").remove(); }}).dialog('open');
      } else { //response is assumed to be HTML
        tag.html(data).dialog({modal: true, title: 'Tendencia Examen',height:500,width:'100%',resizable:false,close: function(event, ui) {  $("#vw_gralab").remove(); }}).dialog('open');
      }
     Muestra_Grafico();
    }
  });
}
 $(function() {

jQuery("#list-diag_pos").jqGrid({
        datatype: 'clientSide',
        multiselect: true,
        width:700,height:70,
        rownumbers: true, 
        colNames:['Codigo','Descripcion'],
        colModel :[ 
        {name:'cod',index:'cod', width:55}, 
        {name:'descr',index:'descr', width:300}],
        rowNum:10,
        viewrecords: true,
        caption: 'Diagnosticos Postoperatorios'
   }); 
$("#btnDelDiagPos").button({icons: {primary: "ui-icon-del"}}); 
$("#btnDelDiagPos").click(function() {Eliminar('list-diag_pos')});

function Eliminar(id){ 
              var rowids = jQuery('#'+id).jqGrid('getGridParam', 'selarrrow');
              for (var i = rowids.length - 1; i >= 0; i--) {
                jQuery('#'+id).jqGrid('delRowData', rowids[i]);
              }
           }
function existe(id, cod ) {
var rowids = jQuery('#'+id).jqGrid('getRowData');
var encontrado =0;
              for (var i = 0; i < rowids.length; i++) {
                if(rowids[i].cod == cod)
                  encontrado =1;
              }
   return encontrado;
}

function log(id, message ) {
var data=message.split(" - ");
if (!existe(id,data[0]))
jQuery("#"+id).addRowData("1", {cod:data[0], descr:data[1]});
else
alert("El registro ya existe");
}
$( "#diag_pos_bus" ).autocomplete({
source: "Servicios/getDiagnosticos.php",
minLength: 3,
select: function( event, ui ) {
log("list-diag_pos", ui.item ?
 ui.item.value  :
"Nada seleccionado, valor buscado " + this.value );
$(this).val(''); return false;
}
});


});

        $( "#busaeu" ).button({icons: {primary: "ui-icon-search"}});
          $( "#guardaraeu" ).button({icons: {primary: "ui-icon-disk"}});
</script>
<script type="text/javascript">
function recorre(id, fld ) {
var datos="";
var rowids = jQuery('#'+id).jqGrid('getRowData');
              for (var i = 0; i < rowids.length; i++) {
                if (i==0) 
                datos=datos + rowids[i].cod
                else 
                datos=datos + "," +rowids[i].cod
              }
   fld.value =datos;
}

function habilita_diag(ind,o,o_obs,id){

  

  if(ind == 1){
    o.disabled=false;
    o.value="";
    o_obs.disabled=true;
    o_obs.value="";
  }
  else{
    var cant=$('#'+id).getGridParam('records');
    for (var i =1; i <=cant; i++) {
     
     jQuery('#'+id).jqGrid('delRowData', 1);
    }
    o.disabled=true;
    o.value="";
    o_obs.disabled=false;
    o_obs.value="";
  }
   
}
function copia_diag(ind){
  if(ind.checked){
    document.myForm_admu.diag_pos_bus.readOnly=true;
    document.myForm_admu.obs_diag_pos.readOnly=true;
    document.myForm_admu.ind_diag_pos.disabled=true;
    document.myForm_admu.obs_diag_pos.value=document.myForm_admu.obs_diag_pre.value;
    document.myForm_admu.ind_diag_pos.value=document.myForm_admu.ind_diag_pre.value;
    jQuery('#btnDelDiagPos').button('disable');
    jQuery('#list-diag_pos').jqGrid('clearGridData');
    var RowList;
    RowList = $('#list-diag_pre').getRowData();
    var Row;
    for( Row in RowList )
      {
	$('#list-diag_pos').addRowData(Row,RowList[Row]);
      }
  }
  else{
    document.myForm_admu.ind_diag_pos.readOnly=false;
    jQuery('#list-diag_pos').jqGrid('clearGridData');
    document.myForm_admu.diag_pos_bus.readOnly=false;
    document.myForm_admu.obs_diag_pos.readOnly=false;
    document.myForm_admu.obs_diag_pos.value="";
    document.myForm_admu.ind_diag_pos.value=1;
    document.myForm_admu.ind_diag_pos.disabled=false;
    jQuery('#btnDelDiagPos').button('enable');
  }
   
}
function habilita_cuadro(ind,o){
  if(ind == 1){
    o.disabled=false;
    o.value="";
  }
  else{
    o.disabled=true;
    o.value="";
  }
   
}
function valida_ficha(f){
  
      if(f.run.value ==""){
    alert('El rut no puede ser nulo'); 
    return false;
   }
   if(!isInteger(f.run.value)){
    alert('El rut debe ser un numero'); 
    return false;
   }
   if(f.dv_run.value ==""){
    alert('El digito verificador del rut no puede ser nulo'); 
    return false;
   } 
   if ( getRutDv(f.run.value) != f.dv_run.value){
      alert('El rut es incorrecto'); 
      return false;
   }
   if(f.nombre.value ==""){
     alert('El nombre no puede ser nulo'); 
      return false;
   }
   if((!f.ind_sexo[0].checked)&&(!f.ind_sexo[1].checked)){
     alert('Debe seleccionar el sexo'); 
      return false;
   }

   if(f.fec_nac.value ==""){
     alert('La fecha de nacimiento no puede ser nula'); 
      return false;
   }

   if(f.fec_ing_hosp.value ==""){
     alert('La fecha de Hospitalizacion no puede ser nula'); 
      return false;
   }
   
   var fec1=f.fec_ing_hosp.value.substr(6, 4)+f.fec_ing_hosp.value.substr(3, 2)+f.fec_ing_hosp.value.substr(0, 2)+f.hora_ini_hosp.value+f.minuto_ini_hosp.value;
   if(f.fec_ing_uci.value ==""){
     alert('La fecha de Ingreso UCI no puede ser nula'); 
      return false;
   }

   var fec2=f.fec_ing_uci.value.substr(6, 4)+f.fec_ing_uci.value.substr(3, 2)+f.fec_ing_uci.value.substr(0, 2)+f.hora_ini_uci.value+f.minuto_ini_uci.value;
   if (fec2 < fec1) {
      alert('El Ingreso UCI no puede ser menor a la Hospitalizacion del Paciente'); 
      return false;
   }
   
  /* if ((f.evolucion != null)&&(f.evolucion.value.trim() =="")&&(f.adjunto != null)&&(f.adjunto.value.trim() !="")) {
      alert('No puede subir un adjunto a una evolucion sin haber registrado algo en dicha evolucion'); 
      return false;
   }
   */
   /*  
   recorre("list-diag_pre",f.diag_pre);
   recorre("list-diag_pos",f.diag_pos);
   if((f.ind_diag_pre.value==1)&&(f.diag_pre.value =="")){
    alert('Debe ingresar al menos un diagnostico presuntivo de ingreso'); 
    return false;
   }
   if((f.ind_diag_pre.value==2)&&(f.obs_diag_pre.value =="")){
    alert('Debe ingresar las observaciones de diagnostico ingreso'); 
    return false;
   } 
   */

   return true;
}

$.getJSON("Servicios/getFicha.php?id_ficha=<?php echo $_REQUEST['id_ficha']?>",
{
format: "json"
},
function(json) {
     
     $("#myForm_admu").reset(); 
     if(json[0]!= null){
      j=0;
        if (json[0].datos.length > 0)
     for(j=0;j< json[0].datos.length;j++){
        if (json[0].estado =='I') {
            $("#datos_"+json[0].datos[j].cod).val(json[0].datos[j].valor);
			$("#obs_datos_"+json[0].datos[j].cod).val(json[0].datos[j].obs);
			$("input[name='datos_"+json[0].datos[j].cod+"'][value='"+json[0].datos[j].valor+"']").prop('checked', true);
		}
		else{
		 $("#datos_"+json[0].datos[j].cod).html("<pre>"+json[0].datos[j].valor+"</pre>");
		 $("#obs_datos_"+json[0].datos[j].cod).html("<pre>"+json[0].datos[j].obs+"</pre>");
		 
         $("#enmdatos_"+json[0].datos[j].cod).val(json[0].datos[j].valor);
		 $("#obs_enmdatos_"+json[0].datos[j].cod).val(json[0].datos[j].obs);
			$("input[name='enmdatos_"+json[0].datos[j].cod+"'][value='"+json[0].datos[j].valor+"']").prop('checked', true);

		}
     }
     j=0;
      
  
      if (json[0].examen_fisico.length > 0)
     for(j=0;j< json[0].examen_fisico.length;j++){
      	if (json[0].estado =='I') {
		  $("#exa_"+json[0].examen_fisico[j].cod).val(json[0].examen_fisico[j].valor);
          $("#exa_"+json[0].examen_fisico[j].cod).prop('disabled', false);
		
		}
		else{
        $("#exa_"+json[0].examen_fisico[j].cod).html(json[0].examen_fisico[j].valor);
        $("#enmexa_"+json[0].examen_fisico[j].cod).val(json[0].examen_fisico[j].valor);
        $("#enmexa_"+json[0].examen_fisico[j].cod).prop('disabled', false);
		}
        
      
     }
j=0;
      
     if (json[0].antecedentes.length > 0)
     for(j=0;j< json[0].antecedentes.length;j++){
        if(json[0].antecedentes[j].valor =="S"){
         if (json[0].estado =='I') {
		  $("input[name=ant_"+json[0].antecedentes[j].cod+"][value=S]").prop('checked', true);
          $("#obs_ant_"+json[0].antecedentes[j].cod).val(json[0].antecedentes[j].obs);
          $("#obs_ant_"+json[0].antecedentes[j].cod).prop('disabled', false);
		 }
		 else{
          $("#ant_"+json[0].antecedentes[j].cod).html('SI');
          $("#obs_ant_"+json[0].antecedentes[j].cod).html(json[0].antecedentes[j].obs);
          $("input[name=enmant_"+json[0].antecedentes[j].cod+"][value=S]").prop('checked', true);
          $("#obs_enmant_"+json[0].antecedentes[j].cod).val(json[0].antecedentes[j].obs);
          $("#obs_enmant_"+json[0].antecedentes[j].cod).prop('disabled', false);
		 }
        }
        else if(json[0].antecedentes[j].valor =="N"){
         if (json[0].estado =='I') {
		  $("input[name=ant_"+json[0].antecedentes[j].cod+"][value=N]").prop('checked', true);
          $("#obs_ant_"+json[0].antecedentes[j].cod).val(json[0].antecedentes[j].obs);
          $("#obs_ant_"+json[0].antecedentes[j].cod).prop('disabled', false);
		 }
		 else{
		  $("#ant_"+json[0].antecedentes[j].cod).html('NO');
          $("#obs_ant_"+json[0].antecedentes[j].cod).html(json[0].antecedentes[j].obs);
          $("input[name=enmant_"+json[0].antecedentes[j].cod+"][value=N]").prop('checked', true);
          $("#obs_enmant_"+json[0].antecedentes[j].cod).val(json[0].antecedentes[j].obs);
          $("#obs_enmant_"+json[0].antecedentes[j].cod).prop('disabled', false);
		 }
        }
        
     }
     document.myForm_admu.id_ficha.value=json[0].id_ficha;
     document.myForm_admu.nombre.value=json[0].nombre;
     document.myForm_admu.apellido.value=json[0].apellido;
     document.myForm_admu.fec_nac.value=json[0].fec_nac;
     document.myForm_admu.edad.value=json[0].edad;
     document.myForm_admu.run.value=json[0].run;
     document.myForm_admu.dv_run.value=json[0].dv_run;
     document.myForm_admu.grado.value=json[0].grado;
     document.myForm_admu.sn_iden.value=json[0].sn_iden;
     if (json[0].ind_sexo =='F')
     document.myForm_admu.ind_sexo[0].checked=true;
     else if (json[0].ind_sexo =='M')
     document.myForm_admu.ind_sexo[1].checked=true;
     document.myForm_admu.fec_ing_hosp.value=json[0].fec_ing_hosp;
     document.myForm_admu.hora_ini_hosp.value=json[0].hora_ini_hosp;
     document.myForm_admu.minuto_ini_hosp.value=json[0].minuto_ini_hosp;
     document.myForm_admu.fec_ing_uci.value=json[0].fec_ing_uci;
     document.myForm_admu.hora_ini_uci.value=json[0].hora_ini_uci;
     document.myForm_admu.minuto_ini_uci.value=json[0].minuto_ini_uci;
     document.myForm_admu.sofa.value=json[0].sofa;
     document.myForm_admu.cama.value=json[0].cama;
	 document.myForm_admu.inghosp.value=json[0].inghosp;
     document.myForm_admu.codinst.value=json[0].codinst;
     
     document.myForm_admu.tipo_ficha.value=json[0].tipo_ficha;
	 document.myForm_admu.subtipo_ficha.value=json[0].subtipo_ficha;
	 
     document.myForm_admu.usuario.value=json[0].usuario;
       j=0;
      html="<ul>";
      
     if (json[0].lab != null)
     for(j=0;j< json[0].lab.length;j++){
        html=html+"<li><a href='#' onclick='muestra_examen(\""+json[0].run+"-"+json[0].dv_run+"\",\""+json[0].lab[j].o_numero+"\")'>"+json[0].lab[j].o_numero+ ' ('+json[0].lab[j].fecha_toma+')</a></li>';
     }
     html=html+"</ul>";
     $('#lab').html(html);
    
     
        
   if (json[0].sn_iden == 'S'){
     document.myForm_busu.run.value=json[0].run;
     document.myForm_busu.dv_run.value=json[0].dv_run;
     $("#myForm_busu").find('input','button').attr('disabled',true);
     $("#myForm_busu").find('select').attr('disabled',true);
     $('#busaeu').button("disable");
     
     document.myForm_admu.nombre.readOnly=true;
     document.myForm_admu.apellido.readOnly=true;
     document.myForm_admu.edad.readOnly=true; 
     document.myForm_admu.ind_sexo[0].readOnly=true;
     document.myForm_admu.ind_sexo[1].readOnly=true;
     document.myForm_admu.fec_nac.readOnly=true;
     
   }
   else{
     $("#myForm_busu").find('input','button').attr('disabled',false);
     $("#myForm_admu").find('input','button').attr('disabled',false);
     $("#myForm_admu").find('select').attr('disabled',true);
     $('#busaeu').button("enable");
     document.myForm_admu.nombre.readOnly=false;
     document.myForm_admu.ind_sexo[0].readOnly=false;
     document.myForm_admu.ind_sexo[1].readOnly=false;
     document.myForm_admu.fec_nac.readOnly=false;
     document.myForm_admu.apellido.readOnly=false;
     document.myForm_admu.edad.readOnly=false; 
     
     $('#guardaraeu').attr('disabled',false);
   }
 //  if (document.myForm_admu.usuario.value !='<?php echo $_SESSION["fichaupc"]["usuario"]; ?>') {
    document.myForm_admu.id_ficha.readOnly=true;
     document.myForm_admu.nombre.readOnly=true;
     document.myForm_admu.apellido.readOnly=true;
     document.myForm_admu.fec_nac.disabled=true;
     document.myForm_admu.edad.readOnly=true;
     document.myForm_admu.run.readOnly=true;
     document.myForm_admu.dv_run.readOnly=true;
     document.myForm_admu.grado.readOnly=true;
     document.myForm_admu.sn_iden.readOnly=true;
     $('.ant').prop('disabled', false);
     $('.exa').prop('disabled', false);
     
         document.myForm_admu.ind_sexo.disabled=true;
      document.myForm_admu.fec_ing_hosp.disabled=true;
     document.myForm_admu.hora_ini_hosp.disabled=true;
     document.myForm_admu.minuto_ini_hosp.disabled=true;
     document.myForm_admu.fec_ing_uci.disabled=true;
     document.myForm_admu.hora_ini_uci.disabled=true;
     document.myForm_admu.minuto_ini_uci.disabled=true;

     document.myForm_admu.sofa.disabled=true;
     document.myForm_admu.cama.disabled=true;
     
   
   //}
   if(json[0].estado == 'F'){
     $('#guardaraeu').attr('disabled',true);
     document.myForm_admu.id_ficha.disabled=true;
     document.myForm_admu.nombre.disabled=true;
     document.myForm_admu.apellido.disabled=true;
     document.myForm_admu.fec_nac.disabled=true;
     document.myForm_admu.edad.disabled=true;
     document.myForm_admu.run.disabled=true;
     document.myForm_admu.dv_run.disabled=true;
     document.myForm_admu.grado.disabled=true;
     document.myForm_admu.sn_iden.disabled=true;
     document.myForm_admu.ind_sexo.disabled=true;
     document.myForm_admu.hora_ini.disabled=true;
     document.myForm_admu.fec_op.disabled=true;
     document.myForm_admu.minuto_ini.disabled=true;
     document.myForm_admu.hora_ter.disabled=true;
     document.myForm_admu.minuto_ter.disabled=true;
     //document.myForm_admu.hora_ini_p.disabled=true;
     //document.myForm_admu.minuto_ini_p.disabled=true;
     //document.myForm_admu.hora_ter_p.disabled=true;
     //document.myForm_admu.minuto_ter_p.disabled=true;
     //document.myForm_admu.otros_prof.disabled=true;
     document.myForm_admu.nro_pabellon.disabled=true; 
     document.myForm_admu.ind_diag_pos.disabled=true;
     document.myForm_admu.cirujano.disabled=true;
     document.myForm_admu.anestesiologo.disabled=true;
     document.myForm_admu.ayudante_1.disabled=true;
     document.myForm_admu.ayudante_2.disabled=true;
     document.myForm_admu.enfermero_anest.disabled=true;
     document.myForm_admu.arsenalero.disabled=true;
     document.myForm_admu.pabellonero.disabled=true;
     document.myForm_admu.obs_diag_pos.disabled=true;
     document.myForm_admu.obs_oper.disabled=true;
     document.myForm_admu.recuento_1.disabled=true;
     document.myForm_admu.recuento_2.disabled=true;
     document.myForm_admu.recuento_3.disabled=true;
     document.myForm_admu.recuento_4.disabled=true;
     document.myForm_admu.recuento_ins.disabled=true;
     document.myForm_admu.anestesia_1.disabled=true;
     document.myForm_admu.anestesia_2.disabled=true;
     document.myForm_admu.biopsia.disabled=true;
     document.myForm_admu.classif_herida.disabled=true;
     document.myForm_admu.descrip_oper.disabled=true;
     document.myForm_admu.drenajes.disabled=true;
     document.myForm_admu.incidentes.disabled=true;
     document.myForm_admu.ind_drenajes.disabled=true;
     document.myForm_admu.ind_incidentes.disabled=true;
     document.myForm_admu.plantilla.disabled=true;
     document.myForm_admu.diag_pre_bus.disabled=true;
     document.myForm_admu.diag_pos_bus.disabled=true;
     document.myForm_admu.oper_bus.disabled=true;
     
     $("#btnDelDiagPre").attr('disabled',true);
     $("#btnDelDiagPos").attr('disabled',true);
     $("#btnDelOper").attr('disabled',true);  
   }
   }
});



var optionsbusu = { 
        
        success:       showResponseBusu
  };
  var optionsadmu = { 
        
        success:       showResponseAdmu
  };
  function showResponseBusu(responseText, statusText, xhr, $form)  { 
   resp = JSON.parse(responseText);
   //$('#guardaraeu').attr('disabled',false);
   
   document.myForm_admu.sn_iden.value="S";
   if (resp.estado == 1){
     if (resp.fichas_vig >0) {
        alert('Paciente ya tiene una ficha UPC en Edicion');
        document.myForm_busu.run.value="";
        document.myForm_busu.dv_run.value="";
    
        return;
     }
	 
     if(resp.hosp[0] != null){
     document.myForm_admu.fec_ing_hosp.value=resp.hosp[0].ingreso.substr(6,2)+'/'+resp.hosp[0].ingreso.substr(4,2)+'/'+resp.hosp[0].ingreso.substr(0,4);
     document.myForm_admu.hora_ini_hosp.value=resp.hosp[0].ingreso.substr(8,2);
     document.myForm_admu.minuto_ini_hosp.value=resp.hosp[0].ingreso.substr(10,2);
	 document.myForm_admu.inghosp.value=resp.hosp[0].inghosp;
	 document.myForm_admu.codinst.value=resp.hosp[0].codinst;
	 
     }
     else{
       
          alert('No se encontraron datos de Hospitalizacion');
                  document.myForm_busu.run.value="";
        document.myForm_busu.dv_run.value="";

          return;
       
     }
	 
      $("#myForm_admu").find('input','button').attr('disabled',false);
    //  $("#myForm_admu").find('select').attr('disabled',false);
     //  $('#guardaraeu').button("enable");
     document.myForm_admu.nombre.value=resp.primer_nombre+ ' '+ resp.segundo_nombre;
     document.myForm_admu.apellido.value= resp.apellido_paterno+ ' '+ resp.apellido_materno;

     document.myForm_admu.fec_nac.value=resp.fecha_nac;
     document.myForm_admu.edad.value=resp.edad;
     document.myForm_admu.run.value=resp.run;
     document.myForm_admu.dv_run.value=resp.dv_run;
     document.myForm_admu.grado.value=resp.sigla_grado;
     if (resp.sexo =='F')
     document.myForm_admu.ind_sexo[0].checked=true;
     else if (resp.sexo =='M')
     document.myForm_admu.ind_sexo[1].checked=true;
     if(resp.sexo ==null){

     document.myForm_admu.ind_sexo[0].readOnly=false;
     document.myForm_admu.ind_sexo[1].readOnly=false;
     document.myForm_admu.ind_sexo[0].disabled=false;
     document.myForm_admu.ind_sexo[1].disabled=false;
     
     }
     
     var j=0;
     var html="<ul>";
     if (resp.lab != null)
     for(j=0;j< resp.lab.length;j++){
        html=html+"<li><a href='#' onclick='muestra_examen(\""+resp.run+"-"+resp.dv_run+"\",\""+resp.lab[j].o_numero+"\")'>"+resp.lab[j].o_numero+ ' ('+resp.lab[j].fecha_toma+')</a></li>';
     }
     html=html+"</ul>"
     $('#lab').html(html);
     
     document.myForm_admu.nombre.readOnly=true;
     document.myForm_admu.apellido.readOnly=true;
     document.myForm_admu.edad.readOnly=true;
     document.myForm_admu.ind_sexo[0].readOnly=true;
     document.myForm_admu.ind_sexo[1].readOnly=true;
     document.myForm_admu.fec_nac.readOnly=true;
   }
   else{
     $("#myForm_admu").find('input','button').attr('disabled',false);
    // $("#myForm_admu").find('select').attr('disabled',false);
       $('#guardaraeu').button("enable");
     
     document.myForm_admu.run.value=document.myForm_busu.run.value;
     document.myForm_admu.dv_run.value=document.myForm_busu.dv_run.value;
     document.myForm_admu.grado.value="";
     document.myForm_admu.nombre.value="";
     document.myForm_admu.fec_nac.value="";
     document.myForm_admu.fec_nac.value="";
     document.myForm_admu.ind_sexo[0].checked=false;
     document.myForm_admu.ind_sexo[1].checked=false;
     document.myForm_admu.nombre.readOnly=false;
     document.myForm_admu.ind_sexo[0].readOnly=false;
     document.myForm_admu.ind_sexo[1].readOnly=false;
     document.myForm_admu.fec_nac.readOnly=false;
   }
   $('#resultreferencialu').html(resp.resbusq);
}
   function showResponseAdmu(responseText, statusText, xhr, $form)  { 
   resp = JSON.parse(responseText);
   if (resp.estado == 2){
     alert('Ficha Editada');
      jQuery("#list-fichas").jqGrid('setGridParam',{datatype:'json'}).trigger('reloadGrid');
      // carga_slider(); 
     $('#vwficha').dialog('close'); 
    
   }
   else if (resp.estado == 1){
     alert('Ficha creada');
     jQuery("#list-fichas").jqGrid('setGridParam',{datatype:'json'}).trigger('reloadGrid');
     //carga_slider();
     $("#vwficha").dialog('close'); 
   }
   else{
     alert(resp.error);
   
   }
   
  }
  jQuery.fn.reset = function () {
  $(this).each (function() { this.reset(); });
  
}
$('.enmdatos').hide();
$('.tabenm').hide();
$(".buttabenm").button({icons: {primary: "ui-icon-pencil"}});
  $('#myForm_busu').ajaxForm(optionsbusu); 
  $('#myForm_admu').ajaxForm(optionsadmu); 
  $('#fec_nacu').datepicker({changeMonth:true,changeYear:true,yearRange:"-110:+0",maxDate: new Date(),dateFormat:"dd/mm/yy"}); 
  $('#fec_ing_hosp').datepicker({changeMonth:true,changeYear:true,yearRange:"-110:+0",maxDate: new Date(),dateFormat:"dd/mm/yy"});
  $('#fec_ing_uci').datepicker({changeMonth:true,changeYear:true,yearRange:"-110:+0",maxDate: new Date(),dateFormat:"dd/mm/yy"});
</script>
<fieldset>
<?php
if($_REQUEST["id_ficha"] != ""){
 $dbh=genera_adodb("portal");
 $creador="Creada por ";
 $ficha=retorna_ficha($db,$_REQUEST["id_ficha"] );
 $creador.=utf8_encode(retorna_datos_portal($dbh,$ficha[0]["usuario"]));
  $creador.=" con fecha : ".$ficha[0]["fec_creacion"];
 $dbh->disconnect();
}
?>
<legend><b>FICHA UCI (<?php echo retorna_tipo_ficha($db,$_REQUEST["tipo_ficha"],$_REQUEST["subtipo_ficha"]) .") ". $creador ;?></b></legend>
<fieldset>
<legend><b>Identificacion</b></legend>

<form id="myForm_busu" name="myForm_busu" onsubmit="return valida_buscar(this);" action="Servicios/getReferenciales.php" method="post"> 

  <table><tr><td><b>RUT: </b><input type="text" name="run" size="8" maxlength="8">-<input type="text" name="dv_run" size="1" maxlength="1"> </td><td> <button type="submit" id="busaeu">Buscar</button> </td><td><div id="resultreferencialu" style="color:#FF0000;"></div></td></tr></table>

</form>
</fieldset>
<form id="myForm_admu" name="myForm_admu"  onsubmit="return valida_ficha(this);" action="Servicios/setFicha.php" method="post"> 
<input type="hidden" name="trans" value="<?php if ($_REQUEST["id_ficha"] == "")echo "add"; else echo "edit";?>">
<input type="hidden" name="run" >
<input type="hidden" name="dv_run" >
<input type="hidden" name="grado" >
<input type="hidden" name="sn_iden" >
<input type="hidden" name="sn_enmienda" id="sn_enmienda" value='N'>
<input type="hidden" name="id_ficha" >
<input type="hidden" name="diag_pos" >
<input type="hidden" name="oper" >
<input type="hidden" name="usuario" >
<input type="hidden" name="inghosp"><input type="hidden" name="codinst">
<input type="hidden" name="tipo_ficha" value="<?php echo $_REQUEST["tipo_ficha"]; ?>">
<input type="hidden" name="subtipo_ficha" value="<?php echo $_REQUEST["subtipo_ficha"]; ?>">

     
<fieldset>
<legend><b>Datos Personales</b></legend>
<table>
<tr>
 <td><b>Nombre:</b></b></td>
 <td><input type="text"  name="nombre" size="50" maxlength="300"></td>
 <td><b>Apellidos:</b></td>
 <td><input type="text"  name="apellido" size="50" maxlength="300"></td>
</tr>
<tr>
 <td><b>Fecha Nacimiento:</b></td>
 <td><input type="text" id="fec_nacu" name="fec_nac" size="10" maxlength="10"></td>
 <td><b>Edad:</b></td>
 <td><input type="text" name="edad" size="3" maxlength="3"></td>
</tr>
<tr>
 <td><b>Sexo:</b></td>
 <td><input type="radio" name="ind_sexo" value="F" onclick="return inhiberadio(this)">F<input type="radio" name="ind_sexo" value="M" onclick="return inhiberadio(this)">M</td>
 <td colspan="2">&nbsp;</td>
</tr>
 <tr><td><b>Fecha Hospitalizacion: </b></td><td><input type="text" id="fec_ing_hosp" name="fec_ing_hosp" size="10" maxlength="10">
 </td>
 <td><b>Hora :</b></td>
 <td><select name="hora_ini_hosp">
<?php
  for($i=0;$i<=23;$i++){
  if($i<=9)
  $hora ="0".$i;
  else
  $hora =$i;
   
  print("<option value=\"$hora\">$hora</option>");
  }
?>
 </select> : <select name="minuto_ini_hosp">
<?php
  for($i=0;$i<=59;$i++){
  if($i<=9)
  $minuto ="0".$i;
  else
  $minuto =$i;
   
  print("<option value=\"$minuto\">$minuto</option>");
  }
?>
 </select> 
 </td>
</tr>
  <tr><td><b>Fecha Ingreso UCI:</b> </td><td> <input type="text" id="fec_ing_uci" name="fec_ing_uci" size="10" maxlength="10">
 </td>
 <td><b>Hora :</b></td>
 <td><select name="hora_ini_uci">
<?php
  for($i=0;$i<=23;$i++){
  if($i<=9)
  $hora ="0".$i;
  else
  $hora =$i;
   
  print("<option value=\"$hora\">$hora</option>");
  }
?>
 </select> : <select name="minuto_ini_uci">
<?php
  for($i=0;$i<=59;$i++){
  if($i<=9)
  $minuto ="0".$i;
  else
  $minuto =$i;
   
  print("<option value=\"$minuto\">$minuto</option>");
  }
?>
 </select> 
 </td>
</tr>
<tr id="d_sofa">
 <td><b>Sofa Ingreso:</b></td>
 <td colspan="3"><select name="sofa" id="sofa">
 <?php
   for($i=0;$i<=18;$i++)
      print("<option value=\"".$i."\">".$i."</option>");
 ?>
 </select></td>
</tr>  
<tr id="d_cama">
 <td><b> Nro Cama:</b></td>
 <td colspan="3"><select name="cama" id="cama"> <option value="">Seleccionar Nro de Cama</option>
 <?php
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

   for($i=1;$i<=$lim;$i++)
      print("<option value=\"".$i."\">".$i."</option>");
 ?>
 </select></td>
</tr>  


</table>
</fieldset>
<?php
     $valores=array();
     $valores[]=$_REQUEST["tipo_ficha"];
     $recordset = $db->Execute("select * from seccion where cod_seccion=1 and tipo_ficha=? order by 1",$valores);
  if (!$recordset) die("hhh".$db->ErrorMsg());

  while ($arr = $recordset->FetchRow()) {
    print("<fieldset>");
    print("<legend><b>".utf8_encode($arr["desc_seccion"])."</b></legend>");


	
 
     $valores=array();
 
     $valores[]=$arr["cod_seccion"];
      $valores[]=$_REQUEST["tipo_ficha"];
     $recordset_d = $db->Execute("select * from datos where  cod_seccion=? and tipo_ficha=? order by 1",$valores);
     if (!$recordset_d) die("hhh".$db->ErrorMsg());
     print("<table border='0' >");
     while ($arr_d = $recordset_d->FetchRow()) {
      $arr_d["descr"]=utf8_encode($arr_d["descr"]);
      $required=($arr_d["mand"]=='S')?'required':'';
      if(($_REQUEST["id_ficha"] =="")||(($_SESSION["fichaupc"]["tipo_usuario"] ==1)&&($ficha[0]["estado"] =="I"))){
      if($arr_d["tipo_entrada"] =="TA"){
	   if($ficha[0]["estado"] =="I")
	   
       $rtf=($arr_d["rtf"]=='S')?'rtfval':'';
	   else
       $rtf=($arr_d["rtf"]=='S')?'rtf':''; 
       print("<tr><td width=\"250px\"><b>".$arr_d["descr"]."</b><br><textarea name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  cols='200' rows='4'  $required class='$rtf'>".$arr_d["prellenado"]."</textarea></td></tr>");
      }
      elseif($arr_d["tipo_entrada"] =="T"){
       print("<tr><td width=\"250px\"><b>".$arr_d["descr"]."</b><br><input type='text' name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  size='".$arr_d["largo"]."' maxlength='".$arr_d["largo"]."'  $required value='".$arr_d["prellenado"]."'></td></tr>");
      }
	  elseif($arr_d["tipo_entrada"] =="D"){
       print("<tr><td width=\"250px\"><b>".$arr_d["descr"]."</b><br><input type='text' name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  size='10' maxlength='10'  $required value='".$arr_d["prellenado"]."' readonly class='fecha'></td></tr>");
      }
	  elseif($arr_d["tipo_entrada"] =="R"){
      $rang=explode(",",$arr_d["rango"]);
	  print("<tr><td width=\"250px\"><b>".$arr_d["descr"]."</b><br>");
      foreach($rang as $kk => $j)
        print("<input type='radio' name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."' value='".$j."'  $required > ".$j."");
		if($arr_d["sn_obs"] =="S")
		  print(" <input type='text' size='40' maxlength='40' name='obs_datos_".$arr_d["cod"]."' id='obs_datos_".$arr_d["cod"]."'>");
		print("</td></tr>");
      }
      elseif($arr_d["tipo_entrada"] =="S"){
      $rang=explode("-",$arr_d["rango"]);
      print("<tr><td width=\"250px\"><b>".$arr_d["descr"]."</b><br><select name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  $required>");
      for($j=$rang[0];$j<=$rang[1];$j++)
        print("<option value='$j'>$j</option>");
      print("</select></td></tr>");
       
      }
      }
      else{
        $class=array();
        $clase="";
        if($required !="")
        $class[]=$required;
      if($arr_d["tipo_entrada"] =="TA"){
       $rtf=($arr_d["rtf"]=='S')?'rtfenm':'';
       if($rtf !="")
        $class[]=$rtf;
       $clase=implode(",",$class); 
       print("<tr><td width='250px'><b>".$arr_d["descr"]."</b><br><div id='datos_".$arr_d["cod"]."'></div></td></tr><tr class=\"enmdatos\"><td><textarea name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."'  cols='200' rows='4'  class='$clase'>".$arr_d["prellenado"]."</textarea></td></tr>");
      }
      elseif($arr_d["tipo_entrada"] =="T"){
       $clase=implode(",",$class); 
       
       print("<tr><td width='250px'><b>".$arr_d["descr"]."</b><br><div id='datos_".$arr_d["cod"]."'></div></td></tr><tr class=\"enmdatos\"><td><input type='text' name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."'  size='".$arr_d["largo"]."' maxlength='".$arr_d["largo"]."'  class='$clase' value='".$arr_d["prellenado"]."'></td></tr>");
      }
	  elseif($arr_d["tipo_entrada"] =="D"){
		$class[]="fecha";
       $clase=implode(",",$class); 
       
       print("<tr><td width='250px'><b>".$arr_d["descr"]."</b><br><div id='datos_".$arr_d["cod"]."'></div></td></tr><tr class=\"enmdatos\"><td><input type='text' name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."'  size='8' maxlength='8'  class='$clase' value='".$arr_d["prellenado"]."'></td></tr>");
      }
	  elseif($arr_d["tipo_entrada"] =="R"){
      $clase=implode(",",$class); 
       
      $rang=explode(",",$arr_d["rango"]);
      print("<tr><td width='250px'><b>".$arr_d["descr"]."</b><br><div id='datos_".$arr_d["cod"]."'></div> <div id='obs_datos_".$arr_d["cod"]."'></div></td></tr><tr class=\"enmdatos\"><td>");
      foreach($rang as $kk => $j)
        print("<input type='radio' name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."' value='".$j."' class='$clase'> ".$j."");
      		if($arr_d["sn_obs"] =="S")
		  print(" <input type='text' size='40' maxlength='40' name='obs_enmdatos_".$arr_d["cod"]."' id='obs_enmdatos_".$arr_d["cod"]."'>");

	  print("</td></tr>");
       
      }
      elseif($arr_d["tipo_entrada"] =="S"){
      $clase=implode(",",$class); 
       
      $rang=explode("-",$arr_d["rango"]);
      print("<tr><td width='250px'><b>".$arr_d["descr"]."</b><br><div id='datos_".$arr_d["cod"]."'></div> <div id='obs_datos_".$arr_d["cod"]."'></div></td></tr><tr class=\"enmdatos\"><td><select name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."' class='$clase'>");
      for($j=$rang[0];$j<=$rang[1];$j++)
        print("<option value='$j'>$j</option>");
		
      print("</select></td></tr>");
       
      }  
      }
     }
      print("</table>");
    
    print("</fieldset>");
    
  }

?>
<fieldset>
<legend><b>Antecedentes</b></legend>
 <?php
     $valores=array();
     $valores[]=$_REQUEST["tipo_ficha"];
     $recordset = $db->Execute("select * from antecedentes where tipo_ficha=? order by 2",$valores);
  if (!$recordset) die("hhh".$db->ErrorMsg());
      print("<table border='0'>");

  while ($arr = $recordset->FetchRow()) {
    $arr["descr"]=utf8_encode($arr["descr"]);
    if($arr["mand"] =="S")
      $required='required';
     else
      $required='';
     
   // print("<tr><td>".$arr["descr"]."</td><td> SI <input type='radio' name='ant_".$arr["cod"]."' id='ant_".$arr["cod"]."' class='ant' value='S' $required onclick='habilita_obs(\"".$arr["cod"]."\")'>  NO <input type='radio' name='ant_".$arr["cod"]."' id='ant_".$arr["cod"]."' class='ant' value='N' $required onclick='habilita_obs(\"".$arr["cod"]."\")'> <td width='70%'><textarea name='obs_ant_".$arr["cod"]."' id='obs_ant_".$arr["cod"]."' size='100' maxlength='1000' class='ant' cols='160' rows='4' disabled></textarea></td></td></tr>");
    if(($_REQUEST["id_ficha"] =="")||(($_SESSION["fichaupc"]["tipo_usuario"] ==1)&&($ficha[0]["estado"] =="I"))){
    print("<tr><td>".$arr["descr"]."</td><td> SI <input type='radio' name='ant_".$arr["cod"]."' id='ant_".$arr["cod"]."' class='ant' value='S' required onclick='habilita_obs(\"ant_".$arr["cod"]."\")'>  NO <input type='radio' name='ant_".$arr["cod"]."' id='ant_".$arr["cod"]."' class='ant' value='N' required onclick='habilita_obs(\"ant_".$arr["cod"]."\")'></td> <td width='70%'><textarea name='obs_ant_".$arr["cod"]."' id='obs_ant_".$arr["cod"]."' size='100' maxlength='1000' class='ant' cols='160' rows='4' disabled></textarea></td></tr>");
       }
       else{
        $required="required";
        $class=array();
        $class[]='ant';
        $clase="";
        if($required !="")
        $class[]=$required;
        $clase=implode(",",$class); 
        print("<tr><td width='20%'>".$arr["descr"]." : </td><td><table><tr><td><div id='ant_".$arr["cod"]."'></div></td><td><div id='obs_ant_".$arr["cod"]."'></div></td></tr></table></td></tr><tr  class=\"enmdatos\"><td>".$arr["descr"]."</td><td> SI <input type='radio' name='enmant_".$arr["cod"]."' id='enmant_".$arr["cod"]."' class='$clase' value='S'  onclick='habilita_obs(\"enmant_".$arr["cod"]."\")'>  NO <input type='radio' name='enmant_".$arr["cod"]."' id='enmant_".$arr["cod"]."' class='$clase' value='N'  onclick='habilita_obs(\"enmant_".$arr["cod"]."\")' ></td> <td width='70%'><textarea name='obs_enmant_".$arr["cod"]."' id='obs_enmant_".$arr["cod"]."' size='100' maxlength='1000' class='ant' cols='160' rows='4' ></textarea></td></tr>");
    
       }
  }
 print("</table>");

  ?>
</fieldset>
<fieldset>
<legend><b>Examen Fisico</b></legend>

    <?php
     $valores=array();
     $valores[]=$_REQUEST["tipo_ficha"];
     $recordset = $db->Execute("select * from examen_fisico where tipo_ficha=? order by 2,3",$valores);
  if (!$recordset) die("hhh".$db->ErrorMsg());
  $datos_ex=array();
  while ($arr = $recordset->FetchRow()) {
    $arr["descr"]=utf8_encode($arr["descr"]);
    $datos_ex[$arr["tipo"]][]=$arr;
  }
  if(count($datos_ex["G"])>0){
    print("<b>General</b><table border='0'>");
    foreach($datos_ex["G"] as $k => $fila){
        if(($_REQUEST["id_ficha"] =="")||(($_SESSION["fichaupc"]["tipo_usuario"] ==1)&&($ficha[0]["estado"] =="I"))){
         print("<tr><td>".$fila["descr"]." : </td><td><input type='text' name='exa_".$fila["cod"]."' id='exa_".$fila["cod"]."' size='100' maxlength='1000' class='exa' ></td></tr>");
        }
      else{
        $required=($fila["mand"]=='S')?'required':'';
        $class=array();
        $class[]='exa';
        $clase="";
        if($required !="")
        $class[]=$required;
        $clase=implode(",",$class); 
        print("<tr><td width='20%'>".$fila["descr"]." : </td><td><table><tr><td>&nbsp;</td><td><div id='exa_".$fila["cod"]."'></div></td></tr></table></td></tr><tr  class=\"enmdatos\"><td>".$fila["descr"]."</td><td> <input type='text' name='enmexa_".$fila["cod"]."' id='enmexa_".$fila["cod"]."'  value='S'  > </td></tr>");
    
      }
   }
    print("</table>");
    
  }
  if(count($datos_ex["S"])>0){
    print("<b>Segmentario</b><table border='0'>");
    foreach($datos_ex["S"] as $k => $fila){
       if(($_REQUEST["id_ficha"] =="")||(($_SESSION["fichaupc"]["tipo_usuario"] ==1)&&($ficha[0]["estado"] =="I"))){
         print("<tr><td>".$fila["descr"]." : </td><td><input type='text' name='exa_".$fila["cod"]."' id='exa_".$fila["cod"]."' size='100' maxlength='1000' class='exa' ></td></tr>");
        }
      else{
        $required=($fila["mand"]=='S')?'required':'';
        $class=array();
        $class[]='exa';
        $clase="";
        if($required !="")
        $class[]=$required;
        $clase=implode(",",$class); 
        print("<tr><td width='20%'>".$fila["descr"]." : </td><td><table><tr><td>&nbsp;</td><td><div id='exa_".$fila["cod"]."'></div></td></tr></table></td></tr><tr  class=\"enmdatos\"><td>".$fila["descr"]."</td><td> <input type='text' name='enmexa_".$fila["cod"]."' id='enmexa_".$fila["cod"]."'  value='S'  >   </td></tr>");
    
      }
    }
    print("</table>");
    
  }
  ?>
</fieldset>
<?php
     $valores=array();
     $valores[]=$_REQUEST["tipo_ficha"];
     $recordset = $db->Execute("select * from seccion where cod_seccion=2  and tipo_ficha=? order by 1",$valores);
  if (!$recordset) die("hhh".$db->ErrorMsg());

  while ($arr = $recordset->FetchRow()) {
    print("<fieldset>");
    print("<legend><b>".utf8_encode($arr["desc_seccion"])."</b></legend>");


	
 
     $valores=array();
 
     $valores[]=$arr["cod_seccion"];
      $valores[]=$_REQUEST["tipo_ficha"];
     $recordset_d = $db->Execute("select * from datos where  cod_seccion=? and tipo_ficha=? order by 1",$valores);
     if (!$recordset_d) die("hhh".$db->ErrorMsg());
     print("<table border='0' >");
     while ($arr_d = $recordset_d->FetchRow()) {
      $arr_d["descr"]=utf8_encode($arr_d["descr"]);
      $required=($arr_d["mand"]=='S')?'required':'';
      if(($_REQUEST["id_ficha"] =="")||(($_SESSION["fichaupc"]["tipo_usuario"] ==1)&&($ficha[0]["estado"] =="I"))){
      if($arr_d["tipo_entrada"] =="TA"){
	   if($ficha[0]["estado"] =="I")
	   
       $rtf=($arr_d["rtf"]=='S')?'rtfval':'';
	   else
       $rtf=($arr_d["rtf"]=='S')?'rtf':''; 
       print("<tr><td width=\"250px\"><b>".$arr_d["descr"]."</b><br><textarea name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  cols='200' rows='4'  $required class='$rtf'>".$arr_d["prellenado"]."</textarea></td></tr>");
      }
      elseif($arr_d["tipo_entrada"] =="T"){
       print("<tr><td width=\"250px\"><b>".$arr_d["descr"]."</b><br><input type='text' name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  size='".$arr_d["largo"]."' maxlength='".$arr_d["largo"]."'  $required value='".$arr_d["prellenado"]."'></td></tr>");
      }
	  elseif($arr_d["tipo_entrada"] =="D"){
       print("<tr><td width=\"250px\"><b>".$arr_d["descr"]."</b><br><input type='text' name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  size='10' maxlength='10'  $required value='".$arr_d["prellenado"]."' readonly class='fecha'></td></tr>");
      }
	  elseif($arr_d["tipo_entrada"] =="R"){
      $rang=explode(",",$arr_d["rango"]);
	  print("<tr><td width=\"250px\"><b>".$arr_d["descr"]."</b><br>");
      foreach($rang as $kk => $j)
        print("<input type='radio' name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."' value='".$j."'  $required > ".$j."");
		if($arr_d["sn_obs"] =="S")
		  print(" <input type='text' size='40' maxlength='40' name='obs_datos_".$arr_d["cod"]."' id='obs_datos_".$arr_d["cod"]."'>");
		print("</td></tr>");
      }
      elseif($arr_d["tipo_entrada"] =="S"){
      $rang=explode("-",$arr_d["rango"]);
      print("<tr><td width=\"250px\"><b>".$arr_d["descr"]."</b><br><select name='datos_".$arr_d["cod"]."' id='datos_".$arr_d["cod"]."'  $required>");
      for($j=$rang[0];$j<=$rang[1];$j++)
        print("<option value='$j'>$j</option>");
      print("</select></td></tr>");
       
      }
      }
      else{
        $class=array();
        $clase="";
        if($required !="")
        $class[]=$required;
      if($arr_d["tipo_entrada"] =="TA"){
       $rtf=($arr_d["rtf"]=='S')?'rtfenm':'';
       if($rtf !="")
        $class[]=$rtf;
       $clase=implode(",",$class); 
       print("<tr><td width='250px'><b>".$arr_d["descr"]."</b><br><div id='datos_".$arr_d["cod"]."'></div></td></tr><tr class=\"enmdatos\"><td><textarea name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."'  cols='200' rows='4'  class='$clase'>".$arr_d["prellenado"]."</textarea></td></tr>");
      }
      elseif($arr_d["tipo_entrada"] =="T"){
       $clase=implode(",",$class); 
       
       print("<tr><td width='250px'><b>".$arr_d["descr"]."</b><br><div id='datos_".$arr_d["cod"]."'></div></td></tr><tr class=\"enmdatos\"><td><input type='text' name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."'  size='".$arr_d["largo"]."' maxlength='".$arr_d["largo"]."'  class='$clase' value='".$arr_d["prellenado"]."'></td></tr>");
      }
	  elseif($arr_d["tipo_entrada"] =="D"){
		$class[]="fecha";
       $clase=implode(",",$class); 
       
       print("<tr><td width='250px'><b>".$arr_d["descr"]."</b><br><div id='datos_".$arr_d["cod"]."'></div></td></tr><tr class=\"enmdatos\"><td><input type='text' name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."'  size='8' maxlength='8'  class='$clase' value='".$arr_d["prellenado"]."'></td></tr>");
      }
	  elseif($arr_d["tipo_entrada"] =="R"){
      $clase=implode(",",$class); 
       
      $rang=explode(",",$arr_d["rango"]);
      print("<tr><td width='250px'><b>".$arr_d["descr"]."</b><br><div id='datos_".$arr_d["cod"]."'></div> <div id='obs_datos_".$arr_d["cod"]."'></div></td></tr><tr class=\"enmdatos\"><td>");
      foreach($rang as $kk => $j)
        print("<input type='radio' name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."' value='".$j."' class='$clase'> ".$j."");
      		if($arr_d["sn_obs"] =="S")
		  print(" <input type='text' size='40' maxlength='40' name='obs_enmdatos_".$arr_d["cod"]."' id='obs_enmdatos_".$arr_d["cod"]."'>");

	  print("</td></tr>");
       
      }
      elseif($arr_d["tipo_entrada"] =="S"){
      $clase=implode(",",$class); 
       
      $rang=explode("-",$arr_d["rango"]);
      print("<tr><td width='250px'><b>".$arr_d["descr"]."</b><br><div id='datos_".$arr_d["cod"]."'></div> <div id='obs_datos_".$arr_d["cod"]."'></div></td></tr><tr class=\"enmdatos\"><td><select name='enmdatos_".$arr_d["cod"]."' id='enmdatos_".$arr_d["cod"]."' class='$clase'>");
      for($j=$rang[0];$j<=$rang[1];$j++)
        print("<option value='$j'>$j</option>");
		
      print("</select></td></tr>");
       
      }  
      }
     }
      print("</table>");
    
    print("</fieldset>");
    
  }

?>



<?php
if(($_SESSION["fichaupc"]["usuario"]==$ficha[0]["usuario"])&&($_REQUEST["id_ficha"] != "")&&($ficha[0]["estado"] =="D")){
    print("<center><button type='button' onclick='toggleingenm(this);' class='buttabenm' id='butingenm'>Mostrar Nueva Enmienda Datos Ingreso</button></center>");
}
   if (($_REQUEST["id_ficha"] != "")&&($ficha[0]["estado"] =="D")){
?>
<fieldset>
<legend><b>Evolucion</b></legend>
 
     <?php
     $valores=array();
     $valores[]=$_REQUEST["id_ficha"];
     $recordset = $db->Execute("select to_char(a.fecha,'DD/MM/YYYY HH24:mi:ss') as fecha_f,fecha,a.id_ficha,a.sec_evo,a.estado,a.evolucion,a.usuario,(nvl((select max(x.sec) from enmienda x where x.id_ficha=a.id_ficha and x.sec_evo=a.sec_evo),0)) as enm,a.sn_interconsulta,a.cod_especialidad  from evolucion a where a.id_ficha=? order by fecha",$valores);
  if (!$recordset) die("hhh".$db->ErrorMsg());  
  $dbh=genera_adodb("portal");
while ($arr = $recordset->FetchRow()) {
  
  $nombre_us=retorna_datos_portal($dbh,$arr["usuario"]);
  
  $sec_evo=$arr["sec_evo"];
  $style="";
  $button="";
  if($arr["estado"] =="I"){
  $style="style=\"background-color:yellow\"";
  if ($_SESSION["fichaupc"]["tipo_usuario"] ==1)
  $button="<button class=\"validar\" onclick=\"valida_evolucion('".$arr["id_ficha"]."','".$arr["sec_evo"]."');\"type=\"button\">Validar Evoluci&oacute;n</button>";
  }
  $interconsulta ="&nbsp;";
  if($arr["sn_interconsulta"] =="S")
      $interconsulta ="<span> Es Interconsulta</span>"; 
  print("<fieldset ".$style."><legend><b>".$nombre_us." - " .$arr["fecha_f"] ." ". $interconsulta ."</b> ".$button."</legend>");
  
  //if($_SESSION["fichaupc"]["usuario"]==$arr["usuario"])
  //print("<textarea class='editor' id='evolucion_".$arr["sec_evo"]."' name='evolucion_".$arr["sec_evo"]."' style=\"width:100%;height:300px;\">".gzinflate($arr["evolucion"])."</textarea>");
  //else
   if($arr["estado"] =="I"){
    if($arr["enm"] > 0)
  print("<del style=\"width:600px !important;max-width:600px !important;display:inline-block !important;\">");
  
   if ($_SESSION["fichaupc"]["tipo_usuario"] ==1){
 	print("<tr><td  valign='top'><b>Evoluci&oacute;n</b><textarea class='editor' id='evolucion_".$arr["id_ficha"]."_".$arr["sec_evo"]."' name='evolucion".$arr["id_ficha"]."_".$arr["sec_evo"]."' style='width:100%;height:300px'>".gzinflate($arr["evolucion"])."</textarea></td></tr>");
   }
   else{
	 if($arr["enm"] > 0){
  print(str_ireplace("width","widtddh",gzinflate($arr["evolucion"])));
  }
  else{
  print("<div style=\"width:600px !important;max-width:600px !important;display:inline-block; !important;\">".str_ireplace("width","widtddh",gzinflate($arr["evolucion"]))."</div>");
  }
   }
  }
  else{
	 if($arr["enm"] > 0){
  print("<del style=\"width:600px !important;max-width:600px !important;display:inline-block !important;\">");
  print(str_ireplace("width","widtddh",gzinflate($arr["evolucion"])));
  }
  else{
  print("<div style=\"width:600px !important;max-width:600px !important;display:inline-block; !important;\">".str_ireplace("width","widtddh",gzinflate($arr["evolucion"]))."</div>");
  }
   
  }
  $valores=array();
  $valores[]=$_REQUEST["id_ficha"];
  $valores[]=$sec_evo;
  $recordset_img = $db->Execute("select id_ficha,sec_evo,sec,enmienda,fecha,to_char(fecha,'DD/MM/YYYY HH24:mi:ss') as fecha_f from enmienda where id_ficha=? and sec_evo=? order by fecha",$valores);
  if (!$recordset_img) die("hhh".$db->ErrorMsg());
  $cont=0;
  while ($arr_img = $recordset_img->FetchRow()) {
      if($arr["enm"] ==$arr_img["sec"])
       print("</del>");
    print("<b>".$arr_img["fecha_f"]."</b><br>".gzinflate($arr_img["enmienda"]));
  }
  if($_SESSION["fichaupc"]["usuario"]==$arr["usuario"]){
    print("<center><button type='button' onclick='toggletabenm(this);' class='buttabenm' id='buttabenm_".$arr["id_ficha"]."_".$arr["sec_evo"]."'>Mostrar Nueva Enmienda</button></center><table border='2' width='100%' class='tabenm' id='tabenm_".$arr["id_ficha"]."_".$arr["sec_evo"]."'>");
    print("<tr><th>Nueva Enmienda</th></tr>");
   
    print("<tr><td  valign='top'><textarea class='editor' id='enmienda_".$arr["id_ficha"]."_".$arr["sec_evo"]."' name='enmienda_".$arr["id_ficha"]."_".$arr["sec_evo"]."' style='width:100%;height:300px'></textarea></td></tr>");
print("</table>");
  }
  if($_SESSION["fichaupc"]["usuario"]==$arr["usuario"])
  print("<br><hr><br><b>Nuevo Anexo</b> <input type='file' name='adjunto_".$arr["id_ficha"]."_".$arr["sec_evo"]."'><br><hr><br>");
  $valores=array();
  $valores[]=$_REQUEST["id_ficha"];
  $valores[]=$sec_evo;
  $recordset_img = $db->Execute("select id_ficha,sec_evo,sec from anexo where id_ficha=? and sec_evo=?",$valores);
  if (!$recordset_img) die("hhh".$db->ErrorMsg());
  $cont=0;
  while ($arr_img = $recordset_img->FetchRow()) {
    $cont++;
    if($cont ==1){
     if($_SESSION["fichaupc"]["usuario"]==$arr["usuario"])
      print("<table border='1'><tr><th width=\"15%\">Elimina</th><th>Adjunto</th></tr>");
     else
      print("<table border='1'><tr><th>Adjunto</th></tr>");
     
    }
    if($_SESSION["fichaupc"]["usuario"]==$arr["usuario"])
      print("<tr><td><center><input type='checkbox' name='elimina_adj_".$arr_img["id_ficha"]."_".$arr_img["sec_evo"]."_".$arr_img["sec"]."' value='S'></center></td><td><img src='Servicios/getAdjunto.php?id_ficha=".$_REQUEST["id_ficha"]."&sec_evo=".$sec_evo."&sec=".$arr_img["sec"]."' height='300px' width='750px'></td></tr>");
    else
      print("<tr><td><img src='Servicios/getAdjunto.php?id_ficha=".$_REQUEST["id_ficha"]."&sec_evo=".$sec_evo."&sec=".$arr_img["sec"]."' width='750px'  height='300px' ></td></tr>");
    
  }
  if($cont >0)
    print("</table>");
  print("</fieldset>");
  
    }
    $dbh->disconnect();
    ?>
<?php
  $espec=array();
  if($_SESSION["portal"]["especialidad"] !=""){
    $espec[$_SESSION["portal"]["especialidad"]]=1;	
  }
  if($_SESSION["portal"]["subespecialidad1"] !=""){
    $espec[$_SESSION["portal"]["subespecialidad1"]]=1;	
  }
  if($_SESSION["portal"]["subespecialidad2"] !=""){
    $espec[$_SESSION["portal"]["subespecialidad2"]]=1;	
  }
  if(count($espec) >0){
	$dbp = genera_adodb("portal");
	$sqlp="select * from especialidad";
	$recordsetp = $dbp->Execute($sqlp);
    if (!$recordsetp) die($dbp->ErrorMsg());  
    while ($arrp = $recordsetp->FetchRow()) {
       if($espec[$arrp["cod_especialidad"]] == 1)
	      $espec[$arrp["cod_especialidad"]]=$arrp["desc_especialidad"];
	}
	$dbp->disconnect();
  }
?>
<table border="2" width="100%">
    <tr><th width="80%">Nueva Evolucion <?php
	if(count($espec) >0){
	 print('(¿Es Interconsulta? <input type="checkbox" name="interconsulta" value="S"> ');
	 print(" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Especialidad <select name='especialidad'>");
	 foreach($espec as $ve => $ke){
	  print("<option value='".$ve."'>".$ke."</option>");
	 }
	 print("</select> )");
	}
	?></th><th width="20%">Examenes</th></tr>
   
    <tr><td  valign="top"><textarea class='editor' id="evolucion" name="evolucion" style="width:100%;height:100%;"></textarea></td><td><div id="lab" style=" height:300px;overflow: scroll;"></div></td></tr>
</table>
<?php
 if($_REQUEST["id_ficha"] != ""){
 ?>
<br><center><button type="button" onclick="mostrar_embebido_examrayos('<?php echo $_REQUEST["id_ficha"]; ?>','FU','<?php echo $_SESSION["fichaupc"]["usuario"];?>',document.myForm_admu.run.value,document.myForm_admu.dv_run.value);">Crear Solicitud Rayos</button></center>
<?php
 }
?>
<?php
if($_SESSION["fichaupc"]["tipo_usuario"] !=3){
  ?>
<legend><b>Anexos</b></legend>
Anexo <input type="file" name="adjunto"><br>

</fieldset>
<?php
}
?>

<center><button type="submit" id="guardaraeu">Guardar</button>  
</center>

<?php
   }
elseif (($_SESSION["fichaupc"]["tipo_usuario"] =="1")&&($_REQUEST["id_ficha"] != "")&&($ficha[0]["estado"] =="I")){
   ?>
<center><button type="submit" onclick="document.myForm_admu.trans.value='val';" id="validaraeu">Validar</button>  
</center>
   
<?php
   }
   else{
   ?>
   <center><button type="submit" id="guardaraeu">Guardar</button>  
</center>
<?php
   }
   ?>
</form>
</fieldset>
<?php
 $db->disconnect();
?>

<form name="veradjunto" method="post" action="Servicios/getAdjunto.php" target="_blank">
<input type="hidden" name="id_ficha" value="<?php echo $_REQUEST['id_ficha']; ?>">
<input type="hidden" name="sec" value="">


</form>
