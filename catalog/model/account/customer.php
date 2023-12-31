<?php
class ModelAccountCustomer extends Model {
	public function addCustomer($data) {
      	$this->db->query("INSERT INTO customer SET firstname = '" . $this->db->escape(@$data['firstname']) . "', lastname = '" . $this->db->escape(@$data['lastname']) . "', email = '" . $this->db->escape(@$data['email']) . "', telephone = '" . $this->db->escape(@$data['telephone']) . "', fax = '" . $this->db->escape(@$data['fax']) . "', password = '" . $this->db->escape(md5(@$data['password'])) . "', newsletter = '" . $this->db->escape(@$data['newsletter']) . "', status = '1', date_added = NOW()");
      	
		$customer_id = $this->db->getLastId();
			
      	$this->db->query("INSERT INTO address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape(@$data['firstname']) . "', lastname = '" . $this->db->escape(@$data['lastname']) . "', company = '" . $this->db->escape(@$data['company']) . "', address_1 = '" . $this->db->escape(@$data['address_1']) . "', address_2 = '" . $this->db->escape(@$data['address_2']) . "', city = '" . $this->db->escape(@$data['city']) . "', postcode = '" . $this->db->escape(@$data['postcode']) . "', country_id = '" . (int)@$data['country_id'] . "', zone_id = '" . (int)@$data['zone_id'] . "'");
		
		$address_id = $this->db->getLastId();

      	$this->db->query("UPDATE customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");	
	}
	
	public function editCustomer($data) {
		$this->db->query("UPDATE customer SET firstname = '" . $this->db->escape(@$data['firstname']) . "', lastname = '" . $this->db->escape(@$data['lastname']) . "', email = '" . $this->db->escape(@$data['email']) . "', telephone = '" . $this->db->escape(@$data['telephone']) . "', fax = '" . $this->db->escape(@$data['fax']) . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function editPassword($email, $password) {
      	$this->db->query("UPDATE customer SET password = '" . $this->db->escape(md5($password)) . "' WHERE email = '" . $this->db->escape($email) . "'");
	}

	public function editNewsletter($customer_id, $newsletter) {
		$this->db->query("UPDATE customer SET newsletter = '" . (int)$newsletter . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}
			
	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT * FROM customer WHERE customer_id = '" . (int)$customer_id . "'");
		
		return $query->row;
	}
	
	public function getTotalCustomersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM customer WHERE email = '" . $this->db->escape($email) . "'");
		
		return $query->row['total'];
	}
}
?>