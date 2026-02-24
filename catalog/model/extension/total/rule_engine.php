<?php
class ModelExtensionTotalRuleEngine extends Model {



    public function getTotal($total_data) {
        // 1. Извличане на правилата от БД
        $rules = $this->getRules();

        // Подготовка на контекста
        $subtotal = $this->cart->getSubTotal();

        $context = array(
            'customer_group_id' => $this->customer->isLogged() ? $this->customer->getGroupId() : $this->config->get('config_customer_group_id'),
            'store_id'          => $this->config->get('config_store_id'),
            'subtotal'          => $subtotal,
            'quantity_total'    => $this->cart->countProducts(),
            'product_ids'       => array_column($this->cart->getProducts(), 'product_id'),
            'shipping_method'   => $this->session->data['shipping_method']['code'] ?? null,
            'coupon'            => $this->session->data['coupon'] ?? null,
        );

 

        foreach ($rules as $rule) {
            $conditions = $this->getConditions($rule['rule_id']);
            $actions = $this->getActions($rule['rule_id']);

            // Проверка на условията
            if (!$this->evaluateConditions($conditions, $context)) {
                continue;
            }



            // Прилагане на действията (Actions)
           foreach ($actions as $action) {

                $discount_amount = 0;
                $eligible_subtotal = 0;

             foreach ($this->cart->getProducts() as $product) {

                    if (!$this->productHasActiveSpecial($product['product_id'])) {
                        $eligible_subtotal += $product['total'];
                    }
                }

                if ($eligible_subtotal <= 0) {
                    continue;
                }

                if ($action['type'] == 'percentage_discount') {
                    $discount_amount = $eligible_subtotal * ($action['value'] / 100);
                }

                if ($action['type'] == 'fixed_discount') {
                    $discount_amount = min($eligible_subtotal, (float)$action['value']);
                }

                if ($discount_amount > 0) {
                    $total_data['totals'][] = array(
                        'code'       => 'rule_engine',
                        'title'      => $rule['name'],
                        'value'      => -$discount_amount,
                        'sort_order' => (int)$this->config->get('total_rule_engine_sort_order')
                    );

                    $total_data['total'] -= $discount_amount;
                }
            }
        }
    }

    private function evaluateConditions($conditions, $context) {
        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $condition) {
            $left_value = $this->resolveType($condition['type'], $context);

        
            if (!$this->compare($left_value, $condition['operator'], $condition['value'])) {
                return false;
            }

            }
         return true;
    }

    private function resolveType($type, $context) {
        switch ($type) {
            case 'cart_total':
                return $context['subtotal'];
            default:
                return null;
        }
    }

    private function compare($left_value, $operator, $right_value) {
        // Логика за масиви (продукти и категории)
        if (is_array($left_value)) {
            foreach ($left_value as $val) {
                if ($this->compare($val, $operator, $right_value)) {
                    return true;
                }
            }
            return false;
        }

        // Подготовка на числови стойности
        $left  = is_numeric($left_value) ? (float)$left_value : $left_value;
        $right = is_numeric($right_value) ? (float)$right_value : $right_value;

        // Нормализиране на оператора (ако идва като '=' или 'equals')
        switch ($operator) {
            case '=':
            case 'equals':
            case 'equal':
                return $left == $right;
            case '>':
            case 'greater_than':
                return $left > $right;
            case '<':
            case 'less_than':
                return $left < $right;
            default:
                return false;
        }
    }

   private function productHasActiveSpecial($product_id) {

    $query = $this->db->query("
        SELECT product_special_id
        FROM " . DB_PREFIX . "product_special
        WHERE product_id = '" . (int)$product_id . "'
        AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'
        AND (date_start = '0000-00-00' OR date_start <= NOW())
        AND (date_end = '0000-00-00' OR date_end >= NOW())
        LIMIT 1
    ");

    return $query->num_rows > 0;
}

    // --- Database Helpers ---
    private function getRules() {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "rules WHERE status = '1' ORDER BY priority ASC")->rows;
    }

    private function getConditions($rule_id) {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "rule_conditions WHERE rule_id = '" . (int)$rule_id . "'")->rows;
    }

    private function getActions($rule_id) {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "rule_actions WHERE rule_id = '" . (int)$rule_id . "'")->rows;
    }

   
}