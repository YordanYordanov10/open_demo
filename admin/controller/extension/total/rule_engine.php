<?php

class ControllerExtensionTotalRuleEngine extends Controller{

    private $error = array ();

    public function index(){

        $this->load->language('extension/total/rule_engine');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

       if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->model_setting_setting->editSetting('total_rule_engine', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link(
                'marketplace/extension',
                'user_token=' . $this->session->data['user_token'] . '&type=total',
                true
            ));
        }

         // --- Грешки за всяко поле ---
        $fields = ['status', 'sort_order'];
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
            'href' => $this->url->link('extension/total/rule_engine', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['action'] = $this->url->link('extension/total/rule_engine', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);


        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

          // --- Зареждане на POST или config стойности ---
        $fields_post = [
            'total_rule_engine_status',
            'total_rule_engine_sort_order',
        ];
        foreach ($fields_post as $field) {
            $data[$field] = isset($this->request->post[$field]) ? $this->request->post[$field] : $this->config->get($field);
        }

         $data['header'] = $this->load->controller('common/header');
         $data['column_left'] = $this->load->controller('common/column_left');
         $data['footer'] = $this->load->controller('common/footer');     
        

        $this->response->setOutput($this->load->view('extension/total/rule_engine', $data));
    }



}