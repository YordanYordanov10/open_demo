<?php
class ControllerApiCompany extends Controller {

    public function eik() {

        $this->load->model('tool/company');

        $eik = $this->request->get['eik'] ?? '';
        $data = $this->model_tool_company->getCompanyByEik($eik);

        $json = [];

        if (!empty($data)) {
            $json = $data;
        } else {
            $json['error'] = 'Company not found';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}