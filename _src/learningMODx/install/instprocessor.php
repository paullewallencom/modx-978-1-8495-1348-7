<?php

error_reporting(E_ALL ^ E_NOTICE);

$create = false;
$errors = 0;

// set timout limit
@set_time_limit(120); // used @ to prevent warning when using safe mode?

echo "Setup will now attempt to setup the database:<br />";


include "../manager/includes/config.inc.php";

// get base path and url
$a = explode("install",str_replace("\\","/",dirname($_SERVER["PHP_SELF"])));
if(count($a)>1) array_pop($a);
$url = implode("install",$a); reset($a);
$a = explode("install",str_replace("\\","/",dirname(__FILE__)));
if(count($a)>1) array_pop($a);
$pth = implode("install",$a); unset($a);
$base_url = $url.(substr($url,-1)!="/"? "/":"");
$base_path = $pth.(substr($pth,-1)!="/"? "/":"");


// connect to the database
echo "<p>Creating connection to the database: ";
if(!@$conn = mysql_connect($database_server, $database_user, $database_password)) {
	echo "<span class='notok'>Database connection failed!</span></p><p>Please check the database login details and try again.</p>";
	return;
} 
else {
	echo "<span class='ok'>OK!</span></p>";
}

// select database
echo "<p>Selecting database `".str_replace("`","",$dbase)."`: ";
if(!@mysql_select_db(str_replace("`","",$dbase), $conn)) {
	echo "<span class='notok' style='color:#707070'>Database selection failed...</span> The database does not exist. Setup will attempt to create it.</p>";
	return;
} else {
	echo "<span class='ok'>OK!</span></p>";
}



// open db connection
include "sqlParser.class.php";
$sqlParser = new SqlParser($database_server, $database_user, $database_password, str_replace("`","",$dbase), $table_prefix, $adminname, $adminpass);
$sqlParser->Connect();


// Install Templates
if(isset($_POST['template'])) {				
	echo "<p style='color:#707070'>Templates:</p> ";
	$selTemplates = $_POST['template'];
	foreach($selTemplates as $si) {
		$si = 		(int)trim($si);
		$name		= mysql_escape_string($moduleTemplates[$si][0]);
		$desc 		= mysql_escape_string($moduleTemplates[$si][1]);
		$type		= $moduleTemplates[$si][2]; // 0:file, 1:content
		$filecontent= $moduleTemplates[$si][3];
		if ($type==0 && !file_exists($filecontent)) echo "<p>&nbsp;&nbsp;$name: <span class='notok'>Unable to install template. File '$filecontent' not found.</span></p>";
		else {
			$template = ($type==1)? $filecontent:implode ('', file($filecontent));
			$template = mysql_escape_string($template);			
			$rs = mysql_query("SELECT * FROM $dbase.`".$table_prefix."site_templates` WHERE templatename='$name'",$sqlParser->conn);
			if (mysql_num_rows($rs)) {
				if(!@mysql_query("UPDATE $dbase.`".$table_prefix."site_templates` SET content='$template' WHERE templatename='$name';",$sqlParser->conn)) {
					$errors += 1;
					echo "<p>".mysql_error()."</p>";
					return;
				}
				echo "<p>&nbsp;&nbsp;$name: <span class='ok'>Upgraded</span></p>";
			}
			else{
				if(!@mysql_query("INSERT INTO $dbase.`".$table_prefix."site_templates` (templatename,description,content) VALUES('$name','$desc','$template');",$sqlParser->conn)) {
					$errors += 1;
					echo "<p>".mysql_error()."</p>";
					return;
				}
				echo "<p>&nbsp;&nbsp;$name: <span class='ok'>Installed</span></p>";
			}
		}
	}
}

// Install Chunks
if(isset($_POST['chunk'])) {				
	echo "<p style='color:#707070'>Chunks:</p> ";
	$selChunks = $_POST['chunk'];
	foreach($selChunks as $si) {
		$si = (int)trim($si);
		$name		= mysql_escape_string($moduleChunks[$si][0]);
		$desc 		= mysql_escape_string($moduleChunks[$si][1]);
		$type		= $moduleChunks[$si][2]; // 0:file, 1:content
		$filecontent= $moduleChunks[$si][3];
		if ($type==0 && !file_exists($filecontent)) echo "<p>&nbsp;&nbsp;$name: <span class='notok'>Unable to install chunk. File '$filecontent' not found.</span></p>";
		else {
			$chunk = ($type==1)? $filecontent:implode ('', file($filecontent));
			$chunk = mysql_escape_string($chunk);			
			$rs = mysql_query("SELECT * FROM $dbase.`".$table_prefix."site_htmlsnippets` WHERE name='$name'",$sqlParser->conn);
			if (mysql_num_rows($rs)) {
				if(!@mysql_query("UPDATE $dbase.`".$table_prefix."site_htmlsnippets` SET snippet='$chunk' WHERE name='$name';",$sqlParser->conn)) {
					$errors += 1;
					echo "<p>".mysql_error()."</p>";
					return;
				}
				echo "<p>&nbsp;&nbsp;$name: <span class='ok'>Upgraded</span></p>";
			}
			else{
				if(!@mysql_query("INSERT INTO $dbase.`".$table_prefix."site_htmlsnippets` (name,description,snippet) VALUES('$name','$desc','$chunk');",$sqlParser->conn)) {
					$errors += 1;
					echo "<p>".mysql_error()."</p>";
					return;
				}
				echo "<p>&nbsp;&nbsp;$name: <span class='ok'>Installed</span></p>";
			}
		}
	}
}

// Install module
if(isset($_POST['module'])) {				
	echo "<p style='color:#707070'>Module:</p> ";
	$selPlugs = $_POST['module'];
	foreach($selPlugs as $si) {
		$si 		= (int)trim($si);
		$name		= mysql_escape_string($moduleModules[$si][0]);
		$desc 		= mysql_escape_string($moduleModules[$si][1]);
		$type		= $moduleModules[$si][2]; // 0:file, 1:content
		$filecontent= $moduleModules[$si][3];
		$properties	= mysql_escape_string($moduleModules[$si][4]);
		$guid		= mysql_escape_string($moduleModules[$si][5]);
		$shared		= mysql_escape_string($moduleModules[$si][6]);
		if ($type==0 && !file_exists($filecontent)) echo "<p>&nbsp;&nbsp;$name: <span class='notok'>Unable to install module. File '$filecontent' not found.</span></p>";
		else{
			$module = ($type==1)? $filecontent:implode ('', file($filecontent));
			$module = mysql_escape_string($module);			
			$rs = mysql_query("SELECT * FROM $dbase.`".$table_prefix."site_modules` WHERE name='$name'",$sqlParser->conn);
			if (mysql_num_rows($rs)) {
				if(!@mysql_query("UPDATE $dbase.`".$table_prefix."site_modules` SET modulecode='$module' WHERE name='$name';",$sqlParser->conn)) {
					echo "<p>".mysql_error()."</p>";
					return;
				}
				echo "<p>&nbsp;&nbsp;$name: <span class='ok'>Upgraded</span></p>";
			}
			else{					
				if(!@mysql_query("INSERT INTO $dbase.`".$table_prefix."site_modules` (name,description,modulecode,properties,guid,enable_sharedparams) VALUES('$name','$desc','$module','$properties','$guid','$shared');",$sqlParser->conn)) {
					echo "<p>".mysql_error()."</p>";
					return;
				}
				echo "<p>&nbsp;&nbsp;$name: <span class='ok'>Installed</span></p>";
			}
		}
	}
}


