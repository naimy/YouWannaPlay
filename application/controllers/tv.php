<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Tv extends MY_Controller {
	public function index() {
		$this->data['SelectedMenu'] = 'tv';
		$this->data ['title'] = 'Chaine TV';
		$this->content = 'welcome_message'; // passing middle to function. change this for different views.
		$this->layout ();
	}
}