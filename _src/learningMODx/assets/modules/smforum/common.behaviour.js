/**
 *	Name:	Common JavaScript Behaviours for SMF module
 *	Author:	Raymond Irving, 24-Jan-2006
 *
 */

var cssRules = {
	'.menutitle' : function(elm) {
		elm.onclick = function(){
			postForm(getAttrib(this,'eventname'));
			return false;	
		}
	},
	
	'#sync' : function(elm) {
		elm.onclick = function(){
			if(confirm('Are you sure you want to synchronize MODx web user accounts with SMF?')) 
				postForm(getAttrib(this,'eventname'));
			return false;	
		}
	},
	
	'#logout' : function(elm) {
		elm.onclick = function(){
			postForm(getAttrib(this,'eventname'));
			return false;	
		}
	},
	
	'#mainbutton' : function(elm) {
		elm.onclick = function(){
			postForm('onload');
			return false;	
		}
	}
	
}

Behaviour.register(cssRules);
 
function postForm(evt){
	document.module.evt.value = evt;
	document.module.submit();
}

function getAttrib(elm,name){
	var v;
	if (elm && elm.getAttribute) v = elm.getAttribute(name);
	else v = elm[name];
	return v
}
