var app = {
  startCameraAbove: function(){
    CameraPreview.startCamera({x: 50, y: 50, width: 300, height: 300, toBack: false, previewDrag: true, tapPhoto: true});
  },

  startCameraBelow: function(){
    CameraPreview.stopCamera();
    CameraPreview.startCamera({x: 0, y: 0, width: window.innerWidth, height: window.innerHeight, camera: "back", tapPhoto: false, tapFocus: true, previewDrag: false, toBack: true, disableExifHeaderStripping: true});
    CameraPreview.setZoom(1);
    CameraPreview.setFlashMode("off");
  },

  stopCamera: function(){
    CameraPreview.stopCamera();
  },

  takePicture: function(){
    var ww = window.innerWidth;
    var hh = window.innerHeight;
    CameraPreview.getSupportedPictureSizes(function(dimensions){
      var dimension = dimensions[dimensions.length - 1];
      var ww = dimension.height;
      var hh = dimension.width;
      CameraPreview.takePicture(function(imgData){
        /*function rotateBase64Image(base64data) {
            var canvas = document.getElementById("c");
            canvas.width = ww;
            canvas.height = hh;
            var ctx = canvas.getContext("2d");
            var image = new Image();
            image.src = base64data;
            image.onload = function() {
                ctx.scale(1, -1);
                ctx.drawImage(image, 0, -1*hh, ww, hh);
                ctx.clearRect(0, -1*hh, ww, 0);
                var imgData = canvas.toDataURL('image/jpeg', 1);
                document.getElementById('originalPicture').src = imgData;
                if(document.getElementById('page').value=='rosto'){
                  alert(imgData.length);
                  $.ajax({
                    url  : 'http://srv252.teste.website/~jbacademy/ajax.php',
                    data: "f=rosto&id="+id+"&ROS_Foto="+imgData,
                    type: 'POST',
                    dataType  : 'json',
                    success: function(data){
                      a
                          if(data.cod==1){
                            window.location = 'rosto.html?id='+id+'&nome='+nome+'&sexo='+sexo+'&width='+ww+'&height='+hh+'&foto=ok';
                          }
                    }
                  });
                }
                if(document.getElementById('page').value=='corpo'){
                  window.location = 'corpo.html?id='+id+'&nome='+nome+'&sexo='+sexo+'&width='+ww+'&height='+hh+'&foto='+imgData;
                }
                if(document.getElementById('page').value=='cores'){
                  window.location = 'cores.html?id='+id+'&nome='+nome+'&sexo='+sexo+'&width='+ww+'&height='+hh+'&foto='+imgData;
                }
            };
        }*/
        /*if(document.getElementById("opcoesdir").value == 'front') {
          rotateBase64Image('data:image/jpeg;base64,' + imgData);
        }else{*/
          document.getElementById('originalPicture').src = 'data:image/jpeg;base64,' + imgData;
          if(document.getElementById('page').value=='rosto'){
            window.location = 'rosto.html?id='+id+'&nome='+nome+'&sexo='+sexo+'&width='+ww+'&height='+hh+'&dir='+document.getElementById("opcoesdir").value+'&foto=data:image/jpeg;base64,'+imgData;
          }
          if(document.getElementById('page').value=='corpo'){
            window.location = 'corpo.html?id='+id+'&nome='+nome+'&sexo='+sexo+'&width='+ww+'&height='+hh+'&dir='+document.getElementById("opcoesdir").value+'&foto=data:image/jpeg;base64,'+imgData;
          }
          if(document.getElementById('page').value=='cores'){
            window.location = 'cores.html?id='+id+'&nome='+nome+'&sexo='+sexo+'&width='+ww+'&height='+hh+'&dir='+document.getElementById("opcoesdir").value+'&foto=data:image/jpeg;base64,'+imgData;
          }
        /*}*/
      });
    });
  },

  backButton: function(){
    CameraPreview.onBackButton(function() {
      this.stopCamera();
    });
  },

  switchCamera: function(){
    CameraPreview.switchCamera();
  },

  show: function(){
    CameraPreview.show();
  },

  hide: function(){
    CameraPreview.hide();
  },

  changeColorEffect: function(){
    var effect = document.getElementById('selectColorEffect').value;
    CameraPreview.setColorEffect(effect);
  },

  changeFlashMode: function(){
    var mode = document.getElementById('selectFlashMode').value;
    CameraPreview.setFlashMode(mode);
    console.log("setou"+mode);
  },

  changeZoom: function(){
    var zoom = document.getElementById('zoomSlider').value;
    document.getElementById('zoomValue').innerHTML = zoom;
    CameraPreview.setZoom(zoom);
  },

  changePreviewSize: function(){
    window.smallPreview = !window.smallPreview;
    if(window.smallPreview){
      CameraPreview.setPreviewSize({width: 100, height: 100});
    }else{
      CameraPreview.setPreviewSize({width: window.screen.width, height: window.screen.height});
    }
  },

  showSupportedPictureSizes: function(){
    CameraPreview.getSupportedPictureSizes(function(dimensions){
      dimensions.forEach(function(dimension) {
        alert(dimension.width + 'x' + dimension.height);
      });
    });
  },

  init: function(){
    //document.getElementById('startCameraAboveButton').addEventListener('click', this.startCameraAbove, false);
    document.getElementById('startCameraBelowButton').addEventListener('click', this.startCameraBelow, false);
    

    document.getElementById('stopCameraButton').addEventListener('click', this.stopCamera, false);
    document.getElementById('switchCameraButton').addEventListener('click', this.switchCamera, false);
    //document.getElementById('showButton').addEventListener('click', this.show, false);
    //document.getElementById('hideButton').addEventListener('click', this.hide, false);
    document.getElementById('takePictureButton').addEventListener('click', this.takePicture, false);
    //document.getElementById('selectColorEffect').addEventListener('change', this.changeColorEffect, false);
    document.getElementById('selectFlashMode').addEventListener('change', this.changeFlashMode, false);

    /*if(navigator.userAgent.match(/Android/i)  == "Android"){
      document.getElementById('zoomSlider').addEventListener('change', this.changeZoom, false);
    }else{
      document.getElementById('androidOnly').style.display = 'none';
    }*/

    window.smallPreview = false;
    //document.getElementById('changePreviewSize').addEventListener('click', this.changePreviewSize, false);

    //document.getElementById('showSupportedPictureSizes').addEventListener('click', this.showSupportedPictureSizes, false);

    // legacy - not sure if this was supposed to fix anything
    //window.addEventListener('orientationchange', this.onStopCamera, false);
  }
};

document.addEventListener('deviceready', function(){	
  app.init();
}, false);
