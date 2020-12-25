<?php
#:: Module Installer 1.0 (Beta 3)
#::	Written By Raymond Irving - Dec 2004
#:::::::::::::::::::::::::::::::::::::::::
#:: Installs Modules, Plugins, Snippets, Chunks, Templates and TVs

	// start session
	session_start();

	// set error reporting
	error_reporting(E_ALL ^ E_NOTICE);	

	$moduleName 		= "Module Installation";
	$moduleVersion 		= "1.0 ";
	$moduleSQLBaseFile 	= "setup.sql";
	$moduleSQLDataFile 	= "setup.data.sql";
	$moduleWhatsNewFile = "setup.whatsnew.html";
	$moduleWhatsNewTitle= "What's New";
	
	$moduleWelcomeMessage= "";
	$moduleLicenseMessage= "";

	
	$moduleChunks 	 	= array(); // chunks - array : name, description, type - 0:file or 1:content, file or content
	$moduleTemplates 	= array(); // templates - array : name, description, type - 0:file or 1:content, file or content
	$moduleSnippets 	= array(); // snippets - array : name, description, type - 0:file or 1:content, file or content,properties
	$modulePlugins		= array(); // plugins - array : name, description, type - 0:file or 1:content, file or content,properties, events,guid
	$moduleModules		= array(); // modules - array : name, description, type - 0:file or 1:content, file or content,properties, guid
	$moduleTemplates 	= array(); // templates - array : name, description, type - 0:file or 1:content, file or content,properties
	$moduleTVs		 	= array(); // template variables - array : name, description, type - 0:file or 1:content, file or content,properties

	# function to call after setup
	$callBackFnc =""; 			
	
	# load setup information file
	$setupPath = dirname(__FILE__);
	include_once "$setupPath/setup.info.php";
	
	$errors = 0;
	$syscheck = ($_POST['syscheck']=="on") ? true:false;
	$upgradeable = file_exists("../manager/includes/config.inc.php") ? 1:0;

	$installMode = !$upgradeable ? 0:-1;
	if(count($_POST)) $installMode = $_POST['installmode']=='upd' ? 1:0;
	
	// get post back status
	$isPostBack = (count($_POST) && !$syscheck);
			
	// start install process
	if($isPostBack) {
		ob_start();
		include_once "$setupPath/instprocessor.php";
		$moduleWelcomeMessage = ob_get_contents();
		ob_end_clean();
	}
	
	// build Welcome Screen
	function buildWelcomeScreen() {
		global $moduleName;
		global $moduleWelcomeMessage;
		if ($moduleWelcomeMessage) return $moduleWelcomeMessage;
		else {
			ob_start();
			?>
				<table width="100%">
				<tr>
				<td valign="top">
					<p class='title'>Welcome to the <?php echo $moduleName; ?> installation program.</p>
					<p>This program will guide you through the rest of the installtion.</p>
					<p>Please select 'Next' button to continue:</p>
				</td>
				<td align="center" width="280">
					<img src="img_box.png" />&nbsp;
				</td>
				</tr>
				</table>
				
			<?php
			$o = ob_get_contents();
			ob_end_clean();
			return $o;
		}		
	}


	
	// build Selection Screen
	function buildOptionsScreen() {
		global $moduleChunks;
		global $modulePlugins;
		global $moduleSnippets;
		global $moduleModules;
		
		ob_start();	
		echo "<p class=\"title\">Installable Items</p><p>Please choose your installation options and click Install:</p>";

		// display templates
		$templates = isset($_POST['template']) ? $_POST['template']:array();
		$limit = count($moduleTemplates);
		if ($limit>0) echo "<h1>Templates</h1>";
		for ($i=0;$i<$limit;$i++) {
			$chk = in_array($i,$templates)||(!count($_POST)) ? "checked='checked'": "";
			echo "&nbsp;<input type='checkbox' name='template[]' value='$i' $chk />Install/Update <span class='comname'>".$moduleTemplates[$i][0]."</span> - ".$moduleTemplates[$i][1]."<hr size='1' style='border:1px dotted silver;' />";
		}
		
		// display chunks
		$chunks = isset($_POST['chunk']) ? $_POST['chunk']:array();
		$limit = count($moduleChunks);
		if ($limit>0) echo "<h1>Chunks</h1>";
		for ($i=0;$i<$limit;$i++) {
			$chk = in_array($i,$chunks)||(!count($_POST)) ? "checked='checked'": "";
			echo "&nbsp;<input type='checkbox' name='chunk[]' value='$i' $chk />Install/Update <span class='comname'>".$moduleChunks[$i][0]."</span> - ".$moduleChunks[$i][1]."<hr size='1' style='border:1px dotted silver;' />";
		}

		// display modules
		$modules = isset($_POST['module']) ? $_POST['module']:array();
		$limit = count($moduleModules);
		if ($limit>0) echo "<h1>Modules</h1>";
		for ($i=0;$i<$limit;$i++) {
			$chk = in_array($i,$modules)||(!count($_POST)) ? "checked='checked'": "";
			echo "&nbsp;<input type='checkbox' name='module[]' value='$i' $chk />Install/Update <span class='comname'>".$moduleModules[$i][0]."</span> - ".$moduleModules[$i][1]."<hr size='1' style='border:1px dotted silver;' />";
		}

		// display plugins
		$plugins = isset($_POST['plugin']) ? $_POST['plugin']:array();
		$limit = count($modulePlugins);
		if ($limit>0) echo "<h1>Plugins</h1>";
		for ($i=0;$i<$limit;$i++) {
			$chk = in_array($i,$plugins)||(!count($_POST)) ? "checked='checked'": "";
			echo "&nbsp;<input type='checkbox' name='plugin[]' value='$i' $chk />Install/Update <span class='comname'>".$modulePlugins[$i][0]."</span> - ".$modulePlugins[$i][1]."<hr size='1' style='border:1px dotted silver;' />";
		}

		// display snippets
		$snippets = isset($_POST['snippet']) ? $_POST['snippet']:array();
		$limit = count($moduleSnippets);
		if ($limit>0) echo "<h1>Snippets</h1>";
		for ($i=0;$i<$limit;$i++) {
			$chk = in_array($i,$snippets)||(!count($_POST)) ? "checked='checked'": "";
			echo "&nbsp;<input type='checkbox' name='snippet[]' value='$i' $chk />Install/Update <span class='comname'>".$moduleSnippets[$i][0]."</span> - ".$moduleSnippets[$i][1]."<hr size='1' style='border:1px dotted silver;' />";
		}

		$o = ob_get_contents();
		ob_end_clean();
		return $o;
	}

