/* Configuração com servidor */
var $server;
$server = 'http://srv252.teste.website/~weew/';

/* Pegar conteúdo da url */
function getURLParameters( param, url ) {
	param = param.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regexS = "[\\?&]"+param+"=([^&#]*)";
	var regex = new RegExp( regexS );
	if (typeof url == "undefined"){
		var results = regex.exec( window.location.href );
	}else{
		var results = regex.exec( url );
	}
	if( results == null ){
		return "";
	}else {
		return decodeURI(results[1]);
	}
}

/*var parametro = getURLParameters('nomevar');*/


/* Sair */
function logout(){ 
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=logout",
		success :  function(response){            
			if(response.cod == "1"){
				window.location.href = "index.html";
			}
		}
	});
}

/* Liberar profissional */
function pro(){ 
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=pro",
		success :  function(response){            
			if(response.cod == "1"){
				if(response.pro<2){
					document.getElementById("menuprofissional").innerHTML = '<img src="imagens/svg/awesome-unlock-alt.svg"><span>Profissional</span>';
					document.getElementById("menuprofissional").addEventListener("click", function () {
						window.location = "oportunidades.html";
					}, false);
				}else{
					document.getElementById("menuprofissional").innerHTML = '<img src="imagens/svg/awesome-lock-alt.svg"><span>Profissional</span>';
					document.getElementById("menuprofissional").addEventListener("click", function () {
						window.location = "cadastropro.html";
					}, false);
				}
				
			}
		}
	});
}

/* Carregamento página inicio.html */
function loadinicio(){ 
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=fotoperfil",
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById("fotoperfil-img").src = response.foto;
			}
		}
	});
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=inicio",
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById("inicio-list").innerHTML = response.html;
				var n = response.idjavascript.length;
				for(var j = 0;j<n;j++){
					if(document.getElementsByName(response.idjavascript[j])){
						var l = document.getElementsByName(response.idjavascript[j]).length;
						for(var i = 0;i<l;i++){
							if(i>0){
								document.getElementsByName(response.idjavascript[j])[i].remove();
							}
						}
					}
				}
			}
		}
	});
}

/* Carregamento página busca.html */
function loadbusca(){
	var pesquisa = getURLParameters('pesquisa');
	document.getElementById("pesquisa").value = pesquisa;
	var pessoas = "&pessoas="+document.getElementById("busca-pessoas-val").value;
	var empresas = "&empresas="+document.getElementById("busca-empresas-val").value;
	var foto = "&foto="+document.getElementById("busca-foto-val").value;
	var pdf = "&pdf="+document.getElementById("busca-pdf-val").value;
	var video = "&video="+document.getElementById("busca-video-val").value;
	/*var posts = "&posts="+document.getElementById("busca-posts-val").value;
	var foruns = "&foruns="+document.getElementById("busca-foruns-val").value;
	var eventos = "&eventos="+document.getElementById("busca-eventos-val").value;
	var podcasts = "&podcasts="+document.getElementById("busca-podcasts-val").value;*/
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=busca&pesquisa="+pesquisa+pessoas+empresas+foto+pdf+video,
		success :  function(response){
			if(response.cod == "1"){
				document.getElementById("busca-list").innerHTML = response.html;
			}
		}
	});
}

