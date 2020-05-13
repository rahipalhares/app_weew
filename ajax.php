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
$server = "http://srv252.teste.website/~weew/";
$ID = $_COOKIE['login'];
if(!$_COOKIE['login']){$ID2 = 1;}else{$ID2 = $_COOKIE['login'];}
$PRO = $_COOKIE['pro'];
if(!$_COOKIE['pro']){$PRO2 = 0;}else{$PRO2 = $_COOKIE['pro'];}


if($_POST["f"]=="login"){
	if(!empty($_POST["celularid"])){$USU_CelularID = $_POST["celularid"];}else{$USU_CelularID = "";}
	if(!empty($_POST["email"])){$USU_Email = $_POST["email"];}else{echo json_encode(array('cod' => 0, 'ide' => 'email'));exit();}
	if(!empty($_POST["senha"])){$USU_Senha = $_POST["senha"];}else{echo json_encode(array('cod' => 0, 'ide' => 'senha'));exit();}

	$sql = "SELECT * FROM usuarios WHERE USU_Email = '".$USU_Email."' AND USU_Senha = md5('".$USU_Senha."')";
	$tql = mysqli_query($serve, $sql);
	$total = mysqli_num_rows($tql);

	if ($total<=0){
		$retorno = array('cod' => 2, 'msg' => 'ERRO - Usuário ou senha incorretos', 'bug' => $serve);
		echo json_encode($retorno);
		exit();
	}else{
		if($USU_CelularID!=""){
			$sql3 = "SELECT USU_RowID FROM usuarios WHERE USU_CelularID = '".$USU_CelularID."'";
			$tql3 = mysqli_query($serve, $sql3);
			$uql3 = mysqli_fetch_array($tql3);
			$sql4 = "UPDATE usuarios SET USU_CelularID = '' WHERE USU_RowID = '".$uql3['USU_RowID']."'";
			$tql4 = mysqli_query($serve, $sql4);
			$uql = mysqli_fetch_array($tql);
			$USU_RowID = $uql['USU_RowID'];
			$login = md5(uniqid());
			$sql2 = "UPDATE usuarios SET USU_Login = '".$login."', USU_CelularID = '".$USU_CelularID."' WHERE USU_RowID = '".$USU_RowID."'";
			$tql2 = mysqli_query($serve, $sql2);
		}else{
			$uql = mysqli_fetch_array($tql);
			$USU_RowID = $uql['USU_RowID'];
			$login = md5(uniqid());
			$sql2 = "UPDATE usuarios SET USU_Login = '".$login."' WHERE USU_RowID = '".$USU_RowID."'";
			$tql2 = mysqli_query($serve, $sql2);
		}
		if($uql['USU_Tipo']===null){$tipo = 2;}
		$c1 = setcookie("loginhash", $login, time()+60*60*24*30, "/", "", false, false);
		$c2 = setcookie("login", $USU_RowID, time()+60*60*24*30, "/", "", false, false);
		$c3 = setcookie("pro", $tipo, time()+60*60*24*30, "/", "", false, false);

		if((strtotime($uql['USU_Validade']) - strtotime(date('Y-m-d')))/86400<0||$uql['USU_Status']==0||$uql['USU_Status']==1){$bloqueado='s';}else{$bloqueado='n';}

		if(!isset($_SESSION))
			session_start();

		$_SESSION['login'] = $USU_RowID; 	

		if($c1&&$c2&&$tql2){
			$retorno = array('cod' => 1, 'msg' => 'Logado com sucesso!', 'bloqueado' => $bloqueado);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2, 'msg' => 'ERRO - Não foi possível efetuar o login');
			echo json_encode($retorno);
			exit();
		}
	}
}

if($_POST["f"]=="session"){

	if(!empty($_POST["celularid"])){$USU_CelularID = $_POST["celularid"];}else{$retorno = array('codigo' => 0);echo json_encode($retorno);exit();}
	$sql = "SELECT * FROM usuarios WHERE USU_CelularID = '".$USU_CelularID."'";

	$tql = mysqli_query($serve, $sql);

	$total = mysqli_num_rows($tql);

	if ($total<=0){

		$retorno = array('codigo' => 0);

		echo json_encode($retorno);

		exit();

	}else{
		$uql = mysqli_fetch_array($tql);
		$USU_RowID = $uql['USU_RowID'];
		$login = md5(uniqid());
		$sql2 = "UPDATE usuarios SET USU_Login = '".$login."' WHERE USU_RowID = '".$USU_RowID."'";
		$tql2 = mysqli_query($serve, $sql2);
		$c1 = setcookie("loginhash", $login, time()+60*60*24*30, "/", "", 0, 0);
		$c2 = setcookie("login", $USU_RowID, time()+60*60*24*30, "/", "", 0, 0);
		if($uql['USU_ChaveFK']){
			$c3 = setcookie("tipo", 'm', time()+60*60*24*30, "/", "", 0, 0);
		}else{
			$c3 = setcookie("tipo", 'p', time()+60*60*24*30, "/", "", 0, 0);
		}

		if(!isset($_SESSION))
			session_start();

		$_SESSION['login'] = $USU_RowID; 	

		if($c1&&$c2&&$c3&&$tql2){
			$retorno = array('codigo' => 1);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('codigo' => 0);
			echo json_encode($retorno);
			exit();
		}
	}
}

if($_POST["f"]=="pro"){	
	if(!$PRO){$PRO = $PRO2;}
	$retorno = array('cod' => 1, 'pro' => $PRO);
	echo json_encode($retorno);
	exit();
}

if($_POST["f"]=="logout"){

	$sql = "UPDATE usuarios SET USU_CelularID = '' WHERE USU_RowID = '".$_COOKIE['login']."'";

	$tql = mysqli_query($serve, $sql);

	if($tql){

		setcookie("loginhash", "", time()-3600, "/", "", 0, 0);

		setcookie("login", "", time()-3600, "/", "", 0, 0);

		setcookie("tipo", "", time()-3600, "/", "", 0, 0);

		$retorno = array('cod' => 1);

		echo json_encode($retorno);

		exit();

	}
}

if($_POST["f"]=="cadastro"){
	if(!empty($_POST["celularid"])){$USU_CelularID = $_POST["celularid"];}else{$USU_CelularID = "";}
	if(!empty($_POST["USU_Nome"])){$USU_Nome = $_POST["USU_Nome"];}else{echo json_encode(array('cod' => 0, 'ide' => 'nome'));exit();}
	if(!empty($_POST["USU_Celular"])){$USU_Celular = $_POST["USU_Celular"];}else{echo json_encode(array('cod' => 0, 'ide' => 'celular'));exit();}
	if(!empty($_POST["USU_Email"])){$USU_Email = $_POST["USU_Email"];}else{echo json_encode(array('cod' => 0, 'ide' => 'email'));exit();}
	if(!empty($_POST["USU_Cep"])){$USU_Cep = $_POST["USU_Cep"];}else{echo json_encode(array('cod' => 0, 'ide' => 'cep'));exit();}
	if(!empty($_POST["USU_PaisOrigem"])){$USU_PaisOrigem = $_POST["USU_PaisOrigem"];}else{echo json_encode(array('cod' => 0, 'ide' => 'pais'));exit();}
	if(!empty($_POST["USU_Estado"])){$USU_Estado = strtoupper($_POST["USU_Estado"]);}else{echo json_encode(array('cod' => 0, 'ide' => 'estado'));exit();}
	if(!empty($_POST["USU_Cidade"])){$USU_Cidade = $_POST["USU_Cidade"];}else{echo json_encode(array('cod' => 0, 'ide' => 'cidade'));exit();}
	if(!empty($_POST["senha"])){$USU_Senha = $_POST["senha"];}else{echo json_encode(array('cod' => 0, 'ide' => 'senha'));exit();}
	if(!empty($_POST["senha1"])){$USU_Senha1 = $_POST["senha1"];}else{echo json_encode(array('cod' => 0, 'ide' => 'senha1'));exit();}

	$sqlC = "SELECT USU_Nome FROM usuarios WHERE USU_Email = '".$USU_Email."'";

	$tqlC = mysqli_query($serve,$sqlC);

	$totalC = mysqli_num_rows($tqlC);

	if ($totalC>0){
		$retorno = array('cod' => 2, 'msg' => 'Usuário já possui cadastro');
		echo json_encode($retorno);
		exit();
	}

	if($USU_Senha!=$USU_Senha1){
		$retorno = array('cod' => 2, 'msg' => 'As senhas inseridas são diferentes');
		echo json_encode($retorno);
		exit();
	}

	mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);

	$sqlC2 = "INSERT INTO usuarios (USU_Nome, USU_PaisOrigem, USU_Estado, USU_Cidade, USU_Celular, USU_Cep, USU_Email, USU_Status, USU_Senha) VALUES ('".$USU_Nome."', '".$USU_PaisOrigem."', '".$USU_Estado."', '".$USU_Cidade."', '".$USU_Celular."', '".$USU_Cep."', '".$USU_Email."', '1', md5('".$USU_Senha."'))";
	$tqlC2 = mysqli_query($serve,$sqlC2);
	if($tqlC2){
		mysqli_commit($serve);
		$sql = "SELECT * FROM usuarios WHERE USU_Email = '".$USU_Email."' AND USU_Senha = md5('".$USU_Senha."')";
		$tql = mysqli_query($serve, $sql);
		$total = mysqli_num_rows($tql);

		if ($total<=0){
			$retorno = array('cod' => 3, 'msg' => 'Cadastro efetuado com sucesso');
			echo json_encode($retorno);
			exit();
		}else{
			if($USU_CelularID!=""){
				$sql3 = "SELECT USU_RowID FROM usuarios WHERE USU_CelularID = '".$USU_CelularID."'";
				$tql3 = mysqli_query($serve, $sql3);
				$uql3 = mysqli_fetch_array($tql3);
				$sql4 = "UPDATE usuarios SET USU_CelularID = '' WHERE USU_RowID = '".$uql3['USU_RowID']."'";
				$tql4 = mysqli_query($serve, $sql4);
				$uql = mysqli_fetch_array($tql);
				$USU_RowID = $uql['USU_RowID'];
				$login = md5(uniqid());
				$sql2 = "UPDATE usuarios SET USU_Login = '".$login."', USU_CelularID = '".$USU_CelularID."' WHERE USU_RowID = '".$USU_RowID."'";
				$tql2 = mysqli_query($serve, $sql2);
			}else{
				$uql = mysqli_fetch_array($tql);
				$USU_RowID = $uql['USU_RowID'];
				$login = md5(uniqid());
				$sql2 = "UPDATE usuarios SET USU_Login = '".$login."' WHERE USU_RowID = '".$USU_RowID."'";
				$tql2 = mysqli_query($serve, $sql2);
			}
			if($uql['USU_Tipo']===null){$tipo = 2;}
			$c1 = setcookie("loginhash", $login, time()+60*60*24*30, "/", "", false, false);
			$c2 = setcookie("login", $USU_RowID, time()+60*60*24*30, "/", "", false, false);
			$c3 = setcookie("pro", $tipo, time()+60*60*24*30, "/", "", false, false);

			if((strtotime($uql['USU_Validade']) - strtotime(date('Y-m-d')))/86400<0||$uql['USU_Status']==0||$uql['USU_Status']==1){$bloqueado='s';}else{$bloqueado='n';}

			if(!isset($_SESSION))
				session_start();

			$_SESSION['login'] = $USU_RowID; 	

			if($c1&&$c2&&$tql2){
				$retorno = array('cod' => 1, 'bloqueado' => $bloqueado);
				echo json_encode($retorno);
				exit();
			}else{
				$retorno = array('cod' => 3, 'msg' => 'Cadastro efetuado com sucesso');
				echo json_encode($retorno);
				exit();
			}
		}
	}else{
		mysqli_rollback($serve);
		$retorno = array('cod' => 2, 'msg' => 'Não foi possível cadastrar agora');
		echo json_encode($retorno);
		exit();
	}
}

if($_POST["f"]=="cadastrofisica"){
	if(!$ID){$ID = $ID2;}
	if(!empty($_POST["cpf"])){$FIS_Cpf = $_POST["cpf"];}else{echo json_encode(array('cod' => 0, 'ide' => 'cpf'));exit();}
	if(!empty($_POST["nascimento"])){
		$data = explode("/", $_POST["nascimento"]);
		$FIS_Nascimento = $data[2]."-".$data[1]."-".$data[0];
	}else{echo json_encode(array('cod' => 0, 'ide' => 'nascimento'));exit();}
	if(!empty($_POST["escolaridade"])){$FIS_Escolaridade = "'".$_POST["escolaridade"]."'";}else{$FIS_Escolaridade = "null";}
	if(!empty($_POST["profissao"])){$FIS_Profissao = "'".$_POST["profissao"]."'";}else{$FIS_Profissao = "null";}
	if(!empty($_POST["empresa"])){$FIS_Empresa = "'".$_POST["empresa"]."'";}else{$FIS_Empresa = "null";}
	if(!empty($_POST["cargo"])){$FIS_Cargo = "'".$_POST["cargo"]."'";}else{$FIS_Cargo = "null";}
	if(!empty($_POST["interesse"])){$FIS_Interesse = "'".$_POST["interesse"]."'";}else{$FIS_Interesse = "null";}

	mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);

	$sql = "INSERT INTO fisica (FIS_Cpf, FIS_Nascimento, FIS_Escolaridade, FIS_Profissao, FIS_Empresa, FIS_Cargo, FIS_Interesse) VALUES ('".$FIS_Cpf."', '".$FIS_Nascimento."', ".$FIS_Escolaridade.", ".$FIS_Profissao.", ".$FIS_Empresa.", ".$FIS_Cargo.", ".$FIS_Interesse.")";
	$tql = mysqli_query($serve,$sql);
	$last_insert_id = mysqli_insert_id($serve);
	$sql2 = "UPDATE usuarios SET USU_Tipo = 0, USU_DetalhesID = '".$last_insert_id."' WHERE USU_RowID = '".$ID."'";
	$tql2 = mysqli_query($serve, $sql2);
	if($tql&&$tql2){
		mysqli_commit($serve);
		$retorno = array('cod' => 1, 'msg' => 'Cadastro efetuado com sucesso!');
		echo json_encode($retorno);
		exit();
	}else{
		mysqli_rollback($serve);
		$retorno = array('cod' => 2, 'msg' => 'ERRO - Não foi possível cadastrar agora');
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="cadastrojuridica"){
	if(!$ID){$ID = $ID2;}
	if(!empty($_POST["cnpj"])){$JUR_Cnpj = $_POST["cnpj"];}else{echo json_encode(array('cod' => 0, 'ide' => 'cnpj'));exit();}
	if(!empty($_POST["atuacao"])){$JUR_Atuacao = "'".$_POST["atuacao"]."'";}else{$JUR_Atuacao = "null";}

	mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);

	$sql = "INSERT INTO juridica (JUR_Cnpj, JUR_Atuacao) VALUES ('".$JUR_Cnpj."', ".$JUR_Atuacao.")";
	$tql = mysqli_query($serve,$sql);
	$last_insert_id = mysqli_insert_id($serve);
	$sql2 = "UPDATE usuarios SET USU_Tipo = 1, USU_DetalhesID = '".$last_insert_id."' WHERE USU_RowID = '".$ID."'";
	$tql2 = mysqli_query($serve, $sql2);
	if($tql&&$tql2){
		mysqli_commit($serve);
		$retorno = array('cod' => 1, 'msg' => 'Cadastro efetuado com sucesso!');
		echo json_encode($retorno);
		exit();
	}else{
		mysqli_rollback($serve);
		$retorno = array('cod' => 2, 'msg' => 'ERRO - Não foi possível cadastrar agora');
		echo json_encode($retorno);
		exit();
	}
}

/* Estados */
if($_POST["f"]=="estados"){
	$sql = "SELECT EST_RowID, EST_Sigla FROM estados ORDER BY EST_RowID";
	$tql = mysqli_query($serve,$sql);
	$options = "<option value='' disabled selected>Estado</option>";
	while($uql = mysqli_fetch_array($tql)){
		$options .= "<option value='".$uql["EST_RowID"].";".$uql["EST_Sigla"]."'>".$uql["EST_Sigla"]."</option>";
	}
	if($tql){
		$retorno = array('cod' => 1, 'html' => $options);
		echo json_encode($retorno);
		exit();
	}else{
		$retorno = array('cod' => 2);
		echo json_encode($retorno);
		exit();
	}
}
/* Cidades */
if($_POST["f"]=="cidades"){
	$estado = $_POST["estado"];
	$sql = "SELECT CID_Cidade FROM cidades WHERE CID_EstadoID = '".$estado."' ORDER BY CID_RowID";
	$tql = mysqli_query($serve,$sql);
	$options = "<option value=\"\" disabled selected>Cidade</option>";
	while($uql = mysqli_fetch_array($tql)){
		$options .= "<option value=\"".$uql["CID_Cidade"]."\">".$uql["CID_Cidade"]."</option>";
	}
	if($tql){
		$retorno = array('cod' => 1, 'html' => $options);
		echo json_encode($retorno);
		exit();
	}else{
		$retorno = array('cod' => 2);
		echo json_encode($retorno);
		exit();
	}
}

