<?php

class ModelExtensionTotalCategoryPromo extends Model
{

    public function getTotal($total_data)
    {

        $this->load->language('extension/total/category_promo');


        if (!$this->config->get('total_category_promo_status')) {
            return;
        }

        if (!$this->customer->isLogged()) {
            return;
        }

        $this->load->model('marketing/category_promo');
        +$this->load->model('catalog/product');

        $promotions = $this->getPromotions();
        $products_in_cart = $this->cart->getProducts();


        $discount = 0;

        foreach ($promotions as $promotion) {
            foreach ($products_in_cart as $product) {


                $product_categories = $this->model_catalog_product->getCategories($product['product_id']);

                $product_info = $this->model_catalog_product->getProduct($product['product_id']);

                if($product_info['special']) {
                    continue; 
                }

                foreach ($product_categories as $cat) {
                    if ($promotion['category_id'] == $cat['category_id']) {
                        $discount += $product['total'] * ($promotion['percent'] / 100);
                        break;
                    }
                }
            }
        }

        if ($discount > 0) {
            $total_data['totals'][] = array(
                'code'       => 'category_promo',
                'title'      => sprintf($this->language->get('text_category_promo')),
                'value'      => -$discount,
                'sort_order' => $this->config->get('total_category_promo_sort_order')
            );

            $total_data['total'] -= $discount;
        }
    }

    private function getPromotions()
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "category_discount WHERE status = '1'")->rows;
    }
}
