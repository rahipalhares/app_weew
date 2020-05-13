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

/* Pegar nome do arquivo */
function nomeupload(file, pagina){
	if(file){
		document.getElementById(pagina+"-arquivo").innerHTML = pegarNomeArquivo(file);
		document.getElementById(pagina+"-uploads").classList.add("d-none");
		document.getElementById(pagina+"-uploads").classList.remove("d-flex");
		document.getElementById(pagina+"-arquivo").classList.remove("d-none");
	}else{
		document.getElementById(pagina+"-arquivo").classList.add("d-none");
		document.getElementById(pagina+"-uploads").classList.add("d-flex");
		document.getElementById(pagina+"-uploads").classList.remove("d-none");
	}
}
function pegarNomeArquivo(caminhoArquivo) {
	var objRE = new RegExp(/([^\/\\]+)$/);
	var nomeArquivo = objRE.exec(caminhoArquivo);
	if (nomeArquivo == null) {
		return null;
	}else{
		return nomeArquivo[0];
	}
}

/* Curtir */
function curtir(th,id){
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=curtir&id="+id,
		success :  function(response){            
			if(response.cod == "1"){
				if(response.atual=="Curtir"){
					th.classList.remove("c2");
					th.classList.add("c3");
					th.children[0].src='imagens/svg/awesome-heart.svg';
					th.children[1].innerHTML=response.atual;
				}else if(response.atual=="Descurtir"){
					th.classList.remove("c3");
					th.classList.add("c2");
					th.children[0].src='imagens/svg/awesome-heart2.svg';
					th.children[1].innerHTML=response.atual;
				}
			}
		}
	});
}

/* Salvar */
function salvar(th,id){
	if(typeof th=="string"){var th = document.getElementById(th);}
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=salvar&id="+id,
		success :  function(response){            
			if(response.cod == "1"){
				if(response.atual=="Salvar"){
					th.classList.remove("c2");
					th.classList.add("c3");
					th.children[0].src='imagens/svg/metro-floppy-disk.svg';
					th.children[1].innerHTML=response.atual;
				}else if(response.atual=="Descartar"){
					th.classList.remove("c3");
					th.classList.add("c2");
					th.children[0].src='imagens/svg/metro-floppy-disk2.svg';
					th.children[1].innerHTML=response.atual;
				}
			}
		}
	});
}

/* Compartilhar */
function compartilhar(id){
	modalConfirm("compartilharagora",'',"Deseja compartilhar esse post em seu feed?",id);
}
function compartilharagora(id){	
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=compartilhar&id="+id,
		success :  function(response){            
			if(response.cod == "1"){
				if(response.atual=="Compartilhar"){
					if(document.getElementsByName(id)){
						var l = document.getElementsByName(id).length;
						for(var i = 0;i<l;i++){
							document.getElementsByName(id)[i].children[1].children[2].classList.remove("c2");
							document.getElementsByName(id)[i].children[1].children[2].classList.add("c3");
							document.getElementsByName(id)[i].children[1].children[2].children[0].src='imagens/svg/awesome-share-square.svg';
							document.getElementsByName(id)[i].children[1].children[2].children[1].innerHTML=response.atual;
						}
					}
					/*th.classList.remove("c2");
					th.classList.add("c3");
					th.children[0].src='imagens/svg/awesome-share-square.svg';
					th.children[1].innerHTML=response.atual;*/
				}else if(response.atual=="Compartilhado"){
					if(document.getElementsByName(id)){
						var l = document.getElementsByName(id).length;
						for(var i = 0;i<l;i++){
							document.getElementsByName(id)[i].children[1].children[2].classList.remove("c3");
							document.getElementsByName(id)[i].children[1].children[2].classList.add("c2");
							document.getElementsByName(id)[i].children[1].children[2].children[0].src='imagens/svg/awesome-share-square3.svg';
							document.getElementsByName(id)[i].children[1].children[2].children[1].innerHTML=response.atual;
						}
					}
					/*th.classList.remove("c3");
					th.classList.add("c2");
					th.children[0].src='imagens/svg/awesome-share-square3.svg';
					th.children[1].innerHTML=response.atual;*/
				}
			}
		}
	});
}
function compartilharevento(id){
	modalConfirm("compartilhareventoagora",'',"Deseja compartilhar esse evento em seu feed?",id);
}
function compartilhareventoagora(id){	
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=compartilhar&id="+id,
		success :  function(response){            
			if(response.cod == "1"){
				location.reload();
			}
		}
	});
}
function compartilharcanal(id){
	modalConfirm("compartilharcanalagora",'',"Deseja compartilhar esse canal em seu feed?",id);
}
function compartilharcanalagora(id){	
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=compartilhar&id="+id,
		success :  function(response){            
			if(response.cod == "1"){
				location.reload();
			}
		}
	});
}

/* Seguir e Deixar de seguir */
function seguir(){
	var id = getURLParameters('id');
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=seguir&id="+id,
		success :  function(response){            
			if(response.cod == "1"){
				location.reload();
			}
		}
	});
}
function acompanhar(id){
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=acompanhar&id="+id,
		success :  function(response){            
			if(response.cod == "1"){
				location.reload();
			}
		}
	});
}

/* Denunciar */
function denunciar(mensagem,id, tipo){
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=denunciar&mensagem="+mensagem+"&id="+id+"&tipo="+tipo,
		success :  function(response){            
			if(response.cod == "1"){
				modalAlert("Denúncia enviada com sucesso!");
			}else if(response.cod == "2"){
				modalAlert(response.msg);
			}
		}
	});
}