?>
<!DOCTYPE html PUBliC "-//W3C//DTD XHTML 1.1//EN" 
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title><?php echo $moduleName; ?> &raquo; Install</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <style type="text/css">
             @import url(./style.css);
        </style>
	<script type="text/javascript" language="JavaScript" src="webelm.js"></script>
	<script language="JavaScript" type="text/javascript">
    	
    	var cursrc = 1;
		var syscheck = <?php echo $syscheck ? "true":"false"; ?>;
		var installMode = <?php echo $installMode; ?>; // -1 - not set, 0 - new, 1 - upgrade
		var sidebar = "<a href='<?php echo $moduleWhatsNewFile; ?>' target='_blank'><?php echo $moduleWhatsNewTitle; ?></a>&nbsp;<p /><img src='img_install.gif' width='48' height='48' />";		

		// jumpTo
		function jumpTo(n) {
			cursrc = n;
			for(i=1;i<=5;i++) {
				o = document.getElementById("screen"+i);
				if (o) {
					if(i==cursrc) o.style.display="block";
					else o.style.display="none";
				}
			}
		}
			
		// change screen
		function changeScreen(n){
			var o;
			var viewer = document.getElementById("viewer");
			var btnback = document.install.cmdback;
			var btnnext = document.install.cmdnext;
			
			//window.scrollTo(0,0);
			viewer.scrollTop = 0;
			// set default values
			btnback.value = "Back";
			btnnext.value = "Next";

			if(n==1) cursrc += 1;
			else cursrc -= 1;
			if(cursrc > 7) cursrc = 7;
			if(cursrc < 1) cursrc = 1;
			switch (cursrc) {
				case 1:	// welcome
					btnnext.disabled = "";
					btnback.style.display="none";
					break;
				case 2:	// 
					syscheck=false;
					btnnext.disabled = "";
					btnnext.value = "Install now";
					btnback.style.display="block";			
					break;
				case 3:	// final screen
					btnnext.value = "Close";
					btnback.style.display="none";
					document.install.submit();
					btnback.disabled = "disabled";
					btnnext.disabled = "disabled";
					break;
			}
			for(i=1;i<=3;i++) {
				o = document.getElementById("screen"+i);
				if (o) {
					if(i==cursrc) o.style.display="block";
					else o.style.display="none";
				}
			}
		}
				
		function closepage(){
			var chk = document.install.rminstaller;
			if(chk && chk.checked) {
				// remove install folder and files
				window.location.href = "../manager/processors/remove_installer.processor.php?rminstall=1";
			}
			else { 
				window.location.href = "../manager/";
			}
		}
		
    </script>
