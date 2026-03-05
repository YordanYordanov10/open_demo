<?php
class ModelMarketingCategoryPromo extends Model
{


    public function getProductPromoData($product_id, $base_price, $special_price = null)
    {

        $this->load->model('catalog/product');

        $promotions = $this->getPromotions();
        $categories = $this->model_catalog_product->getCategories($product_id);

        $max_category_percent = 0;

        $hasDiscount = true;
        $final_price = $base_price;
        $discount_percent = 0;
        $discount_type = null;

        // Намираме най-голямата category отстъпка
        foreach ($promotions as $promotion) {
            foreach ($categories as $category) {
                if ((int)$promotion['category_id'] === (int)$category['category_id']) {
                    if ((float)$promotion['percent'] > $max_category_percent) {
                        $max_category_percent = (float)$promotion['percent'];
                    }
                }
            }
        }

        // Ако няма category промоция
        if ($max_category_percent <= 0) {
            $hasDiscount = false;
        }

        // Изчисляваме category цена
        $category_price = $base_price * (1 - $max_category_percent / 100);
        $category_discount = $base_price - $category_price;

        // Проверяваме дали има special
        $has_special = ($special_price !== null && $special_price !== false && $special_price > 0);

        if ($has_special) {

            $special_discount = $base_price - $special_price;

            // Сравняваме коя отстъпка е по-голяма
            if ($special_discount >= $category_discount) {

                $final_price = $special_price;
                $discount_percent = round(($special_discount / $base_price) * 100, 2);
                $discount_type = 'special';

            } else {

                $final_price = $category_price;
                $discount_percent = $max_category_percent;
                $discount_type = 'category';

            }
        } else {

            //  Няма special → връщаме category
                $final_price = $category_price;
                $discount_percent = $max_category_percent;
                $discount_type = 'category';

        }

        return array(
            'has_discount' => $hasDiscount,
            'final_price' => $final_price,
            'discount_percent' => $discount_percent,
            'discount_type' => $discount_type
        );
    }


    private function getPromotions()
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "category_discount WHERE status = '1'")->rows;
    }

    public function getPromotionByCategoryId($category_id)
    {

        $query = $this->db->query("
            SELECT percent, status 
            FROM " . DB_PREFIX . "category_discount
            WHERE category_id = '" . (int)$category_id . "'
            AND status = 1
            LIMIT 1
        ");

        return $query->row;
    }
}
