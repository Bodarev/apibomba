<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BaseModel extends CI_Model
{
    protected $tblname = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function find($sorder = 'sorder ASC, id DESC', $limit = null, $values = []) {

        if(!empty($values)) {
            foreach ($values as $key => $value) {
                if(is_array($value)) {
                    $this->db->where_in($key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }

        $this->db->order_by($sorder);
        if($limit) $this->db->limit($limit);

        $query = $this->db->get($this->tblname);
        return $query->result();
    }

    public function find_first($id)
    {
        $id = (int)$id;

        $query = $this->db->where('id', $id)->get($this->tblname);

        return $query->row();
    }

    public function put($data = false)
    {
        if (empty($data)) {
            return false;
        }

        return $this->db->insert($this->tblname, $data);
    }

    public function update($data = false, $id = false)
    {
        if (empty($data) || empty($id)) {
            return false;
        }

        return $this->db->where('id', $id)->update($this->tblname, $data);
    }

    public function update_sorder($data = false)
    {
        if (empty($data)) {
            return false;
        }

        foreach ($data as $id => $sorder) {
            if (!$this->db->where('id', $id)->update($this->tblname, array('sorder' => $sorder))) {
                return false;
            }
        }

        return true;
    }

    public function delete($id = false)
    {
        if (empty($id)) {
            return false;
        }

        $id = (int) $id;

        return $this->db->delete($this->tblname, array('id' => $id));
    }
}
