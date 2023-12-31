<?php
class ModelCatalogCategory extends Model {

	function sortCategoriesByEnabledProducts($categories)
{
    // $sortedCategories = [];
    // $enabledProductsCount = [];

    foreach ($categories as $category) {
        $enabledProductsCount[$category['category_id']] = count($category['products']) - count($category['products_disabled']);
    }

    arsort($enabledProductsCount);

    foreach ($enabledProductsCount as $categoryId => $enabledProducts) {
        $sortedCategories[] = $categories[$categoryId];
    }

    return $sortedCategories;
}
	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM category c LEFT JOIN category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->language->getId() . "'");
		
		return $query->row;
	}
	
	public function getCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM category c LEFT JOIN category_description cd ON (c.category_id = cd.category_id) LEFT JOIN image i ON (c.image_id = i.image_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->language->getId() . "' ORDER BY c.sort_order");

		return $query->rows;
	}
				
	public function getTotalCategoriesByCategoryId($parent_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM category WHERE parent_id = '" . (int)$parent_id . "'");
		
		return $query->row['total'];
	}
}
?>