<?php

class ModelReportProfitStats extends Model
{
    public function getRevenueStats($period)
    {

        if ($period == 'today') {
            $date_condition = "DATE(o.date_added) = CURDATE()";
        }

        if ($period == 'month') {
            $date_condition = "MONTH(o.date_added) = MONTH(CURDATE())
                           AND YEAR(o.date_added) = YEAR(CURDATE())";
        }

        if ($period == 'week') {
            $date_condition = "YEARWEEK(o.date_added, 1) = YEARWEEK(CURDATE(), 1)";
        }

        if ($period == 'year') {
            $date_condition = "YEAR(o.date_added) = YEAR(CURDATE())";
        }

        $sql = "
        SELECT 
            SUM(o.total) as revenue,
            COUNT(o.order_id) as orders,
            AVG(o.total) as aov
        FROM `" . DB_PREFIX . "order` o
        WHERE o.order_status_id > 0
        AND $date_condition
    ";

        $query = $this->db->query($sql);

        return [
            'revenue' => $query->row['revenue'] ?? 0,
            'orders'  => $query->row['orders'] ?? 0,
            'aov'     => $query->row['aov'] ?? 0
        ];
    }

   

    public function getProfitStats($period)
    {
        if ($period == 'today') {
            $date_condition = "DATE(o.date_added) = CURDATE()";
        }

        if ($period == 'month') {
            $date_condition = "MONTH(o.date_added) = MONTH(CURDATE())
                           AND YEAR(o.date_added) = YEAR(CURDATE())";
        }

        if ($period == 'week') {
            $date_condition = "YEARWEEK(o.date_added, 1) = YEARWEEK(CURDATE(), 1)";
        }

        if ($period == 'year') {
            $date_condition = "YEAR(o.date_added) = YEAR(CURDATE())";
        }

        $sql = "
        SELECT 
            SUM(o.total) as revenue,
            SUM(op.profit) as profit,
            SUM(op.cost) as cost,
            COUNT(o.order_id) as orders
        FROM `" . DB_PREFIX . "order_profit` op
        LEFT JOIN `" . DB_PREFIX . "order` o ON op.order_id = o.order_id
        WHERE o.order_status_id > 0
        AND $date_condition
    ";
    
        $query = $this->db->query($sql);

        return [
            'profit' => $query->row['profit'] ?? 0,
            'cost' => $query->row['cost'] ?? 0,
            'revenue' => $query->row['revenue'] ?? 0,
            'margin' => ($query->row['revenue'] > 0) ? round(($query->row['profit'] / $query->row['revenue']) * 100, 2) . '%' : '0%',
            'orders' => $query->row['orders'] ?? 0
        ];
    }

    public function getOrdersStats($period)
    {
        if ($period == 'today') {
            $date_condition = "DATE(o.date_added) = CURDATE()";
        }

        if ($period == 'month') {
            $date_condition = "MONTH(o.date_added) = MONTH(CURDATE())
                           AND YEAR(o.date_added) = YEAR(CURDATE())";
        }

        if ($period == 'week') {
            $date_condition = "YEARWEEK(o.date_added, 1) = YEARWEEK(CURDATE(), 1)";
        }

        if ($period == 'year') {
            $date_condition = "YEAR(o.date_added) = YEAR(CURDATE())";
        }

        $sql = "
        SELECT 
            COUNT(*) as orders,
            SUM(o.total) as revenue,
            AVG(o.total) as average_order_value,
            AVG(o.total) as items_sold,
            SUM(CASE WHEN o.order_status_id = 1 THEN 1 ELSE 0 END) as orders_pending,
            SUM(CASE WHEN o.order_status_id = 2 THEN 1 ELSE 0 END) as orders_processing,
            SUM(CASE WHEN o.order_status_id = 5 THEN 1 ELSE 0 END) as orders_completed,
            SUM(CASE WHEN o.order_status_id = 7 THEN 1 ELSE 0 END) as orders_cancelled,
            SUM(CASE WHEN o.order_status_id = 11 THEN 1 ELSE 0 END) as orders_refunded
        FROM `" . DB_PREFIX . "order` o
        WHERE o.order_status_id > 0
        AND $date_condition
    ";

        $query = $this->db->query($sql);
        return [
            'orders' => $query->row['orders'] ?? 0,
            'revenue' => $query->row['revenue'] ?? 0,
            'average_order_value' => $query->row['average_order_value'] ?? 0,
            'items_sold' => $query->row['items_sold'] ?? 0,
            'orders_pending' => $query->row['orders_pending'] ?? 0,
            'orders_processing' => $query->row['orders_processing'] ?? 0,
            'orders_completed' => $query->row['orders_completed'] ?? 0,
            'orders_cancelled' => $query->row['orders_cancelled'] ?? 0,
            'orders_refunded' => $query->row['orders_refunded'] ?? 0
        ];

    }

    public function getMarginStats($period)
    {
         if ($period == 'today') {
            $date_condition = "DATE(o.date_added) = CURDATE()";
        }

        if ($period == 'month') {
            $date_condition = "MONTH(o.date_added) = MONTH(CURDATE())
                           AND YEAR(o.date_added) = YEAR(CURDATE())";
        }

        if ($period == 'week') {
            $date_condition = "YEARWEEK(o.date_added, 1) = YEARWEEK(CURDATE(), 1)";
        }

        if ($period == 'year') {
            $date_condition = "YEAR(o.date_added) = YEAR(CURDATE())";
        }

        $sql = "
        SELECT 
            SUM(op.profit) as profit,
            SUM(op.cost) as cost,
            SUM(o.total) as revenue
        FROM `" . DB_PREFIX . "order_profit` op
        LEFT JOIN `" . DB_PREFIX . "order` o ON o.order_id = op.order_id
        WHERE o.order_status_id > 0
        AND $date_condition
    ";

        $query = $this->db->query($sql);

        $profit = $query->row['profit'] ?? 0;
        $revenue = $query->row['revenue'] ?? 0;
        $cost = $query->row['cost'] ?? 0;
        $margin = ($revenue > 0) ? ($profit / $revenue) * 100 : 0;

        return [
            'margin' => round($margin, 2),
            'revenue' => $revenue,
            'profit' => $profit,
            'cost' => $cost
        ];
    }

  
    public function getStatsDataChart(){

    $sql = "
        SELECT 
            DATE(o.date_added) as date,
            SUM(o.total) as revenue,
            SUM(op.profit) as profit,
            COUNT(o.order_id) as orders
        FROM `" . DB_PREFIX . "order` o
        LEFT JOIN `" . DB_PREFIX . "order_profit` op ON o.order_id = op.order_id
        WHERE o.order_status_id > 0
        AND o.date_added >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY DATE(o.date_added)
        ORDER BY DATE(o.date_added)
    ";

    $query = $this->db->query($sql);

    $labels = [];
    $revenue = [];
    $profit = [];
    $orders = [];
    $margin = [];

    foreach ($query->rows as $row) {

        $labels[] = $row['date'];
        $revenue[] = (float)$row['revenue'];
        $profit[] = (float)$row['profit'];
        $orders[] = (int)$row['orders'];
        $margin[] = ($row['revenue'] > 0) ? round(($row['profit'] / $row['revenue']) * 100, 2) : 0;
    }

    return [
        'labels' => $labels,
        'data' => $revenue,
        'profit' => $profit,
        'orders' => $orders,
        'margin' => array_map(function($rev, $prof) {
            return ($rev > 0) ? round(($prof / $rev) * 100, 2) : 0;
        }, $revenue, $profit)
    ];
}
}    
