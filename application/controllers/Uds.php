<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Uds extends CI_Controller
{
    const ADD_POINTS_STATUS__ERROR = 'ADD_POINTS_STATUS__ERROR';
    const ADD_POINTS_STATUS__NOT_UDS_ORDER = 'ADD_POINTS_STATUS__NOT_UDS_ORDER';
    const ADD_POINTS_STATUS__POINTS_ALREADY_ADDED = 'ADD_POINTS_STATUS__POINTS_ALREADY_ADDED';
    const ADD_POINTS_STATUS__SUCCESS = 'ADD_POINTS_STATUS__SUCCESS';
    const ADD_POINTS_STATUS__NO_BONUSES = 'ADD_POINTS_STATUS__NO_BONUSES';

    protected $apiKey;
    protected $companyId;
    protected $url;
    protected $uuid_v4;

    public function __construct()
    {
        parent::__construct();
        $this->url = 'https://api.uds.app/partner/v2/';
        $this->companyId = '549756058060';
        $this->apiKey = 'ODExNTVlOTctZTk1MC00Y2Y2LTk4NTctODAzOGQwZjVmMGM0';
        $this->uuid_v4 = $this->GUID();
    }

    public function find() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);

        $url = $this->url . "/customers/find";

        $get = array("code" => $params['code']);

        $date = new DateTime();
        $options = array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Accept-Charset: utf-8",
            "Authorization: Basic " . base64_encode("$this->companyId:$this->apiKey"),
            "X-Origin-Request-Id: " . $this->uuid_v4,
            "X-Timestamp: " . $date->format(DateTime::ATOM),
        );

        echo $this->curl_get($url, $get, array(CURLOPT_HTTPHEADER=> $options));
    }

    public function calc() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);

        $url = $this->url . "/operations/calc";

        $date = new DateTime();

        $options = array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Accept-Charset: utf-8",
            "Authorization: Basic " . base64_encode("$this->companyId:$this->apiKey"),
            "X-Origin-Request-Id: " . $this->uuid_v4,
            "X-Timestamp: " . $date->format(DateTime::ATOM),
        );

        $postData = json_encode(
            array(
                'code' => $params['code'],
                'receipt' => array(
                    'total' => $params['total'],
                    'points' => 0,
                ),
            ), JSON_UNESCAPED_UNICODE
        );

        echo $this->curl_post($url, $postData, array(CURLOPT_HTTPHEADER=> $options));
    }

    public function reduction() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);

        $result = ($params['product_total'] * $params['reduction_total']) / $params['cart_total'];
         echo round($result);
    }

    public function operations() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);

        $url = $this->url . "/operations";

        $date = new DateTime();

        $options = array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Accept-Charset: utf-8",
            "Authorization: Basic " . base64_encode("$this->companyId:$this->apiKey"),
            "X-Origin-Request-Id: " . $this->uuid_v4,
            "X-Timestamp: " . $date->format(DateTime::ATOM),
        );

        $postData = json_encode(
            array(
                'code' => $params['code'],
                'receipt' => array(
                    'total' => $params['total'],
                    'points' => $params['points'],
                    'cash' => $params['total'] - $params['points'],
                ),
            ), JSON_UNESCAPED_UNICODE
        );

        echo $this->curl_post($url, $postData, array(CURLOPT_HTTPHEADER=> $options));
    }

    public function reward() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);

        $order = $this->db->where('id', $params['id'])->get("user_order")->row_array();

        if(!$order || empty($order['uds_uid'])) {
            $response['message'] = self::ADD_POINTS_STATUS__NOT_UDS_ORDER;
            echo json_encode($response);
            exit();
        }

        if(!empty($order['uds_accepted'])) {
            $response['message']=  self::ADD_POINTS_STATUS__POINTS_ALREADY_ADDED;
            echo json_encode($response);
            exit();
        }

        $cashback = $this->db->select("sum(product.uds_cashback) as cashback")
            ->join("product", "product.id = user_order_product.product_id")
            ->where('order_id', $order['id'])->get('user_order_product')->row()->cashback;

        if (!$cashback) {
            $response['message'] = self::ADD_POINTS_STATUS__NO_BONUSES;
            echo json_encode($response);
            exit();
        }

        $url = $this->url . "operations/reward";

        $options = array(
            "Authorization: Basic " . base64_encode("$this->companyId:$this->apiKey"),
            "Content-Type: application/json"
        );

        $postData = json_encode(
            array(
                'comment' => $order['generated_id'],
                'points' => $cashback,
                'participants' => $order['uds_id'],
            ), JSON_UNESCAPED_UNICODE
        );

        $res = $this->curl_post($url, $postData, array(CURLOPT_HTTPHEADER=> $options));
        $res = json_decode($res, true);

        if ($res['accepted']) {
            $this->db->where('id', $order['id'])->update('user_order', ['uds_cashback' => $cashback, 'uds_accepted' => 1]);
        }

        $response['message'] = self::ADD_POINTS_STATUS__SUCCESS;
        $response['cashback'] = $cashback;
        echo json_encode($response);
    }

    public function refund() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);

        $date = new DateTime();

        $url = $this->url . "operations/".$params['id']."/refund";

        $options = array(
            "Accept: application/json\r\n" .
            "Accept-Charset: utf-8\r\n" .
            "Authorization: Basic " . base64_encode("$this->companyId:$this->apiKey") . "\r\n" .
            "X-Origin-Request-Id: " . $this->uuid_v4 . "\r\n" .
            "X-Timestamp: " . $date->format(DateTime::ATOM),
        );

        $postData = json_encode(
            array(
                "partialAmount" => $params['total']
            ), JSON_UNESCAPED_UNICODE
        );

        echo $this->curl_post($url, $postData, array(CURLOPT_HTTPHEADER=> $options));
    }

    function GUID() {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function curl_post($url, $post = null, array $options = array()) {

        $defaults = array(
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_SSL_VERIFYHOST =>0,//unsafe, but the fastest solution for the error " SSL certificate problem, verify that the CA cert is OK"
            CURLOPT_SSL_VERIFYPEER=>0, //unsafe, but the fastest solution for the error " SSL certificate problem, verify that the CA cert is OK"
            CURLOPT_POSTFIELDS => $post
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if( ! $result = curl_exec($ch)){
            trigger_error(curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }

    public function curl_get($url, array $get = NULL, array $options = array()) {

        $defaults = array(
            CURLOPT_URL => $url . (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($get) ,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        );
        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if (!$result = curl_exec($ch)) {
            trigger_error(curl_error($ch));
        }

        curl_close($ch);
        return $result;
    }
}
