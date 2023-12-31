<?php
class ModelCatalogInformation extends Model {
	public function addInformation($data) {
		$this->db->query("INSERT INTO information SET sort_order = '" . (int)$this->request->post['sort_order'] . "'");

		$information_id = $this->db->getLastId(); 
			
		foreach ($data['information_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO information_description SET information_id = '" . (int)$information_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		$this->cache->delete('information');
	}
	
	public function editInformation($information_id, $data) {
		$this->db->query("UPDATE information SET sort_order = '" . (int)$data['sort_order'] . "' WHERE information_id = '" . (int)$information_id . "'");

		$this->db->query("DELETE FROM information_description WHERE information_id = '" . (int)$information_id . "'");
					
		foreach ($data['information_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO information_description SET information_id = '" . (int)$information_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		$this->cache->delete('information');
	}
	
	public function deleteInformation($information_id) {
		$this->db->query("DELETE FROM information WHERE information_id = '" . (int)$information_id . "'");
		$this->db->query("DELETE FROM information_description WHERE information_id = '" . (int)$information_id . "'");

		$this->cache->delete('information');
	}	

	public function getInformation($information_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM information WHERE information_id = '" . (int)$information_id . "'");
		
		return $query->row;
	}
		
	public function getInformations($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM information i LEFT JOIN information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->language->getId() . "'";
		
			if (isset($data['sort'])) {
				$sql .= " ORDER BY " . $this->db->escape($data['sort']);	
			} else {
				$sql .= " ORDER BY id.title";	
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
		} else {
			$information = $this->cache->get('information.' . $this->language->getId());
		
			if (!$information) {
				$query = $this->db->query("SELECT * FROM information i LEFT JOIN information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->language->getId() . "' ORDER BY id.title");
	
				$information = $query->rows;
			
				$this->cache->set('information.' . $this->language->getId(), $information);
			}	
	
			return $information;			
		}
	}
	
	public function getInformationDescriptions($information_id) {
		$information_description_data = array();
		
		$query = $this->db->query("SELECT * FROM information_description WHERE information_id = '" . (int)$information_id . "'");

		foreach ($query->rows as $result) {
			$information_description_data[$result['language_id']] = array(
				'title'       => $result['title'],
				'description' => $result['description']
			);
		}
		
		return $information_description_data;
	}
	
	public function getTotalInformations() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM information");
		
		return $query->row['total'];
	}	
}
?>