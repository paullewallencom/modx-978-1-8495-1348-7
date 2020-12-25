//mensaje en la esquina superior izquierda de la lightbox
var objuserMessage = '&copy; '+getYear();
//cargando imagen, loading.gif y loading2.gif incluidas en esta galería, también puede utilizar sus propias imágenes
var loadingImage = 'assets/snippets/maxigallery/slidebox/loading2.gif';
//botón cerrar
var closeButton = 'assets/snippets/maxigallery/slidebox/close.gif';
//botón siguiente
var next_link_image = '';
//botón anterior
var previous_link_image = '';
//texto: Anterior, puede resaltar en negrita la tecla de aceleración
var backText = '<u>A</u>nterior';
//texto: Siguiente, puede resaltar en negrita la tecla de aceleración
var nextText = '<u>S</u>iguiente';
//título del enlace (link) de slidebox 
var imageTitle = 'Imagen siguiente';
//teclas de aceleración para acceder a la siguiente imagen, separadas por comas
var nextKeys = new Array("s"," ");
//teclas de aceleración para acceder a la imagen anterior, separadas por comas
var prevKeys = new Array("a");
//teclas de aceleración para cerrar la lightbox, separadas por comas
var closeKeys = new Array("c","x","q");

//puede eliminar esto si no lo uiliza en objuserMessage
function getYear(){
	Stamp = new Date();
	year = Stamp.getYear();
	if (year < 2000) year = 1900 + year;
	return year;
}
