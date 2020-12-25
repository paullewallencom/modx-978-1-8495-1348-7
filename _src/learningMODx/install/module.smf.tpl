/**
 *	Name:	SMF Module for MODx
 *	Author:	Raymond Irving
 *	Desc:	Connects MODx web users with an SMF forum version 1.1RC2 or higher 
 *
 *	Default Parameters:	
 *			&cmsurl=CMS base url;string;/ 
 *			&smfpth=Forum base path;string; 
 *			&admid=Admin User;string; 
 *			&admpwd=Admin password;string; 
 *			&defgrp=Default group;string; 
 *			&len=Default login time;string;120 
 *			&loginpg=Login page;string; 
 *			&logoutpg=Logout page;string; 
 *			&regpg=Registration page;string; 
 *			&hideforms=Hide forms;list;Yes,No;Yes 
 *			&ondel=When deleting;list;Deactivate SMF account,Leave SMF account,Delete SMF account;Leave SMF account
 *
 *	Version:1.0, March 01, 2006 - Requires SMF 1.1RC2 or higher
 *
 */



# build mod path
$modpath = $modx->config["base_path"]."assets/modules/smforum/";

# events: onadmin, onimport, onexport, onload (default)
$evt = isset($_REQUEST["evt"]) ? strtolower($_REQUEST["evt"]):"onload";

# include main class
include_once $modpath."smf.base.class.inc.php";

# Start processing
switch ($evt) {

	case "onloginadmin":	// login to admin panel 
		include_once $modpath."smf.admin.class.inc.php";
		$smf = new SMFAdminModule();
		$smf->loginSMFAdminPanel();
		break;

	case "onsync":	// synchronize users
		include_once $modpath."smf.sync.class.inc.php";
		$smf = new SMFSync();
		$smf->execute();
		break;

	case "onlogoutadmin":	// logout admin smf
		include_once $modpath."smf.admin.class.inc.php";
		$smf = new SMFAdminModule();
		$smf->logoutSMFAdminPanel();
		$smf->showModuleMain();
		break;
	
	default:		// main screen
		include_once $modpath."smf.admin.class.inc.php";
		$smf = new SMFAdminModule();
		$smf->showModuleMain();
		break;

}
