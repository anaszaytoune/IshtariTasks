<?php  
class ModelCustomerOrder extends Model {
	public function editOrder($order_id, $data) {
		$this->db->query("UPDATE `order` SET order_status_id = '" . (int)$data['order_status_id'] . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

      	$this->db->query("INSERT INTO order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$data['order_status_id'] . "', notify = '" . (int)@$data['notify'] . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW()");

      	if (isset($data['notify'])) {
        	$query = $this->db->query("SELECT *, os.name AS status, l.code AS language FROM `order` o LEFT JOIN order_status os ON (o.order_status_id = os.order_status_id AND os.language_id = o.language_id) LEFT JOIN language l ON (o.language_id = l.language_id) WHERE o.order_id = '" . (int)$order_id . "'");
	    	
			if ($query->num_rows) {
				$this->language->load($query->row['filename'], $query->row['language']);
			
    			$find = array(
					'{store}',
					'{order_id}',
					'{date_added}',
					'{status}',
					'{comment}',
					'{invoice}'
				);
			
				$replace = array(
					'store'      => $this->config->get('config_store'),
					'order_id'   => $order_id,
					'date_added' => date($this->language->get('date_format_short'), strtotime($query->row['date_added'])),
					'status'     => $query->row['status'],
					'comment'    => strip_tags($data['comment']),
					'invoice'    => HTTP_CATALOG . 'index.php?route=account/invoice&order_id=' . $order_id
				);
				
				$subject = str_replace($find, $replace, $this->config->get('mail_update_subject_' . $query->row['language_id']));
				$message = str_replace($find, $replace, $this->config->get('mail_update_message_' . $query->row['language_id']));

				$mail = new Mail();
	    		$mail->setTo($query->row['email']);
				$mail->setFrom($this->config->get('config_email'));
	    		$mail->setSender($this->config->get('config_store'));
	    		$mail->setSubject($subject);
	    		$mail->setText($message);
	    		$mail->send();
			}
		}
	}
	
	public function deleteOrder($order_id) {
      	$this->db->query("DELETE FROM `order` WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM order_history WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM order_product WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM order_option WHERE order_id = '" . (int)$order_id . "'");
	  	$this->db->query("DELETE FROM order_download WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM order_total WHERE order_id = '" . (int)$order_id . "'");
	}
		
	public function getOrder($order_id) {
		$query = $this->db->query("SELECT * FROM `order` WHERE order_id = '" . (int)$order_id . "'");
	
		return $query->row;
	}
	
	public function getOrders($data = array()) {
		$sql = "SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS name, os.name AS status, o.date_added, o.total, o.currency, o.value FROM `order` o LEFT JOIN order_status os ON (o.order_status_id = os.order_status_id) WHERE os.language_id = '" . (int)$this->language->getId() . "' AND o.confirm = '1'";

		if (isset($data['order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['order_id'] . "'";
		}

		if (isset($data['name'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['name']) . "%'";
		}

		if (isset($data['order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['order_status_id'] . "'";
		}
		
		if (isset($data['date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['date_added']) . "')";
		}
		
		if (isset($data['total'])) {
			$sql .= " AND o.total = '" . (float)$data['total'] . "'";
		}

		if (isset($data['sort'])) {
			$sql .= " ORDER BY " . $this->db->escape($data['sort']);	
		} else {
			$sql .= " ORDER BY o.order_id";	
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
	
	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM order_product WHERE order_id = '" . (int)$order_id . "'");
	
		return $query->rows;
	}

	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
	
		return $query->rows;
	}
	
	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT * FROM order_total WHERE order_id = '" . (int)$order_id . "'");
	
		return $query->rows;
	}	

	public function getOrderHistory($order_id) { 
		$query = $this->db->query("SELECT oh.date_added, os.name AS status, oh.comment, oh.notify FROM order_history oh LEFT JOIN order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->language->getId() . "' ORDER BY oh.date_added");
	
		return $query->rows;
	}	

	public function getOrderDownloads($order_id) {
		$query = $this->db->query("SELECT * FROM order_download WHERE order_id = '" . (int)$order_id . "' ORDER BY name");
	
		return $query->rows; 
	}	
				
	public function getTotalOrders($data = array()) {
      	$sql = "SELECT COUNT(*) AS total FROM `order` WHERE confirm = '1'";

		if (isset($data['order_id'])) {
			$sql .= " AND order_id = '" . (int)$data['order_id'] . "'";
		}

		if (isset($data['name'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['name']) . "%'";
		}

		if (isset($data['order_status_id'])) {
			$sql .= " AND order_status_id = '" . (int)$data['order_status_id'] . "'";
		}
		
		if (isset($data['date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['date_added']) . "')";
		}
		
		if (isset($data['total'])) {
			$sql .= " AND total = '" . (float)$data['total'] . "'";
		}
		
		$query = $this->db->query($sql);
		
		return $query->row['total'];
	}
			
	public function getOrderHistoryTotalByOrderStatusId($order_status_id) {
	  	$query = $this->db->query("SELECT order_id, COUNT(*) AS total FROM order_history WHERE order_status_id = '" . (int)$order_status_id . "' GROUP BY order_id");

		return $query->row['total'];
	}

	public function getTotalOrdersByOrderStatusId($order_status_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `order` WHERE order_status_id = '" . (int)$order_status_id . "'");
		
		return $query->row['total'];
	}
	
	public function getTotalOrdersByLanguageId($language_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `order` WHERE language_id = '" . (int)$language_id . "'");
		
		return $query->row['total'];
	}	
	
	public function getTotalOrdersByCurrencyId($currency_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `order` WHERE currency_id = '" . (int)$currency_id . "'");
		
		return $query->row['total'];
	}		
}
?>