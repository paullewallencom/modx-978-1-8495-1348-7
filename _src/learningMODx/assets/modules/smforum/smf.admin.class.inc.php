<?php
/**
 *	Name:	SMF Admin Module Class file
 *	Author:	Raymond Irving, July 2005
 * 	Desc:	SMF Connector admin class
 *
 */


/**
 * SMF Admin Class 
 *
 */
class SMFAdminModule extends SMFBase {
	
	// constructor
	function SMFAdminModule() {
		parent::SMFBase();
	}

	function showModuleMain() {
		global $modx;
		$e = &$modx->event;
		$tpl = $this->getTemplate();
		$content = $this->getFileContent($this->modulePath."main.inc.html");		
		echo str_replace("[+content+]",$content,$tpl);
	}

	
	function loginSMFAdminPanel() {
		global $modx;
		$e = &$modx->event;
		extract($this->smfSettings);
		include_once $this->apiFile;
		$len = isset($e->params['len']) ? $e->params['len']*60 : 3600;
		if(smf_LoginById($e->params['admid'],$len)) {		
			// load smf forum admin panel
			smf_disableSecurityPanel($e->params['admid']);
			echo '<script>window.location.href="'.$this->boardurl.'/index.php?action=admin";</script>';
		}
		else {
			$msg = "Unable to log into the SMF Administration Center using the user name '".$e->params['admid']."'.\n\nPlease check to ensure that you've entered the correct user name and password from the module configuration screen, and that you've selected the correct SMF version.";
			$modx->webAlert($msg,"javascript:history.back(1);");
		}
	}
	
	function logoutSMFAdminPanel() {
		global $modx;
		$e = &$modx->event;
		extract($this->smfSettings);
		include_once $this->apiFile;
		smf_LogoutById($e->params['admid']);
	}

		
}

// temporarily allow admin to login without security panel
function smf_disableSecurityPanel($username){

	global $smf_connection, $smf_settings;	

     $sql = "SELECT *
          FROM $smf_settings[db_prefix]members 
          WHERE memberName = '".mysql_escape_string($username)."'
          LIMIT 1";
     $request = smf_query($sql, __FILE__, __LINE__);
     $smf_user = mysql_fetch_assoc($request);
     
    //Now login
    $key = $smf_user['ID_MEMBER'].'|'.sha1($smf_user['passwd'] . $smf_user['passwordSalt'] . MD5($username));
	setcookie('cmsSMFSAPnl',$key,0,'/');
}

?>