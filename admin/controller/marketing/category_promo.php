<?php

class ControllerMarketingCategoryPromo extends Controller
{

    private $error = array();

    public function index()
    {

        $this->load->language('marketing/category_promo');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/category_promo');

        $this->getList();
    }


    public function add()
    {

        $this->load->language('marketing/category_promo');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/category_promo');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_marketing_category_promo->addPromotion($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketing/category_promo', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    public function edit()
    {

        $this->load->language('marketing/category_promo');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/category_promo');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_marketing_category_promo->editPromotion($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketing/category_promo', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    public function delete()
    {

        $this->load->language('marketing/category_promo');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/category_promo');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $id) {
                $this->model_marketing_category_promo->deletePromotion($id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketing/category_promo', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getList();
    }



    public function getList()
    {

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => 'Home',
            'href' => $this->url->link(
                'common/dashboard',
                'user_token=' . $this->session->data['user_token'],
                true
            )
        ];

        $data['breadcrumbs'][] = [
            'text' => 'Category Promotions',
            'href' => $this->url->link(
                'marketing/category_promo',
                'user_token=' . $this->session->data['user_token'],
                true
            )
        ];

        $data['add'] = $this->url->link('marketing/category_promo/add', 'user_token=' . $this->session->data['user_token'], true);
        $data['delete'] = $this->url->link('marketing/category_promo/delete', 'user_token=' . $this->session->data['user_token'], true);

        $data['category_promo'] = array();

        $results = $this->model_marketing_category_promo->getPromotions();

        foreach ($results as $result) {
            $data['category_promo'][] = array(
                'category_promo_id'  => $result['id'],
                'category_id'        => $result['name'],
                'percent'           => $result['percent'],
                'status'            => $result['status'],
                'edit'              => $this->url->link('marketing/category_promo/edit', 'user_token=' . $this->session->data['user_token'] . '&category_promo_id=' . $result['id'], true)
            );
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('marketing/category_promo_list', $data));
    }



    protected function getForm()
    {

        $data['text_form'] = !isset($this->request->get['category_promo_id'])  ? 'Add Promotion' : 'Edit Promotion';


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }


        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => 'Home',
            'href' => $this->url->link(
                'common/dashboard',
                'user_token=' . $this->session->data['user_token'],
                true
            )
        ];

        $data['breadcrumbs'][] = [
            'text' => 'Category Promotions',
            'href' => $this->url->link(
                'marketing/category_promo',
                'user_token=' . $this->session->data['user_token'],
                true
            )
        ];

        if (!isset($this->request->get['category_promo_id'])) {
            $data['action'] = $this->url->link(
                'marketing/category_promo/add',
                'user_token=' . $this->session->data['user_token'],
                true
            );
        } else {
            $data['action'] = $this->url->link(
                'marketing/category_promo/edit',
                'user_token=' . $this->session->data['user_token'] .
                    '&category_promo_id=' . $this->request->get['category_promo_id'],
                true
            );
        }

        $data['cancel'] = $this->url->link(
            'marketing/category_promo',
            'user_token=' . $this->session->data['user_token'],
            true
        );

        $this->load->model('marketing/category_promo');
        $this->load->model('catalog/category');


        if (
            isset($this->request->get['category_promo_id'])
            && $this->request->server['REQUEST_METHOD'] != 'POST'
        ) {

            $promo_info = $this->model_marketing_category_promo
                ->getPromotion($this->request->get['category_promo_id']);
        } else {
            $promo_info = [];
        }


        $data['categories'] = $this->model_catalog_category->getCategories([]);


        if (isset($this->request->post['category_id'])) {
            $data['category_id'] = $this->request->post['category_id'];
        } elseif (!empty($promo_info)) {
            $data['category_id'] = $promo_info['category_id'];
        } else {
            $data['category_id'] = 0;
        }


        if (isset($this->request->post['percent'])) {
            $data['percent'] = $this->request->post['percent'];
        } elseif (!empty($promo_info)) {
            $data['percent'] = $promo_info['percent'];
        } else {
            $data['percent'] = '';
        }


        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($promo_info)) {
            $data['status'] = $promo_info['status'];
        } else {
            $data['status'] = 1;
        }


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('marketing/category_promo_form', $data));
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'marketing/category_promo')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }


        if ($this->request->post['percent'] === '' || $this->request->post['percent'] < 0 || $this->request->post['percent'] > 99) {
            $this->error['warning'] = 'Discount percent must be between 0 and 99!';
        }


        $rule_info = $this->model_marketing_category_promo->getPromotionByCategoryId($this->request->post['category_id']);

        if ($rule_info) {
            // Ако сме в режим "Добавяне" (нямаме ID в URL-а)
            if (!isset($this->request->get['category_promo_id'])) {
                $this->error['warning'] = 'A promotion for this category already exists!';
            }
            // Ако сме в режим "Редакция" (имаме ID), но намереното правило в базата е с друго ID
            elseif ($rule_info['id'] != $this->request->get['category_promo_id']) {
                $this->error['warning'] = 'A promotion for this category already exists!';
            }
        }

        return !$this->error;
    }
    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'marketing/category_promo')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
