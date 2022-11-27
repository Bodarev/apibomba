<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends BaseModel
{
    protected $tblname = 'product';

    public function get_product_by_uri($lang, $id, $uri) {

        if(empty($id) and empty($uri)) return false;

        $this->db->select("
            $this->tblname.id as id,
            $this->tblname.code as code,
            $this->tblname.uri as uri,
            $this->tblname.brand_id as brand_id,
            $this->tblname.articol as articol,
            $this->tblname.sku as sku,
            $this->tblname.name_$lang as title,
            $this->tblname.first_color as first_color,
            $this->tblname.partner_id as partner_id,
            $this->tblname.rate as rate,
            $this->tblname.uds_cashback as uds_cashback,
            $this->tblname.description_$lang as description,
            $this->tblname.attribute_set_id as attribute_set_id,
            $this->tblname.razm_setka as razm_setka,
            $this->tblname.youtube as youtube,
            $this->tblname.meta_title_$lang as seo_title,
            $this->tblname.meta_keywords_$lang as seo_keywords,
            $this->tblname.meta_description_$lang as seo_desc,
            product_price.price as price,
            product_price.discounted_price,
            sum(product_stock.preorder) as preorder,
            sum(product_stock.quantity) as qty,
            (SELECT count(product_feedback.id) FROM product_feedback WHERE product_feedback.product_id = product.id and product_feedback.isShown = 1) as feedbacks_count,
            (SELECT avg(score) FROM product_feedback WHERE product_feedback.product_id = product.id and product_feedback.isShown = 1) as score,
            (SELECT category_id FROM category_product WHERE category_product.product_id = product.id ORDER BY category_product.category_id DESC LIMIT 1) as category_id,
        ");
        $this->db->join("product_price", "product_price.product_id = product.id");
        $this->db->join("product_stock", "product_stock.product_id = product.id");
        if($id) $this->db->where("$this->tblname.id", $id);
        if($uri) $this->db->where("$this->tblname.uri", $uri);
        $this->db->where("$this->tblname.is_shown", 1);
        $this->db->where("product_price.discounted_price >", 0);
        $this->db->group_by("$this->tblname.id");

        $product = $this->db->get($this->tblname)->row();

        if($product) {
            $product->labels = $this->db->select("name_$lang as title, color as color")->where('product_id', $product->id)->get('product_label')->result();
            $brand = $this->db->where("id", $product->brand_id)->get('brand')->row();
            $product->brand_name = ($brand) ? $brand->name : '' ;
        }

        return $product;
    }

    public function get_products_by_ids($lang, $ids, $limit = 0, $sort = false) {
        if(empty($lang) || empty($ids)) return false;

        $this->db->select("
            $this->tblname.id as id,
            $this->tblname.uri as uri,
            $this->tblname.articol as articol,
            $this->tblname.sku as sku,
            $this->tblname.brand_id as brand_id,
            $this->tblname.name_$lang as title,
            $this->tblname.first_color as first_color,
            $this->tblname.partner_id as partner_id,
            $this->tblname.rate as rate,
            $this->tblname.uds_cashback as uds_cashback,
            $this->tblname.attribute_set_id as attribute_set_id,
            product_price.price as price,
            product_price.discounted_price,
            (SELECT count(product_feedback.id) FROM product_feedback WHERE product_feedback.product_id = product.id and product_feedback.isShown = 1) as feedbacks_count,
            (SELECT avg(score) FROM product_feedback WHERE product_feedback.product_id = product.id and product_feedback.isShown = 1) as score,
            (SELECT category_id FROM category_product WHERE category_product.product_id = product.id ORDER BY category_product.category_id ASC LIMIT 1) as category_id,
            (SELECT sum(preorder) FROM product_stock WHERE product_stock.product_id = product.id) as preorder,
            (SELECT sum(quantity) FROM product_stock WHERE product_stock.product_id = product.id) as qty,
            (SELECT CASE WHEN sum(quantity) > 0 THEN 1 ELSE 0 END FROM product_stock WHERE product_stock.product_id = product.id) as display,
        ");
        $this->db->join("product_price", "product_price.product_id = product.id", "inner");
        $this->db->where_in("$this->tblname.id", $ids);
        if($limit) $this->db->limit($limit);
        if($sort) $this->db->order_by($sort);
        return $this->db->get($this->tblname)->result();
    }

    public function get_products_for_category($lang, $category_id = 0, $ids = array(), $conditions=array()) {

        if(!empty($conditions['query'])) {
            $ids = $this->search($lang, $conditions['query'], $category_id, 1000, true);
        }

        $this->db->select("
            $this->tblname.id as id,
            $this->tblname.uri as uri,
            $this->tblname.brand_id as brand_id,
            $this->tblname.articol as articol,
            $this->tblname.name_$lang as title,
            $this->tblname.first_color as first_color,
            $this->tblname.partner_id as partner_id,
            $this->tblname.rate as rate,
            $this->tblname.uds_cashback as uds_cashback,
            $this->tblname.attribute_set_id as attribute_set_id,
            product_price.price as price,
            product_price.discounted_price,
            (product_price.price - product_price.discounted_price) as diff,
            (SELECT count(product_feedback.id) FROM product_feedback WHERE product_feedback.product_id = product.id and product_feedback.isShown = 1) as feedbacks_count,
            (SELECT avg(score) FROM product_feedback WHERE product_feedback.product_id = product.id and product_feedback.isShown = 1) as score,
            (SELECT sum(preorder) FROM product_stock WHERE product_stock.product_id = product.id) as preorder,
            (SELECT sum(quantity) FROM product_stock WHERE product_stock.product_id = product.id) as qty,
            (SELECT CASE WHEN sum(quantity) > 0 THEN 1 ELSE 0 END FROM product_stock WHERE product_stock.product_id = product.id) as display,
        ");
        $this->db->join("product_price", "product_price.product_id = product.id");
        $this->db->join("product_stock", "product_stock.product_id = product.id");

        if($category_id) {
            $this->db->join("category_product", "category_product.product_id = product.id");
            $this->db->where("category_product.category_id", $category_id);
        }

        if(!empty($conditions['brand'])) {
            $this->db->where_in("$this->tblname.brand_id", $conditions['brand']);
        }

        if(!empty($conditions['min_price']) || !empty($conditions['max_price'])) {
            if (!empty($conditions['min_price'])) $this->db->where("product_price.discounted_price >=", $conditions['min_price']);
            if (!empty($conditions['max_price'])) $this->db->where("product_price.discounted_price <=", $conditions['max_price']);
        } else {
            $this->db->where("product_price.discounted_price >", 0);
        }

        if(!empty($conditions['store'])) {
            $this->db->where("store_id", $conditions['store']);
        }

        if($ids) {
            if(count($ids) > 1000) {
                $idsParts = array_chunk($ids, 1000);
                $this->db->group_start();
                foreach ($idsParts as $key=>$idsPart) {
                    if($key==0) {
                        $this->db->where_in("$this->tblname.id", $idsPart);
                    } else {
                        $this->db->or_where_in("$this->tblname.id", $idsPart);
                    }
                }
                $this->db->group_end();
            } else {
                $this->db->where_in("$this->tblname.id", $ids);
            }
        }
        $this->db->where("$this->tblname.is_shown", 1);

        if(!empty($conditions['limit'])) {
            $this->db->limit($conditions['limit'], $conditions['start']);
        }

        $this->db->group_by("$this->tblname.articol");

        if(!empty($conditions['sort'])) {
            $this->db->order_by($conditions['sort']);
        }

        return $this->db->get($this->tblname)->result();
    }

    public function get_products_count($ids = array(), $conditions=array()) {
        if ( empty($ids) ) return false;

        $this->db->select("
            $this->tblname.id as id,
        ");

        $this->db->join("product_price", "product_price.product_id = $this->tblname.id");
        $this->db->join("product_stock", "product_stock.product_id = $this->tblname.id");

        if(!empty($conditions['brand'])) {
            $this->db->where_in("$this->tblname.brand_id", $conditions['brand']);
        }

        if(!empty($conditions['min_price']) || !empty($conditions['max_price'])) {
            if (!empty($conditions['min_price'])) $this->db->where("product_price.discounted_price >=", $conditions['min_price']);
            if (!empty($conditions['max_price'])) $this->db->where("product_price.discounted_price <=", $conditions['max_price']);
        } else {
            $this->db->where("product_price.discounted_price >", 0);
        }

        if(!empty($conditions['store'])) {
            $this->db->where("store_id", $conditions['store']);
        }

        if(count($ids) > 1000) {
            $idsParts = array_chunk($ids, 1000);
            $this->db->group_start();
                foreach ($idsParts as $key=>$idsPart) {
                    if($key==0) {
                        $this->db->where_in("$this->tblname.id", $idsPart);
                    } else {
                        $this->db->or_where_in("$this->tblname.id", $idsPart);
                    }
                }
            $this->db->group_end();
        } else {
            $this->db->where_in("$this->tblname.id", $ids);
        }
        $this->db->where("$this->tblname.is_shown", 1);
        $this->db->group_by("$this->tblname.articol");
        $result = $this->db->get($this->tblname);

        return $result->num_rows();
    }

    public function get_products_by_type($lang, $type, $category_id, $limit = 10) {
        if(empty($lang) || empty($type)) return false;

        $this->db->select("$this->tblname.id as id");

        if($category_id) $this->db->join("category_product", "category_product.product_id = product.id")->where("category_product.category_id", $category_id);

        $result = $this->db->where("$this->tblname.$type", 1)->get($this->tblname)->result();

        if(!$result) return [];

        $ids = array_map(function($item){return $item->id;},$result);

        return $this->get_products_by_ids($lang, $ids, $limit);
    }

    public function search($lang, $query, $category_id, $limit, $private = false) {
        $this->load->library('elasticsearch');
        $elastic = new Elasticsearch();

        $search = [
            'index' => 'app_products',
            'body'  => [
                'size' => $limit,
                'query' => [
                    'bool' => [
                        'must' => [
                        ]
                    ]
                ],
                'sort' => ['price' => 'asc']
            ]
        ];

        if(is_numeric($query)) {
            $must0 = [
                'term' => [ 'id' => $query ]
            ];
        } else {
            $must0 = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => [ "articol", "code", "name_*", "categories" ],
                    'fuzziness' => 'AUTO',
                    'operator' => 'and'
                ]
            ];
        }

        $search['body']['query']['bool']['must'][0] = $must0;
        //if($category_id) $search['body']['query']['bool']['must'][1] = ['term' => ['category' => $category_id]];

        $result = $elastic->search($search);

        $products = [];

        if($result['hits']['total']) {
            $ids = array_map(function($item) {return $item['_id'];}, $result['hits']['hits']);

            if($private) return $ids;

            $this->load->model('product_model');
            $sort = sprintf("FIELD($this->tblname.id, %s)", implode(', ', $ids));

            $products = $this->product_model->get_products_by_ids($lang, $ids, $limit, $sort);
        }

        return $products;
    }

    public function oldSearch($lang, $query, $category_id, $limit, $private = false) {
        $query = $this->_sanitize(trim($query));

        // id sau code
        $this->db->select('product.id');
        $this->db->group_start();
            $this->db->where('product.id', $query);
            $this->db->or_where('product.code', $query);
        $this->db->group_end();
        $result = $this->db->get($this->tblname)->result();

        if($result) {
            $ids = array_map(function ($item) { return $item->id;}, $result);
            if($private) return $ids;
            $products = $this->get_products_by_ids($lang, $ids, $limit);

            if($category_id) {
                foreach ($products as $key=>$product) {
                    if($category_id != $product->category_id) {
                        unset($products[$key]);
                    }
                }
            }
            return $products;
        }

        // nume incomplet
        //$query = preg_replace('!\W!u', '', $query);
        $this->db->select('product.id');
        $this->db->group_start();
            $this->db->like('product.name_ro', $query);
            $this->db->or_like('product.name_ru', $query);
        $this->db->group_end();
        $result = $this->db->get($this->tblname)->result();

        if($result) {
            $ids = array_map(function ($item) { return $item->id;}, $result);
            if($private) return $ids;
            $products = $this->get_products_by_ids($lang, $ids, $limit);

            if($category_id) {
                foreach ($products as $key=>$product) {
                    if($category_id != $product->category_id) {
                        unset($products[$key]);
                    }
                }
            }
            return $products;
        }

        // FULLTEXT index
        $array = preg_split("/(?<=\D)(?=\d)|(?<=\d)(?=\D)\K/", $query);
        $tmp_arr = [];
        foreach ($array as $key => $value) {
            if (mb_strlen(trim($value), 'utf-8') > 1 ) {
                $tmp_arr[$key] = trim($value) . '*';
            }
        }
        $array = $tmp_arr;

        $ma_search = $query .'*';
        $ma_search_2 = implode($array, ' ');
        $match_str = "MATCH ($this->tblname.name_ru, $this->tblname.name_ro) AGAINST ('(>$ma_search) (<$ma_search_2)' IN BOOLEAN MODE)";

        $this->db->select('product.id, ' . $match_str . 'as rel');
        $this->db->where($match_str);
        $this->db->order_by('rel DESC');
        $result = $this->db->get($this->tblname)->result();

        if($result) {
            $ids = array_map(function ($item) { return $item->id;}, $result);
            if($private) return $ids;
            $products = $this->get_products_by_ids($lang, $ids, $limit);

            if($category_id) {
                foreach ($products as $key=>$product) {
                    if($category_id != $product->category_id) {
                        unset($products[$key]);
                    }
                }
            }
            return $products;
        }

        return array();
    }

    function _sanitize($str) {
        $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
        $str = htmlentities($str, ENT_QUOTES, 'UTF-8');
        //$str = preg_replace("/[^0-9a-zA-Zа-яА-ЯёЁ ]+/msiu", '', $str);
        return $str;
    }
}
