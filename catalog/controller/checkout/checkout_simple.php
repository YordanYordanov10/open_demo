<?php
class ControllerCheckoutCheckoutSimple extends Controller {
    public function index() {
        $this->document->setTitle('Моето плащане');
        
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        // Load model for images
        $this->load->model('tool/image');




        // Load products from the cart and pass them to the view
        $products = $this->cart->getProducts();

        foreach ($products as &$product) {
            $product_total = 0;
            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }
            
            if ($product['minimum'] > $product_total) {
                $this->response->redirect($this->url->link('checkout/cart'));
            }

            if ($product['image']) {
                $product['thumb'] = $this->model_tool_image->resize($product['image'], 100, 100);
            } else {
                $product['thumb'] = '';
            }

        }   
        $data['products'] = $products;

        // echo '<pre>';
        // print_r($products);
        // die();

        // $data['products'][] = [
        //     'cart_id'   => $product['cart_id'],
        //     'thumb'     => $product['thumb'],
        //     'name'      => $product['name'],
        //     'model'     => $product['model'],
        //     'quantity'  => $product['quantity'],
        //     'price'     => $this->currency->format($product['price'], $this->session->data['currency']),
            // 'total'     => $this->currency->format($product['total'], $this->session->data['currency']),
        //     'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id'])
        // ];

        // Тук ще заредим ТВОЯ чист Twig
        $this->response->setOutput($this->load->view('checkout/checkout_simple', $data));
    }

   public function edit() {
    $this->load->language('checkout/cart');
    $json = array();

    // Проверяваме дали са изпратени key и quantity
    if (isset($this->request->post['key']) && isset($this->request->post['quantity'])) {
        
        // Обновяваме конкретния продукт
        $this->cart->update($this->request->post['key'], $this->request->post['quantity']);

        // Изчистваме сесийните данни, за да се преизчислят методите и сумите
        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['payment_methods']);
        unset($this->session->data['reward']);
        
        $json['success'] = true;
    } else {
        $json['error'] = 'Missing data';
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

    
}