<?php
class ControllerTestExample extends Controller {

    public function index() {

        $data = [];

        $this->response->setOutput($this->load->view('test/example', $data));
    }

    public function ajax() {

        $json = [];

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            $json['success'] = true;

        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}