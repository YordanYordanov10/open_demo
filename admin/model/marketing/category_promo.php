<?php
class ModelMarketingCategoryPromo extends Model
{

    public function getPromotions()
    {

        $query = $this->db->query("
        SELECT cd.id,
               cd.category_id,
               cd.percent,
               cd.status,
               c.name
        FROM " . DB_PREFIX . "category_discount cd
        LEFT JOIN " . DB_PREFIX . "category_description c
            ON cd.category_id = c.category_id
        WHERE c.language_id = '" . (int)$this->config->get('config_language_id') . "'
    ");

        return $query->rows;
    }

    

    public function savePromotion($data)
    {

        $this->db->query("
            INSERT INTO " . DB_PREFIX . "category_discount
            SET category_id = '" . (int)$data['category_id'] . "',
                percent = '" . (float)$data['percent'] . "',
                status = 1
        ");
    }

    public function deletePromotion($id)
    {
        $this->db->query("
        DELETE FROM " . DB_PREFIX . "category_discount
        WHERE id = '" . (int)$id . "'
    ");
    }

    public function addPromotion($data)
    {
        $this->db->query("
        INSERT INTO " . DB_PREFIX . "category_discount
        SET category_id = '" . (int)$data['category_id'] . "',
            percent = '" . (float)$data['percent'] . "'
    ");
    }

    public function updatePromotion($id, $data)
    {
        $this->db->query("
        UPDATE " . DB_PREFIX . "category_discount
        SET category_id = '" . (int)$data['category_id'] . "',
            percent = '" . (float)$data['percent'] . "'
        WHERE id = '" . (int)$id . "'
    ");
    }

    public function editPromotion($data) {
    // Взимаме ID-то на записа от URL-а, за да знаем кой ред променяме
    $category_promo_id = (int)$this->request->get['category_promo_id'];

    $this->db->query("UPDATE " . DB_PREFIX . "category_discount SET 
        category_id = '" . (int)$data['category_id'] . "', 
        percent = '" . (float)$data['percent'] . "', 
        status = '" . (int)$data['status'] . "' 
        WHERE id = '" . $category_promo_id . "'");
}

    public function getPromotion($id)
    {
        $query = $this->db->query("
        SELECT * FROM " . DB_PREFIX . "category_discount
        WHERE id = '" . (int)$id . "'
    ");
        return $query->row;
    }


    public function getPromotionByCategoryId($category_id) {
    $query = $this->db->query("
        SELECT * FROM " . DB_PREFIX . "category_discount 
        WHERE category_id = '" . (int)$category_id . "'
    ");
    
    return $query->row; 
}
}
