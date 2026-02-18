<?php

class ControllerExtensionTotalLoyaltyPoints extends Controller{

    private $error = array ();


        public function install() {
            $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "loyalty_transaction` (
              `transaction_id` INT(11) NOT NULL AUTO_INCREMENT,
              `customer_id` INT(11) NOT NULL,
              `order_id` INT(11) NULL,
              `description` VARCHAR(255) NOT NULL,
              `points` INT(11) NOT NULL DEFAULT '0',
              `transaction_type` VARCHAR(20) NOT NULL,
              `status` TINYINT(1) NOT NULL DEFAULT '1',
              `date_expired` DATETIME NULL,
              `date_added` DATETIME NOT NULL, 
              `date_modified` DATETIME NULL,
              PRIMARY KEY (`transaction_id`),
              INDEX (`customer_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            $this->load->model('setting/event');

            $this->model_setting_event->addEvent(
            'loyalty_points_add',
            'catalog/model/checkout/order/addOrderHistory/after',
            'extension/module/loyalty_points/addPointsOnComplete'
            );

            $this->model_setting_event->addEvent(
                'loyalty_points_cancel',
                'catalog/model/checkout/order/addOrderHistory/after',
                'extension/module/loyalty_points/returnPointsOnCancel'
            );
        }

        public function uninstall() {
            $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "loyalty_transaction`;");

            $this->model_setting_event->deleteEventByCode('loyalty_points_add');

            $this->model_setting_event->deleteEventByCode('loyalty_points_cancel');
        }

    public function index(){

        $this->load->language('extension/total/loyalty_points');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

          if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('total_loyalty_points', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link(
                'marketplace/extension',
                'user_token=' . $this->session->data['user_token'] . '&type=total',
                true
            ));
        }

         // --- Грешки за всяко поле ---
        $fields = ['earn_rate_percent', 'points_value', 'points_to_redeem', 'max_order_percent', 'status', 'sort_order'];
        foreach ($fields as $field) {
            $data['error_' . $field] = isset($this->error[$field]) ? $this->error[$field] : '';
        }

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
            'href' => $this->url->link('extension/total/loyalty_points', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['action'] = $this->url->link('extension/total/loyalty_points', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);


        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

         // --- Зареждане на POST или config стойности ---
        $fields_post = [
            'total_loyalty_points_status',
            'total_loyalty_points_sort_order',
            'total_loyalty_points_percent',
            'total_loyalty_points_value',
            'total_loyalty_points_allow_redeem',
            'total_loyalty_points_to_redeem',
            'total_loyalty_points_max_order_percent',
            'total_loyalty_points_complete_status_id',
            'total_loyalty_points_canceled_status_id',
        ];
        foreach ($fields_post as $field) {
            $data[$field] = isset($this->request->post[$field]) ? $this->request->post[$field] : $this->config->get($field);
        }

        $data['loyalty'] = 

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');     
        

        $this->response->setOutput($this->load->view('extension/total/loyalty_points', $data));

    }

    protected function validate(){

        if(!$this->user->hasPermission('modify', 'extension/total/loyalty_points')){
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!isset($this->request->post['total_loyalty_points_percent']) || !is_numeric($this->request->post['total_loyalty_points_percent']) || $this->request->post['total_loyalty_points_percent'] < 0) {
            $this->error['points_percent'] = $this->language->get('error_earn_rate_percent');
        }

        if (!isset($this->request->post['total_loyalty_points_value']) || !is_numeric($this->request->post['total_loyalty_points_value']) || $this->request->post['total_loyalty_points_value'] < 0){
            $this->error['points_value'] = $this->language->get('error_points_value');
        }

        if (!isset($this->request->post['total_loyalty_points_to_redeem']) || !is_numeric($this->request->post['total_loyalty_points_to_redeem']) || $this->request->post['total_loyalty_points_to_redeem'] < 0){
            $this->error['points_to_redeem'] = $this->language->get('error_points_to_redeem');
        }

         if (!isset($this->request->post['total_loyalty_points_max_order_percent']) || !is_numeric($this->request->post['total_loyalty_points_max_order_percent']) || $this->request->post['total_loyalty_points_max_order_percent'] < 0) {
            $this->error['max_order_percent'] = $this->language->get('error_max_order_percent');
        }

        return !$this->error;
    
    }
       
 
}