/* Carregamento página post.html */
function loadpost(){
	var id = getURLParameters('id');
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=post&id="+id,
		success :  function(response){
			if(response.cod == "1"){
				document.getElementById("post-list").innerHTML = response.html;
			}
		}
	});
}
function loadcomentario(msg,filtro = false){
	var id = getURLParameters('id');
	if(msg&&!filtro){var mensagem = "&idmsg="+msg;}else{var mensagem = "";}
	var foto = document.getElementById("comentarios-foto-val")?"&foto="+document.getElementById("comentarios-foto-val").value:"&foto=0";
	var pdf = document.getElementById("comentarios-pdf-val")?"&pdf="+document.getElementById("comentarios-pdf-val").value:"&pdf=0";
	var video = document.getElementById("comentarios-video-val")?"&video="+document.getElementById("comentarios-video-val").value:"&video=0";
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=comentarios&id="+id+foto+pdf+video+mensagem,
		success :  function(response){           
			if(response.cod == "1"){
				if(response.numero>0||document.getElementById("comentarios-list").innerHTML!=response.html){
					if(msg){
						$("#comentarios-list").append(response.html);						
					}else{
						document.getElementById("comentarios-list").innerHTML = response.html;
					}
				}
				document.getElementById("idultima").value = response.ultima;
				if(!filtro){setTimeout(function(){ loadcomentario(response.ultima); }, 2500);}
			}else{
				if(!filtro){setTimeout(function(){ loadcomentario(document.getElementById("idultima").value); }, 2500);}
			}
		},
		error: function(){
			if(!filtro){setTimeout(function(){ loadcomentario(document.getElementById("idultima").value); }, 2500);}
		}
	});
}
/* Filtros comentarios */
function comentariofoto(){
	if(document.getElementById("comentarios-foto-val").value==0){
		document.getElementById("comentarios-foto").classList.add("btn-secondary");
		document.getElementById("comentarios-foto-val").value = 1;
		loadcomentario(false,true);
	}else{
		document.getElementById("comentarios-foto").classList.remove("btn-secondary");
		document.getElementById("comentarios-foto-val").value = 0;
		loadcomentario(false,true);
	}
}
function comentariopdf(){
	if(document.getElementById("comentarios-pdf-val").value==0){
		document.getElementById("comentarios-pdf").classList.add("btn-secondary");
		document.getElementById("comentarios-pdf-val").value = 1;
		loadcomentario();
	}else{
		document.getElementById("comentarios-pdf").classList.remove("btn-secondary");
		document.getElementById("comentarios-pdf-val").value = 0;
		loadcomentario(false,true);
	}
}
function comentariovideo(){	
	if(document.getElementById("comentarios-video-val").value==0){
		document.getElementById("comentarios-video").classList.add("btn-secondary");
		document.getElementById("comentarios-video-val").value = 1;
		loadcomentario(false,true);
	}else{
		document.getElementById("comentarios-video").classList.remove("btn-secondary");
		document.getElementById("comentarios-video-val").value = 0;
		loadcomentario(false,true);
	}
}

/* Carregamento página seguidores.html */
function loadseguidores(){ 
	var id = getURLParameters('id');
	if(id){var usuario = "&usuario="+id;}else{var usuario = "";	}
	var pesquisa = document.getElementById("pesquisa").value;
	if(pesquisa){var pesquisa = "&pesquisa="+pesquisa;document.getElementById("pesquisa").focus();}else{var pesquisa = "";}
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=seguidores"+usuario+pesquisa,
		success :  function(response){
			if(id&&response.nome){
				document.getElementById("nomepagina").innerHTML = response.nome;
				document.getElementById("btnb").classList.add("d-none");
				document.getElementById("btnv").classList.remove("d-none");
				document.getElementById("seguidorestopo").children[0].classList.remove("ativo");
			}else{
				document.getElementById("nomepagina").innerHTML = "Conexões";
				document.getElementById("btnv").classList.add("d-none");
				document.getElementById("btnb").classList.remove("d-none");
				document.getElementById("seguidorestopo").children[0].classList.add("ativo");
			}
			if(response.cod == "1"){
				document.getElementById("seguidores-list").innerHTML = response.html;
				document.getElementById("seguidores-aba1").classList.remove("ativo");
				document.getElementById("seguidores-aba1").classList.add("ativo");
				document.getElementById("seguidores-aba2").classList.remove("ativo");
			}
		}
	});
}
function loadseguindo(){
	var id = getURLParameters('id');
	if(id){var usuario = "&usuario="+id;}else{var usuario = "";}
	var pesquisa = document.getElementById("pesquisa").value;
	if(pesquisa){var pesquisa = "&pesquisa="+pesquisa;document.getElementById("pesquisa").focus();}else{var pesquisa = "";}
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=seguindo"+usuario+pesquisa,
		success :  function(response){   
			if(id&&response.nome){
				document.getElementById("nomepagina").innerHTML = response.nome;
				document.getElementById("btnb").classList.add("d-none");
				document.getElementById("btnv").classList.remove("d-none");
				document.getElementById("seguidorestopo").children[0].classList.remove("ativo");
			}else{
				document.getElementById("nomepagina").innerHTML = "Conexões";
				document.getElementById("btnv").classList.add("d-none");
				document.getElementById("btnb").classList.remove("d-none");
				document.getElementById("seguidorestopo").children[0].classList.add("ativo");
			}
			if(response.cod == "1"){
				document.getElementById("seguidores-list").innerHTML = response.html;
				document.getElementById("seguidores-aba1").classList.remove("ativo");
				document.getElementById("seguidores-aba2").classList.remove("ativo");
				document.getElementById("seguidores-aba2").classList.add("ativo");
			}
		}
	});
}

