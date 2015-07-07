<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Video extends MY_Controller {
	public function index() {
		
		$this->data['SelectedMenu'] = 'video';
		$this->data ['title'] = 'Vidéos';
		$this->content = 'video'; // passing middle to function. change this for different views.
		
		require ('/public_html/api/vendor/autoload.php');
		$this->data['APIKey'] = 'AIzaSyCEdA2n4j2ZlfqkrEA86I3Abp1p3jU0wbI';
		
		$client = new Google_Client();
		$client->setDeveloperKey($this->data['APIKey']);
		
		$youtube = new Google_Service_Youtube($client);
		
		$response = $youtube->search->listSearch('id,snippet',['q' => 'racoon', 'order' => 'relevance','maxResults' => 10, 'type' => 'video']);
		
		echo '<pre>';
		var_dump($response);
		echo '</pre>';
		
		die();
				
		$this->layout ();
	}
}