<?
class Delivery_specific_day_model extends BaseModel {

	private $main_table = 'delivery_specific_day';

    public function get_info_by_date($date) {
        $result = $this->db
            ->select("IsDayOn")
            ->from($this->main_table)
            ->where('Day', $date)
            ->get()
            ->result_array();

        return $result;
    }
}