/* Carregamento página mensagens.html */
function loadmensagens(){ 
	var pesquisa = document.getElementById("pesquisa").value;
	if(pesquisa){var pesquisa = "&pesquisa="+pesquisa;document.getElementById("pesquisa").focus();}else{var pesquisa = "";}
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=mensagens"+pesquisa,
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById("mensagens-list").innerHTML = response.html;
			}
		}
	});
}

/* Carregamento página chat.html */
function loadchat(msg){
	var id = getURLParameters('id');
	if(id){var usuario = "&usuario="+id;}else{var usuario = "";}
	if(msg){var mensagem = "&idmsg="+msg;}else{var mensagem = "";}
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=chat"+usuario+mensagem,
		success :  function(response){            
			if(response.cod == "1"){
				$("#chat-list").append(response.html);
				document.getElementById("idultima").value = response.ultima;
				document.getElementById("chat-list").scroll({
					top: document.getElementById("chat-list").scrollHeight,
					behavior: 'smooth'
				});
			}
		}
	});
}
function enviarchat(){
	document.getElementById("mensagem").focus();
	document.getElementById("mensagens-enviar").removeEventListener("click", enviarchat, false);
	var id = getURLParameters('id');
	var msg = document.getElementById("mensagem").value;
	if(id&&msg){
		var usuario = "&usuario="+id;
		var mensagem = "&mensagem="+msg;
		$.ajax({
			dataType  : 'json',
			type : 'POST',
			url  : $server+'ajax.php',
			data: "f=enviarchat"+usuario+mensagem,
			success :  function(response){            
				if(response.cod == "1"){
					document.getElementById("mensagem").value = "";
				}
				document.getElementById("mensagens-enviar").addEventListener("click", enviarchat, false);
			},
			error: function(){
				document.getElementById("mensagens-enviar").addEventListener("click", enviarchat, false);
			}
		});
	}
	document.getElementById("mensagens-enviar").blur();
}

function enviarcomentario(){
	var id = getURLParameters('id');
	var msg = document.getElementById("mensagem").value;
	var titulo = document.getElementById("titulo").value;
	var arquivo = document.getElementById("arquivo").value;
	if(id&&msg){
		document.getElementById("mensagem").focus();
		document.getElementById("comentario-enviar").removeEventListener("click", enviarcomentario, false);
		var post = "&id="+id;
		var mensagem = "&mensagem="+msg;
		$.ajax({
			dataType  : 'json',
			type : 'POST',
			url  : $server+'ajax.php',
			data: "f=enviarcomentario"+post+mensagem,
			success :  function(response){            
				if(response.cod == "1"){
					document.getElementById("mensagem").value = "";
				}
				document.getElementById("comentario-enviar").addEventListener("click", enviarcomentario, false);
			},
			error: function(){
				document.getElementById("comentario-enviar").addEventListener("click", enviarcomentario, false);
			}
		});
		document.getElementById("comentario-enviar").blur();
	}else if(id&&titulo&&arquivo){
		var formData = $("#novocomentario-form").serialize();
		document.getElementById("novocomentario-btn").removeEventListener("click", enviarcomentario, false);
		var post = "&id="+id;
		$.ajax({
			dataType  : 'json',
			type : 'POST',
			url  : $server+'ajax.php',
			data: "f=enviarcomentario&"+formData+post,
			success :  function(response){            
				if(response.cod == "1"){
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
				}
				document.getElementById("novocomentario-btn").addEventListener("click", enviarcomentario, false);
			},
			error: function(){
				document.getElementById("novocomentario-btn").addEventListener("click", enviarcomentario, false);
			}
		});
		document.getElementById("novocomentario-btn").blur();
	}
}

