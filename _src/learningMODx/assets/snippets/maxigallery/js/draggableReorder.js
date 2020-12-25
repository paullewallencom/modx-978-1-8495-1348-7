// Adds the ability to re-order a maxigallery by dragging the
// images into the correct order. 

// This is provided under the GNU GPL.
// Copyright: Al B, 3-1-2006.
// Author's home page: http://www.e-mediacreation.co.uk
// version 0.2a
//
// This script can use either scriptaculous or mootools. IE has
// problems with this though, so scriptaculous is recommended.
//
// Note: To setup which js library you want to use, see 
// draggabletpl.html file.
//
// Form id
var formID = 'editform';	
// Picture container id
var tnContainerClass = 'managepicturecontainer';	
// Class assigned to a div containing the thumbnail images.
var tnClass = 'managepicture';
///////////// End of variables. Code follows /////////////

var dragWin;

// Open a new window containing draggable thumbnails.
function openDragWin(url, winWidth, winHeight)
{
	// open the new window
	var left = (screen.width-winWidth)/2;
    var top = (screen.height-winHeight)/2;
	dragWin = window.open(url, "dragWin", "left=" + left + ",top=" + top + ",height=" + winHeight + ",width="+ winWidth +",scrollbars=yes,menubar=no,toolbar=no,location=no,directories=no,status=yes,resizable=yes");
}

// Walk the DOM tree, creating image nodes. This is called from
// the draggabletpl.html file after it loads.
function parseImages(activeDoc)
{
	var docFrag = activeDoc.createDocumentFragment();
	var imageContainer = activeDoc.createElement('div');
	imageContainer.setAttribute('id', 'dragSet');
	docFrag.appendChild(imageContainer);

	var imageDivs = this.document.getElementsByClassName(tnClass, formID);
	var image;
	var newElement;
	
	// get the image tags of each container div of the specified class	
	for (var i=0; i < imageDivs.length; i++) 
	{
		image = imageDivs[i].getElementsByTagName('img');
		newElement = activeDoc.createElement('img');
		newElement.setAttribute('src', image[0].getAttribute('src'));
		newElement.setAttribute('class', 'draggableTN');
		newElement.setAttribute('id', 'image_' + i);
		docFrag.firstChild.appendChild(newElement);
   }
	return docFrag;
}

// Save the updated list
function handleSubmit(imgList)
{
	// create an array from the serialised string
	var imgArr = imgList.split('&');
	var imgIndex;

	// write the position into the image's position field, and update the 'modified' hidden field.
	for (var i=0; i<imgArr.length; i++)
	{
		imgIndex = imgArr[i].substr(imgArr[i].indexOf('_')+1);
		$('pos' + imgIndex).value = i;
		$('modified' + imgIndex).value = "yes";
	}
	
	// submit the form to apply the changes
	$(formID).submit();
	dragWin.close();
}
