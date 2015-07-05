<?php
class nav_model extends CI_Model {

	function getNav() {
		if(isset($_SESSION['acces'])){
			$private = $_SESSION['acces'];
			$query = $this->db->query ("SELECT * from nav INNER JOIN acces ON nav.acces_id = acces.acces_id where libelle_acess='users' or libelle_acess='$private'");
		}
		else{
			$query = $this->db->query ("SELECT * from nav INNER JOIN acces ON nav.acces_id = acces.acces_id where libelle_acess='users'");
		}
		$result = $query->result ();
		return $result;

	}
}