/* Avaliar */
function nota1(){
	document.getElementById("star1").src = "imagens/svg/feather-star3.svg";
	document.getElementById("star2").src = "imagens/svg/feather-star2.svg";
	document.getElementById("star3").src = "imagens/svg/feather-star2.svg";
	document.getElementById("star4").src = "imagens/svg/feather-star2.svg";
	document.getElementById("star5").src = "imagens/svg/feather-star2.svg";
	document.getElementById("modal-nota").value = "1";
}
function nota2(){
	document.getElementById("star1").src = "imagens/svg/feather-star3.svg";
	document.getElementById("star2").src = "imagens/svg/feather-star3.svg";
	document.getElementById("star3").src = "imagens/svg/feather-star2.svg";
	document.getElementById("star4").src = "imagens/svg/feather-star2.svg";
	document.getElementById("star5").src = "imagens/svg/feather-star2.svg";
	document.getElementById("modal-nota").value = "2";
}
function nota3(){
	document.getElementById("star1").src = "imagens/svg/feather-star3.svg";
	document.getElementById("star2").src = "imagens/svg/feather-star3.svg";
	document.getElementById("star3").src = "imagens/svg/feather-star3.svg";
	document.getElementById("star4").src = "imagens/svg/feather-star2.svg";
	document.getElementById("star5").src = "imagens/svg/feather-star2.svg";
	document.getElementById("modal-nota").value = "3";
}
function nota4(){
	document.getElementById("star1").src = "imagens/svg/feather-star3.svg";
	document.getElementById("star2").src = "imagens/svg/feather-star3.svg";
	document.getElementById("star3").src = "imagens/svg/feather-star3.svg";
	document.getElementById("star4").src = "imagens/svg/feather-star3.svg";
	document.getElementById("star5").src = "imagens/svg/feather-star2.svg";
	document.getElementById("modal-nota").value = "4";
}
function nota5(){
	document.getElementById("star1").src = "imagens/svg/feather-star3.svg";
	document.getElementById("star2").src = "imagens/svg/feather-star3.svg";
	document.getElementById("star3").src = "imagens/svg/feather-star3.svg";
	document.getElementById("star4").src = "imagens/svg/feather-star3.svg";
	document.getElementById("star5").src = "imagens/svg/feather-star3.svg";
	document.getElementById("modal-nota").value = "5";
}
function avaliar(nota,id){
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=avaliar&id="+id+"&nota="+nota,
		success :  function(response){            
			if(response.cod == "1"){
				modalAlert("Nota enviada com sucesso!");
			}else if(response.cod == "2"){
				modalAlert(response.msg);
			}
		}
	});
}

/* Cadastro */
function cadastro(){
	document.getElementById("cadastro-btn").style.display = "none";
	document.getElementById("aguarde").style.display = "block";
	var celularid = device.uuid;
	var formData = $("#cadastro-form").serialize();
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=cadastro&"+formData+"&celularid="+celularid,
		success :  function(response){  
			document.getElementById("aguarde").style.display = "none";
			document.getElementById("cadastro-btn").style.display = "block";          
			if(response.cod == "1"){
				window.location.href = "inicio.html";
			}else if(response.cod == "2"){
				modalAlert(response.msg);
			}else if(response.cod == "3"){
				modalAlert(response.msg);
				setTimeout(function() {window.location.href = "index.html";}, 3000);				
			}else if(response.cod=="0") {
				document.getElementById(response.ide).classList.add("is-invalid");
				setTimeout(function() {document.getElementById(response.ide).classList.remove("is-invalid");}, 3000);
			}
		},
		error: function (xhr, ajaxOptions, thrownError) {
		  document.getElementById("aguarde").style.display = "none";
		  document.getElementById("cadastro-btn").style.display = "block";
		}
	});
	return false;
}

/* Novo forum */
function novoforum(){
	var formData = $("#novoforum-form").serialize();
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=novoforum&"+formData,
		success :  function(response){            
			if(response.cod == "1"){
				window.location = 'forum.html?id='+response.id;
			}else if(response.cod == "0"){
				document.getElementById(response.ide).classList.add("border-danger");
				document.getElementById(response.ide).addEventListener("click", function(){
					document.getElementById(response.ide).classList.remove("border-danger");
				}, false);
			}else if(response.cod == "2"){
				modalAlert(response.msg);
			}
		}
	});
	return false;
}

/* Novo evento */
function novoevento(){
	var formData = $("#novoevento-form").serialize();
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=novoevento&"+formData,
		success :  function(response){            
			if(response.cod == "1"){
				window.location = 'evento.html?id='+response.id;
			}else if(response.cod == "0"){
				document.getElementById(response.ide).classList.add("border-danger");
				document.getElementById(response.ide).addEventListener("click", function(){
					document.getElementById(response.ide).classList.remove("border-danger");
				}, false);
			}else if(response.cod == "2"){
				modalAlert(response.msg);
			}
		}
	});
	return false;
}

/* Novo post */
function novopost(){
	var formData = $("#novopost-form").serialize();
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=novopost&"+formData,
		success :  function(response){            
			if(response.cod == "1"){
				location.reload();
			}else if(response.cod == "0"){
				document.getElementById(response.ide).classList.add("border-danger");
				document.getElementById(response.ide).addEventListener("click", function(){
					document.getElementById(response.ide).classList.remove("border-danger");
				}, false);
			}else if(response.cod == "2"){
				modalAlert(response.msg);
			}
		}
	});
	return false;
}

/* Cadastro profissional */
function cadastrofisica(){
	var formData = $("#cadastrofisica-form").serialize();
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=cadastrofisica&"+formData,
		success :  function(response){            
			if(response.cod == "1"){
				window.location = 'oportunidades.html';
			}else if(response.cod == "2"){
				modalAlert(response.msg);
			}
		}
	});
	return false;
}
function cadastrojuridica(){
	var formData = $("#cadastrojuridica-form").serialize();
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=cadastrojuridica&"+formData,
		success :  function(response){            
			if(response.cod == "1"){
				window.location = 'oportunidades.html';
			}else if(response.cod == "2"){
				modalAlert(response.msg);
			}
		}
	});
	return false;
}

/* Nova oportunidade */
function novaoportunidade(){
	var formData = $("#novaoportunidade-form").serialize();
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=novaoportunidade&"+formData,
		success :  function(response){            
			if(response.cod == "1"){
				window.location = 'oportunidade.html?id='+response.id;
			}else if(response.cod == "0"){
				document.getElementById(response.ide).classList.add("border-danger");
				document.getElementById(response.ide).addEventListener("click", function(){
					document.getElementById(response.ide).classList.remove("border-danger");
				}, false);
			}else if(response.cod == "2"){
				modalAlert(response.msg);
			}
		}
	});
	return false;
}

/* Investir */
function investirtecnologia(id){
	var prazo = document.getElementById("prazo").value;
	var descricao = document.getElementById("descricao").value;
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=investir&escolher=t&resumo="+prazo+"&descricao="+descricao+"&id="+id,
		success :  function(response){            
			if(response.cod == "1"){
				window.location = 'oportunidade.html?id='+id;
			}else if(response.cod == "0"){
				document.getElementById(response.ide).classList.add("border-danger");
				document.getElementById(response.ide).addEventListener("click", function(){
					document.getElementById(response.ide).classList.remove("border-danger");
				}, false);
			}else if(response.cod == "2"){
				modalAlert(response.msg);
			}
		}
	});
	return false;
}
function investirdinheiro(id){
	var valor = document.getElementById("valor").value;
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=investir&escolher=d&resumo="+valor+"&id="+id,
		success :  function(response){            
			if(response.cod == "1"){
				window.location = 'oportunidade.html?id='+id;
			}else if(response.cod == "0"){
				document.getElementById(response.ide).classList.add("border-danger");
				document.getElementById(response.ide).addEventListener("click", function(){
					document.getElementById(response.ide).classList.remove("border-danger");
				}, false);
			}else if(response.cod == "2"){
				modalAlert(response.msg);
			}
		}
	});
	return false;
}

/* Editar perfil */
function salvardadosperfil(){
	var formData = $("#editardados-form").serialize();
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=editardados&"+formData,
		success :  function(response){            
			if(response.cod == "1"){
				location.reload();
			}else if(response.cod == "2"){
				modalAlert(response.msg);
			}
		}
	});
	return false;
}

/* Uploads */
$(function () {
    $('#novoforum-foto').change(function (event) {
    	document.getElementById("novoforum-uploads").classList.add("d-none");
		document.getElementById("novoforum-uploads").classList.remove("d-flex");
    	document.getElementById("novoforum-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('foto', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("novoforum-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){   
				document.getElementById("novoforum-barra").classList.add("d-none"); 
				document.getElementById("novoforum-progresso").style.width = "0%";        
				if(response.cod == "1"){
					document.getElementById("novoforum-arquivo").innerHTML = "<img src='"+$server+"upload/"+response.src+"' height='auto' width='100%'/>";
					document.getElementById("arquivo").value = response.src;
					document.getElementById("novoforum-deletar").setAttribute('onclick', 'deletarupload(\''+response.src+'\')');
					document.getElementById("novoforum-deletar").classList.add("d-flex");
					document.getElementById("novoforum-deletar").classList.remove("d-none");
					document.getElementById("novoforum-uploads").classList.add("d-none");
					document.getElementById("novoforum-uploads").classList.remove("d-flex");
					document.getElementById("novoforum-arquivo").classList.remove("d-none");
				}else{
					document.getElementById("novoforum-arquivo").classList.add("d-none");
					document.getElementById("novoforum-uploads").classList.add("d-flex");
					reset($("#novoforum-foto"));
					document.getElementById("novoforum-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("novoforum-barra").classList.add("d-none"); 
				document.getElementById("novoforum-progresso").style.width = "0%";  
				document.getElementById("novoforum-arquivo").classList.add("d-none");
				document.getElementById("novoforum-uploads").classList.add("d-flex");
				reset($("#novoforum-foto"));
				document.getElementById("novoforum-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
    $('#novoforum-pdf').change(function (event) {
    	document.getElementById("novoforum-uploads").classList.add("d-none");
		document.getElementById("novoforum-uploads").classList.remove("d-flex");
    	document.getElementById("novoforum-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('pdf', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("novoforum-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){            
				document.getElementById("novoforum-barra").classList.add("d-none"); 
				document.getElementById("novoforum-progresso").style.width = "0%";  
				if(response.cod == "1"){
					document.getElementById("novoforum-arquivo").innerHTML = "<img src='imagens/svg/ionic-md-document.svg'/><br><span class='c4 fs-06'>"+response.nome+"</span>";
					document.getElementById("arquivo").value = response.src;
					document.getElementById("novoforum-deletar").setAttribute('onclick', 'deletarupload(\''+response.src+'\')');
					document.getElementById("novoforum-deletar").classList.add("d-flex");
					document.getElementById("novoforum-deletar").classList.remove("d-none");
					document.getElementById("novoforum-uploads").classList.add("d-none");
					document.getElementById("novoforum-uploads").classList.remove("d-flex");
					document.getElementById("novoforum-arquivo").classList.remove("d-none");
				}else{
					document.getElementById("novoforum-arquivo").classList.add("d-none");
					document.getElementById("novoforum-uploads").classList.add("d-flex");
					reset($("#novoforum-pdf"));
					document.getElementById("novoforum-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("novoforum-barra").classList.add("d-none"); 
				document.getElementById("novoforum-progresso").style.width = "0%";  
				document.getElementById("novoforum-arquivo").classList.add("d-none");
				document.getElementById("novoforum-uploads").classList.add("d-flex");
				reset($("#novoforum-pdf"));
				document.getElementById("novoforum-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
    $('#novoforum-video').change(function (event) {
    	document.getElementById("novoforum-uploads").classList.add("d-none");
		document.getElementById("novoforum-uploads").classList.remove("d-flex");
    	document.getElementById("novoforum-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('video', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("novoforum-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){   
				document.getElementById("novoforum-barra").classList.add("d-none"); 
				document.getElementById("novoforum-progresso").style.width = "0%";           
				if(response.cod == "1"){
					document.getElementById("novoforum-arquivo").innerHTML = "<video height='auto' width='100%' controls><source src='"+$server+"upload/"+response.src+"' type='video/mp4'></video>";
					document.getElementById("arquivo").value = response.src;
					document.getElementById("novoforum-deletar").setAttribute('onclick', 'deletarupload(\''+response.src+'\')');
					document.getElementById("novoforum-deletar").classList.add("d-flex");
					document.getElementById("novoforum-deletar").classList.remove("d-none");
					document.getElementById("novoforum-uploads").classList.add("d-none");
					document.getElementById("novoforum-uploads").classList.remove("d-flex");
					document.getElementById("novoforum-arquivo").classList.remove("d-none");
				}else{
					document.getElementById("novoforum-arquivo").classList.add("d-none");
					document.getElementById("novoforum-uploads").classList.add("d-flex");
					reset($("#novoforum-video"));
					document.getElementById("novoforum-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("novoforum-barra").classList.add("d-none"); 
				document.getElementById("novoforum-progresso").style.width = "0%";  
				document.getElementById("novoforum-arquivo").classList.add("d-none");
				document.getElementById("novoforum-uploads").classList.add("d-flex");
				reset($("#novoforum-video"));
				document.getElementById("novoforum-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
    $('#novoevento-foto').change(function (event) {
    	document.getElementById("novoevento-uploads").classList.add("d-none");
		document.getElementById("novoevento-uploads").classList.remove("d-flex");
    	document.getElementById("novoevento-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('foto', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("novoevento-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){   
				document.getElementById("novoevento-barra").classList.add("d-none"); 
				document.getElementById("novoevento-progresso").style.width = "0%";        
				if(response.cod == "1"){
					document.getElementById("novoevento-arquivo").innerHTML = "<img src='"+$server+"upload/"+response.src+"' height='auto' width='100%'/>";
					document.getElementById("arquivo").value = response.src;
					document.getElementById("novoevento-deletar").setAttribute('onclick', 'deletarupload(\''+response.src+'\')');
					document.getElementById("novoevento-deletar").classList.add("d-flex");
					document.getElementById("novoevento-deletar").classList.remove("d-none");
					document.getElementById("novoevento-uploads").classList.add("d-none");
					document.getElementById("novoevento-uploads").classList.remove("d-flex");
					document.getElementById("novoevento-arquivo").classList.remove("d-none");
				}else{
					document.getElementById("novoevento-arquivo").classList.add("d-none");
					document.getElementById("novoevento-uploads").classList.add("d-flex");
					reset($("#novoevento-foto"));
					document.getElementById("novoevento-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("novoevento-barra").classList.add("d-none"); 
				document.getElementById("novoevento-progresso").style.width = "0%";  
				document.getElementById("novoevento-arquivo").classList.add("d-none");
				document.getElementById("novoevento-uploads").classList.add("d-flex");
				reset($("#novoevento-foto"));
				document.getElementById("novoevento-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
    $('#novopost-foto').change(function (event) {
    	document.getElementById("novopost-uploads").classList.add("d-none");
		document.getElementById("novopost-uploads").classList.remove("d-flex");
    	document.getElementById("novopost-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('foto', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("novopost-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){   
				document.getElementById("novopost-barra").classList.add("d-none"); 
				document.getElementById("novopost-progresso").style.width = "0%";        
				if(response.cod == "1"){
					document.getElementById("novopost-btn").classList.add("d-none");
					document.getElementById("novopost-arquivo").innerHTML = "<img src='"+$server+"upload/"+response.src+"' height='auto' width='100%'/>";
					document.getElementById("arquivo").value = response.src;
					document.getElementById("titulo").classList.remove("d-none");
					document.getElementById("novopost-deletar").setAttribute('onclick', 'deletarupload(\''+response.src+'\')');
					document.getElementById("novopost-deletar").classList.add("d-flex");
					document.getElementById("novopost-deletar").classList.remove("d-none");
					document.getElementById("novopost-uploads").classList.add("d-none");
					document.getElementById("novopost-uploads").classList.remove("d-flex");
					document.getElementById("novopost-arquivo").classList.remove("d-none");
				}else{
					document.getElementById("titulo").classList.add("d-none");
					document.getElementById("novopost-arquivo").classList.add("d-none");
					document.getElementById("novopost-uploads").classList.add("d-flex");
					reset($("#novopost-foto"));
					document.getElementById("novopost-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("titulo").classList.add("d-none");
				document.getElementById("novopost-barra").classList.add("d-none"); 
				document.getElementById("novopost-progresso").style.width = "0%";  
				document.getElementById("novopost-arquivo").classList.add("d-none");
				document.getElementById("novopost-uploads").classList.add("d-flex");
				reset($("#novopost-foto"));
				document.getElementById("novopost-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
    $('#novopost-pdf').change(function (event) {
    	document.getElementById("novopost-uploads").classList.add("d-none");
		document.getElementById("novopost-uploads").classList.remove("d-flex");
    	document.getElementById("novopost-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('pdf', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("novopost-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){            
				document.getElementById("novopost-barra").classList.add("d-none"); 
				document.getElementById("novopost-progresso").style.width = "0%";  
				if(response.cod == "1"){
					document.getElementById("novopost-btn").classList.add("d-none");
					document.getElementById("novopost-arquivo").innerHTML = "<img src='imagens/svg/ionic-md-document.svg'/><br><span class='c4 fs-06'>"+response.nome+"</span>";
					document.getElementById("arquivo").value = response.src;
					document.getElementById("titulo").classList.remove("d-none");
					document.getElementById("novopost-deletar").setAttribute('onclick', 'deletarupload(\''+response.src+'\')');
					document.getElementById("novopost-deletar").classList.add("d-flex");
					document.getElementById("novopost-deletar").classList.remove("d-none");
					document.getElementById("novopost-uploads").classList.add("d-none");
					document.getElementById("novopost-uploads").classList.remove("d-flex");
					document.getElementById("novopost-arquivo").classList.remove("d-none");
				}else{
					document.getElementById("titulo").classList.add("d-none");
					document.getElementById("novopost-arquivo").classList.add("d-none");
					document.getElementById("novopost-uploads").classList.add("d-flex");
					reset($("#novopost-pdf"));
					document.getElementById("novopost-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("titulo").classList.add("d-none");
				document.getElementById("novopost-barra").classList.add("d-none"); 
				document.getElementById("novopost-progresso").style.width = "0%";  
				document.getElementById("novopost-arquivo").classList.add("d-none");
				document.getElementById("novopost-uploads").classList.add("d-flex");
				reset($("#novopost-pdf"));
				document.getElementById("novopost-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
    $('#novopost-video').change(function (event) {
    	document.getElementById("novopost-uploads").classList.add("d-none");
		document.getElementById("novopost-uploads").classList.remove("d-flex");
    	document.getElementById("novopost-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('video', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("novopost-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){   
				document.getElementById("novopost-barra").classList.add("d-none"); 
				document.getElementById("novopost-progresso").style.width = "0%";           
				if(response.cod == "1"){
					document.getElementById("novopost-btn").classList.add("d-none");
					document.getElementById("novopost-arquivo").innerHTML = "<video height='auto' width='100%' controls><source src='"+$server+"upload/"+response.src+"' type='video/mp4'></video>";
					document.getElementById("arquivo").value = response.src;
					document.getElementById("titulo").classList.remove("d-none");
					document.getElementById("novopost-deletar").setAttribute('onclick', 'deletarupload(\''+response.src+'\')');
					document.getElementById("novopost-deletar").classList.add("d-flex");
					document.getElementById("novopost-deletar").classList.remove("d-none");
					document.getElementById("novopost-uploads").classList.add("d-none");
					document.getElementById("novopost-uploads").classList.remove("d-flex");
					document.getElementById("novopost-arquivo").classList.remove("d-none");
				}else{
					document.getElementById("titulo").classList.add("d-none");
					document.getElementById("novopost-arquivo").classList.add("d-none");
					document.getElementById("novopost-uploads").classList.add("d-flex");
					reset($("#novopost-video"));
					document.getElementById("novopost-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("titulo").classList.add("d-none");
				document.getElementById("novopost-barra").classList.add("d-none"); 
				document.getElementById("novopost-progresso").style.width = "0%";  
				document.getElementById("novopost-arquivo").classList.add("d-none");
				document.getElementById("novopost-uploads").classList.add("d-flex");
				reset($("#novopost-video"));
				document.getElementById("novopost-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
    $('#novaoportunidade-foto').change(function (event) {
    	document.getElementById("novaoportunidade-uploads").classList.add("d-none");
		document.getElementById("novaoportunidade-uploads").classList.remove("d-flex");
    	document.getElementById("novaoportunidade-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('foto', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("novaoportunidade-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){   
				document.getElementById("novaoportunidade-barra").classList.add("d-none"); 
				document.getElementById("novaoportunidade-progresso").style.width = "0%";        
				if(response.cod == "1"){
					document.getElementById("novaoportunidade-arquivo").innerHTML = "<img src='"+$server+"upload/"+response.src+"' height='auto' width='100%'/>";
					document.getElementById("arquivo").value = response.src;
					document.getElementById("novaoportunidade-deletar").setAttribute('onclick', 'deletarupload(\''+response.src+'\')');
					document.getElementById("novaoportunidade-deletar").classList.add("d-flex");
					document.getElementById("novaoportunidade-deletar").classList.remove("d-none");
					document.getElementById("novaoportunidade-uploads").classList.add("d-none");
					document.getElementById("novaoportunidade-uploads").classList.remove("d-flex");
					document.getElementById("novaoportunidade-arquivo").classList.remove("d-none");
				}else{
					document.getElementById("novaoportunidade-arquivo").classList.add("d-none");
					document.getElementById("novaoportunidade-uploads").classList.add("d-flex");
					reset($("#novaoportunidade-foto"));
					document.getElementById("novaoportunidade-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("novaoportunidade-barra").classList.add("d-none"); 
				document.getElementById("novaoportunidade-progresso").style.width = "0%";  
				document.getElementById("novaoportunidade-arquivo").classList.add("d-none");
				document.getElementById("novaoportunidade-uploads").classList.add("d-flex");
				reset($("#novaoportunidade-foto"));
				document.getElementById("novaoportunidade-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
    $('#novaoportunidade-pdf').change(function (event) {
    	document.getElementById("novaoportunidade-uploads").classList.add("d-none");
		document.getElementById("novaoportunidade-uploads").classList.remove("d-flex");
    	document.getElementById("novaoportunidade-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('pdf', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("novaoportunidade-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){            
				document.getElementById("novaoportunidade-barra").classList.add("d-none"); 
				document.getElementById("novaoportunidade-progresso").style.width = "0%";  
				if(response.cod == "1"){
					document.getElementById("novaoportunidade-arquivo").innerHTML = "<img src='imagens/svg/ionic-md-document.svg'/><br><span class='c4 fs-06'>"+response.nome+"</span>";
					document.getElementById("arquivo").value = response.src;
					document.getElementById("novaoportunidade-deletar").setAttribute('onclick', 'deletarupload(\''+response.src+'\')');
					document.getElementById("novaoportunidade-deletar").classList.add("d-flex");
					document.getElementById("novaoportunidade-deletar").classList.remove("d-none");
					document.getElementById("novaoportunidade-uploads").classList.add("d-none");
					document.getElementById("novaoportunidade-uploads").classList.remove("d-flex");
					document.getElementById("novaoportunidade-arquivo").classList.remove("d-none");
				}else{
					document.getElementById("novaoportunidade-arquivo").classList.add("d-none");
					document.getElementById("novaoportunidade-uploads").classList.add("d-flex");
					reset($("#novaoportunidade-pdf"));
					document.getElementById("novaoportunidade-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("novaoportunidade-barra").classList.add("d-none"); 
				document.getElementById("novaoportunidade-progresso").style.width = "0%";  
				document.getElementById("novaoportunidade-arquivo").classList.add("d-none");
				document.getElementById("novaoportunidade-uploads").classList.add("d-flex");
				reset($("#novaoportunidade-pdf"));
				document.getElementById("novaoportunidade-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
    $('#novaoportunidade-video').change(function (event) {
    	document.getElementById("novaoportunidade-uploads").classList.add("d-none");
		document.getElementById("novaoportunidade-uploads").classList.remove("d-flex");
    	document.getElementById("novaoportunidade-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('video', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("novaoportunidade-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){   
				document.getElementById("novaoportunidade-barra").classList.add("d-none"); 
				document.getElementById("novaoportunidade-progresso").style.width = "0%";           
				if(response.cod == "1"){
					document.getElementById("novaoportunidade-arquivo").innerHTML = "<video height='auto' width='100%' controls><source src='"+$server+"upload/"+response.src+"' type='video/mp4'></video>";
					document.getElementById("arquivo").value = response.src;
					document.getElementById("novaoportunidade-deletar").setAttribute('onclick', 'deletarupload(\''+response.src+'\')');
					document.getElementById("novaoportunidade-deletar").classList.add("d-flex");
					document.getElementById("novaoportunidade-deletar").classList.remove("d-none");
					document.getElementById("novaoportunidade-uploads").classList.add("d-none");
					document.getElementById("novaoportunidade-uploads").classList.remove("d-flex");
					document.getElementById("novaoportunidade-arquivo").classList.remove("d-none");
				}else{
					document.getElementById("novaoportunidade-arquivo").classList.add("d-none");
					document.getElementById("novaoportunidade-uploads").classList.add("d-flex");
					reset($("#novaoportunidade-video"));
					document.getElementById("novaoportunidade-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("novaoportunidade-barra").classList.add("d-none"); 
				document.getElementById("novaoportunidade-progresso").style.width = "0%";  
				document.getElementById("novaoportunidade-arquivo").classList.add("d-none");
				document.getElementById("novaoportunidade-uploads").classList.add("d-flex");
				reset($("#novaoportunidade-video"));
				document.getElementById("novaoportunidade-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
    $('#novocomentario-foto').change(function (event) {
    	document.getElementById("novocomentario-uploads").classList.add("d-none");
		document.getElementById("novocomentario-uploads").classList.remove("d-flex");
    	document.getElementById("novocomentario-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('foto', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("novocomentario-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){   
				document.getElementById("novocomentario-barra").classList.add("d-none"); 
				document.getElementById("novocomentario-progresso").style.width = "0%";        
				if(response.cod == "1"){
					document.getElementById("novocomentario-btn").classList.add("d-none");
					document.getElementById("novocomentario-arquivo").innerHTML = "<img src='"+$server+"upload/"+response.src+"' height='auto' width='100%'/>";
					document.getElementById("arquivo").value = response.src;
					document.getElementById("titulo").classList.remove("d-none");
					document.getElementById("novocomentario-deletar").setAttribute('onclick', 'deletarupload(\''+response.src+'\')');
					document.getElementById("novocomentario-deletar").classList.add("d-flex");
					document.getElementById("novocomentario-deletar").classList.remove("d-none");
					document.getElementById("novocomentario-uploads").classList.add("d-none");
					document.getElementById("novocomentario-uploads").classList.remove("d-flex");
					document.getElementById("novocomentario-arquivo").classList.remove("d-none");
				}else{
					document.getElementById("titulo").classList.add("d-none");
					document.getElementById("novocomentario-arquivo").classList.add("d-none");
					document.getElementById("novocomentario-uploads").classList.add("d-flex");
					reset($("#novocomentario-foto"));
					document.getElementById("novocomentario-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("titulo").classList.add("d-none");
				document.getElementById("novocomentario-barra").classList.add("d-none"); 
				document.getElementById("novocomentario-progresso").style.width = "0%";  
				document.getElementById("novocomentario-arquivo").classList.add("d-none");
				document.getElementById("novocomentario-uploads").classList.add("d-flex");
				reset($("#novocomentario-foto"));
				document.getElementById("novocomentario-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
    $('#novocomentario-pdf').change(function (event) {
    	document.getElementById("novocomentario-uploads").classList.add("d-none");
		document.getElementById("novocomentario-uploads").classList.remove("d-flex");
    	document.getElementById("novocomentario-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('pdf', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("novocomentario-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){            
				document.getElementById("novocomentario-barra").classList.add("d-none"); 
				document.getElementById("novocomentario-progresso").style.width = "0%";  
				if(response.cod == "1"){
					document.getElementById("novocomentario-btn").classList.add("d-none");
					document.getElementById("novocomentario-arquivo").innerHTML = "<img src='imagens/svg/ionic-md-document.svg'/><br><span class='c4 fs-06'>"+response.nome+"</span>";
					document.getElementById("arquivo").value = response.src;
					document.getElementById("titulo").classList.remove("d-none");
					document.getElementById("novocomentario-deletar").setAttribute('onclick', 'deletarupload(\''+response.src+'\')');
					document.getElementById("novocomentario-deletar").classList.add("d-flex");
					document.getElementById("novocomentario-deletar").classList.remove("d-none");
					document.getElementById("novocomentario-uploads").classList.add("d-none");
					document.getElementById("novocomentario-uploads").classList.remove("d-flex");
					document.getElementById("novocomentario-arquivo").classList.remove("d-none");
				}else{
					document.getElementById("titulo").classList.add("d-none");
					document.getElementById("novocomentario-arquivo").classList.add("d-none");
					document.getElementById("novocomentario-uploads").classList.add("d-flex");
					reset($("#novocomentario-pdf"));
					document.getElementById("novocomentario-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("titulo").classList.add("d-none");
				document.getElementById("novocomentario-barra").classList.add("d-none"); 
				document.getElementById("novocomentario-progresso").style.width = "0%";  
				document.getElementById("novocomentario-arquivo").classList.add("d-none");
				document.getElementById("novocomentario-uploads").classList.add("d-flex");
				reset($("#novocomentario-pdf"));
				document.getElementById("novocomentario-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
    $('#novocomentario-video').change(function (event) {
    	document.getElementById("novocomentario-uploads").classList.add("d-none");
		document.getElementById("novocomentario-uploads").classList.remove("d-flex");
    	document.getElementById("novocomentario-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('video', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("novocomentario-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){   
				document.getElementById("novocomentario-barra").classList.add("d-none"); 
				document.getElementById("novocomentario-progresso").style.width = "0%";           
				if(response.cod == "1"){
					document.getElementById("novocomentario-btn").classList.add("d-none");
					document.getElementById("novocomentario-arquivo").innerHTML = "<video height='auto' width='100%' controls><source src='"+$server+"upload/"+response.src+"' type='video/mp4'></video>";
					document.getElementById("arquivo").value = response.src;
					document.getElementById("titulo").classList.remove("d-none");
					document.getElementById("novocomentario-deletar").setAttribute('onclick', 'deletarupload(\''+response.src+'\')');
					document.getElementById("novocomentario-deletar").classList.add("d-flex");
					document.getElementById("novocomentario-deletar").classList.remove("d-none");
					document.getElementById("novocomentario-uploads").classList.add("d-none");
					document.getElementById("novocomentario-uploads").classList.remove("d-flex");
					document.getElementById("novocomentario-arquivo").classList.remove("d-none");
				}else{
					document.getElementById("titulo").classList.add("d-none");
					document.getElementById("novocomentario-arquivo").classList.add("d-none");
					document.getElementById("novocomentario-uploads").classList.add("d-flex");
					reset($("#novocomentario-video"));
					document.getElementById("novocomentario-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("titulo").classList.add("d-none");
				document.getElementById("novocomentario-barra").classList.add("d-none"); 
				document.getElementById("novocomentario-progresso").style.width = "0%";  
				document.getElementById("novocomentario-arquivo").classList.add("d-none");
				document.getElementById("novocomentario-uploads").classList.add("d-flex");
				reset($("#novocomentario-video"));
				document.getElementById("novocomentario-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
    $('#editarcapa-foto').change(function (event) {
    	document.getElementById("editarcapa-uploads").classList.add("d-none");
		document.getElementById("editarcapa-uploads").classList.remove("d-flex");
    	document.getElementById("editarcapa-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('foto', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("editarcapa-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){   
				document.getElementById("editarcapa-barra").classList.add("d-none"); 
				document.getElementById("editarcapa-progresso").style.width = "0%";        
				if(response.cod == "1"){
					$.ajax({
						dataType  : 'json',
						type : 'POST',
						url  : $server+'ajax.php',
						data: "f=editarcapa&arquivo="+response.src,
						success :  function(response){            
							if(response.cod == "1"){
								location.reload();
							}else if(response.cod == "2"){
								modalAlert(response.msg);
							}
						}
					});
				}else{
					document.getElementById("editarcapa-arquivo").classList.add("d-none");
					document.getElementById("editarcapa-uploads").classList.add("d-flex");
					reset($("#editarcapa-foto"));
					document.getElementById("editarcapa-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("editarcapa-barra").classList.add("d-none"); 
				document.getElementById("editarcapa-progresso").style.width = "0%";  
				document.getElementById("editarcapa-arquivo").classList.add("d-none");
				document.getElementById("editarcapa-uploads").classList.add("d-flex");
				reset($("#editarcapa-foto"));
				document.getElementById("editarcapa-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
    $('#editarperfil-foto').change(function (event) {
    	document.getElementById("editarperfil-uploads").classList.add("d-none");
		document.getElementById("editarperfil-uploads").classList.remove("d-flex");
    	document.getElementById("editarperfil-barra").classList.remove("d-none");
    	var form;
        form = new FormData();
        form.append('foto', event.target.files[0]); // para apenas 1 arquivo
        //var name = event.target.files[0].content.name; // para capturar o nome do arquivo com sua extenção
        $.ajax({
        	xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// Download progress
				//xhr.addEventListener("progress", function(evt){
				// Upload progress
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = (evt.loaded / evt.total)*100;
						//Do something with upload progress
						document.getElementById("editarperfil-progresso").style.width = percentComplete+"%";
					}
				}, false);

				return xhr;
			},
			dataType  : 'json',
			type : 'POST',
			url  : $server+'upload.php',
			data: form,
            processData: false,
            contentType: false,
			success :  function(response){   
				document.getElementById("editarperfil-barra").classList.add("d-none"); 
				document.getElementById("editarperfil-progresso").style.width = "0%";        
				if(response.cod == "1"){
					$.ajax({
						dataType  : 'json',
						type : 'POST',
						url  : $server+'ajax.php',
						data: "f=editarperfil&arquivo="+response.src,
						success :  function(response){            
							if(response.cod == "1"){
								location.reload();
							}else if(response.cod == "2"){
								modalAlert(response.msg);
							}
						}
					});
				}else{
					document.getElementById("editarperfil-arquivo").classList.add("d-none");
					document.getElementById("editarperfil-uploads").classList.add("d-flex");
					reset($("#editarperfil-foto"));
					document.getElementById("editarperfil-uploads").classList.remove("d-none");
					modalAlert("Não foi possível fazer o upload agora!");
				}
			},
			error: function(){
				document.getElementById("editarperfil-barra").classList.add("d-none"); 
				document.getElementById("editarperfil-progresso").style.width = "0%";  
				document.getElementById("editarperfil-arquivo").classList.add("d-none");
				document.getElementById("editarperfil-uploads").classList.add("d-flex");
				reset($("#editarperfil-foto"));
				document.getElementById("editarperfil-uploads").classList.remove("d-none");
				modalAlert("Não foi possível fazer o upload agora!");
			}
		});
    });
});

/* Remover upload */
function deletarupload(arquivo){
	$.ajax({
		dataType  : 'json',
		type : 'POST',
		url  : $server+'ajax.php',
		data: "f=deletarupload&arquivo="+arquivo,
		success :  function(response){            
			if(response.cod == "1"){
				document.getElementById("arquivo").value="";
				if(document.getElementById("novoforum")){
					document.getElementById("novoforum-deletar").setAttribute('onclick', '');
					document.getElementById("novoforum-deletar").classList.add("d-none");
					document.getElementById("novoforum-deletar").classList.remove("d-flex");
					document.getElementById("novoforum-arquivo").classList.add("d-none");
					document.getElementById("novoforum-uploads").classList.add("d-flex");
					reset($("#novoforum-foto"));
					reset($("#novoforum-pdf"));
					reset($("#novoforum-video"));
					document.getElementById("novoforum-uploads").classList.remove("d-none");
				}else if(document.getElementById("novoevento")){
					document.getElementById("novoevento-deletar").setAttribute('onclick', '');
					document.getElementById("novoevento-deletar").classList.add("d-none");
					document.getElementById("novoevento-deletar").classList.remove("d-flex");
					document.getElementById("novoevento-arquivo").classList.add("d-none");
					document.getElementById("novoevento-uploads").classList.add("d-flex");
					reset($("#novoevento-foto"));
					/*reset($("#novoevento-pdf"));
					reset($("#novoevento-video"));*/
					document.getElementById("novoevento-uploads").classList.remove("d-none");
				}else if(document.getElementById("inicio-novopost")){
					document.getElementById("titulo").classList.add("d-none");
					document.getElementById("titulo").value = "";
					if(document.getElementById("post").value.replace(/ /g,"")==""){
						document.getElementById("novopost-btn").classList.add("d-none");
					}else{
						document.getElementById("novopost-btn").classList.remove("d-none");
					}
					document.getElementById("novopost-deletar").setAttribute('onclick', '');
					document.getElementById("novopost-deletar").classList.add("d-none");
					document.getElementById("novopost-deletar").classList.remove("d-flex");
					document.getElementById("novopost-arquivo").classList.add("d-none");
					document.getElementById("novopost-uploads").classList.add("d-flex");
					reset($("#novopost-foto"));
					reset($("#novopost-pdf"));
					reset($("#novopost-video"));
					document.getElementById("novopost-uploads").classList.remove("d-none");
				}else if(document.getElementById("forum")){
					document.getElementById("titulo").classList.add("d-none");
					document.getElementById("titulo").value = "";
					if(document.getElementById("mensagem").value.replace(/ /g,"")==""){
						document.getElementById("novocomentario-btn").classList.add("d-none");
					}else{
						document.getElementById("novocomentario-btn").classList.remove("d-none");
					}
					document.getElementById("novocomentario-deletar").setAttribute('onclick', '');
					document.getElementById("novocomentario-deletar").classList.add("d-none");
					document.getElementById("novocomentario-deletar").classList.remove("d-flex");
					document.getElementById("novocomentario-arquivo").classList.add("d-none");
					document.getElementById("novocomentario-uploads").classList.add("d-flex");
					reset($("#novocomentario-foto"));
					reset($("#novocomentario-pdf"));
					reset($("#novocomentario-video"));
					document.getElementById("novocomentario-uploads").classList.remove("d-none");
				}
				if(document.getElementById("novaoportunidade")){
					document.getElementById("novaoportunidade-deletar").setAttribute('onclick', '');
					document.getElementById("novaoportunidade-deletar").classList.add("d-none");
					document.getElementById("novaoportunidade-deletar").classList.remove("d-flex");
					document.getElementById("novaoportunidade-arquivo").classList.add("d-none");
					document.getElementById("novaoportunidade-uploads").classList.add("d-flex");
					reset($("#novaoportunidade-foto"));
					reset($("#novaoportunidade-pdf"));
					reset($("#novaoportunidade-video"));
					document.getElementById("novaoportunidade-uploads").classList.remove("d-none");
				}
			}else{
				modalAlert("Não foi possível apagar o arquivo agora!");
			}
		},
		error: function(){
			modalAlert("Não foi possível apagar o arquivo agora!");
		}
	});
	return false;
}

/* Reset input file */
window.reset = function(e) {
  e.wrap('<form>').closest('form').get(0).reset();
  e.unwrap();
}