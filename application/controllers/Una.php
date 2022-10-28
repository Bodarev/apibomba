<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Una extends FrontEndController
{
    protected $url;

    public function __construct()
    {
        parent::__construct();
        $this->url = 'http://185.181.228.75:8080/apex/marketplace/shop/order/';
    }

    public function send() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);

        if(!$params['order_id']) {
            echo json_encode(['nok']);
            exit();
        }

        $order = $this->db->where("id", $params['order_id'])->get("user_order")->row_array();
        $products = $this->db->where("order_id", $params['order_id'])->get("user_order_product")->result_array();
        $city = $this->db->where('id', $order['city_id'])->select('name_ro, connector')->from('city')->get()->row_array();
        $store = $this->db->where('id', $order['store_id'])->select('address_ro')->from('store')->get()->row('address_ro');
        $delivery_date = date('Hi') < 1200 ? date('YmdHis', strtotime('+1 day')) : date('YmdHis', strtotime('+2 days'));

        $una['info_any']['idComanda'] = $order['generated_id'];
        $una['info_any']['isFastComanda'] = 0;
        $una['info_any']['data'] = date('YmdHis');
        $una['info_any']['dataLivrare'] = $delivery_date;
        $una['info_any']['clientNume'] = $order['name'];
        $una['info_any']['clientEmail'] = $order['email'];
        $una['info_any']['clientTelefon'] = $order['phone'];
        $una['info_any']['clientAdresa'] = $city['name_ro'] . ', ' .  $order['address'];
        $una['info_any']['modAchitare'] = (int) $order['payment_type_id'];
        if($order['payment_type_id'] == 7) {
            $real_sum = $this->db->select("COALESCE(SUM(value),0) as sum")->where("order_id", $order['generated_id'])->get("cash_log")->row()->sum;
            $una['info_any']['sumaAchitare'] = (int) $real_sum;
        } else {
            $una['info_any']['sumaAchitare'] = (int) $order['total'] + $order['delivery_amount'];
        }
        $una['info_any']['dataAchitare'] = $this->paymentTransform($order['delivery_type_id']) == 5 ? $delivery_date : date('YmdHis');
        $una['info_any']['nrCekFiscal'] = ($order['fiscal_number']) ? $order['fiscal_number'] : null;
        $una['info_any']['IDTerminal'] =  ($order['fiscal_number']) ? $order['fiscal_number'] : null;
        $una['info_any']['livrareMod'] = $this->deliveryTransform($order['delivery_type_id']);
        $una['info_any']['livrareSuma'] = $order['delivery_amount'];
        $una['info_any']['livrareAdresa'] = iconv("UTF-8", "ASCII//TRANSLIT", $city['name_ro']) . ', ' . iconv("UTF-8", "ASCII//TRANSLIT", $store);
        $una['info_any']['LocComanda'] = ($order['terminal_id']) ? '22' : '21';
        $una['info_any']['observatii'] = null;
        $una['info_any']['idConsultant'] = $order['consultant_id'];
        $una['info_any']['LivrRaion'] = !$city['connector'] ? 11 : $city['connector'];
        $una['info_any']['LivrInterva1'] = $order['delivery_date'];

        foreach ($products as $product) {
            $_product = $this->db->select('articol, partner_id, name_ro')->where('id', $product['product_id'])->from('product')->get()->row_array();
            $una['info_any']['pozCmd'][] = array(
                'idArticol' =>  $product['product_id'],
                'category' => 0,
                'nameArticol' => iconv("UTF-8", "ASCII//TRANSLIT", $_product['name_ro']),
                'codExternArticol' => ($_product['partner_id'] != 1) ? $_product['articol'] : null,
                'codBareArticol' => null,
                'codPartener' => $_product['partner_id'],
                'cant' => (int) $product['quantity'],
                'pret' => (string) $product['price'],
                'uds' => (int) $product['reduction'],
                'suma' => ($product['price'] * $product['quantity']) - $product['reduction']
            );
        }

        $json_una = json_encode($una, JSON_UNESCAPED_UNICODE);

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_una);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        $response = curl_exec($ch);
        curl_close($ch);

        $data = array(
            'order_id' => $params['order_id'],
            'request' => $json_una,
            'response' => $response
        );

        $this->db->insert("una_log", $data);

        echo json_encode(['ok']);
        exit();
    }

    function paymentTransform($payment): int
    {
        switch ($payment) {
            // la primire
            case 1:
            default:
                return 5;
                break;
            // online
            case 3:
                return 3;
                break;
        }
    }

    function deliveryTransform($delivery): int
    {
        switch ($delivery) {
            case 2:
            default:
                return 6;
                break;
            case 4:
                return 7;
                break;
        }
    }
}
