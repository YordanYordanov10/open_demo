<?php

class ControllerExtensionTotalLoyaltyCoupon extends Controller {

    private $error = array();
    
    public function install() {
    $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "loyalty_coupon` (
            `coupon_id` INT(11) NOT NULL AUTO_INCREMENT,
            `customer_id` INT(11) NOT NULL,
            `code` VARCHAR(64) NOT NULL,
            `amount` DECIMAL(15,4) NOT NULL,
            `points_spent` INT(11) NOT NULL,
            `status` TINYINT(1) NOT NULL DEFAULT '1',
            `order_id` INT(11) DEFAULT NULL,
            `date_added` DATETIME NOT NULL,
            `date_used` DATETIME DEFAULT NULL,
            `date_expired` DATETIME DEFAULT NULL,
            PRIMARY KEY (`coupon_id`),
            UNIQUE KEY `code` (`code`),
            KEY `customer_id` (`customer_id`),
            KEY `status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "loyalty_coupon`;");
    }

    public function index() {
        $this->load->language('extension/total/loyalty_coupon');

        $data['heading_title'] = $this->language->get('heading_title');

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('total_loyalty_coupon', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link(
                'marketplace/extension',
                'user_token=' . $this->session->data['user_token'] . '&type=total',
                true
            ));
        }

       
         // --- Грешки за всяко поле ---
         $fields = [  'status', 'sort_order'];
         foreach ($fields as $field) {
         $data['error_' . $field] = isset($this->error[$field]) ? $this->error[$field] : '';
        
         // --- Breadcrumbs ---
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true)
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/total/loyalty_coupon', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['action'] = $this->url->link('extension/total/loyalty_coupon', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);


        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

         // --- Зареждане на POST или config стойности ---
        $fields_post = [
            'total_loyalty_coupon_status',
            'total_loyalty_coupon_sort_order',
        ];
        foreach ($fields_post as $field) {
            $data[$field] = isset($this->request->post[$field]) ? $this->request->post[$field] : $this->config->get($field);
        }

       
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');     
        

        $this->response->setOutput($this->load->view('extension/total/loyalty_coupon', $data));
           
           
        }   
    }
    

    public function validate() {
        if (!$this->user->hasPermission('modify', 'extension/total/loyalty_coupon')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}