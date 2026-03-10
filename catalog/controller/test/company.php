<?php
class ControllerTestCompany extends Controller {

    public function index() {

       
        $this->document->setTitle('Тест Company API');

        $this->session->data['eik_page_load'] = time();
        
        $this->session->data['eik_token'] = bin2hex(random_bytes(16));

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => 'Начало',
            'href' => $this->url->link('common/home')
        ];

        $data['breadcrumbs'][] = [
            'text' => 'Тест Company API',
            'href' => $this->url->link('test/company')
        ];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['footer'] = $this->load->controller('common/footer');
        $data['eik_token'] = $this->session->data['eik_token'];


        $this->response->setOutput(
            $this->load->view('test/company', $data)
        );


    }

    //  private function jsonError($message) {
    //     $this->response->addHeader('Content-Type: application/json');
    //     $this->response->setOutput(json_encode(['error' => $message]));
    // }

     public function eik() {

            $json = [];

            // IP базирано rate limiting
            $ip = $this->request->server['REMOTE_ADDR'];

            if (!isset($this->session->data['ip_rate'])) {
                $this->session->data['ip_rate'] = [];
            }

            if (isset($this->session->data['ip_rate'][$ip])) {

                if (time() - $this->session->data['ip_rate'][$ip] < 2) {
                    
                    $json['error'] = 'Прекалено много заявки.';
                }
            }

            $this->session->data['ip_rate'][$ip] = time();

                    
        // CSRF Токен проверка
        if (!isset($this->request->get['eik_token']) || !isset($this->session->data['eik_token']) || ($this->request->get['eik_token'] !== $this->session->data['eik_token'])) {
            $json['error'] = 'Невалиден защитен токен.';
            
        }

        //  Honeypot
        if (!empty($this->request->get['subject_line'])) {

            $this->log->write('Honeypot triggered by IP: ' . $this->request->server['REMOTE_ADDR']);

            $json['error'] = 'Security check failed';
        }


        //  AJAX проверка
        if (
            !isset($this->request->server['HTTP_X_REQUESTED_WITH']) ||
            strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'
        ) {

            $json['error'] = 'Невалидна заявка';
        }


        //  Проверка дали страницата е заредена
        if (isset($this->session->data['eik_page_load'])) {

            $duration = time() - $this->session->data['eik_page_load'];

            if ($duration < 2) {

                $this->log->write('Too fast request (bot?) IP: ' . $this->request->server['REMOTE_ADDR']);

                $json['error'] = 'Системна проверка. Моля, опитайте пак.';
                
            }
        }


        // //  Rate limit
        // if (isset($this->session->data['last_eik_search'])) {

        //     if (time() - $this->session->data['last_eik_search'] < 2) {

        //         $json['error'] = 'Моля, изчакайте малко преди следващото търсене.';
        //     }
        // }

        $this->session->data['last_eik_search'] = time();


        //  Валидация на ЕИК
        $eik = $this->request->get['eik'] ?? '';

        if (!preg_match('/^\d{9}$/', $eik)) {

            $json['error'] = 'Невалиден формат на ЕИК';
        }


        //  Модел
        if (!$json) {

        $this->load->model('tool/company');

        $result = $this->model_tool_company->getCompanyByEik($eik);

        if (isset($result['error'])) {
            $json['error'] = $result['error'];
        } else {
            $json = $result;
        }
    }


        //  Success
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}