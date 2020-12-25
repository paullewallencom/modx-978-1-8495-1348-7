<?php
#:: SMF Connector Setup file 
#:::::::::::::::::::::::::::::::::::::::::

	$moduleName = "SMF Connector";
	$moduleVersion = "1.0 Beta";
	$moduleSQLBaseFile = "setup.sql";
	$moduleWhatsNewFile = "";
	$moduleWhatsNewTitle = "";

	# setup plugins - array : name, description, type - 0:file or 1:content, file or content,properties, guid
	$mp = &$modulePlugins;
	$mp[] = array("SMFConnect","Connector for the SMF module",0,"$setupPath/plugin.smf-connect.tpl","","OnWebLogin,OnWebLogout,OnWebSaveUser,OnWebDeleteUser,OnWebChangePassword ,OnWebAuthentication","ef9f806e4721e5a33701a5b4b3e0f1aa");

	# setup modules - array : name, description, type - 0:file or 1:content, file or content,properties, guid,enable_sharedparams
	$mm = &$moduleModules;
	$mm[] = array("SMF Connector","Connects MODx web users with SMF forums",0,"$setupPath/module.smf.tpl","&cmsurl=CMS base url;string;/;&smfpth=Forum base path;string;&admid=Admin User;string;&admpwd=Admin password;string;&defgrp=Default group;string;&len=Default login time;string;120&loginpg=Login page;string;&logoutpg=Logout page;string;&regpg=Registration page;string;&hideforms=Hide forms;list;Yes,No;Yes;&ondel=When deleting;list;Deactivate SMF account,Leave SMF account,Delete SMF account;Leave SMF account;","ef9f806e4721e5a33701a5b4b3e0f1aa",1);


	# setup callback function
	$callBackFnc = "clean_up";
	
	function clean_up($sqlParser) {
		$ids = array();

		/**** Add SMFConnector Plugin to Module ***/
		// get module id
		$ds = mysql_query("SELECT id FROM ".$sqlParser->prefix."site_modules WHERE name='SMF Connector'");
		if(!$ds) {
			echo "An error occured while executing a query: ".mysql_error();
		}
		else {
			$row = mysql_fetch_assoc($ds);
			$moduleid=$row["id"];
		}
		// get plugin id
		$ds = mysql_query("SELECT id FROM ".$sqlParser->prefix."site_plugins WHERE name='SMFConnect'");
		if(!$ds) {
			echo "An error occured while executing a query: ".mysql_error();
		}
		else {
			$row = mysql_fetch_assoc($ds);
			$pluginid=$row["id"];
		}		
		// setup plugin as module dependency
		$ds = mysql_query("SELECT module FROM ".$sqlParser->prefix."site_module_depobj WHERE module='$moduleid' AND resource='$pluginid' AND type='30' LIMIT 1"); 
		if(!$ds) {
			echo "An error occured while executing a query: ".mysql_error();
		}
		elseif (mysql_num_rows($ds)==0){
			mysql_query("INSERT INTO ".$sqlParser->prefix."site_module_depobj (module, resource, type) VALUES('$moduleid','$pluginid',30)");
		}
		
	}
?>