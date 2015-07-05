<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Controller extends CI_Controller 
 { 
   //set the class variable.
   var $template  = array();
   var $data      = array();
   //Load layout    
   public function layout() {
     // making temlate and send data to view.
     $this->template['header']   = $this->load->view('layout/header', $this->data, true);
     $this->template['right']   = $this->load->view('layout/right', $this->data, true);
     $this->template['content'] = $this->load->view($this->content, $this->data, true);
     $this->template['footer'] = $this->load->view('layout/footer', $this->data, true);
     $this->load->view('layout/index', $this->template);
   }
}