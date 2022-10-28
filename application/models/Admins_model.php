<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admins_model extends BaseModel
{
    protected $tblname = 'admins';

    public function __construct()
    {
        parent::__construct();
    }

    public function try_login($login, $password) {
        $password_hash = hash('sha256', $password);

        $check_user = $this->db->where('login', $login)->where('password', $password_hash)->get($this->tblname)->row();

        if (!empty($check_user)) {
            $_SESSION['admin_id'] = $check_user->id;
            $_SESSION['login'] = $check_user->login;
            $_SESSION['admin_key'] = hash('sha512', $check_user->login.$check_user->password);
            return true;
        } else {
            return false;
        }
    }

    public function check_login($login = false) {
        if (empty($login)) return false;

        $admin_exist = $this->db->where('login', $login)->get($this->tblname)->row();

        $response = (!empty($admin_exist)) ? false : true;

        return $response;
    }

    public function check_password($password = false) {
        if (empty($password)) return false;

        $admin_id = $_SESSION['admin_id'];
        $password_hash = hash('sha256', $password);

        $check = $this->db->where('id', $admin_id)->where('password', $password_hash)->get($this->tblname)->row();

        $response = (empty($check)) ? false : true;

        return $response;
    }

    public function put($data = false) {
        if (empty($data)) return false;

        foreach ($data as $data_item) {
            if (empty($data_item)) return false;
        }

        return $this->db->insert($this->tblname, $data);
    }

    public function change_password($password = false) {
        if (empty($password)) {
            return false;
        }

        $admin_id = $_SESSION['admin_id'];
        $password_hash = hash('sha256', $password);

        return $this->db->where('id', $admin_id)->update($this->tblname, array('password' => $password_hash));
    }
}