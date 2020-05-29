/* Configuração com servidor */
var $server;
$server = 'https://weewbr.com/';

function onBackKeyDown() {return false;}
document.addEventListener("deviceready", onDeviceReady, false);
function onDeviceReady() {
    document.addEventListener("backbutton", onBackKeyDown, false);
    $.ajax({
        dataType  : 'json',
        type : 'POST',
        url  : $server+'ajax.php',
        data: "f=session&celularid="+device.uuid,
        success :  function(response){        
            if(response.codigo == "1"){
                window.location.href = "inicio.html";
            }else{
                navigator.splashscreen.hide();
            }
        }
    });
    $('#formlogin').submit(function(){
          document.getElementById("btnlogin").style.display = "none";
          document.getElementById("aguarde").style.display = "block";
          var data = $("#formlogin").serialize();
          var celularid = device.uuid;
          $.ajax({
              type : 'POST',
              url  : $server+'ajax.php',
              data : "f=login&"+data+"&version=100&celularid="+celularid,
              crossDomain: true,
              cache: false,
              dataType: 'json',
              success :  function(r){
                  document.getElementById("aguarde").style.display = "none";
                  document.getElementById("btnlogin").style.display = "block";        
                  if(r.cod==1){
                      window.location.href = "inicio.html";
                  }else if(r.cod==2) {
                      modalAlert(r.msg);
                  }else if(r.cod==0) {
                      document.getElementById(r.ide).classList.add("is-invalid");
                      setTimeout(function() {document.getElementById(r.ide).classList.remove("is-invalid");}, 3000);
                  }       
              },
              error: function (xhr, ajaxOptions, thrownError) {
                  //alert(xhr.status);
                  //alert(thrownError);
                  document.getElementById("aguarde").style.display = "none";
                  document.getElementById("btnlogin").style.display = "block";
              }
          });
          return false;
      });
}