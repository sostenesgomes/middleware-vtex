<?php

/**
 * @author sostenes
 * classe que carrega os visões (formulários)
 */

class Template {
	
	static protected $conteudo;
	static protected $objeto;
    static protected $cssFiles = array();
    static protected $jsFilesHeader = array();
    static protected $jsFilesFooter = array();
	
	public static function defineTemplate ($pasta, $file) {

		$filename = Config::get('view') . $pasta . DIRECTORY_SEPARATOR .$file . '.php';
		
		if (file_exists($filename)){
			ob_start();

			include_once($filename);
			self::$conteudo = ob_get_contents();

			ob_end_clean();
		}

		$layout = Config::get('layout') . 'scripts' . DIRECTORY_SEPARATOR . 'layout.php';

		require_once($layout);

	}
	
	public static function exibeTemplate(){
		return self::$conteudo;
	}
	
	public static function defineObjeto($nome,$objeto){
		self::$objeto[$nome] = $objeto; 
	}
	
	public static function getObjeto($nome=NULL){
		
		if (NULL === $nome){
			return isset(self::$objeto) ? self::$objeto : NULL;
		}else{
			return isset(self::$objeto) && isset(self::$objeto[$nome]) ? self::$objeto[$nome] : NULL;
		}
			
	}

    public static function registryCSS($cssFiles){

        if (is_array($cssFiles)){
            $cssPath = Config::get('css');

            foreach($cssFiles as $cfile ){
                self::$cssFiles[] = '<link type="text/css" href="'. $cssPath . $cfile .'" rel="stylesheet" />';
            }
        }

    }

    public static function loadCSS(){
        return implode("\r\n", self::$cssFiles);
    }

    public static function registryJSHeader($jsFilesHeader){

        if (is_array($jsFilesHeader)){
            $jsPath = Config::get('js');

            foreach($jsFilesHeader as $jsFile ){
                self::$jsFilesHeader[] = '<link type="text/css" href="'. $jsPath . $jsFile .'" rel="stylesheet" />';
            }
        }
    }

    public static function registryJSFooter($jsFilesFooter){

        if (is_array($jsFilesFooter)){
            $jsPath = Config::get('js');

            foreach($jsFilesFooter as $jsFile ){
                self::$jsFilesFooter[] = '<script type="text/javascript" src="'. $jsPath . $jsFile .'"></script>';
            }
        }

    }

    public static function loadJSHeader(){
        return implode("\r\n", self::$jsFilesHeader);
    }

    public static function loadJSFooter(){
        return implode("\r\n", self::$jsFilesFooter);
    }
}