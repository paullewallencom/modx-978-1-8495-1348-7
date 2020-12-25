//cargando imagen
var fileLoadingImage = "assets/snippets/maxigallery/slimbox/images/loading.gif";		
//botón cerrar
var fileBottomNavCloseImage = "assets/snippets/maxigallery/slimbox/images/closelabel_es.gif";
//botón siguiente
var nextLinkImage = "assets/snippets/maxigallery/slimbox/images/nextlabel_es.gif";
//botón anterior
var previousLinkImage = "assets/snippets/maxigallery/slimbox/images/prevlabel_es.gif";
//controla la velocidad, en milisegundos, al redimensionar una imagen 
var resizeDuration = 500;
//Efecto transicción al redimensionar
var resizeTransition = Fx.Transitions.sineInOut;
//la parte "Imagen" del texto "Imagen 1 de 6"
var imageNrDesc = "Imagen";
//el separador "de" del texto "Imagen 1 de 6", puede cambiarlo por / barra inclinada u otro
var imageNrSep = "de";
//teclas de aceleración para acceder a la siguiente imagen, separadas por comas
var nextKeys = new Array("s"," ");
//teclas de aceleración para acceder a la imagen anterior, separadas por comas
var prevKeys = new Array("a");
//teclas de aceleración para cerrar la lightbox, separadas por comas
var closeKeys = new Array("c","x","q");
