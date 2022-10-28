<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content extends FrontEndController
{
    public function sliders() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);
        $lang = verify_language(@$params['lang']);

        $this->load->model('slider_model');
        $sliders = $this->slider_model->get_sliders($lang);

        echo json_encode($sliders);
    }
}
