# OpenCart Coding Patterns

## Controller JSON Example

public function eik() {

    $json = [];

    if (isset($this->request->get['eik'])) {

        $this->load->model('test/company');

        $json = $this->model_test_company->getCompany($this->request->get['eik']);
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

## Load Model

$this->load->model('test/company');

## Model Query

$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "company WHERE eik = '" . $this->db->escape($eik) . "'");

return $query->row;