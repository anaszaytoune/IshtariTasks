<?php 
class ModelLocalisationOrderStatus extends Model {
	public function addOrderStatus($data) {
		foreach ($data['order_status'] as $language_id => $value) {
			$this->db->query("INSERT INTO order_status SET order_status_id = '" . (int)@$order_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			
			$order_status_id = $this->db->getLastId();
		}
		
		$this->cache->delete('order_status');
	}

	public function editOrderStatus($order_status_id, $data) {
		$this->db->query("DELETE FROM order_status WHERE order_status_id = '" . (int)$order_status_id . "'");

		foreach ($data['order_status'] as $language_id => $value) {
			$this->db->query("INSERT INTO order_status SET order_status_id = '" . (int)$order_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
				
		$this->cache->delete('order_status');
	}
	
	public function deleteOrderStatus($order_status_id) {
		$this->db->query("DELETE FROM order_status WHERE order_status_id = '" . (int)$order_status_id . "'");
	
		$this->cache->delete('order_status');
	}
		
	public function getOrderStatus($order_status_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$this->language->getId() . "'");
		
		return $query->row;
	}
		
	public function getOrderStatuses($data = array()) {
      	if ($data) {
			$sql = "SELECT * FROM order_status WHERE language_id = '" . (int)$this->language->getId() . "'";
			
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
			$order_status = $this->cache->get('order_status.' . $this->language->getId());
		
			if (!$order_status) {
				$query = $this->db->query("SELECT order_status_id, name FROM order_status WHERE language_id = '" . (int)$this->language->getId() . "' ORDER BY name");
	
				$order_status = $query->rows;
			
				$this->cache->set('order_status.' . $this->language->getId(), $order_status);
			}	
	
			return $order_status;				
		}
	}
	
	public function getOrderStatusDescriptions($order_status_id) {
		$order_status_data = array();
		
		$query = $this->db->query("SELECT * FROM order_status WHERE order_status_id = '" . (int)$order_status_id . "'");
		
		foreach ($query->rows as $result) {
			$order_status_data[$result['language_id']] = array('name' => $result['name']);
		}
		
		return $order_status_data;
	}
	
	public function getTotalOrderStatuses() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM order_status WHERE language_id = '" . (int)$this->language->getId() . "'");
		
		return $query->row['total'];
	}	
}
?>