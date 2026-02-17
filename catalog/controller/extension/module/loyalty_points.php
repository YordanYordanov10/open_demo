<?php
class ControllerExtensionModuleLoyaltyPoints extends Controller {

    public function addPointsOnComplete(&$route, &$args, &$output) {
        $order_id = $args[0];
        $order_status_id = $args[1];

        $completed_status_id = (int)$this->config->get('total_loyalty_points_complete_status_id');

        // 1. Проверка на статуса
        if ($order_status_id != $completed_status_id) {
            return;
        }

        $this->load->model('checkout/order');
        $this->load->model('account/loyalty');

        $order_info = $this->model_checkout_order->getOrder($order_id);

        // 2. Проверка за клиент и валидна поръчка
        if ($order_info && $order_info['customer_id'] > 0) {
            $customer_id = (int)$order_info['customer_id'];
            
            // Защита: Ако вече сме давали/вземали точки за ТАЗИ поръчка, спираме.
            if ($this->model_account_loyalty->hasPointsForOrder($order_id)) {
                return;
            }

            // ЛОГИКА ЗА ИЗВАЖДАНЕ (REDEEM)
            $used_points = $this->model_account_loyalty->getPointsUsedForOrder($order_id);
            
            if ($used_points > 0) {
                $this->model_account_loyalty->addPoints(
                    $customer_id, 
                    -$used_points, 
                    'Points redeemed for order #' . $order_id, 
                    $order_id
                );
            }

            // ЛОГИКА ЗА ДОБАВЯНЕ (EARN)
            $earn_rate = (float)$this->config->get('total_loyalty_points_percent');
            $total = (float)$order_info['total'];
            $points_to_earn = floor($total * $earn_rate / 100);

            if ($points_to_earn > 0) {
                $this->model_account_loyalty->addPoints(
                    $customer_id, 
                    $points_to_earn, 
                    'Points earned for order #' . $order_id, 
                    $order_id
                );
            }
        }
    }

  public function returnPointsOnCancel(&$route, &$args, &$output) {

       
        $order_id = $args[0];
        $order_status_id = $args[1];
        $canceled_status_id = (int)$this->config->get('total_loyalty_points_canceled_status_id');

         $this->log->write('CANCEL EVENT TRIGGERED - Order ID: ' . $order_id . ' | Status: ' . $order_status_id);

        // КОРЕКЦИЯ: Продължаваме само ако статусът съвпада
        if ($order_status_id != $canceled_status_id) return;

        $this->load->model('checkout/order');
        $this->load->model('account/loyalty');
        $order_info = $this->model_checkout_order->getOrder($order_id);

        if ($order_info && $order_info['customer_id'] > 0) {
            $customer_id = (int)$order_info['customer_id'];
            
              // Защита: Ако вече сме давали/вземали точки за ТАЗИ поръчка, спираме.
            if ($this->model_account_loyalty->hasPointsForOrder($order_id)) {
                return;
            }

            // 1. Връщане на използваните точки (REDEEM RETURN)
            $used_points = $this->model_account_loyalty->getPointsUsedForOrder($order_id);
            if ($used_points > 0) {
                $this->model_account_loyalty->addPoints($customer_id, $used_points, 'Points returned for cancelled order #' . $order_id, $order_id);
            }

            // 2. Анулиране на спечелените точки (EARN CANCEL)
            $earn_rate = (float)$this->config->get('total_loyalty_points_percent');
            $points_earned = floor((float)$order_info['total'] * $earn_rate / 100);
            if ($points_earned > 0) {
                $this->model_account_loyalty->addPoints($customer_id, -$points_earned, 'Earned points removed (Order Cancelled) #' . $order_id, $order_id);
            }
        }
    }
}

