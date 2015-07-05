<?php
class users_model extends CI_Model {

	function addUser($aUsers) {
		$this->db->insert('users', $aUsers);
	}

	function groupUser() {
		$query = $this->db->query ("SELECT * FROM acces");
		$result = $query->result ();
		return $result;
	}

	function getAllUsers() {
		$query = $this->db->query ("SELECT * FROM users");
		$result = $query->result ();
		return $result;
	}

	function getUser($id) {
		$query = $this->db->query ("SELECT * FROM users INNER JOIN `acces` ON users.acces_id = acces.acces_id where id_user = $id");
		$result = $query->result ();
		return $result;
	}

	function deleteUser($aUsers) {
		$this->db->delete('users', $aUsers);
	}

	function modifyUser($aUsers) {
		$this->db->where('id_user', $aUsers['id_user']);
		$this->db->update('users', $aUsers);
	}

}