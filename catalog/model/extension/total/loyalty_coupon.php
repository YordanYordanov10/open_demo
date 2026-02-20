<?php
class ModelExtensionTotalLoyaltyCoupon extends Model {
    public function getTotal($total_data) {
    if (!$this->config->get('total_loyalty_coupon_status')) {
        return;
    }

    if (!$this->customer->isLogged()) {
        return;
    }

    $this->load->language('extension/total/loyalty_coupon');
    $this->load->model('account/loyalty_coupon');

    $customer_id = $this->customer->getId();
    $coupon = $this->model_account_loyalty_coupon->getOldestCouponByCustomerId($customer_id);

    if (!$coupon || (int)$coupon['status'] !== 1) {
        return;
    }

    $subtotal = $this->cart->getSubTotal();
    $discount = min((float)$coupon['amount'], $subtotal);

    if ($discount > 0) {
        $this->session->data['loyalty_coupon'] = [
            'coupon_id' => $coupon['coupon_id'],
            'code'      => $coupon['code'],
            'amount'    => $discount
        ];

        $total_data['totals'][] = array(
            'code'       => 'loyalty_coupon',
            'title'      => sprintf($this->language->get('text_loyalty_coupon'), $coupon['code']),
            'value'      => -$discount,
            'sort_order' => $this->config->get('total_loyalty_coupon_sort_order')
        );

        $total_data['total'] -= $discount;
    }
}
}