/* Carregamento página foruns.html */
function loadforuns(){ 
	var id = getURLParameters('id');
	if(id){var usuario = "&usuario="+id;}else{var usuario = "";}
	var pesquisa = document.getElementById("pesquisa").value;
	if(pesquisa){var pesquisa = "&pesquisa="+pesquisa;document.getElementById("pesquisa").focus();}else{var pesquisa = "";}
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=meusforuns"+pesquisa+usuario,
		success :  function(response){
			if(id&&response.nome){
				document.getElementById("nomepagina").innerHTML = response.nome;
				document.getElementById("btnb").classList.add("d-none");
				document.getElementById("btnv").classList.remove("d-none");
			}else{
				document.getElementById("nomepagina").innerHTML = "Fóruns de Discussão";
				document.getElementById("btnv").classList.add("d-none");
				document.getElementById("btnb").classList.remove("d-none");
				document.getElementById("foruns-novo").classList.remove("d-none");
			}            
			if(response.cod == "1"){
				document.getElementById("meusforuns-list").innerHTML = response.html;
				document.getElementById("meusforuns-card").classList.remove("d-none");
				if(pesquisa){
					document.getElementById("meusforuns-resultado").classList.remove("d-none");
					document.getElementById("meusforuns-resultado").innerHTML = response.resultado;
				}else{
					document.getElementById("meusforuns-resultado").classList.add("d-none");
					document.getElementById("meusforuns-resultado").innerHTML = "";
				}
			}
		}
	});
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=seguindoforuns"+pesquisa+usuario,
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById("seguindoforuns-list").innerHTML = response.html;
				if(!id){document.getElementById("seguindoforuns-card").classList.remove("d-none");}
				if(pesquisa){
					document.getElementById("seguindoforuns-resultado").classList.remove("d-none");
					document.getElementById("seguindoforuns-resultado").innerHTML = response.resultado;
				}else{
					document.getElementById("seguindoforuns-resultado").classList.add("d-none");
					document.getElementById("seguindoforuns-resultado").innerHTML = "";
				}
			}
		}
	});
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=privadosforuns"+pesquisa,
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById("privadosforuns-list").innerHTML = response.html;
				if(!id){document.getElementById("privadosforuns-card").classList.remove("d-none");}
				if(pesquisa){
					document.getElementById("privadosforuns-resultado").classList.remove("d-none");
					document.getElementById("privadosforuns-resultado").innerHTML = response.resultado;
				}else{
					document.getElementById("privadosforuns-resultado").classList.add("d-none");
					document.getElementById("privadosforuns-resultado").innerHTML = "";
				}
			}
		}
	});
}
/* Carregamento página novoforum.html */
function loadnovoforum(){
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=fotoperfil",
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById("fotoperfil-img").src = response.foto;
			}
		}
	});
}

/* Carregamento página forum.html */
function loadforum(){
	var id = getURLParameters('id');
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=forum&id="+id,
		success :  function(response){
			if(response.cod == "1"){
				document.getElementById("forum-list").innerHTML = response.html;
			}
		}
	});
}

