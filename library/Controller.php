<?php

class Controller{
	
	public function __construct(){
		$this->_carregaModulos();
	}

	private function _carregaModulos(){
		$modulo = new modulo();
		Template::defineObjeto('modulos', $modulo->getModulos());
	}
	
	
}

?>