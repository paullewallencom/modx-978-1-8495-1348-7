/*
 * CodePrettify *
 *
 * DESCRIPTION: Allows syntax highlighting of code blocks, using the 
 * google-code-prettify javascript
 *
 * HISTORY:
 * version 0.5 (2007-09-08): by Daniele "MadMage" Calisi
 * 
 * NOTES: google-code-prettify can be downloaded from Google Code website (http://code.google.com/p/google-code-prettify/)
 * and is under the Apache 2.0 license (http://www.apache.org/licenses)
 *
 * INSTRUCTIONS: 
 * - extract the content of the zip archive in assets/plugins/codeprettify
 * - create a new MODx plugin using the code of this file
 * - select "OnLoadWebDocument" as the System Event that will trigger this plugin
 * - all source code in the webpage enclosed in <code class="prettyprint">...</code>
 *   or in <pre class="prettyprint">...</pre> will be automatically prettified.
 * - you can optionally put some css in assets/plugins/codeprettify/prettify-custom.css file
 */

switch ($modx->Event->name) {
	case "OnLoadWebDocument":
		$modx->regClientCSS('assets/plugins/codeprettify/prettify.css');
		$modx->regClientCSS('assets/plugins/codeprettify/prettify-custom.css');
		$modx->regClientStartupScript('manager/media/script/mootools/mootools.js');
		$modx->regClientStartupScript('assets/plugins/codeprettify/prettify.js');
		$jspp = '<script type="text/javascript">';
		$jspp .= 'window.addEvent("domready", prettyPrint);';
		$jspp .= '</script>';
		$modx->regClientStartupScript($jspp);
		break;
		
	default:	// stop here
		return; 
		break;	
}
