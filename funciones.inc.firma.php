<?php
function retorna_id_documento($id_us,$pin){
$url_final="http://172.25.16.18/DocumentSigner/DocumentManagerServlet?method=reserve&userId=".$id_us."&pin=".$pin;
$params = array('http' => array(
              'method' => 'GET'
            ));

  $ctx = stream_context_create($params);
  $fp = @fopen($url_final, 'rb', false, $ctx);
  if (!$fp) {
    //return null;
           throw new Exception("Problema con $url_final, $php_errormsg");
	
  }
  $response = @stream_get_contents($fp);
  if ($response === false) {
    //return null; 
    throw new Exception("Problema leyendo datos from $url_final, $php_errormsg");
    
  }
return json_decode($response, true);
}
function firmar_documento($id_doc,$id_us,$pin,$doc){
$fp=fopen("/tmp/".$id_doc.".pdf",'wb');
fwrite($fp,$doc);
fclose($fp); 
$url_final="http://172.25.16.18/DocumentSigner/DocumentManagerServlet?method=sign&context=test&docId=".
$id_doc."&userId=".$id_us."&pin=".$pin;
$file_name_with_full_path = realpath("/tmp/".$id_doc.".pdf");
 $post = array(
'file_contents'=>'@'.$file_name_with_full_path);
 
        $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url_final);
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$result=curl_exec ($ch);
	curl_close ($ch);
	return $result;


}


?>
