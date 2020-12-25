<?php
///////////////////////////////////////////////////////////////////////////////////////////////////
// This file includes the functions used in MaxiGallery snippet
//
class maxiGallery {
	// Declaring private variables
	var $mgconfig;
	
	//
	// Constructor class
	//
	function maxiGallery($mgconfig,$strings) {
		// Set template variables to empty var
		$this->mgconfig = $mgconfig;
		$this->strings = $strings;
		$this->pics_tbl = null;
		$this->pageinfo = null;
		$this->path_to_gal = null;
	}
	
	//-------------------------------------------------------------------------------------------------
	//function for paging
	//returns array of pager values
	function getPagerData($numHits, $limit, $page) {
		$numHits = (int) $numHits;
		$limit = max((int) $limit, 1);
		$page = (int) $page;
		$numPages = ceil($numHits / $limit);
		$page = max($page, 1);
		$page = min($page, $numPages);
		$offset = ($page - 1) * $limit;
		$ret['offset'] = $offset; 
		$ret['limit'] = $limit; 
		$ret['numPages'] = $numPages; 
		$ret['page'] = $page; 
		return $ret;
	}
	//-------------------------------------------------------------------------------------------------
	//function to create table
	//returns true or false indicating success
	function createTable() {
		global $modx;
		$sql="CREATE TABLE $this->pics_tbl (
			`id` int(10) unsigned NOT NULL auto_increment,
			`gal_id` int(10) unsigned NOT NULL,
			`filename` tinytext NOT NULL,
			`title` text NOT NULL,
			`date` datetime NOT NULL,
			`descr` text default NULL,
			`pos` int(10) default NULL,
			`own_id` int(10) default NULL,
			`hide` int(1) default 0,
			PRIMARY KEY  (`id`)
	    ) TYPE=MyISAM AUTO_INCREMENT=1 ;";
		$query1=$modx->db->query($sql);
		return $query1;
	}
	//-------------------------------------------------------------------------------------------------
	//function to check database table for description field
	//returns true or false
	function isDescValid($table_desc){
		global $modx;
		for($i=0;$i<$modx->db->getRecordCount($table_desc);$i++)  {
			$temprow=$modx->db->getRow($table_desc, 'assoc');
			if ($temprow["Field"]=="descr") {
				return true;
			}
		}//if description field was not found, try to create it
		$query=$modx->db->query("ALTER TABLE ".$this->pics_tbl." ADD `descr` TEXT DEFAULT NULL ;");
		if($query){ 
			return true;
		}
		return false;
	}
	//-------------------------------------------------------------------------------------------------
	//function to check database table for position field
	//returns true or false
	function isPosValid($table_desc){
		global $modx;
		for($i=0;$i<$modx->db->getRecordCount($table_desc);$i++)  {
			$temprow=$modx->db->getRow($table_desc, 'assoc');
			if ($temprow["Field"]=="pos") {
				return true;
			}
		}//if position field was not found, try to create it
		$query=$modx->db->query("ALTER TABLE ".$this->pics_tbl." ADD `pos` int(10) DEFAULT NULL ;");
		if($query){ 
			return true;
		}
		return false;
	}
	//-------------------------------------------------------------------------------------------------
	//function to check database table for picture owner field
	//returns true or false
	function isOwnIDValid($table_desc){
		global $modx;
		for($i=0;$i<$modx->db->getRecordCount($table_desc);$i++)  {
			$temprow=$modx->db->getRow($table_desc, 'assoc');
			if ($temprow["Field"]=="own_id") {
				return true;
			}
		}//if owner field was not found, try to create it
		$query=$modx->db->query("ALTER TABLE ".$this->pics_tbl." ADD `own_id` int(10) DEFAULT NULL ;");
		if($query){ 
			return true;
		}
		return false;
	}
	//-------------------------------------------------------------------------------------------------
	//function to check database table for hide field
	//returns true or false
	function isHideValid($table_desc){
		global $modx;
		for($i=0;$i<$modx->db->getRecordCount($table_desc);$i++)  {
			$temprow=$modx->db->getRow($table_desc, 'assoc');
			if ($temprow["Field"]=="hide") {
				return true;
			}
		}//if hide field was not found, try to create it
		$query=$modx->db->query("ALTER TABLE ".$this->pics_tbl." ADD `hide` int(1) DEFAULT 0;");
		if($query){ 
			return true;
		}
		return false;
	}	
	//-------------------------------------------------------------------------------------------------
	//function to check access permissions, fix provided by TobyL
	function checkPermissions($userid,$docid){
		global $modx;
		if($userid) {
			//if user is logged in from backend, check user-document permissions 
			include_once $modx->config['base_path'].'manager/processors/user_documents_permissions.class.php';
    		$udperms = new udperms();
    		$udperms->user = $userid;
    		$udperms->document = $docid;
    		$udperms->role = $_SESSION['mgrRole'];
    		if ($udperms->checkPermissions()) {
    			return true;
    		}
		}
		
		//check whether user is logged in and belongs to selected webgroups
		if((count($this->mgconfig['manager_webgroups'])>0 && $modx->isMemberOfWebGroup($this->mgconfig['manager_webgroups'])) || (count($this->mgconfig['admin_webgroups'])>0 && $modx->isMemberOfWebGroup($this->mgconfig['admin_webgroups']))){
			return true; 
		//check whether user is logged in and is defined in manager_webusers
		} else if (($modx->getLoginUserName()!="" && in_array($modx->getLoginUserName(), $this->mgconfig['manager_webusers'])) || ($modx->getLoginUserName()!="" && in_array($modx->getLoginUserName(), $this->mgconfig['admin_webusers']))){
			return true;
		} else {
			return false;
		}
	}
	//-------------------------------------------------------------------------------------------------
	//function to create the pics
	function createthumb($filename,$filetype,$path_to_gal,$prefix="",$resize=true) {
		if($prefix == "tn_"){
			$use_watermark = $this->mgconfig['thumb_use_watermark'];
			$max_thumb_size = $this->mgconfig['max_thumb_size'];
			$quality = $this->mgconfig['quality_thumb'];
			$watermark_type = $this->mgconfig['thumb_watermark_type'];
			$watermark_img = $this->mgconfig['thumb_watermark_img'];
			$watermark_txt = $this->mgconfig['thumb_watermark_txt'];
			$watermark_txt_color = $this->mgconfig['thumb_watermark_txt_color'];
			$watermark_font = $this->mgconfig['thumb_watermark_font'];
			$watermark_valign = $this->mgconfig['thumb_watermark_valign'];
			$watermark_halign = $this->mgconfig['thumb_watermark_halign'];
			$watermark_txt_hmargin = $this->mgconfig['thumb_watermark_txt_hmargin'];
			$watermark_txt_vmargin = $this->mgconfig['thumb_watermark_txt_vmargin'];
			$use_dropshadow = $this->mgconfig['thumb_use_dropshadow'];
			$dropshadow_bg = $this->mgconfig['thumb_shadow_bgcolor'];
			$shadow_path = $this->mgconfig['thumb_shadow_path'];
			$use_imagemask = $this->mgconfig['thumb_use_imagemask'];
			$imagemask_bg = $this->mgconfig['thumb_mask_bgcolor'];
			$imagemask_pos = $this->mgconfig['thumb_mask_position'];
			$imagemask_img = $this->mgconfig['thumb_mask_img'];
		}else if($prefix == "big_"){
			$max_thumb_size = $this->mgconfig['max_big_size'];
			$quality = $this->mgconfig['quality_big'];
			$use_watermark = $this->mgconfig['big_use_watermark'];
			$watermark_type = $this->mgconfig['big_watermark_type'];
			$watermark_img = $this->mgconfig['big_watermark_img'];
			$watermark_txt = $this->mgconfig['big_watermark_txt'];
			$watermark_txt_color = $this->mgconfig['big_watermark_txt_color'];
			$watermark_font = $this->mgconfig['big_watermark_font'];
			$watermark_valign = $this->mgconfig['big_watermark_valign'];
			$watermark_halign = $this->mgconfig['big_watermark_halign'];
			$watermark_txt_hmargin = $this->mgconfig['big_watermark_txt_hmargin'];
			$watermark_txt_vmargin = $this->mgconfig['big_watermark_txt_vmargin'];
			$use_dropshadow = $this->mgconfig['big_use_dropshadow'];
			$dropshadow_bg = $this->mgconfig['big_shadow_bgcolor'];
			$shadow_path = $this->mgconfig['big_shadow_path'];
			$use_imagemask = $this->mgconfig['big_use_imagemask'];
			$imagemask_bg = $this->mgconfig['big_mask_bgcolor'];
			$imagemask_pos = $this->mgconfig['big_mask_position'];
			$imagemask_img = $this->mgconfig['big_mask_img'];
		}else{
			$max_thumb_size = $this->mgconfig['max_pic_size'];
			$quality = $this->mgconfig['quality_pic'];
			$use_watermark = $this->mgconfig['pic_use_watermark'];
			$watermark_type = $this->mgconfig['pic_watermark_type'];
			$watermark_img = $this->mgconfig['pic_watermark_img'];
			$watermark_txt = $this->mgconfig['pic_watermark_txt'];
			$watermark_txt_color = $this->mgconfig['pic_watermark_txt_color'];
			$watermark_font = $this->mgconfig['pic_watermark_font'];
			$watermark_valign = $this->mgconfig['pic_watermark_valign'];
			$watermark_halign = $this->mgconfig['pic_watermark_halign'];
			$watermark_txt_hmargin = $this->mgconfig['pic_watermark_txt_hmargin'];
			$watermark_txt_vmargin = $this->mgconfig['pic_watermark_txt_vmargin'];
			$use_dropshadow = $this->mgconfig['pic_use_dropshadow'];
			$dropshadow_bg = $this->mgconfig['pic_shadow_bgcolor'];
			$shadow_path = $this->mgconfig['pic_shadow_path'];
			$use_imagemask = $this->mgconfig['pic_use_imagemask'];
			$imagemask_bg = $this->mgconfig['pic_mask_bgcolor'];
			$imagemask_pos = $this->mgconfig['pic_mask_position'];
			$imagemask_img = $this->mgconfig['pic_mask_img'];
		}
		//resize and watermark if needed
		include_once($modx->config['base_path'].MAXIGALLERY_PATH.'watermark/Thumbnail.class.php');
		$thumb=new Thumbnail($path_to_gal.$filename);
		$thumb->quality=$quality;
		if($filetype=="jpeg"){
			$thumb->output_format='JPG';
		}else if($filetype=="png"){
			$thumb->output_format='PNG';
		}
		if($use_watermark){	
			//apply watermark
			if($watermark_type == "image"){
				$thumb->img_watermark=$watermark_img;
				$thumb->img_watermark_Valing=strtoupper($watermark_valign);
				$thumb->img_watermark_Haling=strtoupper($watermark_halign);
			}else{
				$thumb->txt_watermark=$watermark_txt;
				$thumb->txt_watermark_color=$watermark_txt_color;
				$thumb->txt_watermark_font=$watermark_font;
				$thumb->txt_watermark_Valing=strtoupper($watermark_valign);
				$thumb->txt_watermark_Haling=strtoupper($watermark_halign);
				$thumb->txt_watermark_Hmargin=strtoupper($watermark_txt_hmargin);
				$thumb->txt_watermark_Vmargin=strtoupper($watermark_txt_vmargin);
			}
		}
		if($resize){
			$sizes = explode('x', $max_thumb_size);
			if (count($sizes) > 1) {
				$thumb->size($sizes[0], $sizes[1]);
			} else {
				$thumb->size_auto($sizes[0]);
			}
		}
		$thumb->process();
		$thumb->save($path_to_gal.$prefix.$filename);
		
		if ($thumb->img["src"]) {
			@ImageDestroy($thumb->img["src"]);
		}
		if ($thumb->img["watermark"]) {
			@ImageDestroy($thumb->img["watermark"]);
		}

		unset($thumb);
		
		//dropshadow
		if($use_dropshadow){
			include_once($modx->config['base_path'].MAXIGALLERY_PATH.'dropshadow/class.dropshadow.php');
			$ds = new dropShadow(FALSE);
			$ds->setShadowPath($shadow_path);
			$ds->loadImage($path_to_gal.$prefix.$filename);
			$ds->applyShadow($dropshadow_bg);
			$ds->saveShadow($path_to_gal.$prefix.$filename,'',100);
			$ds->flushImages();
			unset($ds);
		}

		//imagemask
		if($use_imagemask){
			switch ($imagemask_pos) {
				case "topleft" :
					$mask_option = 0;
					break;
				case "top" :
					$mask_option = 1;
					break;
				case "topright" :
					$mask_option = 2;
					break;
				case "left" :
					$mask_option = 3;
					break;
				case "center" :
					$mask_option = 4;
					break;
				case "right" :
					$mask_option = 5;
					break;
				case "bottomleft" :
					$mask_option = 6;
					break;
				case "bottom" :
					$mask_option = 7;
					break;
				case "bottomright" :
					$mask_option = 8;
					break;
				case "resize" :
					$mask_option = 9;
					break;
				default :
					$mask_option = 9;
					break;
			}
			include_once($modx->config['base_path'].MAXIGALLERY_PATH.'imagemask/class.imagemask.php');
			$im = new imageMask($imagemask_bg);
		    $im->setDebugging(false);
			$im->maskOption($mask_option);
			$im->loadImage($path_to_gal.$prefix.$filename);
			$im->applyMask($imagemask_img);
		    $im->saveImage($path_to_gal.$prefix.$filename);
		    
		    @ImageDestroy($im->_img['orig']);
			@ImageDestroy($im->_mask['orig']);
			@ImageDestroy($im->_img['final']);
			@ImageDestroy($im->_mask['gray']);
			
			unset($im);
		}
	}
	//-------------------------------------------------------------------------------------------------
	//function to create the gallery xml for slidebox
	function createGalleryXML(){
		global $modx;
		$res=$modx->db->query("SELECT * FROM " . $this->pics_tbl . " WHERE (gal_id='" . $this->pageinfo['id'] . "' AND NOT hide='1') ORDER BY " . $this->mgconfig['order_by'] . " " . $this->mgconfig['order_direction']);
		//pic_count
		$totalpics = $modx->db->getRecordCount($res);
		if($totalpics>0){
			// create a new XML document
			$xmlstr = '<?xml version="1.0" encoding="'.$modx->config['etomite_charset'].'"?>'."\n";
			$xmlstr .= '<response>'."\n";
			// process pics
			$i = 1;
			while($pic=$modx->fetchRow($res)) {
				$url = $modx->config['site_url'].$this->path_to_gal.$pic['filename'];
				//create pic nodes
				$xmlstr .= "\t".'<source id="'.$url.'">'."\n";
				$xmlstr .= "\t\t".'<title><![CDATA['.stripslashes($pic['title']).']]></title>'."\n";
				$xmlstr .= "\t\t".'<caption><![CDATA['.stripslashes($pic['descr']).']]></caption>'."\n";
				$xmlstr .= "\t\t".'<number><![CDATA['.$i.'/'.$totalpics.']]></number>'."\n";
				$xmlstr .= "\t".'</source>'."\n";
				$i++;
			}
			$xmlstr .= '</response>';
			//write the xml file
			$fp = fopen($this->path_to_gal."gallery.xml", "w");
			fwrite($fp, $xmlstr);
			fclose($fp);
			chmod($this->path_to_gal."gallery.xml",0666);
		}else if(file_exists($this->path_to_gal."gallery.xml")){ //if gallery xml exists but not in use, delete it
			unlink($this->path_to_gal."gallery.xml");
		}
	}
	//-------------------------------------------------------------------------------------------------
	//function to delete non empty directory
	function deldir($dir){
	  $current_dir = opendir($dir);
	  while($entryname = readdir($current_dir)){
	     if(is_dir("$dir/$entryname") and ($entryname != "." and $entryname!="..")){
	        $this->deldir("${dir}/${entryname}");
	     }elseif($entryname != "." and $entryname!=".."){
	        unlink("${dir}/${entryname}");
	     }
	  }
	  closedir($current_dir);
	  rmdir(${dir});
	}
	//-------------------------------------------------------------------------------------------------
	//function to convert gif to png
	function gif2png($name){
		$src=imagecreatefromgif($this->path_to_gal.$name);
		//calculate size for the image
		$src_size = getimagesize($this->path_to_gal.$name);
		//create blank destination image
		$dest=imagecreate($src_size[0],$src_size[1]);
		//delete gif image
		unlink($this->path_to_gal.$name);
		$name = str_replace(".gif", ".png", $name);
		//resize the image
		if(function_exists('imagecopyresampled')){
			imagecopyresampled($dest,$src,0,0,0,0,$src_size[0],$src_size[1],$src_size[0],$src_size[1]);
		}else{
			imagecopyresized($dest,$src,0,0,0,0,$src_size[0],$src_size[1],$src_size[0],$src_size[1]);
		}
		//create new image
		imagepng($dest,$this->path_to_gal.$name);
		@imagedestroy($src);
		@imagedestroy($dest);
		return $name;
	}
	//-------------------------------------------------------------------------------------------------
	//function to figure out what pics to do from the file and do them
	function handlePics($name, $type){
		global $modx;
		$pic_date = "NOW()";
		//read EXIF information
		if($type == "jpg"){
			if(function_exists("exif_read_data")) {
				$exif = exif_read_data($this->path_to_gal.$name, 0, true);
				if(array_key_exists("EXIF", $exif)) {
					$pic_date = " '".$exif["EXIF"]["DateTimeOriginal"]."' ";
				}
			}
		}
		//if gif image, convert to png
		if($type == "gif"){
			$name = $this->gif2png($name);
			$type = "png";
		}
		$imagesize=getimagesize($this->path_to_gal.$name);
		//create bigger image if needed
		if ($this->mgconfig['keep_bigimg'] == true){
			if($this->mgconfig['max_big_size'] != 0){
				if($this->mgconfig['max_pic_size']>0 && ($imagesize[0]>$this->mgconfig['max_pic_size'] || $imagesize[1]>$this->mgconfig['max_pic_size'])) {
					if($this->mgconfig['max_big_size']>0 && ($imagesize[0]>$this->mgconfig['max_big_size'] || $imagesize[1]>$this->mgconfig['max_big_size'])){
						//if picture size is bigger than big pic size, resize
						$this->createthumb($name,$type,$this->path_to_gal,"big_");
					}else if($this->mgconfig['big_use_watermark'] || $this->mgconfig['big_use_dropshadow'] || $this->mgconfig['big_use_imagemask']){
						//if not bigger, but image still needs to be changed
						$this->createthumb($name,$type,$this->path_to_gal,"big_",false);
					}else{
						//else just copy
						copy($this->path_to_gal.$name, $this->path_to_gal."big_".$name);
					}
				}
			}else if($this->mgconfig['big_use_watermark'] || $this->mgconfig['big_use_dropshadow'] || $this->mgconfig['big_use_imagemask']){
				//if max size is not set for the big pics, but image still needs to be changed
				$this->createthumb($name,$type,$this->path_to_gal,"big_",false);		
			}else{
				//else just copy
				copy($this->path_to_gal.$name, $this->path_to_gal."big_".$name);
			}
		}
		//create thumbnail
		$this->createthumb($name,$type,$this->path_to_gal,"tn_");
		//create normal 
		if($this->mgconfig['max_pic_size']>0 && ($imagesize[0]>$this->mgconfig['max_pic_size'] || $imagesize[1]>$this->mgconfig['max_pic_size'])) {
			$this->createthumb($name,$type,$this->path_to_gal,"");
		}else if($this->mgconfig['pic_use_watermark'] || $this->mgconfig['pic_use_dropshadow'] || $this->mgconfig['pic_use_imagemask']){
			//if max image size is not reached, but the image needs some changing to be done
			$this->createthumb($name,$type,$this->path_to_gal,"",false);
		}
		if($modx->getLoginUserID()!="" && $modx->getLoginUserType()=='web'){ //if web user is posting picture, put owner id
			$rs1=$modx->db->query("INSERT INTO " . $this->pics_tbl . "(id, gal_id, filename, title, date, own_id) VALUES(NULL,'" . $this->pageinfo['id'] . "','" . $name . "',''," . $pic_date . ",'".$modx->getLoginUserID()."')");
		}else{
			$rs1=$modx->db->query("INSERT INTO " . $this->pics_tbl . "(id, gal_id, filename, title, date) VALUES(NULL,'" . $this->pageinfo['id'] . "','" . $name . "',''," . $pic_date . ")");
		}
	}
	//-------------------------------------------------------------------------------------------------
	//function to handle uploaded file
	function handleFile($name, $current_pics_count=-1){
		if(substr(strtolower($name),-4)==".jpg" || substr(strtolower($name),-5)==".jpeg") {
			$this->handlePics($name, "jpeg");
		}else if(substr(strtolower($name),-4)==".png"){
			$this->handlePics($name, "png");
		}else if(substr(strtolower($name),-4)==".gif"){
			if(function_exists('imagecreatefromgif')){
				$this->handlePics($name, "gif");
			}else{
				unlink($this->path_to_gal.$name);
				return $this->strings['gif_not_supported'];
			}
		}else if(substr(strtolower($name),-4)==".zip") {
			if (!class_exists('PclZip')) {
				$zipclass = $modx->config['base_path'].MAXIGALLERY_PATH.'pclzip/pclzip.lib.php';
				if (file_exists($zipclass)) {
					include_once $zipclass;
				} else {
					return 'Cannot find PclZip class file! ('.$zipclass.')';
				}
			}
			
  			$archive = new PclZip($this->path_to_gal.$name);
  			$list = $archive->extract(PCLZIP_OPT_PATH, $this->path_to_gal, PCLZIP_OPT_REMOVE_ALL_PATH, PCLZIP_CB_PRE_EXTRACT, 'preZipExtract');
  			unlink($this->path_to_gal.$name);
  			if ($list == 0) {
    			return "Error : ".$archive->errorInfo(true);
  			} else {
  				for($i=0;$i<count($list);$i++){
  					if ($list[$i]['status'] == 'ok') {
  						//if max pics limit reached, return
  						if($this->mgconfig['max_pic_number']!=0 && $current_pics_count != -1 && ($this->mgconfig['max_pic_number'] <= ($i + $current_pics_count))){
							return $this->strings['max_pics_reached_some_discarded'];
						}
  						//remove random number from the beginning of filename and generate new filename
  						$info = pathinfo($list[$i]['filename']);
  						$newname = $this->getFilename(substr($info['basename'], 7));
  						if(rename($list[$i]['filename'], $info['dirname'].'/'.$newname)){
  							$this->handleFile($newname);
  						} else {
  							$this->handleFile($list[$i]['filename']);	
  						}
  					}
  				}
  			}
		} else {
			unlink($this->path_to_gal.$name);
			return $this->strings['supported_types'];
		}
		return "";
	}
	//-------------------------------------------------------------------------------------------------
	//function to get suitable filename according to settings
	function getFilename($name){
		$name=$this->clearFilename($name);
		if ($this->mgconfig['random_filenames'] && $name != "") {
			$ext = strtolower(substr($name, strrpos($name, ".")));
			$name = $this->getRandomString(8).$ext;
		}
		if($name!="") {
			//check for existing filenames
			$ni = 1;
			$base = $name;
			while (file_exists($this->path_to_gal.$name)) { 
				$name=$ni.$base; 
				$ni++;
			}
		}
		return $name;
	}
	//-------------------------------------------------------------------------------------------------
	//function to add SmoothGallery styles
	function regSmoothGalleryCSS($smoothGalleryId){
		global $modx;
		$smoothgallery_css_str = '
			<style type="text/css">
			#myGallery'.$smoothGalleryId.'
			{
			width: '.$this->mgconfig['smoothgallery_width'].'px;
			height: '.$this->mgconfig['smoothgallery_height'].'px;
			z-index:5;
			display: none;
			}
			</style>';
		$smoothgallery_script_str = '
			<script type="text/javascript">
			function startGallery() {
			var myGallery = new gallery($(\'myGallery'.$smoothGalleryId.'\'), {
			showArrows: '.$this->mgconfig['smoothgallery_showArrows'].', 
			showCarousel: '.$this->mgconfig['smoothgallery_showCarousel'].',
			showInfopane: '.$this->mgconfig['smoothgallery_showInfopane'].', 
			thumbHeight: '.$this->mgconfig['smoothgallery_thumbHeight'].', 
			thumbWidth: '.$this->mgconfig['smoothgallery_thumbWidth'].', 
			thumbSpacing: '.$this->mgconfig['smoothgallery_thumbSpacing'].', 
			embedLinks: '.$this->mgconfig['smoothgallery_embedLinks'].', 
			fadeDuration: '.$this->mgconfig['smoothgallery_fadeDuration'].', 
			timed: '.$this->mgconfig['smoothgallery_timed'].', 
			delay: '.$this->mgconfig['smoothgallery_delay'].', 
			preloader: '.$this->mgconfig['smoothgallery_preloader'].', 
			slideInfoZoneOpacity: '.$this->mgconfig['smoothgallery_slideInfoZoneOpacity'].', 
			carouselMinimizedOpacity: '.$this->mgconfig['smoothgallery_carouselMinimizedOpacity'].', 
			carouselMinimizedHeight: '.$this->mgconfig['smoothgallery_carouselMinimizedHeight'].', 
			carouselMaximizedOpacity: '.$this->mgconfig['smoothgallery_carouselMaximizedOpacity'].', 
			textShowCarousel: \''.$this->mgconfig['smoothgallery_textShowCarousel'].'\' 
			});
			}
			window.onDomReady(startGallery);
			</script>';
		$modx->regClientCSS($smoothgallery_css_str);
		$modx->regClientScript($smoothgallery_script_str);
	}
	//-------------------------------------------------------------------------------------------------
	//function to register css and javascript from snippet parameters
	function regSnippetScriptsAndCSS(){
		global $modx;
		if ($this->mgconfig['css'] != "") {
			if ($modx->getChunk($this->mgconfig['css']) != "") {
				$modx->regClientCSS($modx->getChunk($this->mgconfig['css']));
			} else if (file_exists($modx->config['base_path'].$this->mgconfig['css'])) {
				$modx->regClientCSS('<link rel="stylesheet" href="'. $modx->config['base_url'].$this->mgconfig['css'].'" type="text/css" media="screen" />');
			} else {	
				$modx->regClientCSS($this->mgconfig['css']);
			}
		}
		if ($this->mgconfig['js'] != "") {
			if ($modx->getChunk($this->mgconfig['js']) != "") {
				$modx->regClientStartupScript($modx->getChunk($this->mgconfig['js']));
			} else if (file_exists($modx->config['base_path'].$this->mgconfig['js'])) {
				$modx->regClientStartupScript($modx->config['base_url'].$this->mgconfig['js']);
			} else {
				$modx->regClientStartupScript($this->mgconfig['js']);
			}
		}
	}
	//-------------------------------------------------------------------------------------------------
	//function to load correct css and scripts
	function regScriptsAndCSS(){
		global $modx;
		
		//slidebox css
		$slidebox_css_link = '<link rel="stylesheet" href="'. $modx->config['base_url'].MAXIGALLERY_PATH.'slidebox/style.css" type="text/css" media="screen" />';
		$slidebox_css_str = '
			<!--[if gte IE 5.5]>
			<![if lt IE 7]>
			<style type="text/css">
			* html #overlay{
			background-color: #333;
			back\ground-color: transparent;
			background-image: url(' . $modx->config['base_url'] . MAXIGALLERY_PATH . 'slidebox/blank.gif);
			filter: progid:DXImageTransform.Microsoft.AlphaImageLoader (src="' . $modx->config['base_url'] . MAXIGALLERY_PATH. 'slidebox/overlay.png", sizingMethod="scale");
			</style>
			<![endif]>
			<![endif]-->';
		
		//slidebox scripts
		$slidebox_script_link1 = $modx->config['base_url'] . MAXIGALLERY_PATH . 'slidebox/slidebox_setup.js';
		$slidebox_settings = MAXIGALLERY_PATH.'slidebox/slidebox_lang_'.$this->mgconfig['lang'].'.js';
		if(file_exists($modx->config['base_path'].$slidebox_settings)){
			$slidebox_script_link2 = $modx->config['base_url'] . $slidebox_settings;
		}else{
			$slidebox_script_link2 = $modx->config['base_url'] . MAXIGALLERY_PATH . 'slidebox/slidebox_lang_en.js';
		}
		$slidebox_script_link3 = $modx->config['base_url'] . MAXIGALLERY_PATH . 'slidebox/prototype.js';
		$slidebox_script_link4 = $modx->config['base_url'] . MAXIGALLERY_PATH . 'slidebox/slidebox.js';

		//lightbox scripts
		$lightboxv2_css_link = '<link rel="stylesheet" href="' . $modx->config['base_url'] . MAXIGALLERY_PATH . 'lightboxv2/css/lightbox.css" type="text/css" media="screen" />';
		$lightboxv2_script_link1 = $modx->config['base_url'] . MAXIGALLERY_PATH . 'lightboxv2/js/lightbox_setup.js'; 
		$lightboxv2_settings = MAXIGALLERY_PATH . 'lightboxv2/js/lightbox_lang_'.$this->mgconfig['lang'].'.js';
		if(file_exists($modx->config['base_path'].$lightboxv2_settings)){
			$lightboxv2_script_link2 = $modx->config['base_url'] . $lightboxv2_settings;
		}else{
			$lightboxv2_script_link2 = $modx->config['base_url'] . MAXIGALLERY_PATH . 'lightboxv2/js/lightbox_lang_en.js';
		}
		$lightboxv2_script_link3 = $modx->config['base_url'] . MAXIGALLERY_PATH . 'lightboxv2/js/prototype.js';
		$lightboxv2_script_link4 = $modx->config['base_url'] . MAXIGALLERY_PATH . 'lightboxv2/js/scriptaculous.js?load=effects';
		$lightboxv2_script_link5 = $modx->config['base_url'] . MAXIGALLERY_PATH . 'lightboxv2/js/lightbox.js';
		
		//slimbox scripts
		$slimbox_css_link = '<link rel="stylesheet" href="' . $modx->config['base_url'] . MAXIGALLERY_PATH . 'slimbox/css/slimbox.css" type="text/css" media="screen" />';
		$slimbox_script_link1 = $modx->config['base_url'] . MAXIGALLERY_PATH . 'slimbox/js/mootools.js'; 
		$slimbox_settings = MAXIGALLERY_PATH . 'slimbox/js/slimbox_lang_'.$this->mgconfig['lang'].'.js';
		if(file_exists($modx->config['base_path'].$slimbox_settings)){
			$slimbox_script_link2 = $modx->config['base_url'] . $slimbox_settings;
		}else{
			$slimbox_script_link2 = $modx->config['base_url'] . MAXIGALLERY_PATH . 'slimbox/js/slimbox_lang_en.js';
		}
		$slimbox_script_link3 = $modx->config['base_url'] . MAXIGALLERY_PATH . 'slimbox/js/slimbox.js';		
		
		//smoothgallery scripts
		$smoothgallery_css_link = '<link rel="stylesheet" href="' . $modx->config['base_url'] . MAXIGALLERY_PATH . 'smoothgallery/css/jd.gallery.css" type="text/css" media="screen" />';
		$smoothgallery_script_link1 = $modx->config['base_url'] . MAXIGALLERY_PATH . 'smoothgallery/js/mootools.js'; 
		$smoothgallery_script_link2 = $modx->config['base_url'] . MAXIGALLERY_PATH . 'smoothgallery/js/jd.gallery.js';
		
				
		//custom scripts
		$popup_script = $modx->config['base_url'] . MAXIGALLERY_PATH . 'js/popup.js';
		$external_script = $modx->config['base_url'] . MAXIGALLERY_PATH . 'js/external.js';
		$click_script = $modx->config['base_url'] . MAXIGALLERY_PATH . 'js/click.js';

		//register popup helper if in use
		if(($this->mgconfig['display'] == "embedded" && $this->mgconfig['embedtype'] == "popup") || ($this->mgconfig['keep_bigimg'] && $this->mgconfig['big_img_linkstyle'] == "popup" )){
			$modx->regClientStartupScript($popup_script);
		}

		//register external helper if in use
		if(($this->mgconfig['display'] == "embedded" && $this->mgconfig['embedtype'] == "external") || ($this->mgconfig['keep_bigimg'] && $this->mgconfig['big_img_linkstyle'] == "external" )){
			$modx->regClientStartupScript($external_script);
		}
		
		//register rightclick disabling if enabled
		if($this->mgconfig['disable_rightclick']){
			$modx->regClientStartupScript($click_script);
		}
		
		//register slidebox scripts if in use
		if(($this->mgconfig['display'] == "embedded" && $this->mgconfig['embedtype'] == "slidebox") || ($this->mgconfig['keep_bigimg'] && $this->mgconfig['big_img_linkstyle'] == "slidebox")){
			$modx->regClientCSS($slidebox_css_link);
			$modx->regClientCSS($slidebox_css_str);
			$modx->regClientStartupScript($slidebox_script_link1);
			$modx->regClientStartupScript($slidebox_script_link2);
			// do not include prototype if javascript libraries are set to be disabled
			if(!$this->mgconfig['disable_js_libs']) {
				$modx->regClientStartupScript($slidebox_script_link3);
			}
			$modx->regClientStartupScript($slidebox_script_link4);
		}
		
		//register lightboxv2.0 scripts if in use
		if(($this->mgconfig['display'] == "embedded" && $this->mgconfig['embedtype'] == "lightboxv2") || ($this->mgconfig['keep_bigimg'] && $this->mgconfig['big_img_linkstyle'] == "lightboxv2")){
			$modx->regClientCSS($lightboxv2_css_link);
			$modx->regClientStartupScript($lightboxv2_script_link1);
			$modx->regClientStartupScript($lightboxv2_script_link2);
			// do not include prototype and scriptaculous if javascript libraries are set to be disabled
			if(!$this->mgconfig['disable_js_libs']) {
				$modx->regClientStartupScript($lightboxv2_script_link3);
				$modx->regClientStartupScript($lightboxv2_script_link4);
			}
			$modx->regClientStartupScript($lightboxv2_script_link5);
		}
		
		//register slimbox scripts if in use
		if(($this->mgconfig['display'] == "embedded" && $this->mgconfig['embedtype'] == "slimbox") || ($this->mgconfig['keep_bigimg'] && $this->mgconfig['big_img_linkstyle'] == "slimbox")){
			$modx->regClientCSS($slimbox_css_link);
			// do not include mootools if javascript libraries are set to be disabled
			if(!$this->mgconfig['disable_js_libs']) {
				$modx->regClientStartupScript($slimbox_script_link1);
			}
			$modx->regClientStartupScript($slimbox_script_link2);
			$modx->regClientStartupScript($slimbox_script_link3);
		}
		
		//register smoothgallery scripts if in use
		if(($this->mgconfig['display'] == "embedded" && $this->mgconfig['embedtype'] == "smoothgallery") || ($this->mgconfig['smoothgallery_pictureview'])) {
			$modx->regClientCSS($smoothgallery_css_link);
			// do not include mootools if javascript libraries are set to be disabled
			if(!$this->mgconfig['disable_js_libs']) {
				$modx->regClientStartupScript($smoothgallery_script_link1);
			}
			$modx->regClientStartupScript($smoothgallery_script_link2);
		}
		
		$this->regSnippetScriptsAndCSS();
	}
	//-------------------------------------------------------------------------------------------------
	//function to clear the picture filenames
	function clearFilename($filename){
		//try to salvage some of the usual illegal characters
		$filename = strtr($filename, "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöùúûüýþÿ ", "aaaaaaaceeeeiiiidnooooo0uuuuypsaaaaaaaceeeeiiiionooooouuuuypy_");
		//get filename without extension
		$name = substr($filename, 0, strrpos($filename,"."));
		//get extension
		$ext = substr(strrchr($filename, "."), 0);
		//strip illegal characters from the filename and double "_"
		$name = strtolower(preg_replace("/_+/", '_', preg_replace("/[^a-zA-Z0-9\-\_]/", '_', $name))); //clear filename
		//return the cleaned filename
		return $name.$ext;
	}
	//-------------------------------------------------------------------------------------------------
	//function to get all child documents by Mark Kaplan, modified by doze
	function getAllSubDocuments($childgalleryIds = array(), $seedArray = array(), $sortby = "createdon", $sortdir = "desc", $descendentDepth = 1, $limit = 0, $showPublishedOnly = true, $seeThruUnpub = false, $tvNames = array()) {
		global $modx;

		// ---------------------------------------------------
		// Seed list of viable ids
		// ---------------------------------------------------

		$kids = array();
		foreach ($seedArray AS $seed) {
			$kids = $this->getChildIds($seed, $descendentDepth, $kids);
		}
		$kids = array_values($kids);
		
		$kids = array_merge($kids, $seedArray);
		
		$index = array_search($modx->documentIdentifier, $kids);
		
		if ($index) {
			unset($kids[$index]);
		}

		//remove id's that don't have gallery
		$kids = array_intersect($kids, $childgalleryIds);
		
		$resources = $modx->getDocuments($kids, $showPublishedOnly, 0, "*", '', $sortby, $sortdir);
		
		if ($limit != 0) {
			$resources = array_slice($resources, 0, $limit);
		}
		
		if(count($tvNames)>0){
			$resultIds = array();
			foreach ($resources as $res) {
				$resultIds[] = $res['id'];
			}
			
			$allTvars = $this->getTVList();
			$tvValues = array();
			foreach ($tvNames as $tvName) {
				if (in_array($tvName, $allTvars)) {
					$tvValues = array_merge_recursive($this->appendTV($tvName,$resultIds),$tvValues);
				}
			}
			//loop through the document array and add the tvar values to each document
			for ($i=0;$i<count($resources);$i++) {
				if (array_key_exists("#{$resources[$i]['id']}",$tvValues)) {
					foreach ($tvValues["#{$resources[$i]['id']}"] as $tvName => $tvValue) {
						$resources[$i]['tv.'.$tvName] = $tvValue;
					}
				}
			}
		}
		return $resources;

	}
	//-------------------------------------------------------------------------------------------------	
	//function to get all childs by Jason Coward
	function getChildIds($id, $depth= 10, $children= array()) {
		global $modx;
		$c= null;
		foreach ($modx->documentMap as $mapEntry) {
			if (isset ($mapEntry[$id])) {
				$childId= $mapEntry[$id];
				$childKey= array_search($childId, $modx->documentListing);
				if (!$childKey) {
					$childKey= "$childId";
				}
				$c[$childKey]= $childId;
			}
     	}
     	$depth--;
		if (is_array($c)) {
			if (is_array($children)) {
				$children= $children + $c;
			} else {
				$children= $c;
			}
			if ($depth) {
				foreach ($c as $child) {
					$children= $children + $this->getChildIds($child, $depth, $children);
				}
			}
     	}
		return $children;
	}
	
	//-------------------------------------------------------------------------------------------------	
	//function to generate urls for pagination by keeping existing url parameters by bS
	//http://modxcms.com/forums/index.php/topic,5309.0.html
	function getPaginationUrl($docid, $docalias, $array_values) {
		global $modx;
		$array_url = $_GET;
		$urlstring = array();
		
		unset($array_url["id"]);
		unset($array_url["q"]);
		
		$array_url = array_merge($array_url,$array_values);

		foreach ($array_url as $name => $value) {
			if (!is_null($value)) {
			  $urlstring[] = $name . '=' . urlencode($value);
			}
		}
		
		return $modx->makeUrl($docid, $docalias, join('&',$urlstring));
	}

	//-------------------------------------------------------------------------------------------------	
	//function to generate random strings
	function getRandomString($lenght){
		$str = "";
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		while ($i <= $lenght) {
			$num = rand(0,61);
			$tmp = substr($salt, $num, 1);
			$str = $str . $tmp;
			$i++;
		}
		return $str;
	}
	
	//-------------------------------------------------------------------------------------------------	
	//function to append a TV to the documents array by Mark Kaplan 
	function appendTV($tvname,$docIDs){
		global $modx;
		
		$baspath= $modx->config["base_path"] . "manager/includes";
	    include_once $baspath . "/tmplvars.format.inc.php";
	    include_once $baspath . "/tmplvars.commands.inc.php";

		$tb1 = $modx->getFullTableName("site_tmplvar_contentvalues");
		$tb2 = $modx->getFullTableName("site_tmplvars");

		$query = "SELECT stv.name,stc.tmplvarid,stc.contentid,stv.type,stv.display,stv.display_params,stc.value";
		$query .= " FROM ".$tb1." stc LEFT JOIN ".$tb2." stv ON stv.id=stc.tmplvarid ";
		$query .= " WHERE stv.name='".$tvname."' AND stc.contentid IN (".implode($docIDs,",").") ORDER BY stc.contentid ASC;";
		$rs = $modx->db->query($query);
		$tot = $modx->db->getRecordCount($rs);
		$resourceArray = array();
		for($i=0;$i<$tot;$i++)  {
			$row = @$modx->fetchRow($rs);
			$resourceArray["#{$row['contentid']}"][$row['name']] = getTVDisplayFormat($row['name'], $row['value'], $row['display'], $row['display_params'], $row['type'],$row['contentid']);   
		}

		if ($tot != count($docIDs)) {
			$query = "SELECT name,type,display,display_params,default_text";
			$query .= " FROM $tb2";
			$query .= " WHERE name='".$tvname."' LIMIT 1";
			$rs = $modx->db->query($query);
			$row = @$modx->fetchRow($rs);
			$defaultOutput = getTVDisplayFormat($row['name'], $row['default_text'], $row['display'], $row['display_params'], $row['type']);
			foreach ($docIDs as $id) {
				if (!isset($resourceArray["#{$id}"])) {
					$resourceArray["#{$id}"][$tvname] = $defaultOutput;
				}
			}
		}
		return $resourceArray;
	}

	//-------------------------------------------------------------------------------------------------
	//function to get a list of all available TVs by Mark Kaplan
	function getTVList() {
		global $modx;
		$table = $modx->getFullTableName("site_tmplvars");
		$tvs = $modx->db->select("name", $table);
			// TODO: make it so that it only pulls those that apply to the current template
		$dbfields = array();
		while ($dbfield = $modx->db->getRow($tvs))
			$dbfields[] = $dbfield['name'];
		return $dbfields;
	}	
}
//-------------------------------------------------------------------------------------------------
?>