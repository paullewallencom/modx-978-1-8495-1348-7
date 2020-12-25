function openWindow(url, title, height, width, name, parms) {
	var left = Math.floor( (screen.width - width) / 2);
	var top = Math.floor( (screen.height - height) / 2);
	var winParms = "top=" + top + ",left=" + left + ",height=" + height + ",width=" + width;
	if (parms) { winParms += "," + parms; }
	var win = window.open('', name, winParms);
	win.document.clear();
	win.focus();
	win.document.writeln('<html><head><title>'+title+'<\/title><\/head><body style=\"margin:0;padding:0;\">');
	win.document.writeln('<img src=\"'+url+'\" title=\"'+title+'\" alt=\"'+title+'\">');
	win.document.writeln('<\/body><\/html>');
	win.document.close();
	win.focus();
	if (parseInt(navigator.appVersion) >= 4) { win.window.focus(); }
	return win;
}