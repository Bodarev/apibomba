<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Slider_model extends BaseModel
{
    protected $tblname = 'slider';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_sliders($lang)
    {
        if (empty($lang)) return false;

        return $this->db->select("
            name_$lang as title,
            description_$lang as desc,
            link_$lang as link,
            price,
            image_$lang as img,
            image_mobile_$lang as mobile_img,
            image_terminal_$lang as terminal_img,
            image_terminal_sleep_$lang as terminal_sleep_img,
        ")
            ->where("isShown", 1)
            ->order_by('sorder asc,id desc')
            ->get($this->tblname)->result();
    }
}
