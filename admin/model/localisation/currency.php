<?php
class ModelLocalisationCurrency extends Model {
	public function addCurrency($data) {
		$this->db->query("INSERT INTO currency SET title = '" . $this->db->escape(@$data['title']) . "', code = '" . $this->db->escape(@$data['code']) . "', symbol_left = '" . $this->db->escape(@$data['symbol_left']) . "', symbol_right = '" . $this->db->escape($data['symbol_right']) . "', decimal_place = '" . $this->db->escape($data['decimal_place']) . "', value = '" . $this->db->escape($data['value']) . "', status = '" . (int)$data['status'] . "', date_modified = NOW()");

		$this->cache->delete('currency');
	}
	
	public function editCurrency($currency_id, $data) {
		$this->db->query("UPDATE currency SET title = '" . $this->db->escape($data['title']) . "', code = '" . $this->db->escape($data['code']) . "', symbol_left = '" . $this->db->escape($data['symbol_left']) . "', symbol_right = '" . $this->db->escape($data['symbol_right']) . "', decimal_place = '" . $this->db->escape($data['decimal_place']) . "', value = '" . $this->db->escape($data['value']) . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE currency_id = '" . (int)$currency_id . "'");

		$this->cache->delete('currency');
	}
	
	public function deleteCurrency($currency_id) {
		$this->db->query("DELETE FROM currency WHERE currency_id = '" . (int)$currency_id . "'");
	
		$this->cache->delete('currency');
	}

	public function getCurrency($currency_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM currency WHERE currency_id = '" . (int)$currency_id . "'");
	
		return $query->row;
	}
	
	public function getCurrencies($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM currency";

			if (isset($data['sort'])) {
				$sql .= " ORDER BY " . $this->db->escape($data['sort']);	
			} else {
				$sql .= " ORDER BY title";	
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
			$currency = $this->cache->get('currency');

			if (!$currency) {
				$query = $this->db->query("SELECT * FROM currency ORDER BY title ASC");
	
				$currency = $query->rows;
			
				$this->cache->set('currency', $currency);
			}
			
			return $currency;			
		}
	}	
	
	public function getTotalCurrencies() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM currency");
		
		return $query->row['total'];
	}
}
?>