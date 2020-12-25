//cargando imagen
var fileLoadingImage = "assets/snippets/maxigallery/lightboxv2/images/loading.gif";		
//botón cerrar
var fileBottomNavCloseImage = "assets/snippets/maxigallery/lightboxv2/images/closelabel_es.gif";
//botón siguiente
var nextLinkImage = "assets/snippets/maxigallery/lightboxv2/images/nextlabel_es.gif";
//botón anterior
var previousLinkImage = "assets/snippets/maxigallery/lightboxv2/images/prevlabel_es.gif";
//controla la velocidad al redimensionar una imagen (1=máxima lentitud y 10=máxima rapided)
var resizeSpeed = 7;
//si ajusta el padding en el CSS, necesitará actualizar esta variable
var borderSize = 10;
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
