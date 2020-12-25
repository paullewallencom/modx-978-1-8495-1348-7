//message to the top left corner of the lightbox
var objuserMessage = '&copy; '+getYear();
//loading image, loading.gif and loading2.gif bundled in this gallery, you can have your own too
var loadingImage = 'assets/snippets/maxigallery/slidebox/loading2.gif';
//close image
var closeButton = 'assets/snippets/maxigallery/slidebox/close.gif';
//next image
var next_link_image = '';
//previous image
var previous_link_image = '';
//text: Back, you can bold the accelerator key
var backText = '<u>E</u>dellinen';
//text: Next, you can bold the accelerator key
var nextText = '<u>S</u>euraava';
//slidebox link title
var imageTitle = 'Seuraava kuva';
//accelerator keys that goes to next picture, separate by commas
var nextKeys = new Array("s"," ");
//accelerator keys that goes to previous picture, separate by commas
var prevKeys = new Array("e");
//accelerator keys that close the lightbox, separate by commas
var closeKeys = new Array("c","x","q");

//you can remove this if you don't use it in objuserMessage
function getYear(){
	Stamp = new Date();
	year = Stamp.getYear();
	if (year < 2000) year = 1900 + year;
	return year;
}