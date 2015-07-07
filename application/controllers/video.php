<?php
if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class Video extends MY_Controller {
	
	public function index() {
		
		$this->data['SelectedMenu'] = 'video';
		$this->data ['title'] = 'Vidéos';
		$this->content = 'video'; // passing middle to function. change this for different views.
		
		require_once ('./public_html/api/vendor/autoload.php');
		
		$key = 'AIzaSyA65jBgblQcRlGvID4MuB8RALgjbANBGA0';
		$client = new Google_Client();
		$client->setDeveloperKey($key);
		
		$youtube = new Google_Service_YouTube($client);
		
		$response = $youtube->Channels->list(
				'id,snippet',
				array (
					'forUsername' => "youwannaplay"
				)
		);
		
		echo '<pre>';
		var_dump($response);
		echo '</pre>';
		
		die();
				
		$this->layout ();
	}
	
	
}