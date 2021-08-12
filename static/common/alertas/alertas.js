function ajax() {
  var xmlhttp;
  if (window.XMLHttpRequest){
      xmlhttp=new XMLHttpRequest();
  } else{
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  return xmlhttp;
}

var objAJAX = ajax();

function confirma_formulario(tipo, path) {
  //WINDOWS
  //var enlace = "/dh_tordo/static/common/alertas/confirma_formulario.html";        
  //LINUX
  var enlace = "/static/common/alertas/confirma_formulario.html";        
  
  var div_modal = document.getElementById('div_modal');
  switch(tipo) {
    case 1:
      var msg = "¿Desea guardar el nuevo registro?";
      break;
    case 2:
      var msg = "¿Desea confirmar la edición?";
      break;
    case 3:
      var msg = "¿Desea editar el registro?";
      break;
    case 4:
      var msg = "¿Desea eliminar el registro?";
      break;
  }

  objAJAX.onreadystatechange=function(){
    if (objAJAX.readyState==4 && objAJAX.status==200){
      var text = objAJAX.responseText;
      text = text.replace('@msg@', msg);
      div_modal.innerHTML = text;
    }
  }
  
  objAJAX.open("GET", enlace, false);
  objAJAX.send(); 
  $('#sistema_alertas').modal('show');
  var btnConfirmar = document.getElementById('btnConfirmar');

  switch(tipo) {
    case 1:
      btnConfirmar.onclick = function() { confirmar_formulario(); };
      break;
    case 2:
      btnConfirmar.onclick = function() { confirmar_formulario(); };
      break;
    case 3:
      btnConfirmar.onclick = function() { location.href = path; };
      break;
    case 4:
      btnConfirmar.onclick = function() { location.href = path; };
      break;
  }
}

function confirmar_formulario() {
  document.getElementById('formulario_guardar').submit();
}

function confirmar_enlace(path) {
  location.href = path;
}