// Install plugins
if(isset($_POST['plugin'])) {				
	echo "<p style='color:#707070'>Plugin:</p> ";
	$selPlugs = $_POST['plugin'];
	foreach($selPlugs as $si) {
		$si 		= (int)trim($si);
		$name		= mysql_escape_string($modulePlugins[$si][0]);
		$desc 		= mysql_escape_string($modulePlugins[$si][1]);
		$type		= $modulePlugins[$si][2]; // 0:file, 1:content
		$filecontent= $modulePlugins[$si][3];
		$properties	= mysql_escape_string($modulePlugins[$si][4]);
		$events		= explode(",",$modulePlugins[$si][5]);
		$guid		= mysql_escape_string($modulePlugins[$si][6]);
		if ($type==0 && !file_exists($filecontent)) echo "<p>&nbsp;&nbsp;$name: <span class='notok'>Unable to install plugin. File '$filecontent' not found.</span></p>";
		else{
			$plugin = ($type==1)? $filecontent:implode ('', file($filecontent));
			$plugin = mysql_escape_string($plugin);			
			$rs = mysql_query("SELECT * FROM $dbase.`".$table_prefix."site_plugins` WHERE name='$name'",$sqlParser->conn);
			if (mysql_num_rows($rs)) {
				if(!@mysql_query("UPDATE $dbase.`".$table_prefix."site_plugins` SET plugincode='$plugin' WHERE name='$name';",$sqlParser->conn)) {
					echo "<p>".mysql_error()."</p>";
					return;
				}
				echo "<p>&nbsp;&nbsp;$name: <span class='ok'>Upgraded</span></p>";
			}
			else{					
				if(!@mysql_query("INSERT INTO $dbase.`".$table_prefix."site_plugins` (name,description,plugincode,properties,moduleguid) VALUES('$name','$desc','$plugin','$properties','$guid');",$sqlParser->conn)) {
					echo "<p>".mysql_error()."</p>";
					return;
				}
				echo "<p>&nbsp;&nbsp;$name: <span class='ok'>Installed</span></p>";
			}
			//add system events
			if(count($events)>0) {
				$ds=mysql_query("SELECT id FROM $dbase.`".$table_prefix."site_plugins` WHERE name='$name';",$sqlParser->conn); 
				if($ds) {
					$row = mysql_fetch_assoc($ds);
					$id = $row["id"];
					mysql_query("INSERT INTO $dbase.`".$table_prefix."site_plugin_events` (pluginid, evtid) SELECT '$id' as 'pluginid',se.id as 'evtid' FROM $dbase.`".$table_prefix."system_eventnames` se WHERE name IN ('".implode("','",$events)."')");
				}
			}
		}
	}
}

// Install Snippet
if(isset($_POST['snippet'])) {				
	echo "<p style='color:#707070'>Snippets:</p> ";
	$selSnips = $_POST['snippet'];
	foreach($selSnips as $si) {
		$si = (int)trim($si);
		$name		= mysql_escape_string($moduleSnippets[$si][0]);
		$desc 		= mysql_escape_string($moduleSnippets[$si][1]);
		$type		= $moduleSnippets[$si][2]; // 0:file, 1:content
		$filecontent= $moduleSnippets[$si][3];
		$properties	= mysql_escape_string($moduleSnippets[$si][4]);
		if ($type==0 && !file_exists($filecontent)) echo "<p>&nbsp;&nbsp;$name: <span class='notok'>Unable to install snippet. File '$filecontent' not found.</span></p>";
		else{
			$snippet = ($type==1)? $filecontent:implode ('', file($filecontent));
			$snippet = mysql_escape_string($snippet);			
			$rs = mysql_query("SELECT * FROM $dbase.`".$table_prefix."site_snippets` WHERE name='$name'",$sqlParser->conn);
			if (mysql_num_rows($rs)) {
				if(!@mysql_query("UPDATE $dbase.`".$table_prefix."site_snippets` SET snippet='$snippet' WHERE name='$name';",$sqlParser->conn)) {
					echo "<p>".mysql_error()."</p>";
					return;
				}
				echo "<p>&nbsp;&nbsp;$name: <span class='ok'>Upgraded</span></p>";
			}
			else{					
				if(!@mysql_query("INSERT INTO $dbase.`".$table_prefix."site_snippets` (name,description,snippet,properties) VALUES('$name','$desc','$snippet','$properties');",$sqlParser->conn)) {
					echo "<p>".mysql_error()."</p>";
					return;
				}
				echo "<p>&nbsp;&nbsp;$name: <span class='ok'>Installed</span></p>";
			}
		}
	}
}


// call back function
if ($callBackFnc!="") $callBackFnc($sqlParser);

// always empty cache after install
include_once "../manager/processors/cache_sync.class.processor.php";
$sync = new synccache();
$sync->setCachepath("../assets/cache/");
$sync->setReport(false);
$sync->emptyCache(); // first empty the cache

// setup completed!
echo "<p>Installation was successful!</p>";
echo "<p>To log into the Content Manager (manager/index.php) you can click on the 'Close' button.</p>";

// close db connection
$sqlParser->close();

	
?>