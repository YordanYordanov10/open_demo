<?php
class ControllerCustomerCompanies extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('customer/companies');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer');

        $this->getList();
    }

    public function add() {
        $this->load->language('customer/companies');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_customer_customer->addCompany($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('customer/companies', 'user_token=' . $this->session->data['user_token'] . $this->getUrl(), true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('customer/companies');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_customer_customer->editCompany($this->request->get['company_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('customer/companies', 'user_token=' . $this->session->data['user_token'] . $this->getUrl(), true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('customer/companies');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $company_id) {
                $this->model_customer_customer->deleteCompany($company_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('customer/companies', 'user_token=' . $this->session->data['user_token'] . $this->getUrl(), true));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['filter_company'])) {
            $filter_company = $this->request->get['filter_company'];
        } else {
            $filter_company = '';
        }

        if (isset($this->request->get['filter_eik'])) {
            $filter_eik = $this->request->get['filter_eik'];
        } else {
            $filter_eik = '';
        }

        if (isset($this->request->get['filter_city'])) {
            $filter_city = $this->request->get['filter_city'];
        } else {
            $filter_city = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'company';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = $this->getUrl();

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('customer/companies', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['add'] = $this->url->link('customer/companies/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('customer/companies/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['companies'] = array();
        $data['customer_details'] = array();

        $filter_data = array(
            'filter_company' => $filter_company,
            'filter_eik'     => $filter_eik,
            'filter_city'    => $filter_city,
            'sort'           => $sort,
            'order'          => $order,
            'start'          => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'          => $this->config->get('config_limit_admin')
        );

        $company_total = $this->model_customer_customer->getTotalCustomerCompanies($filter_data);

        $results = $this->model_customer_customer->getCustomerCompanies($filter_data);

        $customer_results = $results;

        foreach ($results as $result) {
            $data['companies'][] = array(
                'company_id'  => $result['company_id'],
                'eik'         => $result['eik'],
                'company'     => $result['company'],
                'city'        => $result['city'],
                'address'     => $result['address'],
                'manager'     => $result['manager'],
                'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'edit'        => $this->url->link('customer/companies/edit', 'user_token=' . $this->session->data['user_token'] . '&company_id=' . $result['company_id'] . $url, true)
            );

        }

        foreach ($customer_results as $result) {
            $data['customer_details'][] = array(
                'company_id'      => $result['company_id'],
                'eik'             => $result['eik'],
                'company'         => $result['company'],
                'customer_name'   => trim($result['firstname'] . ' ' . $result['lastname']),
                'customer_group'  => $result['customer_group'],
                'email'           => $result['email'],
                'telephone'       => $result['telephone'],
                'status'          => $result['status'] ? 'Enabled' : 'Disabled',
                'newsletter'      => $result['newsletter'] ? 'Yes' : 'No',
                'safe'            => $result['safe'] ? 'Yes' : 'No',
                'date_added'      => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'edit_customer'   => $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['company_id'], true)
            );
        }

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_company'])) {
            $url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_eik'])) {
            $url .= '&filter_eik=' . urlencode(html_entity_decode($this->request->get['filter_eik'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_city'])) {
            $url .= '&filter_city=' . urlencode(html_entity_decode($this->request->get['filter_city'], ENT_QUOTES, 'UTF-8'));
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        $data['sort_company'] = $this->url->link('customer/companies', 'user_token=' . $this->session->data['user_token'] . '&sort=company' . $url, true);
        $data['sort_eik'] = $this->url->link('customer/companies', 'user_token=' . $this->session->data['user_token'] . '&sort=eik' . $url, true);
        $data['sort_city'] = $this->url->link('customer/companies', 'user_token=' . $this->session->data['user_token'] . '&sort=city' . $url, true);
        $data['sort_date_added'] = $this->url->link('customer/companies', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_company'])) {
            $url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_eik'])) {
            $url .= '&filter_eik=' . urlencode(html_entity_decode($this->request->get['filter_eik'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_city'])) {
            $url .= '&filter_city=' . urlencode(html_entity_decode($this->request->get['filter_city'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $company_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('customer/companies', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($company_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($company_total - $this->config->get('config_limit_admin'))) ? $company_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $company_total, ceil($company_total / $this->config->get('config_limit_admin')));

        $data['filter_company'] = $filter_company;
        $data['filter_eik'] = $filter_eik;
        $data['filter_city'] = $filter_city;

        $data['user_token'] = $this->session->data['user_token'];
        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('customer/company_list', $data));
    }

    protected function getForm() {
        $data['text_form'] = !isset($this->request->get['company_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['error_eik'] = isset($this->error['eik']) ? $this->error['eik'] : '';
        $data['error_company'] = isset($this->error['company']) ? $this->error['company'] : '';
        $data['error_city'] = isset($this->error['city']) ? $this->error['city'] : '';
        $data['error_address'] = isset($this->error['address']) ? $this->error['address'] : '';
        $data['error_manager'] = isset($this->error['manager']) ? $this->error['manager'] : '';

        $url = $this->getUrl();

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('customer/companies', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['company_id'])) {
            $data['action'] = $this->url->link('customer/companies/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('customer/companies/edit', 'user_token=' . $this->session->data['user_token'] . '&company_id=' . $this->request->get['company_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('customer/companies', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['company_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $company_info = $this->model_customer_customer->getCompany($this->request->get['company_id']);
        } else {
            $company_info = array();
        }

        $data['eik_readonly'] = isset($this->request->get['company_id']) ? true : false;

        if (isset($this->request->post['eik'])) {
            $data['eik'] = $this->request->post['eik'];
        } elseif (!empty($company_info)) {
            $data['eik'] = $company_info['eik'];
        } else {
            $data['eik'] = '';
        }

        if (isset($this->request->post['company'])) {
            $data['company'] = $this->request->post['company'];
        } elseif (!empty($company_info)) {
            $data['company'] = $company_info['company'];
        } else {
            $data['company'] = '';
        }

        if (isset($this->request->post['city'])) {
            $data['city'] = $this->request->post['city'];
        } elseif (!empty($company_info)) {
            $data['city'] = $company_info['city'];
        } else {
            $data['city'] = '';
        }

        if (isset($this->request->post['address'])) {
            $data['address'] = $this->request->post['address'];
        } elseif (!empty($company_info)) {
            $data['address'] = $company_info['address'];
        } else {
            $data['address'] = '';
        }

        if (isset($this->request->post['manager'])) {
            $data['manager'] = $this->request->post['manager'];
        } elseif (!empty($company_info)) {
            $data['manager'] = $company_info['manager'];
        } else {
            $data['manager'] = '';
        }

        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('customer/company_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'customer/companies')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!isset($this->request->post['eik']) || !preg_match('/^\d{9}$/', $this->request->post['eik'])) {
            $this->error['eik'] = $this->language->get('error_eik');
        }

        if ((utf8_strlen($this->request->post['company']) < 1) || (utf8_strlen($this->request->post['company']) > 255)) {
            $this->error['company'] = $this->language->get('error_company');
        }

        $company_id = isset($this->request->get['company_id']) ? (int)$this->request->get['company_id'] : 0;

        if ($company_id) {
            $company_info = $this->model_customer_customer->getCompany($company_id);

            if ($company_info && isset($company_info['eik']) && $this->request->post['eik'] !== $company_info['eik']) {
                $this->error['eik'] = $this->language->get('error_eik_locked');
            }
        }

        if (isset($this->request->post['eik']) && $this->model_customer_customer->getTotalCompaniesByEik($this->request->post['eik'], $company_id)) {
            $this->error['eik'] = $this->language->get('error_eik_exists');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'customer/companies')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    private function getUrl() {
        $url = '';

        if (isset($this->request->get['filter_company'])) {
            $url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_eik'])) {
            $url .= '&filter_eik=' . urlencode(html_entity_decode($this->request->get['filter_eik'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_city'])) {
            $url .= '&filter_city=' . urlencode(html_entity_decode($this->request->get['filter_city'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        return $url;
    }
}
