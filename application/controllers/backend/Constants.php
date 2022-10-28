<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Constants extends  BackEndController
{
    public function __construct()
    {
        parent::__construct(__CLASS__);
    }

    public function index()
    {
        $main_page = 'constants';
        $title = lang('Constants');

        $this->load->model('constants_model');

        $constants = $this->constants_model->find();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            foreach(language(true) as $lang){
                ${$lang} = $this->input->post($lang, FALSE);
            }

            try {
                foreach(language(true) as $lang){
                    foreach (${$lang} as $id => $val) {
                        if (!$this->constants_model->update_constants($id, $val, $lang)) {
                            throw new Exception(lang('Error writing data to table').$main_page.' '.strtoupper($lang));
                        }
                    }
                }
                $constants = $this->constants_model->find();

                $_SESSION['success'] = lang('You have successfully updated the constants!');
            } catch (Exception $e) {
                log_message('error', $e->getMessage());
                $errors[] = lang('Exception thrown') . $e->getMessage();
                $_SESSION['error'] = $errors;
            }
        }

        $data = array(
            'inner_view' => 'dashboard/' . $main_page . '/index',
            'constants' => $constants,
            'title' => $title
        );

        $this->load->vars($data);
        $this->load->view('dashboard/index');
    }
}
