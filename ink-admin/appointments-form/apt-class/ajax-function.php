<?php
function text_ajax_process_request() {
    global $wpdb, $apt_timeslot, $apt_dateslot, $apt_service, $appointment_data;
    $front_sr_id = $_POST['id'];
    // first check if data is being sent and that it is the data we want   
    if (isset($_POST["id"])) {
        $datem = new AptService();
        $datechange = $_POST['ids'];
        $dateformat = explode('/', $datechange);
        $front_end_date = $dateformat[1] . '/' . $dateformat[0] . '/' . $dateformat[2];
        $sql_day_chk = $datem->apt_day_cheaking($front_sr_id, $front_end_date);
        $sql_week_chk = $wpdb->get_row("SELECT * FROM $apt_dateslot Where service_id='$front_sr_id'", ARRAY_N);
        $database_first_date = $sql_week_chk[2];
        $database_last_date = $sql_week_chk[3];
        $matchdate = $datem->date_compare($database_last_date, $database_first_date, $front_end_date);
        if ($matchdate == 'Match') {
            if ($sql_day_chk != 'Not Available') {
                $queries = $wpdb->get_results($sql_day_chk);
                foreach ($queries as $query) {
                    $boook_n_t = $query->booking_number_time;
                    $timechk = $query->timeslot_start_time . '-' . $query->timeslot_end_time;
                    $avail = $datem->date_available_cheak($front_sr_id, $front_end_date, $timechk);
                    $seat_avail = $datem->seat_available_cheak($front_sr_id, $front_end_date, $timechk);
                    if (($avail != true) || ($boook_n_t > $seat_avail)) {
                        echo '<option>' . $query->timeslot_start_time . '-' . $query->timeslot_end_time . '</option>';
                        $i = "finddata";
                    } //avil true closed if
                }// end foreach
                if ($i != 'finddata') {
                    echo '<option value="notavi">' . NOT_AVI . '</option>';
                }
            } // end daychk if
            else {
                echo '<option value="notavi">' . NOT_AVI . '</option>';
            }
        }    // end match if
        else {
            echo '<option value="notavi" >' . NOT_AVI . '</option>';
        }
        die();
    }
    $rnam = $_POST['price'];
    if (isset($_POST["price"])) {
        $price = get_option('apt_currency_symbol');
        $datechk = $wpdb->get_row("SELECT * FROM $apt_service WHERE service_id = '$rnam' ", ARRAY_N);
        if (!empty($datechk[2])) {
            $prices = '  Price is  ' . $price . '' . $datechk[2];
        } else {
            $prices = "Select any service.";
        }
        echo " <input type='text' name='sr_price' id='sr_price'  class='inktext inklarge' value='" . $prices . "'/>";
        die();
    }
}
add_action('wp_ajax_master_response', 'text_ajax_process_request');
add_action('wp_ajax_nopriv_master_response', 'text_ajax_process_request');
?>