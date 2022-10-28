<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends BaseModel
{
    protected $tblname = 'category';

    public function __construct()
    {
        parent::__construct();

        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    }

    function _fill_categories($lang) {
        $categories = [];

        if (!$categories_result = $this->cache->get('categories_result_'.$lang)) {
            $query = $this->db
                ->select(
                    'jivosite_'.$lang.' as jivosite, 
                	name_'.$lang.' as title,
                	h1_'.$lang.' as h1,
                    seo_title_'.$lang.' as seo_title,
                    seo_kw_'.$lang.' as seo_keywords,
                    seo_description_'.$lang.' as seo_desc,
                    description_'.$lang.' as description,
                    fixed_link_'.$lang.' as fixed_link,
                    id as id,
                    url as uri,
                    sorder,
                    parent_id,
                    level,
                    delivery_price,
                    show_popup_18,
                    is_shown,
                    set_id,
                    image as img,
                    image_terminal as terminal_img,
                    image_size_'.$lang.' as image_size,
                    need_update,
                    is_russian_size,
                    is_new,
                    is_popular,
                    on_search,
                    multiple_filters'
                )
                ->where("is_shown", 1)
                ->where('level <=',4)->where_not_in('id', [348410, 348404])
                ->order_by('sorder asc,id desc')
                ->get($this->tblname);

            $categories_result = $query->result_array();

            // Save into the cache for 3 hours

            $this->cache->save('categories_result_'.$lang, $categories_result, 60 * 60 * 6);
        }

        foreach($categories_result as $key=>$row) {
            if (empty($row['url'])) $row['url']=$row['id'];
            $categories[$row['id']] = $row;
        }

        return $categories;
    }

    function get_all_categories($lang) {
        return $this->_fill_categories($lang);
    }

    function get_tree_categories($lang) {

        $categories = $this->_fill_categories($lang);
        $levels = array();

        foreach($categories as $category) {
            unset($category['seo_title']);
            unset($category['seo_kw']);
            unset($category['description']);
            $levels[$category['level']][$category['id'].''] = $category;
        }

        $main_list = $levels[1];
        $level2 = $levels[2];

        foreach($levels[4] as $id => $category) {
            if (!empty($levels[3][$category['parent_id']])) {
                $levels[3][$category['parent_id']]['children'][$id.''] = $category;
            }
        }

        foreach($levels[3] as $id => $category) {
            if (!empty($level2[$category['parent_id']])) {
                $level2[$category['parent_id']]['children'][$id.''] = $category;
            }
        }

        foreach($level2 as $id => $category) {
            if (!empty($main_list[$category['parent_id']])) {
                $main_list[$category['parent_id']]['children'][$id.''] = $category;
            }
        }

        return $main_list;
    }

    function get_search_categories($lang) {
        $categories = $this->_fill_categories($lang);

        $search = [];
        foreach ($categories as $category) {
            if($category['on_search']) {
                $search[] = $category;
            }
        }

        return $search;
    }

    function get_popular_categories($lang) {
        $categories = $this->_fill_categories($lang);

        $popular = [];
        foreach ($categories as $category) {
            if($category['is_popular']) {
                $popular[] = $category;
            }
        }

        return $popular;
    }

    function get_category($lang, $uri) {
        return $this->db->select(
                'jivosite_'.$lang.' as jivosite, 
                	name_'.$lang.' as title,
                	h1_'.$lang.' as h1,
                    seo_title_'.$lang.' as seo_title,
                    seo_kw_'.$lang.' as seo_keywords,
                    seo_description_'.$lang.' as seo_desc,
                    description_'.$lang.' as text,
                    fixed_link_'.$lang.' as fixed_link,
                    id as id,
                    url as uri,
                    sorder,
                    parent_id,
                    level,
                    delivery_price,
                    show_popup_18,
                    is_shown,
                    set_id,
                    image as img,
                    image_size_'.$lang.' as image_size,
                    need_update,
                    is_russian_size,
                    is_new,
                    is_popular,
                    multiple_filters'
            )
            ->where('url', $uri)
            ->or_where('id', $uri)
            ->get($this->tblname)->row();
    }

    function get_subcategories($lang, $id){
        return $this->db->select(
            'jivosite_'.$lang.' as jivosite, 
                	name_'.$lang.' as title,
                	h1_'.$lang.' as h1,
                    seo_title_'.$lang.' as seo_title,
                    seo_kw_'.$lang.' as seo_keywords,
                    seo_description_'.$lang.' as seo_desc,
                    description_'.$lang.' as description,
                    fixed_link_'.$lang.' as fixed_link,
                    id as id,
                    url as uri,
                    sorder,
                    parent_id,
                    level,
                    delivery_price,
                    show_popup_18,
                    is_shown,
                    set_id,
                    image as img,
                    image_terminal as terminal_img,
                    image_size_'.$lang.' as image_size,
                    need_update,
                    is_russian_size,
                    is_new,
                    is_popular,
                    multiple_filters'
        )
            ->where('parent_id', $id)
            ->where('is_shown', 1)
            ->order_by('sorder asc')
            ->get($this->tblname)->result();
    }

    function get_categories_by_ids($lang, $ids){
        return $this->db->select(
            'jivosite_'.$lang.' as jivosite, 
                	name_'.$lang.' as title,
                	h1_'.$lang.' as h1,
                    seo_title_'.$lang.' as seo_title,
                    seo_kw_'.$lang.' as seo_keywords,
                    seo_description_'.$lang.' as seo_desc,
                    description_'.$lang.' as description,
                    fixed_link_'.$lang.' as fixed_link,
                    id as id,
                    url as uri,
                    sorder,
                    parent_id,
                    level,
                    delivery_price,
                    show_popup_18,
                    is_shown,
                    set_id,
                    image as img,
                    image_terminal as terminal_img,
                    image_size_'.$lang.' as image_size,
                    need_update,
                    is_russian_size,
                    is_new,
                    is_popular,
                    multiple_filters'
        )
            ->where_in('id', $ids)
            ->order_by('sorder asc')
            ->get($this->tblname)->result();
    }
}
