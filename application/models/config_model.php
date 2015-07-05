<?php
class config_model extends CI_Model {

    function getTitle() {
        $query = $this->db->query ("SELECT * FROM config where id_config = 1");
        $result = $query->result ();
        return $result;
    }

    function getAdress() {
    	$query = $this->db->query ("SELECT * FROM config where id_config != 1");
    	$result = $query->result ();
    	return $result;
    }

    function modifyTitle($value) {
        $query = $this->db->query ("UPDATE config SET title = '$value' WHERE id_config = 1");
    }

    function getAllConfig() {
    	$query = $this->db->query ("select * from config");
    	$result = $query->result ();
    	return $result;
    }

    function updateData($data) {
    	var_dump($data);

    	$ts3 = $data['adresse_Ts'];
    	$transmission = $data['adresse_Transmission'];
    	$webmin = $data['adresse_Webmin'];
    	$plex = $data['adresse_Plex'];
    	$server = $data['adresse_Server'];

    	$query = $this->db->query ("
    			UPDATE config
    			SET value = '$ts3'
    			WHERE title = 'adresse_Ts'");

    	$query = $this->db->query ("
    			UPDATE config
    			SET value = '$transmission'
    			WHERE title = 'adresse_Transmission'");

    	$query = $this->db->query ("
    			UPDATE config
    			SET value = '$webmin'
    			WHERE title = 'adresse_Webmin'");

    	$query = $this->db->query ("
    			UPDATE config
    			SET value = '$plex'
    			WHERE title = 'adresse_Plex'");

    	$query = $this->db->query ("
    			UPDATE config
    			SET value = '$server'
    			WHERE title = 'adresse_Server'");
    }
}