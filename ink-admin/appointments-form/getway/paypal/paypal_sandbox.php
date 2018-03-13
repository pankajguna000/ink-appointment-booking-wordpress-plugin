<?php
global $wpdb, $apt_service;
$aptname = $_POST['fname'];
$aptserviceid = $_POST['service_select'];
$srchk = $wpdb->get_row("SELECT * FROM $apt_service WHERE service_id = '$aptserviceid' ", ARRAY_N);
$_POST['service_select1'];
$apttime = $_POST['time'];
$aptdate = $_POST['aptcal'];
$aptrandom = $_POST['random'];
$aptemail = $_POST['aptemail'];
$aptphone = $_POST['aptphone'];
$aptmessage = $_POST['aptmessage'];
//$url = site_url();
$url = get_option('return_apt_url');
//$url = 'http://127.0.0.1/aptproject/?page_id=71';
$url = $url . '/?';
define('PAYPAL_RETURN', $url . 'aptpaypalamountpaid&paypal-trans&apttime=' . $apttime . '&aptdate=' . $aptdate . '&aptname='
        . $aptname . '&aptserviceid=' . $aptserviceid . '&aptrandom=' . $aptrandom . '&aptemail=' . $aptemail . '&aptphone=' . $aptphone . '&aptmessage=' . $aptmessage);
$paypalamount = $srchk[2];
$service_title = $srchk[1];
//$paymentOpts = get_payment_optins($_REQUEST['pay_method']);
$merchantid = get_option('apt_merchaint_email');
$cancel_return = PAYPAL_RETURN;
//$notify_url = PAYPAL_RETURN;
$currency_code = get_option('apt_currency_code');
$returnUrl = PAYPAL_RETURN;
?>
<form name="paypal_sandbox" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" style="padding: 0; margin: 0;">
    <input type="hidden" name="business" value="<?php echo $merchantid; ?>" />
    <!-- Instant Payment Notification & Return Page Details -->
    <input type="hidden" name="notify_url" value="<?php echo $notify_url; ?>" />
    <input type="hidden" name="cancel_return" value="<?php echo $cancel_return; ?>" />
    <input type="hidden" name="return" value="<?php echo $returnUrl; ?>" />
    <input type="hidden" name="rm" value="2" />
    <!-- Configures Basic Checkout Fields -->
    <input type="hidden" name="lc" value="" />
    <input type="hidden" name="no_shipping" value="1" />
    <input type="hidden" name="no_note" value="1" />
   <!-- <input type="hidden" name="custom" value="localhost" />-->
    <input type="hidden" name="currency_code" value="<?php echo $currency_code; ?>" />
    <input type="hidden" name="first_name" value="<?php echo $_POST['fname']; ?>" />
    <input type="hidden" name="page_style" value="paypal" />
    <input type="hidden" name="charset" value="utf-8" />
    <input type="hidden" name="item_name" value="<?php echo $service_title; ?>" />
    <input type="hidden" value="_xclick" name="cmd"/>
    <input type="hidden" name="amount" value="<?php echo $paypalamount; ?>" />
</form>
<div class="wrapper" >
    <div class="clearfix container_message">
        <center><h1 class="head"><?php echo 'Processing.... Please Wait...'; ?></h1></center>
        <center><img class="processing" src="<?php echo GIF_IMAGE; ?>"/></center>
    </div>
</div>
<script>
    setTimeout("document.paypal_sandbox.submit()",50); 
</script>
