<?php
add_shortcode('ink-appointments-form', 'ink_appoitment');

function ink_appoitment() {
    ?>
    <script>
        jQuery.noConflict();
        jQuery(function() {
            jQuery( "#aptcal" ).datepicker();
            jQuery("#aptcal").datepicker("option", "minDate", 0);
		        });
    </script>
    <?php
    global $wpdb, $apt_service;
	if($_POST['chk_apt']!=$_POST['apt_cpt']){
	$cpt_true=true;
 }
    if (isset($_POST['submit']) && $cpt_true==false) {
        $_POST['time'];
        if (($_POST['time'] == 'notavi') || ($_POST['service_select'] == 'notavi')) {
            echo "<p>Please Insert All data.</p>";
        } else {
            echo $badUrl = $_POST['sr_price'];
            if (get_option('apt_paypal') == "sandbox") {
                gateway_sandbox();
            } elseif (get_option('apt_paypal') == "paypal") {
                gateway_paypal();
            } elseif (get_option('apt_paypal') == "cash") {
                $datechange = $_POST['aptcal'];
                $dateformat = explode('/', $datechange);
                $newaptdate = $dateformat[1] . '/' . $dateformat[0] . '/' . $dateformat[2];
                cash_payment($_POST['service_select'], $_POST['time'], $newaptdate, $_POST['fname'], $_POST['aptemail'], $_POST['aptphone'], $_POST['aptmessage'], $_POST['random']);
            }
        }
    }
    if (isset($_GET['paypal-trans'])) {
        ink_apt_trans_display();
    } else {
        $ruri = $_SERVER['REQUEST_URI'];
        $sname = $_SERVER['SERVER_NAME'];
        $fullpath = 'http://' . $sname . $ruri;
        update_option('return_apt_url', $fullpath);
        $br = new AptService();
        $iechk = $br->ink_browser();
if((!isset($_POST['submit']))||($_POST['chk_apt']!=$_POST['apt_cpt'])){?>

 


            <div class="ink-container">
			<div class="inkappointment_wrapper">
                                      <div class="inkappointment_form_top">
                                    <h2 class="msg_text"><?php echo "Book Your Appointment"; ?></h2>
                                    </div>
                                    <div class="inkappointment_form_wrapper">
                <form method="post" action="#" id="ink-form" name="ink-form" class="ink-form" >
                
                    <header id="ink-header" class="ink-info">
                    </header>
                    <ul class="inkappform">
                        <li></li>
                        <li><input type="text" name="fname" id="fname" class="inktext inklarge inkrequired"   placeholder="Name"  maxlength="100" />
						<label id="apt_error"> </label>
                        </li>
                        <li><input type="email" name="aptemail" id="aptemail" class="inktext inklarge inkrequired" placeholder="Email"  maxlength="100" /></li>
                        <li><input type="text" name="aptphone" id="aptphone" class="inktext inklarge" placeholder="Contact Number"  maxlength="100" /></li>
                        <li><span class="fix_date"><?php echo get_option('apt_fix_date'); ?></span></li>
                        <li class="select_item"><select  id="service_select" name="service_select" class="inktext inklarge inkrequired" >
                                <option  value="noavi">Select Service</option>
                                <?php $showts = mysql_query("SELECT * FROM $apt_service ");
                                while ($timerow = mysql_fetch_array($showts)) { ?>
                                    <option  value="<?php echo $timerow['service_id']; ?>"><?php echo $timerow['service_name']; ?></option>
            <?php } ?> </select></li>
                        <li><input type="text" name="aptcal" id="aptcal" class="dateField inktext inklarge"  placeholder="Select Date" /></li>
                        <li class="select_item"><select id="time" name="time" class="inktext inklarge inkrequired">
                                <option value="notavi">Select Time</option>  </select></li>
                        <li><div id="price">
                                <input type="text" name="sr_price" id="sr_price"  class="inktext inklarge inkrequired" value="Service Price"/>
                            </div> </li>
                        <li><span class="fix_date"><?php echo get_option('apt_custom_msg'); ?></span></li>
                        <li><textarea name="aptmessage" id="aptmessage" class="inktext inklarge" maxlength="255" rows="3" cols="50" placeholder="Your Message (Optional)" ></textarea></li>
					
                        <input type="hidden" name="random" id="random"  value="<?php echo rand(); ?>"/>
                        <li class="submit_bg">
                            <input type="submit" name="submit" id="submit"  class='ink-submit inkrequired' value="Book Appointment"/>  </li>
                    </ul>
                </form>
				
				 </div>
                                    
                                </div>
				
            </div>
			
			
            <?php
        } //submit not set
    }
}
?>