/* Carregamento página eventos.html */
function loadeventos(){ 
	var id = getURLParameters('id');
	if(id){var usuario = "&usuario="+id;}else{var usuario = "";}
	var pesquisa = document.getElementById("pesquisa").value;
	if(pesquisa){var pesquisa = "&pesquisa="+pesquisa;document.getElementById("pesquisa").focus();}else{var pesquisa = "";}
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=meuseventos"+pesquisa+usuario,
		success :  function(response){
			if(id&&response.nome){
				document.getElementById("nomepagina").innerHTML = response.nome;
				document.getElementById("btnb").classList.add("d-none");
				document.getElementById("btnv").classList.remove("d-none");
			}else{
				document.getElementById("nomepagina").innerHTML = "Eventos";
				document.getElementById("btnv").classList.add("d-none");
				document.getElementById("btnb").classList.remove("d-none");
				document.getElementById("eventos-novo").classList.remove("d-none");
			}            
			if(response.cod == "1"){
				document.getElementById("meuseventos-list").innerHTML = response.html;
				document.getElementById("meuseventos-card").classList.remove("d-none");
				if(pesquisa){
					document.getElementById("meuseventos-resultado").classList.remove("d-none");
					document.getElementById("meuseventos-resultado").innerHTML = response.resultado;
				}else{
					document.getElementById("meuseventos-resultado").classList.add("d-none");
					document.getElementById("meuseventos-resultado").innerHTML = "";
				}
			}
		}
	});
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=seguindoeventos"+pesquisa+usuario,
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById("seguindoeventos-list").innerHTML = response.html;
				if(!id){document.getElementById("seguindoeventos-card").classList.remove("d-none");}
				if(pesquisa){
					document.getElementById("seguindoeventos-resultado").classList.remove("d-none");
					document.getElementById("seguindoeventos-resultado").innerHTML = response.resultado;
				}else{
					document.getElementById("seguindoeventos-resultado").classList.add("d-none");
					document.getElementById("seguindoeventos-resultado").innerHTML = "";
				}
			}
		}
	});
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=populareseventos"+pesquisa,
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById("populareseventos-list").innerHTML = response.html;
				if(!id){document.getElementById("populareseventos-card").classList.remove("d-none");}
				if(pesquisa){
					document.getElementById("populareseventos-resultado").classList.remove("d-none");
					document.getElementById("populareseventos-resultado").innerHTML = response.resultado;
				}else{
					document.getElementById("populareseventos-resultado").classList.add("d-none");
					document.getElementById("populareseventos-resultado").innerHTML = "";
				}
			}
		}
	});
}
/* Carregamento página novoevento.html */
function loadnovoevento(){
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=fotoperfil",
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById("fotoperfil-img").src = response.foto;
			}
		}
	});
	estados("estado");
}

/* Carregamento página evento.html */
function loadevento(){
	var id = getURLParameters('id');
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=evento&id="+id,
		success :  function(response){
			if(response.cod == "1"){
				document.getElementById("evento-list").innerHTML = response.html;
			}
		}
	});
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=participantes&id="+id,
		success :  function(response){
			if(response.cod == "1"){
				$("#participantes-list").append(response.html);
			}
		}
	});
}

/* Carregamento página oportunidades.html */
function loadoportunidades(){ 
	var id = getURLParameters('id');
	if(id){var usuario = "&usuario="+id;}else{var usuario = "";}
	var pesquisa = document.getElementById("pesquisa").value;
	if(pesquisa){var pesquisa = "&pesquisa="+pesquisa;document.getElementById("pesquisa").focus();}else{var pesquisa = "";}
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=oportunidades"+pesquisa+usuario,
		success :  function(response){      
			if(response.cod == "1"){
				document.getElementById("oportunidades-list").innerHTML = response.html;
			}
		}
	});
}
/* Carregamento página novaoportunidade.html */
function loadnovaoportunidade(){
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=fotoperfil",
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById("fotoperfil-img").src = response.foto;
			}
		}
	});
}

/* Carregamento página oportunidade.html */
function loadoportunidade(){
	var id = getURLParameters('id');
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=oportunidade&id="+id,
		success :  function(response){
			if(response.cod == "1"){
				document.getElementById("fotoperfil-img").src = response.foto;
				document.getElementById("titulo").innerHTML = response.titulo;
				document.getElementById("nome").innerHTML = response.nome;
				document.getElementById("barra").innerHTML = response.barra;
				document.getElementById("problema-texto").innerHTML = response.problema;
				if(response.meu=="1"){
					$.ajax({
						dataType  : 'json',
						type : 'POST',
						url  : $server+'ajax.php',
						data: "f=investidores&id="+id,
						success :  function(response){
							if(response.cod == "1"){
								document.getElementById("ajudar-list").innerHTML = response.html;
								document.getElementById("ajudar").classList.remove("d-none");
							}
						}
					});
				}else{
					document.getElementById("ajudar").classList.add("d-none");
					$("#problema").append("<button id=\"ajudar-btn\" class=\"btnpro mb-0 Mt-10 w-100 pesquisa-corpo-btnnovo\" onclick=\"window.location = 'ajudar.html?id="+id+"'\">Me interesso em ajudar</button>");
				}
			}
		}
	});
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=solucoes&id="+id,
		success :  function(response){
			if(response.cod == "1"){
				$("#solucoes-list").append("<div>Soluções propostas</div>"+response.html);
				document.getElementById("solucoes-list").classList.remove("d-none");
			}
		}
	});
}

