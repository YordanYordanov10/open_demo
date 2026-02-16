<?php

class ModelExtensionTotalTierDiscount extends Model{

    public function getTotal($total) {
        $this->load->language('extension/total/tier_discount');
        $this->load->model('account/order');

        if (!$this->config->get('total_tier_discount_status')) {
            return;
        }

        $subtotal = $this->cart->getSubTotal();
        $discount = 0;     
        $is_vip = false;
        


        if($this->customer->isLogged()){
            $total_spent = $this->model_account_order->getOrderTotalsById($this->customer->getId());

		 if( (float)$total_spent >= 1000 ) {
		  $is_vip = true;
         }
		}

        $discount_tier1_min = (float)$this->config->get('total_tier_discount_tier1_min');
        $discount_tier2_min = (float)$this->config->get('total_tier_discount_tier2_min');

        $discount_tier1_percent = (float)$this->config->get('total_tier_discount_tier1_percent');
        $discount_tier2_percent = (float)$this->config->get('total_tier_discount_tier2_percent');
        $discount_vip_percent = (float)$this->config->get('total_tier_discount_vip_percent');
       
        $totalDiscount = 0;

        if ($this->customer->isLogged()) {

        if ($is_vip) {
           
            if($subtotal >= $discount_tier2_min) {
                $totalDiscount = $discount_vip_percent + $discount_tier2_percent;
                $discount = $subtotal * ($totalDiscount  / 100);
            } elseif ($subtotal >= $discount_tier1_min) {
                $totalDiscount = $discount_vip_percent + $discount_tier1_percent;
                $discount = $subtotal * ($totalDiscount  / 100);
            
            } else {
                $totalDiscount = $discount_vip_percent;
                $discount = $subtotal * ($totalDiscount / 100);
               
            }
        }

        } else {

        if ($subtotal >= $discount_tier2_min) {
                $totalDiscount = $discount_tier2_percent;
                $discount = $subtotal * ($totalDiscount / 100);
            } elseif ($subtotal >= $discount_tier1_min) {
                $totalDiscount = $discount_tier1_percent;
                 $discount = $subtotal * ($totalDiscount / 100);
               
            }
        }

        if ($discount > 0) {
        $total['totals'][] = array(
            'code' => 'tier_discount',
            'title' => sprintf( $this->language->get('text_tier_discount'),$totalDiscount),
            'value' => -$discount,
            'sort_order' => $this->config->get('total_tier_discount_sort_order')
        );
        $total['total'] -= $discount;
        
        }

       

    }    
        
}