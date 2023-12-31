<?php
class ModelCatalogManufacturer extends Model {
	public function addManufacturer($data) {
      	$this->db->query("INSERT INTO manufacturer SET name = '" . $this->db->escape(@$data['name']) . "', image_id = '" . (int)@$data['image_id'] . "', sort_order = '" . (int)$data['sort_order'] . "'");
      	  	
		$this->cache->delete('manufacturer');
	}
	
	public function editManufacturer($manufacturer_id, $data) {
      	$this->db->query("UPDATE manufacturer SET name = '" . $this->db->escape(@$data['name']) . "', image_id = '" . (int)@$data['image_id'] . "', sort_order = '" . (int)@$data['sort_order'] . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
			
		$this->cache->delete('manufacturer');
	}
	
	public function deleteManufacturer($manufacturer_id) {
		$this->db->query("DELETE FROM manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
			
		$this->cache->delete('manufacturer');
	}	
	
	public function getManufacturer($manufacturer_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		
		return $query->row;
	}
	
	public function getManufacturers($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM manufacturer";
			
			if (isset($data['sort'])) {
				$sql .= " ORDER BY " . $this->db->escape($data['sort']);	
			} else {
				$sql .= " ORDER BY name";	
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
			$manufacturer = $this->cache->get('manufacturer');
		
			if (!$manufacturer) {
				$query = $this->db->query("SELECT * FROM manufacturer ORDER BY name");
	
				$manufacturer = $query->rows;
			
				$this->cache->set('manufacturer', $manufacturer);
			}
		 
			return $manufacturer;
		}
	}
	
	public function getTotalManufacturersByImageId($image_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM manufacturer WHERE image_id = '" . (int)$image_id . "'");

		return $query->row['total'];
	}

	public function getTotalManufacturers() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM manufacturer");
		
		return $query->row['total'];
	}	
}
?>