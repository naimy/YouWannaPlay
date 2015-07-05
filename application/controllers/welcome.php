<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Welcome extends CI_Controller {

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

		$this->load->view ( 'common/header', $data );
		$this->load->view ( 'index' );
		$this->load->view ( 'common/footer');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */