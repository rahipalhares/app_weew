$( document ).ready(function() {

/* Configuração com servidor */
var $server;
$server = 'http://srv252.teste.website/~weew/';
var id = getURLParameters('id');

/* Cadastro */
if(document.getElementById("cadastro-btn")){
	document.getElementById("cadastro-btn").addEventListener("click", function () {cadastro();}, false);
}

/* Atalhos topo */
if(document.getElementById("btnv")){document.getElementById("btnv").addEventListener("click", function () {history.go(-1);}, false);}
if(document.getElementById("seguidorestopo")){document.getElementById("seguidorestopo").addEventListener("click", function () {window.location = "seguidores.html";}, false);}
if(document.getElementById("mensagenstopo")){document.getElementById("mensagenstopo").addEventListener("click", function () {window.location = "mensagens.html";}, false);}
if(document.getElementById("notificacoestopo")){document.getElementById("notificacoestopo").addEventListener("click", function () {window.location = "notificacoes.html";}, false);}
if(document.getElementById("destaquestopo")){document.getElementById("destaquestopo").addEventListener("click", function () {window.location = "destaques.html";}, false);}

/* Links menu esquerda */
if(document.getElementById("btnb")){
	document.getElementById("menuinicio").addEventListener("click", function () {window.location = "inicio.html";}, false);
	document.getElementById("menuperfil").addEventListener("click", function () {window.location = "perfil.html";}, false);
	document.getElementById("menuseguidores").addEventListener("click", function () {window.location = "seguidores.html";}, false);
	document.getElementById("menumensagens").addEventListener("click", function () {window.location = "mensagens.html";}, false);
	document.getElementById("menusalvos").addEventListener("click", function () {window.location = "salvos.html";}, false);
	document.getElementById("menuforuns").addEventListener("click", function () {window.location = "foruns.html";}, false);
	document.getElementById("menueventos").addEventListener("click", function () {window.location = "eventos.html";}, false);
	document.getElementById("menupodcasts").addEventListener("click", function () {window.location = "podcasts.html";}, false);
	pro();
	document.getElementById("menusair").addEventListener("click", function () {logout();}, false);
}

/* Botão postar */
if(document.getElementById("inicio")&&document.getElementById("post")){
	document.getElementById("post").addEventListener("keyup", function () {
		if(document.getElementById("arquivo").value.replace(/ /g,"")==""){
			if(document.getElementById("post").value.replace(/ /g,"").length>0){
				if(document.getElementById("novopost-btn").classList.contains("d-none")){
					document.getElementById("novopost-btn").classList.remove("d-none");
				}
			}else{
				document.getElementById("novopost-btn").classList.add("d-none");
			}
		}
	}, false);
	document.getElementById("titulo").addEventListener("keyup", function () {
		if(document.getElementById("titulo").value.replace(/ /g,"").length>0){
			if(document.getElementById("novopost-btn").classList.contains("d-none")){
				document.getElementById("novopost-btn").classList.remove("d-none");
			}
		}else{
			document.getElementById("novopost-btn").classList.add("d-none");
		}
	}, false);
}
if(document.getElementById("novopost-btn")){
	document.getElementById("novopost-btn").addEventListener("click", function () {novopost();}, false);
}

/* Buscar */
if(document.getElementById("iniciobuscar-btn")){
	document.getElementById("iniciobuscar-btn").addEventListener("click", function () {
		var pesquisa = document.getElementById("pesquisa").value;
		if(document.getElementById("pesquisa").value.replace(/ /g,"").length>0){
			window.location = "busca.html?pesquisa="+pesquisa;
		}
	}, false);
}
if(document.getElementById("busca-pesquisa")){
	document.getElementById("busca-pesquisa").addEventListener("click", function () {
		var pesquisa = document.getElementById("pesquisa").value;
		if(document.getElementById("pesquisa").value.replace(/ /g,"").length>0){
			window.location = "busca.html?pesquisa="+pesquisa;
		}
	}, false);
}

/* Filtros busca */
if(document.getElementById("busca-pessoas")){	
	document.getElementById("busca-pessoas").addEventListener("click", function () {
		if(document.getElementById("busca-pessoas-val").value==0){
			document.getElementById("busca-pessoas").classList.add("btn-secondary");
			document.getElementById("busca-pessoas-val").value = 1;
			loadbusca();
		}else{
			document.getElementById("busca-pessoas").classList.remove("btn-secondary");
			document.getElementById("busca-pessoas-val").value = 0;
			loadbusca();
		}
	}, false);
}
if(document.getElementById("busca-empresas")){	
	document.getElementById("busca-empresas").addEventListener("click", function () {
		if(document.getElementById("busca-empresas-val").value==0){
			document.getElementById("busca-empresas").classList.add("btn-secondary");
			document.getElementById("busca-empresas-val").value = 1;
			loadbusca();
		}else{
			document.getElementById("busca-empresas").classList.remove("btn-secondary");
			document.getElementById("busca-empresas-val").value = 0;
			loadbusca();
		}
	}, false);
}
if(document.getElementById("busca-foto")){	
	document.getElementById("busca-foto").addEventListener("click", function () {
		if(document.getElementById("busca-foto-val").value==0){
			document.getElementById("busca-foto").classList.add("btn-secondary");
			document.getElementById("busca-foto-val").value = 1;
			loadbusca();
		}else{
			document.getElementById("busca-foto").classList.remove("btn-secondary");
			document.getElementById("busca-foto-val").value = 0;
			loadbusca();
		}
	}, false);
}
if(document.getElementById("busca-pdf")){	
	document.getElementById("busca-pdf").addEventListener("click", function () {
		if(document.getElementById("busca-pdf-val").value==0){
			document.getElementById("busca-pdf").classList.add("btn-secondary");
			document.getElementById("busca-pdf-val").value = 1;
			loadbusca();
		}else{
			document.getElementById("busca-pdf").classList.remove("btn-secondary");
			document.getElementById("busca-pdf-val").value = 0;
			loadbusca();
		}
	}, false);
}
if(document.getElementById("busca-video")){	
	document.getElementById("busca-video").addEventListener("click", function () {
		if(document.getElementById("busca-video-val").value==0){
			document.getElementById("busca-video").classList.add("btn-secondary");
			document.getElementById("busca-video-val").value = 1;
			loadbusca();
		}else{
			document.getElementById("busca-video").classList.remove("btn-secondary");
			document.getElementById("busca-video-val").value = 0;
			loadbusca();
		}
	}, false);
}
if(document.getElementById("busca-posts")){	
	document.getElementById("busca-posts").addEventListener("click", function () {
		if(document.getElementById("busca-posts-val").value==0){
			document.getElementById("busca-posts").classList.add("btn-secondary");
			document.getElementById("busca-posts-val").value = 1;
			loadbusca();
		}else{
			document.getElementById("busca-posts").classList.remove("btn-secondary");
			document.getElementById("busca-posts-val").value = 0;
			loadbusca();
		}
	}, false);
}
if(document.getElementById("busca-foruns")){	
	document.getElementById("busca-foruns").addEventListener("click", function () {
		if(document.getElementById("busca-foruns-val").value==0){
			document.getElementById("busca-foruns").classList.add("btn-secondary");
			document.getElementById("busca-foruns-val").value = 1;
			loadbusca();
		}else{
			document.getElementById("busca-foruns").classList.remove("btn-secondary");
			document.getElementById("busca-foruns-val").value = 0;
			loadbusca();
		}
	}, false);
}
if(document.getElementById("busca-eventos")){	
	document.getElementById("busca-eventos").addEventListener("click", function () {
		if(document.getElementById("busca-eventos-val").value==0){
			document.getElementById("busca-eventos").classList.add("btn-secondary");
			document.getElementById("busca-eventos-val").value = 1;
			loadbusca();
		}else{
			document.getElementById("busca-eventos").classList.remove("btn-secondary");
			document.getElementById("busca-eventos-val").value = 0;
			loadbusca();
		}
	}, false);
}
if(document.getElementById("busca-podcasts")){	
	document.getElementById("busca-podcasts").addEventListener("click", function () {
		if(document.getElementById("busca-podcasts-val").value==0){
			document.getElementById("busca-podcasts").classList.add("btn-secondary");
			document.getElementById("busca-podcasts-val").value = 1;
			loadbusca();
		}else{
			document.getElementById("busca-podcasts").classList.remove("btn-secondary");
			document.getElementById("busca-podcasts-val").value = 0;
			loadbusca();
		}
	}, false);
}

/* Comentarios */
if(document.getElementById("comentario-enviar")){
	document.getElementById("comentario-enviar").addEventListener("click", enviarcomentario, false);
}
if(document.getElementById("novocomentario-btn")){
	document.getElementById("novocomentario-btn").addEventListener("click", enviarcomentario, false);
}
if(document.getElementById("forum")&&document.getElementById("mensagem")){
	document.getElementById("titulo").addEventListener("keyup", function () {
		if(document.getElementById("titulo").value.replace(/ /g,"").length>0){
			if(document.getElementById("novocomentario-btn").classList.contains("d-none")){
				document.getElementById("novocomentario-btn").classList.remove("d-none");
			}
		}else{
			document.getElementById("novocomentario-btn").classList.add("d-none");
		}
	}, false);
}
if(document.getElementById("comentario-anexo")){
	document.getElementById("comentario-anexo").addEventListener("click", function () {
		document.getElementById("texto").classList.add("d-none");
		document.getElementById("novocomentario-btn").classList.add("d-none");
		document.getElementById("titulo").classList.add("d-none");
		document.getElementById("upload").classList.remove("d-none");
	}, false);
}
if(document.getElementById("comentario-anexo-cancelar")){
	document.getElementById("comentario-anexo-cancelar").addEventListener("click", function () {
		document.getElementById("novocomentario-uploads").classList.add("d-flex");
		document.getElementById("novocomentario-uploads").classList.remove("d-none");
		document.getElementById("novocomentario-btn").classList.add("d-none");
		document.getElementById("titulo").classList.add("d-none");
		document.getElementById("titulo").value = "";
		document.getElementById("arquivo").value = "";
		document.getElementById("novocomentario-arquivo").innerHTML = "";
		document.getElementById("novocomentario-deletar").classList.add("d-none");
		document.getElementById("novocomentario-deletar").classList.remove("d-flex");
		document.getElementById("upload").classList.add("d-none");
		document.getElementById("texto").classList.remove("d-none");
		reset($("#novocomentario-foto"));
		reset($("#novocomentario-pdf"));
		reset($("#novocomentario-video"));
	}, false);
}

/* Botão seguir e deixar de seguir */
if(document.getElementById("perfilseguir")){
	document.getElementById("perfilseguir").addEventListener("click", seguir, false);
}
if(document.getElementById("perfildesseguir")){
	document.getElementById("perfildesseguir").addEventListener("click", seguir, false);
}
if(document.getElementById("perfileditar")){
	document.getElementById("perfileditar").addEventListener("click", function () {window.location = "editar.html";}, false);
}
if(document.getElementById("perfilmensagem")){
	document.getElementById("perfilmensagem").addEventListener("click", function () {window.location = "chat.html?id="+id;}, false);
}

/* Botão editar perfil */
if(document.getElementById("editarcapa-btn")){
	document.getElementById("editarcapa-btn").addEventListener("click", function () {
		document.getElementById("editarcapa-atual").classList.add("d-none");
		document.getElementById("editarcapa-editar").classList.remove("d-none");
	}, false);
}
if(document.getElementById("cancelarcapa-btn")){
	document.getElementById("cancelarcapa-btn").addEventListener("click", function () {
		document.getElementById("editarcapa-editar").classList.add("d-none");
		document.getElementById("editarcapa-atual").classList.remove("d-none");
	}, false);
}
if(document.getElementById("editarperfil-btn")){
	document.getElementById("editarperfil-btn").addEventListener("click", function () {
		document.getElementById("editarperfil-atual").classList.add("d-none");
		document.getElementById("editarperfil-editar").classList.remove("d-none");
	}, false);
}
if(document.getElementById("cancelarperfil-btn")){
	document.getElementById("cancelarperfil-btn").addEventListener("click", function () {
		document.getElementById("editarperfil-editar").classList.add("d-none");
		document.getElementById("editarperfil-atual").classList.remove("d-none");
	}, false);
}
if(document.getElementById("editardados-btn")){
	document.getElementById("editardados-btn").addEventListener("click", function () {
		document.getElementById("editardados-atual").classList.add("d-none");
		document.getElementById("editardados-editar").classList.remove("d-none");
	}, false);
}
if(document.getElementById("cancelardados-btn")){
	document.getElementById("cancelardados-btn").addEventListener("click", function () {
		document.getElementById("editardados-editar").classList.add("d-none");
		document.getElementById("editardados-atual").classList.remove("d-none");
	}, false);
}
if(document.getElementById("salvardados-btn")){
	document.getElementById("salvardados-btn").addEventListener("click", salvardadosperfil, false);
}

/* Links seguidores */
/* Carregar função do botão pesquisar */
if(document.getElementById("seguidores-pesquisa")){
	var aba = getURLParameters('aba');
	if(aba=="seguindo"){
		document.getElementById("seguidores-pesquisa").addEventListener("click", loadseguindo, false);
	}else{
		document.getElementById("seguidores-pesquisa").addEventListener("click", loadseguidores, false);
	}
	document.getElementById("pesquisa").addEventListener("keyup", function () {
		if(document.getElementById("seguidores-aba1").classList.contains("ativo")){		
			if(document.getElementById("pesquisa").value.length==0){loadseguidores();}		
		}else{
			if(document.getElementById("pesquisa").value.length==0){loadseguindo();}		
		}
	}, false);
}
/* Carregar função das abas */
if(document.getElementById("seguidores-aba1")){document.getElementById("seguidores-aba1").addEventListener("click", function () {
	loadseguidores();
	document.getElementById("seguidores-pesquisa").removeEventListener("click", loadseguindo, false);
	document.getElementById("seguidores-pesquisa").addEventListener("click", loadseguidores, false);
}, false);}
if(document.getElementById("seguidores-aba2")){document.getElementById("seguidores-aba2").addEventListener("click", function () {
	loadseguindo();
	document.getElementById("seguidores-pesquisa").removeEventListener("click", loadseguidores, false);
	document.getElementById("seguidores-pesquisa").addEventListener("click", loadseguindo, false);
}, false);}

/* Links mensagens */
if(document.getElementById("mensagens-pesquisa")){	
	document.getElementById("mensagens-pesquisa").addEventListener("click", loadmensagens, false);
	document.getElementById("pesquisa").addEventListener("keyup", function () {
		if(document.getElementById("pesquisa").value.length==0){loadmensagens();}
	}, false);
}
if(document.getElementById("mensagem")){
	document.getElementById("mensagem").addEventListener("focus", function () {
		document.getElementById("chat-list").scroll({
			top: document.getElementById("chat-list").scrollHeight,
			behavior: 'smooth'
		});
	}, false);
}
if(document.getElementById("mensagens-enviar")){
	document.getElementById("mensagens-enviar").addEventListener("click", enviarchat, false);
}

/* Links fóruns */
if(document.getElementById("foruns-novo")){
	document.getElementById("foruns-novo").addEventListener("click", function () {window.location = "novoforum.html";}, false);
}
if(document.getElementById("novoforum-btn")){
	document.getElementById("novoforum-btn").addEventListener("click", function () {novoforum();}, false);
}
if(document.getElementById("foruns-pesquisa")){	
	document.getElementById("foruns-pesquisa").addEventListener("click", loadforuns, false);
	document.getElementById("pesquisa").addEventListener("keyup", function () {
		if(document.getElementById("pesquisa").value.length==0){loadforuns();}
	}, false);
}
/*if(document.getElementById("novoforum-foto")){	
	document.getElementById("novoforum-foto").addEventListener("change", function () {
		var x = document.getElementById("novoforum-foto");
		//nomeupload(x.value,'novoforum');
		//upload();
	}, false);
}
if(document.getElementById("novoforum-pdf")){	
	document.getElementById("novoforum-pdf").addEventListener("change", function () {
		var x = document.getElementById("novoforum-pdf");
		nomeupload(x.value,'novoforum');
		upload();
	}, false);
}
if(document.getElementById("novoforum-video")){	
	document.getElementById("novoforum-video").addEventListener("change", function () {
		var x = document.getElementById("novoforum-video");
		nomeupload(x.value,'novoforum');
		upload();
	}, false);
}*/

/* Links eventos */
if(document.getElementById("eventos-novo")){
	document.getElementById("eventos-novo").addEventListener("click", function () {window.location = "novoevento.html";}, false);
}
if(document.getElementById("novoevento-btn")){
	document.getElementById("novoevento-btn").addEventListener("click", function () {novoevento();}, false);
}
if(document.getElementById("eventos-pesquisa")){	
	document.getElementById("eventos-pesquisa").addEventListener("click", loadeventos, false);
	document.getElementById("pesquisa").addEventListener("keyup", function () {
		if(document.getElementById("pesquisa").value.length==0){loadeventos();}
	}, false);
}
if(document.getElementById("estado")&&document.getElementById("cidade")){	
	document.getElementById("estado").addEventListener("change", function () {
		var estado = document.getElementById("estado").value.split(";");
		cidades("cidade",estado[0]);
	}, false);
}

/* Links podcasts */
if(document.getElementById("podcasts-pesquisa")){	
	document.getElementById("podcasts-pesquisa").addEventListener("click", loadpodcasts, false);
	document.getElementById("pesquisa").addEventListener("keyup", function () {
		if(document.getElementById("pesquisa").value.length==0){loadpodcasts();}
	}, false);
}
if(document.getElementById("canal-salvar")){	
	document.getElementById("canal-salvar").addEventListener("click", function () {
		 salvar("canal-salvar",id);
	}, false);
}
if(document.getElementById("canal-compartilhar")){	
	document.getElementById("canal-compartilhar").addEventListener("click", function () {
		compartilharcanal(id);
	}, false);
}
if(document.getElementById("canal-votar")){	
	document.getElementById("canal-votar").addEventListener("click", function () {
		compartilharcanal(id);
		modalVote("avaliar","Avaliar canal",id,"");
	}, false);
}

/* Botões player podcasts */
var progresso;
var carregado;
if(document.getElementById("player")){
	document.getElementById("play").addEventListener("click", function () {
		document.getElementById('player-audio').play();
		document.getElementById("play").classList.add("d-none");
		document.getElementById("pause").classList.remove("d-none");
		progresso = setInterval(progressoaudio, 1000);
	}, false);
	document.getElementById("pause").addEventListener("click", function () {
		document.getElementById('player-audio').pause();
		document.getElementById("pause").classList.add("d-none");
		document.getElementById("play").classList.remove("d-none");
		clearInterval(progresso);
	}, false);
	document.getElementById("fechar").addEventListener("click", function () {
		document.getElementById("player-bar").value = 0;
		document.getElementById('player-audio').pause();
		document.getElementById("pause").classList.add("d-none");
		document.getElementById("play").classList.remove("d-none");
		clearInterval(progresso);
	}, false);
	carregado = setInterval(carregadoaudio, 500);
	var audio = document.getElementById('player-audio');
	var observer = new MutationObserver(function(mutations) {
		mutations.forEach(function(mutation) {
			if(mutation.type == "attributes" && mutation.attributeName == "src") {
				document.getElementById('player-audio').pause();
				document.getElementById("pause").classList.add("d-none");
				document.getElementById("play").classList.remove("d-none");
				document.getElementById('player-progresso').style.width = "0%";
			}
		});
	});
	observer.observe(audio, {attributes: true});
}
if(document.getElementById("abrir-player")){
	document.getElementById("abrir-player").addEventListener("click", function () {
		document.getElementById("player-bar").value = 1;
	}, false);
}

/* Carregar função das abas destaques */
if(document.getElementById("destaques-aba1")){document.getElementById("destaques-aba1").addEventListener("click", function () {
	loaddestaques();
}, false);}
if(document.getElementById("destaques-aba2")){document.getElementById("destaques-aba2").addEventListener("click", function () {
	loaddestaques('s');
}, false);}
if(document.getElementById("destaques-aba3")){document.getElementById("destaques-aba3").addEventListener("click", function () {
	loaddestaques('m');
}, false);}

/* Links salvos */
if(document.getElementById("salvos-pesquisa")){	
	document.getElementById("salvos-pesquisa").addEventListener("click", loadsalvos, false);
	document.getElementById("pesquisa").addEventListener("keyup", function () {
		if(document.getElementById("pesquisa").value.length==0){loadsalvos();}
	}, false);
}

/* Filtros salvos */
if(document.getElementById("salvos-posts")){	
	document.getElementById("salvos-posts").addEventListener("click", function () {
		if(document.getElementById("salvos-posts-val").value==0){
			document.getElementById("salvos-posts").classList.add("btn-secondary");
			document.getElementById("salvos-posts-val").value = 1;
			loadsalvos();
		}else{
			document.getElementById("salvos-posts").classList.remove("btn-secondary");
			document.getElementById("salvos-posts-val").value = 0;
			loadsalvos();
		}
	}, false);
}
if(document.getElementById("salvos-foruns")){	
	document.getElementById("salvos-foruns").addEventListener("click", function () {
		if(document.getElementById("salvos-foruns-val").value==0){
			document.getElementById("salvos-foruns").classList.add("btn-secondary");
			document.getElementById("salvos-foruns-val").value = 1;
			loadsalvos();
		}else{
			document.getElementById("salvos-foruns").classList.remove("btn-secondary");
			document.getElementById("salvos-foruns-val").value = 0;
			loadsalvos();
		}
	}, false);
}
if(document.getElementById("salvos-eventos")){	
	document.getElementById("salvos-eventos").addEventListener("click", function () {
		if(document.getElementById("salvos-eventos-val").value==0){
			document.getElementById("salvos-eventos").classList.add("btn-secondary");
			document.getElementById("salvos-eventos-val").value = 1;
			loadsalvos();
		}else{
			document.getElementById("salvos-eventos").classList.remove("btn-secondary");
			document.getElementById("salvos-eventos-val").value = 0;
			loadsalvos();
		}
	}, false);
}
if(document.getElementById("salvos-podcasts")){	
	document.getElementById("salvos-podcasts").addEventListener("click", function () {
		if(document.getElementById("salvos-podcasts-val").value==0){
			document.getElementById("salvos-podcasts").classList.add("btn-secondary");
			document.getElementById("salvos-podcasts-val").value = 1;
			loadsalvos();
		}else{
			document.getElementById("salvos-podcasts").classList.remove("btn-secondary");
			document.getElementById("salvos-podcasts-val").value = 0;
			loadsalvos();
		}
	}, false);
}

/* Links oportunidades */
if(document.getElementById("oportunidades-novo")){
	document.getElementById("oportunidades-novo").addEventListener("click", function () {window.location = "novaoportunidade.html";}, false);
}
if(document.getElementById("novaoportunidade-btn")){
	document.getElementById("novaoportunidade-btn").addEventListener("click", function () {novaoportunidade();}, false);
}
if(document.getElementById("oportunidades-pesquisa")){	
	document.getElementById("oportunidades-pesquisa").addEventListener("click", loadoportunidades, false);
	document.getElementById("pesquisa").addEventListener("keyup", function () {
		if(document.getElementById("pesquisa").value.length==0){loadoportunidades();}
	}, false);
}

/* Cadastro profissional */
if(document.getElementById("cadastropro-btn")){
	document.getElementById("cadastropro-btn").addEventListener("click", function () {window.location = "cadastropro2.html";}, false);
}
/* Botão cadastro profissional */
if(document.getElementById("escolher-fisica")){	
	document.getElementById("escolher-fisica").addEventListener("click", function () {
		if(document.getElementById("escolher-fisica-val").value==0){
			document.getElementById("escolher-fisica").classList.add("btn-secondary");
			document.getElementById("escolher-fisica-val").value = 1;
			document.getElementById("escolher-juridica").classList.remove("btn-secondary");
			document.getElementById("escolher-juridica-val").value = 0;
			document.getElementById("cadastropro2-btn").classList.remove("d-none");
		}else{
			document.getElementById("escolher-fisica").classList.remove("btn-secondary");
			document.getElementById("escolher-fisica-val").value = 0;
			document.getElementById("cadastropro2-btn").classList.add("d-none");
		}
	}, false);
}
if(document.getElementById("escolher-juridica")){	
	document.getElementById("escolher-juridica").addEventListener("click", function () {
		if(document.getElementById("escolher-juridica-val").value==0){
			document.getElementById("escolher-juridica").classList.add("btn-secondary");
			document.getElementById("escolher-juridica-val").value = 1;
			document.getElementById("escolher-fisica").classList.remove("btn-secondary");
			document.getElementById("escolher-fisica-val").value = 0;
			document.getElementById("cadastropro2-btn").classList.remove("d-none");
		}else{
			document.getElementById("escolher-juridica").classList.remove("btn-secondary");
			document.getElementById("escolher-juridica-val").value = 0;
			document.getElementById("cadastropro2-btn").classList.add("d-none");
		}
	}, false);
}
if(document.getElementById("fisica-salvar")){	
	document.getElementById("fisica-salvar").addEventListener("click", cadastrofisica, false);
}
if(document.getElementById("juridica-salvar")){	
	document.getElementById("juridica-salvar").addEventListener("click", cadastrojuridica, false);
}
if(document.getElementById("cadastropro2-btn")){	
	document.getElementById("cadastropro2-btn").addEventListener("click", function () {
		if(document.getElementById("escolher-fisica-val").value==1){
			document.getElementById("cadastropro2-escolher").classList.add("d-none");
			document.getElementById("cadastropro2-juridica").classList.add("d-none");
			document.getElementById("cadastropro2-fisica").classList.remove("d-none");
		}else if(document.getElementById("escolher-juridica-val").value==1){
			document.getElementById("cadastropro2-escolher").classList.add("d-none");
			document.getElementById("cadastropro2-fisica").classList.add("d-none");
			document.getElementById("cadastropro2-juridica").classList.remove("d-none");
		}
	}, false);
}

/* Botão oportunidade */
if(document.getElementById("escolher-tecnologia")){	
	document.getElementById("escolher-tecnologia").addEventListener("click", function () {
		if(document.getElementById("escolher-tecnologia-val").value==0){
			document.getElementById("escolher-tecnologia").classList.add("btn-secondary");
			document.getElementById("escolher-tecnologia-val").value = 1;
			document.getElementById("escolher-dinheiro").classList.remove("btn-secondary");
			document.getElementById("escolher-dinheiro-val").value = 0;
			document.getElementById("oportunidade-escolher").classList.remove("d-none");
		}else{
			document.getElementById("escolher-tecnologia").classList.remove("btn-secondary");
			document.getElementById("escolher-tecnologia-val").value = 0;
			document.getElementById("oportunidade-escolher").classList.add("d-none");
		}
	}, false);
}
if(document.getElementById("escolher-dinheiro")){	
	document.getElementById("escolher-dinheiro").addEventListener("click", function () {
		if(document.getElementById("escolher-dinheiro-val").value==0){
			document.getElementById("escolher-dinheiro").classList.add("btn-secondary");
			document.getElementById("escolher-dinheiro-val").value = 1;
			document.getElementById("escolher-tecnologia").classList.remove("btn-secondary");
			document.getElementById("escolher-tecnologia-val").value = 0;
			document.getElementById("oportunidade-escolher").classList.remove("d-none");
		}else{
			document.getElementById("escolher-dinheiro").classList.remove("btn-secondary");
			document.getElementById("escolher-dinheiro-val").value = 0;
			document.getElementById("oportunidade-escolher").classList.add("d-none");
		}
	}, false);
}
if(document.getElementById("oportunidade-escolher")){	
	document.getElementById("oportunidade-escolher").addEventListener("click", function () {
		if(document.getElementById("escolher-tecnologia-val").value==1){
			document.getElementById("escolher").classList.add("d-none");
			document.getElementById("dinheiro").classList.add("d-none");
			document.getElementById("tecnologia").classList.remove("d-none");
		}else if(document.getElementById("escolher-dinheiro-val").value==1){
			document.getElementById("escolher").classList.add("d-none");
			document.getElementById("tecnologia").classList.add("d-none");
			document.getElementById("dinheiro").classList.remove("d-none");
		}
	}, false);
}
if(document.getElementById("tecnologia-escolher")){	
	document.getElementById("tecnologia-escolher").addEventListener("click", function () {
		investirtecnologia(id);
	}, false);
}
if(document.getElementById("dinheiro-escolher")){	
	document.getElementById("dinheiro-escolher").addEventListener("click", function () {
		investirdinheiro(id);
	}, false);
}


/* Textarea responsivo */
$('textarea').each(function () {
	this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
}).on('input', function () {
	this.style.height = 'auto';
	this.style.height = (this.scrollHeight) + 'px';
});

/* Verificar conexão à internet */
window.setInterval(function() {
	if(document.getElementById("bodyB")){
		var online = navigator.onLine;
		if(online==true) {
			if(document.getElementById("bodyB").style.display == 'block'){
				document.getElementById("bodyA").style.display = 'block';
				document.getElementById("bodyB").style.display = 'none';
			}
		} else {
			document.getElementById("bodyA").style.display = 'none';
			document.getElementById("bodyB").style.display = 'block';
		}
	}
}, 5000);

});