</head>	

<body>
<!-- start install screen-->
<table border="0" cellpadding="0" cellspacing="0" class="mainTable" style="width:100%;">
<tr>
    <td colspan="2">
		  <h1 style="font-size:18px;font-weight:bold;margin:0px;padding:15px;text-indent:32px;background: #ffffff url('img_banner.gif') no-repeat;"><?php echo $moduleName; ?></h1>
    </td>
  </tr>
  <tr class="fancyRow2">
    <td colspan="2" class="border-top-bottom smallText" align="right"><?php echo $moduleName; ?> </b>&nbsp;<i>version <?php echo $moduleVersion; ?></i></td>
  </tr>
  <tr align="left" valign="top">
    <td colspan="2"><table width="100%"  border="0" cellspacing="0" cellpadding="1">
      <tr align="left" valign="top">
        <td class="pad" id="content" colspan="2">
			<table border="0" width="100%">
			<tr>
			<td valign="top" nowrap="nowrap"><div id="sidebar" class="sidebar"><script>document.write(sidebar);</script></div></td>
			<td style="border-left:1px dotted silver;padding-left:30px;padding-right:20px;">
			<form name="install" action="index.php" method="post">
			<div id="viewer" class="viewer" style="visibility:hidden">
				<div id="screen1" style="display:block"><?php echo buildWelcomeScreen(); ?></div>
				<?php if(!$isPostBack) { ?>
					<div id="screen2" style="display:none"><?php echo buildOptionsScreen(); ?></div>
					<div id="screen3" style="display:none"><p /><br /><h1>Running setup script... please wait</h1></div>
				<?php } ?>
			</div>
			<br />
			<div id="navbar">
				<?php if($isPostBack) { ?>
					<input type='button' value='Close' name='cmdclose' style='float:right;width:100px;' onclick="closepage();" />
					<?php if($errors==0) { ?>
						<span id="removeinstall" style='float:left;cursor:pointer;color:#505050;line-height:18px;' onclick="var chk=document.install.rminstaller; if(chk) chk.checked=!chk.checked;"><input type="checkbox" name="rminstaller" onclick="event.cancelBubble=true;" checked="checked" style="cursor:default;" />Remove the install folder and files from my website. </span>
					<?php } ?>
				<?php } else {?>
					<input type='button' value='Next' name='cmdnext' style='float:right;width:100px;' onclick="changeScreen(1);" />
					<span style="float:right">&nbsp;</span>
					<input type='button' value='Back' name='cmdback' style='float:right;width:100px;' onclick="changeScreen(-1);" />
				<?php } ?>
			</div>
			<input name="syscheck" type="hidden" value="<?php echo ($syscheck && $errors) ? "on":""; ?>" />
			</form>
            </td>
            </tr>
            </table>
		</td>
      </tr>
    </table></td>
  </tr>
  <tr class="fancyRow2">
    <td class="border-top-bottom smallText" colspan="2"> 
    &nbsp;</td>
  </tr>
</table>
<!-- end install screen-->
<script type="text/javascript">
	var errors = <?php echo $errors; ?>;
</script>

<?php if(!$isPostBack) { ?>
	<script>
		<?php if ($syscheck) { ?>
			cursrc = 5;
			changeScreen(1);
		<?php } else {?>
		var btnback = document.install.cmdback;
		btnback.style.display="none";
		<?php } ?>
	</script>
<?php } ?>
<script type="text/javascript">
	var iviewer = document.getElementById("viewer");
	iviewer.style.visibility = 'visible';
</script>
</body>
</html>