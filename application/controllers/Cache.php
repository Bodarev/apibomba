<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cache extends FrontEndController
{
    public function filters_for_category() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        if(!isset($params['category_id'])) {
            echo json_encode([]);
        } else {
            $filters = get_filters_for_category($lang, $params['category_id']);

            $response['filters'] = $filters;
            $response['tag_attributes'] = $this->create_tag_attributes($filters);
            echo json_encode($response);
        }
    }

    public function filters_for_set() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        $filters = get_filters_for_set($lang, $params['category_set_id'], $params['prod_ids'], $params['attribute_id_list']);
        echo json_encode($filters);
    }

    public function attributes_for_product() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        $response = get_attributes_for_product($lang, $params['attribute_set_id'], $params['product_id']);
        echo json_encode($response);
    }

    public function init_category_cache() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        if(!isset($params['id'])) {
            echo json_encode([]);
        } else {
            $result = init_category_cache($lang, $params['id']);

            echo json_encode($result);
        }
    }

    private function create_tag_attributes($filters): array
    {
        $tag_attributes = [];
        foreach($filters as $key=>$attribute) {
            $attribute['values'] = json_decode($attribute['values'],true);
            $attribute['values_ro'] = json_decode($attribute['values_ro'],true);
            if (empty($attribute['values'])) continue;
            foreach ($attribute['values_ro'] as $index=>$value) {
                $tag_attributes[$key]['type'] = $attribute['type'];
                if($value['value']!= '\N') {
                    $tag_attributes[$key]['values'][transliteration($value['value'])] = $attribute['values'][$index]['value'];
                }
            }
        }

        return $tag_attributes;
    }
}
