<?php

class ControllerAccountLoyalty extends Controller {
    public function index() {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/loyalty', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }


        $this->load->language('account/loyalty');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', '', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('account/loyalty', '', true)
        );

        $this->load->model('account/loyalty');

        
		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 10;
        $data['loyalty'] = array();

		$filter_data = array(
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		);


        $reward_total = $this->model_account_loyalty->getCustomerPoints($this->customer->getId());

        foreach ($this->model_account_loyalty->getTransactions($filter_data) as $result) {
            $data['loyalty'][] = array(
                'order_id'    => $result['order_id'],
                'points'      => $result['points'],
                'description' => $result['description'],
                'transaction_type' => $result['transaction_type'],
                'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'href'        => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], true)
            );
        }

        $pagination = new Pagination();
        $pagination->total = $reward_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link('account/loyalty', 'page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['continue'] = $this->url->link('account/account', '', true);

        $data['total'] = $this->model_account_loyalty->getCustomerPoints($this->customer->getId());

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('account/loyalty', $data));
    }
}