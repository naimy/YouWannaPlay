<?php
class Login_model extends CI_Model {
	function getLogin($login, $password) {
		if ($login == "" || $password == "") {
			throw new Exception("Aucun login et / ou mot de passe"); // absence de mot de passe et / ou login
		} else {
			$login = mysql_real_escape_string ( $login );
			$query = $this->db->query ( "SELECT * from users INNER JOIN acces ON users.acces_id = acces.acces_id where login_user = '$login'" );
			$result = $query->result ();

			if (! empty ( $result )) {
				if ($result [0]->password_user == $password) {
					return $result;
				} else {
					throw new Exception("Mauvais login et / ou mot de passe"); // mauvais mot de passe
				}
			} else {
				throw new Exception("Mauvais login et / ou mot de passe"); // mauvais login et / ou mot de passe
			}
		}
	}
}