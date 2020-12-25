<?php
/**
 *	Name:	SMF Base Class file
 *	Author:	Raymond Irving, July 2005
 *	
 */


/**
 * SMF Base Class 
 *
 */
class SMFBase {
	
	var $modulePath;
	var $isPostBack;
	var $boardurl;
	var $boardpath;
	
	var $smfSettings,
		$smfSettingsFile;
	
	// constructor
	function SMFBase() {
		global $modx;
		$e = &$modx->event;
		$this->isPostBack = count($_POST);
		$this->modulePath = str_replace("\\","/",dirname(__FILE__))."/";
		$this->moduleURL = $modx->config["base_url"]."assets/modules/smforum";
		$this->apiVersion = '1.1';
		$this->apiFile = $this->modulePath."smf_api_1.1.php";
		$this->smfSettingsFile  = $e->params['smfpth'].'/Settings.php';
		if(is_file($this->smfSettingsFile)) {
			// open settings file
			include $this->smfSettingsFile;
			$this->boardurl = $boardurl;
			$this->boardpath = $e->params['smfpth'];
			$this->smfSettings["maintenance"]	    = $maintenance;
			$this->smfSettings["mtitle"]		    = $mtitle;
			$this->smfSettings["mmessage"]		    = $mmessage;
			$this->smfSettings["mbname"]		    = $mbname;
			$this->smfSettings["language"]		    = $language;
			$this->smfSettings["boardurl"]		    = $boardurl;
			$this->smfSettings["webmaster_email"]   = $webmaster_email;
			$this->smfSettings["cookiename"]	    = $cookiename;
			$this->smfSettings["db_server"]		    = $db_server;
			$this->smfSettings["db_name"]		    = $db_name;
			$this->smfSettings["db_user"]		    = $db_user;
			$this->smfSettings["db_passwd"]		    = $db_passwd;
			$this->smfSettings["db_prefix"]		    = $db_prefix;
			$this->smfSettings["db_persist"]	    = $db_persist;
			$this->smfSettings["db_error_send"]	    = $db_error_send;
			$this->smfSettings["db_last_error"]	    = $db_last_error;
			$this->smfSettings["boarddir"]		    = $boarddir;
			$this->smfSettings["sourcedir"]		    = $sourcedir;			
		}
		else {
			$modx->webAlert("Unable to load SMF settings. Please make sure that the SMF Path '".$e->params['smfpath']."' was entered correctly",'index.php?a=106');
			exit;
		}		
	}
	
	// get smf table name
	function getFullTableName($tbl) {
		return $this->smfSettings['db_name'].".".$this->smfSettings['db_prefix'].$tbl;
	}

	// get external file content
	function getFileContent($fl){
		global $modx;
		if(!is_file($fl)) return '';
		if(function_exists("file_get_contents")) $content = file_get_contents($fl);
		else {
			$fd = fopen($fl, "r");
			$content = fread($fd, filesize($fl));
			fclose($fd);
		}
		// set placeholders
		if(strpos($fl,".htm")){
			$content = str_replace("[+id+]",((int)$_GET['id']),$content); // behaviour  library
			$content = str_replace("[+moduleurl+]",$modx->config['site_url']."assets/modules/smforum/",$content); // behaviour  library
			$content = str_replace("[+baseurl+]",$modx->config['base_url'],$content); // behaviour  library
		}
		return $content;			
	}

	function getTemplate(){
		global $modx;
		$tpl = $this->getFileContent($this->modulePath."template.inc.html");
		return $tpl;		
	}	
}

?>