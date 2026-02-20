<?php

class ControllerAccountLoyaltyCoupon extends Controller {
   public function index() {

    if (!$this->customer->isLogged()) {
        $this->session->data['redirect'] = $this->url->link('account/loyalty_coupon', '', true);
        return $this->response->redirect($this->url->link('account/login', '', true));
    }

    $this->load->language('account/coupon');
    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('account/loyalty_coupon');
    $this->load->model('account/loyalty');

    $customer_id = $this->customer->getId();

   
    $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
    $limit = 10;
    $start = ($page - 1) * $limit;

    $coupon_total = $this->model_account_loyalty_coupon->getTotalCouponsByCustomerId($customer_id);

    $results = $this->model_account_loyalty_coupon->getCouponsByCustomerId($customer_id, $start, $limit);

    $data['coupons'] = [];

    foreach ($results as $result) {
        $data['coupons'][] = [
            'coupon_id'    => $result['coupon_id'],
            'code'         => $result['code'],
            'amount'       => $this->currency->format($result['amount'],$this->session->data['currency']),
            'points_spent' => $result['points_spent'],
            'status'       => $result['status'],
            'date_added'   => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
            'date_used'    => $result['date_used'] ? date($this->language->get('date_format_short'), strtotime($result['date_used'])) : '',
            'date_expired' => date($this->language->get('date_format_short'), strtotime($result['date_expired']))
        ];
    }

  
    $pagination = new Pagination();
    $pagination->total = $coupon_total;  
    $pagination->page  = $page;
    $pagination->limit = $limit;
    $pagination->url   = $this->url->link('account/coupon', 'page={page}', true);

    $data['pagination'] = $pagination->render();


    $data['continue'] = $this->url->link('account/account', '', true);

    $data['total'] = $this->model_account_loyalty->getCustomerPoints($this->customer->getId());

    $data['header'] = $this->load->controller('common/header');
    $data['footer'] = $this->load->controller('common/footer');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['column_right'] = $this->load->controller('common/column_right');
    $data['content_top'] = $this->load->controller('common/content_top');
    $data['content_bottom'] = $this->load->controller('common/content_bottom');

    $this->response->setOutput($this->load->view('account/loyalty_coupon', $data));
   
}

    public function generateCoupon(){
        
    if (!$this->customer->isLogged()) {
        $this->session->data['redirect'] = $this->url->link('account/loyalty_coupon', '', true);
        return $this->response->redirect($this->url->link('account/login', '', true));
    }

    $this->load->model('account/loyalty_coupon');
    $this->load->model('account/loyalty');
    $customer_id = $this->customer->getId();

    $json = array();

    if (isset($this->request->post['points']) && isset($this->request->post['amount'])) {
        $points = (int)$this->request->post['points'];
        $amount = (float)$this->request->post['amount'];

        $customer_points = $this->model_account_loyalty->getCustomerPoints($customer_id);
        
        if ($customer_points >= $points) {
            $code = substr(md5(uniqid(rand(), true)), 0, 10);
            $this->model_account_loyalty_coupon->addCoupon($customer_id, $code, $amount, $points);
            $this->model_account_loyalty->addTransaction($customer_id, -$points,'coupon', 'Get coupon for ' . $amount, null);
            $json['success'] = 'Купонът е успешно генериран!';
        } else {
            $json['error'] = 'Нямате достатъчно точки за този купон.';
        }
    } else {
        $json['error'] = 'Невалидни данни.';
    }


    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

}