/* Esconder splashscreen */
function splashoff() { if(!document.getElementById("formlogin")){navigator.splashscreen.hide();} }
document.addEventListener("deviceready", splashoff, false);

/* Alerta */
window.modalAlert = function(m) {
    //mensagem
    var mnp = document.getElementById('modal');
    mnp.style.display="block";
    var mnp2 = 'document.getElementById(\"modal\")';
    var none = '"none"';
    mnp.innerHTML="<div class='modal-conteudo'><div class='modal-header'>"+m+"</div><div class='modal-footer'><button class='btn btn1' onclick='"+mnp2+".innerHTML=\"\";"+mnp2+".style.display="+none+";'>OK</button></div></div>";
    var modal = document.getElementById('modal');
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}
window.modalConfirm = function(x,y,m,a,at,b,bt,c,ct,d,dt,e,et,f,ft) {
    //função,mensagem,var,vart('s' para acrescentar aspas)
    var mnp = document.getElementById('modal');
    mnp.style.display="block";
    if(at=='s'){var a='"'+a+'"'}
    if(bt=='s'){var b='"'+b+'"'}
    if(ct=='s'){var c='"'+c+'"'}
    if(dt=='s'){var d='"'+d+'"'}
    if(et=='s'){var e='"'+e+'"'}
    if(ft=='s'){var f='"'+f+'"'}
    var all="";
    if(a){all+=a;}
    if(b){all+=","+b;}
    if(c){all+=","+c;}
    if(d){all+=","+d;}
    if(e){all+=","+e;}
    if(f){all+=","+f;}
    if(y){var y=y+"("+all+");";}else{var y="";}
    var mnp2 = 'document.getElementById(\"modal\")';
    var none = '"none"';
    mnp.innerHTML="<div class='modal-conteudo'><div class='modal-header'>"+m+"</div><div class='modal-footer'><button onclick='"+mnp2+".innerHTML=\"\";"+mnp2+".style.display="+none+";"+y+"' class='modal-botao-n C5' tabindex='0'>Não</button><button class='modal-botao-s btn' onclick='"+mnp2+".innerHTML=\"\";"+mnp2+".style.display="+none+";"+x+"("+all+");'>Sim</button></div></div>";
    var modal = document.getElementById('modal');
    window.onclick = function(event) {
        if (event.target == modal) {
            //modal.style.display = "none";
        }
    }
}
window.modalInput = function(x,m,a,at,b,bt,c,ct,d,dt,e,et,f,ft) {
    //função,mensagem,var,vart('s' para acrescentar aspas)
    var mnp = document.getElementById('modal');
    mnp.style.display="block";
    if(at=='s'){var a='"'+a+'"'}
    if(bt=='s'){var b='"'+b+'"'}
    if(ct=='s'){var c='"'+c+'"'}
    if(dt=='s'){var d='"'+d+'"'}
    if(et=='s'){var e='"'+e+'"'}
    if(ft=='s'){var f='"'+f+'"'}
    var all="";
    if(a){all+=","+a;}
    if(b){all+=","+b;}
    if(c){all+=","+c;}
    if(d){all+=","+d;}
    if(e){all+=","+e;}
    if(f){all+=","+f;}
    var mnp2 = 'document.getElementById(\"modal\")';
    var none = '"none"';
    mnp.innerHTML="<div class='modal-conteudo'><div class='modal-header'>"+m+"</div><textarea class='modal-textarea' id='modal-textarea'></textarea><div class='modal-footer'><button onclick='"+mnp2+".innerHTML=\"\";"+mnp2+".style.display="+none+";' class='modal-botao-n C5' tabindex='0'>Cancelar</button><button class='modal-botao-s btn' onclick='var txt = document.getElementById(\"modal-textarea\").value;"+mnp2+".innerHTML=\"\";"+mnp2+".style.display="+none+";"+x+"(txt"+all+");'>Enviar</button></div></div>";
    var modal = document.getElementById('modal');
    window.onclick = function(event) {
        if (event.target == modal) {
            //modal.style.display = "none";
        }
    }
}
window.modalVote = function(x,m,a,at,b,bt,c,ct,d,dt,e,et,f,ft) {
    //função,mensagem,var,vart('s' para acrescentar aspas)
    var mnp = document.getElementById('modal');
    mnp.style.display="block";
    if(at=='s'){var a='"'+a+'"'}
    if(bt=='s'){var b='"'+b+'"'}
    if(ct=='s'){var c='"'+c+'"'}
    if(dt=='s'){var d='"'+d+'"'}
    if(et=='s'){var e='"'+e+'"'}
    if(ft=='s'){var f='"'+f+'"'}
    var all="";
    if(a){all+=","+a;}
    if(b){all+=","+b;}
    if(c){all+=","+c;}
    if(d){all+=","+d;}
    if(e){all+=","+e;}
    if(f){all+=","+f;}
    var mnp2 = 'document.getElementById(\"modal\")';
    var none = '"none"';
    mnp.innerHTML="<div class='modal-conteudo'><div class='modal-header'>"+m+"</div><input type='hidden' id='modal-nota'/><div><img id='star1' class='mr-10' src='imagens/svg/feather-star2.svg' onclick='nota1();'><img id='star2' class='mr-10' src='imagens/svg/feather-star2.svg' onclick='nota2();'><img id='star3' class='mr-10' src='imagens/svg/feather-star2.svg' onclick='nota3();'><img id='star4' class='mr-10' src='imagens/svg/feather-star2.svg' onclick='nota4();'><img id='star5' class='mr-10' src='imagens/svg/feather-star2.svg' onclick='nota5();'></div><div class='modal-footer'><button onclick='"+mnp2+".innerHTML=\"\";"+mnp2+".style.display="+none+";' class='modal-botao-n C5' tabindex='0'>Cancelar</button><button class='modal-botao-s btn' onclick='var txt = document.getElementById(\"modal-nota\").value;"+mnp2+".innerHTML=\"\";"+mnp2+".style.display="+none+";"+x+"(txt"+all+");'>Salvar</button></div></div>";
    var modal = document.getElementById('modal');
    window.onclick = function(event) {
        if (event.target == modal) {
            //modal.style.display = "none";
        }
    }
}