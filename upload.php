<?php
//header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified', true, 304);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
//require_once('_classes/upload.class.php');

$serve = mysqli_connect('localhost', 'weew_app', 'Alvvo18BR', 'weew_app');

mysqli_query($serve,"SET NAMES 'utf8'");

mysqli_query($serve,'SET character_set_connection=utf8');

mysqli_query($serve,'SET character_set_client=utf8');

mysqli_query($serve,'SET character_set_results=utf8');

/*
Status de clientes

*/
$server = "https://weewbr.com/";

/*error_reporting(E_ALL);
ini_set("display_errors", 1);*/

$uploaddir = 'upload/';

if(!empty($_FILES['foto']['name'])){
	$imvfoto = uniqid('img_').".".pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
	$uploadfile = $uploaddir . $imvfoto;

	if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadfile)) {
		$retorno = array('cod' => 1, 'src' => $imvfoto);
		echo json_encode($retorno);
		exit();
	} else {
		$retorno = array('cod' => 2);
		echo json_encode($retorno);
		exit();
	}
}
if(!empty($_FILES['pdf']['name'])){
	$imvpdf = uniqid('pdf_').".".pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION);
	$uploadfile = $uploaddir . $imvpdf;

	if (move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadfile)) {
		$retorno = array('cod' => 1, 'src' => $imvpdf, 'nome' => $_FILES['pdf']['name']);
		echo json_encode($retorno);
		exit();
	} else {
		$retorno = array('cod' => 2);
		echo json_encode($retorno);
		exit();
	}
}
if(!empty($_FILES['video']['name'])){
	$imvvideo = uniqid('vid_').".".pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
	$uploadfile = $uploaddir . $imvvideo;

	if (move_uploaded_file($_FILES['video']['tmp_name'], $uploadfile)) {
		$retorno = array('cod' => 1, 'src' => $imvvideo);
		echo json_encode($retorno);
		exit();
	} else {
		$retorno = array('cod' => 2);
		echo json_encode($retorno);
		exit();
	}
}

?>