/* Carregamento estados */
function estados(id,select){
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=estados",
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById(id).innerHTML = response.html;
				if(select){document.getElementById(id).value = select;}
			}
		}
	});
} 
/* Carregamento cidades */
function cidades(id,estado,select){
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=cidades&estado="+estado,
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById(id).innerHTML = response.html;
				if(select){document.getElementById(id).value = select;}
			}
		}
	});
} 

/* Carregamento player.html */
function progressoaudio(){
	var Zaudio = document.getElementById('player-audio').seekable.end(0);
    var Naudio = document.getElementById('player-audio').played.end(0);
    var reproduzido = (Naudio/Zaudio)*100;
    document.getElementById('player-progresso').style.width = reproduzido+"%";
}
function carregadoaudio(){
	var Zaudio = document.getElementById('player-audio').seekable.end(0);
    var Naudio = document.getElementById('player-audio').buffered.end(0);
    var Ncarregado = (Naudio/Zaudio)*100;
    document.getElementById('player-carregado').style.width = Ncarregado+"%";
    if(Ncarregado==100){clearInterval(carregado);}
}

/* Carregamento podcasts.html */
function loadpodcasts(){ 
	var pesquisa = document.getElementById("pesquisa").value;
	if(pesquisa){var pesquisa = "&pesquisa="+pesquisa;document.getElementById("pesquisa").focus();}else{var pesquisa = "";}
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=podcasts"+pesquisa,
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById("podcasts-list").innerHTML = response.html;
				if(response.resultado){
					document.getElementById("podcasts-resultado").innerHTML = response.resultado;
				}else{
					document.getElementById("podcasts-resultado").innerHTML = "";
				}
			}
		}
	});
}
function loadpodcast(){
	var id = getURLParameters('id');
	if(id){var canal = "&canal="+id;}else{var canal = "";}
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=podcast"+canal,
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById("nomepagina").innerHTML = response.nomecanal;
				document.getElementById("canal-img").src = response.fotocanal;
				document.getElementById("canal-dados").innerHTML = response.canaldados;
				var ths = document.getElementById("canal-salvar");
				if(response.salvar=="Salvar"){
					ths.classList.remove("c2");
					ths.classList.add("c3");
					ths.children[0].src='imagens/svg/metro-floppy-disk.svg';
					ths.children[1].innerHTML=response.salvar;
				}else if(response.salvar=="Descartar"){
					ths.classList.remove("c3");
					ths.classList.add("c2");
					ths.children[0].src='imagens/svg/metro-floppy-disk2.svg';
					ths.children[1].innerHTML=response.salvar;
				}
				var thc = document.getElementById("canal-compartilhar");
				if(response.compartilhar=="Compartilhar"){
					thc.classList.remove("c2");
					thc.classList.add("c3");
					thc.children[0].src='imagens/svg/awesome-share-square.svg';
					thc.children[1].innerHTML=response.compartilhar;
				}else if(response.compartilhar=="Compartilhado"){
					thc.classList.remove("c3");
					thc.classList.add("c2");
					thc.children[0].src='imagens/svg/awesome-share-square3.svg';
					thc.children[1].innerHTML=response.compartilhar;
				}
				var thv = document.getElementById("canal-votar");
				if(response.avaliar=="Avaliar"){
					thv.classList.remove("c2");
					thv.classList.add("c3");
					thv.children[0].src='imagens/svg/feather-star2.svg';
					thv.children[1].innerHTML=response.avaliar;
				}else if(response.avaliar=="Avaliado"){
					thv.classList.remove("c3");
					thv.classList.add("c2");
					thv.children[0].src='imagens/svg/feather-star4.svg';
					thv.children[1].innerHTML=response.avaliar;
				}
				document.getElementById("podcast-list").innerHTML = response.html;
			}
		}
	});
}

/* Carregamento página notificacoes.html */
function loadnotificacoes(){
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=notificacoes",
		success :  function(response){
			if(response.cod == "1"){
				document.getElementById("notificacoes-list").innerHTML = response.html;
			}
		}
	});
}

