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
 
}