<?php
/**
 *	Name:	SMF Module
 *	Author:	Raymond Irving, 22-Jan-2006
 *	Desc:	Integrates SMF with MODx
 *
 */
 
function cms_smf_start() {
	$smf_action = '';
	$a = isset($_REQUEST['action']) ? explode(';',$_REQUEST['action'],2):'';
	if(is_array($a)) $smf_action = $a[0];
	
	// load cms params on first load or during special actions
	if(!isset($_COOKIE['cms_smf_checked']) || in_array($smf_action,array('login','logout','register'))) {	
		setcookie('cms_smf_checked',1);
		// connect to MODx DB and get module settings
		global $dbase;
		global $table_prefix;
		include realpath(dirname(__FILE__)."/../../../manager/includes/config.inc.php");
		$conn = mysql_connect($database_server, $database_user, $database_password);
		mysql_select_db(str_replace('`','',$dbase),$conn);
		$rows = mysql_query('
			SELECT * 
			FROM '.cms_table('site_modules').' 
			WHERE name=\'SMF Connector\'
		',$conn);
		$row = mysql_fetch_assoc($rows);
		if(!$row) return;
		// if module is diabled then return
		if(isset($row['disabled']) && $row['disabled']==1) return true;
		$params = cms_parseProperties($row['properties']);
		if (isset($params['hideforms']) && $params['hideforms']=='Yes') {
			setcookie('cms_smf_hideForms',1);
			$_COOKIE['cms_smf_hideForms'] = 1;
		}
		else {
			setcookie('cms_smf_hideForms','');
			$_COOKIE['cms_smf_hideForms'] = 0;
		}
				
		// redirect to login/register page
		$cmsurl = isset($params['cmsurl']) ? $params['cmsurl']:'' ;
		if(substr($cmsurl,-1)!='/') $cmsurl.='/';
		switch ($smf_action) {
			// note: the header('location') function does not work 
			// with cookies and IIS5
			case 'login':
				// redirect login to MODx login page if $smf_action=login				
				global $boardurl;
				$refurl='&refurl='.urlencode($boardurl);	// setup return url
				header("refresh:0;url={$cmsurl}index.php?id=".(isset($params['loginpg']) ? $params['loginpg'] : 1).$refurl);
				exit;
				break;
			case 'logout':
				// redirect logout to MODx logout page if $smf_action=logout
				header("refresh:0;url={$cmsurl}index.php?id=".(isset($params['logoutpg']) && !empty($params['logoutpg']) ? $params['logoutpg'] : $params['loginpg']).'&webloginmode=lo&smflogout=1');
				exit;
				break;
			case 'register':
				// redirect registration to MODx registration page if $smf_action=register		
				header("refresh:0;url={$cmsurl}index.php?id=".(isset($params['regpg']) ? $params['regpg'] : 1));
				exit;
				break;
		}
		
	}	
	// start buffering
	if(isset($_COOKIE['cms_smf_hideForms'])) ob_start();
}


function cms_smf_end() {
	// hide login/registration pages
	if(isset($_COOKIE['cms_smf_hideForms']) && $_COOKIE['cms_smf_hideForms']==1) {
		$html = ob_get_contents();
		ob_end_clean();
		$html=str_replace('</body>',
			'<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA['."\n".
			'for(i=0;i<document.forms.length;i++) {'.
			'   f=document.forms[i];'.
			'   if (f && f.action.indexOf("action=login2")>0) f.style.display="none";'.
			'   else if (f && f.action.indexOf("action=register2")>0) f.style.display="none";'.		
			'}'."\n".
			'// ]]></script></body>',
			$html);
		echo $html;
	}
}

function cms_smf_verifyUser() {
	global $modSettings,$db_prefix;
	// setup so admin don't have to type password again
	$key = isset($_COOKIE['cmsSMFSAPnl']) ? $_COOKIE['cmsSMFSAPnl']: '';	
	$arr = explode('|',$key);
	$id = $arr[0];
	$key = isset($arr[1])>1 ? $arr[1]:'';
	if($id>0 && !empty($key) && (!isset($_SESSION['admin_time']) || empty($_SESSION['admin_time']))) {
	
		$sql = "SELECT *
		  FROM {$db_prefix}members 
		  WHERE ID_MEMBER = '".$id."'
		  LIMIT 1";
		$request = db_query($sql, __FILE__, __LINE__);
		if ($request) {
			$smf_user = mysql_fetch_assoc($request);
			if($key==sha1($smf_user['passwd'] . $smf_user['passwordSalt'] . MD5($smf_user['memberName']))) {
				$_SESSION['admin_time'] = time();
				unset($_SESSION['just_registered']);
			}
		}		
	}
}


// return full MODx table name
function cms_table($name){
	global $dbase,$table_prefix;
	return $dbase.'.`'.$table_prefix.$name.'`';
}

// parses a resource property string and returns the result as an array
function cms_parseProperties($propertyString){
	$parameter = array();
	if(!empty($propertyString)) {
		$tmpParams = explode("&",$propertyString);
		for($x=0; $x<count($tmpParams); $x++) {
			if (strpos($tmpParams[$x], '=', 0)) {
				$pTmp = explode("=", $tmpParams[$x]);
				$pvTmp = explode(";", trim($pTmp[1]));
				if ($pvTmp[1]=='list' && $pvTmp[3]!="") $parameter[trim($pTmp[0])] = $pvTmp[3]; //list default
				else if($pvTmp[1]!='list' && $pvTmp[2]!="") $parameter[trim($pTmp[0])] = $pvTmp[2];
			}
		}
	}
	return $parameter;
}

?>