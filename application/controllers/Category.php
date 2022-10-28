<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends FrontEndController
{
    public function tree() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        $this->load->model('category_model');
        $categories = $this->category_model->get_tree_categories($lang);

        echo json_encode($categories);
    }

    public function search() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        $this->load->model('category_model');
        $categories = $this->category_model->get_search_categories($lang);

        echo json_encode($categories);
    }

    public function deleteCache() {

        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));

        $this->cache->delete('categories_result_ru');
        $this->cache->delete('categories_result_ro');

        echo 'OK';
    }

    public function popular() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        $this->load->model('category_model');
        $categories = $this->category_model->get_popular_categories($lang);

        echo json_encode($categories);
    }

    public function findOne() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        if(!isset($params['uri'])) {
            echo json_encode([]);
        } else {
            $this->load->model('category_model');
            $category = $this->category_model->get_category($lang, $params['uri']);

            echo json_encode($category);
        }
    }

    public function subcategories() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        if(!isset($params['id'])) {
            echo json_encode([]);
        } else {
            $this->load->model('category_model');
            $subcategories = $this->category_model->get_subcategories($lang, $params['id']);

            echo json_encode($subcategories);
        }
    }

    public function find() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        $this->load->model('category_model');
        $categories = $this->category_model->get_all_categories($lang);

        echo json_encode($categories);
    }

    public function findByIds() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        if(!isset($params['ids'])) {
            echo json_encode([]);
        } else {
            $this->load->model('category_model');
            $categories = $this->category_model->get_categories_by_ids($lang, $params['ids']);

            echo json_encode($categories);
        }
    }

}
