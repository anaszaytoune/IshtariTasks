<?php
class ModelCatalogDownload extends Model {
	public function addDownload($data) {
      	$this->db->query("INSERT INTO download SET filename = '" . $this->db->escape($data['download']['name']) . "', mask = '" . $this->db->escape($data['mask']) . "', remaining = '" . (int)$data['remaining'] . "', date_added = NOW()");

      	$download_id = $this->db->getLastId();

      	foreach ($data['download_description'] as $language_id => $value) {
        	$this->db->query("INSERT INTO download_description SET download_id = '" . (int)$download_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
      	}	
	}
	
	public function editDownload($download_id, $data) {
      	if ($data['download']['name']) {
        	$this->db->query("UPDATE download SET filename = '" . $this->db->escape($data['download']['name']) . "' WHERE download_id = '" . (int)$download_id . "'");
      	}
 
        $this->db->query("UPDATE download SET mask = '" . $this->db->escape($data['mask']) . "', remaining = '" . (int)$data['remaining'] . "' WHERE download_id = '" . (int)$download_id . "'");
		
      	$this->db->query("DELETE FROM download_description WHERE download_id = '" . (int)$download_id . "'");

      	foreach ($data['download_description'] as $language_id => $value) {
        	$this->db->query("INSERT INTO download_description SET download_id = '" . (int)$download_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
      	}	
	}
	
	public function deleteDownload($download_id) {
      	$this->db->query("DELETE FROM download WHERE download_id = '" . (int)$download_id . "'");
	  	$this->db->query("DELETE FROM download_description WHERE download_id = '" . (int)$download_id . "'");	
	}	

	public function getDownload($download_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM download WHERE download_id = '" . (int)$download_id . "'");
		
		return $query->row;
	}

	public function getDownloads($data = array()) {
		$sql = "SELECT * FROM download d LEFT JOIN download_description dd ON (d.download_id = dd.download_id) WHERE dd.language_id = '" . (int)$this->language->getId() . "'";
			
		if (isset($data['sort'])) {
			$sql .= " ORDER BY " . $this->db->escape($data['sort']);	
		} else {
			$sql .= " ORDER BY dd.name";	
		}
			
		if (isset($data['order'])) {
			$sql .= " " . $this->db->escape($data['order']);
		} else {
			$sql .= " ASC";
		}
			
		if (isset($data['start']) || isset($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);
	
		return $query->rows;
	}
	
	public function getDownloadDescriptions($download_id) {
		$download_description_data = array();
		
		$query = $this->db->query("SELECT * FROM download_description WHERE download_id = '" . (int)$download_id . "'");
		
		foreach ($query->rows as $result) {
			$download_description_data[$result['language_id']] = array('name' => $result['name']);
		}
		
		return $download_description_data;
	}
	
	public function getTotalDownloads() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM download");
		
		return $query->row['total'];
	}	
}
?>