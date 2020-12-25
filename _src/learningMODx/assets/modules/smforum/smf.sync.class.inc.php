<?php
/**
 *	Name:	SMF Sync Class file
 *	Author:	Raymond Irving, July 2005
 *	Desc:	Synchronizes MODx web user accounts with SMF
 */


/**
 * SMF Sync Class 
 *
 */
class SMFSync extends SMFBase {
	
	// constructor
	function SMFSync() {
		parent::SMFBase();
	}
	
	function execute() {
		global $modx;
		$e = &$modx->event;		
		$imports = 0;
		$exports = 0;
		$syncLog = array();
		$smfUsers = array();
		
		// include SMF API
		extract($this->smfSettings);
		include_once $this->apiFile;
		
		// set dbapi connection
		$db = new DBAPI();
		$db->conn = &$smf_connection;
		
		// update integration fields
		$tbl= $this->getFullTableName("settings");
		$file = str_replace('\\','/',dirname(__FILE__)).'/smf.integrate.inc.php';
		$db->query("REPLACE INTO $tbl (variable,value) VALUES('integrate_pre_include','$file');");
		$db->query("REPLACE INTO $tbl (variable,value) VALUES('integrate_pre_load','cms_smf_start');");
		$db->query("REPLACE INTO $tbl (variable,value) VALUES('integrate_exit','cms_smf_end');");
		$db->query("REPLACE INTO $tbl (variable,value) VALUES('integrate_validate_login','cms_smf_validateLogin');");
		$db->query("REPLACE INTO $tbl (variable,value) VALUES('integrate_register','cms_smf_registerUser');");
		$db->query("REPLACE INTO $tbl (variable,value) VALUES('integrate_verify_user','cms_smf_verifyUser');");
		$syncLog[] = "Redirect SMF login, logout and register functions to MODx";
		
		@set_time_limit(120);

		/**** Import SMF users into MODx ***/
		
		// get list of smf members to import
		$tbl= $this->getFullTableName("members");
		$ds = $db->select("memberName, lastLogin, realName, passwd, emailAddress, gender",$tbl);
		while($smf = $db->getRow($ds)){
			// check for existing web user id
			$id = $modx->db->getValue(
				"SELECT id FROM ".$modx->getFullTableName("web_users")." ".
				"WHERE username='".mysql_escape_string($smf["memberName"])."' LIMIT 1"
			);

			// store smf user id
			$smfUsers[] = mysql_escape_string($smf["memberName"]);
			
			// is account exist then ignore
			if(!empty($id)) {
				$syncLog[] = "MODx web user '".$smf["memberName"]."' already exists. Ignoring SMF account import...";
				continue;
			}
			
			$tbl = $modx->getFullTableName("web_users");
			$flds = array('username' => $smf["memberName"],'password'=>'<SMF>');
			if(empty($id)){
				// create new user
				$id = $modx->db->insert($flds,$tbl);
				$tbl = $modx->getFullTableName("web_user_attributes");
				$flds = array(
					internalKey	 => $id, 
					fullname 	 => $smf["realName"], 
					email 		 => $smf["emailAddress"],
					lastlogin 	 => $smf["lastLogin"], 
					gender		 => $smf["gender"] // Modificato da Iade
				);
				// create user attribute
				$modx->db->insert($flds,$tbl);
				$imports++;
				$syncLog[] = "SMF user '".$smf["memberName"]."' was successfully imported into MODx.";
			}
			
		}
		
		/**** Export MODx web users to SMF ***/
	
		// get default SMF user group
		if(empty($e->params['defgrp'])) $id_group = 4;
		else {
			$id_group = $db->getValue(
				"SELECT ID_GROUP FROM ".$this->getFullTableName("membergroups")." ".
				"WHERE groupName='".addslashes($e->params['defgrp'])."' LIMIT 1"
			);
			if(empty($id_group)) $id_group = 4;
		}
			
		// get list of modx web users to export
		$tbl= $modx->getFullTableName("web_users");
		$ds = $modx->db->select("*",$tbl,(count($smfUsers)>0 ? 'username NOT IN (\''.implode('\',\'',$smfUsers).'\')' :''));
		while($row = $modx->db->getRow($ds)){
		
			// get web user attribute
			$tbl = $modx->getFullTableName("web_user_attributes");
			$ads = $modx->db->select("*",$tbl,"internalKey='".$row["id"]."'");
			$att = $modx->db->getRow($ads);

			// create new SMF account
			$id = smf_registerMember($row["username"], $att["email"], uniqid('cms'.rand()));
			// update attribute
			$tbl = $this->getFullTableName("members");
			$flds = array(
				'dateRegistered'=> time(),
				'ID_GROUP'		=> $id_group,
				'ID_POST_GROUP' => '4', // Newbie
				'showOnline' 	=> 1,
				'lastLogin' 	=> $att["lastlogin"],
				'gender'		=> $att["gender"], // Modificato da Iade
				'is_activated'	=> 1, // when exporting we should activated account
			);
			$db->update($flds,$tbl,"ID_MEMBER='$id'");
			$exports++;
			$syncLog[] = "MODx web user '".$row["username"]."' was successfully exported to SMF.";			
		}
				
		$tpl = $this->getTemplate();
		$content = $this->getContent($imports, $exports, $syncLog);
		echo str_replace("[+content+]",$content,$tpl);
	}


	// getContent
	function getContent($imports, $exports, $log) {
		return '
		<span class="subtitle">Synchronize Users</span>
		<div style="margin:0 20px 040px;">
			<img src="'.$this->moduleURL.'/images/crarrow.gif" width="32" height="32" align="left" hspace="5" />
			<div style="magring-left:32px;">
				<b>'.$imports.'</b> SMF user account(s) imported into MODx.<br />
				<b>'.$exports.'</b> MODx web user account(s) exported to SMF.<br />
			</div>
			<p>&nbsp;&nbsp;<a id="mainbutton" href="javascript:;">Click here</a> to return to the main menu.</p>
			<b>System Log</b> <br />
			<ul><li>'.implode('</li><li>',$log).'</li></ul>			
		</div>
		';
	}
	
}

?>