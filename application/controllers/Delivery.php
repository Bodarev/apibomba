<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery extends FrontEndController
{
    public function dates() {

        $courierDeliveryDateTime =  get_courier_delivery_date()['deliveryDate'];
        $dates = get_working_dates($courierDeliveryDateTime, 5, true);

        echo json_encode($dates);
    }

    public function amount()
    {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);

        $this->_define_constants('ru');

        $city = $this->db->select('is_shown_slots, is_sector, slots, region_id')->where('id', $params['city_id'])->get('city')->row();

        if ($city->is_shown_slots) {
            if(!empty($city->slots)) {
                $slots = json_decode($city->slots, true);
            } else {
                $region = $this->db->where("id", $city->region_id)->get("regions")->row();
                $slots = json_decode($region->slots, true);
            }

            $pizza= explode("-", $params['key']);

            echo ($params['total'] > $slots[$pizza[0]][$pizza[1]]['free']) ? 0 : $slots[$pizza[0]][$pizza[1]]['price'];
        } else {
            $stores = $this->db->select([
                'id',
            ])
                ->where('city_id', ($city->is_sector) ? 1 : $params['city_id'])
                ->get('store')
                ->result();

            if ($stores) {
                if ($params['total'] > CHISINAU_MIN_SUM_FOR_FREE) {
                    echo 0;
                } else {
                    echo DELIVERY_PRICE_FOR_CHISINAU;
                }
            } else {
                if ($params['total'] > ALL_MIN_SUM_FOR_FREE) {
                    echo 0;
                } else {
                    echo DELIVERY_PRICE_FOR_ALL;
                }
            }
        }
    }

    public function online() {
        $jsonStr = file_get_contents("php://input");
        $params = json_decode($jsonStr, true);

        $is_payment_online = 0;

        $city = $this->db->where("id", $params['city_id'])->get("city")->row_array();

        if(!$city['is_sector'] || $params['delivery_type_id'] == 4) $is_payment_online = 1;

        echo json_encode($is_payment_online);
    }
}
