<?php

class ControllerExtensionModuleLoyaltyPoints extends Controller{

    public function addPointsOnComplete(&$route, &$args, &$output) {

        $order_id = $args[0];
        $order_status_id = $args[1];

        $completed_status_id = (int)$this->config->get('total_loyalty_points_complete_status_id'); 

        if ($order_status_id == $completed_status_id) {

            $this->load->model('checkout/order');
            $this->load->model('account/loyalty');

            $order_info = $this->model_checkout_order->getOrder($order_id);
            
              $earn_rate = (float)$this->config->get('total_loyalty_points_earn_rate');
                $point_value = (float)$this->config->get('total_loyalty_points_value');
                $min_points_to_redeem = (float)$this->config->get('total_loyalty_points_to_redeem');
                $allow_redeem = (float)$this->config->get('total_loyalty_allow_redeem');
                $max_order_percent = (float)$this->config->get('total_loyalty_point_max_percent');

                $points = 0;

                
                

            if ($order_info && $order_info['customer_id'] > 0) {

                $total = $order_info['total'];
                $customer_id = $order_info['customer_id'];
                

                $points = $total * $earn_rate/100;

                $description = 'Points earned for order #' . $order_id;

                $this->model_account_loyalty->addPoints($customer_id,$points,$description,$order_id);

            }
        }
    }
}
