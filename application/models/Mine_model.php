<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mine_model extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function delete_photo($tblname = false, $id = false, $col=false)
    {
        if (empty($tblname) || empty($id) || empty($col)) return false;

        $id = (int)$id;

        return $this->db->where('id', $id)->update($tblname, array($col => ''));
    }

    public function delete_file($tblname = false, $lang = false, $id = false)
    {
        if (empty($tblname) || empty($lang) || empty($id)) return false;

        $file = 'file' . $lang;
        $id = (int)$id;

        return $this->db->where('id', $id)->update($tblname, array($file => ''));
    }

    public function change_select($tblname = false, $id = false, $value = false, $col = false)
    {
        if (empty($tblname) || empty($id) || empty($value) || empty($col)) return false;

        $id = (int)$id;
        $value = (int)$value;

        return $this->db->where('id', $id)->update($tblname, array($col => $value));
    }

    public function change_check($tblname = false, $id = false, $value = false, $col = false)
    {
        if (empty($tblname) || empty($id) || empty($col)) return false;

        $id = (int)$id;
        $value = (bool)$value;

        return $this->db->where('id', $id)->update($tblname, array($col => $value));
    }

    public function match($lang = 'ru', $table = false, $match = false) {
        if (empty($tblname) || empty($match)) return false;

        return $this->db->select("id as id, title$lang as title")
        ->like("title$lang", $match)
        ->like("titleRO", $match)
        ->limit(15)
        ->get($tblname)->result();
    }
}