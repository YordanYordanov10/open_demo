<?php

class ControllerExtensionTotalTierDiscount extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/total/tier_discount');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('total_tier_discount', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link(
                'marketplace/extension',
                'user_token=' . $this->session->data['user_token'] . '&type=total',
                true
            ));
        }

        // --- Грешки за всяко поле ---
        $fields = ['tier1_min', 'tier1_percent', 'tier2_min', 'tier2_percent', 'vip_percent', 'status', 'sort_order'];
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
            'href' => $this->url->link('extension/total/tier_discount', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['action'] = $this->url->link('extension/total/tier_discount', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);

        // --- Зареждане на POST или config стойности ---
        $fields_post = [
            'total_tier_discount_status',
            'total_tier_discount_sort_order',
            'total_tier_discount_tier1_min',
            'total_tier_discount_tier1_percent',
            'total_tier_discount_tier2_min',
            'total_tier_discount_tier2_percent',
            'total_tier_discount_vip_percent'
        ];
        foreach ($fields_post as $field) {
            $data[$field] = isset($this->request->post[$field]) ? $this->request->post[$field] : $this->config->get($field);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');     

        $this->response->setOutput($this->load->view('extension/total/tier_discount', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/total/tier_discount')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        // --- Проверка на Tier 1 ---
        if (!isset($this->request->post['total_tier_discount_tier1_min']) || !is_numeric($this->request->post['total_tier_discount_tier1_min']) || $this->request->post['total_tier_discount_tier1_min'] < 0) {
            $this->error['tier1_min'] = $this->language->get('error_tier1_min');
        }

        if (!isset($this->request->post['total_tier_discount_tier1_percent']) || !is_numeric($this->request->post['total_tier_discount_tier1_percent']) || $this->request->post['total_tier_discount_tier1_percent'] < 0) {
            $this->error['tier1_percent'] = $this->language->get('error_tier1_percent');
        }

        // --- Проверка на Tier 2 ---
        if (!isset($this->request->post['total_tier_discount_tier2_min']) || !is_numeric($this->request->post['total_tier_discount_tier2_min']) || $this->request->post['total_tier_discount_tier2_min'] < 0) {
            $this->error['tier2_min'] = $this->language->get('error_tier2_min');
        }

        if (!isset($this->request->post['total_tier_discount_tier2_percent']) || !is_numeric($this->request->post['total_tier_discount_tier2_percent']) || $this->request->post['total_tier_discount_tier2_percent'] < 0) {
            $this->error['tier2_percent'] = $this->language->get('error_tier2_percent');
        }

        // --- Проверка на VIP ---
        if (!isset($this->request->post['total_tier_discount_vip_percent']) || !is_numeric($this->request->post['total_tier_discount_vip_percent']) || $this->request->post['total_tier_discount_vip_percent'] < 0) {
            $this->error['vip_percent'] = $this->language->get('error_vip_percent');
        }

        return !$this->error;
    }
}