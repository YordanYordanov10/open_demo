<?php

class ControllerExtensionDashboardProfit extends Controller{
    
    public function index()
    {
        $this->load->language('extension/dashboard/profit');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_view'] = $this->language->get('text_view');

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('dashboard_profit', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/dashboard/profit', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/dashboard/profit', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true);

        if (isset($this->request->post['dashboard_profit_width'])) {
            $data['dashboard_profit_width'] = $this->request->post['dashboard_profit_width'];
        } else {
            $data['dashboard_profit_width'] = $this->config->get('dashboard_profit_width');
        }

        $data['columns'] = array();

        for ($i = 3; $i <= 12; $i++) {
            $data['columns'][] = $i;
        }

        if (isset($this->request->post['dashboard_profit_status'])) {
            $data['dashboard_profit_status'] = $this->request->post['dashboard_profit_status'];
        } else {
            $data['dashboard_profit_status'] = $this->config->get('dashboard_profit_status');
        }

        if (isset($this->request->post['dashboard_profit_sort_order'])) {
            $data['dashboard_profit_sort_order'] = $this->request->post['dashboard_profit_sort_order'];
        } else {
            $data['dashboard_profit_sort_order'] = $this->config->get('dashboard_profit_sort_order');
        }


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/dashboard/profit_form', $data));
    }



    public function dashboard()
    {

        $this->load->language('extension/dashboard/profit');
        $this->load->model('catalog/product');
        $this->load->model('sale/order');


        $data['user_token'] = $this->session->data['user_token'];
        $data['orders'] = array();
        $filter_data = array(
            'filter_order_status' => 5,
            'sort' => 'o.date_added',
            'order' => 'DESC',
            'start' => 0,
            'limit' => 5
        );

        $results = $this->model_sale_order->getOrders($filter_data);

        // Total revenue today
        $query = $this->db->query("
            SELECT SUM(o.total) as total 
            FROM " . DB_PREFIX . "order o
            WHERE DATE(o.date_added) = CURDATE()
            AND o.order_status_id = 5
        ");

        $revenue_today = $query->row['total'];

        // Total profit today
        $query = $this->db->query("
            SELECT SUM(op.profit) as total 
            FROM " . DB_PREFIX . "order_profit op
            LEFT JOIN " . DB_PREFIX . "order o 
            ON op.order_id = o.order_id
            WHERE DATE(o.date_added) = CURDATE()
            AND o.order_status_id = 5
        ");

        $profit_today = $query->row['total'];

        // Orders today
                $query = $this->db->query("
            SELECT COUNT(*) as total
            FROM " . DB_PREFIX . "order
            WHERE DATE(date_added) = CURDATE()
            AND order_status_id = 5
        ");

        $orders_today = $query->row['total'];

        // Margin
        $margin = 0;
        if ($revenue_today > 0) {
            $margin = ($profit_today / $revenue_today) * 100;
        }

        $currency = $this->config->get('config_currency');
        $currency_value = $this->currency->getValue($currency);

        $data['revenue_today'] = $this->currency->format($revenue_today, $currency, $currency_value);
        $data['profit_today'] = $this->currency->format($profit_today, $currency, $currency_value);
        $data['orders_today'] = $orders_today;
        $data['margin_today'] = round($margin, 2) . '%';

     
        foreach ($results as $result) {
            $profit_info = $this->model_sale_order->getOrderProfit($result['order_id']);

            // ако няма запис → пресмятаме
            if (!$profit_info) {
                $this->model_sale_order->calculateAndStoreOrderProfit($result['order_id']);
                $profit_info = $this->model_sale_order->getOrderProfit($result['order_id']);
            }

            $profit_value = (isset($profit_info['profit'])) ? $profit_info['profit'] : 0;
            if ($profit_value > 0) {
                $currentClass = 'text-success';
            } else {
                $currentClass = 'text-danger';
            }

           
            $data['revenue_link'] = $this->url->link('report/profit_stats/revenue', 'user_token=' . $this->session->data['user_token'], true);
            $data['profit_link'] = $this->url->link('report/profit_stats/profit', 'user_token=' . $this->session->data['user_token'], true);
            $data['margin_link'] = $this->url->link('report/profit_stats/margin', 'user_token=' . $this->session->data['user_token'], true);
            $data['orders_link'] = $this->url->link('report/profit_stats/orders', 'user_token=' . $this->session->data['user_token'], true);

            $data['orders'][] = array(
                'order_id' => $result['order_id'],
                'customer' => $result['customer'],
                'status' => $result['order_status'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                'cost' => $this->currency->format($profit_info['cost'], $result['currency_code'], $result['currency_value']),
                'loyalty_points' => $this->currency->format($profit_info['loyalty_points'], $result['currency_code'], $result['currency_value']),
                'category_promo' => $this->currency->format($profit_info['category_promo'], $result['currency_code'], $result['currency_value']),
                'loyalty_coupon' => $this->currency->format($profit_info['loyalty_coupon'], $result['currency_code'], $result['currency_value']),
                'profit' => $this->currency->format($profit_value, $result['currency_code'], $result['currency_value']),
                'view'       => $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'], true),
                'profit_class' => $currentClass
            );
        }
        return $this->load->view('extension/dashboard/profit_info', $data);
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/dashboard/profit')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
