<?php

class ControllerExtensionTotalTierDiscount extends Controller{
    private $error = array();

    public function index() {
        $this->load->language('extension/total/tier_discount');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('total_tier_discount', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true));
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
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/total/tier_discount', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/total/tier_discount', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);

        if (isset($this->request->post['total_tier_discount_status'])) {
            $data['total_tier_discount_status'] = $this->request->post['total_tier_discount_status'];
        } else {
            $data['total_tier_discount_status'] = $this->config->get('total_tier_discount_status');
        }

        if (isset($this->request->post['total_tier_discount_sort_order'])) {
            $data['total_tier_discount_sort_order'] = $this->request->post['total_tier_discount_sort_order'];
        } else {
            $data['total_tier_discount_sort_order'] = $this->config->get('total_tier_discount_sort_order');
        }

        if (isset($this->request->post['total_tier_discount_tier1_min'])) {
            $data['total_tier_discount_tier1_min'] = $this->request->post['total_tier_discount_tier1_min'];
        } else {
            $data['total_tier_discount_tier1_min'] = $this->config->get('total_tier_discount_tier1_min');
        }

        if (isset($this->request->post['total_tier_discount_tier1_percent'])) {
            $data['total_tier_discount_tier1_percent'] = $this->request->post['total_tier_discount_tier1_percent'];
        } else {
            $data['total_tier_discount_tier1_percent'] = $this->config->get('total_tier_discount_tier1_percent');
        }

        if (isset($this->request->post['total_tier_discount_tier2_min'])) {
            $data['total_tier_discount_tier2_min'] = $this->request->post['total_tier_discount_tier2_min'];
        } else {
            $data['total_tier_discount_tier2_min'] = $this->config->get('total_tier_discount_tier2_min');
        }

        if (isset($this->request->post['total_tier_discount_tier2_percent'])) {
            $data['total_tier_discount_tier2_percent'] = $this->request->post['total_tier_discount_tier2_percent'];
        } else {
            $data['total_tier_discount_tier2_percent'] = $this->config->get('total_tier_discount_tier2_percent');
        }

        if (isset($this->request->post['total_tier_discount_vip_percent'])) {
            $data['total_tier_discount_vip_percent'] = $this->request->post['total_tier_discount_vip_percent'];
        } else {
            $data['total_tier_discount_vip_percent'] = $this->config->get('total_tier_discount_vip_percent');
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

        // if (!is_numeric($this->request->post['total_tier_discount_tier1_min']) || $this->request->post['total_tier_discount_tier1_min'] < 0) {
        //     $this->error['warning'] = 'Tier 1 Minimum must be a non-negative number!';
        // }

        // if (!is_numeric($this->request->post['total_tier_discount_tier2_min']) || $this->request->post['total_tier_discount_tier2_min'] < 0) {
        //     $this->error['warning'] = 'Tier 2 Minimum must be a non-negative number!';
        // }

        // if (!is_numeric($this->request->post['total_tier_discount_vip_percent']) || $this->request->post['total_tier_discount_vip_percent'] < 0) {
        //     $this->error['warning'] = 'VIP Percent must be a non-negative number!';
        // }
        //     if (!is_numeric($this->request->post['total_tier_discount_tier1_percent']) || $this->request->post['total_tier_discount_tier1_percent'] < 0) {
        //         $this->error['warning'] = 'Tier 1 Percent must be a non-negative number!';
        //     }
        //     if (!is_numeric($this->request->post['total_tier_discount_tier2_percent']) || $this->request->post['total_tier_discount_tier2_percent'] < 0) {
        //         $this->error['warning'] = 'Tier 2 Percent must be a non-negative number!';
        //     }

        return !$this->error;
    }
}