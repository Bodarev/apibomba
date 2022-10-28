<?php
function get_courier_delivery_date(): array
{
    $courierDeliveryDays = 1;
    $d = new DateTimeImmutable();

    $dates = get_working_dates($d, $courierDeliveryDays);
    $deliveryDate = array_pop($dates);
    $cDelivery = DateTimeImmutable::createFromFormat('Y-m-d', $deliveryDate);

    return ['deliveryDate' => $cDelivery];
}

function get_working_dates($d, $deliveryDaysQuantity, $isIncludingStartDate = false): array
{
    $ci =&get_instance();
    $ci->load->model('delivery_specific_day_model');
    $i = 0;
    $deliveryDays = array();

    while (count($deliveryDays) < $deliveryDaysQuantity) {
        if (!$isIncludingStartDate) {
            $i++;
        }

        $isWorkingDay = false;
        $isDayDefined = false;

        $day = ($d->modify('+' . $i . ' day'));
        $dayString = $day->format('Y-m-d');

        $holidayInfo = $ci->delivery_specific_day_model->get_info_by_date($dayString);

        if (count($holidayInfo) > 0) {
            $dayInfo = $holidayInfo[0];

            if ((int) $dayInfo['IsDayOn']) {
                $isWorkingDay = true;
            }
            $isDayDefined = true;
        }

        if (!$isDayDefined && (int)$day->format('N') < 6) {
            $isWorkingDay = true;
        }

        if ($isWorkingDay) {
            $deliveryDays[] = $dayString;
        }

        if ($isIncludingStartDate) {
            $i++;
        }
    }
    return $deliveryDays;
}