/* Carregamento página destaques.html */
function loaddestaques(tempo='d'){
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=destaques&tempo="+tempo,
		success :  function(response){
			if(response.cod == "1"){
				document.getElementById("destaques-list").innerHTML = response.html;
				document.getElementById("destaques-aba1").classList.remove("ativo");
				document.getElementById("destaques-aba2").classList.remove("ativo");
				document.getElementById("destaques-aba3").classList.remove("ativo");
				if(tempo=='d'){
					document.getElementById("destaques-aba1").classList.add("ativo");
				}else if(tempo=='s'){
					document.getElementById("destaques-aba2").classList.add("ativo");
				}else if(tempo=='m'){
					document.getElementById("destaques-aba3").classList.add("ativo");
				}
			}
		}
	});
}

/* Carregamento página perfil.html */
function loadperfil(){ 
	var id = getURLParameters('id');
	if(id){var usuario = "&usuario="+id;var redirect = "&id="+id;}else{var usuario = "";var redirect = "";}
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=perfil"+usuario,
		success :  function(response){
			if(id){
				document.getElementById("nomepagina").innerHTML = "Perfil";
				document.getElementById("btnb").classList.add("d-none");
				document.getElementById("btnv").classList.remove("d-none");
				document.getElementById("perfilmensagem").classList.remove("d-none");
				$("#perfil-list").prepend(response.denunciar);
				if(response.seguir==0){
					document.getElementById("perfilseguir").classList.remove("d-none");
					document.getElementById("perfildesseguir").classList.add("d-none");
					document.getElementById("perfileditar").classList.add("d-none");
				}else if(response.seguir>=0){
					document.getElementById("perfilseguir").classList.add("d-none");
					document.getElementById("perfildesseguir").classList.remove("d-none");
					document.getElementById("perfileditar").classList.add("d-none");
				}
			}else{
				document.getElementById("nomepagina").innerHTML = "Meu Perfil";
				document.getElementById("btnv").classList.add("d-none");
				document.getElementById("btnb").classList.remove("d-none");
				document.getElementById("perfilseguir").classList.add("d-none");
				document.getElementById("perfildesseguir").classList.add("d-none");
				document.getElementById("perfileditar").classList.remove("d-none");
				document.getElementById("perfilmensagem").classList.add("d-none");
			}
			if(response.cod == "1"){
				document.getElementById("fotocapa-img").style.backgroundImage = "url('"+response.capa+"')";
				document.getElementById("fotoperfil-img").src = response.perfil;
				document.getElementById("nome").innerHTML = response.nome;
				if(response.local!=""){
					document.getElementById("perfillocal-texto").innerHTML = response.local;
					document.getElementById("perfillocal").classList.remove("d-none");
				}
				if(response.profissao!=""){
					document.getElementById("perfilprofissao-texto").innerHTML = response.profissao;
					document.getElementById("perfilprofissao").classList.remove("d-none");
				}
				if(response.empresa!=""){
					document.getElementById("perfilempresa-texto").innerHTML = response.empresa;
					document.getElementById("perfilempresa").classList.remove("d-none");
				}
				document.getElementById("perfilseguindo-texto").innerHTML = response.seguindo;
				if(response.seguindo>0){
					document.getElementById("perfilseguindo").addEventListener("click", function () {window.location = "seguidores.html?aba=seguindo"+redirect;}, false);
				}
				document.getElementById("perfilseguidores-texto").innerHTML = response.seguidores;
				if(response.seguidores>0){
					document.getElementById("perfilseguidores").addEventListener("click", function () {window.location = "seguidores.html?aba=seguidores"+redirect;}, false);
				}
				document.getElementById("perfileventos-texto").innerHTML = response.eventos;
				if(response.eventos>0){
					document.getElementById("perfileventos").addEventListener("click", function () {window.location = "eventos.html?"+redirect;}, false);
				}
				document.getElementById("perfilforuns-texto").innerHTML = response.foruns;
				if(response.foruns>0){
					document.getElementById("perfilforuns").addEventListener("click", function () {window.location = "foruns.html?"+redirect;}, false);
				}
				if(response.profissional==1){
					document.getElementById("perfilprocriado-texto").innerHTML = response.criado;
					document.getElementById("perfilproenvolvido-texto").innerHTML = response.envolvido;
					document.getElementById("perfilprovidas-texto").innerHTML = response.vidas;
					document.getElementById("perfilprofissional").classList.add("d-flex");
					document.getElementById("perfilprofissional").classList.remove("d-none");
				}
			}
		}
	});
}

