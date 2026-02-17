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
            
            $earn_rate = (float)$this->config->get('total_loyalty_points_percent');
              
            $points = 0;

            if ($order_info && $order_info['customer_id'] > 0) {

                $total = $order_info['total'];
                $customer_id = $order_info['customer_id'];
               
            
                if ($this->model_account_loyalty->hasPointsForOrder($order_id)) {
                    return;
                }

                $this->log->write('Total: ' . $total . ' | Rate: ' . $earn_rate);

                $points = floor($total * $earn_rate / 100);

                $this->log->write('Calculated Points: ' . $points);

                $points = floor($total * $earn_rate/100);
                $description = 'Points earned for order #' . $order_id;

                if($points > 0) {
                $this->model_account_loyalty->addPoints($customer_id,$points,$description,$order_id);
                }

            }
        }
    }

    public function deductPointsOnRefund(&$route, &$args, &$output) {

        $order_id = $args[0];
        $order_status_id = $args[1];

        $completed_status_id = (int)$this->config->get('total_loyalty_points_complete_status_id'); 
        

        if ($order_status_id != $completed_status_id) {
            return;
        }

        $this->load->model('checkout/order');
        $this->load->model('account/loyalty');

        $order_info = $this->model_checkout_order->getOrder($order_id);
        $customer_id = $order_info['customer_id'];


        $order_info = $this->model_checkout_order->getOrder($order_id);

        if ($order_info && $order_info['customer_id'] > 0) {

            $customer_id = $order_info['customer_id'];
            $point_value = (float)$this->config->get('total_loyalty_points_value');


            
        }



// 2️⃣ Поръчката има ли клиент?

// ✔ order_info съществува
// ✔ customer_id > 0

// Ако е guest → няма точки → exit

// 3️⃣ Поръчката използвала ли е точки?

// Отиваш в order_total и проверяваш:

// ✔ Има ли ред с code = loyalty_points
// ✔ Ако няма → няма какво да вадиш → exit

// 4️⃣ Имаме ли реална стойност за вадене?

// ✔ Четеш loyalty_points_used
// ✔ Проверяваш дали е > 0
// ✔ Ако е 0 → exit

// 5️⃣ Вече изваждани ли са точки за тази поръчка?

// Много важно.

// Проверяваш в customer_loyalty_points:

// ✔ Има ли запис

// със същия order_id

// със отрицателни точки

// Ако има → exit (защита от двойно вадене)

// 6️⃣ Добавяш movement

// Добавяш нов запис:

// customer_id	points	order_id	description
// X	-150	1001	Points used for order

// ✔ points винаги отрицателно
// ✔ description ясно показва, че е redeem
    }
}
