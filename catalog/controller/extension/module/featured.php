<?php
class ControllerExtensionModuleFeatured extends Controller
{
	public function index($setting)
	{
		$this->load->language('extension/module/featured');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		if (!empty($setting['product'])) {
			$products = array_slice($setting['product'], 0, (int)$setting['limit']);

			foreach ($products as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				$this->load->model('marketing/category_promo'); // Load the promo model


				if ($product_info) {
					$promo_data = $this->model_marketing_category_promo->getProductPromoData(
						$product_info['product_id'],
						$product_info['price'],
						$product_info['special']
					);

					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$data['price'] = false;
					}

					if (!is_null($product_info['special']) && (float)$product_info['special'] >= 0) {

						if ($promo_data['has_discount'] && $promo_data['discount_type'] === 'category') {
							$final_price_value = $promo_data['final_price'];
						} else {
							$final_price_value = $product_info['special'];
						}
						$data['special'] = $this->currency->format($this->tax->calculate($final_price_value, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						$tax_price = $this->tax->calculate($final_price_value, $product_info['tax_class_id'], $this->config->get('config_tax'));
					} else if ($promo_data['has_discount'] && $promo_data['discount_type'] === 'category') {

						$final_price_value = $promo_data['final_price'];
						$data['special'] = $this->currency->format($this->tax->calculate($final_price_value, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						$tax_price = $this->tax->calculate($final_price_value, $product_info['tax_class_id'], $this->config->get('config_tax'));
					} else {

						$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						$data['special'] = false;
						$tax_price = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
					}


					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}


					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}

					$data['products'][] = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'name'        => $product_info['name'],
						'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $data['price'],
						'special'     => $data['special'],
						'tax' => $this->currency->format($tax_price,$this->session->data['currency']),
						'rating'      => $rating,
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}

			if ($data['products']) {
				return $this->load->view('extension/module/featured', $data);
			}
		}
	}
}
