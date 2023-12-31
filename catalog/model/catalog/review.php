<?php
class ModelCatalogReview extends Model {		
	public function addReview($product_id, $data) {
		 $this->db->query("INSERT INTO review SET author = '" . $this->db->escape($data['name']) . "', customer_id = '" . (int)$this->customer->getId() . "', product_id = '" . (int)$product_id . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', rating = '" . (int)$data['rating'] . "', date_added = NOW()");
	}
	
	public function getReview($review_id) {
		$query = $this->db->query("SELECT DISTINCT r.review_id, r.author, r.rating, r.text, p.product_id, pd.name, p.price, p.tax_class_id, i.filename, r.date_added FROM review r LEFT JOIN product p ON (r.product_id = p.product_id) LEFT JOIN product_description pd ON (p.product_id = pd.product_id) LEFT JOIN image i ON (p.image_id = i.image_id) WHERE p.status = '1' AND r.review_id = '" . (int)$review_id . "' AND pd.language_id = '" . (int)$this->language->getId() . "' AND p.date_available < NOW() AND p.status = '1' AND r.status = '1'");
		
		return $query->row;
	}
		
	public function getReviewsByProductId($product_id, $start = 0, $limit = 20) {
		$query = $this->db->query("SELECT r.review_id, r.author, r.rating, r.text, p.product_id, pd.name, p.price, i.filename, r.date_added FROM review r LEFT JOIN product p ON (r.product_id = p.product_id) LEFT JOIN product_description pd ON (p.product_id = pd.product_id) LEFT JOIN image i ON (p.image_id = i.image_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available < NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->language->getId() . "' ORDER BY r.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
		
		return $query->rows;
	}
	
	public function getRandomReview($limit) {
		$query = $this->db->query("SELECT r.review_id, r.rating, r.text, p.product_id, pd.name, i.filename FROM review r LEFT JOIN product p ON (r.product_id = p.product_id) LEFT JOIN product_description pd ON (p.product_id = pd.product_id) LEFT JOIN image i ON (p.image_id = i.image_id) WHERE pd.language_id = '" . (int)$this->language->getId() . "' AND p.date_available < NOW() AND p.status = '1' AND r.status = '1' ORDER BY rand() LIMIT " . (int)$limit);
		
		return $query->rows;
	}
	
	public function getRandomReviewByProductId($product_id, $limit) {
		$query = $this->db->query("SELECT r.review_id, r.rating, r.text, p.product_id, pd.name, i.filename FROM review r LEFT JOIN product p ON (r.product_id = p.product_id) LEFT JOIN product_description pd ON (p.product_id = pd.product_id) LEFT JOIN image i ON (p.image_id = i.image_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->language->getId() . "' AND p.date_available < NOW() AND p.status = '1' AND r.status = '1' ORDER BY rand() LIMIT " . (int)$limit);
		
		return $query->rows;
	}
	
	public function getAverageRating($product_id) {
		$query = $this->db->query("SELECT AVG(rating) AS total FROM review WHERE status = '1' AND product_id = '" . (int)$product_id . "' GROUP BY product_id");
		
		if (isset($query->row['total'])) {
			return (int)$query->row['total'];
		} else {
			return 0;
		}
	}	
	
	public function getTotalReviews() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM review r LEFT JOIN product p ON (r.product_id = p.product_id) WHERE p.date_available < NOW() AND p.status = '1' AND r.status = '1'");
		
		return $query->row['total'];
	}

	public function getTotalReviewsByProductId($product_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM review r LEFT JOIN product p ON (r.product_id = p.product_id) LEFT JOIN product_description pd ON (p.product_id = pd.product_id) LEFT JOIN image i ON (p.image_id = i.image_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available < NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->language->getId() . "'");
		
		return $query->row['total'];
	}
}
?>