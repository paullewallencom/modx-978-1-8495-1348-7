<?php
//////////////////////////////////////////////////////////////////////////////////////////////////
// 
// Logic code for MaxiGallery. Default parameter values can be changed in this file.
// Do not touch anything unless you know what you are doing. 
// 
//////////////////////////////////////////////////////////////////////////////////////////////////

global $modx;

if (!defined ('MAXIGALLERY_PATH')){
	$output = 'MaxiGallery setup path is not defined, please check the snippet code in MODx manager.'; 
	return;
}

//Include a custom config file if specified
if (isset($config)) {
	$configFile = $modx->config['base_path'].MAXIGALLERY_PATH.'configs/'.$config.'.config.php';	
}
if (file_exists($configFile)) {
	include_once($configFile);
}

//language default value
$mgconfig['lang'] = (isset($lang)) ? $lang : "en"; // [ en | fi | da | es | it | nl | pt | sl | sv ] (you can add more by your self, see lang_en.php for example)

//include lang file
$langfile = $modx->config['base_path'].MAXIGALLERY_PATH.'lang/lang_'.$mgconfig['lang'].'.php';
if(file_exists($langfile)){
	include($langfile);
} else {
	$defaultlang = $modx->config['base_path'].MAXIGALLERY_PATH.'lang/lang_en.php';
	if (file_exists($defaultlang)){
		include($defaultlang);
	} else {
		$output = 'Default language file is missing!'; 
		return;
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// Default values (can be overridden in the snippet call), 
// No need to edit unless you know what you're doing!
//
$mgconfig['debug'] = (isset($debug)) ? $debug : 0; //[0 | 1] display debug trace 
$mgconfig['manager_webgroups'] = (isset($manager_webgroups)) ? explode(",",$manager_webgroups) : array(); // [ array ]
$mgconfig['manager_webusers'] = (isset($manager_webusers)) ? explode(",",$manager_webusers) : array(); // [ array ]
$mgconfig['is_target'] = (isset($is_target)) ? $is_target : false; // [ true | false ]
$mgconfig['picture_target'] = (isset($picture_target)) ? $picture_target : ""; // [ number | empty ]
$mgconfig['manage_target'] = (isset($manage_target)) ? $manage_target : ""; // [ number | empty ]
$mgconfig['manage_gallery'] = (isset($manage_gallery)) ? $manage_gallery : ""; // [ number | empty ]
$mgconfig['view_gallery'] = (isset($view_gallery)) ? $view_gallery : ""; // [ number | empty ]
$mgconfig['pics_per_page'] = (isset($pics_per_page)) ? $pics_per_page : 0; // [ number | 0 for unlimited number ]
$mgconfig['pics_per_row'] = (isset($pics_per_row)) ? $pics_per_row : 0; // [ number | 0 for no auto break ]
$mgconfig['order_by'] = (isset($order_by)) ? $order_by : "pos,date"; // [ date | id | filename | title | pos | random ] 
$mgconfig['order_direction'] = (isset($order_direction)) ? $order_direction : "DESC"; // [ ASC | DESC ] 
$mgconfig['gtable'] = (isset($gtable)) ? $gtable : "maxigallery"; // [ text ] 
$mgconfig['display'] = (isset($display)) ? $display : "normal"; // [ normal | embedded | childgalleries | pictureview ]
$mgconfig['childgalleries_level_limit'] = (isset($childgalleries_level_limit)) ? $childgalleries_level_limit : 0; // [ number ]
$mgconfig['embedtype'] = (isset($embedtype)) ? $embedtype : ""; // [ slidebox | lightboxv2 | slimbox | smoothgallery | popup | external ]
$mgconfig['max_thumb_size'] = (isset($max_thumb_size)) ? $max_thumb_size : 130; // [ number for autosize | widthxheight to set biggest width and height for thumbnail ] 
$mgconfig['max_pic_size'] = (isset($max_pic_size)) ? $max_pic_size : 450; // [ number for autosize | widthxheight to set biggest width and height for thumbnail | zero for unlimited size ] 
$mgconfig['max_pic_number'] = (isset($max_pic_number)) ? $max_pic_number : 0; // [ number | 0 for unlimited number ] 
$mgconfig['quality_thumb'] = (isset($quality_thumb)) ? $quality_thumb : 70; // [ number between 0-100 ] 
$mgconfig['quality_pic'] = (isset($quality_pic)) ? $quality_pic : 70; // [ number between 0-100 ] 
$mgconfig['keep_bigimg'] = (isset($keep_bigimg)) ? $keep_bigimg : false; // [ true | false ] 
$mgconfig['max_big_size'] = (isset($max_big_size)) ? $max_big_size : 1024; // [ number for autosize | widthxheight to set biggest width and height for thumbnail | zero for unlimited size ] 
$mgconfig['quality_big'] = (isset($quality_big)) ? $quality_big : 100; // [ number between 0-100 ] 
$mgconfig['big_img_linkstyle'] = (isset($big_img_linkstyle)) ? $big_img_linkstyle : "external"; // [ slidebox | lightboxv2 | popup | external ]
$mgconfig['keep_date'] = (isset($keep_date)) ? $keep_date : true; // [true | false ] 
$mgconfig['disable_rightclick'] = (isset($disable_rightclick)) ? $disable_rightclick : false; // [ true | false ]
$mgconfig['thumb_use_watermark'] = (isset($thumb_use_watermark)) ? $thumb_use_watermark : false; // [ true | false ]
$mgconfig['thumb_watermark_txt'] = (isset($thumb_watermark_txt)) ? $thumb_watermark_txt : "Copyright ".date("Y"); // [ text ]
$mgconfig['thumb_watermark_txt_color'] = (isset($thumb_watermark_txt_color)) ? $thumb_watermark_txt_color : "FFFFFF";	// [ RGB Hexadecimal ]
$mgconfig['thumb_watermark_font'] = (isset($thumb_watermark_font)) ? $thumb_watermark_font : 1; // [ 1 | 2 | 3 | 4 | 5 ]
$mgconfig['thumb_watermark_txt_vmargin'] = (isset($thumb_watermark_txt_vmargin)) ? $thumb_watermark_txt_vmargin : 2; // [ number ]
$mgconfig['thumb_watermark_txt_hmargin'] = (isset($thumb_watermark_txt_hmargin)) ? $thumb_watermark_txt_hmargin : 2;	// [ number ]
$mgconfig['thumb_watermark_img'] = (isset($thumb_watermark_img)) ? $thumb_watermark_img : MAXIGALLERY_PATH.'watermark/watermark.png'; //path 
$mgconfig['thumb_watermark_type'] = (isset($thumb_watermark_type)) ? $thumb_watermark_type : "text"; // [ text | image ]
$mgconfig['thumb_watermark_valign'] = (isset($thumb_watermark_valign)) ? $thumb_watermark_valign : "bottom"; // [ top | center | bottom ]
$mgconfig['thumb_watermark_halign'] = (isset($thumb_watermark_halign)) ? $thumb_watermark_halign : "right"; // [ left | center | right ]
$mgconfig['pic_use_watermark'] = (isset($pic_use_watermark)) ? $pic_use_watermark : false; // [ true | false ]
$mgconfig['pic_watermark_txt'] = (isset($pic_watermark_txt)) ? $pic_watermark_txt : "Copyright ".date("Y")." ".$modx->config['site_name']; // [ text ]
$mgconfig['pic_watermark_txt_color'] = (isset($pic_watermark_txt_color)) ? $pic_watermark_txt_color : "FFFFFF";	// [ RGB Hexadecimal ]
$mgconfig['pic_watermark_font'] = (isset($pic_watermark_font)) ? $pic_watermark_font : 3; // [ 1 | 2 | 3 | 4 | 5 ]
$mgconfig['pic_watermark_txt_vmargin'] = (isset($pic_watermark_txt_vmargin)) ? $pic_watermark_txt_vmargin : 10; // [ number ]
$mgconfig['pic_watermark_txt_hmargin'] = (isset($pic_watermark_txt_hmargin)) ? $pic_watermark_txt_hmargin : 10;	// [ number ]
$mgconfig['pic_watermark_img'] = (isset($pic_watermark_img)) ? $pic_watermark_img : MAXIGALLERY_PATH.'watermark/watermark.png'; //path 
$mgconfig['pic_watermark_type'] = (isset($pic_watermark_type)) ? $pic_watermark_type : "text"; // [ text | image ]
$mgconfig['pic_watermark_valign'] = (isset($pic_watermark_valign)) ? $pic_watermark_valign : "bottom"; // [ top | center | bottom ]
$mgconfig['pic_watermark_halign'] = (isset($pic_watermark_halign)) ? $pic_watermark_halign : "right"; // [ left | center | right ]
$mgconfig['big_use_watermark'] = (isset($big_use_watermark)) ? $big_use_watermark : false; // [ true | false ]
$mgconfig['big_watermark_txt'] = (isset($big_watermark_txt)) ? $big_watermark_txt : "Copyright ".date("Y")." ".$modx->config['site_name']; // [ text ]
$mgconfig['big_watermark_txt_color'] = (isset($big_watermark_txt_color)) ? $big_watermark_txt_color : "FFFFFF";	// [ RGB Hexadecimal ]
$mgconfig['big_watermark_font'] = (isset($big_watermark_font)) ? $big_watermark_font : 5; // [ 1 | 2 | 3 | 4 | 5 ]
$mgconfig['big_watermark_txt_vmargin'] = (isset($big_watermark_txt_vmargin)) ? $big_watermark_txt_vmargin : 15; // [ number ]
$mgconfig['big_watermark_txt_hmargin'] = (isset($big_watermark_txt_hmargin)) ? $big_watermark_txt_hmargin : 15;	// [ number ]
$mgconfig['big_watermark_img'] = (isset($big_watermark_img)) ? $big_watermark_img : MAXIGALLERY_PATH.'watermark/watermark.png'; // [ path ]
$mgconfig['big_watermark_type'] = (isset($big_watermark_type)) ? $big_watermark_type : "text"; // [ text | image ]
$mgconfig['big_watermark_valign'] = (isset($big_watermark_valign)) ? $big_watermark_valign : "bottom"; // [ top | center | bottom ]
$mgconfig['big_watermark_halign'] = (isset($big_watermark_halign)) ? $big_watermark_halign : "right"; // [ left | center | right ]
$mgconfig['thumb_use_dropshadow'] = (isset($thumb_use_dropshadow)) ? $thumb_use_dropshadow : false; // [ true | false ] 
$mgconfig['thumb_shadow_bgcolor'] = (isset($thumb_shadow_bgcolor)) ? $thumb_shadow_bgcolor : "FFFFFF"; // [ RGB Hexadecimal ]
$mgconfig['thumb_shadow_path'] = (isset($thumb_shadow_path)) ? $thumb_shadow_path : MAXIGALLERY_PATH.'dropshadow/'; // [ path ]
$mgconfig['pic_use_dropshadow'] = (isset($pic_use_dropshadow)) ? $pic_use_dropshadow : false; // [ true | false ]
$mgconfig['pic_shadow_bgcolor'] = (isset($pic_shadow_bgcolor)) ? $pic_shadow_bgcolor : "FFFFFF"; // [ RGB Hexadecimal ] 
$mgconfig['pic_shadow_path'] = (isset($pic_shadow_path)) ? $pic_shadow_path : MAXIGALLERY_PATH.'dropshadow/'; // [ path ]
$mgconfig['big_use_dropshadow'] = (isset($big_use_dropshadow)) ? $big_use_dropshadow : false; // [ true | false ]
$mgconfig['big_shadow_bgcolor'] = (isset($big_shadow_bgcolor)) ? $big_shadow_bgcolor : "FFFFFF"; // [ RGB Hexadecimal ] 
$mgconfig['big_shadow_path'] = (isset($big_shadow_path)) ? $big_shadow_path : MAXIGALLERY_PATH.'dropshadow/'; // [ path ]
$mgconfig['thumb_use_imagemask'] = (isset($thumb_use_imagemask)) ? $thumb_use_imagemask : false; // [ true | false ]
$mgconfig['thumb_mask_bgcolor'] = (isset($thumb_mask_bgcolor)) ? $thumb_mask_bgcolor : "FFFFFF"; // [ RGB Hexadecimal ]
$mgconfig['thumb_mask_position'] = (isset($thumb_mask_position)) ? $thumb_mask_position : "resize"; // [ top | topleft | topright | left | center | right | bottom | bottomleft | bottomright | resize ]
$mgconfig['thumb_mask_img'] = (isset($thumb_mask_img)) ? $thumb_mask_img : MAXIGALLERY_PATH.'imagemask/demomask-frame1.png'; // [ path ]
$mgconfig['pic_use_imagemask'] = (isset($pic_use_imagemask)) ? $pic_use_imagemask : false; // [ true | false ]
$mgconfig['pic_mask_bgcolor'] = (isset($pic_mask_bgcolor)) ? $pic_mask_bgcolor : "FFFFFF"; // [ RGB Hexadecimal ]
$mgconfig['pic_mask_position'] = (isset($pic_mask_position)) ? $pic_mask_position : "resize"; // [ top | topleft | topright | left | center | right | bottom | bottomleft | bottomright | resize ]
$mgconfig['pic_mask_img'] = (isset($pic_mask_img)) ? $pic_mask_img : MAXIGALLERY_PATH.'imagemask/demomask-frame2.png'; // [ path ]
$mgconfig['big_use_imagemask'] = (isset($big_use_imagemask)) ? $big_use_imagemask : false; // [ true | false ]
$mgconfig['big_mask_bgcolor'] = (isset($big_mask_bgcolor)) ? $big_mask_bgcolor : "FFFFFF"; // [ RGB Hexadecimal ]
$mgconfig['big_mask_position'] = (isset($big_mask_position)) ? $big_mask_position : "resize"; // [ top | topleft | topright | left | center | right | bottom | bottomleft | bottomright | resize ]
$mgconfig['big_mask_img'] = (isset($big_mask_img)) ? $big_mask_img : MAXIGALLERY_PATH.'imagemask/demomask-frame2.png'; // [ path ]

// new in v05
$mgconfig['manageButtonTpl'] = (isset($manageButtonTpl)) ? $manageButtonTpl : '@FILE:'.MAXIGALLERY_PATH.'templates/managebuttontpl.html'; // [ path | chunkname | text ]
$mgconfig['manageOuterTpl'] = (isset($manageOuterTpl)) ? $manageOuterTpl : '@FILE:'.MAXIGALLERY_PATH.'templates/manageoutertpl.html'; // [ path | chunkname | text ]
$mgconfig['managePictureTpl'] = (isset($managePictureTpl)) ? $managePictureTpl : '@FILE:'.MAXIGALLERY_PATH.'templates/managepicturetpl.html'; // [ path | chunkname | text ]
$mgconfig['manageUploadTpl'] = (isset($manageUploadTpl)) ? $manageUploadTpl : '@FILE:'.MAXIGALLERY_PATH.'templates/manageuploadtpl.html'; // [ path | chunkname | text ]
$mgconfig['galleryOuterTpl'] = (isset($galleryOuterTpl)) ? $galleryOuterTpl : '@FILE:'.MAXIGALLERY_PATH.'templates/galleryoutertpl.html'; // [ path | chunkname | text ]
$mgconfig['galleryPictureTpl'] = (isset($galleryPictureTpl)) ? $galleryPictureTpl : '@FILE:'.MAXIGALLERY_PATH.'templates/gallerypicturetpl.html'; // [ path | chunkname | text ]
$mgconfig['childgalleryTpl'] = (isset($childgalleryTpl)) ? $childgalleryTpl : '@FILE:'.MAXIGALLERY_PATH.'templates/childgallerytpl.html'; // [ path | chunkname | text ]
$mgconfig['pictureTpl'] = (isset($pictureTpl)) ? $pictureTpl : '@FILE:'.MAXIGALLERY_PATH.'templates/picturetpl.html'; // [ path | chunkname | text ]
$mgconfig['clearerTpl'] = (isset($clearerTpl)) ? $clearerTpl : '@FILE:'.MAXIGALLERY_PATH.'templates/clearertpl.html'; // [ path | chunkname | text ]
$mgconfig['pageNumberTpl'] = (isset($pageNumberTpl)) ? $pageNumberTpl : '@FILE:'.MAXIGALLERY_PATH.'templates/pagenumbertpl.html'; // [ path | chunkname | text ]
$mgconfig['draggableTpl'] = (isset($draggableTpl)) ? $draggableTpl : '@FILE:'.MAXIGALLERY_PATH.'templates/draggabletpl.html'; // [ path | chunkname | text ]
$mgconfig['css'] = (isset($css)) ? $css : MAXIGALLERY_PATH.'css/default.css'; // [ text ]
$mgconfig['js'] = (isset($js)) ? $js : ""; // [ text ]
$mgconfig['smoothgallery_id'] = (isset($smoothgallery_id)) ? $smoothgallery_id : -1; // [ number ]
$mgconfig['smoothgallery_width'] = (isset($smoothgallery_width)) ? $smoothgallery_width : $mgconfig['max_pic_size']; // [ number ]
$mgconfig['smoothgallery_height'] = (isset($smoothgallery_height)) ? $smoothgallery_height : $mgconfig['max_pic_size']; // [ number ]
$mgconfig['smoothgallery_showArrows'] = (isset($smoothgallery_showArrows)) ? $smoothgallery_showArrows : "true"; // [ as string, true | false ]
$mgconfig['smoothgallery_showCarousel'] = (isset($smoothgallery_showCarousel)) ? $smoothgallery_showCarousel : "true"; // [ as string, true | false ]
$mgconfig['smoothgallery_showInfopane'] = (isset($smoothgallery_showInfopane)) ? $smoothgallery_showInfopane : "true"; // [ as string, true | false ]
$mgconfig['smoothgallery_thumbHeight'] = (isset($smoothgallery_thumbHeight)) ? $smoothgallery_thumbHeight : 75; // [ number ]
$mgconfig['smoothgallery_thumbWidth'] = (isset($smoothgallery_thumbWidth)) ? $smoothgallery_thumbWidth : 100; // [ number ]
$mgconfig['smoothgallery_thumbSpacing'] = (isset($smoothgallery_thumbSpacing)) ? $smoothgallery_thumbSpacing : 10; // [ number ]
$mgconfig['smoothgallery_embedLinks'] = (isset($smoothgallery_embedLinks)) ? $smoothgallery_embedLinks : "true"; // [ as string, true | false ]
$mgconfig['smoothgallery_fadeDuration'] = (isset($smoothgallery_fadeDuration)) ? $smoothgallery_fadeDuration : 500; // [ number ]
$mgconfig['smoothgallery_timed'] = (isset($smoothgallery_timed)) ? $smoothgallery_timed : "false"; // [ as string, true | false ]
$mgconfig['smoothgallery_delay'] = (isset($smoothgallery_delay)) ? $smoothgallery_delay : 9000; // [ number ]
$mgconfig['smoothgallery_preloader'] = (isset($smoothgallery_preloader)) ? $smoothgallery_preloader : "true"; // [ as string, true | false ]
$mgconfig['smoothgallery_slideInfoZoneOpacity'] = (isset($smoothgallery_slideInfoZoneOpacity)) ? $smoothgallery_slideInfoZoneOpacity : 0.7; // [ number ]
$mgconfig['smoothgallery_carouselMinimizedOpacity'] = (isset($smoothgallery_carouselMinimizedOpacity)) ? $smoothgallery_carouselMinimizedOpacity : 0.4; // [ number ]
$mgconfig['smoothgallery_carouselMinimizedHeight'] = (isset($smoothgallery_carouselMinimizedHeight)) ? $smoothgallery_carouselMinimizedHeight : 20; // [ number ]
$mgconfig['smoothgallery_carouselMaximizedOpacity'] = (isset($smoothgallery_carouselMaximizedOpacity)) ? $smoothgallery_carouselMaximizedOpacity : 0.7; // [ number ]
$mgconfig['smoothgallery_textShowCarousel'] = (isset($smoothgallery_textShowCarousel)) ? $smoothgallery_textShowCarousel : $strings['pictures'];
$mgconfig['admin_webgroups'] = (isset($admin_webgroups)) ? explode(",",$admin_webgroups) : array(); // [ array ]
$mgconfig['admin_webusers'] = (isset($admin_webusers)) ? explode(",",$admin_webusers) : array(); // [ array ]
$mgconfig['childgalleries_order_by'] = (isset($childgalleries_order_by)) ? $childgalleries_order_by : "menuindex"; // [ MODx Document Object field(s) ] 
$mgconfig['childgalleries_order_direction'] = (isset($childgalleries_order_direction)) ? $childgalleries_order_direction : "ASC"; // [ ASC | DESC ]
$mgconfig['childgalleries_ids'] = (isset($childgalleries_ids)) ? explode(",",$childgalleries_ids) : array($modx->documentIdentifier); // [ numbers | all ]
$mgconfig['childgalleries_limit'] = (isset($childgalleries_limit)) ? $childgalleries_limit : 0; // [ number ]
$mgconfig['offset'] = (isset($offset)) ? $offset : 0; // [ number ]
$mgconfig['limit'] = (isset($limit)) ? $limit : 9999999; // [ number ]
$mgconfig['random_filenames'] = (isset($random_filenames)) ? $random_filenames : false; // [ true | false ]
$mgconfig['disable_js_libs'] = (isset($disable_js_libs)) ? $disable_js_libs : false; // [ true | false ]
$mgconfig['use_ftp_commands'] = (isset($use_ftp_commands)) ? $use_ftp_commands : false; // [ true | false ]
$mgconfig['ftp_server'] = (isset($ftp_server)) ? $ftp_server : "ftp.yourserver.fi"; // [ text ]
$mgconfig['ftp_port'] = (isset($ftp_port)) ? $ftp_port : 21; // [ number ]
$mgconfig['ftp_user'] = (isset($ftp_user)) ? $ftp_user : "username"; // [ text ]
$mgconfig['ftp_pass'] = (isset($ftp_pass)) ? $ftp_pass : "password"; // [ text ]
$mgconfig['ftp_base_dir'] = (isset($ftp_base_dir)) ? $ftp_base_dir : "/"; // [ text ]
$mgconfig['gal_query_ids'] = (isset($gal_query_ids)) ? explode(",",$gal_query_ids) : array(); // [ numbers | all ]
$mgconfig['query_level_limit'] = (isset($query_level_limit)) ? $query_level_limit : 1; // [ number ]
$mgconfig['pic_query_ids'] = (isset($pic_query_ids)) ? explode(",",$pic_query_ids) : array(); // [ numbers | all ]
$mgconfig['pictureview_start_id'] = (isset($pictureview_start_id)) ? $pictureview_start_id : -1; // [ number ]
$mgconfig['pictureview_start_pos'] = (isset($pictureview_start_pos)) ? $pictureview_start_pos : -1; // [ number ]
$mgconfig['upload_field_count'] = (isset($upload_field_count)) ? $upload_field_count : 10; // [ number ]


///////////////////////////////////////////////////////////////////////////////////////////////////
//-------------------------------------------------------------------------------------------------
//  SNIPPET LOGIC CODE STARTS HERE
//-------------------------------------------------------------------------------------------------

// callback (pre) function for the zip extraction 
if (!function_exists('preZipExtract')) {
	function preZipExtract($p_event, &$p_header) {
		// add random number temporarily to file name in case there are already files with this name
		$info = pathinfo($p_header['filename']);
	    srand((double)microtime()*1000000); 
		$p_header['filename'] = $info['dirname'].'/'.rand(1000000,9000000).$info['basename'];
		return 1;
	}	
}

if (!class_exists('maxiGallery')) {
	$mgclass=$modx->config['base_path'].MAXIGALLERY_PATH.'maxigallery.class.inc.php';
	if(file_exists($mgclass)){
		include_once($mgclass);
	} else {
		$output = 'Cannot find maxigallery class file! ('.$mgclass.')'; 
		return;
	}
}

// Initialize class
if (class_exists('maxiGallery')) {
   $mg = new maxiGallery($mgconfig,$strings);
} else {
	$output =  'MaxiGallery class not found'; 
	return;
}

if (!class_exists('mgChunkie')) {
	$chunkieclass = $modx->config['base_path'].MAXIGALLERY_PATH.'chunkie/chunkie.class.inc.php';
	if (file_exists($chunkieclass)) {
		include_once $chunkieclass;
	} else {
		$output = 'Cannot find chunkie class file! ('.$chunkieclass.')'; 
		return;
	}
}

//Initialize variables
$mg->pics_tbl = $modx->db->config['dbase'].".".$modx->db->config['table_prefix'].$mg->mgconfig['gtable'];
$descvalid = 0; // assume descriptions not supported
$custposvalid = 0; // assume that custom image ordering not supported
if($mg->mgconfig['is_target']==true && $_REQUEST['gal_id']){
	$mg->pageinfo=$modx->getPageInfo($_REQUEST['gal_id'],0, "id, pagetitle, longtitle, description, alias, createdby");
}else if($mg->mgconfig['manage_gallery']!=""){
	$mg->pageinfo=$modx->getPageInfo($mg->mgconfig['manage_gallery'],0, "id, pagetitle, longtitle, description, alias, createdby");
}else if($mg->mgconfig['view_gallery']!=""){ 
	$mg->pageinfo=$modx->getPageInfo($mg->mgconfig['view_gallery'],0, "id, pagetitle, longtitle, description, alias, createdby");
}else{
	$mg->pageinfo=$modx->getPageInfo($modx->documentIdentifier,0, "id, pagetitle, longtitle, description, alias, createdby");
}
$mg->path_to_gal=$path_to_galleries.$mg->pageinfo['id']."/";

//validate gallery table
$query=mysql_query("DESC $mg->pics_tbl");
if(!$query) {
	if($mg->createTable()){
		$descvalid = 1; 
		$custposvalid = 1;
	}else{
		$output = $mg->strings['database_error'];
		return;
	}
} else {
	if($mg->isDescValid($query)) {
		$descvalid=1;
	}
	if($mg->isPosValid($query)) {
		$custposvalid=1;
	}
	if(!$mg->isOwnIDValid($query)) {
		$output = $mg->strings['database_error_field'].'own_id';
		return;
	}
	if(!$mg->isHideValid($query)) {
		$output = $mg->strings['database_error_ownid'].'hide';
		return;
	}	
}

//if dragsort requested, clear output buffers and output dragsort html
if ((isset($_REQUEST['dragsort']) && $_REQUEST['dragsort'] == 1) 
		&& ($mg->checkPermissions($_SESSION['mgrInternalKey'],$mg->pageinfo['id']))){
	//clean buffer
	while (@ob_end_clean()) {}
	//render template
	$tpl = new mgChunkie($mg->mgconfig['draggableTpl']);
	$draggableTplData = array();
	$draggableTplData['path'] = MAXIGALLERY_PATH;
	$draggableTplData['path_to_gal'] = $mg->path_to_gal;
	$draggableTplData['pageinfo'] = $mg->pageinfo;
	$draggableTplData['strings'] = $mg->strings;
	$draggableTplData['config'] = $mg->mgconfig;
	$tpl->addVar('maxigallery', $draggableTplData);
	echo $tpl->Render();
	exit();
}

//manage pictures
if($mg->checkPermissions($_SESSION['mgrInternalKey'],$mg->pageinfo['id'])) {
	if($_REQUEST['mode']=="admin" || ($mg->mgconfig['manage_gallery']!="" && $mg->mgconfig['manage_target']=="")) {
		// array to store the data for tpl
		$manageOuterTplData = array('messages' => '');
		
		// --- Add by Marc:
		if ($mg->mgconfig['debug']) echo"Debug >> ".__LINE__." - //if user is allowed to modify and has entered admin mode<br />\n";
		if ($mg->mgconfig['debug']) echo"|--- Action: ".$_REQUEST['action']."<br />\n";
		if ($mg->mgconfig['debug']) echo"|--- From_id: ".$_REQUEST['from_id']."<br />\n";
		if ($mg->mgconfig['debug']) echo"|--- Gal_id: ".$_REQUEST['gal_id']."<br />\n";
		if ($mg->mgconfig['debug']) echo"|--- max_pic_number: ".$mg->mgconfig['max_pic_number']."<br />\n";
		if ($mg->mgconfig['debug']) echo"|--- display: ".$mg->mgconfig['display']."<br />\n";
		if ($mg->mgconfig['debug']) echo"|--- embedtype: ".$mg->mgconfig['embedtype']."<br />\n";
		// --- End add by Marc
		
		//if user is allowed to modify and has entered admin mode:
		if($descvalid==0) {
			$manageOuterTplData['messages'] .= $mg->strings['db_no_descr_support'];
		}
		if($custposvalid==0) {
			$manageOuterTplData['messages'] .= $mg->strings['db_no_pos_support'];
		}
		//processors
		if($_REQUEST['action']=='upload_pics') { //if "upload pictures" has been used
			if(!file_exists($mg->path_to_gal)) {
				if(!$mg->mgconfig['use_ftp_commands']) {    
					$old_umask = umask(0);
					if(!mkdir($mg->path_to_gal, 0777)) {
						$output = 'Directory creation failed!'; 
						return;
					}
					umask($old_umask);
				} else {
					$connect = ftp_connect($mg->mgconfig['ftp_server'], $mg->mgconfig['ftp_port']);
					if (!$connect) {
						$output = 'Connection to FTP failed.'; 
						return;
					}
					$login = ftp_login($connect, $mg->mgconfig['ftp_user'], $mg->mgconfig['ftp_pass']);
					if (!$login) {
						$output = 'Could not login to FTP.'; 
						return;	
					}
					$changeDir = ftp_chdir($connect, $mg->mgconfig['ftp_base_dir'].$path_to_galleries);
					if (!$changeDir) {
						$output = 'Could not change directory to: '.$mg->mgconfig['ftp_base_dir'].$path_to_galleries;
						return;
					}
					$makeDir = ftp_mkdir($connect, $mg->pageinfo['id']);
					if (!$makeDir) {
						$output = 'Could not created directory.';
						return;
					}
					$old_umask = umask(0);
					$setPerm = ftp_site($connect, 'CHMOD 0777 /'.$path_to_galleries.$mg->pageinfo['id'].'/');
					if (!$setPerm) {
						$output = 'Could not set permissions: '.'CHMOD 0777 /'.$path_to_galleries.$mg->pageinfo['id'].'/';
					}
					umask($old_umask);
					ftp_close($connect);
				} 
			}
			//if max picture limit is set, get current pictures in table
			if($mg->mgconfig['max_pic_number']!=0){
				//if random order, sort by date,pos in picture management
				$orderby = $mg->mgconfig['order_by'];
				if ($mg->mgconfig['order_by'] == "random") {
					$orderby = "date,pos";
				}
				$rsx=$modx->db->query("SELECT * FROM " . $mg->pics_tbl . " WHERE gal_id='" . $mg->pageinfo['id'] . "' ORDER BY " . $orderby . " " . $mg->mgconfig['order_direction']);
			}
			$upload_error = false;
			for($i=0;$i<$mg->mgconfig['upload_field_count'];$i++) { //for each of the upload fields:
				$name=$_FILES['file'.$i]['name'];
				
				//if max number of pics limit has been reached, break
				if($mg->mgconfig['max_pic_number']!=0 && $name != "" && ($mg->mgconfig['max_pic_number'] < (($i+1) + $modx->db->getRecordCount($rsx)))){
					$upload_error = true;
					$manageOuterTplData['messages'] .= $mg->strings['max_pics_reached_some_discarded'];
					break;
				}
				
				if($name!="") {
					$name = $mg->getFilename($name);
					
					move_uploaded_file( $_FILES['file'.$i]['tmp_name'] , $mg->path_to_gal.$name );
					chmod($mg->path_to_gal.$name,0666);
					
					$handleMessage = $mg->handleFile($name, $modx->db->getRecordCount($rsx));
					if ($handleMessage != "") {
						$manageOuterTplData['messages'] .= $handleMessage;
						$upload_error = true;
					}
					
				}
			}
			if(!$upload_error){
				$manageOuterTplData['messages'] .= $mg->strings['pictures_successfully_uploaded'];
			}
			//if display tyle is embedded and slidebox in use, create/update gallery xml
			if($mg->mgconfig['display']=="embedded" && $mg->mgconfig['embedtype']=="slidebox"){
				$mg->createGalleryXML();
			}
		}

		if($_REQUEST['action']=='edit_pics' || $_REQUEST['action']=='delete_pics' || $_REQUEST['action']=='gallery_synch') {   
			if($_REQUEST['action']=='edit_pics') { // If "save changes" has been used
				for($i=0;$i<$_REQUEST['number'];$i++) {
					if($_REQUEST['delete'.$i]=='yes') {
						$rs0=$modx->db->query("SELECT id,filename FROM $mg->pics_tbl WHERE id='" . $_REQUEST['pic_id'.$i] . "'");
						$deletepic=$modx->fetchRow($rs0);
						if($deletepic['filename'] != "") {
							if(file_exists($mg->path_to_gal.$deletepic['filename'])) 
								unlink($mg->path_to_gal.$deletepic['filename']);
							if(file_exists($mg->path_to_gal."tn_".$deletepic['filename'])) 
								unlink($mg->path_to_gal."tn_".$deletepic['filename']);
							if(file_exists($mg->path_to_gal."big_".$deletepic['filename'])) 
								unlink($mg->path_to_gal."big_".$deletepic['filename']);
						}
						$rs1=$modx->db->query("DELETE FROM $mg->pics_tbl WHERE id='" . $_REQUEST['pic_id'.$i] . "'");
					}
					if($_REQUEST['modified'.$i]=='yes') {
						// restructured for clarity and extended MF oct2005
						$updateQueryString= "UPDATE ".$mg->pics_tbl." SET ";
						$updateQueryString.= "title='".addslashes($_REQUEST['title'.$i])."'"; //add title content
						if(!$mg->mgconfig['keep_date']) 
							$updateQueryString.=",date=NOW()";
						if($descvalid==1) 
							$updateQueryString.=", descr='".addslashes($_REQUEST['descr'.$i])."'"; //MF add descr content
						if($custposvalid==1 && is_numeric($_REQUEST['pos'.$i]))
							$updateQueryString.=", pos=".$_REQUEST['pos'.$i]; //add pos value
						if ($_REQUEST['hide'.$i]=="yes") {
							$updateQueryString.=", hide='1'";
						} else {
							$updateQueryString.=", hide='0'";
						} 
						$updateQueryString.=" WHERE id='" .$_REQUEST['pic_id'.$i]."'";
						$rs3=$modx->db->query( $updateQueryString );
					}
				}
				$manageOuterTplData['messages'] .= $mg->strings['changes_have_been_saved'];
			}
			else if($_REQUEST['action']=='delete_pics') {    // If "Delete all pictures" has been used
				//if random order, sort by date,pos in picture management
				$orderby = $mg->mgconfig['order_by'];
				if ($mg->mgconfig['order_by'] == "random") {
					$orderby = "date,pos";
				}
				//if logged from backend and rights to edit this page or logged from front end and belongs to admin webgroups or webusers, get all pics	
				if(($_SESSION['mgrValidated'] && $_SESSION['mgrPermissions']['edit_document']) || (count($mg->mgconfig['admin_webgroups'])>0 && $modx->isMemberOfWebGroup($mg->mgconfig['admin_webgroups'])) || ($modx->getLoginUserName()!="" && in_array($modx->getLoginUserName(), $mg->mgconfig['admin_webusers']))){
					$sql = "SELECT * FROM " . $mg->pics_tbl . " WHERE gal_id='" . $mg->pageinfo['id'] . "' ORDER BY " . $orderby . " " . $mg->mgconfig['order_direction'];
				}else{
					$sql = "SELECT * FROM " . $mg->pics_tbl . " WHERE gal_id='" . $mg->pageinfo['id'] . "' AND own_id='" . $modx->getLoginUserID() . "' ORDER BY " . $orderby . " " . $mg->mgconfig['order_direction'];
				}
				$rs1=$modx->db->query($sql);
				
				while($deletepic=$modx->fetchRow($rs1)) {
					$file = $mg->path_to_gal.$pic['filename'];
					if(file_exists($mg->path_to_gal.$deletepic['filename'])) 
						unlink($mg->path_to_gal.$deletepic['filename']);
					if(file_exists($mg->path_to_gal."tn_".$deletepic['filename'])) 
						unlink($mg->path_to_gal."tn_".$deletepic['filename']);
					if(file_exists($mg->path_to_gal."big_".$deletepic['filename'])) 
						unlink($mg->path_to_gal."big_".$deletepic['filename']);
					$rsx=$modx->db->query("DELETE FROM $mg->pics_tbl WHERE id='" . $deletepic['id'] . "'");
				}
				$manageOuterTplData['messages'] .= $mg->strings['changes_have_been_saved'];
			}
			else if($_REQUEST['action']=='gallery_synch' && is_dir($mg->path_to_gal)) { //if "resynch gallery" has been used
				//get current pictures in table
				//if random order, sort by date,pos in picture management
				$orderby = $mg->mgconfig['order_by'];
				if ($mg->mgconfig['order_by'] == "random") {
					$orderby = "date,pos";
				}
				$rsx=$modx->db->query("SELECT * FROM " . $mg->pics_tbl . " WHERE gal_id='" . $mg->pageinfo['id'] . "' ORDER BY " . $orderby . " " . $mg->mgconfig['order_direction']);

				// Store files currently in gallery
				$filesExist = array();
				while ($row = $modx->db->getRow($rsx)) {
					$filesExist[$row['filename']] = true;
				}

				$upload_error = false;

				$dh = opendir($mg->path_to_gal);
				while ($name = readdir($dh)) { //for each file in the gallery directory
					//if max number of pics limit has been reached, break
					if($mg->mgconfig['max_pic_number']!=0 && ($mg->mgconfig['max_pic_number'] <= ($i + $modx->db->getRecordCount($rsx)))){
						$upload_error = true;
						$manageOuterTplData['messages'] .= $mg->strings['max_pics_reached_some_discarded'];
						break;
					}

					if ($name != '..' && $name != '.' && strpos($name, 'tn_') !== 0 && !isset($filesExist[$name])) {
						$foundNewPics = true;
						//handle image in gallery folder
						$newname = $mg->getFilename($name);
  						if(rename($mg->path_to_gal.$name, $mg->path_to_gal.$newname)){
  							$handleMessage = $mg->handleFile($newname, $modx->db->getRecordCount($rsx));
  						} else {
  							$handleMessage = $mg->handleFile($name, $modx->db->getRecordCount($rsx));	
  						}
						if ($handleMessage != "") {
							$manageOuterTplData['messages'] .= $handleMessage;
							$upload_error = true;
						}					
					}
				}
				closedir($dh);

				if(!$upload_error && $foundNewPics){
					$manageOuterTplData['messages'] .= $mg->strings['pictures_successfully_uploaded'];
				}
				//if display tyle is embedded and slidebox in use, create/update gallery xml
				if($mg->mgconfig['display']=="embedded" && $mg->mgconfig['embedtype']=="slidebox"){
					$mg->createGalleryXML();
				}
				$manageOuterTplData['messages'] .= $mg->strings['gallery_resynched'];
			} // end if "resynch gallery"
			//if display style is embedded and slidebox in use, create/update gallery xml
			if($mg->mgconfig['display']=="embedded" && $mg->mgconfig['embedtype']=="slidebox"){
				$mg->createGalleryXML();
			}else if(file_exists($mg->path_to_gal."gallery.xml")){ //if gallery xml exists but not in use, delete it
				unlink($mg->path_to_gal."gallery.xml");
			}
			//if random order, sort by date,pos in picture management
			$orderby = $mg->mgconfig['order_by'];
			if ($mg->mgconfig['order_by'] == "random") {
				$orderby = "date,pos";
			}
			//remove gallery directory if all images have been removed
			$res=$modx->db->query("SELECT * FROM " . $mg->pics_tbl . " WHERE gal_id='" . $mg->pageinfo['id'] . "' ORDER BY " . $orderby . " " . $mg->mgconfig['order_direction']);
			$totalpics = $modx->db->getRecordCount($res);
			if($totalpics == 0 && file_exists($mg->path_to_gal)){
				$mg->deldir($mg->path_to_gal);
			}
		}
		

		// Create manage template data 
		// Edit gallery and its pictures
		$urparams = "";
		if($mg->mgconfig['is_target']==true && $_REQUEST['gal_id'] ){
			$urparams = "gal_id=".$_REQUEST['gal_id'];
			$backtonormal = $modx->makeUrl($_REQUEST['gal_id'], '', '');
		}else if($mg->mgconfig['manage_gallery']!=""){
			$backtonormal = $modx->makeUrl($mg->mgconfig['manage_gallery'], '', '');
		}else if($_REQUEST['from_id']){
			$urparams = "from_id=".$_REQUEST['from_id'];
			$backtonormal = $modx->makeUrl($_REQUEST['from_id'], '', '');
		}else{
			$backtonormal = $modx->makeUrl($modx->documentIdentifier, '','');
		}
		
		$manageOuterTplData['urlback'] = $backtonormal; 
		$manageOuterTplData['urlaction'] = $modx->makeUrl($modx->documentIdentifier, '', $urparams);
		$manageOuterTplData['urldragsort'] = $modx->makeUrl($modx->documentIdentifier, '', $urparams.'&dragsort=1');
		
		//if random order, sort by date,pos in picture management
		$orderby = $mg->mgconfig['order_by'];
		if ($mg->mgconfig['order_by'] == "random") {
			$orderby = "date,pos";
		}
				
		//if logged from backend and rights to edit this page or logged from front end and belongs to admin webgroups or webusers, get all pics	
		if(($_SESSION['mgrValidated'] && $_SESSION['mgrPermissions']['edit_document']) || (count($mg->mgconfig['admin_webgroups'])>0 && $modx->isMemberOfWebGroup($mg->mgconfig['admin_webgroups'])) || ($modx->getLoginUserName()!="" && in_array($modx->getLoginUserName(), $mg->mgconfig['admin_webusers']))){
			$sql = "SELECT * FROM " . $mg->pics_tbl . " WHERE gal_id='" . $mg->pageinfo['id'] . "' ORDER BY " . $orderby . " " . $mg->mgconfig['order_direction'];
		}else{
			$sql = "SELECT * FROM " . $mg->pics_tbl . " WHERE gal_id='" . $mg->pageinfo['id'] . "' AND own_id='" . $modx->getLoginUserID() . "' ORDER BY " . $orderby . " " . $mg->mgconfig['order_direction'];
		}
		$rs1=$modx->db->query($sql);
		$i=0;
		
		$manageOuterTplData['managepictures'] = '';
		
		while($pic=$modx->fetchRow($rs1)) {
			$file = $mg->path_to_gal.$pic['filename'];
			$tn_file = $mg->path_to_gal . "tn_" . $pic['filename'];
			
			$tpl = new mgChunkie($mg->mgconfig['managePictureTpl']);
			
			$managePictureTplData = array();
			
			$pic['title'] = stripslashes($pic['title']);
			$pic['descr'] = stripslashes($pic['descr']);
			$managePictureTplData['picture'] = $pic;
			$managePictureTplData['path_to_gal'] = $mg->path_to_gal;
			
			$managePictureTplData['strings'] = $mg->strings;
			
			$managePictureTplFieldNameData = array();
			
			$managePictureTplFieldNameData['delete'] = 'delete'.$i;
			$managePictureTplFieldNameData['hide'] = 'hide'.$i;
			$managePictureTplFieldNameData['position'] = 'pos'.$i;
			$managePictureTplFieldNameData['title'] = 'title'.$i;
			$managePictureTplFieldNameData['pictureid'] = 'pic_id'.$i;
			$managePictureTplFieldNameData['modified'] = 'modified'.$i;
			$managePictureTplFieldNameData['description'] = 'descr'.$i;
			
			$managePictureTplData['fieldnames'] = $managePictureTplFieldNameData; 
			
			$managePictureTplData['config'] = $mg->mgconfig;
			
			$tpl->addVar('maxigallery', $managePictureTplData);
			
			$manageOuterTplData['managepictures'] .= $tpl->Render();
			
			$i++;
			
		}
		
		$outerTplDataHiddenFields['mainform'] = '<input type="hidden" name="action" value="edit_pics" /><input type="hidden" name="mode" value="admin" /><input type="hidden" name="number" value="'.$i.'" />';
		$outerTplDataHiddenFields['deleteform'] = '<input type="hidden" name="action" value="delete_pics" /><input type="hidden" name="mode" value="admin" /><input type="hidden" name="number" value="'.$i.'" />';
		
		//check that max limit of pictures have not been reached, if it's set
		if(!isset($mg->mgconfig['max_pic_number']) || $mg->mgconfig['max_pic_number']==0 || $mg->mgconfig['max_pic_number']>$modx->db->getRecordCount($rs1)){
			$outerTplDataHiddenFields['uploadform'] = '<input type="hidden" name="action" value="upload_pics" /><input type="hidden" name="mode" value="admin" />';
			if(isset($mg->mgconfig['max_pic_number']) && $mg->mgconfig['max_pic_number']!=0) {
				$uploadCount = $mg->mgconfig['max_pic_number'] - $modx->db->getRecordCount($rs1);
			} else {
				$uploadCount = $mg->mgconfig['upload_field_count'];
			}
			for($i=0;$i<$uploadCount&&$i<$mg->mgconfig['upload_field_count'];$i++) {
				$tpl = new mgChunkie($mg->mgconfig['manageUploadTpl']);
				$uploadPictureTplData = array();
				
				$uploadPictureTplData['counter'] = $i+1;
				$uploadPictureTplData['fieldnames.file'] = 'file'.$i;
				$uploadPictureTplData['config'] = $mg->mgconfig;
				
				$tpl->addVar('maxigallery', $uploadPictureTplData);
			
				$manageOuterTplData['uploadpictures'] .= $tpl->Render();
							
			}
			
		}else {
			$manageOuterTplData['messages'] .= $mg->strings['max_pics_reached'];
		}
		
		//if max pictures limit is set, do a placeholder to show how many pictures are available to be uploaded
		if(isset($mg->mgconfig['max_pic_number']) && $mg->mgconfig['max_pic_number']!=0) {
			$manageOuterTplData['pics_allowed_count'] = $mg->mgconfig['max_pic_number']-$modx->db->getRecordCount($rs1);
		} else {
			$manageOuterTplData['pics_allowed_count'] = "";
		}
		
		$manageOuterTplData['hiddenfields'] = $outerTplDataHiddenFields;
		
		//Construct chunkie with the manager template
		$tpl = new mgChunkie($mg->mgconfig['manageOuterTpl']);
		//add data to the template
		$manageOuterTplData['pageinfo'] = $mg->pageinfo;
		$manageOuterTplData['strings'] = $mg->strings;
		$manageOuterTplData['config'] = $mg->mgconfig;
		$tpl->addVar('maxigallery', $manageOuterTplData);
		//render template and return output
		$output = $tpl->Render();
		$mg->regSnippetScriptsAndCSS();
		//register draggable picture sorting script
		$modx->regClientStartupScript($modx->config['base_url'] . MAXIGALLERY_PATH . 'js/draggableReorder.js');
		$modx->regClientStartupScript($modx->config['base_url'] . MAXIGALLERY_PATH . 'js/mooSortables.js');
		return;
		
	}
}

$outerTplData = array();
$pictureTplData = array();

if($mg->checkPermissions($_SESSION['mgrInternalKey'],$mg->pageinfo['id']) && $mg->mgconfig['is_target']==false) {
	if(isset($mg->mgconfig['manage_target']) && $mg->mgconfig['manage_target'] != "") {
		$formaction= $modx->makeUrl($mg->mgconfig['manage_target'], '', "gal_id=".$mg->pageinfo['id']);
	} else if(isset($mg->mgconfig['view_gallery']) && $mg->mgconfig['view_gallery'] != "") {
		$formaction= $modx->makeUrl($mg->mgconfig['view_gallery'], '', "from_id=".$modx->documentIdentifier);
	} else{
		$formaction= $modx->makeUrl($modx->documentIdentifier, '', '');
	} 
	// if not in custom query mode, make manage button available
	if (count($mg->mgconfig['gal_query_ids']) == 0 && count($mg->mgconfig['pic_query_ids']) == 0) {
		$buttonTplData = array();
		$buttonTplData['urlaction'] = $formaction;
		$buttonTplData['hiddenfields'] = '<input type="hidden" name="mode" value="admin" />';
		$buttonTplData['strings'] = $mg->strings;
		$buttonTplData['config'] = $mg->mgconfig;
		$tpl = new mgChunkie($mg->mgconfig['manageButtonTpl']);
		$tpl->addVar('maxigallery', $buttonTplData);		
		$outerTplData['managebutton'] = $tpl->Render();
		$pictureTplData['managebutton'] = $tpl->Render();
	}	
}


//-------------------------------------------------------------------------------------------------
// Single picture
//-------------------------------------------------------------------------------------------------

if ($_REQUEST['pic'] || $mg->mgconfig['display'] == 'pictureview') {
	
	$request_pic = $_REQUEST['pic'];
	if (!$request_pic && $mg->mgconfig['pictureview_start_id'] != -1) {
		$request_pic = $mg->mgconfig['pictureview_start_id'];
	}
		
	// --- Add by Marc:
	if ($mg->mgconfig['debug']) echo"Debug >> ". __LINE__." - // Show single Pic<br />\n";
	if ($mg->mgconfig['debug']) echo"|--- Pics: ".$request_pic."<br />\n";
	if ($mg->mgconfig['debug']) echo"|--- From_id: ".$_REQUEST['from_id']."<br />\n";
	if ($mg->mgconfig['debug']) echo"|--- Gal_id: ".$_REQUEST['gal_id']."<br />\n";
	if ($mg->mgconfig['debug']) echo"|--- Big_img_linkstyle: ".$mg->mgconfig['big_img_linkstyle']."<br />\n";
	if ($mg->mgconfig['debug']) echo"|--- Keep_bigimg: ".$mg->mgconfig['keep_bigimg']."<br />\n";
	if ($mg->mgconfig['debug']) echo"|--- Display_picno: ".$mg->mgconfig['display_picno']."<br />\n";
	// --- End add by Marc
	
	//if random order, use mysql RAND() 
	if ($mg->mgconfig['order_by'] == "random") {
		$orderby = "RAND()";
	} else {
		$orderby = $mg->mgconfig['order_by'];
	}
		
	//retrieve all gallery pics
	$rs=$modx->db->select("*", $modx->db->config['table_prefix'].$mg->mgconfig['gtable'], "gal_id='" . $mg->pageinfo['id'] . "'", $orderby.' '.$mg->mgconfig['order_direction']);
	$pics = array();
	while ($row = $modx->db->getRow($rs)) {
	 	array_push($pics, $row);
	}
	
	$total_pics=count($pics);
		
	$pictureTplData['pageinfo'] = $mg->pageinfo;
	$pictureTplData['big_img_linkstyle'] = $mg->mgconfig['big_img_linkstyle'];
	$pictureTplData['keep_bigimg'] = intval($mg->mgconfig['keep_bigimg']);
	$pictureTplData['strings'] = $mg->strings;
	$pictureTplData['config'] = $mg->mgconfig;
	$pictureTplData['path_to_gal'] = $mg->path_to_gal;
	$pictureTplData['total_pics_count'] = $total_pics;
	$pictureTplData['counter'] = 0;
	
	if ($total_pics>0) {
		//find pic
		$i=0;
		if ($request_pic || $mg->mgconfig['pictureview_start_pos'] != -1) {
			foreach($pics as $pic) {
				if($pic['id']==$request_pic || $mg->mgconfig['pictureview_start_pos'] == $i+1) {
					$current=$i;
					break;
				}
			$i++;
			}
		} else {
			$current=0;
			$i=0;
		}
		
		$pic_number=$i+1;
				
		$pics[$i]['title'] = stripslashes($pics[$i]['title']);
		$pics[$i]['descr'] = stripslashes($pics[$i]['descr']);
		$pictureTplData['picture'] = $pics[$i];
		$pictureTplData['counter'] = $pic_number;
		
		if(file_exists($modx->config['base_path'].$mg->path_to_gal."big_".$pics[$i]['filename'])){
			$imagesize=getimagesize($modx->config['base_path'].$mg->path_to_gal."big_".$pics[$i]['filename']);
			$pictureTplData['picture_height_big'] = $imagesize[1];
			$pictureTplData['picture_width_big'] = $imagesize[0];
			$pictureTplData['big_pic_exists'] = 1;
		} else {
			$pictureTplData['big_pic_exists'] = 0;
		}
		
		if(file_exists($modx->config['base_path'].$mg->path_to_gal.$pics[$i]['filename'])) {
			$imagesize=getimagesize($modx->config['base_path'].$mg->path_to_gal.$pics[$i]['filename']);
			$pictureTplData['picture_height_normal'] = $imagesize[1];
			$pictureTplData['picture_width_normal'] = $imagesize[0];
		}
		
		if(file_exists($modx->config['base_path'].$mg->path_to_gal."tn_".$pics[$i]['filename'])) {
			$imagesize=getimagesize($modx->config['base_path'].$mg->path_to_gal."tn_".$pics[$i]['filename']);
			$pictureTplData['picture_height_thumb'] = $imagesize[1];
			$pictureTplData['picture_width_thumb'] = $imagesize[0];
		}
		
		//build prev and next links
		$next_pic_querystr = "";
		$previous_pic_querystr = "";
		if($_REQUEST['from_id']){
			$next_pic_querystr .= "&from_id=".$_REQUEST['from_id'];
			$previous_pic_querystr .= "&from_id=".$_REQUEST['from_id'];
		}
		if($mg->mgconfig['is_target']==true && $_REQUEST['gal_id']){
			$next_pic_querystr .= "&gal_id=".$_REQUEST['gal_id'];
			$previous_pic_querystr .= "&gal_id=".$_REQUEST['gal_id'];
		}
		$next_pic = $modx->makeUrl($modx->documentIdentifier, '', "pic=".$pics[$i+1]['id'].$next_pic_querystr);
		$previous_pic = $modx->makeUrl($modx->documentIdentifier, '', "pic=".$pics[$i-1]['id'].$previous_pic_querystr);
		
		//build back to index link
		if($_REQUEST['from_id']){
			$back_to_index = $modx->makeUrl($_REQUEST['from_id'], '', '');
		}else if($mg->mgconfig['is_target']==true && $_REQUEST['gal_id']){
			$back_to_index = $modx->makeUrl($_REQUEST['gal_id'], '', '');
		}else{
			$back_to_index = $modx->makeUrl($modx->documentIdentifier, '', '');
		}
		
		$pictureTplData['index_url'] = $back_to_index;
		
		if($i+1!=1){
			$pictureTplData['previous_pic_url'] = $previous_pic;
		} else {
			$pictureTplData['previous_pic_url'] = $back_to_index;
		}
		
		if($i+1!=count($pics)){
			$pictureTplData['next_pic_url'] = $next_pic;
		} else {
			$pictureTplData['next_pic_url'] = $back_to_index;
		}
	}
	
	
	$tpl = new mgChunkie($mg->mgconfig['pictureTpl']);
	$tpl->addVar('maxigallery', $pictureTplData);
	$output =  $tpl->Render();
	$mg->regScriptsAndCSS();
	return;
	
}

$pictureTplData = array();

//-------------------------------------------------------------------------------------------------
// Childgalleries
//-------------------------------------------------------------------------------------------------

if($mg->mgconfig['display']=="childgalleries") {
	// collect gallery ids
	$rs = $modx->db->select("DISTINCT gal_id", $modx->db->config['table_prefix'].$mg->mgconfig['gtable'] );
	$childgalleryIds = array();
	while ($row = $modx->db->getRow($rs)) {
	 	array_push($childgalleryIds, $row['gal_id']);
	}
	if ($mg->mgconfig['childgalleries_ids'][0] == 'all') {
		$mg->mgconfig['childgalleries_ids'] = array_merge($childgalleryIds, $mg->mgconfig['childgalleries_ids']);
	}
	//check that does the template contain tv's
	$tpl = new mgChunkie($mg->mgconfig['childgalleryTpl']);
	preg_match_all('~tv\.(.[^\+|\:]*)~',$tpl->template, $matches);
	$tvNames = $matches[1];

	$children = $mg->getAllSubDocuments($childgalleryIds, $mg->mgconfig['childgalleries_ids'], $mg->mgconfig['childgalleries_order_by'], $mg->mgconfig['childgalleries_order_direction'], $mg->mgconfig['childgalleries_level_limit'], $mg->mgconfig['childgalleries_limit'], true, false, $tvNames);
	
	$outerTplData['childgallerycount'] = count($children);
	
	$counter = 1;
	if ($children != null && count($children) > 0) {
		foreach($children as $child) {
			//if random order, use mysql RAND()			
			if ($mg->mgconfig['order_by'] == "random") {
				$orderby = "RAND()";
			} else {
				$orderby = $mg->mgconfig['order_by'];
			}
			$rs = $modx->db->select("*", $modx->db->config['table_prefix'].$mg->mgconfig['gtable'], "(gal_id='" . $child['id'] . "' AND NOT hide='1')",$orderby.' '.$mg->mgconfig['order_direction'],1);
			$thumb = array();
			while ($row = $modx->db->getRow($rs)) {
		 		array_push($thumb, $row);
			}
			if($thumb[0]['filename']=="") continue;
		
			$childgalleryTplData = array();
			$childgalleryTplData['picscount'] = count($thumb);
			$childgalleryTplData['strings'] = $mg->strings;
			$childgalleryTplData['pageinfo'] = $child;
			$thumb[0]['title'] = stripslashes($thumb[0]['title']);
			$thumb[0]['descr'] = stripslashes($thumb[0]['descr']);
			$childgalleryTplData['picture'] = $thumb[0]; 
			$childgalleryTplData['childurl'] = $modx->makeUrl($child['id'], '', '');
			$childgalleryTplData['path_to_gal'] = $path_to_galleries.$child['id']."/";
			
			if(file_exists($modx->config['base_path'].$path_to_galleries.$child['id']."/"."big_".$thumb[0]['filename'])){
				$imagesize=getimagesize($modx->config['base_path'].$path_to_galleries.$child['id']."/"."big_".$thumb[0]['filename']);
				$childgalleryTplData['picture_height_big'] = $imagesize[1];
				$childgalleryTplData['picture_width_big'] = $imagesize[0];
				$childgalleryTplData['big_pic_exists'] = 1;
			} else {
				$childgalleryTplData['big_pic_exists'] = 0;
			}
			
			if(file_exists($modx->config['base_path'].$path_to_galleries.$child['id']."/".$thumb[0]['filename'])){
				$imagesize=getimagesize($modx->config['base_path'].$path_to_galleries.$child['id']."/".$thumb[0]['filename']);
				$childgalleryTplData['picture_height_normal'] = $imagesize[1];
				$childgalleryTplData['picture_width_normal'] = $imagesize[0];
			}
			
			if(file_exists($modx->config['base_path'].$path_to_galleries.$child['id']."/"."tn_".$thumb[0]['filename'])){
				$imagesize=getimagesize($modx->config['base_path'].$path_to_galleries.$child['id']."/"."tn_".$thumb[0]['filename']);
				$childgalleryTplData['picture_height_thumb'] = $imagesize[1];
				$childgalleryTplData['picture_width_thumb'] = $imagesize[0];
				$childgalleryTplData['config'] = $mg->mgconfig;
			}
			
			$tpl = new mgChunkie($mg->mgconfig['childgalleryTpl']);
			$tpl->addVar('maxigallery', $childgalleryTplData);
			$outerTplData['childgalleries'] .= $tpl->Render();
		
			if($counter == $mg->mgconfig['pics_per_row']) {
				$outerTplData['childgalleries'] .= $tpl->getTemplate($mg->mgconfig['clearerTpl']);
				$counter = 0;
			}
		
			$counter++;
		}
	}
}


//-------------------------------------------------------------------------------------------------
// Display gallery thumbnails
//-------------------------------------------------------------------------------------------------

// --- Add by Marc:
if ($mg->mgconfig['debug']) echo"Debug >> ". __LINE__." - // Show gallery overview with thumbnails<br />\n";
if ($mg->mgconfig['debug']) echo"|--- Pics_in_a_row: ".$mg->mgconfig['pics_in_a_row']."<br />\n";
if ($mg->mgconfig['debug']) echo"|--- Pics per page: ".$mg->mgconfig['pics_per_page']."<br />\n";
if ($mg->mgconfig['debug']) echo"|--- Only_page_numbers: ".$mg->mgconfig['only_page_numbers']."<br />\n";
if ($mg->mgconfig['debug']) echo"|--- Display: ".$mg->mgconfig['display']."<br />\n";
if ($mg->mgconfig['debug']) echo"|--- Embedtype: ".$mg->mgconfig['embedtype']."<br />\n";
// --- End add by Marc

//if random order, use mysql RAND() 
if ($mg->mgconfig['order_by'] == "random") {
	$orderby = "RAND()";
}else{
	$orderby = $mg->mgconfig['order_by'];
}
if (count($mg->mgconfig['gal_query_ids']) > 0) {
	//if all galleries are set to be retrieved, select distinct gal_id from db table
	if ($mg->mgconfig['gal_query_ids'][0] == 'all') {
		$rs = $modx->db->select("DISTINCT gal_id", $modx->db->config['table_prefix'].$mg->mgconfig['gtable'] );
		$mg->mgconfig['gal_query_ids'] = array();
		while ($row = $modx->db->getRow($rs)) {
		 	array_push($mg->mgconfig['gal_query_ids'], $row['gal_id']);
		}
	} else {
		//retrieve all child id's 
		$childs = array();
		foreach ($mg->mgconfig['gal_query_ids'] AS $gal_id) {
			$childs = $mg->getChildIds($gal_id, $mg->mgconfig['query_level_limit'], $childs);
		}
		$childs = array_values($childs);
		
		$mg->mgconfig['gal_query_ids'] = array_merge($childs, $mg->mgconfig['gal_query_ids']);
	}
	//retrieve pics by gal id's (query mode)
	$rs=$modx->db->select("*", $modx->db->config['table_prefix'].$mg->mgconfig['gtable'], "(gal_id IN ('" . implode("','", $mg->mgconfig['gal_query_ids']) . "') AND NOT hide='1')", $orderby . ' ' . $mg->mgconfig['order_direction'], $mg->mgconfig['offset'].','.$mg->mgconfig['limit']);
} else if (count($mg->mgconfig['pic_query_ids']) > 0) {
	//retrieve pics by picture id's (query mode)
	$rs=$modx->db->select("*", $modx->db->config['table_prefix'].$mg->mgconfig['gtable'], "(id IN ('" . implode("','", $mg->mgconfig['pic_query_ids']) . "') AND NOT hide='1')",$orderby.' '.$mg->mgconfig['order_direction'], $mg->mgconfig['offset'].','.$mg->mgconfig['limit']);
} else {
	//retrieve pics normally (by $mg->pageinfo['id'])
	$rs=$modx->db->select("*", $modx->db->config['table_prefix'].$mg->mgconfig['gtable'], "(gal_id='" . $mg->pageinfo['id'] . "' AND NOT hide='1')",$orderby.' '.$mg->mgconfig['order_direction'], $mg->mgconfig['offset'].','.$mg->mgconfig['limit']);
}

$pics = array();
while ($row = $modx->db->getRow($rs)) {
	 array_push($pics, $row);
}

$outerTplData['picscount'] = count($pics);

$offset = 0;
//if pictures per page limit is set, make the page navigator
if(isset($mg->mgconfig['pics_per_page']) && $mg->mgconfig['pics_per_page']!=0){
	// get pager values 
    if($_REQUEST['page']){
		$page = $_REQUEST['page']; 
	}else{
		$page = 1;
	}
    $total = count($pics);  
	$pager  = $mg->getPagerData($total, $mg->mgconfig['pics_per_page'], $page);
	
	$outerTplData['currentpage'] = $page;
	$outerTplData['pagecount'] = $pager['numPages'];
	 
	$outerTplData['previous_page_url'] = $mg->getPaginationUrl($modx->documentIdentifier, '', array("page" => $page-1));
	$outerTplData['next_page_url'] = $mg->getPaginationUrl($modx->documentIdentifier, '', array("page" => $page+1));

	//page numbers
	for ($i=1;$i<=$pager['numPages'];$i++) { 
		
		$pageNumberTplData = array();
		$pageNumberTplData['pageurl'] = $mg->getPaginationUrl($modx->documentIdentifier,'',array("page" => $i));
		$pageNumberTplData['pagenumber'] = $i;
		$pageNumberTplData['pagecount'] = $pager['numPages'];
		$pageNumberTplData['currentpage'] = $pager['page']; 
		$pageNumberTplData['config'] = $mg->mgconfig;
		
		$tpl = new mgChunkie($mg->mgconfig['pageNumberTpl']);
		$tpl->addVar('maxigallery', $pageNumberTplData);
		$outerTplData['pagenumbers'] .= $tpl->Render();
		
	}

	if($_REQUEST['page']){
		$offset = $pager['offset']; 
	}
}
//figure out the limit value for pics to get
if(!isset($mg->mgconfig['pics_per_page']) || $mg->mgconfig['pics_per_page']==0 || ($offset+$mg->mgconfig['pics_per_page']>count($pics))){
	$limit=count($pics);
}else{
	$limit=$offset+$mg->mgconfig['pics_per_page'];
}
//print out gallery overview
$counter = 1;
for($i=$offset;$i<$limit;$i++){
	//if in query mode
	if (count($mg->mgconfig['gal_query_ids']) > 0 || count($mg->mgconfig['pic_query_ids']) > 0) {
		$mg->path_to_gal = $path_to_galleries.$pics[$i]['gal_id']."/";
	}
	
	$pictureTplData['strings'] = $mg->strings;
	$pictureTplData['pageinfo'] = $mg->pageinfo;
	$pictureTplData['embedtype'] = $mg->mgconfig['embedtype'];
	$pictureTplData['path_to_gal'] = $mg->path_to_gal;
		
	$pics[$i]['title'] = stripslashes($pics[$i]['title']);
	$pics[$i]['descr'] = stripslashes($pics[$i]['descr']);
	$pictureTplData['picture'] = $pics[$i];
	
	//if in query mode
	if (count($mg->mgconfig['gal_query_ids']) > 0 || count($mg->mgconfig['pic_query_ids']) > 0) {
		$pictureTplData['picture_link_url'] = $modx->makeUrl($pics[$i]['gal_id'], '', "&pic=".$pics[$i]['id']."&from_id=".$modx->documentIdentifier);
	} else if(isset($mg->mgconfig['picture_target']) && $mg->mgconfig['picture_target'] != "") {
		$pictureTplData['picture_link_url'] = $modx->makeUrl($mg->mgconfig['picture_target'], '', "&gal_id=".$mg->pageinfo['id']."&pic=".$pics[$i]['id']);
	} else if(isset($mg->mgconfig['view_gallery']) && $mg->mgconfig['view_gallery'] != "") {
		$pictureTplData['picture_link_url'] = $modx->makeUrl($mg->pageinfo['id'], '', "&pic=".$pics[$i]['id']."&from_id=".$modx->documentIdentifier);
	} else {
		$pictureTplData['picture_link_url'] = $modx->makeUrl($mg->pageinfo['id'], '', "&pic=".$pics[$i]['id']);
	}
	
	if(file_exists($modx->config['base_path'].$mg->path_to_gal."big_".$pics[$i]['filename'])){
		$imagesize=getimagesize($modx->config['base_path'].$mg->path_to_gal."big_".$pics[$i]['filename']);
		$pictureTplData['picture_height_big'] = $imagesize[1];
		$pictureTplData['picture_width_big'] = $imagesize[0];
		$pictureTplData['big_pic_exists'] = 1;
	} else {
		$pictureTplData['big_pic_exists'] = 0;
	}
	
	if(file_exists($modx->config['base_path'].$mg->path_to_gal.$pics[$i]['filename'])) {
		$imagesize=getimagesize($modx->config['base_path'].$mg->path_to_gal.$pics[$i]['filename']);
		$pictureTplData['picture_height_normal'] = $imagesize[1];
		$pictureTplData['picture_width_normal'] = $imagesize[0];
	}
	if(file_exists($modx->config['base_path'].$mg->path_to_gal."tn_".$pics[$i]['filename'])) {
		$imagesize=getimagesize($modx->config['base_path'].$mg->path_to_gal."tn_".$pics[$i]['filename']);
		$pictureTplData['picture_height_thumb'] = $imagesize[1];
		$pictureTplData['picture_width_thumb'] = $imagesize[0];
		$pictureTplData['config'] = $mg->mgconfig;
	}
	$tpl = new mgChunkie($mg->mgconfig['galleryPictureTpl']);
	$tpl->addVar('maxigallery', $pictureTplData);
	$outerTplData['pictures'] .= $tpl->Render();
	
	if($counter == $mg->mgconfig['pics_per_row']) {
		$outerTplData['pictures'] .= $tpl->getTemplate($mg->mgconfig['clearerTpl']);
		$counter = 0;
	}
	
	$counter++;
}

$tpl = new mgChunkie($mg->mgconfig['galleryOuterTpl']);
//add data to the template
$outerTplData['embedtype'] = $mg->mgconfig['embedtype'];
$outerTplData['pageinfo'] = $mg->pageinfo;
$outerTplData['strings'] = $mg->strings;
$outerTplData['config'] = $mg->mgconfig;
$tpl->addVar('maxigallery', $outerTplData);
//render template and return output
$output =  $tpl->Render();
if($_REQUEST['mode']!="admin"){
	if (count($pics)>0) {
		//add smoothgallery div css
		if ($mg->mgconfig['display'] == "embedded" && $mg->mgconfig['embedtype'] == "smoothgallery") {
			if ($mg->mgconfig['smoothgallery_id'] != -1){
				$mg->regSmoothGalleryCSS($mg->mgconfig['smoothgallery_id']);
			} else {
				$mg->regSmoothGalleryCSS($mg->pageinfo['id']);	
			}
		}	
	} 
	$mg->regScriptsAndCSS();
}
return;
?>