/* Inicio */
if($_POST["f"]=="fotoperfil"){
	if(!$ID){$ID = $ID2;}
	$sql = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_UsuID = '".$ID."'";
	$tql = mysqli_query($serve,$sql);
	$uql = mysqli_fetch_array($tql);
	if(!$uql["POS_Arquivo"]){
		$foto = $server."upload/avatar.jpg";
	}else{
		$foto = $server."upload/".$uql["POS_Arquivo"];
	}
	if($tql){
		$retorno = array('cod' => 1, 'foto' => $foto);
		echo json_encode($retorno);
		exit();
	}else{
		$retorno = array('cod' => 2);
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="inicio"){
	$ID = $_POST["usuario"];
	if(!$ID){$ID = $ID2;}
	$sqlnome = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$ID."'";
	$tqlnome = mysqli_query($serve,$sqlnome);
	$uqlnome = mysqli_fetch_array($tqlnome);
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$html = "";
	$idjavascript = array();
		/* Verifica o número total de posts seguidos pelo perfil logado */
		$sqlN = "SELECT COUNT(*) N FROM (
			SELECT B.POS_RowID FROM
			acao A, posts B, acao C WHERE
			C.ACA_UsuID = '".$ID."' AND C.ACA_Acao = 'Seguir' AND
			C.ACA_PosID = B.POS_RowID AND (B.POS_Tipo = 'Forum' OR B.POS_Tipo = 'Evento' OR B.POS_Tipo = 'Canal') AND
			B.POS_RowID = A.ACA_PosPaiID AND A.ACA_Acao = 'Comentar'
			UNION
			SELECT B.POS_RowID FROM
			acao A, posts B, acao C WHERE
			C.ACA_UsuID = '".$ID."' AND C.ACA_Acao = 'Seguir' AND
			C.ACA_Usu2ID = A.ACA_UsuID AND (A.ACA_Acao = 'Postar' OR A.ACA_Acao = 'Comentar' OR A.ACA_Acao = 'Curtir' OR A.ACA_Acao = 'Compartilhar') AND
			A.ACA_PosID = B.POS_RowID AND (B.POS_Tipo = 'Feed' OR B.POS_Tipo = 'Forum' OR B.POS_Tipo = 'Evento' OR B.POS_Tipo = 'Canal')
		) x";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		/* Pega o ID dos foruns seguidos pelo perfil logado que deverão aparecer na página de consulta solicitada */
		$sql = "SELECT B.POS_RowID, A.ACA_Data dta, 'x' quem, A.ACA_Acao oque, B.POS_Tipo tipo FROM
		acao A, posts B, acao C WHERE
		C.ACA_UsuID = '".$ID."' AND C.ACA_Acao = 'Seguir' AND
		C.ACA_PosID = B.POS_RowID AND (B.POS_Tipo = 'Forum' OR B.POS_Tipo = 'Evento' OR B.POS_Tipo = 'Canal') AND
		B.POS_RowID = A.ACA_PosPaiID AND (A.ACA_Acao = 'Comentar' OR A.ACA_Acao = 'Postar')
		UNION
		SELECT B.POS_RowID, A.ACA_Data dta, A.ACA_UsuID quem, A.ACA_Acao oque, B.POS_Tipo tipo FROM
		acao A, posts B, acao C WHERE
		C.ACA_UsuID = '".$ID."' AND C.ACA_Acao = 'Seguir' AND
		C.ACA_Usu2ID = A.ACA_UsuID AND (A.ACA_Acao = 'Postar' OR A.ACA_Acao = 'Comentar' OR A.ACA_Acao = 'Curtir' OR A.ACA_Acao = 'Compartilhar') AND
		A.ACA_PosID = B.POS_RowID AND (B.POS_Tipo = 'Feed' OR B.POS_Tipo = 'Forum' OR B.POS_Tipo = 'Evento' OR B.POS_Tipo = 'Canal')
		ORDER BY dta DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	if ($total>0){
		/* Percorre um por um a lista das pessoas seguidas pelo perfil logado escolhidas para exibição */
		while($uql = mysqli_fetch_array($tql)){
			$idpost = $uql["POS_RowID"];
			$quem = $uql["quem"];
			$oque = $uql["oque"];
			$tipo = $uql["tipo"];
			if($quem=="x"){
				if($oque=="Comentar"){$oque="comentário";}
				if($oque=="Postar"&&$tipo=="Canal"){$oque="podcast";}
				if($tipo=="Forum"){$tipo="fórum";}
				$titulo = "Novo ".$oque." em um ".strtolower($tipo)." que você segue";
				$denunciar = "";
			}else{
				if($oque=="Postar"){$oque=" criou um novo ";}
				if($oque=="Comentar"){$oque=" comentou um ";}
				if($oque=="Curtir"){$oque=" curtiu um ";}
				if($oque=="Compartilhar"){$oque=" compartilhou um ";}
				if($tipo=="Feed"){$tipo="post";}
				if($tipo=="Forum"){$tipo="fórum";}
				$sqlnomepost = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$quem."'";
				$tqlnomepost = mysqli_query($serve,$sqlnomepost);
				$uqlnomepost = mysqli_fetch_array($tqlnomepost);
				$nome = $uqlnomepost["USU_Nome"];
				$sqlfoto = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_RowID = '".$quem."'";
				$tqlfoto = mysqli_query($serve,$sqlfoto);
				$uqlfoto = mysqli_fetch_array($tqlfoto);
				if(!$uqlfoto["POS_Arquivo"]){
					$fotourl = $server."upload/avatar.jpg";
				}else{
					$fotourl = $server."upload/".$uqlfoto["POS_Arquivo"];
				}
				$fotoperfil = "<img src=\"".$fotourl."\" class=\"brs-50 mxh-46 mr-10\" height=\"46\" width=\"46\"/>";
				$titulo = $fotoperfil.$nome.$oque.strtolower($tipo);
				$denunciar = "<img class=\"f-right h-18\" src=\"imagens/svg/interface.svg\" onclick=\"modalInput('denunciar','Descreva o motivo de sua denúncia','".$idpost."','','p','s');\">";
			}
			/* Pega os dados do post seguido percorrido */
			$sql2 = "SELECT POS_Arquivo, POS_Titulo, POS_Resumo, POS_Descricao FROM posts WHERE POS_RowID = '".$idpost."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			$pagina = "";
			$resumo = "<div class=\"fs-07 c4\">".$uql2["POS_Resumo"]."</div>";
			if($uql["tipo"]=="Feed"){
				$pagina = "post";
				$resumo = "<div class=\"fs-08\">".$uql2["POS_Descricao"]."</div>";
			}
			if($uql["tipo"]=="Forum"){$pagina = "forum";}
			if($uql["tipo"]=="Evento"){
				$pagina = "evento";
				$dados = explode(";", $uql2["POS_Resumo"]);
				$data = explode(":", $dados[0]);
				$local = explode(":", $dados[1]);
				$ruacidade = explode(" / ", $local[1]);
				$resumo = "<div class=\"fs-07 c4\">".$ruacidade[1]."</div>
	                        <div class=\"fs-08 c4\">".$data[1]."</div>";
			}
			if($uql["tipo"]=="Canal"){$pagina = "podcast";$denunciar = "";}

			$link = "onclick=\"window.location = '".$pagina.".html?id=".$idpost."';\"";
			if(!$uql2["POS_Arquivo"]){
				/*if($uql["tipo"]=="Forum"){$fotopadrao = "forum.jpg";}
				if($uql["tipo"]=="Evento"){$fotopadrao = "evento.jpg";}
				if($uql["tipo"]=="Canal"){$fotopadrao = "podcast.jpg";}
				$foto = $server."upload/".$fotopadrao;
				$arquivo = "<img src=\"".$foto."\" class=\"mxw-100p\">";*/
				$arquivo = "";
			}else{
				$foto = $server."upload/".$uql2["POS_Arquivo"];
				if(substr($uql2["POS_Arquivo"], -3)=='pdf'){
					/*if($uql["tipo"]=="Forum"){$fotopadrao = "forum.jpg";}
					if($uql["tipo"]=="Evento"){$fotopadrao = "evento.jpg";}
					if($uql["tipo"]=="Canal"){$fotopadrao = "podcast.jpg";}
					$foto = $server."upload/".$fotopadrao;
					$arquivo = "<img src=\"".$foto."\" class=\"mxw-100p\">";*/
					$arquivo = "";
				}else if(substr($uql2["POS_Arquivo"], -3)=='mp4'){
					$arquivo = "<video height=\"auto\" width=\"auto\" class=\"mxw-100p\" controls><source src=\"".$foto."#t=0.00001\" type=\"video/mp4\"></video>";
					$link = "";
				}else{
					$arquivo = "<img src=\"".$foto."\" class=\"mxw-100p\">";
				}				
			}
			/* Pega a data do último comentário */
			$sql3 = "SELECT ACA_Data FROM acao WHERE ACA_PosPaiID = '".$idpost."' ORDER BY ACA_Data DESC LIMIT 1";
			$tql3 = mysqli_query($serve,$sql3);
			$Ncomentarios = mysqli_num_rows($tql3);
			$atualizado = "";
			if($Ncomentarios>0){
				$uql3 = mysqli_fetch_array($tql3);
				$data = date_format(date_create($uql3["ACA_Data"]), 'd/m H:i');
				$atualizado = "<div class=\"fs-05 c4\">Atualizado em ".$data."</div>";
			}
			$sql4 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Curtir' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
			$tql4 = mysqli_query($serve,$sql4);
			$Ncurtir = mysqli_num_rows($tql4);
			if($Ncurtir>0){
				$cclasse = "c2";
				$cicone = "imagens/svg/awesome-heart2.svg";
				$ctexto = "Descurtir";
			}else{
				$cclasse = "c3";
				$cicone = "imagens/svg/awesome-heart.svg";
				$ctexto = "Curtir";
			}
			$sql5 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Compartilhar' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
			$tql5 = mysqli_query($serve,$sql5);
			$Ncompartilhar = mysqli_num_rows($tql5);
			if($Ncompartilhar>0){
				$coclasse = "c2";
				$coicone = "imagens/svg/awesome-share-square3.svg";
				$cotexto = "Compartilhado";
			}else{
				$coclasse = "c3";
				$coicone = "imagens/svg/awesome-share-square.svg";
				$cotexto = "Compartilhar";
			}
			$sql6 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Salvar' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
			$tql6 = mysqli_query($serve,$sql6);
			$Nsalvar = mysqli_num_rows($tql6);
			if($Nsalvar>0){
				$sclasse = "c2";
				$sicone = "imagens/svg/metro-floppy-disk2.svg";
				$stexto = "Descartar";
			}else{
				$sclasse = "c3";
				$sicone = "imagens/svg/metro-floppy-disk.svg";
				$stexto = "Salvar";
			}
			$titulopost = $uql2["POS_Titulo"]?"<div class=\"fs-08 mt-10\">".$uql2["POS_Titulo"]."</div>":"";
			/* Monta o bloco de informações de exibição do tópico percorrido */
			$html .= "<div name=\"".$idpost."\" class=\"card\">
						<div class=\"d-flex flex-column no-overflow\">
	                        <div>".$titulo.$denunciar."</div>
	                        <div ".$link.">".$titulopost."</div>
	                        <div ".$link.">".$arquivo."</div>
	                        <div ".$link.">".$resumo."</div>
	                        ".$atualizado."
	                    </div>
	                    <div class=\"d-flex flex-h mt-10\">
	                        <div class=\"textcenter ".$cclasse." fs-06 br\" onclick=\"curtir(this,".$idpost.")\">
	                        	<img class=\"mr-5px h-18\" src=\"".$cicone."\"/><span>".$ctexto."</span></div>
	                        <div class=\"textcenter c3 fs-06 br\">
	                        	<img class=\"mr-5px h-18\" src=\"imagens/svg/material-chat.svg\"/>Comentar</div>
	                        <div class=\"textcenter ".$coclasse." fs-06 br\" onclick=\"compartilhar(".$idpost.")\">
	                        	<img class=\"mr-5px h-18\" src=\"".$coicone."\"/><span>".$cotexto."</span></div>
	                        <div class=\"textcenter ".$sclasse." fs-06\" onclick=\"salvar(this,".$idpost.")\">
	                        	<img class=\"mr-5px h-18\" src=\"".$sicone."\"/><span>".$stexto."</span></div>
	                    </div>
	                </div>";
	        array_push($idjavascript,$idpost);
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'html' => $html, 'proxima' => $proxima, 'numero' => $Ntotal, 'nome' =>  $uqlnome["USU_Nome"], 'idjavascript' => $idjavascript);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhum feed disponível</div>", 'numero' => 0, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}
if($_POST["f"]=="novopost"){
	if(!$ID){$ID = $ID2;}
	$POS_Arquivo = $_POST["arquivo"]?"'".$_POST["arquivo"]."'":'null';
	if(!empty($_POST["titulo"])){$POS_Titulo = "'".$_POST["titulo"]."'";}else if($POS_Arquivo!='null'){echo json_encode(array('cod' => 0, 'ide' => 'titulo'));exit();}else{$POS_Titulo = 'null';}
	if(!empty($_POST["descricao"])){$POS_Descricao = $_POST["descricao"];}else{echo json_encode(array('cod' => 0, 'ide' => 'descricao'));exit();}
	mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);
	$sql = "INSERT INTO posts (POS_Tipo, POS_UsuID, POS_Titulo, POS_Descricao, POS_Arquivo) VALUES ('Feed', '".$ID."', ".$POS_Titulo.", '".$POS_Descricao."', ".$POS_Arquivo.")";
	$tql = mysqli_query($serve,$sql);
	$last_insert_id = mysqli_insert_id($serve);
	$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_PosID) VALUES ('Postar', '".$ID."', '".$last_insert_id."')";
	$tql2 = mysqli_query($serve,$sql2);
	if($tql&&$tql2){
		mysqli_commit($serve);
		$retorno = array('cod' => 1, 'id' => $last_insert_id);
		echo json_encode($retorno);
		exit();
	}else{
		mysqli_rollback($serve);
		$retorno = array('cod' => 2, 'msg' => 'ERRO - Não foi possível publicar agora');
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="post"){
	if(!$ID){$ID = $ID2;}
	$idpost = $_POST["id"];
	/* Pega os dados do post seguido percorrido */
	$sql2 = "SELECT POS_UsuID, POS_Arquivo, POS_Titulo, POS_Resumo, POS_Descricao FROM posts WHERE POS_RowID = '".$idpost."'";
	$tql2 = mysqli_query($serve,$sql2);
	$uql2 = mysqli_fetch_array($tql2);
	$sqlnomepost = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$uql2["POS_UsuID"]."'";
	$tqlnomepost = mysqli_query($serve,$sqlnomepost);
	$uqlnomepost = mysqli_fetch_array($tqlnomepost);
	$nome = $uqlnomepost["USU_Nome"];
	$sqlfoto = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_RowID = '".$uql2["POS_UsuID"]."'";
	$tqlfoto = mysqli_query($serve,$sqlfoto);
	$uqlfoto = mysqli_fetch_array($tqlfoto);
	if(!$uqlfoto["POS_Arquivo"]){
		$fotourl = $server."upload/avatar.jpg";
	}else{
		$fotourl = $server."upload/".$uqlfoto["POS_Arquivo"];
	}
	$fotoperfil = "<img src=\"".$fotourl."\" class=\"brs-50 mxh-46 mr-10\" height=\"46\" width=\"46\"/>";
	$denunciar = "<img class=\"f-right h-18\" src=\"imagens/svg/interface.svg\" onclick=\"modalInput('denunciar','Descreva o motivo de sua denúncia','".$idpost."','','p','s');\">";
	$titulo = $fotoperfil.$nome.$denunciar;
	$html = "";
	$resumo = "<div class=\"fs-08\">".$uql2["POS_Descricao"]."</div>";		
	if(!$uql2["POS_Arquivo"]){
		$arquivo = "";
	}else{
		$foto = $server."upload/".$uql2["POS_Arquivo"];
		if(substr($uql2["POS_Arquivo"], -3)=='pdf'){
			$arquivo = "<a href=\"".$foto."\" target=\"_blank\" download><div class=\"pdf b\"><img src='imagens/svg/ionic-md-document.svg'/><br><span class='c4 fs-06'>PDF</span></div></a>";
		}else if(substr($uql2["POS_Arquivo"], -3)=='mp4'){
			$arquivo = "<video height=\"auto\" width=\"auto\" class=\"mxw-100p\" controls><source src=\"".$foto."#t=0.00001\" type=\"video/mp4\"></video>";
		}else{
			$arquivo = "<img src=\"".$foto."\" class=\"mxw-100p\">";
		}				
	}
	$sql4 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Curtir' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
	$tql4 = mysqli_query($serve,$sql4);
	$Ncurtir = mysqli_num_rows($tql4);
	if($Ncurtir>0){
		$cclasse = "c2";
		$cicone = "imagens/svg/awesome-heart2.svg";
		$ctexto = "Descurtir";
	}else{
		$cclasse = "c3";
		$cicone = "imagens/svg/awesome-heart.svg";
		$ctexto = "Curtir";
	}
	$sql5 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Compartilhar' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
	$tql5 = mysqli_query($serve,$sql5);
	$Ncompartilhar = mysqli_num_rows($tql5);
	if($Ncompartilhar>0){
		$coclasse = "c2";
		$coicone = "imagens/svg/awesome-share-square3.svg";
		$cotexto = "Compartilhado";
	}else{
		$coclasse = "c3";
		$coicone = "imagens/svg/awesome-share-square.svg";
		$cotexto = "Compartilhar";
	}
	$sql6 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Salvar' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
	$tql6 = mysqli_query($serve,$sql6);
	$Nsalvar = mysqli_num_rows($tql6);
	if($Nsalvar>0){
		$sclasse = "c2";
		$sicone = "imagens/svg/metro-floppy-disk2.svg";
		$stexto = "Descartar";
	}else{
		$sclasse = "c3";
		$sicone = "imagens/svg/metro-floppy-disk.svg";
		$stexto = "Salvar";
	}
	$titulopost = $uql2["POS_Titulo"]?"<div class=\"fs-08 mt-10\">".$uql2["POS_Titulo"]."</div>":"";
	/* Monta o bloco de informações de exibição do tópico percorrido */
	$html .= "<div name=\"".$idpost."\">
				<div class=\"d-flex flex-column no-overflow\">
                    <div>".$titulo."</div>
                    <div class=\"mt-10\">".$titulopost."</div>
                    <div>".$arquivo."</div>
                    <div>".$resumo."</div>
                </div>
                <div class=\"d-flex flex-h mt-10\">
                    <div class=\"textcenter ".$cclasse." fs-06 br\" onclick=\"curtir(this,".$idpost.")\">
                    	<img class=\"mr-5px h-18\" src=\"".$cicone."\"/><span>".$ctexto."</span></div>
                    <div class=\"textcenter c3 fs-06 br\">
                    	<img class=\"mr-5px h-18\" src=\"imagens/svg/material-chat.svg\"/>Comentar</div>
                    <div class=\"textcenter ".$coclasse." fs-06 br\" onclick=\"compartilhar(".$idpost.")\">
                    	<img class=\"mr-5px h-18\" src=\"".$coicone."\"/><span>".$cotexto."</span></div>
                    <div class=\"textcenter ".$sclasse." fs-06\" onclick=\"salvar(this,".$idpost.")\">
                    	<img class=\"mr-5px h-18\" src=\"".$sicone."\"/><span>".$stexto."</span></div>
                </div>
            </div>";
	if($tql2&&$tqlnomepost&&$tqlfoto&&$tql4&&$tql5&&$tql6){
		$retorno = array('cod' => 1, 'html' => $html);
		echo json_encode($retorno);
		exit();
	}else{
		$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Post não encontrado</div>");
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="comentarios"){
	if(!$ID){$ID = $ID2;}
	$idpost = $_POST["id"];
	$msg = $_POST["idmsg"];
	if(!$msg){$msg = "";}else{$msg = "AND A.ACA_RowID > '".$msg."'";}
	$html = "";
	$nfiltros = 0;
	if($_POST["foto"]>0){
		$imagem = "(RIGHT (B.POS_Arquivo, 3) <> 'pdf' AND RIGHT (B.POS_Arquivo, 3) <> 'mp4')";
		$nfiltros++;
	}else{$imagem="";}
	if($_POST["pdf"]>0){
		$pdfor = $nfiltros>0?" OR ":"";
		$pdf = $pdfor."RIGHT (B.POS_Arquivo, 3) = 'pdf'";
		$nfiltros++;
	}else{$pdf="";}
	if($_POST["video"]>0){
		$videoor = $nfiltros>0?" OR ":"";
		$video = $videoor."RIGHT (B.POS_Arquivo, 3) = 'mp4'";
		$nfiltros++;
	}else{$video="";}
	if($nfiltros==0){
		$imagem = "(RIGHT (B.POS_Arquivo, 3) <> 'pdf' AND RIGHT (B.POS_Arquivo, 3) <> 'mp4')";
		$pdf = " OR RIGHT (B.POS_Arquivo, 3) = 'pdf'";
		$video = " OR RIGHT (B.POS_Arquivo, 3) = 'mp4'";
		$texto = " OR B.POS_Arquivo is null";
	}else{
		$texto="";
	}
	$sqlO = "SELECT A.ACA_RowID, A.ACA_UsuID FROM acao A, posts B WHERE A.ACA_PosPaiID = '".$idpost."' AND A.ACA_PosID = B.POS_RowID AND (".$imagem.$pdf.$video.$texto.") AND A.ACA_Acao = 'Comentar' ".$msg." ORDER BY A.ACA_RowID ASC";
	$tqlO = mysqli_query($serve,$sqlO);
	$total = mysqli_num_rows($tqlO);
	if ($total>0){
		while($uqlO = mysqli_fetch_array($tqlO)){
			$idconversa = $uqlO["ACA_RowID"];
			$idremetente = $uqlO["ACA_UsuID"];
			/* Pega o nome do usuario */
			$sql2 = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$idremetente."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			/* Pega a foto de perfil do usuario */
			$sql3 = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_UsuID = '".$idremetente."'";
			$tql3 = mysqli_query($serve,$sql3);
			$uql3 = mysqli_fetch_array($tql3);
			/* Pega o conteudo da última mensagem */
			$sql4 = "SELECT B.POS_Titulo, B.POS_Descricao, B.POS_Arquivo, B.POS_Data FROM acao A, posts B WHERE B.POS_RowID = A.ACA_PosID AND A.ACA_RowID = '".$idconversa."'";
			$tql4 = mysqli_query($serve,$sql4);
			$uql4 = mysqli_fetch_array($tql4);
			if(!$uql3["POS_Arquivo"]){
				$foto = $server."upload/avatar.jpg";
			}else{
				$foto = $server."upload/".$uql3["POS_Arquivo"];
			}	
			if(!$uql4["POS_Arquivo"]){
				$comentario = "<div class=\"fs-08 c4\">".$uql4["POS_Descricao"]."</div>";
				$arquivo = "";
			}else{
				$foto2 = $server."upload/".$uql4["POS_Arquivo"];
				$titulo = "<div class=\"fs-06 c4\">".$uql4["POS_Titulo"]."</div>";
				if(substr($uql4["POS_Arquivo"], -3)=='pdf'){
					$arquivo = "<a href=\"".$foto2."\" target=\"_blank\" download><div class=\"pdf b\"><img src='imagens/svg/ionic-md-document.svg'/><br><span class='c4 fs-06'>PDF</span></div></a>";
				}else if(substr($uql4["POS_Arquivo"], -3)=='mp4'){
					$arquivo = "<video height=\"auto\" width=\"auto\" class=\"mxw-100p\" controls><source src=\"".$foto2."#t=0.00001\" type=\"video/mp4\"></video>";
				}else{
					$arquivo = "<img src=\"".$foto2."\" class=\"mxw-100p\">";
				}		
				$comentario = $titulo.$arquivo;		
			}		
			$date = date_create($uql4["POS_Data"]);
			$data = date_format($date, 'd/m H:i');
			/* Monta o bloco de informações de exibição da mensagem percorrida */
			if($idremetente!=$ID){
				$html .= "<div>
							<div class=\"d-flex flex-h pt-10\">
		                        <div class=\"mr-10 w-40px\"><img src=\"".$foto."\" class=\"brs-50 mxh-40\" height=\"40\" width=\"40\"></div>
		                        <div class=\"flex-column mxw-chat\">
		                        	<div class=\"h-11\">".$uql2["USU_Nome"]."</div>
		                        	<div class=\"fs-06 c4\">".$data."</div>
		                        	".$comentario."
		                        </div>
		                    </div>
		                </div>";
			}else{
				$html .= "<div class=\"minhamsg\">
							<div class=\"d-flex flex-h pt-10\">
		                        <div class=\"mr-10 w-40px\"><img src=\"".$foto."\" class=\"brs-50 mxh-40\" height=\"40\" width=\"40\"></div>
		                        <div class=\"flex-column mxw-chat\">
		                        	<div class=\"h-11\">".$uql2["USU_Nome"]."</div>
		                        	<div class=\"fs-06 c4\">".$data."</div>
		                        	".$comentario."
		                        </div>
		                    </div>
		                </div>";				
			}
		}
		if($tqlO&&$tql2&&$tql3&&$tql4){
			$retorno = array('cod' => 1, 'html' => $html, 'ultima' => $idconversa, 'numero' => $Ntotal);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($total==0&&!$msg){		
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Inicie os comentários</div>", 'numero' => 0, 'sql' => $sqlO);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}	
}
if($_POST["f"]=="enviarcomentario"){
	if(!$ID){$ID = $ID2;}
	$idpost = $_POST["id"];
	$msg = "'".$_POST["mensagem"]."'";
	$titulo = "'".$_POST["titulo"]."'";
	$arquivo = "'".$_POST["arquivo"]."'";
	if($titulo!="''"&&$arquivo!="''"){
		$msg = "null";
	}else{
		$titulo = "null";
		$arquivo = "null";
	}
	mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);
	$sql = "INSERT INTO posts (POS_Tipo, POS_UsuID, POS_Descricao, POS_Titulo, POS_Arquivo) VALUES ('Mensagem', '".$ID."', ".$msg.", ".$titulo.", ".$arquivo.")";
	$tql = mysqli_query($serve,$sql);
	$last_insert_id = mysqli_insert_id($serve);
	$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_PosPaiID, ACA_PosID) VALUES ('Comentar', '".$ID."', '".$idpost."', '".$last_insert_id."')";
	$tql2 = mysqli_query($serve,$sql2);
	if($tql&&$tql2){
		mysqli_commit($serve);
		$retorno = array('cod' => 1, 'id' => $REG_RowID);
		echo json_encode($retorno);
		exit();
	}else{
		mysqli_rollback($serve);
		$retorno = array('cod' => 2, 'msg' => 'ERRO - Não foi possível comentar agora');
		echo json_encode($retorno);
		exit();
	}
}

/* Busca */
if($_POST["f"]=="busca"){
	if(!$ID){$ID = $ID2;}
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$html = "";
	$pesquisa = $_POST["pesquisa"];
	$pesquisahtml = "";
	$nfiltros = 0;
	$nfiltros2 = 0;
	if($_POST["pessoas"]>0){
		$pessoa = "A.USU_Tipo = '0' OR A.USU_Tipo is null";
		$nfiltros2++;
	}else{$pessoa="";}
	if($_POST["empresas"]>0){
		$empresaor = $nfiltros2>0?" OR ":"";
		$empresa = $empresaor."A.USU_Tipo = '1'";
		$nfiltros2++;
	}else{$empresa="";}
	if($_POST["foto"]>0){
		$imagem = "(RIGHT (B.POS_Arquivo, 3) <> 'pdf' AND RIGHT (B.POS_Arquivo, 3) <> 'mp4')";
		$nfiltros++;
	}else{$imagem="";}
	if($_POST["pdf"]>0){
		$pdfor = $nfiltros>0?" OR ":"";
		$pdf = $pdfor."RIGHT (B.POS_Arquivo, 3) = 'pdf'";
		$nfiltros++;
	}else{$pdf="";}
	if($_POST["video"]>0){
		$videoor = $nfiltros>0?" OR ":"";
		$video = $videoor."RIGHT (B.POS_Arquivo, 3) = 'mp4'";
		$nfiltros++;
	}else{$video="";}
	/*if($_POST["posts"]>0){
		$feed = "B.POS_Tipo = 'Feed'";
		$nfiltros++;
	}else{$feed="";}
	if($_POST["foruns"]>0){
		$forumor = $nfiltros>0?" OR ":"";
		$forum = $forumor."B.POS_Tipo = 'Forum'";
		$nfiltros++;
	}else{$forum="";}
	if($_POST["eventos"]>0){
		$eventoor = $nfiltros>0?" OR ":"";
		$evento = $eventoor."B.POS_Tipo = 'Evento'";
		$nfiltros++;
	}else{$evento="";}
	if($_POST["podcasts"]>0){
		$podcastor = $nfiltros>0?" OR ":"";
		$podcast = $podcastor."B.POS_Tipo = 'Canal' OR B.POS_Tipo = 'Podcast'";
		$nfiltros++;
	}else{$podcast="";}*/
	if($nfiltros==0&&$nfiltros2==0){
		$pessoa = "A.USU_Tipo = '0' OR A.USU_Tipo is null";
		$empresa = " OR A.USU_Tipo = '1'";
		$imagem = "(RIGHT (B.POS_Arquivo, 3) <> 'pdf' AND RIGHT (B.POS_Arquivo, 3) <> 'mp4')";
		$pdf = " OR RIGHT (B.POS_Arquivo, 3) = 'pdf'";
		$video = " OR RIGHT (B.POS_Arquivo, 3) = 'mp4'";
		/*$feed = "B.POS_Tipo = 'Feed'";
		$forum = " OR B.POS_Tipo = 'Forum'";
		$evento = " OR B.POS_Tipo = 'Evento'";
		$podcast = " OR B.POS_Tipo = 'Canal' OR B.POS_Tipo = 'Podcast'";*/
	}else if($nfiltros>0&&$nfiltros2==0){
		$pessoa = "false";
	}else if($nfiltros==0&&$nfiltros2>0){
		$imagem = "false";
	}
	if($pesquisa){
		$pesquisahtml .= "Resultado da pesquisa para \"".$pesquisa."\"";
		$pesquisasql = "AND (B.POS_Titulo LIKE '%".$pesquisa."%' OR B.POS_Resumo LIKE '%".$pesquisa."%' OR B.POS_Descricao LIKE '%".$pesquisa."%')";
		$pesquisasql2 = "AND (A.USU_Nome LIKE '%".$pesquisa."%' OR C.CID_Cidade LIKE '%".$pesquisa."%' OR D.EST_Estado LIKE '%".$pesquisa."%')";
	}else{
		$pesquisasql = "";
		$pesquisasql2 = "";
	}
	$sqlN = "SELECT 'x' post, A.USU_RowID quem, A.USU_RowID ord FROM usuarios A, cidades C, estados D WHERE A.USU_CidadeID = C.CID_RowID AND A.USU_EstadoID = D.EST_RowID AND (".$pessoa.$empresa.") ".$pesquisasql2." UNION SELECT B.POS_RowID post, B.POS_UsuID quem, B.POS_Data ord FROM posts B WHERE (".$imagem.$pdf.$video.") ".$pesquisasql." ORDER BY ord DESC";
	$tqlN = mysqli_query($serve,$sqlN);
	$Ntotal = mysqli_num_rows($tqlN);
	$Nporpag = 50;
	$Npag = ceil($Ntotal/$Nporpag);
	$proxima = $pag>=$Npag?false:$pag+1;
	$L0 = ($pag - 1) * $Nporpag;
	$L1 = $L0 + $Nporpag;
	$sql = "SELECT 'x' post, A.USU_RowID quem, A.USU_RowID ord FROM usuarios A, cidades C, estados D WHERE A.USU_CidadeID = C.CID_RowID AND A.USU_EstadoID = D.EST_RowID AND (".$pessoa.$empresa.") ".$pesquisasql2." UNION SELECT B.POS_RowID post, B.POS_UsuID quem, B.POS_Data ord FROM posts B WHERE (".$imagem.$pdf.$video.") ".$pesquisasql." ORDER BY ord DESC LIMIT ".$L0.",".$L1;
	$tql = mysqli_query($serve,$sql);
	$total = mysqli_num_rows($tql);
	/*$retorno = array('cod' => 2, 'sql' => $sql);
	echo json_encode($retorno);
	exit();*/
	$i = 0;
	if ($total>0){
		/* Percorre um por um a lista das pessoas seguidas pelo perfil logado escolhidas para exibição */
		while($uql = mysqli_fetch_array($tql)){
			$idpost = $uql["post"];
			$quem = $uql["quem"];
			$sqlnomepost = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$quem."'";
			$tqlnomepost = mysqli_query($serve,$sqlnomepost);
			$uqlnomepost = mysqli_fetch_array($tqlnomepost);
			$nome = $uqlnomepost["USU_Nome"];
			$sqlfoto = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_RowID = '".$quem."'";
			$tqlfoto = mysqli_query($serve,$sqlfoto);
			$uqlfoto = mysqli_fetch_array($tqlfoto);
			if(!$uqlfoto["POS_Arquivo"]){
				$fotourl = $server."upload/avatar.jpg";
			}else{
				$fotourl = $server."upload/".$uqlfoto["POS_Arquivo"];
			}
			$fotoperfil = "<img src=\"".$fotourl."\" class=\"brs-50 mxh-46\" height=\"46\" width=\"46\"/>";
			$titulo = $fotoperfil.$nome;
			
			/* Pega os dados do post seguido percorrido */
			$sql2 = "SELECT POS_Titulo, POS_Tipo, POS_Resumo, POS_Data FROM posts WHERE POS_RowID = '".$idpost."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			$sql4 = "SELECT C.CID_Cidade, E.EST_Sigla FROM usuarios A, cidades C, estados E WHERE A.USU_RowID = '".$quem."' AND A.USU_CidadeID = C.CID_RowID AND A.USU_EstadoID = E.EST_RowID";
			$tql4 = mysqli_query($serve,$sql4);
			$uql4 = mysqli_fetch_array($tql4);
			$pagina = "";
			if($idpost=='x'){
				$titulo = $nome;
				$subtitulo = titleCase(strtolower($uql4['CID_Cidade'])).", ".$uql4['EST_Sigla'];
				$location = "window.location = 'perfil.html?id=".$quem."'";
				$atualizado = "";
			}else{
				$titulo = $uql2["POS_Titulo"];
				$subtitulo = "Postado por ".$nome;
				if($uql2["POS_Tipo"]=="Forum"){$pagina = "forum";}
				if($uql2["POS_Tipo"]=="Evento"){$pagina = "evento";}
				if($uql2["POS_Tipo"]=="Canal"){$pagina = "podcast";}
				if($uql2["POS_Tipo"]=="Podcast"){$pagina = "podcast";}
				if($uql2["POS_Tipo"]=="Feed"){$pagina = "post";}
				if($pagina!=""){
					$location = "window.location = '".$pagina.".html?id=".$idpost."'";
				}else{
					$location = "";
				}
				/* Pega a data do último comentário */
				$sql3 = "SELECT ACA_Data FROM acao WHERE ACA_PosPaiID = '".$idpost."' ORDER BY ACA_Data DESC LIMIT 1";
				$tql3 = mysqli_query($serve,$sql3);
				$Ncomentarios = mysqli_num_rows($tql3);
				$atualizado = "";
				if($Ncomentarios>0){
					$uql3 = mysqli_fetch_array($tql3);
					$data = date_format(date_create($uql3["ACA_Data"]), 'd/m H:i');
					$atualizado = "<div class=\"fs-05 c4\">Atualizado em ".$data."</div>";
				}else{
					$data = date_format(date_create($uql2["POS_Data"]), 'd/m H:i');
					$atualizado = "<div class=\"fs-05 c4\">em ".$data."</div>";
				}
			}
			$i++;
			if($i==$total){
				$classe = "";
			}else{
				$classe = "mb-10 mt-10 pb-10 bb";
			}
			/* Monta o bloco de informações de exibição do tópico percorrido */
			$html .= "<div onclick=\"".$location."\" class=\"".$classe."\">
						<div class=\"d-flex no-overflow\">
	                        <div class=\"mr-10 mxw-46\">".$fotoperfil."</div>
							<div class=\"d-flex flex-column no-overflow\">
		                        <div class=\"fs-08\">".$titulo."</div>
		                        <div class=\"fs-07 c4\">".$subtitulo."</div>
		                        ".$atualizado."
		                    </div>
		                    <div class=\"w-30px\" style=\"position:relative;\"><img src=\"imagens/svg/awesome-chevron-circle-left.svg\" style=\"position:absolute;top:50%;transform:translateY(-50%) rotate(180deg);\"/></div>
		                </div>
	                </div>";
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'html' => $pesquisahtml.$html, 'proxima' => $proxima, 'numero' => $Ntotal, 'sql' =>  $sqlN);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => $pesquisahtml."<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhum resultado disponível</div>", 'numero' => 0, 'sql' =>  $sqlN);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}

/* Seguidores */
if($_POST["f"]=="seguidores"){
	$ID = $_POST["usuario"];
	if(!$ID){$ID = $ID2;}
	$sqlnome = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$ID."'";
	$tqlnome = mysqli_query($serve,$sqlnome);
	$uqlnome = mysqli_fetch_array($tqlnome);
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$pesquisa = $_POST["pesquisa"];
	$html = "";
	$pesquisahtml = "";
	if($pesquisa){
		$pesquisahtml .= "<div class=\"mt-10 mb-10 textcenter\">Resultado da pesquisa para \"".$pesquisa."\"</div>";
		/* Verifica o número total de seguidores que combinam com a pesquisa */
		$sqlN = "SELECT count(A.ACA_UsuID) N FROM acao A, usuarios B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_Usu2ID = '".$ID."' AND A.ACA_UsuID = B.USU_RowID AND A.ACA_PosID is null AND B.USU_Nome LIKE '%".$pesquisa."%'";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		/* Pega o ID dos seguidores que combinam com a pesquisa que deverão aparecer na página de consulta solicitada */
		$sql = "SELECT A.ACA_UsuID FROM acao A, usuarios B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_Usu2ID = '".$ID."' AND A.ACA_UsuID = B.USU_RowID AND A.ACA_PosID is null AND B.USU_Nome LIKE '%".$pesquisa."%' ORDER BY ACA_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}else{
		/* Verifica o número total de seguidores */
		$sqlN = "SELECT count(ACA_UsuID) N FROM acao WHERE ACA_Acao = 'Seguir' AND ACA_PosID is null AND ACA_Usu2ID = '".$ID."'";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		/* Pega o ID dos seguidores que deverão aparecer na página de consulta solicitada */
		$sql = "SELECT ACA_UsuID FROM acao WHERE ACA_Acao = 'Seguir' AND ACA_PosID is null AND ACA_Usu2ID = '".$ID."' ORDER BY ACA_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}
	if ($total>0){
		/* Percorre um por um a lista de seguidores escolhidos para exibição */
		while($uql = mysqli_fetch_array($tql)){
			$idseguidor = $uql["ACA_UsuID"];
			/* Pega os dados textuais do seguidor percorrido */
			$sql2 = "SELECT A.USU_Nome, B.CID_Cidade, C.EST_Sigla FROM usuarios A, cidades B, estados C WHERE A.USU_RowID = '".$idseguidor."' AND A.USU_CidadeID = B.CID_RowID AND A.USU_EstadoID = C.EST_RowID";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			/* Pega a foto de perfil do seguidor percorrido */
			$sql3 = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_UsuID = '".$idseguidor."'";
			$tql3 = mysqli_query($serve,$sql3);
			$uql3 = mysqli_fetch_array($tql3);
			if(!$uql3["POS_Arquivo"]){
				$foto = $server."upload/avatar.jpg";
			}else{
				$foto = $server."upload/".$uql3["POS_Arquivo"];
			}
			/* Monta o bloco de informações de exibição do seguidor percorrido */
			$html .= "<div onclick=\"window.location = 'perfil.html?id=".$idseguidor."';\">
						<div class=\"d-flex flex-h pt-10\">
	                        <div class=\"mr-10 w-40px\"><img src=\"".$foto."\" class=\"brs-50 mxh-40\" height=\"40\" width=\"40\"></div>
	                        <div class=\"flex-column\"><div>".$uql2["USU_Nome"]."</div><div class=\"fs-07 c4\">".$uql2["CID_Cidade"].", ".$uql2["EST_Sigla"]."</div></div>
	                    </div>
	                </div>";
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'nome' =>  $uqlnome["USU_Nome"], 'html' => $pesquisahtml.$html, 'proxima' => $proxima, 'numero' => $Ntotal);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'nome' =>  $uqlnome["USU_Nome"], 'html' => $pesquisahtml."<div class=\"w-100 mt-10 mb-10 textcenter\">Não há seguidores</div>", 'numero' => 0);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}
	}
}
if($_POST["f"]=="seguindo"){
	$ID = $_POST["usuario"];
	if(!$ID){$ID = $ID2;}
	$sqlnome = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$ID."'";
	$tqlnome = mysqli_query($serve,$sqlnome);
	$uqlnome = mysqli_fetch_array($tqlnome);
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$pesquisa = $_POST["pesquisa"];
	$html = "";
	$pesquisahtml = "";
	if($pesquisa){
		$pesquisahtml .= "<div class=\"mt-10 mb-10 textcenter\">Resultado da pesquisa para \"".$pesquisa."\"</div>";
		/* Verifica o número total de pessoas seguidas pelo perfil logado que combinam com a pesquisa */
		$sqlN = "SELECT count(A.ACA_Usu2ID) N FROM acao A, usuarios B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_UsuID = '".$ID."' AND A.ACA_Usu2ID = B.USU_RowID AND A.ACA_PosID is null AND B.USU_Nome LIKE '%".$pesquisa."%'";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		/* Pega o ID das pessoas seguidas pelo perfil logado que combinam com a pesquisa que deverão aparecer na página de consulta solicitada */
		$sql = "SELECT A.ACA_Usu2ID, B.USU_Nome FROM acao A, usuarios B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_UsuID = '".$ID."' AND A.ACA_Usu2ID = B.USU_RowID AND A.ACA_PosID is null AND B.USU_Nome LIKE '%".$pesquisa."%' ORDER BY ACA_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}else{
		/* Verifica o número total de pessoas seguidas pelo perfil logado */
		$sqlN = "SELECT count(ACA_Usu2ID) N FROM acao WHERE ACA_Acao = 'Seguir' AND ACA_PosID is null AND ACA_UsuID = '".$ID."'";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		/* Pega o ID das pessoas seguidas pelo perfil logado que deverão aparecer na página de consulta solicitada */
		$sql = "SELECT ACA_Usu2ID FROM acao WHERE ACA_Acao = 'Seguir' AND ACA_PosID is null AND ACA_UsuID = '".$ID."' ORDER BY ACA_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}
	if ($total>0){
		/* Percorre um por um a lista das pessoas seguidas pelo perfil logado escolhidas para exibição */
		while($uql = mysqli_fetch_array($tql)){
			$idseguidor = $uql["ACA_Usu2ID"];
			/* Pega os dados textuais da pessoa seguida percorrida */
			$sql2 = "SELECT A.USU_Nome, B.CID_Cidade, C.EST_Sigla FROM usuarios A, cidades B, estados C WHERE A.USU_RowID = '".$idseguidor."' AND A.USU_CidadeID = B.CID_RowID AND A.USU_EstadoID = C.EST_RowID";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			/* Pega a foto de perfil da pessoa seguida percorrida */
			$sql3 = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_UsuID = '".$idseguidor."'";
			$tql3 = mysqli_query($serve,$sql3);
			$uql3 = mysqli_fetch_array($tql3);
			if(!$uql3["POS_Arquivo"]){
				$foto = $server."upload/avatar.jpg";
			}else{
				$foto = $server."upload/".$uql3["POS_Arquivo"];
			}
			/* Monta o bloco de informações de exibição da pessoa seguida percorrida */
			$html .= "<div onclick=\"window.location = 'perfil.html?id=".$idseguidor."';\">
						<div class=\"d-flex flex-h pt-10\">
	                        <div class=\"mr-10 w-40px\"><img src=\"".$foto."\" class=\"brs-50 mxh-40\" height=\"40\" width=\"40\"></div>
	                        <div class=\"flex-column\"><div>".$uql2["USU_Nome"]."</div><div class=\"fs-07 c4\">".$uql2["CID_Cidade"].", ".$uql2["EST_Sigla"]."</div></div>
	                    </div>
	                </div>";
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'nome' =>  $uqlnome["USU_Nome"], 'html' => $pesquisahtml.$html, 'proxima' => $proxima, 'numero' => $Ntotal);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'nome' =>  $uqlnome["USU_Nome"], 'html' => $pesquisahtml."<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhum perfil seguido</div>", 'numero' => 0);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}
	}
}

/* Mensagens */
if($_POST["f"]=="mensagens"){
	$ID = $_POST["usuario"];
	if(!$ID){$ID = $ID2;}
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$pesquisa = $_POST["pesquisa"];
	$html = "";
	$pesquisahtml = "";
	if($pesquisa){
		$pesquisahtml .= "<div class=\"mt-10 mb-10 textcenter\">Resultado da pesquisa para \"".$pesquisa."\"</div>";
		$sqlN = "SELECT count(usuario) N FROM ((SELECT A.ACA_UsuID usuario  FROM acao A, usuarios B WHERE A.ACA_Acao = 'Conversar' AND A.ACA_Usu2ID = '".$ID."' AND A.ACA_UsuID = B.USU_RowID AND B.USU_Nome LIKE '%".$pesquisa."%') UNION (SELECT A.ACA_Usu2ID usuario  FROM acao A, usuarios B WHERE A.ACA_Acao = 'Conversar' AND A.ACA_UsuID = '".$ID."' AND A.ACA_Usu2ID = B.USU_RowID AND B.USU_Nome LIKE '%".$pesquisa."%')) x";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		$sql = "(SELECT A.ACA_UsuID usuario  FROM acao A, usuarios B WHERE A.ACA_Acao = 'Conversar' AND A.ACA_Usu2ID = '1' AND A.ACA_UsuID = B.USU_RowID AND B.USU_Nome LIKE '%".$pesquisa."%') UNION (SELECT A.ACA_Usu2ID usuario  FROM acao A, usuarios B WHERE A.ACA_Acao = 'Conversar' AND A.ACA_UsuID = '1' AND A.ACA_Usu2ID = B.USU_RowID AND B.USU_Nome LIKE '%".$pesquisa."%')";
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}else{
		$sqlN = "SELECT count(usuario) N FROM ((SELECT ACA_UsuID usuario FROM acao WHERE ACA_Acao = 'Conversar' AND ACA_Usu2ID = '".$ID."') UNION (SELECT ACA_Usu2ID usuario FROM acao WHERE ACA_Acao = 'Conversar' AND ACA_UsuID = '".$ID."')) x";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		$sql = "(SELECT ACA_UsuID usuario  FROM acao WHERE ACA_Acao = 'Conversar' AND ACA_Usu2ID = '".$ID."') UNION (SELECT ACA_Usu2ID usuario  FROM acao WHERE ACA_Acao = 'Conversar' AND ACA_UsuID = '".$ID."')";
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}
	if ($total>0){
		$usuarios = array();
		$conversas = array();
		while($uql = mysqli_fetch_array($tql)){
			$sqlO = "SELECT ACA_RowID, ACA_Data FROM acao WHERE ((ACA_UsuID = '".$uql["usuario"]."' AND ACA_Usu2ID = '".$ID."') OR (ACA_Usu2ID = '".$uql["usuario"]."' AND ACA_UsuID = '".$ID."')) AND ACA_Acao = 'Conversar' ORDER BY ACA_Data DESC LIMIT 1";
			$tqlO = mysqli_query($serve,$sqlO);
			$uqlO = mysqli_fetch_array($tqlO);
			$usuarios[$uql["usuario"]] = $uqlO["ACA_Data"];
			$conversas[$uqlO["ACA_RowID"]] = $uqlO["ACA_Data"];
		}
		arsort($usuarios);
		$idsusuarios = array_keys($usuarios);
		arsort($conversas);
		$idsconversas = array_keys($conversas);
		$size = count($conversas);
		for ($i = 0; $i < $size; $i++) {
			$idusuario = $idsusuarios[$i];
			$idconversa = $idsconversas[$i];
			/* Pega o nome do usuario */
			$sql2 = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$idusuario."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			/* Pega a foto de perfil do usuario */
			$sql3 = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_UsuID = '".$idusuario."'";
			$tql3 = mysqli_query($serve,$sql3);
			$uql3 = mysqli_fetch_array($tql3);
			/* Pega o conteudo da última mensagem */
			$sql4 = "SELECT B.POS_Descricao, B.POS_UsuID FROM acao A, posts B WHERE B.POS_RowID = A.ACA_PosID AND A.ACA_RowID = '".$idconversa."'";
			$tql4 = mysqli_query($serve,$sql4);
			$uql4 = mysqli_fetch_array($tql4);
			if(!$uql3["POS_Arquivo"]){
				$foto = $server."upload/avatar.jpg";
			}else{
				$foto = $server."upload/".$uql3["POS_Arquivo"];
			}
			if($uql4["POS_UsuID"]==$ID){$eu = "Eu: ";}else{$eu = "";}
			$etc = strlen($uql4["POS_Descricao"])>35?"...":"";
			/* Monta o bloco de informações de exibição da última conversa com usuario percorrido */
			$html .= "<div onclick=\"window.location = 'chat.html?id=".$idusuario."';\">
						<div class=\"d-flex flex-h pt-10\">
	                        <div class=\"mr-10 w-40px\"><img src=\"".$foto."\" class=\"brs-50 mxh-40\" height=\"40\" width=\"40\"></div>
	                        <div class=\"flex-column\"><div>".$uql2["USU_Nome"]."</div><div class=\"fs-07 c4\">".$eu.substr($uql4["POS_Descricao"], 0, 35)." ".$etc."</div></div>
	                    </div>
	                </div>";
		}
		if($tql&&$tql2&&$tql3&&$tql4){
			$retorno = array('cod' => 1, 'html' => $pesquisahtml.$html, 'proxima' => $proxima, 'numero' => $Ntotal);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => $pesquisahtml."<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhuma conversa iniciada</div>", 'numero' => 0);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}
if($_POST["f"]=="chat"){
	if(!$ID){$ID = $ID2;}
	$idusuario = $_POST["usuario"];
	$msg = $_POST["idmsg"];
	if(!$msg){$msg = "";}else{$msg = "AND ACA_RowID > '".$msg."'";}
	$html = "";
	$sqlO = "SELECT ACA_RowID, ACA_UsuID FROM acao WHERE ((ACA_UsuID = '".$idusuario."' AND ACA_Usu2ID = '".$ID."') OR (ACA_Usu2ID = '".$idusuario."' AND ACA_UsuID = '".$ID."')) AND ACA_Acao = 'Conversar' ".$msg." ORDER BY ACA_RowID ASC";
	$tqlO = mysqli_query($serve,$sqlO);
	$total = mysqli_num_rows($tqlO);
	if ($total>0){
		while($uqlO = mysqli_fetch_array($tqlO)){
			$idconversa = $uqlO["ACA_RowID"];
			$idremetente = $uqlO["ACA_UsuID"];
			/* Pega o nome do usuario */
			$sql2 = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$idremetente."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			/* Pega a foto de perfil do usuario */
			$sql3 = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_UsuID = '".$idremetente."'";
			$tql3 = mysqli_query($serve,$sql3);
			$uql3 = mysqli_fetch_array($tql3);
			/* Pega o conteudo da última mensagem */
			$sql4 = "SELECT B.POS_Descricao, B.POS_Data FROM acao A, posts B WHERE B.POS_RowID = A.ACA_PosID AND A.ACA_RowID = '".$idconversa."'";
			$tql4 = mysqli_query($serve,$sql4);
			$uql4 = mysqli_fetch_array($tql4);
			if(!$uql3["POS_Arquivo"]){
				$foto = $server."upload/avatar.jpg";
			}else{
				$foto = $server."upload/".$uql3["POS_Arquivo"];
			}			
			$date = date_create($uql4["POS_Data"]);
			$data = date_format($date, 'd/m H:i');
			/* Monta o bloco de informações de exibição da mensagem percorrida */
			if($idremetente==$idusuario){
				$html .= "<div>
							<div class=\"d-flex flex-h pt-10\">
		                        <div class=\"mr-10 w-40px\"><img src=\"".$foto."\" class=\"brs-50 mxh-40\" height=\"40\" width=\"40\"></div>
		                        <div class=\"flex-column mxw-chat\">
		                        	<div class=\"h-11\">".$uql2["USU_Nome"]."</div>
		                        	<div class=\"fs-06 c4\">".$data."</div>
		                        	<div class=\"fs-08 c4\">".$uql4["POS_Descricao"]."</div>
		                        </div>
		                    </div>
		                </div>";
			}else{
				$html .= "<div class=\"minhamsg\">
							<div class=\"d-flex flex-h pt-10\">
		                        <div class=\"mr-10 w-40px\"><img src=\"".$foto."\" class=\"brs-50 mxh-40\" height=\"40\" width=\"40\"></div>
		                        <div class=\"flex-column mxw-chat\">
		                        	<div class=\"h-11\">".$uql2["USU_Nome"]."</div>
		                        	<div class=\"fs-06 c4\">".$data."</div>
		                        	<div class=\"fs-08 c4\">".$uql4["POS_Descricao"]."</div>
		                        </div>
		                    </div>
		                </div>";				
			}
		}
		if($tqlO&&$tql2&&$tql3&&$tql4){
			$retorno = array('cod' => 1, 'html' => $html, 'ultima' => $idconversa, 'numero' => $Ntotal);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($total==0&&!$msg){	
			/* Pega o nome do usuario */
			$sql2 = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$idusuario."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);		
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Envie uma mensagem para ".$uql2["USU_Nome"]."</div>", 'numero' => 0);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
	
}
if($_POST["f"]=="enviarchat"){
	if(!$ID){$ID = $ID2;}
	$idusuario = $_POST["usuario"];
	$msg = $_POST["mensagem"];
	mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);
	$sql = "INSERT INTO posts (POS_Tipo, POS_UsuID, POS_Descricao) VALUES ('Mensagem', '".$ID."', '".$msg."')";
	$tql = mysqli_query($serve,$sql);
	$last_insert_id = mysqli_insert_id($serve);
	$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_Usu2ID, ACA_PosID) VALUES ('Conversar', '".$ID."', '".$idusuario."', '".$last_insert_id."')";
	$tql2 = mysqli_query($serve,$sql2);
	if($tql&&$tql2){
		mysqli_commit($serve);
		$retorno = array('cod' => 1, 'id' => $REG_RowID);
		echo json_encode($retorno);
		exit();
	}else{
		mysqli_rollback($serve);
		$retorno = array('cod' => 2, 'msg' => 'ERRO - Não foi possível cadastrar agora');
		echo json_encode($retorno);
		exit();
	}
}

/* Foruns */
if($_POST["f"]=="meusforuns"){
	$ID = $_POST["usuario"];
	if(!$ID){$ID = $ID2;}
	$sqlnome = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$ID."'";
	$tqlnome = mysqli_query($serve,$sqlnome);
	$uqlnome = mysqli_fetch_array($tqlnome);
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$pesquisa = $_POST["pesquisa"];
	$html = "";
	$pesquisahtml = "";
	if($pesquisa){
		$pesquisahtml .= "Resultado da pesquisa para \"".$pesquisa."\"";
		/* Verifica o número total de posts seguidos pelo perfil logado que combinam com a pesquisa */
		$sqlN = "SELECT count(A.ACA_PosID) N FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Forum' AND A.ACA_UsuID = '".$ID."' AND B.POS_Status = 'Publico' AND (B.POS_Titulo LIKE '%".$pesquisa."%' OR B.POS_Resumo LIKE '%".$pesquisa."%')";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		/* Pega o ID dos foruns seguidos pelo perfil logado que combinam com a pesquisa que deverão aparecer na página de consulta solicitada */
		$sql = "SELECT A.ACA_PosID FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Forum' AND A.ACA_UsuID = '".$ID."' AND B.POS_Status = 'Publico' AND (B.POS_Titulo LIKE '%".$pesquisa."%' OR B.POS_Resumo LIKE '%".$pesquisa."%') ORDER BY A.ACA_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}else{
		/* Verifica o número total de posts seguidos pelo perfil logado */
		$sqlN = "SELECT count(A.ACA_PosID) N FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Forum' AND A.ACA_UsuID = '".$ID."' AND B.POS_Status = 'Publico'";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		/* Pega o ID dos foruns seguidos pelo perfil logado que deverão aparecer na página de consulta solicitada */
		$sql = "SELECT A.ACA_PosID FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Forum' AND A.ACA_UsuID = '".$ID."' AND B.POS_Status = 'Publico' ORDER BY A.ACA_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}
	if ($total>0){
		/* Percorre um por um a lista das pessoas seguidas pelo perfil logado escolhidas para exibição */
		while($uql = mysqli_fetch_array($tql)){
			$idpost = $uql["ACA_PosID"];
			/* Pega os dados do post seguido percorrido */
			$sql2 = "SELECT POS_Arquivo, POS_Titulo, POS_Resumo FROM posts WHERE POS_RowID = '".$idpost."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			if(!$uql2["POS_Arquivo"]){
				$foto = $server."upload/forum.jpg";
				$arquivo = "<img src=\"".$foto."\" class=\"mxw-120 mxh-120\">";
			}else{
				$foto = $server."upload/".$uql2["POS_Arquivo"];
				if(substr($uql2["POS_Arquivo"], -3)=='pdf'){
					$foto = $server."upload/forum.jpg";
					$arquivo = "<img src=\"".$foto."\" class=\"mxw-120 mxh-120\">";
				}else if(substr($uql2["POS_Arquivo"], -3)=='mp4'){
					$arquivo = "<video height=\"auto\" width=\"auto\" class=\"mxw-120 mxh-120\"><source src=\"".$foto."#t=0.00001\" type=\"video/mp4\"></video>";
				}else{
					$arquivo = "<img src=\"".$foto."\" class=\"mxw-120 mxh-120\">";
				}				
			}
			/* Pega a data do último comentário */
			$sql3 = "SELECT ACA_Data FROM acao WHERE ACA_PosPaiID = '".$idpost."' ORDER BY ACA_Data DESC LIMIT 1";
			$tql3 = mysqli_query($serve,$sql3);
			$Ncomentarios = mysqli_num_rows($tql3);
			$atualizado = "";
			if($Ncomentarios>0){
				$uql3 = mysqli_fetch_array($tql3);
				$data = date_format(date_create($uql3["ACA_Data"]), 'd/m H:i');
				$atualizado = "<div class=\"fs-05 c4\">Atualizado em ".$data."</div>";
			}
			/* Monta o bloco de informações de exibição do tópico percorrido */
			$html .= "<div onclick=\"window.location = 'forum.html?id=".$idpost."';\" class=\"mnw-120 mxw-120 mr-10\">
						<div class=\"d-flex flex-column p-10 b brs-20px no-overflow\">
	                        <div class=\"imgtopico\">".$arquivo."</div>
	                        <div class=\"fs-08 mt-10\">".$uql2["POS_Titulo"]."</div>
	                        <div class=\"fs-07 c4\">".$uql2["POS_Resumo"]."</div>
	                        ".$atualizado."
	                    </div>
	                </div>";
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'html' => $html, 'proxima' => $proxima, 'numero' => $Ntotal, 'resultado' => $pesquisahtml, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhum fórum acompanhado</div>", 'numero' => 0, 'resultado' => $pesquisahtml, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}
if($_POST["f"]=="seguindoforuns"){
	$ID = $_POST["usuario"];
	if(!$ID){$ID = $ID2;}
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$sqlnome = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$ID."'";
	$tqlnome = mysqli_query($serve,$sqlnome);
	$uqlnome = mysqli_fetch_array($tqlnome);
	$sqlseguindo = "SELECT ACA_Usu2ID FROM acao WHERE ACA_Acao = 'Seguir' AND ACA_UsuID = '".$ID."'";
	$tqlseguindo = mysqli_query($serve,$sqlseguindo);
	$Nseguindo = mysqli_num_rows($tqlseguindo);
	$pesquisa = $_POST["pesquisa"];
	$html = "";
	$pesquisahtml = "";
	if($Nseguindo>0){
		$Ntotal = 0;
		while($uqlseguindo = mysqli_fetch_array($tqlseguindo)){
			$idseguindo = $uqlseguindo["ACA_Usu2ID"];
			if($pesquisa){
				$pesquisahtml = "Resultado da pesquisa para \"".$pesquisa."\"";
				$sqlN = "SELECT count(A.ACA_PosID) N FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Forum' AND A.ACA_UsuID = '".$idseguindo."' AND B.POS_Status = 'Publico' AND (B.POS_Titulo LIKE '%".$pesquisa."%' OR B.POS_Resumo LIKE '%".$pesquisa."%')";
				$tqlN = mysqli_query($serve,$sqlN);
				$uqlN = mysqli_fetch_array($tqlN);
				$Ntotal = $Ntotal+$uqlN["N"];
				$Nporpag = 50;
				$Npag = ceil($Ntotal/$Nporpag);
				$proxima = $pag>=$Npag?false:$pag+1;
				$L0 = ($pag - 1) * $Nporpag;
				$L1 = $L0 + $Nporpag;
				$sql = "SELECT A.ACA_PosID FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Forum' AND A.ACA_UsuID = '".$idseguindo."' AND B.POS_Status = 'Publico' AND (B.POS_Titulo LIKE '%".$pesquisa."%' OR B.POS_Resumo LIKE '%".$pesquisa."%') ORDER BY A.ACA_Data DESC LIMIT ".$L0.",".$L1;
				$tql = mysqli_query($serve,$sql);
				$total = mysqli_num_rows($tql);
			}else{
				$sqlN = "SELECT count(A.ACA_PosID) N FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Forum' AND A.ACA_UsuID = '".$idseguindo."' AND B.POS_Status = 'Publico'";
				$tqlN = mysqli_query($serve,$sqlN);
				$uqlN = mysqli_fetch_array($tqlN);
				$Ntotal = $Ntotal+$uqlN["N"];
				$Nporpag = 50;
				$Npag = ceil($Ntotal/$Nporpag);
				$proxima = $pag>=$Npag?false:$pag+1;
				$L0 = ($pag - 1) * $Nporpag;
				$L1 = $L0 + $Nporpag;
				$sql = "SELECT A.ACA_PosID FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Forum' AND A.ACA_UsuID = '".$idseguindo."' AND B.POS_Status = 'Publico' ORDER BY A.ACA_Data DESC LIMIT ".$L0.",".$L1;
				$tql = mysqli_query($serve,$sql);
				$total = mysqli_num_rows($tql);
			}
			if ($total>0){
				while($uql = mysqli_fetch_array($tql)){
					$idpost = $uql["ACA_PosID"];
					$sql2 = "SELECT POS_Arquivo, POS_Titulo, POS_Resumo FROM posts WHERE POS_RowID = '".$idpost."'";
					$tql2 = mysqli_query($serve,$sql2);
					$uql2 = mysqli_fetch_array($tql2);
					if(!$uql2["POS_Arquivo"]){
						$foto = $server."upload/forum.jpg";
						$arquivo = "<img src=\"".$foto."\" class=\"mxw-120 mxh-120\">";
					}else{
						$foto = $server."upload/".$uql2["POS_Arquivo"];
						if(substr($uql2["POS_Arquivo"], -3)=='pdf'){
							$foto = $server."upload/forum.jpg";
							$arquivo = "<img src=\"".$foto."\" class=\"mxw-120 mxh-120\">";
						}else if(substr($uql2["POS_Arquivo"], -3)=='mp4'){
							$arquivo = "<video height=\"auto\" width=\"auto\" class=\"mxw-120 mxh-120\"><source src=\"".$foto."#t=0.00001\" type=\"video/mp4\"></video>";
						}else{
							$arquivo = "<img src=\"".$foto."\" class=\"mxw-120 mxh-120\">";
						}	
					}
					$sql3 = "SELECT ACA_Data FROM acao WHERE ACA_PosPaiID = '".$idpost."' ORDER BY ACA_Data DESC LIMIT 1";
					$tql3 = mysqli_query($serve,$sql3);
					$Ncomentarios = mysqli_num_rows($tql3);
					$atualizado = "";
					if($Ncomentarios>0){
						$uql3 = mysqli_fetch_array($tql3);
						$data = date_format(date_create($uql3["ACA_Data"]), 'd/m H:i');
						$atualizado = "<div class=\"fs-05 c4\">Atualizado em ".$data."</div>";
					}
					$html .= "<div onclick=\"window.location = 'forum.html?id=".$idpost."';\" class=\"mnw-120 mxw-120 mr-10\">
								<div class=\"d-flex flex-column p-10 b brs-20px no-overflow\">
			                        <div class=\"imgtopico\">".$arquivo."</div>
			                        <div class=\"fs-08 mt-10\">".$uql2["POS_Titulo"]."</div>
			                        <div class=\"fs-07 c4\">".$uql2["POS_Resumo"]."</div>
			                        ".$atualizado."
			                    </div>
			                </div>";
				}
			}
		}
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhum fórum acompanhado por pessoas seguidas</div>", 'numero' => 0, 'resultado' => $pesquisahtml);
			echo json_encode($retorno);
			exit();
		}else{
			if($tql&&$tql2&&$tql3){
				$retorno = array('cod' => 1, 'html' => $html, 'proxima' => $proxima, 'numero' => $Ntotal, 'resultado' => $pesquisahtml);
				echo json_encode($retorno);
				exit();
			}else{
				$retorno = array('cod' => 2);
				echo json_encode($retorno);
				exit();
			}
		}
	}else{
		$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhuma pessoa seguida</div>", 'numero' => 0);
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="privadosforuns"){
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$pesquisa = $_POST["pesquisa"];
	$html = "";
	$pesquisahtml = "";
	if($pesquisa){
		$pesquisahtml .= "Resultado da pesquisa para \"".$pesquisa."\"";
		$sqlN = "SELECT count(POS_RowID) N FROM posts WHERE POS_Tipo = 'Forum' AND POS_Status = 'Privado' AND (POS_Titulo LIKE '%".$pesquisa."%' OR POS_Resumo LIKE '%".$pesquisa."%')";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		$sql = "SELECT POS_RowID FROM posts WHERE POS_Tipo = 'Forum' AND POS_Status = 'Privado' AND (POS_Titulo LIKE '%".$pesquisa."%' OR POS_Resumo LIKE '%".$pesquisa."%') ORDER BY POS_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}else{
		$sqlN = "SELECT count(POS_RowID) N FROM posts WHERE POS_Tipo = 'Forum' AND POS_Status = 'Privado'";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		$sql = "SELECT POS_RowID FROM posts WHERE POS_Tipo = 'Forum' AND POS_Status = 'Privado' ORDER BY POS_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}
	if ($total>0){
		while($uql = mysqli_fetch_array($tql)){
			$idpost = $uql["POS_RowID"];
			$sql2 = "SELECT POS_Arquivo, POS_Titulo, POS_Resumo FROM posts WHERE POS_RowID = '".$idpost."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			if(!$uql2["POS_Arquivo"]){
				$foto = $server."upload/forum.jpg";
				$arquivo = "<img src=\"".$foto."\" class=\"mxw-120 mxh-120\">";
			}else{
				$foto = $server."upload/".$uql2["POS_Arquivo"];
				if(substr($uql2["POS_Arquivo"], -3)=='pdf'){
					$foto = $server."upload/forum.jpg";
					$arquivo = "<img src=\"".$foto."\" class=\"mxw-120 mxh-120\">";
				}else if(substr($uql2["POS_Arquivo"], -3)=='mp4'){
					$arquivo = "<video height=\"auto\" width=\"auto\" class=\"mxw-120 mxh-120\"><source src=\"".$foto."#t=0.00001\" type=\"video/mp4\"></video>";
				}else{
					$arquivo = "<img src=\"".$foto."\" class=\"mxw-120 mxh-120\">";
				}	
			}
			$sql3 = "SELECT ACA_Data FROM acao WHERE ACA_PosPaiID = '".$idpost."' ORDER BY ACA_Data DESC LIMIT 1";
			$tql3 = mysqli_query($serve,$sql3);
			$Ncomentarios = mysqli_num_rows($tql3);
			$atualizado = "";
			if($Ncomentarios>0){
				$uql3 = mysqli_fetch_array($tql3);
				$data = date_format(date_create($uql3["ACA_Data"]), 'd/m H:i');
				$atualizado = "<div class=\"fs-05 c4\">Atualizado em ".$data."</div>";
			}
			$html .= "<div onclick=\"window.location = 'forum.html?id=".$idpost."';\" class=\"mnw-120 mxw-120 mr-10\">
						<div class=\"d-flex flex-column p-10 b brs-20px no-overflow\">
	                        <div class=\"imgtopico\">".$arquivo."</div>
	                        <div class=\"fs-08 mt-10\">".$uql2["POS_Titulo"]."</div>
	                        <div class=\"fs-07 c4\">".$uql2["POS_Resumo"]."</div>
	                        ".$atualizado."
	                    </div>
	                </div>";
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'html' => $html, 'proxima' => $proxima, 'numero' => $Ntotal, 'resultado' => $pesquisahtml);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhum fórum privado</div>", 'numero' => 0, 'resultado' => $pesquisahtml);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}
if($_POST["f"]=="novoforum"){
	if(!$ID){$ID = $ID2;}
	if(!empty($_POST["titulo"])){$POS_Titulo = $_POST["titulo"];}else{echo json_encode(array('cod' => 0, 'ide' => 'titulo'));exit();}
	if(!empty($_POST["resumo"])){$POS_Resumo = $_POST["resumo"];}else{echo json_encode(array('cod' => 0, 'ide' => 'resumo'));exit();}
	if(!empty($_POST["descricao"])){$POS_Descricao = $_POST["descricao"];}else{echo json_encode(array('cod' => 0, 'ide' => 'descricao'));exit();}
	$POS_Arquivo = $_POST["arquivo"]?"'".$_POST["arquivo"]."'":'null';
	if(!empty($_POST["status"])){$POS_Status = $_POST["status"];}else{echo json_encode(array('cod' => 0, 'ide' => 'status'));exit();}
	mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);
	$sql = "INSERT INTO posts (POS_Tipo, POS_UsuID, POS_Titulo, POS_Resumo, POS_Descricao, POS_Arquivo, POS_Status) VALUES ('Forum', '".$ID."', '".$POS_Titulo."', '".$POS_Resumo."', '".$POS_Descricao."', ".$POS_Arquivo.", '".$POS_Status."')";
	$tql = mysqli_query($serve,$sql);
	$last_insert_id = mysqli_insert_id($serve);
	$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_PosID) VALUES ('Postar', '".$ID."', '".$last_insert_id."')";
	$tql2 = mysqli_query($serve,$sql2);
	$sql3 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_PosID) VALUES ('Seguir', '".$ID."', '".$last_insert_id."')";
	$tql3 = mysqli_query($serve,$sql3);
	if($tql&&$tql2&&$tql3){
		mysqli_commit($serve);
		$retorno = array('cod' => 1, 'id' => $last_insert_id);
		echo json_encode($retorno);
		exit();
	}else{
		mysqli_rollback($serve);
		$retorno = array('cod' => 2, 'msg' => 'ERRO - Não foi possível cadastrar agora');
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="forum"){
	if(!$ID){$ID = $ID2;}
	$idpost = $_POST["id"];
	/* Pega os dados do post seguido percorrido */
	$sql2 = "SELECT POS_UsuID, POS_Arquivo, POS_Titulo, POS_Resumo, POS_Descricao FROM posts WHERE POS_RowID = '".$idpost."'";
	$tql2 = mysqli_query($serve,$sql2);
	$uql2 = mysqli_fetch_array($tql2);
	$sqlnomepost = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$uql2["POS_UsuID"]."'";
	$tqlnomepost = mysqli_query($serve,$sqlnomepost);
	$uqlnomepost = mysqli_fetch_array($tqlnomepost);
	$nome = $uqlnomepost["USU_Nome"];
	$sqlfoto = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_RowID = '".$uql2["POS_UsuID"]."'";
	$tqlfoto = mysqli_query($serve,$sqlfoto);
	$uqlfoto = mysqli_fetch_array($tqlfoto);
	if(!$uqlfoto["POS_Arquivo"]){
		$fotourl = $server."upload/avatar.jpg";
	}else{
		$fotourl = $server."upload/".$uqlfoto["POS_Arquivo"];
	}
	$fotoperfil = "<img src=\"".$fotourl."\" class=\"brs-50 mxh-46 mr-10\" height=\"46\" width=\"46\"/>";

	$sql7 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Seguir' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
	$tql7 = mysqli_query($serve,$sql7);
	$Nseguir = mysqli_num_rows($tql7);
	if($Nseguir>0){
		$seicone = "imagens/svg/feather-rss-no.svg";
		$setexto = "Deixar de acompanhar";
	}else{
		$seicone = "imagens/svg/feather-rss.svg";
		$setexto = "Acompanhar";
	}
	$denunciar = "<img class=\"f-right h-18\" src=\"imagens/svg/interface.svg\" onclick=\"modalInput('denunciar','Descreva o motivo de sua denúncia','".$idpost."','','p','s');\">";
	$titulo = $fotoperfil."<span>".$uql2["POS_Titulo"]."<br><span class=\"fs-08\">postado por ".$nome."</span></span>
		<div>".$denunciar."<button id=\"postseguir\" class=\"Mt-20 smallbtn\" onclick=\"acompanhar(".$idpost.")\">
            <img src=\"".$seicone."\" style=\"height: 12px;\">
            <span class=\"ml-5px\">".$setexto."</span>
        </button></div>";
	$html = "";
	$resumo = "<div class=\"fs-08\">".$uql2["POS_Resumo"]."</div>";	
	$descricao = "<div class=\"fs-08\">".$uql2["POS_Descricao"]."</div>";		
	if(!$uql2["POS_Arquivo"]){
		$arquivo = "";
	}else{
		$foto = $server."upload/".$uql2["POS_Arquivo"];
		if(substr($uql2["POS_Arquivo"], -3)=='pdf'){
			$arquivo = "<a href=\"".$foto."\" target=\"_blank\" download><div class=\"pdf b\"><img src='imagens/svg/ionic-md-document.svg'/><br><span class='c4 fs-06'>PDF</span></div></a>";
		}else if(substr($uql2["POS_Arquivo"], -3)=='mp4'){
			$arquivo = "<video height=\"auto\" width=\"auto\" class=\"mxw-100p\" controls><source src=\"".$foto."#t=0.00001\" type=\"video/mp4\"></video>";
		}else{
			$arquivo = "<img src=\"".$foto."\" class=\"mxw-100p\">";
		}				
	}
	$sql4 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Curtir' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
	$tql4 = mysqli_query($serve,$sql4);
	$Ncurtir = mysqli_num_rows($tql4);
	if($Ncurtir>0){
		$cclasse = "c2";
		$cicone = "imagens/svg/awesome-heart2.svg";
		$ctexto = "Descurtir";
	}else{
		$cclasse = "c3";
		$cicone = "imagens/svg/awesome-heart.svg";
		$ctexto = "Curtir";
	}
	$sql5 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Compartilhar' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
	$tql5 = mysqli_query($serve,$sql5);
	$Ncompartilhar = mysqli_num_rows($tql5);
	if($Ncompartilhar>0){
		$coclasse = "c2";
		$coicone = "imagens/svg/awesome-share-square3.svg";
		$cotexto = "Compartilhado";
	}else{
		$coclasse = "c3";
		$coicone = "imagens/svg/awesome-share-square.svg";
		$cotexto = "Compartilhar";
	}
	$sql6 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Salvar' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
	$tql6 = mysqli_query($serve,$sql6);
	$Nsalvar = mysqli_num_rows($tql6);
	if($Nsalvar>0){
		$sclasse = "c2";
		$sicone = "imagens/svg/metro-floppy-disk2.svg";
		$stexto = "Descartar";
	}else{
		$sclasse = "c3";
		$sicone = "imagens/svg/metro-floppy-disk.svg";
		$stexto = "Salvar";
	}
	/* Monta o bloco de informações de exibição do tópico percorrido */
	$html .= "<div name=\"".$idpost."\">
				<div class=\"d-flex flex-column no-overflow\">
                    <div class=\"d-flex p-rel\">".$titulo."</div>
                    <div class=\"mt-10\">".$resumo."</div>
                    <div>".$arquivo."</div>
                    <div>".$descricao."</div>
                </div>
                <div class=\"d-flex flex-h mt-10 mb-10\">
                    <div class=\"textcenter ".$cclasse." fs-06 br\" onclick=\"curtir(this,".$idpost.")\">
                    	<img class=\"mr-5px h-18\" src=\"".$cicone."\"/><span>".$ctexto."</span></div>
                    <div class=\"textcenter c3 fs-06 br\">
                    	<img class=\"mr-5px h-18\" src=\"imagens/svg/material-chat.svg\"/>Comentar</div>
                    <div class=\"textcenter ".$coclasse." fs-06 br\" onclick=\"compartilhar(".$idpost.")\">
                    	<img class=\"mr-5px h-18\" src=\"".$coicone."\"/><span>".$cotexto."</span></div>
                    <div class=\"textcenter ".$sclasse." fs-06\" onclick=\"salvar(this,".$idpost.")\">
                    	<img class=\"mr-5px h-18\" src=\"".$sicone."\"/><span>".$stexto."</span></div>
                </div>
                <div class=\"slider-h flex-item-auto mt-20\">
                    <div class=\"d-flex flex-h c5 text-center\">
                        <div class=\"fs-06 p-2-10 brs-20px b mr-10\" id=\"comentarios-foto\" onclick=\"comentariofoto()\">Fotos</div>
                        <input type=\"hidden\" value=\"0\" id=\"comentarios-foto-val\" name=\"foto\" />
                        <div class=\"fs-06 p-2-10 brs-20px b mr-10\" id=\"comentarios-pdf\" onclick=\"comentariopdf()\">PDF</div>
                        <input type=\"hidden\" value=\"0\" id=\"comentarios-pdf-val\" name=\"pdf\" />
                        <div class=\"fs-06 p-2-10 brs-20px b mr-10\" id=\"comentarios-video\" onclick=\"comentariovideo()\">Vídeos</div>
                        <input type=\"hidden\" value=\"0\" id=\"comentarios-video-val\" name=\"video\" />
                    </div>
                </div>
            </div>";
	if($tql2&&$tqlnomepost&&$tqlfoto&&$tql4&&$tql5&&$tql6){
		$retorno = array('cod' => 1, 'html' => $html);
		echo json_encode($retorno);
		exit();
	}else{
		$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Fórum não encontrado</div>");
		echo json_encode($retorno);
		exit();
	}
}

/* Eventos */
if($_POST["f"]=="meuseventos"){
	$ID = $_POST["usuario"];
	if(!$ID){$ID = $ID2;}
	$sqlnome = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$ID."'";
	$tqlnome = mysqli_query($serve,$sqlnome);
	$uqlnome = mysqli_fetch_array($tqlnome);
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$pesquisa = $_POST["pesquisa"];
	$html = "";
	$pesquisahtml = "";
	if($pesquisa){
		$pesquisahtml .= "Resultado da pesquisa para \"".$pesquisa."\"";
		/* Verifica o número total de posts seguidos pelo perfil logado que combinam com a pesquisa */
		$sqlN = "SELECT count(A.ACA_PosID) N FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Evento' AND A.ACA_UsuID = '".$ID."' AND (B.POS_Titulo LIKE '%".$pesquisa."%' OR B.POS_Resumo LIKE '%".$pesquisa."%')";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		/* Pega o ID dos foruns seguidos pelo perfil logado que combinam com a pesquisa que deverão aparecer na página de consulta solicitada */
		$sql = "SELECT A.ACA_PosID FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Evento' AND A.ACA_UsuID = '".$ID."' AND (B.POS_Titulo LIKE '%".$pesquisa."%' OR B.POS_Resumo LIKE '%".$pesquisa."%') ORDER BY A.ACA_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}else{
		/* Verifica o número total de posts seguidos pelo perfil logado */
		$sqlN = "SELECT count(A.ACA_PosID) N FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Evento' AND A.ACA_UsuID = '".$ID."'";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		/* Pega o ID dos foruns seguidos pelo perfil logado que deverão aparecer na página de consulta solicitada */
		$sql = "SELECT A.ACA_PosID FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Evento' AND A.ACA_UsuID = '".$ID."' ORDER BY A.ACA_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}
	if ($total>0){
		/* Percorre um por um a lista das pessoas seguidas pelo perfil logado escolhidas para exibição */
		while($uql = mysqli_fetch_array($tql)){
			$idpost = $uql["ACA_PosID"];
			/* Pega os dados do post seguido percorrido */
			$sql2 = "SELECT POS_Arquivo, POS_Titulo, POS_Resumo FROM posts WHERE POS_RowID = '".$idpost."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			if(!$uql2["POS_Arquivo"]){
				$foto = $server."upload/evento.jpg";
			}else{
				$foto = $server."upload/".$uql2["POS_Arquivo"];
			}
			/* Pega a data do último comentário */
			$sql3 = "SELECT ACA_Data FROM acao WHERE ACA_PosPaiID = '".$idpost."' ORDER BY ACA_Data DESC LIMIT 1";
			$tql3 = mysqli_query($serve,$sql3);
			$Ncomentarios = mysqli_num_rows($tql3);
			$atualizado = "";
			if($Ncomentarios>0){
				$uql3 = mysqli_fetch_array($tql3);
				$data = date_format(date_create($uql3["ACA_Data"]), 'd/m H:i');
				$atualizado = "<div class=\"fs-05 c4\">Atualizado em ".$data."</div>";
			}
			$dados = explode(";", $uql2["POS_Resumo"]);
			$data = explode(":", $dados[0]);
			$local = explode(":", $dados[1]);
			$ruacidade = explode(" / ", $local[1]);
			/* Monta o bloco de informações de exibição do tópico percorrido */
			$html .= "<div onclick=\"window.location = 'evento.html?id=".$idpost."';\" class=\"mnw-120 mxw-120 mr-10\">
						<div class=\"d-flex flex-column p-10 b brs-20px no-overflow\">
	                        <div class=\"imgtopico\"><img src=\"".$foto."\" class=\"mxw-120 mxh-120\"></div>
	                        <div class=\"fs-08 mt-10\">".$uql2["POS_Titulo"]."</div>
	                        <div class=\"fs-07 c4\">".$ruacidade[1]."</div>
	                        <div class=\"fs-08 c4\">".$data[1]."</div>
	                        ".$atualizado."
	                    </div>
	                </div>";
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'html' => $html, 'proxima' => $proxima, 'numero' => $Ntotal, 'resultado' => $pesquisahtml, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhum evento confirmado</div>", 'numero' => 0, 'resultado' => $pesquisahtml, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}
if($_POST["f"]=="seguindoeventos"){
	$ID = $_POST["usuario"];
	if(!$ID){$ID = $ID2;}
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$sqlnome = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$ID."'";
	$tqlnome = mysqli_query($serve,$sqlnome);
	$uqlnome = mysqli_fetch_array($tqlnome);
	$sqlseguindo = "SELECT ACA_Usu2ID FROM acao WHERE ACA_Acao = 'Seguir' AND ACA_UsuID = '".$ID."'";
	$tqlseguindo = mysqli_query($serve,$sqlseguindo);
	$Nseguindo = mysqli_num_rows($tqlseguindo);
	$pesquisa = $_POST["pesquisa"];
	$html = "";
	$pesquisahtml = "";
	if($Nseguindo>0){
		$Ntotal = 0;
		while($uqlseguindo = mysqli_fetch_array($tqlseguindo)){
			$idseguindo = $uqlseguindo["ACA_Usu2ID"];
			if($pesquisa){
				$pesquisahtml = "Resultado da pesquisa para \"".$pesquisa."\"";
				$sqlN = "SELECT count(A.ACA_PosID) N FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Evento' AND A.ACA_UsuID = '".$idseguindo."' AND (B.POS_Titulo LIKE '%".$pesquisa."%' OR B.POS_Resumo LIKE '%".$pesquisa."%')";
				$tqlN = mysqli_query($serve,$sqlN);
				$uqlN = mysqli_fetch_array($tqlN);
				$Ntotal = $Ntotal+$uqlN["N"];
				$Nporpag = 50;
				$Npag = ceil($Ntotal/$Nporpag);
				$proxima = $pag>=$Npag?false:$pag+1;
				$L0 = ($pag - 1) * $Nporpag;
				$L1 = $L0 + $Nporpag;
				$sql = "SELECT A.ACA_PosID FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Evento' AND A.ACA_UsuID = '".$idseguindo."' AND (B.POS_Titulo LIKE '%".$pesquisa."%' OR B.POS_Resumo LIKE '%".$pesquisa."%') ORDER BY A.ACA_Data DESC LIMIT ".$L0.",".$L1;
				$tql = mysqli_query($serve,$sql);
				$total = mysqli_num_rows($tql);
			}else{
				$sqlN = "SELECT count(A.ACA_PosID) N FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Evento' AND A.ACA_UsuID = '".$idseguindo."'";
				$tqlN = mysqli_query($serve,$sqlN);
				$uqlN = mysqli_fetch_array($tqlN);
				$Ntotal = $Ntotal+$uqlN["N"];
				$Nporpag = 50;
				$Npag = ceil($Ntotal/$Nporpag);
				$proxima = $pag>=$Npag?false:$pag+1;
				$L0 = ($pag - 1) * $Nporpag;
				$L1 = $L0 + $Nporpag;
				$sql = "SELECT A.ACA_PosID FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Evento' AND A.ACA_UsuID = '".$idseguindo."' ORDER BY A.ACA_Data DESC LIMIT ".$L0.",".$L1;
				$tql = mysqli_query($serve,$sql);
				$total = mysqli_num_rows($tql);
			}
			if ($total>0){
				while($uql = mysqli_fetch_array($tql)){
					$idpost = $uql["ACA_PosID"];
					$sql2 = "SELECT POS_Arquivo, POS_Titulo, POS_Resumo FROM posts WHERE POS_RowID = '".$idpost."'";
					$tql2 = mysqli_query($serve,$sql2);
					$uql2 = mysqli_fetch_array($tql2);
					if(!$uql2["POS_Arquivo"]){
						$foto = $server."upload/evento.jpg";
					}else{
						$foto = $server."upload/".$uql2["POS_Arquivo"];
					}
					$sql3 = "SELECT ACA_Data FROM acao WHERE ACA_PosPaiID = '".$idpost."' ORDER BY ACA_Data DESC LIMIT 1";
					$tql3 = mysqli_query($serve,$sql3);
					$Ncomentarios = mysqli_num_rows($tql3);
					$atualizado = "";
					if($Ncomentarios>0){
						$uql3 = mysqli_fetch_array($tql3);
						$data = date_format(date_create($uql3["ACA_Data"]), 'd/m H:i');
						$atualizado = "<div class=\"fs-05 c4\">Atualizado em ".$data."</div>";
					}
					$dados = explode(";", $uql2["POS_Resumo"]);
					$data = explode(":", $dados[0]);
					$local = explode(":", $dados[1]);
					$ruacidade = explode(" / ", $local[1]);
					$html .= "<div onclick=\"window.location = 'evento.html?id=".$idpost."';\" class=\"mnw-120 mxw-120 mr-10\">
								<div class=\"d-flex flex-column p-10 b brs-20px no-overflow\">
			                        <div class=\"imgtopico\"><img src=\"".$foto."\" class=\"mxw-120 mxh-120\"></div>
			                        <div class=\"fs-08 mt-10\">".$uql2["POS_Titulo"]."</div>
	                        <div class=\"fs-07 c4\">".$ruacidade[1]."</div>
	                        <div class=\"fs-08 c4\">".$data[1]."</div>
			                        ".$atualizado."
			                    </div>
			                </div>";
				}
			}
		}
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhum evento confirmado por pessoas seguidas</div>", 'numero' => 0, 'resultado' => $pesquisahtml);
			echo json_encode($retorno);
			exit();
		}else{
			if($tql&&$tql2&&$tql3){
				$retorno = array('cod' => 1, 'html' => $html, 'proxima' => $proxima, 'numero' => $Ntotal, 'resultado' => $pesquisahtml);
				echo json_encode($retorno);
				exit();
			}else{
				$retorno = array('cod' => 2);
				echo json_encode($retorno);
				exit();
			}
		}
	}else{
		$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhuma pessoa seguida</div>", 'numero' => 0);
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="populareseventos"){
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$pesquisa = $_POST["pesquisa"];
	$html = "";
	$pesquisahtml = "";
	if($pesquisa){
		$pesquisahtml .= "Resultado da pesquisa para \"".$pesquisa."\"";
		$sql = "SELECT B.POS_RowID, count(A.ACA_RowID) N FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Evento' AND B.POS_Titulo LIKE '%".$pesquisa."%' ORDER BY N DESC LIMIT 20";
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}else{
		$sql = "SELECT B.POS_RowID, count(A.ACA_RowID) N FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Evento' ORDER BY N DESC LIMIT 20";
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}
	if ($total>0&&$uql["N"]>0){
		while($uql = mysqli_fetch_array($tql)){
			$idpost = $uql["POS_RowID"];
			$sql2 = "SELECT POS_Arquivo, POS_Titulo, POS_Resumo FROM posts WHERE POS_RowID = '".$idpost."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			if(!$uql2["POS_Arquivo"]){
				$foto = $server."upload/evento.jpg";
			}else{
				$foto = $server."upload/".$uql2["POS_Arquivo"];
			}
			$sql3 = "SELECT ACA_Data FROM acao WHERE ACA_PosPaiID = '".$idpost."' ORDER BY ACA_Data DESC LIMIT 1";
			$tql3 = mysqli_query($serve,$sql3);
			$Ncomentarios = mysqli_num_rows($tql3);
			$atualizado = "";
			if($Ncomentarios>0){
				$uql3 = mysqli_fetch_array($tql3);
				$data = date_format(date_create($uql3["ACA_Data"]), 'd/m H:i');
				$atualizado = "<div class=\"fs-05 c4\">Atualizado em ".$data."</div>";
			}
			$dados = explode(";", $uql2["POS_Resumo"]);
			$data = explode(":", $dados[0]);
			$local = explode(":", $dados[1]);
			$ruacidade = explode(" / ", $local[1]);
			$html .= "<div onclick=\"window.location = 'evento.html?id=".$idpost."';\" class=\"mnw-120 mxw-120 mr-10\">
						<div class=\"d-flex flex-column p-10 b brs-20px no-overflow\">
	                        <div class=\"imgtopico\"><img src=\"".$foto."\" class=\"mxw-120 mxh-120\"></div>
	                        <div class=\"fs-08 mt-10\">".$uql2["POS_Titulo"]."</div>
	                        <div class=\"fs-07 c4\">".$ruacidade[1]."</div>
	                        <div class=\"fs-08 c4\">".$data[1]."</div>
	                        ".$atualizado."
	                    </div>
	                </div>";
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'html' => $html, 'proxima' => $proxima, 'numero' => $Ntotal, 'resultado' => $pesquisahtml);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhum evento disponível</div>", 'numero' => 0, 'resultado' => $pesquisahtml);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}
if($_POST["f"]=="novoevento"){
	if(!$ID){$ID = $ID2;}
	if(!empty($_POST["titulo"])){$POS_Titulo = $_POST["titulo"];}else{echo json_encode(array('cod' => 0, 'ide' => 'titulo'));exit();}
	if(!empty($_POST["tipo"])){$tipo = $_POST["tipo"];}else{echo json_encode(array('cod' => 0, 'ide' => 'tipo'));exit();}
	if(!empty($_POST["data"])){$data = $_POST["data"];}else{echo json_encode(array('cod' => 0, 'ide' => 'data'));exit();}
	if(!empty($_POST["estado"])){$est = explode(";", $_POST["estado"]);$estado = $est[1];}else{echo json_encode(array('cod' => 0, 'ide' => 'estado'));exit();}
	if(!empty($_POST["cidade"])){$cidade = $_POST["cidade"];}else{echo json_encode(array('cod' => 0, 'ide' => 'cidade'));exit();}
	if(!empty($_POST["local"])){$local = $_POST["local"];}else{echo json_encode(array('cod' => 0, 'ide' => 'local'));exit();}
	$POS_Resumo = "DATA:".date("d/m/Y", strtotime($data)).";LOCAL:".$local." / ".$cidade." - ".$estado.";TIPO:".$tipo;
	if(!empty($_POST["descricao"])){$POS_Descricao = $_POST["descricao"];}else{echo json_encode(array('cod' => 0, 'ide' => 'descricao'));exit();}
	$POS_Arquivo = $_POST["arquivo"]?"'".$_POST["arquivo"]."'":'null';
	if(!empty($_POST["status"])){$POS_Status = $_POST["status"];}else{echo json_encode(array('cod' => 0, 'ide' => 'status'));exit();}
	mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);
	$sql = "INSERT INTO posts (POS_Tipo, POS_UsuID, POS_Titulo, POS_Resumo, POS_Descricao, POS_Arquivo, POS_Status) VALUES ('Evento', '".$ID."', '".$POS_Titulo."', '".$POS_Resumo."', '".$POS_Descricao."', ".$POS_Arquivo.", '".$POS_Status."')";
	$tql = mysqli_query($serve,$sql);
	$last_insert_id = mysqli_insert_id($serve);
	$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_PosID) VALUES ('Postar', '".$ID."', '".$last_insert_id."')";
	$tql2 = mysqli_query($serve,$sql2);
	$sql3 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_PosID) VALUES ('Seguir', '".$ID."', '".$last_insert_id."')";
	$tql3 = mysqli_query($serve,$sql3);
	if($tql&&$tql2&&$tql3){
		mysqli_commit($serve);
		$retorno = array('cod' => 1, 'id' => $last_insert_id);
		echo json_encode($retorno);
		exit();
	}else{
		mysqli_rollback($serve);
		$retorno = array('cod' => 2, 'msg' => 'ERRO - Não foi possível cadastrar agora');
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="evento"){
	if(!$ID){$ID = $ID2;}
	$idpost = $_POST["id"];
	/* Pega os dados do post seguido percorrido */
	$sql2 = "SELECT POS_UsuID, POS_Arquivo, POS_Titulo, POS_Resumo, POS_Descricao FROM posts WHERE POS_RowID = '".$idpost."'";
	$tql2 = mysqli_query($serve,$sql2);
	$uql2 = mysqli_fetch_array($tql2);
	$sqlnomepost = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$uql2["POS_UsuID"]."'";
	$tqlnomepost = mysqli_query($serve,$sqlnomepost);
	$uqlnomepost = mysqli_fetch_array($tqlnomepost);
	$nome = $uqlnomepost["USU_Nome"];
	$sqlfoto = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_RowID = '".$uql2["POS_UsuID"]."'";
	$tqlfoto = mysqli_query($serve,$sqlfoto);
	$uqlfoto = mysqli_fetch_array($tqlfoto);
	if(!$uqlfoto["POS_Arquivo"]){
		$fotourl = $server."upload/avatar.jpg";
	}else{
		$fotourl = $server."upload/".$uqlfoto["POS_Arquivo"];
	}
	$fotoperfil = "<img src=\"".$fotourl."\" class=\"brs-50 mxh-46 mr-10\" height=\"46\" width=\"46\"/>";

	$sql7 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Seguir' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
	$tql7 = mysqli_query($serve,$sql7);
	$Nseguir = mysqli_num_rows($tql7);
	if($Nseguir>0){
		$seicone = "imagens/svg/feather-rss-no.svg";
		$setexto = "Deixar de participar";
	}else{
		$seicone = "imagens/svg/feather-rss.svg";
		$setexto = "Participar";
	}
	$sql5 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Compartilhar' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
	$tql5 = mysqli_query($serve,$sql5);
	$Ncompartilhar = mysqli_num_rows($tql5);
	if($Ncompartilhar>0){
		$coicone = "imagens/svg/awesome-share-alt.svg";
		$cotexto = "Compartilhado";
	}else{
		$coicone = "imagens/svg/awesome-share-alt.svg";
		$cotexto = "Compartilhar";
	}
	$denunciar = "<img class=\"f-right h-18\" src=\"imagens/svg/interface.svg\" onclick=\"modalInput('denunciar','Descreva o motivo de sua denúncia','".$idpost."','','p','s');\">";
	$titulo = $fotoperfil."<span>".$uql2["POS_Titulo"]."<br><span class=\"fs-08\">postado por ".$nome."</span></span>
		<div>".$denunciar."<button id=\"postseguir\" class=\"Mt-20 smallbtn\" onclick=\"acompanhar(".$idpost.")\">
            <img src=\"".$seicone."\" style=\"height: 12px;\">
            <span class=\"ml-5px\">".$setexto."</span>
        </button>
		<button class=\"Mt-20 smallbtn smallbtn2\" onclick=\"compartilharevento(".$idpost.")\">
            <img src=\"".$coicone."\" style=\"height: 12px;\">
            <span class=\"ml-5px\">".$cotexto."</span>
        </button></div>";
	$html = "";
	$dados = explode(";", $uql2["POS_Resumo"]);
	$data = explode(":", $dados[0]);
	$local = explode(":", $dados[1]);
	$ruacidade = explode(" / ", $local[1]);
	$resumo = "<div class=\"fs-08\">DATA: ".$data[1]."</div>
                <div class=\"fs-08\">LOCAL: ".$local[1]."</div>";
	$descricao = "<div class=\"fs-08\">".$uql2["POS_Descricao"]."</div>";		
	if(!$uql2["POS_Arquivo"]){
		$arquivo = "";
	}else{
		$foto = $server."upload/".$uql2["POS_Arquivo"];
		if(substr($uql2["POS_Arquivo"], -3)=='pdf'){
			$arquivo = "";
		}else if(substr($uql2["POS_Arquivo"], -3)=='mp4'){
			$arquivo = "<video height=\"auto\" width=\"auto\" class=\"mxw-100p\" controls><source src=\"".$foto."#t=0.00001\" type=\"video/mp4\"></video>";
		}else{
			$arquivo = "<img src=\"".$foto."\" class=\"mxw-100p\">";
		}				
	}
	/* Monta o bloco de informações de exibição do tópico percorrido */
	$html .= "<div name=\"".$idpost."\">
				<div class=\"d-flex flex-column no-overflow\">
                    <div class=\"d-flex p-rel\">".$titulo."</div>
                    <div class=\"mt-50\">".$resumo."</div>
                    <div>".$arquivo."</div>
                    <div>".$descricao."</div>
                </div>
            </div>";
	if($tql2&&$tqlnomepost&&$tqlfoto&&$tql5&&$tql7){
		$retorno = array('cod' => 1, 'html' => $html);
		echo json_encode($retorno);
		exit();
	}else{
		$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Evento não encontrado</div>");
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="participantes"){
	$idpost = $_POST["id"];
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$html = "";
	/* Verifica o número total de seguidores */
	$sqlN = "SELECT count(ACA_UsuID) N FROM acao WHERE ACA_Acao = 'Seguir' AND ACA_Usu2ID is null AND ACA_PosID = '".$idpost."'";
	$tqlN = mysqli_query($serve,$sqlN);
	$uqlN = mysqli_fetch_array($tqlN);
	$Ntotal = $uqlN["N"];
	$Nporpag = 50;
	$Npag = ceil($Ntotal/$Nporpag);
	$proxima = $pag>=$Npag?false:$pag+1;
	$L0 = ($pag - 1) * $Nporpag;
	$L1 = $L0 + $Nporpag;
	/* Pega o ID dos seguidores que deverão aparecer na página de consulta solicitada */
	$sql = "SELECT ACA_UsuID FROM acao WHERE ACA_Acao = 'Seguir' AND ACA_Usu2ID is null AND ACA_PosID = '".$idpost."' ORDER BY ACA_Data DESC LIMIT ".$L0.",".$L1;
	$tql = mysqli_query($serve,$sql);
	$total = mysqli_num_rows($tql);
	if ($total>0){
		/* Percorre um por um a lista de seguidores escolhidos para exibição */
		while($uql = mysqli_fetch_array($tql)){
			$idseguidor = $uql["ACA_UsuID"];
			/* Pega os dados textuais do seguidor percorrido */
			$sql2 = "SELECT A.USU_Nome, B.CID_Cidade, C.EST_Sigla FROM usuarios A, cidades B, estados C WHERE A.USU_RowID = '".$idseguidor."' AND A.USU_CidadeID = B.CID_RowID AND A.USU_EstadoID = C.EST_RowID";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			/* Pega a foto de perfil do seguidor percorrido */
			$sql3 = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_UsuID = '".$idseguidor."'";
			$tql3 = mysqli_query($serve,$sql3);
			$uql3 = mysqli_fetch_array($tql3);
			if(!$uql3["POS_Arquivo"]){
				$foto = $server."upload/avatar.jpg";
			}else{
				$foto = $server."upload/".$uql3["POS_Arquivo"];
			}
			/* Monta o bloco de informações de exibição do seguidor percorrido */
			$html .= "<div onclick=\"window.location = 'perfil.html?id=".$idseguidor."';\" class=\"f-left\">
						<div class=\"d-flex flex-h pt-10\">
	                        <div class=\"mr-10 w-40px\"><img src=\"".$foto."\" class=\"brs-50 mxh-40\" height=\"40\" width=\"40\"></div>
	                    </div>
	                </div>";
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'html' => $html, 'proxima' => $proxima, 'numero' => $Ntotal);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => $pesquisahtml."<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhum participante confirmado até o momento</div>", 'numero' => 0);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}

/* Oportunidades */
if($_POST["f"]=="oportunidades"){
	$ID = $_POST["usuario"];
	if(!$ID){$ID = $ID2;}
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$pesquisa = $_POST["pesquisa"];
	$html = "";
	$pesquisahtml = "";
	$i = 0;
	if($pesquisa){
		$pesquisahtml .= "Resultado da pesquisa para \"".$pesquisa."\"";
		/* Verifica o número total de posts seguidos pelo perfil logado que combinam com a pesquisa */
		$sqlN = "SELECT count(B.POS_RowID) N FROM posts B WHERE B.POS_Tipo = 'Oportunidade' AND (B.POS_Titulo LIKE '%".$pesquisa."%' OR B.POS_Resumo LIKE '%".$pesquisa."%')";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		/* Pega o ID dos foruns seguidos pelo perfil logado que combinam com a pesquisa que deverão aparecer na página de consulta solicitada */
		$sql = "SELECT B.POS_RowID FROM posts B WHERE B.POS_Tipo = 'Oportunidade' AND (B.POS_Titulo LIKE '%".$pesquisa."%' OR B.POS_Resumo LIKE '%".$pesquisa."%') ORDER BY B.POS_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}else{
		/* Verifica o número total de posts seguidos pelo perfil logado */
		$sqlN = "SELECT count(B.POS_RowID) N FROM posts B WHERE B.POS_Tipo = 'Oportunidade'";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		/* Pega o ID dos foruns seguidos pelo perfil logado que deverão aparecer na página de consulta solicitada */
		$sql = "SELECT B.POS_RowID FROM posts B WHERE B.POS_Tipo = 'Oportunidade' ORDER BY B.POS_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}
	if ($total>0){
		/* Percorre um por um a lista das pessoas seguidas pelo perfil logado escolhidas para exibição */
		while($uql = mysqli_fetch_array($tql)){
			$idpost = $uql["POS_RowID"];
			/* Pega os dados do post seguido percorrido */
			$sql2 = "SELECT POS_Arquivo, POS_Titulo, POS_Resumo, POS_UsuID FROM posts WHERE POS_RowID = '".$idpost."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			if(!$uql2["POS_Arquivo"]){
				$foto = $server."upload/forum.jpg";
				$arquivo = "<img src=\"".$foto."\" class=\"brs-50 mxh-46\" height=\"46\" width=\"46\"/>";
			}else{
				$foto = $server."upload/".$uql2["POS_Arquivo"];
				if(substr($uql2["POS_Arquivo"], -3)=='pdf'){
					$foto = $server."upload/forum.jpg";
					$arquivo = "<img src=\"".$foto."\" class=\"mxw-120 mxh-120\">";
				}else if(substr($uql2["POS_Arquivo"], -3)=='mp4'){
					$arquivo = "<video height=\"auto\" width=\"auto\" class=\"mxw-120 mxh-120\"><source src=\"".$foto."#t=0.00001\" type=\"video/mp4\"></video>";
				}else{
					$arquivo = "<img src=\"".$foto."\" class=\"brs-50 mxh-46\" height=\"46\" width=\"46\"/>";
				}				
			}
			$sqlnome = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$uql2["POS_UsuID"]."'";
			$tqlnome = mysqli_query($serve,$sqlnome);
			$uqlnome = mysqli_fetch_array($tqlnome);
			/* Pega a data do último comentário */
			$dados = explode(";", $uql2["POS_Resumo"]);
			$valor = explode(":", $dados[0]);
			$valortotal = $valor[1];
			$valortotalF = number_format($valor[1], 2, ',', '.');
			$sql3 = "SELECT SUM(B.POS_Resumo) investido FROM acao A, posts B WHERE A.ACA_Acao = 'Investir' AND A.ACA_PosPaiID = '".$idpost."' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Dinheiro' ORDER BY ACA_Data DESC LIMIT 1";
			$tql3 = mysqli_query($serve,$sql3);
			$uql3 = mysqli_fetch_array($tql3);
			$investido = $uql3["investido"]?$uql3["investido"]:"0";
			$investidoF = number_format($investido, 2, ',', '.');;
			$porcentagem = ($investido/$valortotal)*100;
			$i++;
			if($i==$total){
				$classe = "";
			}else{
				$classe = "mb-10 mt-10 pb-10 bb";
			}
			/* Monta o bloco de informações de exibição do tópico percorrido */
            $html .= "<div onclick=\"window.location = 'oportunidade.html?id=".$idpost."';\" class=\"".$classe."\">
				<div class=\"d-flex no-overflow\">
                    <div class=\"mr-10 mxw-46\">".$arquivo."</div>
					<div class=\"d-flex flex-column no-overflow\">
                        <div class=\"fs-08\">".$uql2["POS_Titulo"]."</div>
                        <div class=\"fs-07 c4\">Postado por ".$uqlnome["USU_Nome"]."</div>
                        <div class=\"fs-07 c4\">R$".$investidoF." / R$".$valortotalF."</div>
                        <div class=\"upload-barra-pro mt-10 mr-10\"><div class=\"upload-progresso-pro\" style=\"width:".$porcentagem."%\"></div></div>
                    </div>
                    <div class=\"w-30px\" style=\"position:relative;\"><img src=\"imagens/svg/awesome-chevron-circle-left.svg\" style=\"position:absolute;top:50%;transform:translateY(-50%) rotate(180deg);\"/></div>
                </div>
            </div>";
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'html' => $pesquisahtml.$html, 'proxima' => $proxima, 'numero' => $Ntotal, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => $pesquisahtml."<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhuma oportunidade disponível</div>", 'numero' => 0, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}
if($_POST["f"]=="novaoportunidade"){
	if(!$ID){$ID = $ID2;}
	if(!empty($_POST["titulo"])){$POS_Titulo = $_POST["titulo"];}else{echo json_encode(array('cod' => 0, 'ide' => 'titulo'));exit();}
	if(!empty($_POST["valor"])){$valor = $_POST["valor"];}else{echo json_encode(array('cod' => 0, 'ide' => 'valor'));exit();}
	if(!empty($_POST["prazo"])){$prazo = $_POST["prazo"];}else{echo json_encode(array('cod' => 0, 'ide' => 'prazo'));exit();}
	if(!empty($_POST["pessoas"])){$pessoas = $_POST["pessoas"];}else{echo json_encode(array('cod' => 0, 'ide' => 'pessoas'));exit();}
	$POS_Resumo = "VALOR:".$valor.";PRAZO:".$prazo.";PESSOAS:".$pessoas;
	if(!empty($_POST["descricao"])){$POS_Descricao = $_POST["descricao"];}else{echo json_encode(array('cod' => 0, 'ide' => 'descricao'));exit();}
	$POS_Arquivo = $_POST["arquivo"]?"'".$_POST["arquivo"]."'":'null';
	mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);
	$sql = "INSERT INTO posts (POS_Tipo, POS_UsuID, POS_Titulo, POS_Resumo, POS_Descricao, POS_Arquivo) VALUES ('Oportunidade', '".$ID."', '".$POS_Titulo."', '".$POS_Resumo."', '".$POS_Descricao."', ".$POS_Arquivo.")";
	$tql = mysqli_query($serve,$sql);
	$last_insert_id = mysqli_insert_id($serve);
	$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_PosID) VALUES ('Postar', '".$ID."', '".$last_insert_id."')";
	$tql2 = mysqli_query($serve,$sql2);
	if($tql&&$tql2){
		mysqli_commit($serve);
		$retorno = array('cod' => 1, 'id' => $last_insert_id);
		echo json_encode($retorno);
		exit();
	}else{
		mysqli_rollback($serve);
		$retorno = array('cod' => 2, 'msg' => 'ERRO - Não foi possível cadastrar agora');
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="oportunidade"){
	if(!$ID){$ID = $ID2;}
	$idpost = $_POST["id"];
	$sql2 = "SELECT POS_Arquivo, POS_Titulo, POS_Resumo, POS_Descricao, POS_UsuID FROM posts WHERE POS_RowID = '".$idpost."'";
	$tql2 = mysqli_query($serve,$sql2);
	$uql2 = mysqli_fetch_array($tql2);
	if(!$uql2["POS_Arquivo"]){
		$foto = $server."upload/forum.jpg";
		$arquivo = "<img src=\"".$foto."\" class=\"brs-50 mxh-46\" height=\"46\" width=\"46\"/>";
	}else{
		$foto = $server."upload/".$uql2["POS_Arquivo"];
		if(substr($uql2["POS_Arquivo"], -3)=='pdf'){
			$foto = $server."upload/forum.jpg";
			$arquivo = "<img src=\"".$foto."\" class=\"mxw-120 mxh-120\">";
		}else if(substr($uql2["POS_Arquivo"], -3)=='mp4'){
			$arquivo = "<video height=\"auto\" width=\"auto\" class=\"mxw-120 mxh-120\"><source src=\"".$foto."#t=0.00001\" type=\"video/mp4\"></video>";
		}else{
			$arquivo = "<img src=\"".$foto."\" class=\"brs-50 mxh-46\" height=\"46\" width=\"46\"/>";
		}				
	}
	$sqlnome = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$uql2["POS_UsuID"]."'";
	$tqlnome = mysqli_query($serve,$sqlnome);
	$uqlnome = mysqli_fetch_array($tqlnome);
	$dados = explode(";", $uql2["POS_Resumo"]);
	$valor = explode(":", $dados[0]);
	$valortotal = $valor[1];
	$valortotalF = number_format($valor[1], 2, ',', '.');
	$sql3 = "SELECT SUM(B.POS_Resumo) investido FROM acao A, posts B WHERE A.ACA_Acao = 'Investir' AND A.ACA_PosPaiID = '".$idpost."' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Dinheiro' ORDER BY ACA_Data DESC LIMIT 1";
	$tql3 = mysqli_query($serve,$sql3);
	$uql3 = mysqli_fetch_array($tql3);
	$investido = $uql3["investido"]?$uql3["investido"]:"0";
	$investidoF = number_format($investido, 2, ',', '.');;
	$porcentagem = ($investido/$valortotal)*100;
    $barra = "<div class=\"fs-07 c4\">R$".$investidoF." / R$".$valortotalF."</div>
                <div class=\"upload-barra-pro mt-10\"><div class=\"upload-progresso-pro\" style=\"width:".$porcentagem."%\"></div></div>";
    $meu = $uql2["POS_UsuID"]==$ID?"1":"0";
	if($tql2&&$tql3){
		$retorno = array('cod' => 1, 'foto' => $foto, 'titulo' => $uql2["POS_Titulo"], 'nome' => "postado por ".$uqlnome["USU_Nome"], 'barra' => $barra, 'problema' => $uql2["POS_Descricao"], 'meu' => $meu);
		echo json_encode($retorno);
		exit();
	}else{
		$retorno = array('cod' => 2);
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="solucoes"){
	$idpost = $_POST["id"];
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$html = "";
	/* Verifica o número total de seguidores */
	$sqlN = "SELECT count(A.ACA_UsuID) N FROM acao A, posts B WHERE A.ACA_Acao = 'Investir' AND A.ACA_Usu2ID is null AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Tecnologia' AND A.ACA_PosPaiID = '".$idpost."'";
	$tqlN = mysqli_query($serve,$sqlN);
	$uqlN = mysqli_fetch_array($tqlN);
	$Ntotal = $uqlN["N"];
	$Nporpag = 50;
	$Npag = ceil($Ntotal/$Nporpag);
	$proxima = $pag>=$Npag?false:$pag+1;
	$L0 = ($pag - 1) * $Nporpag;
	$L1 = $L0 + $Nporpag;
	/* Pega o ID dos seguidores que deverão aparecer na página de consulta solicitada */
	$sql = "SELECT A.ACA_UsuID, B.POS_Resumo, B.POS_Descricao FROM acao A, posts B WHERE A.ACA_Acao = 'Investir' AND A.ACA_Usu2ID is null AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Tecnologia' AND A.ACA_PosPaiID = '".$idpost."' ORDER BY A.ACA_Data DESC LIMIT ".$L0.",".$L1;
	$tql = mysqli_query($serve,$sql);
	$total = mysqli_num_rows($tql);
	$i=0;
	if ($total>0){
		/* Percorre um por um a lista de seguidores escolhidos para exibição */
		while($uql = mysqli_fetch_array($tql)){
			$idinvestidor = $uql["ACA_UsuID"];
			/* Pega os dados textuais do seguidor percorrido */
			$sql2 = "SELECT A.USU_Nome, B.CID_Cidade, C.EST_Sigla FROM usuarios A, cidades B, estados C WHERE A.USU_RowID = '".$idinvestidor."' AND A.USU_CidadeID = B.CID_RowID AND A.USU_EstadoID = C.EST_RowID";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			/* Pega a foto de perfil do seguidor percorrido */
			$sql3 = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_UsuID = '".$idinvestidor."'";
			$tql3 = mysqli_query($serve,$sql3);
			$uql3 = mysqli_fetch_array($tql3);
			if(!$uql3["POS_Arquivo"]){
				$foto = $server."upload/avatar.jpg";
			}else{
				$foto = $server."upload/".$uql3["POS_Arquivo"];
			}
			$fotoperfil = "<img src=\"".$foto."\" class=\"brs-50 mxh-46\" height=\"46\" width=\"46\"/>";
			$i++;
			if($i==$total){
				if($i==1){
					$classe = "mt-10";
				}else{
					$classe = "";
				}
			}else{
				$classe = "mb-10 mt-10 pb-10 bb";
			}
			/* Monta o bloco de informações de exibição do seguidor percorrido */
            $html .= "<div class=\"".$classe."\">
				<div class=\"d-flex no-overflow\">
                    <div onclick=\"window.location = 'perfil.html?id=".$idinvestidor."';\" class=\"mr-10 mxw-46\">".$fotoperfil."</div>
					<div class=\"d-flex flex-column no-overflow\">
                        <div class=\"fs-08\">".$uql["POS_Descricao"]."</div>
                        <div class=\"fs-08 c4\">Prazo: ".$uql["POS_Resumo"]."</div>
                    </div>
                </div>
            </div>";
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'html' => $html, 'proxima' => $proxima, 'numero' => $Ntotal);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 fs-08 c5 textcenter\">Nenhuma solução tecnológica proposta até o momento</div>", 'numero' => 0);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}
if($_POST["f"]=="investidores"){
	$idpost = $_POST["id"];
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$html = "";
	/* Verifica o número total de seguidores */
	$sqlN = "SELECT count(A.ACA_UsuID) N FROM acao A, posts B WHERE A.ACA_Acao = 'Investir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Dinheiro' AND A.ACA_Usu2ID is null AND A.ACA_PosPaiID = '".$idpost."'";
	$tqlN = mysqli_query($serve,$sqlN);
	$uqlN = mysqli_fetch_array($tqlN);
	$Ntotal = $uqlN["N"];
	$Nporpag = 50;
	$Npag = ceil($Ntotal/$Nporpag);
	$proxima = $pag>=$Npag?false:$pag+1;
	$L0 = ($pag - 1) * $Nporpag;
	$L1 = $L0 + $Nporpag;
	/* Pega o ID dos seguidores que deverão aparecer na página de consulta solicitada */
	$sql = "SELECT A.ACA_UsuID FROM acao A, posts B WHERE A.ACA_Acao = 'Investir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Dinheiro' AND A.ACA_Usu2ID is null AND A.ACA_PosPaiID = '".$idpost."' ORDER BY A.ACA_Data DESC LIMIT ".$L0.",".$L1;
	$tql = mysqli_query($serve,$sql);
	$total = mysqli_num_rows($tql);
	if ($total>0){
		/* Percorre um por um a lista de seguidores escolhidos para exibição */
		while($uql = mysqli_fetch_array($tql)){
			$idinvestidor = $uql["ACA_UsuID"];
			/* Pega os dados textuais do seguidor percorrido */
			$sql2 = "SELECT A.USU_Nome, B.CID_Cidade, C.EST_Sigla FROM usuarios A, cidades B, estados C WHERE A.USU_RowID = '".$idinvestidor."' AND A.USU_CidadeID = B.CID_RowID AND A.USU_EstadoID = C.EST_RowID";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			/* Pega a foto de perfil do seguidor percorrido */
			$sql3 = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_UsuID = '".$idinvestidor."'";
			$tql3 = mysqli_query($serve,$sql3);
			$uql3 = mysqli_fetch_array($tql3);
			if(!$uql3["POS_Arquivo"]){
				$foto = $server."upload/avatar.jpg";
			}else{
				$foto = $server."upload/".$uql3["POS_Arquivo"];
			}
			/* Monta o bloco de informações de exibição do seguidor percorrido */
			$html .= "<div onclick=\"window.location = 'perfil.html?id=".$idinvestidor."';\" class=\"f-left\">
						<div class=\"d-flex flex-h pt-10\">
	                        <div class=\"mr-10 w-40px\"><img src=\"".$foto."\" class=\"brs-50 mxh-40\" height=\"40\" width=\"40\"></div>
	                    </div>
	                </div>";
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'html' => $html, 'proxima' => $proxima, 'numero' => $Ntotal);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 fs-08 c5 textcenter\">Nenhum investidor interessado até o momento</div>", 'numero' => 0);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}

/* Podcasts */
if($_POST["f"]=="podcasts"){
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$pesquisa = $_POST["pesquisa"];
	$html = "";
	$pesquisahtml = "";
	if($pesquisa){
		$pesquisahtml .= "Resultado da pesquisa para \"".$pesquisa."\"";
		$sqlN = "SELECT count(POS_RowID) N FROM posts WHERE POS_Tipo = 'Canal' AND (POS_Titulo LIKE '%".$pesquisa."%')";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		$sql = "SELECT POS_RowID FROM posts WHERE POS_Tipo = 'Canal' AND (POS_Titulo LIKE '%".$pesquisa."%') ORDER BY POS_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}else{
		$sqlN = "SELECT count(POS_RowID) N FROM posts WHERE POS_Tipo = 'Canal'";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		$sql = "SELECT POS_RowID FROM posts WHERE POS_Tipo = 'Canal' ORDER BY POS_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
	}
	if ($total>0){
		while($uql = mysqli_fetch_array($tql)){
			$idpost = $uql["POS_RowID"];
			$sql2 = "SELECT POS_Arquivo, POS_Titulo, POS_UsuID FROM posts WHERE POS_RowID = '".$idpost."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			if(!$uql2["POS_Arquivo"]){
				$foto = $server."upload/podcast.jpg";
			}else{
				$foto = $server."upload/".$uql2["POS_Arquivo"];
			}
			$sql3 = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$uql2["POS_UsuID"]."'";
			$tql3 = mysqli_query($serve,$sql3);
			$uql3 = mysqli_fetch_array($tql3);
			$sql8 = "SELECT ACA_PosID FROM acao WHERE ACA_Acao = 'Avaliar' AND ACA_PosPaiID = '".$idpost."'";
			$tql8 = mysqli_query($serve,$sql8);
			$Nnotas = mysqli_num_rows($tql8);
			$nota = 0;
			if ($Nnotas>0){
				while($uql8 = mysqli_fetch_array($tql8)){
					$sql9 = "SELECT POS_Resumo FROM posts WHERE POS_Tipo = 'Nota' AND POS_RowID = '".$uql8["ACA_PosID"]."'";
					$tql9 = mysqli_query($serve,$sql9);
					$uql9 = mysqli_fetch_array($tql9);
					$nota = $nota + $uql9["POS_Resumo"];
				}
				$notafinal = $nota / $Nnotas;
			}else{
				$notafinal = 0;
			}
			$s0 = "<img src=\"imagens/svg/feather-star2.svg\" class=\"h-14\">";
			$s1 = "<img src=\"imagens/svg/feather-star3.svg\" class=\"h-14\">";	
			if($notafinal<0.5){
				$avaliacao = "<div class=\"mt--3\">".$s0.$s0.$s0.$s0.$s0."</div>";
			}
			if($notafinal>=0.5){
				$avaliacao = "<div class=\"mt--3\">".$s1.$s0.$s0.$s0.$s0."</div>";
			}
			if($notafinal>=1.5){
				$avaliacao = "<div class=\"mt--3\">".$s1.$s1.$s0.$s0.$s0."</div>";
			}
			if($notafinal>=2.5){
				$avaliacao = "<div class=\"mt--3\">".$s1.$s1.$s1.$s0.$s0."</div>";
			}
			if($notafinal>=3.5){
				$avaliacao = "<div class=\"mt--3\">".$s1.$s1.$s1.$s1.$s0."</div>";
			}
			if($notafinal>=4.5){
				$avaliacao = "<div class=\"mt--3\">".$s1.$s1.$s1.$s1.$s1."</div>";
			}
			$html .= "<div onclick=\"window.location = 'podcast.html?id=".$idpost."';\" class=\"canal\">
						<div class=\"d-flex flex-column no-overflow text-center\">
	                        <div class=\"imgcanal\"><img src=\"".$foto."\" class=\"mxw-100 mxh-100\" height=\"100\" width=\"100\"></div>
	                        <div class=\"fs-08\">".$uql2["POS_Titulo"]."</div>
	                        <div class=\"fs-07 c4\">".$uql3["USU_Nome"]."</div>
	                        ".$avaliacao."
	                    </div>
	                </div>";
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'html' => $html, 'proxima' => $proxima, 'numero' => $Ntotal, 'resultado' => $pesquisahtml);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhum fórum privado</div>", 'numero' => 0, 'resultado' => $pesquisahtml);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}
if($_POST["f"]=="podcast"){
	if(!$ID){$ID = $ID2;}
	$idcanal = $_POST["canal"];
	$sql3 = "SELECT POS_Titulo, POS_Arquivo, POS_UsuID FROM posts WHERE POS_RowID = '".$idcanal."'";
	$tql3 = mysqli_query($serve,$sql3);
	$uql3 = mysqli_fetch_array($tql3);
	$sql4 = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$uql3["POS_UsuID"]."'";
	$tql4 = mysqli_query($serve,$sql4);
	$uql4 = mysqli_fetch_array($tql4);
	$nomecanal = $uql3["POS_Titulo"];
	$autorcanal = $uql4["USU_Nome"];
	$sql8 = "SELECT ACA_PosID FROM acao WHERE ACA_Acao = 'Avaliar' AND ACA_PosPaiID = '".$idcanal."'";
	$tql8 = mysqli_query($serve,$sql8);
	$Nnotas = mysqli_num_rows($tql8);
	$nota = 0;
	if ($Nnotas>0){
		while($uql8 = mysqli_fetch_array($tql8)){
			$sql9 = "SELECT POS_Resumo FROM posts WHERE POS_Tipo = 'Nota' AND POS_RowID = '".$uql8["ACA_PosID"]."'";
			$tql9 = mysqli_query($serve,$sql9);
			$uql9 = mysqli_fetch_array($tql9);
			$nota = $nota + $uql9["POS_Resumo"];
		}
		$notafinal = $nota / $Nnotas;
	}else{
		$notafinal = 0;
	}
	$s0 = "<img src=\"imagens/svg/feather-star2.svg\">";
	$s1 = "<img src=\"imagens/svg/feather-star3.svg\">";	
	if($notafinal<0.5){
		$avaliacao = "<div>".$s0.$s0.$s0.$s0.$s0."</div>";
	}
	if($notafinal>=0.5){
		$avaliacao = "<div>".$s1.$s0.$s0.$s0.$s0."</div>";
	}
	if($notafinal>=1.5){
		$avaliacao = "<div>".$s1.$s1.$s0.$s0.$s0."</div>";
	}
	if($notafinal>=2.5){
		$avaliacao = "<div>".$s1.$s1.$s1.$s0.$s0."</div>";
	}
	if($notafinal>=3.5){
		$avaliacao = "<div>".$s1.$s1.$s1.$s1.$s0."</div>";
	}
	if($notafinal>=4.5){
		$avaliacao = "<div>".$s1.$s1.$s1.$s1.$s1."</div>";
	}
	if(!$uql3["POS_Arquivo"]){
		$fotocanal = $server."upload/podcast.jpg";
	}else{
		$fotocanal = $server."upload/".$uql3["POS_Arquivo"];
	}
	$canaldados = "<div class=\"d-flex flex-column no-overflow\">
                    <div>".$nomecanal."</div>
                    <div class=\"fs-09 c4\">".$autorcanal."</div>
                    ".$avaliacao."
                </div>";
	$sql6 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Salvar' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idcanal."'";
	$tql6 = mysqli_query($serve,$sql6);
	$Nsalvar = mysqli_num_rows($tql6);
	if($Nsalvar>0){
		$stexto = "Descartar";
	}else{
		$stexto = "Salvar";
	}
	$sql5 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Compartilhar' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idcanal."'";
	$tql5 = mysqli_query($serve,$sql5);
	$Ncompartilhar = mysqli_num_rows($tql5);
	if($Ncompartilhar>0){
		$cotexto = "Compartilhado";
	}else{
		$cotexto = "Compartilhar";
	}
	$sql7 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Avaliar' AND ACA_UsuID = '".$ID."' AND ACA_PosPaiID = '".$idcanal."'";
	$tql7 = mysqli_query($serve,$sql7);
	$Navaliar = mysqli_num_rows($tql7);
	if($Navaliar>0){
		$avtexto = "Avaliado";
	}else{
		$avtexto = "Avaliar";
	}
	$html = "";
	$sqlO = "SELECT B.POS_Titulo, B.POS_Arquivo, B.POS_UsuID FROM acao A, posts B WHERE A.ACA_PosPaiID = '".$idcanal."' AND A.ACA_Acao = 'Postar' AND B.POS_Tipo = 'Podcast' AND A.ACA_PosID = B.POS_RowID ORDER BY B.POS_Data DESC";
	$tqlO = mysqli_query($serve,$sqlO);
	$total = mysqli_num_rows($tqlO);
	$i = 0;
	if ($total>0){
		while($uqlO = mysqli_fetch_array($tqlO)){
			$i++;
			if($i==$total){
				$classe = "d-flex mt-10";
			}else{
				$classe = "d-flex mb-10 mt-10 pb-10 bb";
			}
			$idusuario = $uqlO["POS_UsuID"];
			/* Pega o nome do usuario */
			$sql2 = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$idusuario."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			if($uqlO["POS_Arquivo"]){
				$html .= "<div onclick=\"abrirplayer('".$uqlO["POS_Arquivo"]."','".$uqlO["POS_Titulo"]."','".$uql2["USU_Nome"]."')\" class=\"".$classe."\">
							<div class=\"d-flex flex-column no-overflow\">
		                        <div class=\"fs-08\">".$uqlO["POS_Titulo"]."</div>
		                        <div class=\"fs-07 c4\">".$uql2["USU_Nome"]."</div>
		                    </div>
		                    <div class=\"w-30px\"><img src=\"imagens/svg/awesome-play-circle.svg\"/></div>
		                </div>";
			}
		}
		if($tqlO&&$tql2){
			$retorno = array('cod' => 1, 'html' => $html, 'nomecanal' => $nomecanal, 'fotocanal' => $fotocanal, 'canaldados' => $canaldados, 'salvar' => $stexto, 'compartilhar' => $cotexto, 'avaliar' => $avtexto, 'numero' => $total);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($total==0&&!$msg){			
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhum podcast neste canal</div>", 'nomecanal' => $nomecanal, 'fotocanal' => $fotocanal, 'canaldados' => $canaldados, 'salvar' => $stexto, 'compartilhar' => $cotexto, 'avaliar' => $avtexto, 'numero' => 0);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}

/* Notificações */
if($_POST["f"]=="notificacoes"){
	$ID = $_POST["usuario"];
	if(!$ID){$ID = $ID2;}
	$sqlnome = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$ID."'";
	$tqlnome = mysqli_query($serve,$sqlnome);
	$uqlnome = mysqli_fetch_array($tqlnome);
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$html = "";
	$idjavascript = array();
		/* Verifica o número total de posts seguidos pelo perfil logado */
		$sqlN = "SELECT COUNT(*) N FROM (
			SELECT DISTINCT B.POS_RowID FROM
			acao A, posts B, acao C WHERE
			C.ACA_UsuID = '".$ID."' AND C.ACA_Acao = 'Postar' AND
			C.ACA_PosID = B.POS_RowID AND (B.POS_Tipo = 'Forum' OR B.POS_Tipo = 'Evento' OR B.POS_Tipo = 'Canal') AND
			B.POS_RowID = A.ACA_PosPaiID AND A.ACA_Acao = 'Comentar'
		) x";
		$tqlN = mysqli_query($serve,$sqlN);
		$uqlN = mysqli_fetch_array($tqlN);
		$Ntotal = $uqlN["N"];
		$Nporpag = 50;
		$Npag = ceil($Ntotal/$Nporpag);
		$proxima = $pag>=$Npag?false:$pag+1;
		$L0 = ($pag - 1) * $Nporpag;
		$L1 = $L0 + $Nporpag;
		/* Pega o ID dos foruns seguidos pelo perfil logado que deverão aparecer na página de consulta solicitada */
		$sql = "SELECT DISTINCT B.POS_RowID, B.POS_Tipo tipo FROM
		acao A, posts B, acao C WHERE
		C.ACA_UsuID = '".$ID."' AND C.ACA_Acao = 'Postar' AND
		C.ACA_PosID = B.POS_RowID AND (B.POS_Tipo = 'Forum' OR B.POS_Tipo = 'Evento' OR B.POS_Tipo = 'Canal') AND
		B.POS_RowID = A.ACA_PosPaiID AND (A.ACA_Acao = 'Comentar' OR A.ACA_Acao = 'Curtir' OR A.ACA_Acao = 'Compartilhar') ORDER BY A.ACA_Data DESC LIMIT ".$L0.",".$L1;
		$tql = mysqli_query($serve,$sql);
		$total = mysqli_num_rows($tql);
		$i = 0;
	if ($total>0){
		/* Percorre um por um a lista das pessoas seguidas pelo perfil logado escolhidas para exibição */
		while($uql = mysqli_fetch_array($tql)){
			$i++;
			$idpost = $uql["POS_RowID"];
			$tipo = $uql["tipo"];
			$sql3 = "SELECT ACA_Data, ACA_Acao, ACA_UsuID FROM acao WHERE ACA_PosPaiID = '".$idpost."' AND (ACA_Acao = 'Comentar' OR ACA_Acao = 'Curtir' OR ACA_Acao = 'Compartilhar') ORDER BY ACA_Data DESC LIMIT 1";
			$tql3 = mysqli_query($serve,$sql3);
			$Ncomentarios = mysqli_num_rows($tql3);
			$atualizado = "";
			if($Ncomentarios>0){
				$uql3 = mysqli_fetch_array($tql3);
				$quem = $uql3["ACA_UsuID"];
				$oque = $uql3["ACA_Acao"];
				$data = date_format(date_create($uql3["ACA_Data"]), 'd/m H:i');
				$atualizado = "<div class=\"fs-08 c4\">".$data."</div>";
				if($oque=="Comentar"){$oque=" comentou em seu ";}
				if($oque=="Curtir"){$oque=" curtiu seu ";}
				if($oque=="Compartilhar"){$oque=" compartilhou seu ";}
				if($tipo=="Feed"){$tipo="post";}
				if($tipo=="Forum"){$tipo="fórum";}
				$sqlnomepost = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$quem."'";
				$tqlnomepost = mysqli_query($serve,$sqlnomepost);
				$uqlnomepost = mysqli_fetch_array($tqlnomepost);
				$nome = $uqlnomepost["USU_Nome"];
				$sqlfoto = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_RowID = '".$quem."'";
				$tqlfoto = mysqli_query($serve,$sqlfoto);
				$uqlfoto = mysqli_fetch_array($tqlfoto);
				if(!$uqlfoto["POS_Arquivo"]){
					$fotourl = $server."upload/avatar.jpg";
				}else{
					$fotourl = $server."upload/".$uqlfoto["POS_Arquivo"];
				}
				$fotoperfil = "<img src=\"".$fotourl."\" class=\"brs-50 mxh-46 mr-10\" height=\"46\" width=\"46\"/>";
				$titulo = $nome.$oque.strtolower($tipo);
				$pagina = "";
				if($uql["tipo"]=="Feed"){$pagina = "post";}
				if($uql["tipo"]=="Forum"){$pagina = "forum";}
				if($uql["tipo"]=="Evento"){$pagina = "evento";}
				if($uql["tipo"]=="Canal"){$pagina = "podcast";}
				$bb = $Ntotal==$i?"":"bb";
				$link = "onclick=\"window.location = '".$pagina.".html?id=".$idpost."';\"";
				/* Monta o bloco de informações de exibição do tópico percorrido */
				$html .= "<div name=\"".$idpost."\" class=\"".$bb." pt-10 pb-10\">
							<div class=\"d-flex no-overflow\">
								<div ".$link." class=\"mr-10 mxw-46\">".$fotoperfil."</div>
		                        <div ".$link.">".$titulo."<br>".$atualizado."</div>
		                    </div>
		                </div>";
		        array_push($idjavascript,$idpost);
			}
		}
		if($tql&&$tqlnomepost){
			$retorno = array('cod' => 1, 'html' => $html, 'proxima' => $proxima, 'numero' => $Ntotal, 'idjavascript' => $idjavascript);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhuma notificação para você</div>", 'numero' => 0);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}

/* Destaques */
if($_POST["f"]=="destaques"){
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$tempo = $_POST["tempo"];
	$html = "";
	if($tempo=="d"){
		$intervalo = "AND (B.POS_Data BETWEEN CURRENT_TIMESTAMP()-CURRENT_TIME() AND CURRENT_TIMESTAMP())";
	}else if($tempo=="s"){
		$intervalo = "AND (B.POS_Data BETWEEN CURRENT_TIMESTAMP() - INTERVAL 1 WEEK AND CURRENT_TIMESTAMP())";
	}else if($tempo=="m"){
		$intervalo = "AND (B.POS_Data BETWEEN CURRENT_TIMESTAMP() - INTERVAL 1 MONTH AND CURRENT_TIMESTAMP())";
	}else{
		$intervalo = "";
	}
	/* Verifica o número total de posts seguidos pelo perfil logado */
	$sqlN = "SELECT IF(A.ACA_PosPaiID is null, A.ACA_PosID, A.ACA_PosPaiID) post, IF(A.ACA_PosPaiID is null, count(A.ACA_PosID), count(A.ACA_PosPaiID)) N, B.POS_UsuID quem FROM acao A, posts B WHERE ((A.ACA_Acao = 'Curtir' AND A.ACA_PosID = B.POS_RowID) OR (A.ACA_Acao = 'Comentar' AND A.ACA_PosPaiID = B.POS_RowID)) AND (B.POS_Tipo = 'Forum' OR B.POS_Tipo = 'Evento' OR B.POS_Tipo = 'Canal' OR B.POS_Tipo = 'Feed' OR B.POS_Tipo = 'Podcast') ".$intervalo." GROUP BY post HAVING N > 0 ORDER BY N DESC";
	$tqlN = mysqli_query($serve,$sqlN);
	$Ntotal = mysqli_num_rows($tqlN);
	$Nporpag = 50;
	$Npag = ceil($Ntotal/$Nporpag);
	$proxima = $pag>=$Npag?false:$pag+1;
	$L0 = ($pag - 1) * $Nporpag;
	$L1 = $L0 + $Nporpag;
	/* Pega o ID dos foruns seguidos pelo perfil logado que deverão aparecer na página de consulta solicitada */
	$sql = "SELECT IF(A.ACA_PosPaiID is null, A.ACA_PosID, A.ACA_PosPaiID) post, IF(A.ACA_PosPaiID is null, count(A.ACA_PosID), count(A.ACA_PosPaiID)) N, B.POS_UsuID quem FROM acao A, posts B WHERE ((A.ACA_Acao = 'Curtir' AND A.ACA_PosID = B.POS_RowID) OR (A.ACA_Acao = 'Comentar' AND A.ACA_PosPaiID = B.POS_RowID)) AND (B.POS_Tipo = 'Forum' OR B.POS_Tipo = 'Evento' OR B.POS_Tipo = 'Canal' OR B.POS_Tipo = 'Feed' OR B.POS_Tipo = 'Podcast') ".$intervalo." GROUP BY post HAVING N > 0 ORDER BY N DESC LIMIT ".$L0.",".$L1;
	$tql = mysqli_query($serve,$sql);
	$total = mysqli_num_rows($tql);
	$i = 0;
	if ($total>0){
		/* Percorre um por um a lista das pessoas seguidas pelo perfil logado escolhidas para exibição */
		while($uql = mysqli_fetch_array($tql)){
			$idpost = $uql["post"];
			$quem = $uql["quem"];
			$sqlnomepost = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$quem."'";
			$tqlnomepost = mysqli_query($serve,$sqlnomepost);
			$uqlnomepost = mysqli_fetch_array($tqlnomepost);
			$nome = $uqlnomepost["USU_Nome"];
			$sqlfoto = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_RowID = '".$quem."'";
			$tqlfoto = mysqli_query($serve,$sqlfoto);
			$uqlfoto = mysqli_fetch_array($tqlfoto);
			if(!$uqlfoto["POS_Arquivo"]){
				$fotourl = $server."upload/avatar.jpg";
			}else{
				$fotourl = $server."upload/".$uqlfoto["POS_Arquivo"];
			}
			$fotoperfil = "<img src=\"".$fotourl."\" class=\"brs-50 mxh-46\" height=\"46\" width=\"46\"/>";
			$titulo = $fotoperfil.$nome;
			
			/* Pega os dados do post seguido percorrido */
			$sql2 = "SELECT POS_Titulo, POS_Tipo, POS_Resumo, POS_Data FROM posts WHERE POS_RowID = '".$idpost."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			$pagina = "";
			if($uql2["POS_Tipo"]=="Forum"){$pagina = "forum";}
			if($uql2["POS_Tipo"]=="Evento"){$pagina = "evento";}
			if($uql2["POS_Tipo"]=="Canal"){$pagina = "podcast";}
			if($uql2["POS_Tipo"]=="Podcast"){$pagina = "podcast";}
			if($uql2["POS_Tipo"]=="Feed"){$pagina = "post";}
			if($pagina!=""){
				$location = "window.location = '".$pagina.".html?id=".$idpost."'";
			}else{
				$location = "";
			}
			/* Pega a data do último comentário */
			$sql3 = "SELECT ACA_Data FROM acao WHERE ACA_PosPaiID = '".$idpost."' ORDER BY ACA_Data DESC LIMIT 1";
			$tql3 = mysqli_query($serve,$sql3);
			$Ncomentarios = mysqli_num_rows($tql3);
			$atualizado = "";
			if($Ncomentarios>0){
				$uql3 = mysqli_fetch_array($tql3);
				$data = date_format(date_create($uql3["ACA_Data"]), 'd/m H:i');
				$atualizado = "<div class=\"fs-05 c4\">Atualizado em ".$data."</div>";
			}else{
				$data = date_format(date_create($uql2["POS_Data"]), 'd/m H:i');
				$atualizado = "<div class=\"fs-05 c4\">em ".$data."</div>";
			}
			$i++;
			if($i==$total){
				$classe = "";
			}else{
				$classe = "mb-10 mt-10 pb-10 bb";
			}
			/* Monta o bloco de informações de exibição do tópico percorrido */
			$html .= "<div onclick=\"".$location."\" class=\"".$classe."\">
						<div class=\"d-flex no-overflow\">
	                        <div class=\"mr-10 mxw-46\">".$fotoperfil."</div>
							<div class=\"d-flex flex-column no-overflow\">
		                        <div class=\"fs-08\">".$uql2["POS_Titulo"]."</div>
		                        <div class=\"fs-07 c4\">Postado por ".$nome."</div>
		                        ".$atualizado."
		                    </div>
		                    <div class=\"w-30px\" style=\"position:relative;\"><img src=\"imagens/svg/awesome-chevron-circle-left.svg\" style=\"position:absolute;top:50%;transform:translateY(-50%) rotate(180deg);\"/></div>
		                </div>
	                </div>";
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'html' => $html, 'proxima' => $proxima, 'numero' => $Ntotal, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhum post disponível</div>", 'numero' => 0, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}

if($_POST["f"]=="listaeu"){
	$sql = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$_COOKIE['login']."'";
	$tql = mysqli_query($serve,$sql);
	$uql = mysqli_fetch_array($tql);
	echo $uql["USU_Nome"];
}

/* Perfil */
if($_POST["f"]=="perfil"){
	$idlogado = $ID;
	if(!$ID){$idlogado = $ID2;}
	$ID = $_POST["usuario"];
	if(!$ID){$ID = $ID2;}
	$sql = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoCapa' AND POS_UsuID = '".$ID."'";
	$tql = mysqli_query($serve,$sql);
	$uql = mysqli_fetch_array($tql);
	if(!$uql["POS_Arquivo"]){
		$capa = $server."upload/capa.jpg";
	}else{
		$capa = $server."upload/".$uql["POS_Arquivo"];
	}
	$sql2 = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_UsuID = '".$ID."'";
	$tql2 = mysqli_query($serve,$sql2);
	$uql2 = mysqli_fetch_array($tql2);
	if(!$uql2["POS_Arquivo"]){
		$perfil = $server."upload/avatar.jpg";
	}else{
		$perfil = $server."upload/".$uql2["POS_Arquivo"];
	}
	$denunciar = "<img class=\"Mt-225 f-right h-18\" src=\"imagens/svg/interface.svg\" onclick=\"modalInput('denunciar','Descreva o motivo de sua denúncia','".$ID."','','u','s');\">";
	$sql3 = "SELECT USU_Nome, USU_CidadeID, USU_EstadoID, USU_Tipo, USU_DetalhesID FROM usuarios WHERE USU_RowID = '".$ID."'";
	$tql3 = mysqli_query($serve,$sql3);
	$uql3 = mysqli_fetch_array($tql3);
	$sql4 = "SELECT CID_Cidade FROM cidades WHERE CID_RowID = '".$uql3['USU_CidadeID']."'";
	$tql4 = mysqli_query($serve,$sql4);
	$uql4 = mysqli_fetch_array($tql4);
	$sql5 = "SELECT EST_Sigla FROM estados WHERE EST_RowID = '".$uql3['USU_EstadoID']."'";
	$tql5 = mysqli_query($serve,$sql5);
	$uql5 = mysqli_fetch_array($tql5);
	if($tql4&&$tql5){
		$local = titleCase(strtolower($uql4['CID_Cidade'])).", ".$uql5['EST_Sigla'];
	}else{
		$local = "";
	}
	if($uql3['USU_Tipo']==0){
		$sql6 = "SELECT FIS_Profissao, FIS_Cargo, FIS_Empresa FROM fisica WHERE FIS_RowID = '".$uql3['USU_DetalhesID']."'";
		$tql6 = mysqli_query($serve,$sql6);
		$uql6 = mysqli_fetch_array($tql6);
		if($uql6['FIS_Profissao']){
			$profissao = titleCase(strtolower($uql6['FIS_Profissao']));
		}else{
			$profissao = "";
		}
		if($uql6['FIS_Cargo']){
			$cargo = titleCase(strtolower($uql6['FIS_Cargo']));
		}else{
			$cargo = "";
		}
		if($uql6['FIS_Empresa']){
			$empresa = titleCase(strtolower($uql6['FIS_Empresa']));
		}else{
			$empresa = "";
		}
		if($uql6['FIS_Cargo']&&$uql6['FIS_Empresa']){
			$em = " em ";
		}else{
			$em = "";
		}
		$cargoempresa = $cargo.$em.$empresa;
	}else{
		$cargoempresa = "";
	}
	$sql7 = "SELECT count(A.ACA_Usu2ID) N FROM acao A, usuarios B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_UsuID = '".$ID."' AND A.ACA_Usu2ID = B.USU_RowID AND A.ACA_PosID is null";
	$tql7 = mysqli_query($serve,$sql7);
	$uql7 = mysqli_fetch_array($tql7);
	$seguindo = $uql7["N"];
	$sql8 = "SELECT count(A.ACA_UsuID) N FROM acao A, usuarios B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_Usu2ID = '".$ID."' AND A.ACA_UsuID = B.USU_RowID AND A.ACA_PosID is null";
	$tql8 = mysqli_query($serve,$sql8);
	$uql8 = mysqli_fetch_array($tql8);
	$seguidores = $uql8["N"];
	$sql9 = "SELECT count(A.ACA_PosID) N FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Evento' AND A.ACA_UsuID = '".$ID."'";
	$tql9 = mysqli_query($serve,$sql9);
	$uql9 = mysqli_fetch_array($tql9);
	$eventos = $uql9["N"];
	$sql10 = "SELECT count(A.ACA_PosID) N FROM acao A, posts B WHERE A.ACA_Acao = 'Seguir' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Forum' AND A.ACA_UsuID = '".$ID."' AND B.POS_Status = 'Publico'";
	$tql10 = mysqli_query($serve,$sql10);
	$uql10 = mysqli_fetch_array($tql10);
	$foruns = $uql10["N"];
	if(!$uql3['USU_DetalhesID']){
		$profissional = 0;
		$criado = "";
		$envolvido = "";
		$vidas = "";
	}else{
		$profissional = 1;
		$sql11 = "SELECT count(POS_RowID) N FROM posts WHERE POS_Tipo = 'Oportunidade' AND POS_UsuID = '".$ID."'";
		$tql11 = mysqli_query($serve,$sql11);
		$uql11 = mysqli_fetch_array($tql11);
		$criado = $uql11["N"]?$uql11["N"]:"0";
		$sql12 = "SELECT count(POS_RowID) N FROM posts WHERE (POS_Tipo = 'Oportunidade' OR POS_Tipo = 'Dinheiro' OR POS_Tipo = 'Tecnologia') AND POS_UsuID = '".$ID."'";
		$tql12 = mysqli_query($serve,$sql12);
		$uql12 = mysqli_fetch_array($tql12);
		$envolvido = $uql12["N"]?$uql12["N"]:"0";
		$sql13 = "SELECT DISTINCT A.POS_RowID, A.POS_Resumo FROM posts A, acao B , posts C WHERE (C.POS_Tipo = 'Oportunidade' OR C.POS_Tipo = 'Dinheiro' OR C.POS_Tipo = 'Tecnologia') AND C.POS_UsuID = '".$ID."' AND ((C.POS_RowID = B.ACA_PosID AND B.ACA_Acao ='Postar' AND C.POS_RowID = A.POS_RowID) OR (C.POS_RowID = B.ACA_PosID AND B.ACA_Acao ='Investir' AND B.ACA_PosPaiID = A.POS_RowID))";
		$tql13 = mysqli_query($serve,$sql13);
		$total13 = mysqli_num_rows($tql13);
		$somavidas = 0;
		if ($total13>0){
			while($uql13 = mysqli_fetch_array($tql13)){
				$dados = explode(";", $uql13["POS_Resumo"]);
				$pessoas = explode(":", $dados[2]);
				$somavidas = $somavidas + $pessoas[1];
			}
			$vidas = $somavidas;
		}else{
			$vidas = "0";
		}
	}
	if($idlogado!=$ID){
		$sql11 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Seguir' AND ACA_UsuID = '".$idlogado."' AND ACA_Usu2ID = '".$ID."'";
		$tql11 = mysqli_query($serve,$sql11);
		$uql11 = mysqli_fetch_array($tql11);
		$idseguir = $uql11["ACA_RowID"]?$uql11["ACA_RowID"]:0;
	}
	if($tql&&$tql2&&$tql3){
		$retorno = array(
			'cod' => 1,
			'capa' => $capa,
			'perfil' => $perfil,
			'nome' => $uql3['USU_Nome'],
			'denunciar' => $denunciar,
			'seguir' => $idseguir,
			'local' => $local,
			'profissao' => $profissao,
			'empresa' => $cargoempresa,
			'seguindo' => $seguindo,
			'seguidores' => $seguidores,
			'eventos' => $eventos,
			'foruns' => $foruns,
			'profissional' => $profissional,
			'criado' => $criado,
			'envolvido' => $envolvido,
			'vidas' => $vidas
		);
		echo json_encode($retorno);
		exit();
	}else{
		$retorno = array('cod' => 2);
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="editar"){
	if(!$ID){$ID = $ID2;}
	$sql = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoCapa' AND POS_UsuID = '".$ID."'";
	$tql = mysqli_query($serve,$sql);
	$uql = mysqli_fetch_array($tql);
	if(!$uql["POS_Arquivo"]){
		$capa = $server."upload/capa.jpg";
	}else{
		$capa = $server."upload/".$uql["POS_Arquivo"];
	}
	$sql2 = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_UsuID = '".$ID."'";
	$tql2 = mysqli_query($serve,$sql2);
	$uql2 = mysqli_fetch_array($tql2);
	if(!$uql2["POS_Arquivo"]){
		$perfil = $server."upload/avatar.jpg";
	}else{
		$perfil = $server."upload/".$uql2["POS_Arquivo"];
	}
	$sql3 = "SELECT USU_Nome, USU_CidadeID, USU_EstadoID, USU_Tipo, USU_DetalhesID FROM usuarios WHERE USU_RowID = '".$ID."'";
	$tql3 = mysqli_query($serve,$sql3);
	$uql3 = mysqli_fetch_array($tql3);
	$sql4 = "SELECT CID_Cidade FROM cidades WHERE CID_RowID = '".$uql3['USU_CidadeID']."'";
	$tql4 = mysqli_query($serve,$sql4);
	$uql4 = mysqli_fetch_array($tql4);
	$sql5 = "SELECT EST_Sigla FROM estados WHERE EST_RowID = '".$uql3['USU_EstadoID']."'";
	$tql5 = mysqli_query($serve,$sql5);
	$uql5 = mysqli_fetch_array($tql5);
	if($tql4&&$tql5){
		$local = titleCase(strtolower($uql4['CID_Cidade'])).", ".$uql5['EST_Sigla'];
		$estado = $uql3['USU_EstadoID'].";".$uql5['EST_Sigla'];
		$cidade = $uql4['CID_Cidade'];
	}else{
		$local = "";
		$estado = "";
		$cidade = "";
	}
	if($uql3['USU_Tipo']==0){
		$sql6 = "SELECT FIS_Profissao, FIS_Cargo, FIS_Empresa FROM fisica WHERE FIS_RowID = '".$uql3['USU_DetalhesID']."'";
		$tql6 = mysqli_query($serve,$sql6);
		$uql6 = mysqli_fetch_array($tql6);
		if($uql6['FIS_Profissao']){
			$profissao = titleCase(strtolower($uql6['FIS_Profissao']));
		}else{
			$profissao = "";
		}
		if($uql6['FIS_Cargo']){
			$cargo = titleCase(strtolower($uql6['FIS_Cargo']));
		}else{
			$cargo = "";
		}
		if($uql6['FIS_Empresa']){
			$empresa = titleCase(strtolower($uql6['FIS_Empresa']));
		}else{
			$empresa = "";
		}
		if($uql6['FIS_Cargo']&&$uql6['FIS_Empresa']){
			$em = " em ";
		}else{
			$em = "";
		}
		$cargoempresa = $cargo.$em.$empresa;
	}else{
		$cargoempresa = "";
	}
	if(!$uql3['USU_Tipo']){
		$profissional = 0;
	}else{
		$profissional = 1;
	}
	if($tql&&$tql2&&$tql3){
		$retorno = array(
			'cod' => 1,
			'capa' => $capa,
			'perfil' => $perfil,
			'nome' => $uql3['USU_Nome'],
			'local' => $local,
			'estado' => $estado,
			'cidade' => $cidade,
			'profissao' => $profissao,
			'cargoempresa' => $cargoempresa,
			'cargo' => $cargo,
			'empresa' => $cargoempresa
		);
		echo json_encode($retorno);
		exit();
	}else{
		$retorno = array('cod' => 2);
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="editarcapa"){
	if(!$ID){$ID = $ID2;}
	if($_POST["arquivo"]){
		$POS_Arquivo = $_POST["arquivo"];
	}else{
		$retorno = array('cod' => 2, 'msg' => 'Nenhuma imagem foi selecionada');
		echo json_encode($retorno);
		exit();
	}
	$sql = "SELECT POS_RowID, POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoCapa' AND POS_UsuID = '".$ID."'";
	$tql = mysqli_query($serve,$sql);
	$uql = mysqli_fetch_array($tql);
	mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);
	if(!$uql["POS_RowID"]){
		$sql2 = "INSERT INTO posts (POS_UsuID, POS_Tipo, POS_Arquivo) VALUES ('".$ID."', 'FotoCapa', '".$POS_Arquivo."')";
		$tql2 = mysqli_query($serve,$sql2);
	}else{
		$sql2 = "UPDATE posts SET POS_Arquivo = '".$POS_Arquivo."' WHERE POS_RowID = '".$uql["POS_RowID"]."'";
		$tql2 = mysqli_query($serve, $sql2);
	}
	if($tql&&$tql2){
		mysqli_commit($serve);
		if($uql["POS_Arquivo"]){
			$uploaddir = 'upload/';
			$arquivo = $uql["POS_Arquivo"];
			$delete = unlink($uploaddir.$arquivo);
		}
		$retorno = array('cod' => 1);
		echo json_encode($retorno);
		exit();
	}else{
		mysqli_rollback($serve);
		$retorno = array('cod' => 2, 'msg' => 'Não foi possível alterar a capa, tente novamente enviando um novo arquivo');
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="editardados"){
	if(!$ID){$ID = $ID2;}
	if($_POST["nome"]){
		$USU_Nome = $_POST["nome"];
	}else{
		$retorno = array('cod' => 2, 'msg' => 'Preencha o nome para salvar');
		echo json_encode($retorno);
		exit();
	}
	if($_POST["estado"]){
		$estado = explode(";", $_POST["estado"]);
		$USU_EstadoID = $estado[0];	
		if($_POST["cidade"]){
			$cidade = explode(";", $_POST["estado"]);
			$USU_EstadoID = $estado[0];
			$sql7 = "SELECT CID_RowID FROM cidades WHERE CID_Cidade = '".$_POST["cidade"]."' AND CID_EstadoID = '".$USU_EstadoID."'";
			$tql7 = mysqli_query($serve,$sql7);
			$uql7 = mysqli_fetch_array($tql7);
			$USU_CidadeID = $uql7['CID_RowID'];
		}else{
			$retorno = array('cod' => 2, 'msg' => 'Selecione uma cidade para salvar');
			echo json_encode($retorno);
			exit();
		}	
	}else{
		$retorno = array('cod' => 2, 'msg' => 'Selecione um estado para salvar');
		echo json_encode($retorno);
		exit();
	}

	mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);

	$sql3 = "UPDATE usuarios SET USU_Nome = '".$USU_Nome."', USU_CidadeID = '".$USU_CidadeID."', USU_EstadoID = '".$USU_EstadoID."' WHERE USU_RowID = '".$ID."'";
	$tql3 = mysqli_query($serve,$sql3);
	$sql = "SELECT USU_Tipo, USU_DetalhesID FROM usuarios WHERE USU_RowID = '".$ID."'";
	$tql = mysqli_query($serve,$sql);
	$uql = mysqli_fetch_array($tql);
	if($uql['USU_Tipo']==0){
		$profissao = $_POST["profissao"]?$_POST["profissao"]:"";
		$cargo = $_POST["cargo"]?$_POST["cargo"]:"";
		$empresa = $_POST["empresa"]?$_POST["empresa"]:"";
		$sql6 = "UPDATE fisica SET FIS_Profissao = '".$profissao."', FIS_Cargo = '".$cargo."', FIS_Empresa = '".$empresa."' WHERE FIS_RowID = '".$uql['USU_DetalhesID']."'";
		$tql6 = mysqli_query($serve,$sql6);
	}
	if($tql3&&$tql6){
		mysqli_commit($serve);
		if($uql["POS_Arquivo"]){
			$uploaddir = 'upload/';
			$arquivo = $uql["POS_Arquivo"];
			$delete = unlink($uploaddir.$arquivo);
		}
		$retorno = array('cod' => 1);
		echo json_encode($retorno);
		exit();
	}else{
		mysqli_rollback($serve);
		$retorno = array('cod' => 2, 'msg' => 'Não foi possível salvar os dados agora');
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="editarperfil"){
	if(!$ID){$ID = $ID2;}
	if($_POST["arquivo"]){
		$POS_Arquivo = $_POST["arquivo"];
	}else{
		$retorno = array('cod' => 2, 'msg' => 'Nenhuma imagem foi selecionada');
		echo json_encode($retorno);
		exit();
	}
	$sql = "SELECT POS_RowID, POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_UsuID = '".$ID."'";
	$tql = mysqli_query($serve,$sql);
	$uql = mysqli_fetch_array($tql);
	mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);
	if(!$uql["POS_RowID"]){
		$sql2 = "INSERT INTO posts (POS_UsuID, POS_Tipo, POS_Arquivo) VALUES ('".$ID."', 'FotoPerfil', '".$POS_Arquivo."')";
		$tql2 = mysqli_query($serve,$sql2);
	}else{
		$sql2 = "UPDATE posts SET POS_Arquivo = '".$POS_Arquivo."' WHERE POS_RowID = '".$uql["POS_RowID"]."'";
		$tql2 = mysqli_query($serve, $sql2);
	}
	if($tql&&$tql2){
		mysqli_commit($serve);
		if($uql["POS_Arquivo"]){
			$uploaddir = 'upload/';
			$arquivo = $uql["POS_Arquivo"];
			$delete = unlink($uploaddir.$arquivo);
		}
		$retorno = array('cod' => 1);
		echo json_encode($retorno);
		exit();
	}else{
		mysqli_rollback($serve);
		$retorno = array('cod' => 2, 'msg' => 'Não foi possível alterar a foto de perfil, tente novamente enviando um novo arquivo');
		echo json_encode($retorno);
		exit();
	}
}

/* Salvos */
if($_POST["f"]=="salvos"){
	if(!$ID){$ID = $ID2;}
	$pag = $_POST["pagina"];
	if(!$pag){$pag = 1;}
	$html = "";
	$pesquisa = $_POST["pesquisa"];
	$pesquisahtml = "";
	$nfiltros = 0;
	if($_POST["posts"]>0){
		$feed = "B.POS_Tipo = 'Feed'";
		$nfiltros++;
	}else{$feed="";}
	if($_POST["foruns"]>0){
		$forumor = $nfiltros>0?" OR ":"";
		$forum = $forumor."B.POS_Tipo = 'Forum'";
		$nfiltros++;
	}else{$forum="";}
	if($_POST["eventos"]>0){
		$eventoor = $nfiltros>0?" OR ":"";
		$evento = $eventoor."B.POS_Tipo = 'Evento'";
		$nfiltros++;
	}else{$evento="";}
	if($_POST["podcasts"]>0){
		$podcastor = $nfiltros>0?" OR ":"";
		$podcast = $podcastor."B.POS_Tipo = 'Canal' OR B.POS_Tipo = 'Podcast'";
		$nfiltros++;
	}else{$podcast="";}
	if($nfiltros==0){
		$feed = "B.POS_Tipo = 'Feed'";
		$forum = " OR B.POS_Tipo = 'Forum'";
		$evento = " OR B.POS_Tipo = 'Evento'";
		$podcast = " OR B.POS_Tipo = 'Canal' OR B.POS_Tipo = 'Podcast'";
	}
	if($pesquisa){
		$pesquisahtml .= "Resultado da pesquisa para \"".$pesquisa."\"";
		$pesquisasql = "AND (B.POS_Titulo LIKE '%".$pesquisa."%' OR B.POS_Resumo LIKE '%".$pesquisa."%' OR B.POS_Descricao LIKE '%".$pesquisa."%')";
	}else{
		$pesquisasql = "";
	}
	$sqlN = "SELECT A.ACA_PosID post, B.POS_UsuID quem FROM acao A, posts B WHERE A.ACA_Acao = 'Salvar' AND A.ACA_PosID = B.POS_RowID AND (".$feed.$forum.$evento.$podcast.") AND A.ACA_UsuID = '".$ID."' ".$pesquisasql." ORDER BY A.ACA_Data DESC";
	$tqlN = mysqli_query($serve,$sqlN);
	$Ntotal = mysqli_num_rows($tqlN);
	$Nporpag = 50;
	$Npag = ceil($Ntotal/$Nporpag);
	$proxima = $pag>=$Npag?false:$pag+1;
	$L0 = ($pag - 1) * $Nporpag;
	$L1 = $L0 + $Nporpag;
	$sql = "SELECT A.ACA_PosID post, B.POS_UsuID quem FROM acao A, posts B WHERE A.ACA_Acao = 'Salvar' AND A.ACA_PosID = B.POS_RowID AND (".$feed.$forum.$evento.$podcast.") AND A.ACA_UsuID = '".$ID."' ".$pesquisasql." ORDER BY A.ACA_Data DESC LIMIT ".$L0.",".$L1;
	$tql = mysqli_query($serve,$sql);
	$total = mysqli_num_rows($tql);
	/*$retorno = array('cod' => 2, 'sql' => $sql);
	echo json_encode($retorno);
	exit();*/
	$i = 0;
	if ($total>0){
		/* Percorre um por um a lista das pessoas seguidas pelo perfil logado escolhidas para exibição */
		while($uql = mysqli_fetch_array($tql)){
			$idpost = $uql["post"];
			$quem = $uql["quem"];
			$sqlnomepost = "SELECT USU_Nome FROM usuarios WHERE USU_RowID = '".$quem."'";
			$tqlnomepost = mysqli_query($serve,$sqlnomepost);
			$uqlnomepost = mysqli_fetch_array($tqlnomepost);
			$nome = $uqlnomepost["USU_Nome"];
			$sqlfoto = "SELECT POS_Arquivo FROM posts WHERE POS_Tipo = 'FotoPerfil' AND POS_RowID = '".$quem."'";
			$tqlfoto = mysqli_query($serve,$sqlfoto);
			$uqlfoto = mysqli_fetch_array($tqlfoto);
			if(!$uqlfoto["POS_Arquivo"]){
				$fotourl = $server."upload/avatar.jpg";
			}else{
				$fotourl = $server."upload/".$uqlfoto["POS_Arquivo"];
			}
			$fotoperfil = "<img src=\"".$fotourl."\" class=\"brs-50 mxh-46\" height=\"46\" width=\"46\"/>";
			$titulo = $fotoperfil.$nome;
			
			/* Pega os dados do post seguido percorrido */
			$sql2 = "SELECT POS_Titulo, POS_Tipo, POS_Resumo, POS_Data FROM posts WHERE POS_RowID = '".$idpost."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			$pagina = "";
			if($uql2["POS_Tipo"]=="Forum"){$pagina = "forum";}
			if($uql2["POS_Tipo"]=="Evento"){$pagina = "evento";}
			if($uql2["POS_Tipo"]=="Canal"){$pagina = "podcast";}
			if($uql2["POS_Tipo"]=="Podcast"){$pagina = "podcast";}
			if($uql2["POS_Tipo"]=="Feed"){$pagina = "post";}
			if($pagina!=""){
				$location = "window.location = '".$pagina.".html?id=".$idpost."'";
			}else{
				$location = "";
			}
			/* Pega a data do último comentário */
			$sql3 = "SELECT ACA_Data FROM acao WHERE ACA_PosPaiID = '".$idpost."' ORDER BY ACA_Data DESC LIMIT 1";
			$tql3 = mysqli_query($serve,$sql3);
			$Ncomentarios = mysqli_num_rows($tql3);
			$atualizado = "";
			if($Ncomentarios>0){
				$uql3 = mysqli_fetch_array($tql3);
				$data = date_format(date_create($uql3["ACA_Data"]), 'd/m H:i');
				$atualizado = "<div class=\"fs-05 c4\">Atualizado em ".$data."</div>";
			}else{
				$data = date_format(date_create($uql2["POS_Data"]), 'd/m H:i');
				$atualizado = "<div class=\"fs-05 c4\">em ".$data."</div>";
			}
			$i++;
			if($i==$total){
				$classe = "";
			}else{
				$classe = "mb-10 mt-10 pb-10 bb";
			}
			/* Monta o bloco de informações de exibição do tópico percorrido */
			$html .= "<div onclick=\"".$location."\" class=\"".$classe."\">
						<div class=\"d-flex no-overflow\">
	                        <div class=\"mr-10 mxw-46\">".$fotoperfil."</div>
							<div class=\"d-flex flex-column no-overflow\">
		                        <div class=\"fs-08\">".$uql2["POS_Titulo"]."</div>
		                        <div class=\"fs-07 c4\">Postado por ".$nome."</div>
		                        ".$atualizado."
		                    </div>
		                    <div class=\"w-30px\" style=\"position:relative;\"><img src=\"imagens/svg/awesome-chevron-circle-left.svg\" style=\"position:absolute;top:50%;transform:translateY(-50%) rotate(180deg);\"/></div>
		                </div>
	                </div>";
		}
		if($tql&&$tql2){
			$retorno = array('cod' => 1, 'html' => $html, 'proxima' => $proxima, 'numero' => $Ntotal, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}else{
		if($Ntotal==0){			
			$retorno = array('cod' => 1, 'html' => "<div class=\"w-100 mt-10 mb-10 textcenter\">Nenhum post disponível</div>", 'numero' => 0, 'nome' =>  $uqlnome["USU_Nome"]);
			echo json_encode($retorno);
			exit();
		}else{
			$retorno = array('cod' => 2);
			echo json_encode($retorno);
			exit();
		}
	}
}

/* Acao */
if($_POST["f"]=="curtir"){
	if(!$ID){$ID = $ID2;}
	$idpost = $_POST["id"];
	$sql = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Curtir' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
	$tql = mysqli_query($serve,$sql);
	$uql = mysqli_fetch_array($tql);
	if($tql){
		if($uql["ACA_RowID"]){
			$sql2 = "DELETE FROM acao WHERE ACA_RowID = '".$uql["ACA_RowID"]."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			if($tql2){
				$retorno = array('cod' => 1, 'atual' => 'Curtir');
				echo json_encode($retorno);
				exit();
			}else{
				$retorno = array('cod' => 2);
				echo json_encode($retorno);
				exit();
			}
		}else{
			$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_PosID) VALUES ('Curtir', '".$ID."', '".$idpost."')";
			$tql2 = mysqli_query($serve,$sql2);
			if($tql2){
				$retorno = array('cod' => 1, 'atual' => 'Descurtir');
				echo json_encode($retorno);
				exit();
			}else{
				$retorno = array('cod' => 2);
				echo json_encode($retorno);
				exit();
			}
		}
	}else{
		$retorno = array('cod' => 2);
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="salvar"){
	if(!$ID){$ID = $ID2;}
	$idpost = $_POST["id"];
	$sql = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Salvar' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
	$tql = mysqli_query($serve,$sql);
	$uql = mysqli_fetch_array($tql);
	if($tql){
		if($uql["ACA_RowID"]){
			$sql2 = "DELETE FROM acao WHERE ACA_RowID = '".$uql["ACA_RowID"]."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			if($tql2){
				$retorno = array('cod' => 1, 'atual' => 'Salvar');
				echo json_encode($retorno);
				exit();
			}else{
				$retorno = array('cod' => 2);
				echo json_encode($retorno);
				exit();
			}
		}else{
			$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_PosID) VALUES ('Salvar', '".$ID."', '".$idpost."')";
			$tql2 = mysqli_query($serve,$sql2);
			if($tql2){
				$retorno = array('cod' => 1, 'atual' => 'Descartar');
				echo json_encode($retorno);
				exit();
			}else{
				$retorno = array('cod' => 2);
				echo json_encode($retorno);
				exit();
			}
		}
	}else{
		$retorno = array('cod' => 2);
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="seguir"){
	if(!$ID){$ID = $ID2;}
	$idusuario = $_POST["id"];
	$sql = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Seguir' AND ACA_UsuID = '".$ID."' AND ACA_Usu2ID = '".$idusuario."'";
	$tql = mysqli_query($serve,$sql);
	$uql = mysqli_fetch_array($tql);
	if($tql){
		if($uql["ACA_RowID"]){
			$sql2 = "DELETE FROM acao WHERE ACA_RowID = '".$uql["ACA_RowID"]."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			if($tql2){
				$retorno = array('cod' => 1);
				echo json_encode($retorno);
				exit();
			}else{
				$retorno = array('cod' => 2);
				echo json_encode($retorno);
				exit();
			}
		}else{
			$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_Usu2ID) VALUES ('Seguir', '".$ID."', '".$idusuario."')";
			$tql2 = mysqli_query($serve,$sql2);
			if($tql2){
				$retorno = array('cod' => 1);
				echo json_encode($retorno);
				exit();
			}else{
				$retorno = array('cod' => 2);
				echo json_encode($retorno);
				exit();
			}
		}
	}else{
		$retorno = array('cod' => 2);
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="acompanhar"){
	if(!$ID){$ID = $ID2;}
	$idpost = $_POST["id"];
	$sql = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Seguir' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
	$tql = mysqli_query($serve,$sql);
	$uql = mysqli_fetch_array($tql);
	if($tql){
		if($uql["ACA_RowID"]){
			$sql2 = "DELETE FROM acao WHERE ACA_RowID = '".$uql["ACA_RowID"]."'";
			$tql2 = mysqli_query($serve,$sql2);
			$uql2 = mysqli_fetch_array($tql2);
			if($tql2){
				$retorno = array('cod' => 1);
				echo json_encode($retorno);
				exit();
			}else{
				$retorno = array('cod' => 2);
				echo json_encode($retorno);
				exit();
			}
		}else{
			$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_PosID) VALUES ('Seguir', '".$ID."', '".$idpost."')";
			$tql2 = mysqli_query($serve,$sql2);
			if($tql2){
				$retorno = array('cod' => 1);
				echo json_encode($retorno);
				exit();
			}else{
				$retorno = array('cod' => 2);
				echo json_encode($retorno);
				exit();
			}
		}
	}else{
		$retorno = array('cod' => 2);
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="compartilhar"){
	if(!$ID){$ID = $ID2;}
	$idpost = $_POST["id"];
	$sql = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Compartilhar' AND ACA_UsuID = '".$ID."' AND ACA_PosID = '".$idpost."'";
	$tql = mysqli_query($serve,$sql);
	$uql = mysqli_fetch_array($tql);
	if($tql){
		if($uql["ACA_RowID"]){
			$retorno = array('cod' => 1, 'atual' => 'Compartilhado');
			echo json_encode($retorno);
			exit();
		}else{
			$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_PosID) VALUES ('Compartilhar', '".$ID."', '".$idpost."')";
			$tql2 = mysqli_query($serve,$sql2);
			if($tql2){
				$retorno = array('cod' => 1, 'atual' => 'Compartilhado');
				echo json_encode($retorno);
				exit();
			}else{
				$retorno = array('cod' => 2);
				echo json_encode($retorno);
				exit();
			}
		}
	}else{
		$retorno = array('cod' => 2);
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="denunciar"){
	if(!$ID){$ID = $ID2;}
	$id = $_POST["id"];
	$msg = "'".$_POST["mensagem"]."'";
	$tipo = $_POST["tipo"];
	if($tipo=="u"){
		$ACA_Usu2ID = "'".$id."'";
		$ACA_PosPaiID = "null";
		$sql3 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Denunciar' AND ACA_UsuID = '".$ID."' AND ACA_Usu2ID = ".$ACA_Usu2ID;
	}else if($tipo=="p"){
		$ACA_Usu2ID = "null";
		$ACA_PosPaiID = "'".$id."'";
		$sql3 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Denunciar' AND ACA_UsuID = '".$ID."' AND ACA_PosPaiID = ".$ACA_PosPaiID;
	}
	$tql3 = mysqli_query($serve, $sql3);
	$total = mysqli_num_rows($tql3);
	if($total>0){
		$retorno = array('cod' => 2, 'msg' => 'Sua denúncia já foi enviada');
		echo json_encode($retorno);
		exit();
	}
	mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);
	$sql = "INSERT INTO posts (POS_Tipo, POS_UsuID, POS_Descricao, POS_Titulo) VALUES ('Mensagem', '".$ID."', ".$msg.", 'Denúncia')";
	$tql = mysqli_query($serve,$sql);
	$last_insert_id = mysqli_insert_id($serve);
	$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_Usu2ID, ACA_PosPaiID, ACA_PosID) VALUES ('Denunciar', '".$ID."', ".$ACA_Usu2ID.", ".$ACA_PosPaiID.", '".$last_insert_id."')";
	$tql2 = mysqli_query($serve,$sql2);
	if($tql&&$tql2){
		mysqli_commit($serve);
		$retorno = array('cod' => 1);
		echo json_encode($retorno);
		exit();
	}else{
		mysqli_rollback($serve);
		$retorno = array('cod' => 2, 'msg' => 'Não foi possível denunciar agora');
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="avaliar"){
	if(!$ID){$ID = $ID2;}
	$id = $_POST["id"];
	$nota = $_POST["nota"]==""?"0":$_POST["nota"];
	$msg = "'".$nota."'";
	$ACA_PosPaiID = "'".$id."'";
	$sql3 = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Avaliar' AND ACA_UsuID = '".$ID."' AND ACA_PosPaiID = ".$ACA_PosPaiID;
	$tql3 = mysqli_query($serve, $sql3);
	$total = mysqli_num_rows($tql3);
	if($total>0){
		$retorno = array('cod' => 2, 'msg' => 'Sua avaliação já foi enviada');
		echo json_encode($retorno);
		exit();
	}
	mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);
	$sql = "INSERT INTO posts (POS_Tipo, POS_UsuID, POS_Resumo) VALUES ('Nota', '".$ID."', ".$msg.")";
	$tql = mysqli_query($serve,$sql);
	$last_insert_id = mysqli_insert_id($serve);
	$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_PosPaiID, ACA_PosID) VALUES ('Avaliar', '".$ID."', ".$ACA_PosPaiID.", '".$last_insert_id."')";
	$tql2 = mysqli_query($serve,$sql2);
	if($tql&&$tql2){
		mysqli_commit($serve);
		$retorno = array('cod' => 1);
		echo json_encode($retorno);
		exit();
	}else{
		mysqli_rollback($serve);
		$retorno = array('cod' => 2, 'msg' => 'Não foi possível avaliar agora');
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="investir"){
	if(!$ID){$ID = $ID2;}
	$idpost = $_POST["id"];
	$sql = "SELECT ACA_RowID FROM acao WHERE ACA_Acao = 'Investir' AND ACA_UsuID = '".$ID."' AND ACA_PosPaiID = '".$idpost."'";
	$tql = mysqli_query($serve,$sql);
	$uql = mysqli_fetch_array($tql);
	if($tql){
		if($uql["ACA_RowID"]){
			$retorno = array('cod' => 2, 'msg' => 'Você já investiu nesta oportunidade');
			echo json_encode($retorno);
			exit();
		}else{
			if($_POST["escolher"]=='t'){
				if(!empty($_POST["resumo"])){$POS_Resumo = $_POST["resumo"];}else{echo json_encode(array('cod' => 0, 'ide' => 'prazo'));exit();}
				if(!empty($_POST["descricao"])){$POS_Descricao = $_POST["descricao"];}else{echo json_encode(array('cod' => 0, 'ide' => 'descricao'));exit();}
				mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);
				$sql = "INSERT INTO posts (POS_Tipo, POS_UsuID, POS_Resumo, POS_Descricao) VALUES ('Tecnologia', '".$ID."', '".$POS_Resumo."', '".$POS_Descricao."')";
				$tql = mysqli_query($serve,$sql);
				$last_insert_id = mysqli_insert_id($serve);
				$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_PosID, ACA_PosPaiID) VALUES ('Investir', '".$ID."', '".$last_insert_id."', '".$idpost."')";
				$tql2 = mysqli_query($serve,$sql2);
				if($tql&&$tql2&&$last_insert_id!=0){
					mysqli_commit($serve);
					$retorno = array('cod' => 1);
					echo json_encode($retorno);
					exit();
				}else{
					mysqli_rollback($serve);
					$retorno = array('cod' => 2, 'msg' => 'Não foi possível enviar agora');
					echo json_encode($retorno);
					exit();
				}
			}else if($_POST["escolher"]=='d'){
				$sql3 = "SELECT SUM(B.POS_Resumo) investido FROM acao A, posts B WHERE A.ACA_Acao = 'Investir' AND A.ACA_PosPaiID = '".$idpost."' AND A.ACA_PosID = B.POS_RowID AND B.POS_Tipo = 'Dinheiro' ORDER BY ACA_Data DESC LIMIT 1";
				$tql3 = mysqli_query($serve,$sql3);
				$uql3 = mysqli_fetch_array($tql3);
				$investido = $uql3["investido"]?$uql3["investido"]:"0";
				$investidoF = number_format($investido, 2, ',', '.');;
				$porcentagem = ($investido/$valortotal)*100;
				if($porcentagem==100){
					$retorno = array('cod' => 2, 'msg' => 'Essa oportunidade já recebeu todo o investimento necessário');
					echo json_encode($retorno);
					exit();
				}
				if(!empty($_POST["resumo"])){$POS_Resumo = $_POST["resumo"];}else{echo json_encode(array('cod' => 0, 'ide' => 'valor'));exit();}
				mysqli_begin_transaction($serve, MYSQLI_TRANS_START_READ_WRITE);
				$sql = "INSERT INTO posts (POS_Tipo, POS_UsuID, POS_Resumo) VALUES ('Dinheiro', '".$ID."', '".$POS_Resumo."')";
				$tql = mysqli_query($serve,$sql);
				$last_insert_id = mysqli_insert_id($serve);
				$sql2 = "INSERT INTO acao (ACA_Acao, ACA_UsuID, ACA_PosID, ACA_PosPaiID) VALUES ('Investir', '".$ID."', '".$last_insert_id."', '".$idpost."')";
				$tql2 = mysqli_query($serve,$sql2);
				if($tql&&$tql2&&$last_insert_id!=0){
					mysqli_commit($serve);
					$retorno = array('cod' => 1);
					echo json_encode($retorno);
					exit();
				}else{
					mysqli_rollback($serve);
					$retorno = array('cod' => 2, 'msg' => 'Não foi possível enviar agora');
					echo json_encode($retorno);
					exit();
				}			
			}
		}
	}else{
		$retorno = array('cod' => 2, 'msg' => 'Não foi possível enviar agora');
		echo json_encode($retorno);
		exit();
	}
}
if($_POST["f"]=="deletarupload"){
	$uploaddir = 'upload/';
	if(!empty($_POST["arquivo"])){$arquivo = $_POST["arquivo"];}else{echo json_encode(array('cod' => 0, 'ide' => 'arquivo'));exit();}
	$delete = unlink($uploaddir.$arquivo);
	if($delete){
		$retorno = array('cod' => 1);
		echo json_encode($retorno);
		exit();
	}else{
		$retorno = array('cod' => 2, 'caminho' => $server.$uploaddir.$arquivo);
		echo json_encode($retorno);
		exit();
	}
}


function titleCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("de", "da", "dos", "das", "do"))
    {
        $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
        foreach ($delimiters as $dlnr => $delimiter) {
            $words = explode($delimiter, $string);
            $newwords = array();
            foreach ($words as $wordnr => $word) {
                if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtoupper($word, "UTF-8");
                } elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtolower($word, "UTF-8");
                } elseif (!in_array($word, $exceptions)) {
                    // convert to uppercase (non-utf8 only)
                    $word = ucfirst($word);
                }
                array_push($newwords, $word);
            }
            $string = join($delimiter, $newwords);
       }//foreach
       return $string;
    }
?>