<?php
class ModelExtensionTotalLoyaltyPoints extends Model {
    public function getTotal($total) {
        if (!$this->config->get('total_loyalty_points_status')) {
            return;
        }

        // Засега не правим нищо
    }
}