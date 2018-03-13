<?php
/**
 * 	 Paypal Transaction	
 * @ gateway_sandbox
 * */
function gateway_sandbox() {
    if (isset($_POST['submit'])) {
        if (file_exists(GETWAY_PATH . "/paypal_sandbox.php")) {
            GETWAY_PATH . "/paypal_sandbox.php";
            include_once(GETWAY_PATH . "/paypal_sandbox.php");
        }
    }
}
/**
 * 	 Paypal Transaction	
 * @ gateway_paypal
 * */
function gateway_paypal() {
    if (isset($_POST['submit'])) {
        if (file_exists(GETWAY_PATH . "/paypal_response.php")) {
            GETWAY_PATH . "/paypal_response.php";
            include_once(GETWAY_PATH . "/paypal_response.php");
        }
    }
}
function cash_payment($sr_apt_id, $sr_apt_time, $sr_apt_date, $sr_apt_persion_name, $sr_apt_email, $sr_apt_phone, $sr_msg, $sr_data_rand) {
    global $wpdb, $apt_service, $appointment_data;
    $sql_srdata = $wpdb->get_row("SELECT * FROM $apt_service Where service_id='$sr_apt_id'", ARRAY_N);
    $cr_code = get_option('apt_currency_code');
    $price = $sql_srdata[2] . '&nbsp' . $cr_code . '&nbsp- Pay Cash Later';
    $priceshow = $sql_srdata[2] . '' . $cr_code;
    $apt_txn_booking_date = date("F j, Y, g:i A");
    $apt = new AptService();
    if (isset($_POST['submit'])) {
        $sql_val = $wpdb->get_row("SELECT * FROM $appointment_data Where apt_data_rand='$sr_data_rand'");
        if (!$sql_val) {
            $apt->insert_data_frontend($sr_apt_id, $sr_apt_date, $sr_apt_persion_name, $sql_srdata[1], $sr_apt_time, $price, $sr_apt_email, $sr_apt_phone, $sr_msg, $sr_data_rand, $apt_txn_booking_date, 'cash');
            echo '<p>Your appointment has been booked successfully. You have to pay the amount of ' . $priceshow . ' at the time of appointment.</br>Thanks</p>';
            /**
             * Send transaction notification to admin or client
             */
            $personname = $sr_apt_persion_name;
            $servicename = $sql_srdata[1];
            $aptime = $sr_apt_time;
            $aptdate = $sr_apt_date;
            $aptemail = $sr_apt_email;
            $url = site_url();
            $adminurl = str_replace('http://', '', $url);
            $transaction_details .= "Hello  $personname,\r";
            $transaction_details .= "\r";
            $transaction_details .= "Your Appointment had been fixed, below are the details of your appointment. \r \r";
            $transaction_details .= "Service Name: $servicename \r";
            $transaction_details .= "Appointment Date: $aptdate\r";
            $transaction_details .= "Appointment Time: $aptime\r";
            $transaction_details .= "Amount Paid: $priceshow\r";
            $transaction_details .= "Date: $apt_txn_booking_date\r \r";
            $transaction_details .= "Thanks for booking with us.\r";
            $transaction_details .= "Warm Regards,\r \r";
            $transaction_details .= "$adminurl\r";
            $subject = __("Your Appointment Confirmation Email", THEME_SLUG);
            $filecontent = $transaction_details;
            $admin_email = get_option('admin_email');
            $headers = 'From: ' . $admin_email . ' <' . $aptemail . '>' . "\r\n" . 'Reply-To: ' . $admin_email;
            $header = 'From: ' . $aptemail . ' <' . $admin_email . '>' . "\r\n" . 'Reply-To: ' . $aptemail;
            //mail($to_admin, $subject, $filecontent, $headers);
            wp_mail($aptemail, $subject, $filecontent, $headers); //email to user
            wp_mail($admin_email, $subject, $filecontent, $header); //email to admin								
        } //refresh value if end
    } //submit data if end
}
//function end
?>