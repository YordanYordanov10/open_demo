<?php
class ModelAccountLoyaltyCoupon extends Model {

   
    public function addCoupon($customer_id, $code, $amount, $points_spent) {

        $this->db->query("INSERT INTO " . DB_PREFIX . "loyalty_coupon SET
            customer_id = '" . (int)$customer_id . "',
            code = '" . $this->db->escape($code) . "',
            amount = '" . (float)$amount . "',
            points_spent = '" . (int)$points_spent . "',
            status = '1',
            date_added = NOW(),
            date_used = NULL,
            date_expired = DATE_ADD(NOW(), INTERVAL 1 YEAR)
        ");
    }

   
    public function getCouponsByCustomerId($customer_id, $start = 0, $limit = 10) {

        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 10;
        }

        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "loyalty_coupon
            WHERE customer_id = '" . (int)$customer_id . "'
            ORDER BY date_added DESC
            LIMIT " . (int)$start . "," . (int)$limit . "
        ");

        return $query->rows;
    }

    public function getOldestCouponByCustomerId($customer_id) {

        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "loyalty_coupon
            WHERE customer_id = '" . (int)$customer_id . "'
            AND status = '1'
            AND date_expired > NOW()
            ORDER BY date_added ASC
            LIMIT 1
        ");

        return $query->row;
    }

    
    public function getTotalCouponsByCustomerId($customer_id) {

        $query = $this->db->query("
            SELECT COUNT(*) AS total
            FROM " . DB_PREFIX . "loyalty_coupon
            WHERE customer_id = '" . (int)$customer_id . "'
        ");

        return (int)$query->row['total'];
    }


    public function getActiveCoupons($customer_id) {

        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "loyalty_coupon
            WHERE customer_id = '" . (int)$customer_id . "'
            AND status = '1'
            AND date_expired > NOW()
            ORDER BY date_added DESC
        ");

        return $query->rows;
    }

    
    public function markCouponAsUsed($coupon_id, $order_id) {
    $this->db->query("
        UPDATE " . DB_PREFIX . "loyalty_coupon
        SET 
            status = 0,
            order_id = '" . (int)$order_id . "',
            date_used = NOW()
        WHERE coupon_id = '" . (int)$coupon_id . "'
    ");

    
}

public function getCouponIdByOrderId($order_id) {
        $query = $this->db->query("SELECT coupon_id FROM " . DB_PREFIX . "loyalty_coupon WHERE order_id = '" . (int)$order_id . "' LIMIT 1");

        if ($query->num_rows) {
            return (int)$query->row['coupon_id'];
        }

        return 0;
    }

}