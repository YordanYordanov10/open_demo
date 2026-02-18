<?php

class ModelAccountLoyalty extends Model{

 public function getCustomerPoints($customer_id) {

    $query = $this->db->query("
        SELECT SUM(points) AS total 
        FROM `" . DB_PREFIX . "loyalty_transaction` 
        WHERE customer_id = '" . (int)$customer_id . "'
    ");

    return (float)$query->row['total'];
 }

 public function getTransactions($data = array()) {
    $sql = "SELECT * FROM `" . DB_PREFIX . "loyalty_transaction` WHERE customer_id = '" . (int)$this->customer->getId() . "'";

    if (isset($data['sort']) && in_array($data['sort'], ['date_added', 'points'])) {
        $sql .= " ORDER BY " . $data['sort'];
    } else {
        $sql .= " ORDER BY date_added";
    }

    if (isset($data['order']) && ($data['order'] == 'DESC')) {
        $sql .= " DESC";
    } else {
        $sql .= " ASC";
    }

    if (isset($data['start']) || isset($data['limit'])) {
        if ($data['start'] < 0) {
            $data['start'] = 0;
        }

        if ($data['limit'] < 1) {
            $data['limit'] = 20;
        }

        $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
    }

    $query = $this->db->query($sql);

    return $query->rows;
 }

 public function addTransaction($customer_id, $points, $type, $description, $order_id = null) {
     $this->db->query("INSERT INTO " . DB_PREFIX . "loyalty_transaction SET 
            customer_id = '" . (int)$customer_id . "', 
            order_id = '" . (int)$order_id . "', 
            description = '" . $this->db->escape($description) . "', 
            points = '" . (int)$points . "', 
            transaction_type = '" . $this->db->escape($type) . "',
            date_added = NOW(),
            date_expired = IF(transaction_type = 'earn', DATE_ADD(NOW(), INTERVAL 1 YEAR), NULL)
     ");

    
 }

 public function deductPoints($customer_id, $points, $description, $order_id){

    $this->db->query("INSERT INTO " . DB_PREFIX . "loyalty_transaction SET
            customer_id = '" . (int)$customer_id . "',
             order_id = '" . (int)$order_id . "', 
            points = '" . (int)$points . "', 
            description = '" . $this->db->escape($description) . "', 
            date_added = NOW(),
            transaction_type = 'redeem'
            ");
           
 }

   public function hasPointsForOrder($order_id) {
    $query = $this->db->query("
        SELECT transaction_id 
        FROM " . DB_PREFIX . "loyalty_transaction
        WHERE order_id = '" . (int)$order_id . "' AND transaction_type = 'earn'
        LIMIT 1
    ");

    return $query->num_rows;
    }

    public function hasCancelTransactionForOrder($order_id) {
        $query = $this->db->query("
            SELECT transaction_id 
            FROM " . DB_PREFIX . "loyalty_transaction
            WHERE order_id = '" . (int)$order_id . "' AND transaction_type = 'cancel'
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