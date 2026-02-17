<?php
class ModelExtensionTotalLoyaltyPoints extends Model {
    public function getTotal($total) {
         $this->load->language('extension/total/loyalty_points');

        if (!$this->config->get('total_loyalty_points_status')) {
            return;
        }

        if ($this->customer->isLogged()) {
            $customer_id = $this->customer->getId();
            $this->load->model('account/loyalty');
            $points_balance = $this->model_account_loyalty->getCustomerPoints($customer_id);

            $points_to_redeem = (float)$this->config->get('total_loyalty_points_to_redeem');
            $point_value = (float)$this->config->get('total_loyalty_points_value');
            $max_order_percent = (float)$this->config->get('total_loyalty_points_max_order_percent');
            $allow_redeem = (float)$this->config->get('total_loyalty_points_allow_redeem');

            $order_total = $total['total'];
            $max_redeemable = ($max_order_percent / 100) * $order_total;

            if ($points_balance >= $points_to_redeem && $point_value > 0 && $allow_redeem) {
                $points_used = (floor($max_redeemable / $point_value)) ? floor($max_redeemable / $point_value) : 0;
                $points_used = min($points_used, $points_balance);
                $discount = $points_used * $point_value;
               
                   $total['totals'][] = [
                    'code'  => 'loyalty_points',
                    'title' => 'Loyalty Points (' . $points_used . ')',
                    'value' => -$discount,
                    'loyalty_points_used' => $points_used,
                    'sort_order' => $this->config->get('total_loyalty_points_sort_order')
                    ];

                    $total['total'] -= $discount;
            }   
            
        }

  }
}