<?php
class chat_model extends CI_Model {

	function getAllMessage() {
		$query = $this->db->query ("SELECT * FROM chat INNER JOIN `users` ON  users.id_user = chat.user_id");
		$result = $query->result ();
		return $result;
	}

	function getTimeMessage($nowtime, $nowDate) {
		$lasttime = $nowtime - 6;
		$query = $this->db->query ("SELECT * FROM chat INNER JOIN `users` ON  users.id_user = chat.user_id where time > $lasttime and time < $nowtime");
		$result = $query->result ();
		return $result;
	}

	function addChat($data) {
		$this->db->insert('chat', $data);
	}

}