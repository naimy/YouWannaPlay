<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class ajaxArticle extends CI_Controller {

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
		$this->load->helper ( 'url' );
		$baseurl = site_url ();
		$id_article = $_POST ['id'];

		$this->load->database ();
		$this->load->model ( 'article_model' );
		$this->load->model ( 'users_model' );

		try {
			$data ['group'] = $this->users_model->groupUser();
			$data['articles'] = $this->article_model->getArticleFromId($id_article);
			//var_dump($data);
			$this->load->view ( 'ajaxArticle', $data );
		} catch ( Exception $exception ) {
			header ( 'Location:' . $baseurl . "?error=1" );
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */