<?php

include '../library/autoload.php';

setlocale(LC_ALL, 'pt_BR.UTF-8', 'pt_BR', 'portuguese');
date_default_timezone_set( "America/Sao_Paulo" );
mb_internal_encoding("UTF-8");
iconv_set_encoding('internal_encoding', "UTF-8");
header('Content-Type: text/html; charset=' . Config::get('sys_charset'), true);
ini_set('mbstring.internal_encoding', Config::get('sys_charset'));
ini_set('default_charset', Config::get('sys_charset'));

if (Config::get('development') === true || (isset($_GET) && isset($_GET['developer']) && $_GET['developer'] == 'EenS-[WR')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
else
    ini_set('display_errors', 0);


/*carregar o controller */
$path = Config::get('controller');
URI::parse_uri();

if (URI::get_segment(0, false)) {
	
	$controller = URI::get_segment(0, false);
	$file = $path . $controller . '.page.php';

	if (file_exists($file)) {
		$controllerClass = $controller.'_Controller';
		$controller 	 = $file;
	}
	
}else{
	$controller 	 = $path . 'index.page.php';
	$controllerClass = 'index_Controller';
}

ob_start();
require_once($controller);
new $controllerClass;
ob_end_flush();

?>
