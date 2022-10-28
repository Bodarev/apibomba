<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FrontEndController extends CI_Controller
{
    protected function _define_constants($lang)
    {
        $this->load->model('constants_model');
        $constants = $this->constants_model->find();
        foreach ($constants as $constant) {
            if (!defined($constant->ConstantName)) {
                define($constant->ConstantName, $constant->$lang);
            }
        }
    }

    public function __construct()
    {
        parent::__construct();
    }
}
