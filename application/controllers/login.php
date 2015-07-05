<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Login extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * http://example.com/index.php/welcome
	 * - or -
	 * http://example.com/index.php/welcome/index
	 * - or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 *
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index() {
		session_start ();
		$this->load->helper ( 'url' );
		$baseurl = site_url ();
		$this->load->database ();

		$login = $_POST ["login"];
		$password = md5 ( ($_POST ["password"]) );

		$this->load->model ( 'Login_model' );
		try {
			$responses = $this->Login_model->getLogin ( $login, $password );
			$_SESSION ['pseudo'] = $responses[0]->pseudo;
			$_SESSION ['id'] = $responses[0]->id_user;
			$_SESSION ['login'] = $responses [0]->login_user;
			$_SESSION ['name'] = $responses [0]->name;
			$_SESSION ['acces'] = $responses [0]->libelle_acess;
			$_SESSION ['email'] = $responses [0]->user_email;
			$_SESSION ['pseudo'] = $responses [0]->pseudo;
			header ( 'Location:' . $baseurl );
		} catch ( Exception $exception ) {
			header ( 'Location:' . $baseurl . "?error=1" );
		}
	}
	public function logout() {
		session_start ();
		$this->load->helper ( 'url' );
		$baseurl = site_url ();
		session_destroy ();
		header ( 'Location:' . $baseurl );
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */