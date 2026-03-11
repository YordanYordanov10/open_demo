<?php
class ControllerTestCompany extends Controller
{

    public function index()
    {


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


        $this->response->setOutput($this->load->view('test/company', $data));
    }



    public function eik()
    {

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



        $this->session->data['last_eik_search'] = time();


        //  Валидация на ЕИК
        $eik = $this->request->get['eik'] ?? '';

        if (!preg_match('/^\d{9}$/', $eik)) {

            $json['error'] = 'Невалиден формат на ЕИК';
        }


        //  Модел
        if (!$json) {

            $this->load->model('tool/company');

            $company = $this->model_tool_company->getCompanyByEik($eik);
           
            if ($company) {
                $json = [
                    'name' => $company['company'],
                    'city' => $company['city'],
                    'address' => $company['address'],
                    'manager' => $company['manager'],
                    'source' => 'db'
                ];
            } else {

                $result = $this->model_tool_company->getCompanyFromApi($eik);

                if (isset($result['error'])) {
                    $json['error'] = $result['error'];
                } else {
                    $json = $result;
                }
            }
        }


        //  Success
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function submit()
    {
        $json = [];


        if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
            $json['error'] = 'Невалидна заявка.';
        }

        if (!empty($this->request->post['subject_line'])) {
            // $json['error'] = 'Bot detected!';
            $json['success'] = 'Заявката е получена!'; // Lie to the bot
            $this->response->setOutput(json_encode($json));
            return;
        }

        if (!isset($this->request->post['token']) || $this->request->post['token'] !== $this->session->data['eik_token']) {
            $json['error'] = 'Невалидна сесия. Моля, презаредете страницата.';
        }

        //  Валидация на ЕИК
        $eik = $this->request->post['eik'] ?? '';

        if (!preg_match('/^\d{9}$/', $eik)) {

            $json['error'] = 'Невалиден формат на ЕИК';
        }

        //  AJAX проверка
        if (
            !isset($this->request->server['HTTP_X_REQUESTED_WITH']) ||
            strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'
        ) {

            $json['error'] = 'Невалидна заявка';
        }

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




        if (!$json) {

            $eik     = trim($this->request->post['eik'] ?? '');
            $company = trim($this->request->post['company'] ?? '');
            $manager = trim($this->request->post['manager'] ?? '');
            $address = trim($this->request->post['address'] ?? '');
            $city    = trim($this->request->post['city'] ?? '');


            if (empty($eik)) {
                $json['errors']['eik'] = 'ЕИК е задължително поле.';
            }
            if (empty($company)) {
                $json['errors']['company'] = 'Име на фирмата е задължително поле.';
            }
            if (empty($manager)) {
                $json['errors']['manager'] = 'Управител е задължително поле.';
            }
            if (empty($address)) {
                $json['errors']['address'] = 'Адрес е задължително поле.';
            }
            if (empty($city)) {
                $json['errors']['city'] = 'Град е задължително поле.';
            }



            if (!$json) {
                $this->load->model('tool/company');

                $data = [
                    'eik'     => $eik,
                    'company' => $company,
                    'manager' => $manager,
                    'address' => $address,
                    'city'    => $city
                ];

                if ($this->model_tool_company->checkEikExists($eik)) {
                    $this->model_tool_company->updateCompanyData($data);
                    $json['success'] = 'Данните бяха обновени!';
                } else {
                    $this->model_tool_company->saveCompanyData($data);
                    $json['success'] = 'Фирмата беше записана!';
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
