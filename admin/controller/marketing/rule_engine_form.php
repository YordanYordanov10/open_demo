<?php
class ControllerMarketingRuleEngineForm extends Controller {
    public function index() {
        $this->load->language('marketing/rule_engine');

        $this->load->model('marketing/rule_engine');

        if (isset($this->request->get['rule_id'])) {
            $rule_id = (int)$this->request->get['rule_id'];
            $rule_info = $this->model_marketing_rule_engine->getRuleEngine($rule_id);
            $data['action'] = $this->url->link('marketing/rule_engine/edit', 'user_token=' . $this->session->data['user_token'] . '&rule_id=' . $rule_id, true);
            $data['form_heading'] = $this->language->get('text_edit');
        } else {
            $rule_id = 0;
            $rule_info = array();
            $data['action'] = $this->url->link('marketing/rule_engine/add', 'user_token=' . $this->session->data['user_token'], true);
            $data['form_heading'] = $this->language->get('text_add');
        }

        $data['cancel'] = $this->url->link('marketing/rule_engine', 'user_token=' . $this->session->data['user_token'], true);

        // Fields with precedence: POST -> existing rule -> default
        $fields = array(
            'name' => '',
            'description' => '',
            'code' => '',
            'priority' => 0,
            'stop_processing' => 0,
            'status' => 1
        );

        foreach ($fields as $field => $default) {
            if (isset($this->request->post[$field])) {
                $data[$field] = $this->request->post[$field];
            } elseif (!empty($rule_info) && isset($rule_info[$field])) {
                $data[$field] = $rule_info[$field];
            } else {
                $data[$field] = $default;
            }
        }

       // Conditions
        if (isset($this->request->post['conditions'])) {
            $data['conditions'] = $this->request->post['conditions'];
        } elseif ($rule_id) {
            $data['conditions'] = $this->model_marketing_rule_engine->getRuleConditions($rule_id);
        } else {
            $data['conditions'] = array();
        }

        // Actions
        if (isset($this->request->post['actions'])) {
            $data['actions'] = $this->request->post['actions'];
        } elseif ($rule_id) {
            $data['actions'] = $this->model_marketing_rule_engine->getRuleActions($rule_id);
        } else {
        $data['actions'] = array();
        }       

        // Available types for selects
        $data['condition_types'] = array(
            'cart_total'     => 'Cart Total',
            'product_id'     => 'Product',
            'category_id'    => 'Category',
            'customer_group' => 'Customer Group'
        );

        $data['action_types'] = array(
            'percentage_discount' => 'Percentage Discount',
            'fixed_discount'      => 'Fixed Discount',
            'free_shipping'       => 'Free Shipping'
        );

        $data['heading_title'] = $this->language->get('heading_title');

        // Pull any validation errors from session (set by validateForm())
        $data['error_warning'] = '';
        $data['error_name'] = '';
        $data['error_actions'] = '';

        if (isset($this->session->data['form_errors'])) {
            $errors = $this->session->data['form_errors'];

            if (!empty($errors['warning'])) {
                $data['error_warning'] = $errors['warning'];
            }

            if (!empty($errors['name'])) {
                $data['error_name'] = $errors['name'];
            }

            if (!empty($errors['actions'])) {
                $data['error_actions'] = $errors['actions'];
            }

            unset($this->session->data['form_errors']);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('marketing/rule_engine_form', $data));
    }
}