/* Carregamento página perfil.html */
function loadeditar(){ 
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=editar",
		success :  function(response){
			if(response.cod == "1"){
				document.getElementById("fotocapa-img").style.backgroundImage = "url('"+response.capa+"')";
				document.getElementById("fotoperfil-img").src = response.perfil;
				document.getElementById("nome").innerHTML = response.nome;
				document.getElementById("editarnome").value = response.nome;
				if(response.local!=""){
					document.getElementById("perfillocal-texto").innerHTML = response.local;
					document.getElementById("perfillocal").classList.remove("d-none");
					estados("estado",response.estado);
					cidades("cidade",response.estado,response.cidade);
				}
				if(response.profissao!=""){
					document.getElementById("perfilprofissao-texto").innerHTML = response.profissao;
					document.getElementById("perfilprofissao").classList.remove("d-none");
					document.getElementById("editarprofissao-texto").value = response.profissao;
				}
				if(response.cargoempresa!=""){
					document.getElementById("perfilempresa-texto").innerHTML = response.cargoempresa;
					document.getElementById("perfilempresa").classList.remove("d-none");
					document.getElementById("editarcargo-texto").value = response.cargo;
					document.getElementById("editarempresa-texto").value = response.empresa;
				}
			}
		}
	});
}

/* Carregamento página salvos.html */
function loadsalvos(){ 
	var pesquisa = document.getElementById("pesquisa").value;
	var posts = "&posts="+document.getElementById("salvos-posts-val").value;
	var foruns = "&foruns="+document.getElementById("salvos-foruns-val").value;
	var eventos = "&eventos="+document.getElementById("salvos-eventos-val").value;
	var podcasts = "&podcasts="+document.getElementById("salvos-podcasts-val").value;
	if(pesquisa){var pesquisa = "&pesquisa="+pesquisa;document.getElementById("pesquisa").focus();}else{var pesquisa = "";}
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=salvos"+posts+foruns+eventos+podcasts+pesquisa,
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById("salvos-list").innerHTML = response.html;
			}
		}
	});
}

function player() {
	var player = document.getElementById("player");
	if(player){
		var paginabar = document.getElementById("player-bar");
		var paginaarquivo = document.getElementById("player-arquivo");
		var playerbar = document.getElementById("player-bar");
		var playeraudio = document.getElementById("player-audio");
		if(document.getElementById("player").classList.contains("d-none")){var barra = 0;}else{var barra = 1;}
		if(paginabar){
			if(paginabar.value=="X"){
				paginabar.value = barra;
			}
		}
		if((paginabar.value==0||playerbar.value==0)&&barra==1){
			document.getElementById("player").classList.add("d-none");			
			document.getElementById("player").classList.remove("d-flex");
			playerbar.value = 0;
			paginabar.value = 0;
		}else if((paginabar.value==1||playerbar.value==1)&&barra==0){		
			document.getElementById("player").classList.add("d-flex");
			document.getElementById("player").classList.remove("d-none");
			playerbar.value = 1;
			paginabar.value = 1;
		}
		if(paginaarquivo){
			if(playeraudio.src!=paginaarquivo.value&&paginaarquivo.value){
				playeraudio.src = paginaarquivo.value;
			}
		}
	}
}
var progresso;
var carregado;
window.setInterval(player, 500);
function abrirplayer(arquivo,titulo,autor){
	document.getElementById("player-arquivo").value = $server+'upload/podcasts/'+arquivo;
	document.getElementById("player-bar").value = 1;
	document.getElementById("player-titulo").innerHTML = titulo;
	document.getElementById("player-autor").innerHTML = autor;
	setTimeout(function(){
		document.getElementById('player-audio').play();
		document.getElementById("play").classList.add("d-none");
		document.getElementById("pause").classList.remove("d-none");
		progresso = setInterval(progressoaudio, 1000);
	}, 1000);
}