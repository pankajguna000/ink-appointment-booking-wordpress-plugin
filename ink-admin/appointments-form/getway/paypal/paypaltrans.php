<?php
class InkPaypal {
    public $item_name = '';
    public $item_number = 0;
    public $payment_status = '';
    public $payment_amount = 0;
    public $payment_currency = '';
    public $txn_id = '';
    public $receiver_email = '';
    public $payer_email = '';
    public $status = 0;
    //Recurring variable
    function __construct() {
        // parent::__construct();
        $this->appointments_paypal_trans();
    }
    public function appointments_paypal_trans() {
        global $current_user;
        // assign posted variables to local variables       
        $this->first_name = $_POST['first_name'];
        $this->item_name = $_POST['item_name'];
        $this->item_number = $_POST['item_number'];
        $this->payment_status = $_POST['payment_status'];
        $this->payment_amount = $_POST['mc_gross'];
        $this->payment_currency = $_POST['mc_currency'];
        $this->txn_id = $_POST['txn_id'];
        $this->receiver_email = $_POST['receiver_email'];
        $this->payer_email = $_POST['payer_email'];
        if ($this->payment_status == 'Completed' || $this->payment_status == 'Pending') {
            $this->status = 1;
            $this->payment_status = 'Completed';
            $post_status_to_admin = "Payment Received";
            $post_status_to_client = "Your @" . $store_name . " is successfully completed.";
        }
    }
    public function mail_send_func() {
        /**
         * Send transaction notification to admin or client
         */
        $personname = $_GET['aptname'];
        $aptime = $_GET['apttime'];
        $aptdate = $_GET['aptdate'];
        $aptemail = $_GET['aptemail'];
        $apt_txn_booking_date = date("F j, Y, g:i a");
        $url = site_url();
        $adminurl = str_replace('http://', '', $url);
        $transaction_details .= "Hello  $personname,\r";
        $transaction_details .= "\r";
        $transaction_details .= "Your Appointment had been fixed, below are the details of your appointment. \r \r";
        $transaction_details .= "Service Name: $this->item_name \r";
        $transaction_details .= "Appointment Date: $aptdate\r";
        $transaction_details .= "Appointment Time: $aptime\r";
        $transaction_details .= "Amount Paid: $this->payment_amount\r";
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
    }
    public function admin_payment_mail() {
        /** Send transaction notification to admin * */
        $personname = $_GET['aptname'];
        $aptime = $_GET['apttime'];
        $aptdate = $_GET['aptdate'];
        $aptemail = $_GET['aptemail'];
        $apt_txn_booking_date = date("F j, Y, g:i a");
        $admin_email = get_option('admin_email');
        $url = site_url();
        $adminurl = str_replace('http://', '', $url);
        $subject = "New Appointment Booked through $adminurl";
        $transaction_details .= "Hello,\r \r";
        $transaction_details .= "New Appointment booked. Below are the details of the Appointment which is booked.\r";
        $transaction_details .= "\r";
        $transaction_details .= "Service Name Booked: $this->item_name \r";
        $transaction_details .= "Person Name: $personname \r";
        $transaction_details .= "Appointment Date Booked: $aptdate\r";
        $transaction_details .= "Appointment Time Booked: $aptime\r";
        $transaction_details .= "Amount Received: $this->payment_amount \r \r";
        $transaction_details .= "You can login to your dashboard to see all the details.\r";
        $transaction_details .= "Thanks\r";
        $transaction_details = __($transaction_details, THEME_SLUG);
        $content = $transaction_details;
        $headersa = 'From: ' . $aptemail . ' <' . $admin_email . '>' . "\r\n" . 'Reply-To: ' . $aptemail;
        wp_mail($admin_email, $subject, $content, $headersa); //email to client
    }
}
//add_shortcode('pay-status', 'ink_apt_trans_display');	
function ink_apt_trans_display() {
    global $wpdb, $apt_service, $appointment_data, $apt_transaction;
    $paypal_init = new InkPaypal();
    $as = new AptService();
    $showdate = $as->date_change_format($_GET['aptdate']);
    if (!empty($paypal_init->item_name)) {
        $paypal_init->mail_send_func();
        echo PAYPAL_RET_TEXT . '</b><br/>';
        //echo "Payment Persoin Name:&nbsp;&nbsp;<b>" . $paypal_init->first_name . '</b><br/>';
        echo "<p>";
        echo "Service Name:&nbsp;&nbsp;<b>" . $paypal_init->item_name . '</b><br/>';
        echo "Appointment Date:&nbsp;&nbsp;<b>" . $showdate . '</b><br/>';
        echo "Appointment Time:&nbsp;&nbsp;<b>" . $_GET['apttime'] . '</b><br/>';
        echo "Amount Paid:&nbsp;&nbsp;<b>" . $paypal_init->payment_amount . '</b><br/>';
        echo "Payment Currency:&nbsp;&nbsp;<b>" . $paypal_init->payment_currency . '</b><br/>';
        echo "Transaction ID:&nbsp;&nbsp;<b>" . $paypal_init->txn_id . '</b><br/>';
        echo "Payment Receiver Email:&nbsp;&nbsp;<b>" . $paypal_init->receiver_email . '</b><br/>';
        echo "Payment Payer Email:&nbsp;&nbsp;<b>" . $paypal_init->payer_email . '</b><br/>';
        echo "Mode of Payment:&nbsp;&nbsp;<b>Paypal</b><br/>";
        echo "Your Payment Status:&nbsp;&nbsp;<b>" . $paypal_init->payment_status . '</b><br/>';
        echo "</p><br/>";
        $randnumber = $_GET['aptrandom'];
        $randvalue = $wpdb->get_row("SELECT * FROM $appointment_data WHERE  apt_data_rand ='$randnumber'");
        if ((!$randvalue) && (!empty($paypal_init->item_name)) && (!empty($paypal_init->txn_id)) && (!empty($paypal_init->payment_amount))) {
            $service_id = $_GET["aptserviceid"];
            $front_apt_price = $paypal_init->payment_amount . '' . $paypal_init->payment_currency;
            $apt_txn_booking_date = date("F j, Y, g:i A");
            $save = new AptService();
            $save->insert_data_frontend($service_id, $showdate, $_GET['aptname'], $paypal_init->item_name, $_GET['apttime'], $front_apt_price, $_GET['aptemail'], $_GET['aptphone'], $_GET['aptmessage'], $randnumber, $apt_txn_booking_date, 'paypal');
            $max_id = $wpdb->get_var("SELECT MAX(APTID) FROM $appointment_data");
            $save->insert_transaction($max_id, $service_id, $apt_txn_booking_date, $paypal_init->item_name, $front_apt_price, $paypal_init->payer_email, $paypal_init->payment_status, $paypal_init->txn_id, $randnumber);
        }
        $paypal_init->admin_payment_mail();
    } else {
        echo APT_USF;
    };
}
?>
