<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends BackEndController
{
    public function __construct()
    {
        parent::__construct(__CLASS__);
        $this->load->model('mine_model');
    }

    public function delete_photo() {
        check_if_POST();

        $tblname = $this->input->post('table');
        $id = $this->input->post('id');
        $col = $this->input->post('col');

        $model = $tblname . '_model';
        $this->load->model($model);
        $item = $this->$model->find_first($id);
        unlink_files($tblname, $item->$col);

        $result = ($this->mine_model->delete_photo($tblname, $id, $col)) ? 200 : 500;

        $response['status'] = $result;

        echo json_encode($response);
        exit();
    }

    public function delete_img_row() {
        check_if_POST();

        $patch = $this->input->post('table');
        $pizza = explode("/", $patch);
        $id = $this->input->post('id');
        $model = current($pizza) . '_model';
        $this->load->model($model);

        // delete main image
        $item = $this->$model->find_first($id);
        unlink_files($patch, $item->img);

        $result = ($this->$model->delete($id)) ? 200 : 500;

        $response['status'] = $result;

        echo json_encode($response);
        exit();

    }

    public function delete_file() {
        check_if_POST();

        $tblname = $this->input->post('table');
        $id = $this->input->post('id');
        $lang = $this->input->post('lang');

        $result = ($this->mine_model->delete_file($tblname, $lang, $id)) ? 200 : 500;

        $response['status'] = $result;

        echo json_encode($response);
        exit();
    }

    public function change_select() {
        check_if_POST();

        $tblname = $this->input->post('table');
        $id = $this->input->post('id');
        $value = $this->input->post('value');
        $col = $this->input->post('col');

        if ($value == 3){
            // return sku to products
            $this->db->where([ 'order_id' => intval($id), 'returned' => 0 ]);
            $products = $this->db->get('order_items')->result();
            if (!empty($products)){
                foreach ($products as $product){
                    // change quantity
                    $this->db->where('sku_id', $product->sku_id);
                    $this->db->set('qty', 'qty+' . $product->quan, FALSE);
                    $this->db->update('product_sku');
                    $this->db->where(['sku_id' => $product->sku_id, 'order_id' => intval($id)])->update('order_items', ['returned' => 1]);
                }
            }
        }

        $result = ($this->mine_model->change_select($tblname, $id, $value, $col)) ? 200 : 500;
        $response['status'] = $result;
        echo json_encode($response);
        exit();
    }

    public function change_check() {
        check_if_POST();

        $tblname = $this->input->post('table');
        $id = $this->input->post('id');
        $value = $this->input->post('value');
        $col = $this->input->post('col');

        $result = ($this->mine_model->change_check($tblname, $id, $value, $col)) ? 200 : 500;

        $response['status'] = $result;

        echo json_encode($response);
        exit();
    }
}
