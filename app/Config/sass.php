<?php

App::import('Vendor','PHPSass/SassParser');
App::uses('PxToRem','Lib');

// If development
if (env('MAJESTIC_ENV')==null) {

	$cssFolderDefault = APP . '/webroot/css/';

	$files = array(
		array(
			'scss' => $cssFolderDefault.'scss/style.scss',
			'css' => $cssFolderDefault.'style.css'
		)
	);

	$options = array(
		'style' => 'expanded',
		'cache' => FALSE,
		'syntax' => '.scss',
		'debug' => FALSE,
		'callbacks' => array(
			'warn' => 'warn',
			'debug' => 'debug'
		),
	);
	$parser = new SassParser($options);
	try {
		foreach ($files as $file) {
			//$fileCss = PxToRem::cssSetRemForPx($parser->toCss($file['scss']));
			$fileCss = $parser->toCss($file['scss']);
			file_put_contents($file['css'], $fileCss);
		}
	} catch (Exception $e) {
		print $e->getMessage();
	}
}