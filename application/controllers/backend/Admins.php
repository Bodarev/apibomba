<?php
/**
 * Created by PhpStorm.
 * User: WhoAmI
 * Date: 02.03.2018
 * Time: 13:55
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Admins extends BackEndController
{
    private $add;
    private $index_title;
    private $success_add;
    private $success_delete;

    public function __construct()
    {
        parent::__construct(__CLASS__);

        $this->a_path = $this->path . 'add_admin/';
        $this->del_path = $this->path. 'delete_admin/';
        $this->add = lang('Add');
        $this->index_title = lang('Users');
        $this->success_add = lang('You have successfully added an object');
        $this->success_edit = lang('You have successfully updated the object');
        $this->success_delete = lang('You have successfully deleted the object');

        $this->data['title'] = $this->index_title;
        $this->data['add'] = $this->add;
        $this->data['a_path'] = $this->a_path;
        $this->data['del_path'] = $this->del_path;
        $this->load->model('admins_model');
    }

    public function index()
    {
        $chp_path = $this->path . 'change_password/';

        if (!empty($_SESSION['inserted_data'])) {
            $login_value = 'value="' . $_SESSION['inserted_data']['login'] . '"';
            $accordion_status = 'in';
            $i_icon = 'fa-minus';
            $aria_expanded = 'true';
            unset($_SESSION['inserted_data']);
        } else {
            $login_value = '';
            $accordion_status = '';
            $i_icon = 'fa-plus';
            $aria_expanded = 'false';
        }

        $admins = $this->admins_model->find('id asc');

        $this->data['inner_view'] = $this->index_view;
        $this->data['admins'] = $admins;
        $this->data['chp_path'] = $chp_path;
        $this->data['login_value'] = $login_value;
        $this->data['accordion_status'] = $accordion_status;
        $this->data['i_icon'] = $i_icon;
        $this->data['aria_expanded'] = $aria_expanded;

        $this->load->vars($this->data);
        $this->load->view($this->main_layout);
    }

    public function add_admin()
    {
        check_if_POST();

        $data = array();

        try {
            foreach ($_POST as $index => $item) {
                $post[$index] = $this->input->post($index, TRUE);
            }

            if ($post['password'] != $post['passwordCheck']) {
                throw new Exception(lang('Passwords do not match!'));
            }

            unset($post['passwordCheck']);

            $post['password'] = hash('sha256', $post['password']);

            if (!$this->admins_model->check_login($post['login'])) {
                throw new Exception(lang('This login is already taken!'));
            }

            if (!$this->admins_model->put($post)) {
                throw new Exception(lang('Error writing data to table') . $this->main_page);
            }

            $_SESSION['success'] = $this->success_add;
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            $errors[] = lang('Exception thrown') . $e->getMessage();
            $_SESSION['error'] = $errors;
            $_SESSION['inserted_data']['login'] = $data['login'];
        }

        redirect($this->path);
    }

    public function delete_admin($id = false)
    {
        $id = (int)$id;

        $this->load->model('admins_model');

        $admin = $this->admins_model->find_first($id);

        if (empty($admin)) throw_on_404();

        try {
            if (!$this->admins_model->delete($id)) {
                throw new Exception(lang('Error deleting data from table') . $this->main_page);
            }

            $_SESSION['success'] = $this->success_delete;
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            $errors[] = lang('Exception thrown') . $e->getMessage();
            $_SESSION['error'] = $errors;
        }

        redirect($this->path);
    }

    public function change_password()
    {
        check_if_POST();

        $old_password = $this->input->post('old_password', TRUE);
        $password = $this->input->post('password', TRUE);
        $password_check = $this->input->post('passwordCheck', TRUE);

        $this->load->model('admins_model');

        try {
            if (empty($old_password) || empty($password) || empty($password_check)) throw_on_404();

            if ($password != $password_check) {
                throw new Exception(lang('New passwords do not match!'));
            }

            if (!$this->admins_model->check_password($old_password)) {
                throw new Exception(lang('The current user password is incorrect!'));
            }

            if (!$this->admins_model->change_password($password)) {
                throw new Exception(lang('Error writing data to table') . $this->main_page);
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            $errors[] = lang('Exception thrown') . $e->getMessage();
            $_SESSION['error_passwords'] = $errors;
        }

        redirect($this->path);
    }
}
