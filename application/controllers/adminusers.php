<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Adminusers extends CI_Controller {

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
		$this->load->database ();

		if (isset ( $_SESSION ['login'] )) {
			$connexion = true;
		} else {
			$connexion = false;
		}

        $this->load->model ( 'nav_model' );
        $this->load->model ( 'config_model' );

        try {
            $responses = $this->nav_model->getNav ();
            $title = $this->config_model->getTitle ();
        } catch ( Exception $exception ) {
        }

        $data = array (
            'connexion' => $connexion,
            'nav' => $responses,
            'title' => $title[0]->title
        );

		$this->load->model ( 'users_model' );

		$dataGroup ['group'] = $this->users_model->groupUser ();
		$dataGroup ['users'] = $this->users_model->getAllusers ();

		$this->load->view ( 'common/header', $data );
		$this->load->view ( 'adminusers', $dataGroup );
		$this->load->view ( 'common/footer');
	}

	public function addlogin() {
		$this->load->helper ( 'url' );
		$baseurl = site_url ();

		$aUsers = array (
				"id_user" => "",
				"login_user" => $_POST ['login'],
				"password_user" => md5($_POST ['password']),
				"name" => $_POST ['Name'],
				"user_email" => $_POST ['mail'],
				"acces_id" => $_POST ['groupUsers']
		);

		$this->load->database ();
		$this->load->model ( 'users_model' );
		try {
			$this->users_model->addUser ( $aUsers );
			header ( 'Location:' . $baseurl . "index.php/adminusers?sucess=1" );
		} catch ( Exception $exception ) {
			header ( 'Location:' . $baseurl . "?error=1" );
		}
	}

	public function deletelogin() {
		$this->load->helper ( 'url' );
		$baseurl = site_url ();

		$aUsers = array (
				"id_user" => $_POST ['idUserDelete']
		);

		$this->load->database ();
		$this->load->model ( 'users_model' );
		try {
			$this->users_model->deleteUser ( $aUsers );
			header ( 'Location:' . $baseurl . "index.php/adminusers?sucess=1" );
		} catch ( Exception $exception ) {
			header ( 'Location:' . $baseurl . "?error=1" );
		}
	}

	public function modifyusers() {
		$this->load->helper ( 'url' );
		$baseurl = site_url ();

		$aUsers = array (
				"id_user" => $_POST ['id_user'],
				"login_user" => $_POST ['login'],
				//"password_user" => $_POST ['password'],
				"name" => $_POST ['Name'],
				"user_email" => $_POST ['mail'],
				"acces_id" => $_POST ['groupUsers']
		);

		$this->load->database ();
		$this->load->model ( 'users_model' );
		try {
			$this->users_model->modifyUser($aUsers);
			header ( 'Location:' . $baseurl . "index.php/adminusers?sucess=1" );
		} catch ( Exception $exception ) {
			header ( 'Location:' . $baseurl . "?error=1" );
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */