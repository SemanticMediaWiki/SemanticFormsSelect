<?php
if( !defined( 'MEDIAWIKI' ) ) {
	die("This file is an extension to the MediaWiki software and cannot be used standalone.\n");
}

//self executing anonymous function to prevent global scope assumptions
call_user_func( function() {

	$GLOBALS['wgExtensionCredits'][defined( 'SEMANTIC_EXTENSION_TYPE' ) ? 'semantic' : 'specialpage'][] = array(
		   'path' => __FILE__,
		   'name' => 'SemanticFormsSelect',
		   'author' =>array( '[http://www.mediawiki.org/wiki/User:Jasonzhang Jasonzhang]', 'Toniher'),
		   'url' => 'https://www.mediawiki.org/wiki/Extension:SemanticFormsSelect', 
		   'description' => 'Generate a select field in Semantic Form which values are from query',
		   'version'  => 1.1,
	);

	//$wgAjaxExportList[] = "QueryExecution";
	//$wgAjaxExportList[] = "FunctionExecution";
	$GLOBALS['wgExtensionFunctions'][] = "SFSelect_formSetup";

	$GLOBALS['wgAutoloadClasses']['SemanticFormsSelect'] = dirname(__FILE__) . '/SemanticFormsSelect.classes.php';
	$GLOBALS['wgAutoloadClasses']['ApiSemanticFormsSelect'] = dirname(__FILE__) . '/SemanticFormsSelect.api.php';

	// api modules
	$GLOBALS['wgAPIModules']['sformsselect'] = 'ApiSemanticFormsSelect';

	$GLOBALS['wgSF_SelectDir'] = dirname(__FILE__) ;
	$GLOBALS['wgSF_SelectScriptPath']  = $GLOBALS['wgScriptPath'] . '/extensions/'.basename($GLOBALS['wgSF_SelectDir']);
	$GLOBALS['wgScriptSelectCount']=0;
	
	$GLOBALS['wgSF_Select_debug']=0;
	
	$GLOBALS['wgResourceModules']['ext.sf_select.scriptselect'] = array(
		'localBasePath' => $GLOBALS['wgSF_SelectDir'],
		'remoteExtPath' => 'SemanticFormsSelect',
		 'scripts' => array( 'scriptSelect.js' ),
		 'dependencies' => array('ext.semanticforms.main')
	);

});

function SFSelect_formSetup() {
	global $sfgFormPrinter, $wgOut;
	$sfgFormPrinter->setInputTypeHook('SF_Select','SemanticFormsSelect::SF_Select',array());
}


?>