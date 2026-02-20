<?php
class ControllerExtensionModuleLoyaltyCoupon extends Controller {

    public function confirm(&$route, &$args, &$output) {
        $order_id = isset($args[0]) ? (int)$args[0] : 0;
        $order_status_id = isset($args[1]) ? (int)$args[1] : 0;

        if (!$order_id) {
            return;
        }

        $completed_status_id = (int)$this->config->get('total_loyalty_coupon_complete_status_id');

        if ($order_status_id != $completed_status_id) {
            return;
        }

        $this->load->model('checkout/order');
        $this->load->model('account/loyalty_coupon');

        $coupon_id = 0;

        // 1. Опит за вземане от сесията
        if (!empty($this->session->data['loyalty_coupon']['coupon_id'])) {
            $coupon_id = (int)$this->session->data['loyalty_coupon']['coupon_id'];
        } 
        
        // 2. Fallback: Търсене на активен купон в базата данни за този клиент
        if (!$coupon_id) {
            $order_info = $this->model_checkout_order->getOrder($order_id);
            
            if ($order_info && $order_info['customer_id']) {
                $active_coupon = $this->model_account_loyalty_coupon->getOldestCouponByCustomerId($order_info['customer_id']);
                
                if ($active_coupon) {
                    $coupon_id = (int)$active_coupon['coupon_id'];
                }
            }
        }

        // 3. Маркиране като използван
        if ($coupon_id) {
            $this->model_account_loyalty_coupon->markCouponAsUsed($coupon_id, $order_id);

            if (isset($this->session->data['loyalty_coupon'])) {
                unset($this->session->data['loyalty_coupon']);
            }
        }
    }
}