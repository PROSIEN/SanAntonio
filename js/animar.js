function animar(){
  var contenedor= document.getElementById("page-wrapper");
  $("#page-wrapper").addClass("animar");
  contenedor.addEventListener("transitionend",function(){
    $("#page-wrapper").removeClass("animar");
  },false);
}
