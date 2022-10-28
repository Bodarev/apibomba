<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Constants_model extends BaseModel {
    protected $tblname = 'constants';

    public function __construct()
    {
        parent::__construct();
    }

    public function update_constants($id, $data, $lang = false)
    {
        if (empty($id) || empty($lang)) return false;

        return $this->db->where('id', $id)->update($this->tblname, array( strtoupper($lang) => $data));
    }
}
