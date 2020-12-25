/**
 *	Name:	SMFConnect Plugin for MODx
 *	Author:	Raymond Irving, July 2005
 *	Desc:	Connector for the SMF module
 *
 *	Parameters:
 *			Imported from SMF Module
 *
 *	Version:1.0 - Requires SMF 1.1RC2 or higher
 *
 */

$e = &$modx->event;

// build mod path
$modpath = $modx->config["base_path"]."assets/modules/smforum/";

// include main class
include_once $modpath."smf.base.class.inc.php";
$smf = new SMFBase();

// include SMF API
global $smf_settings, $smf_user_info, $smf_connection;
extract($smf->smfSettings);
include_once $smf->apiFile;

// enable binary look up for MODx workaround
$binaryLookup = !empty($smf_settings['reserveCase']) ? 'BINARY':'';

// md5_hmac function
if(!function_exists('md5_hmac')) {
	function md5_hmac($data, $key) {
	   if (strlen($key) > 64)
		  $key = pack('H*', md5($key));
	   $key  = str_pad($key, 64, chr(0x00));

	   $k_ipad = $key ^ str_repeat(chr(0x36), 64);
	   $k_opad = $key ^ str_repeat(chr(0x5c), 64);

	   return md5($k_opad . pack('H*', md5($k_ipad . $data)));
	}
}

// function to set MODx DB as the default
if(!function_exists('cms_smf_reset_db')) {
	function cms_smf_reset_db() {
		global $modx;
		if(!is_resource($modx->db->conn)) $modx->db->connect();
		$dbase = str_replace('`','',$modx->dbConfig['dbase']);
		mysql_select_db($dbase, $modx->db->conn); 
	}
}


// Event switch
switch($e->name) {

	case "OnWebAuthentication": 
		// check to see if this user was imported from SMF database
		if (strpos('<SMF>',$savedpassword)!==false) {
			$password = array();
			// get hased password
			$sql = "SELECT * FROM $smf_settings[db_prefix]members 
			  		WHERE $binaryLookup memberName = '".$modx->db->escape($username)."' LIMIT 1";
			$request = smf_query($sql, __FILE__, __LINE__);
			$smf_user = mysql_fetch_assoc($request);
			$hashPassword = $smf_user['passwd']; 	
			// build password hash list
			$passwords[] = @sha1(strtolower($username) . $userpassword); // version 1.1 encryption
			if($smf_user['passwordSalt']==''){
				// other password hash formats
				$passwords[] = sha1($userpassword);
				$passwords[] = md5($userpassword);
				$passwords[] = md5($userpassword.strtolower($username));
				$passwords[] = md5_hmac($userpassword,strtolower($username));
			}
			// compare passwords
			if(in_array($hashPassword, $passwords)) {
				// update user account with MODx hash format
				$tbl = $modx->getFullTableName('web_users');				
				$fld = array('password'=>md5($userpassword));
				$modx->db->update($fld,$tbl,"username='".mysql_escape_string($username)."'");
				$e->output(TRUE);
			}
			
		}
		cms_smf_reset_db(); // set MODx DB as the default
		break;
		
	
	case "OnWebLogin":
		$len = 3600;
		if($rememberme) {			
			$len = 60*525600;	// set login duration to always
			$_SESSION['cms_smf_always'] = true;
		}
		if(smf_LoginById($username,$len)) smf_logOnline(); // log user as online
		cms_smf_reset_db(); // set MODx DB as the default
		break;


	case "OnWebLogout": 
		if($_GET['smflogout']==1 || !isset($_SESSION['cms_smf_always'])) {
			smf_LogoutById($username); // logout smf user
			unset($_SESSION['cms_smf_always']);		
		}
		cms_smf_reset_db(); // set MODx DB as the default
		break;


	case "OnWebSaveUser":
		$db = new DBAPI();
		$db->conn = &$smf_connection; // set dbapi connection
		$tbl = $smf->getFullTableName("members");
		if($mode=="new") {	
			// check for duplicate user name
			$dupname = $db->getValue("SELECT memberName FROM $tbl WHERE $binaryLookup memberName = '".$modx->db->escape($username)."'");
			if($dupname) {
				$modx->logEvent(0,2,"Unable to register '$username' due to duplicate member name. "," SMF - Account registration");
				return;
			}	
			// create new user with random password
			if(!smf_registerMember($username,$useremail,uniqid('cms'.rand()))) {
					$modx->logEvent(0,2,"Unable to register member '$username'"," SMF - Account registration");
			}			
		}
		else if($mode=="upd"){
			// check for duplicate user name
			if(!empty($oldusername)){
				$dupname = $db->getValue("SELECT memberName FROM $tbl WHERE $binaryLookup memberName!='".$modx->db->escape($oldusername)."' AND $binaryLookup memberName='".$modx->db->escape($username)."'");
				if($dupname) {
					$modx->logEvent(0,2,"Unable to update SMF account due to duplicate member name '$username' being used by '$dupname'."," SMF - Account update");
					return;
				}
			}
			// check for duplicate user email
			if(!empty($olduseremail)){
				$dupname = $db->getValue("SELECT memberName FROM $tbl WHERE $binaryLookup memberName!='".$modx->db->escape(!empty($oldusername) ? $oldusername:$username)."' AND emailAddress='".$modx->db->escape($useremail)."'");
				if($dupname) {
					$modx->logEvent(0,2,"Unable to update SMF account due to duplicate email '$useremail' being used by '$dupname'."," SMF - Account update");
					return;
				}
			}
			// edit user
			$flds = array(
				'memberName' => addslashes($username),
				'emailAddress' => "$useremail",
				'realName' => addslashes($username)
			);
			$db->update($flds,$tbl,"$binaryLookup memberName='".$modx->db->escape(!empty($oldusername) ? $oldusername:$username)."'");
		}
		cms_smf_reset_db(); // set MODx DB as the default
		break;
		

	case "OnWebChangePassword":
		// update password
		if(!smf_ChangePassword($username, $userpassword)) {
			$modx->logEvent(0,2,"Unable to update password for SMF user '$username'"," SMF - Change password");
		}
		cms_smf_reset_db(); // set MODx DB as the default
		break;


	case "OnWebDeleteUser":
		if(strtolower($ondel)=='deactivate smf account') $delMode = 1;
		else if(strtolower($ondel)=='leave smf account') $delMode = 2;
		else if(strtolower($ondel)=='delete smf account')$delMode = 3;
		if($delMode!=2) {
			// deactivate forum user when webuser is deleted
			$db = new DBAPI();
			$db->conn = &$smf_connection; // set dbapi connection
			// get id number from name
			$tbl = $smf->getFullTableName("members");
			if($delMode==1) {
				$flds = array('is_activated' => 0);
				$db->update($flds,$tbl,"$binaryLookup memberName='".$modx->db->escape($username)."'");
				$modx->logEvent(0,1,"SMF account '$username' has been deactivated","SMF - Account deactivated");
			}
			else if($delMode==3) {
				// to-do we also need to delete a few other things here. 
				// can someone add this?
				$db->delete($tbl,"$binaryLookup memberName='".$modx->db->escape($username)."'");
				$modx->logEvent(0,1,"SMF account '$username' has been deleted","SMF - Account delete");
			}
		}
		cms_smf_reset_db(); // set MODx DB as the default
		break;
		

	default:
		cms_smf_reset_db();
		return;
		break;
}
