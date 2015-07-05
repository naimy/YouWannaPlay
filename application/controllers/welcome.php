<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Welcome extends MY_Controller {
	public function index() {
		$this->content = 'welcome_message'; // passing middle to function. change this for different views.
		$this->layout ();
	}
}