<?php

class index_Controller extends Controller{ 
	
	protected $pasta;
	
	public function __construct(){

		#parent::__construct();
		
		$this->pasta = 'index';
		
		Template::defineTemplate($this->pasta, 'form');
	}
}