<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends BaseModel
{
    protected $tblname = 'menu';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_page_data($lang = false, $uri = false)
    {
        if (empty($uri) || empty($lang)) {
            return false;
        }

        $this->db->select("
            id as id,
            title$lang as title,
            desc$lang as desc,
            text$lang as text,
            seoTitle$lang as seo_title,
            seoKeywords$lang as seo_keywords,
            seoDesc$lang as seo_desc,
            uri$lang as uri,
            uriRO as uriRO,
            uriRU as uriRU,
            uriEN as uriEN,
            isShown as isShown,
            sorder as sorder,
            onTop as onTop,
            onBottom as onBottom,
            img as img
        ");
        $this->db->where("uri$lang", $uri);
        $this->db->where('isShown', 1);
        return $this->db->get('menu')->row();
    }

    public function get_page_data_by_id($lang = false, $id = false)
    {
        if (empty($lang) || empty($id)) {
            return false;
        }

        $id = (int) $id;

        $this->db->select("
            id as id,
            title$lang as title,
            desc$lang as desc,
            text$lang as text,
            seoTitle$lang as seo_title,
            seoKeywords$lang as seo_keywords,
            seoDesc$lang as seo_desc,
            uri$lang as uri,
            uriRO as uriRO,
            uriRU as uriRU,
            uriEN as uriEN,
            isShown as isShown,
            sorder as sorder,
            onTop as onTop,
            onBottom as onBottom,
            img as img
        ");
        $this->db->where('id', $id);
        $this->db->where('isShown', 1);
        return $this->db->get('menu')->row();
    }

    public function get_menu($lang)
    {
        if (empty($lang)) return false;

        $this->db->select("
            id as id,
            title$lang as title,
            uri$lang as uri,
            isShown as isShown,
            onTop as onTop,
            onBottom as onBottom,
        ");
        //$this->db->where('isShown', 1);
        $this->db->order_by('sorder ASC, ID DESC');
        $data = $this->db->get('menu')->result();

        $arr = array();
        $response = array();
        $response['top'] = array();
        $response['bottom'] = array();
        $response['all'] = array();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $arr[$value->id] = $value;
            }
            $data = $arr;
            foreach ($data as $key => $value) {
                if ($value->onTop == 1 && $value->isShown == 1) $response['top'][$key] = $data[$key];
            }
            foreach ($data as $key => $value) {
                if ($value->onBottom == 1 && $value->isShown == 1) $response['bottom'][$key] = $data[$key];
            }
        }

        $response['all'] = $data;

        return $response;
    }
}
