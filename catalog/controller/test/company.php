<?php
class ControllerTestCompany extends Controller {

    public function index() {

        $this->response->setOutput(
            $this->load->view('test/company', [])
        );

    }

}