<?php

class ModelAccountLoyalty extends Model{

 public function getCustomerPoints($customer_id) {

    $query = $this->db->query("
        SELECT SUM(points) AS total 
        FROM `" . DB_PREFIX . "customer_loyalty_points` 
        WHERE customer_id = '" . (int)$customer_id . "'
    ");

    return (float)$query->row['total'];
 }

 public function addPoints($customer_id,$points,$description,$order_id){

    $this->db->query("INSERT INTO " . DB_PREFIX . "customer_loyalty_points SET 
            customer_id = '" . (int)$customer_id . "', 
            order_id = '" . (int)$order_id . "', 
            points = '" . (float)$points . "', 
            description = '" . $this->db->escape($description) . "', 
            date_added = NOW()");
    
 }

 public function deductPoints($customer_id, $points, $description, $order_id){

    $this->db->query("INSERT INTO " . DB_PREFIX . "customer_loyalty_points SET
            customer_id = '" . (int)$customer_id . "',
             order_id = '" . (int)$order_id . "', 
            points = '" . (float)$points . "', 
            description = '" . $this->db->escape($description) . "', 
            date_added = NOW()");
           
 }

   public function hasPointsForOrder($order_id) {
    $query = $this->db->query("
        SELECT id 
        FROM " . DB_PREFIX . "customer_loyalty_points
        WHERE order_id = '" . (int)$order_id . "'
        LIMIT 1
    ");

    return $query->num_rows;
}

public function getPointsUsedForOrder($order_id) {
    $query = $this->db->query("SELECT value FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' AND code = 'loyalty_points'");

    if ($query->num_rows) {
        $discount = abs($query->row['value']); 
        $point_value = (float)$this->config->get('total_loyalty_points_value');

        if ($point_value > 0) {
            return (int)($discount / $point_value); // ВРЪЩА резултата
        }
    }
    
    return 0; // Връща 0, ако няма използвани точки
}

}