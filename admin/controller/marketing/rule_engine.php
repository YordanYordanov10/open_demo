<?php

class ControllerMarketingRuleEngine extends Controller{

    private $error = array();

    public function index() {
        $this->load->language('marketing/rule_engine');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/rule_engine');

        $this->getList();
    }

    public function add() {
        $this->load->language('marketing/rule_engine');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/rule_engine');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_marketing_rule_engine->addRuleEngine($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('marketing/rule_engine', 'user_token=' . $this->session->data['user_token'] . $url));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('marketing/rule_engine');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/rule_engine');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $rule_id = isset($this->request->get['rule_id']) ? $this->request->get['rule_id'] : (isset($this->request->get['rule_engine_id']) ? $this->request->get['rule_engine_id'] : 0);
            $this->model_marketing_rule_engine->editRuleEngine($rule_id, $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('marketing/rule_engine', 'user_token=' . $this->session->data['user_token'] . $url));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('marketing/rule_engine');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/rule_engine');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $rule_engine_id) {
                $this->model_marketing_rule_engine->deleteRuleEngine($rule_engine_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('marketing/rule_engine', 'user_token=' . $this->session->data['user_token'] . $url));
        }

        $this->getList();
    }

    protected function getList() {
       	if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_priority'])) {
			$filter_priority = $this->request->get['filter_priority'];
		} else {
			$filter_priority = '';
		}

		if (isset($this->request->get['filter_stop_processing'])) {
			$filter_stop_processing = $this->request->get['filter_stop_processing'];
		} else {
			$filter_stop_processing = '';
		}

        if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}



		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'm.name';
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

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('marketing/rule_engine', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('marketing/rule_engine/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('marketing/rule_engine/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['rules'] = array();

        $data['heading_title'] = $this->language->get('heading_title');

       

		$filter_data = array(
			'filter_name'       => $filter_name,
			'filter_priority'   => $filter_priority,
			'filter_stop_processing' => $filter_stop_processing,
			'filter_status'     => $filter_status,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

       $rules_total = $this->model_marketing_rule_engine->getTotalRuleEngines($filter_data);
        $results = $this->model_marketing_rule_engine->getRuleEngines($filter_data);

        $data['rules'] = array();

        // Define labels for mapping types to human readable text
        $condition_types = array(
            'cart_total'     => 'Cart Total',
            'product_id'     => 'Product',
            'category_id'    => 'Category',
            'customer_group' => 'Customer Group'
        );

        $action_types = array(
            'percentage_discount' => 'Percentage Discount',
            'fixed_discount'      => 'Fixed Discount',
            'free_shipping'       => 'Free Shipping'
        );

        foreach ($results as $result) {
            // Map condition/action keys to labels for display
            $conds = $this->model_marketing_rule_engine->getRuleConditions($result['rule_id']);
            foreach ($conds as &$c) {
                $c['type'] = isset($condition_types[$c['type']]) ? $condition_types[$c['type']] : $c['type'];
            }

            $acts = $this->model_marketing_rule_engine->getRuleActions($result['rule_id']);
            foreach ($acts as &$a) {
                $a['type'] = isset($action_types[$a['type']]) ? $action_types[$a['type']] : $a['type'];
            }

            $data['rules'][] = array(
                'rule_id'        => $result['rule_id'],
                'name'           => $result['name'],
                'conditions'     => $conds,
                'actions'        => $acts,
                'priority'       => $result['priority'],
                'stop_processing'=> $result['stop_processing'],
                'status'         => $result['status'],
                'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'edit'           => $this->url->link('marketing/rule_engine/edit', 'user_token=' . $this->session->data['user_token'] . '&rule_id=' . $result['rule_id'] . $url, true)
            );
        }

        $pagination = new Pagination();
		$pagination->total = $rules_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('marketing/rule_engine', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($rules_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($rules_total - $this->config->get('config_limit_admin'))) ? $rules_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $rules_total, ceil($rules_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_priority'] = $filter_priority;
		$data['filter_stop_processing'] = $filter_stop_processing;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

      $this->response->setOutput($this->load->view('marketing/rule_engine', $data));
    }
    

    protected function getForm() {
        return $this->load->controller('marketing/rule_engine_form');
    }

   protected function validateForm() {
    $this->load->language('marketing/rule_engine');

    // Permission check
    if (!$this->user->hasPermission('modify', 'marketing/rule_engine')) {
        $this->error['warning'] = $this->language->get('error_permission');
    }

    // Name validation
    if (!isset($this->request->post['name']) || 
        utf8_strlen(trim($this->request->post['name'])) < 3 || 
        utf8_strlen(trim($this->request->post['name'])) > 255) {

        $this->error['name'] = $this->language->get('error_name');
    }

    $valid_actions = [];

    if (!empty($this->request->post['actions']) && is_array($this->request->post['actions'])) {

        foreach ($this->request->post['actions'] as $action) {

            $type  = isset($action['type']) ? trim($action['type']) : '';
            $value = isset($action['value']) ? trim($action['value']) : '';

            if ($type === '') {
                continue;
            }

            // free_shipping не изисква стойност
            if ($type === 'free_shipping') {
                $value = 0;
            }

            // numeric validation за discount типове
            if (in_array($type, ['percentage_discount', 'fixed_discount'])) {
                if ($value === '' || !is_numeric($value)) {
                    continue;
                }

                $value = (float)$value;
            }

            $valid_actions[] = [
                'type'  => $type,
                'value' => $value
            ];
        }
    }

    if (empty($valid_actions)) {
        $this->error['actions'] = $this->language->get('error_actions');
    } else {
        // replace original POST actions with cleaned version
        $this->request->post['actions'] = $valid_actions;
    }

   
    if (!empty($this->request->post['conditions']) && is_array($this->request->post['conditions'])) {

        $valid_conditions = [];

        foreach ($this->request->post['conditions'] as $condition) {

            $type     = isset($condition['type']) ? trim($condition['type']) : '';
            $operator = isset($condition['operator']) ? trim($condition['operator']) : '';
            $value    = isset($condition['value']) ? trim($condition['value']) : '';

            if ($type === '' || $operator === '' || $value === '') {
                continue;
            }

            $valid_conditions[] = [
                'type'     => $type,
                'operator' => $operator,
                'value'    => $value
            ];
        }

        $this->request->post['conditions'] = $valid_conditions;
    }

   
    if ($this->error) {
        $this->session->data['form_errors'] = $this->error;
        return false;
    }

    return true;
}

    protected function validateDelete() {
        return true;
    }
}