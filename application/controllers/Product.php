<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends FrontEndController
{
    public function search() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        $this->load->model('product_model');
        $products = $this->product_model->search($lang, $params['query'], false, $params['limit']);

        if($products) {
            $product_ids = array_map(function($item){return $item->id;}, $products);
            $labels = $this->db->select("product_id as product_id, name_$lang as title, color as color")->where_in("product_id", $product_ids)->get('product_label')->result();
            foreach ($products as &$product) {
                $product->labels = [];
                foreach ($labels as $label) {
                    if ($label->product_id == $product->id) $product->labels[] = $label;
                }
            }
        }

        echo json_encode($products);
    }

    public function find() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        $this->load->model('product_model');
        $products = $this->product_model->get_products_by_ids($lang, $params['ids'], $params['limit']);
        $product_ids = array_map(function($item) {return $item->id;}, $products);

        if($product_ids) {
            $labels = $this->db->select("product_id as product_id, name_$lang as title, color as color")->where_in("product_id", $product_ids)->get('product_label')->result();
            foreach ($products as &$product) {
                $product->labels = [];
                foreach ($labels as $label) {
                    if ($label->product_id == $product->id) $product->labels[] = $label;
                }
            }
        }

        echo json_encode($products);
    }

    public function filtered() {

        $this->load->model('product_model');
        $this->load->model('brands_model');

        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        $this->_define_constants($lang);

        $get = $params['get'];

        $page = @$get['page'];
        if (empty($page) || $page<1) $page = 1;

        $limit = @$get['limit'];
        $limit_view = [32 => EACH_30, 64 => EACH_60, 9999 => ALL];
        $limit_code = [32, 64, 9999];
        if (empty($limit) || !in_array($limit, $limit_code)) {
            $limit = $limit_code[0];
        }

        $start = ($page-1) * $limit;

        $query = @$get['query'];

        $sort = @$get['sort'];
        if (empty($sort) || $sort<0 || $sort>5) $sort = 0;

        $sorter_view = array(
            0=>SORT_BY_PRICE_ASC,
            1=>SORT_BY_PRICE_DESC,
            2=>SORT_BY_POPULAR,
            3=>SORT_BY_SCORE,
            4=>SORT_BY_REDUCTION,
        );

        $sorter_code = array(
            0=>"display desc, discounted_price asc, id asc",
            1=>"display desc, discounted_price desc, id asc",
            2=>"display desc, is_popular desc, price asc, id asc",
            3=>"display desc, score desc, price asc, id asc",
            4=>"display desc, diff desc, price desc, id asc",
        );

        $get_store = @$get['store'];
        if (empty($get_store) || $get_store<1) $get_store = 208679;

        $min_price = 99999999;
        $max_price = 0;

        $all_for_category_products = $this->product_model->get_products_for_category($lang, $params['category_id'], $params['ids'], ['query'=>$query]);
        $product_ids = array_map(function ($item){return$item->id;},$all_for_category_products);

        $spoiler = [];

        if($params['first'] && !$params['terminalFlag'] && $query == '' && empty($params['ids'])) {

            usort($all_for_category_products, function($a, $b) {
                return (int) ($a->id < $b->id);
            });

            $spoiler[0][0] = @$all_for_category_products[0];
            $spoiler[0][1] = @$all_for_category_products[1];
            $spoiler[0][2] = @$all_for_category_products[2];

            usort($all_for_category_products, function($a, $b) {
                return (int) ($a->price > $b->price);
            });

            $spoiler[1][0] = @$all_for_category_products[0];
            $spoiler[1][1] = @$all_for_category_products[1];
            $spoiler[1][2] = @$all_for_category_products[2];

            usort($all_for_category_products, function($a, $b) {
                return (int) ($a->price < $b->price);
            });

            $spoiler[2][0] = @$all_for_category_products[0];
            $spoiler[2][1] = @$all_for_category_products[1];
            $spoiler[2][2] = @$all_for_category_products[2];
        }

        foreach($all_for_category_products as &$row){
            if ($row->price < $min_price) $min_price = $row->price;
            if ($row->price > $max_price) $max_price = $row->price;
        }

        $conditions = array();
        $conditions['sort'] = $sorter_code[$sort];
        $conditions['start'] = $start;
        $conditions['limit'] = $limit;
        $conditions['store'] = $get_store;
        $conditions['brand'] = @$get['brand'];

        if (!empty($get['min_price'])) {
            if ($get['min_price']<=$max_price && $get['min_price']>=$min_price) $conditions['min_price'] = $get['min_price'];
        }
        if (!empty($get['max_price'])) {
            if ($get['max_price']<=$max_price && $get['max_price']>=$min_price) $conditions['max_price'] = $get['max_price'];
        }

        $brands = $this->brands_model->get_connected($product_ids);

        $all_values = [];

        if (!empty($get['filters']) && !empty($product_ids)) {
            $filter_ids = array();
            foreach($get['filters'] as $key=>$val) {
                $filter_ids[] = intval($key);
            }
            $attribute_values = get_attribute_values_ro($params['category_id'], $filter_ids, $product_ids);
            $attribute_values_products = array_keys($attribute_values);

            $filtered_products = [];
            foreach($product_ids as $product_id) {
                if(in_array($product_id, $attribute_values_products)) {
                    $filtered_products[$product_id] = $product_id;
                }
            }


            foreach($get['filters'] as $key=>$val) {
                if (empty($val) && $val !== 0) continue;
                foreach($attribute_values as $product_id => $attrs) {
                    $this_vals = array();
                    foreach($val as $f_val) {
                        $this_vals[]=$f_val;
                        $all_values[]=$f_val;
                    }

                    if(isset($attrs[$key])) {
                        foreach ($attrs[$key] as $attr) {
                            $attr = transliteration($attr);
                            if (!in_array($attr,$this_vals)) {
                                unset($filtered_products[$product_id]);
                                continue;
                            }
                        }
                    }

                }
            }

            if(!empty($filtered_products)) {
                $articol_request = $this->db->select("distinct(articol) as articol")->where_in("id", $filtered_products)->get("product")->result();
                $articols = array_map(function ($item){return$item->articol;},$articol_request);
                $products = $this->product_model->get_products_for_category($lang, $params['category_id'], $filtered_products, $conditions);
                $count = $this->product_model->get_products_count($filtered_products, $conditions);
                $labels = $this->db->select("product_id as product_id, name_$lang as title, color as color")->where_in("product_id", $product_ids)->get('product_label')->result();
                foreach($products as &$product) {
                    $product->labels = [];
                    foreach($labels as $label) {
                        if($label->product_id == $product->id) $product->labels[] = $label;
                    }
                }
            } else {
                $articols = [];
                $products = [];
                $count = 0;
            }
        } else {
            if($product_ids) {
                $articols = array_map(function ($item){return$item->articol;},$all_for_category_products);
                $products = $this->product_model->get_products_for_category($lang, false, $product_ids, $conditions);
                $count = $this->product_model->get_products_count($product_ids, $conditions);
                $labels = $this->db->select("product_id as product_id, name_$lang as title, color as color")->where_in("product_id", $product_ids)->get('product_label')->result();
                foreach($products as &$product) {
                    $product->labels = [];
                    foreach($labels as $label) {
                        if($label->product_id == $product->id) $product->labels[] = $label;
                    }
                }
            } else {
                $articols = [];
                $products = [];
                $count = 0;
            }
        }

        if($params['terminalFlag'] or $query != '') {
            $redirect = [];
        } else {
            $redirect = $this->createRedirectLink($lang, $params['uri'], $params['category_uri'], @$get['brand'], $brands, $all_values);
        }

        $response = [
            'page' => $page,
            'sorter_view' => $sorter_view,
            'query' => $query,
            'sort' => $sort,
            'limit_view' => $limit_view,
            'limit' => $limit,
            'get_store' => $get_store,
            'brands' => $brands,
            'products' => $products,
            'articols' => $articols,
            'count' => $count,
            'spoiler' => $spoiler,
            'redirect' => $redirect,
        ];

        echo json_encode($response);
    }

    public function findOne() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        $this->load->model('product_model');
        $product = $this->product_model->get_product_by_uri($lang, @$params['id'], @$params['uri']);

        echo json_encode($product);
    }

    public function findByType() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        $this->load->model('product_model');
        $products = $this->product_model->get_products_by_type($lang, $params['type'], @$params['category_id']);

        echo json_encode($products);
    }

    private function createRedirectLink($lang, $uri, $category_uri, $get_brand, $brands, $all_values): array
    {
        $link_parts['main'] = $category_uri;

        if (!empty($get_brand)) {
            $brand_ids = array_keys($get_brand);
            foreach ($brands as $brand) {
                if (in_array($brand->id, $brand_ids)) {
                    $link_parts[transliteration($brand->title)] = transliteration($brand->title);
                }
            }
        }

        foreach ($all_values as $val) {
            $link_parts[transliteration($val)] = transliteration($val);
        }
        $link = implode('__', $link_parts);

        $flag = false;
        $full_link = false;

        $rendered = $this->db->where('rendered_link', $link)->get('category_filtered')->row();

        if($rendered and $link !== $uri) {
            $flag = true;
            $full_link  = '/'.$lang.'/category/'.$link;
        }

        $pizza = explode("__", $uri);
        if(!$rendered and count($pizza) > 1) {
            $flag = true;
            $full_link  = '/'.$lang.'/category/'.$category_uri;
        }

        $data['link'] = $full_link;
        $data['flag'] = $flag;

        return $data;
    }
}
