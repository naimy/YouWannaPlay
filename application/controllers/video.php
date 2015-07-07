<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Video extends MY_Controller {
	public function index() {
		$this->data['SelectedMenu'] = 'video';
		$this->data ['title'] = 'Vidéos';
		$this->content = 'video'; // passing middle to function. change this for different views.
		$this->layout ();
	}
}