<?php

class ControllerExtensionTotalLoyaltyPoints extends Controller{

    private $error = array ();

    public function index(){

        $this->load->language('extension/total/loyalty_points');
        $this->document->setTitle($this->language->get('headin_tittle'));

        $this->load->model('setting/setting');

        


    }
}