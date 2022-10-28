<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brands_model extends BaseModel
{
    protected $tblname = 'brand';

    public function __construct()
    {
        parent::__construct();
    }

    function get_connected($ids) {
        if (empty($ids)) return false;

         $this->db->select(
            'brand.name as title,
			brand.id as id,
			COUNT(DISTINCT(product.articol)) AS count'
        )
            ->from('product')
            ->join('brand','brand.id=product.brand_id','inner');

         if(count($ids) > 1000) {
             $idsParts = array_chunk($ids, 1000);
             $this->db->group_start();
                 foreach ($idsParts as $key=>$idsPart) {
                     if($key==0) {
                         $this->db->where_in("product.id", $idsPart);
                     } else {
                         $this->db->or_where_in("product.id", $idsPart);
                     }
                 }
             $this->db->group_end();
         } else {
             $this->db->where_in('product.id', $ids);
         }

        return $this->db->group_by('brand.id')
            ->order_by('brand.name asc')
            ->get()
            ->result();
    }
}
