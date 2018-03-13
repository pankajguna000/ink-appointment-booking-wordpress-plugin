<?php
/**
 * Class and Function
 *
 * @class AptService
 * @insert_service()
 * @insert_timeslot()
 * @insert_dateslot()
 * @date_change()
 * @date_compare()
 * @apt_day_cheaking()
 *
 */
class AptService {
    /** Insert TIme in Databse
     * @insert_timeslot()
     */
    public function insert_service($srname, $srprice, $srrand) {
        global $wpdb, $apt_service;
        $srvalue = "SELECT * FROM $apt_service WHERE  service_rand =$srrand";
        $srvalue = $wpdb->get_row($srvalue);
        if (!empty($srname) && !empty($srprice)) {
            if (!$srvalue) {
                $query = $wpdb->insert($apt_service, array(
                    'service_name' => $srname,
                    'service_price' => $srprice,
                    'service_date' => date('Y-m-d'),
                    'service_rand' => $srrand
                        ));
            } else {
                echo "Please do not refresh this page";
            }
        } else {
            echo "This Field is empty.";
        }
    }
    /**
     * Insert TIme in Databse
     * @insert_timeslot()
     */
    public function insert_timeslot($timeid, $strtime, $endtime, $tssun, $tsrand, $bookslot) {
        global $wpdb, $apt_timeslot;
        $tsvalue = "SELECT * FROM $apt_timeslot WHERE  timeslot_rand =$tsrand";
        $tsvalue = $wpdb->get_row($tsvalue);
        if (empty($timeid)) {
            $maxid = $wpdb->get_var("SELECT MAX(timeslot_id) FROM $apt_timeslot");
            $slotarray = $wpdb->get_row("SELECT * FROM $apt_timeslot WHERE timeslot_id = $maxid", ARRAY_N);
            $timeid = $slotarray[1];
        }
        if (empty($bookslot)) {
            $bookslot = 1;
        }
        if (!$tsvalue) {
            $query = $wpdb->insert($apt_timeslot, array(
                'service_id' => $timeid,
                'timeslot_day' => $tssun,
                'timeslot_start_time' => $strtime,
                'timeslot_end_time' => $endtime,
                'timeslot_date' => date('Y-m-d'),
                'timeslot_rand' => $tsrand,
                'booking_number_time' => $bookslot
                    ));
        }
    }
    /**
     * Insert Date in Databse
     * @insert_dateslot()
     */
    public function insert_dateslot($srid, $strdate, $enddate, $chksu, $chkmo, $chktu, $chkwe, $chkth, $chkfr, $chksa, $tsrand) {
        global $wpdb, $apt_dateslot;
        $tsvalue = $wpdb->get_row("SELECT * FROM $apt_dateslot WHERE  timeslot_rand =$tsrand");
        $srvalue = $wpdb->get_row("SELECT * FROM $apt_dateslot WHERE  service_id = $srid");
        $tstimeid = $srvalue->service_id;
        if (!$tsvalue) {
            if (empty($tstimeid)) {
                $query = $wpdb->insert($apt_dateslot, array(
                    'service_id' => $srid,
                    'dsi_str_date' => $strdate,
                    'dsi_end_date' => $enddate,
                    'dsi_sun' => $chksu,
                    'dsi_mon' => $chkmo,
                    'dsi_tue' => $chktu,
                    'dsi_wed' => $chkwe,
                    'dsi_thu' => $chkth,
                    'dsi_fri' => $chkfr,
                    'dsi_sat' => $chksa,
                    'timeslot_date' => date('Y-m-d'),
                    'timeslot_rand' => $tsrand
                        ));
            } else {
                $wpdb->update($apt_dateslot, array('dsi_str_date' => $strdate, 'dsi_end_date' => $enddate, 'dsi_sun' => $chksu, 'dsi_mon' => $chkmo,
                    'dsi_tue' => $chktu, 'dsi_wed' => $chkwe, 'dsi_thu' => $chkth, 'dsi_fri' => $chkfr, 'dsi_sat' => $chksa, 'timeslot_date' => date('y-m-d'),
                    'timeslot_rand' => $tsrand), array('service_id' => $srid), array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',), array('%d'));
            }
        } // End refresh if
    }
    /**
     * Date Change format function
     * @date_change()
     */
    public function date_change($dd_mm_yyyy) {
        $parts = explode('/', $dd_mm_yyyy); //$dateformate = "12/25/2010";
        $yyyy_mm_dd = $parts[0] . '-' . $parts[1] . '-' . $parts[2];
        return $yyyy_mm_dd;
    }
    /**
     * Date Compare function
     * @date_compare()
     */
    public function date_compare($database_last_date, $database_first_date, $second_date) {
        $first_date_chk = $this->date_change($database_first_date);
        $last_date_chk = $this->date_change($database_last_date);
        $second_date_chk = $this->date_change($second_date);
        if ((strtotime($last_date_chk) >= strtotime($second_date_chk)) && (strtotime($first_date_chk) <= strtotime($second_date_chk))) {
            $match_date = "Match";
            return $match_date;
        } else {
            $not_match = "Not Match";
            return $not_match;
        }
    }
    /**
     * Day Cheaking function
     * @apt_day_cheaking()
     */
    public function apt_day_cheaking($front_sr_id, $front_end_date) {
        global $wpdb, $apt_timeslot, $apt_dateslot, $apt_service;
        // date chang format dd-mm-yyyy
        $change_date_format = $this->date_change($front_end_date);
        // week find like:Sun or Mon
        $dayname = date("D", strtotime($change_date_format));
        //database value find
        $sql_week_chk = $wpdb->get_row("SELECT * FROM $apt_dateslot Where service_id='$front_sr_id'", ARRAY_N);
        if ($dayname == "Sun") {
            // database multiple value count like:sunday=2
            $week_count = $wpdb->get_var("SELECT  COUNT(timeslot_day) FROM $apt_timeslot WHERE service_id=$front_sr_id AND timeslot_day='sunday'");
            if (($sql_week_chk[4] == 1) && ($week_count > 0)) {
                // database value find and return
                $sql = $wpdb->prepare("SELECT * FROM $apt_timeslot Where service_id= %d AND timeslot_day = %s ORDER BY timeslot_id ASC", $front_sr_id, 'sunday');
                return $sql;
            } else {
                $sql_error = "Not Available";
                return $sql_error;
            }
        } elseif ($dayname == "Mon") {
            // database multiple value count like:monday=2
            $week_count = $wpdb->get_var("SELECT  COUNT(timeslot_day) FROM $apt_timeslot WHERE service_id=$front_sr_id AND timeslot_day='monday'");
            if (($sql_week_chk[5] == 2) && ($week_count > 0)) {
                // database value find and return
                $sql = $wpdb->prepare("SELECT * FROM $apt_timeslot Where service_id= %d AND timeslot_day = %s ORDER BY timeslot_id ASC", $front_sr_id, 'monday');
                return $sql;
            } else {
                $sql_error = "Not Available";
                return $sql_error;
            }
        } elseif ($dayname == "Tue") {
            $week_count = $wpdb->get_var("SELECT  COUNT(timeslot_day) FROM $apt_timeslot WHERE service_id=$front_sr_id AND timeslot_day='tuesday'");
            if (($sql_week_chk[6] == 3) && ($week_count > 0)) {
                $sql = $wpdb->prepare("SELECT * FROM $apt_timeslot Where service_id= %d AND timeslot_day = %s ORDER BY timeslot_id ASC", $front_sr_id, 'tuesday');
                return $sql;
            } else {
                $sql_error = "Not Available";
                return $sql_error;
            }
        } elseif ($dayname == "Wed") {
            $week_count = $wpdb->get_var("SELECT  COUNT(timeslot_day) FROM $apt_timeslot WHERE service_id=$front_sr_id AND timeslot_day='wednesday'");
            if (($sql_week_chk[7] == 4) && ($week_count > 0)) {
                $sql = $wpdb->prepare("SELECT * FROM $apt_timeslot Where service_id= %d AND timeslot_day = %s ORDER BY timeslot_id ASC", $front_sr_id, 'wednesday');
                return $sql;
            } else {
                $sql_error = "Not Available";
                return $sql_error;
            }
        } elseif ($dayname == "Thu") {
            $week_count = $wpdb->get_var("SELECT  COUNT(timeslot_day) FROM $apt_timeslot WHERE service_id=$front_sr_id AND timeslot_day='thursday'");
            if (($sql_week_chk[8] == 5) && ($week_count > 0)) {
                $sql = $wpdb->prepare("SELECT * FROM $apt_timeslot Where service_id= %d AND timeslot_day = %s ORDER BY timeslot_id ASC", $front_sr_id, 'thursday');
                return $sql;
            } else {
                $sql_error = "Not Available";
                return $sql_error;
            }
        } elseif ($dayname == "Fri") {
            $week_count = $wpdb->get_var("SELECT  COUNT(timeslot_day) FROM $apt_timeslot WHERE service_id=$front_sr_id AND timeslot_day='friday'");
            if (($sql_week_chk[9] == 6) && ($week_count > 0)) {
                $sql = $wpdb->prepare("SELECT * FROM $apt_timeslot Where service_id= %d AND timeslot_day = %s ORDER BY timeslot_id ASC", $front_sr_id, 'friday');
                return $sql;
            } else {
                $sql_error = "Not Available";
                return $sql_error;
            }
        } elseif ($dayname == "Sat") {
            $week_count = $wpdb->get_var("SELECT  COUNT(timeslot_day) FROM $apt_timeslot WHERE service_id=$front_sr_id AND timeslot_day='saturday'");
            if (($sql_week_chk[10] == 7) && ($week_count > 0)) {
                $sql = $wpdb->prepare("SELECT * FROM $apt_timeslot Where service_id= %d AND timeslot_day = %s ORDER BY timeslot_id ASC", $front_sr_id, 'saturday');
                return $sql;
            } else {
                $sql_error = "Not Available";
                return $sql_error;
            }
        }
    }
// end apt_day_cheaking() function
    public function insert_data_frontend($front_apt_id, $front_apt_date, $front_apt_persion_name, $front_apt_name, $front_apt_time, $front_apt_price, $apt_data_email, $apt_data_mobile, $apt_data_message, $apt_data_rand, $apt_data_current_date, $apt_payment_method) {
        $parts = explode('/', $front_apt_date);
        $todaydate = $parts[0] . $parts[1] . $parts[2];
        global $wpdb, $appointment_data;
        $query = $wpdb->insert($appointment_data, array(
            'service_id' => $front_apt_id,
            'apt_data_date' => $front_apt_date,
            'apt_data_persion_name' => $front_apt_persion_name,
            'apt_data_service_name' => $front_apt_name,
            'apt_data_time' => $front_apt_time,
            'apt_data_price' => $front_apt_price,
            'apt_data_email' => $apt_data_email,
            'apt_data_mobile' => $apt_data_mobile,
            'apt_data_message' => $apt_data_message,
            'apt_data_rand' => $apt_data_rand,
            'apt_data_current_Date' => $apt_data_current_date,
            'apt_payment_method' => $apt_payment_method,
            'ddmmyyy' => $todaydate
                ));
    }
    public function insert_transaction($max_id, $front_apt_id, $apt_txn_booking_date, $apt_txn_service_name, $apt_txn_price, $apt_txn_payer_email, $apt_txn_status, $apt_txn_txnid, $apt_data_rand) {
        global $wpdb, $apt_transaction;
        $query = $wpdb->insert($apt_transaction, array(
            'TXN_ID' => $max_id,
            'service_id' => $front_apt_id,
            'apt_txn_booking_date' => $apt_txn_booking_date,
            'apt_txn_service_name' => $apt_txn_service_name,
            'apt_txn_price' => $apt_txn_price,
            'apt_txn_payer_email' => $apt_txn_payer_email,
            'apt_txn_status' => $apt_txn_status,
            'apt_txn_txnid' => $apt_txn_txnid,
            'apt_data_rand' => $apt_data_rand
                ));
    }
    public function date_available_cheak($front_sr_id, $front_end_date, $timechk) {
        global $wpdb, $appointment_data;
        $parts = explode('/', $front_end_date);
        $sql2 = $wpdb->prepare("SELECT * FROM $appointment_data Where service_id= %d ", $front_sr_id);
        $queries = $wpdb->get_results($sql2);
        foreach ($queries as $query) {
            $avail4 = $query->apt_data_date; //date
            $avail5 = $query->apt_data_time; //time
            $part2 = explode('/', $avail4);
            if (($avail5 == $timechk) && ($parts[0] == $part2[0])) {
                return true;
            }
        }
    }
//date_available_cheak() end
    public function seat_available_cheak($front_sr_id, $front_end_date, $timechk) {
        global $wpdb, $appointment_data;
        $parts = explode('/', $front_end_date);
        $fulldate = $parts[0] . $parts[1] . $parts[2];
        $week_count = $wpdb->get_var("SELECT  COUNT(ddmmyyy) FROM $appointment_data WHERE service_id='$front_sr_id' AND ddmmyyy='$fulldate' AND apt_data_time='$timechk'");
        return $week_count;
    }
    public function date_blank_cheak($front_sr_id) {
        global $wpdb, $apt_timeslot, $apt_dateslot, $apt_service, $appointment_data;
        $week_count = $wpdb->get_var("SELECT  COUNT(timeslot_start_time) FROM $apt_timeslot WHERE service_id='$front_sr_id' ");
//$week_count = $wpdb->get_var( "SELECT  COUNT(timeslot_day) FROM $appointment_data WHERE service_id='$front_sr_id' " );
        return $week_count;
    }
    function ink_browser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $ub = '';
        if (preg_match('/MSIE/i', $u_agent)) {
            $ub = "ie";
        }
        return $ub;
    }
    /**
     * Date Change format function
     * @date_change()
     */
    public function date_change_format($dd_mm_yyyy) {
        $parts = explode('/', $dd_mm_yyyy); //$dateformate = "12/25/2010";
        $mm_dd_yyyy = $parts[1] . '/' . $parts[0] . '/' . $parts[2];
        return $mm_dd_yyyy;
    }
    function all_day_time_set($timeid, $open_am_time, $close_am_time, $int_time, $arr_day, $tsrand, $booktime) {
        $time_in_24_open = date("H:i", strtotime($open_am_time));
        $time_in_24_close = date("H:i", strtotime($close_am_time));
        $open_time_1 = strtotime($time_in_24_open);
        $close_time_2 = strtotime($time_in_24_close);
        $totaltime_1 = $int_time;
        $totaltime2 = 0;
        while ($close_time_2 > $camptime) {
//first time caluculation
            $totaltime1 = $totaltime_1 - $int_time;
            $totaltime1 = $totaltime1 . '' . 'minutes';
            $firsttime = date("h:i A", strtotime($totaltime1, $open_time_1));
//second time caluculation
            $totaltime2 = $totaltime2 + $int_time;
            $totaltime_1 = $totaltime2 + $int_time;
            $totaltime2 = $totaltime2 . '' . 'minutes';
            $secondtime = date("h:i A", strtotime($totaltime2, $open_time_1));
            $camptime = strtotime($secondtime);
            //randum value
            if (!empty($arr_day[0])) {
                $tsrand = $tsrand + 1253;
                $this->insert_timeslot($timeid, $firsttime, $secondtime, $arr_day[0], $tsrand, $booktime);
            }
            if (!empty($arr_day[1])) {
                $tsrand = $tsrand + 1253;
                $this->insert_timeslot($timeid, $firsttime, $secondtime, $arr_day[1], $tsrand, $booktime);
            }
            if (!empty($arr_day[2])) {
                $tsrand = $tsrand + 1253;
                $this->insert_timeslot($timeid, $firsttime, $secondtime, $arr_day[2], $tsrand, $booktime);
            }
            if (!empty($arr_day[3])) {
                $tsrand = $tsrand + 1253;
                $this->insert_timeslot($timeid, $firsttime, $secondtime, $arr_day[3], $tsrand, $booktime);
            }
            if (!empty($arr_day[4])) {
                $tsrand = $tsrand + 1253;
                $this->insert_timeslot($timeid, $firsttime, $secondtime, $arr_day[4], $tsrand, $booktime);
            }
            if (!empty($arr_day[5])) {
                $tsrand = $tsrand + 1253;
                $this->insert_timeslot($timeid, $firsttime, $secondtime, $arr_day[5], $tsrand, $booktime);
            }
            if (!empty($arr_day[6])) {
                $tsrand = $tsrand + 1253;
                $this->insert_timeslot($timeid, $firsttime, $secondtime, $arr_day[6], $tsrand, $booktime);
            }
        }
    }
//end function

function apt_cpt_1(){
$text = rand(0,9); 
return $text;

}
function apt_cpt_2(){
$text1 = rand(0,9); 
return $text1;

}
}
// End Class
?>