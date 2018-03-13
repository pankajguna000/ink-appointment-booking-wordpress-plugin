<?php
function past_appointment_detail() {
    global $wpdb, $appointment_data, $apt_transaction;
    if (isset($_POST['chkall_aptsubmit'])) {
        if (!empty($_POST['check_apt_list'])) {
            foreach ($_POST['check_apt_list'] as $checked) {
                $wpdb->query($wpdb->prepare("DELETE FROM $appointment_data WHERE APTID = %d", $checked));
                $wpdb->query($wpdb->prepare("DELETE FROM $apt_transaction WHERE TXN_ID = %d", $checked));
            }
        }
    }
    $booking_date = date("d/m/Y");
    $todaydate = date("dmY");
    $pastaptsql = mysql_query("SELECT * FROM $appointment_data WHERE ddmmyyy < '$todaydate'");
    $nr = mysql_num_rows($pastaptsql);
    if ($nr >= 1) {
        if (isset($_GET['pn'])) {
            $pn = preg_replace('#[^0-9]#i', '', $_GET['pn']);
        } else {
            $pn = 1;
        }
        $itemsPerPage = 10;
        $lastPage = ceil($nr / $itemsPerPage);
        if ($pn < 1) {
            $pn = 1;
        } else if ($pn > $lastPage) {
            $pn = $lastPage;
        }
        $centerPages = "";
        $sub1 = $pn - 1;
        $sub2 = $pn - 2;
        $add1 = $pn + 1;
        $add2 = $pn + 2;
        if ($pn == 1) {
            $centerPages .= '&nbsp; <span class="selectpeginumber" class="pagNumActive">' . $pn . '</span> &nbsp;';
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=pasttrans&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
        } else if ($pn == $lastPage) {
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=pasttrans&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <span class="selectpeginumber" class="pagNumActive">' . $pn . '</span> &nbsp;';
        } else if ($pn > 2 && $pn < ($lastPage - 1)) {
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=pasttrans&pn=' . $sub2 . '">' . $sub2 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=pasttrans&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <span class="selectpeginumber" class="pagNumActive">' . $pn . '</span> &nbsp;';
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=pasttrans&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=pasttrans&pn=' . $add2 . '">' . $add2 . '</a> &nbsp;';
        } else if ($pn > 1 && $pn < $lastPage) {
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=pasttrans&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <span class="selectpeginumber" class="pagNumActive">' . $pn . '</span> &nbsp;';
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=pasttrans&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
        }
        $limit = 'LIMIT ' . ($pn - 1) * $itemsPerPage . ',' . $itemsPerPage;
//$sql2 = mysql_query("SELECT * FROM $appointment_data ORDER BY APTID DESC $limit");
        $pastsqldata = $wpdb->prepare("SELECT * FROM $appointment_data  WHERE ddmmyyy < '$todaydate' ORDER BY ddmmyyy DESC $limit");
        $paginationDisplay = "";
        if ($lastPage != "1") {
//$paginationDisplay .= 'Page <strong>' . $pn . '</strong> of ' . $lastPage . '&nbsp;  &nbsp;  &nbsp; ';
            if ($pn != 1) {
                $previous = $pn - 1;
                $paginationDisplay .= '&nbsp;  <a class="inkpeginumber" href="' . get_permalink() . '?page=pasttrans&pn=' . $previous . '">«</a> ';
            }
            $paginationDisplay .= '<span class="paginationNumbers">' . $centerPages . '</span>';
            if ($pn != $lastPage) {
                $nextPage = $pn + 1;
                $paginationDisplay .= '&nbsp;  <a class="inkpeginumber" href="' . get_permalink() . '?page=pasttrans&pn=' . $nextPage . '">»</a> ';
            }
        }
    }
    ?>
    <?php if ($_GET['page'] == 'pasttrans') {
 //wp_enqueue_script('jquery-chk-min', INK_ADMIN . '/js/jquery.min.js', array('jquery'));
 //wp_enqueue_script('jquery-chk-min', '../../../../../../wp-includes/js/jquery.min.js', array('jquery'));
	?>
      
        <SCRIPT language="javascript">
            $(function(){
         
                // add multiple select / deselect functionality
                $("#selectall_chkapt").click(function () {
                    $('.chk_info').attr('checked', this.checked);
                });
         
                // if all checkbox are selected, check the selectall checkbox
                // and viceversa
                $(".chk_info").click(function(){
         
                    if($(".chk_info").length == $(".chk_info:checked").length) {
                        $("#selectall_chkapt").attr("checked", "checked");
        			
                    } else {
                        $("#selectall_chkapt").removeAttr("checked");
                    }
         
                });
            });
        </SCRIPT>
    <?php } ?>
    <div class="showdata">
        <h3>All Past Appointments</h3>
        <form action="" method="post" >
            <table id="apt_data_show" class="wp-list-table widefat fixed pages" >
                <thead >
                    <tr>
                        <th  scope="col">Service Name</th>
                        <th  scope="col">Appointment Date</th>
                        <th  scope="col">Appointment Time</th>
                        <th  scope="col"> Person Name</th>
                        <th  scope="col">Contact Email</th>
                        <th  scope="col">Contact Number</th>
                        <th  scope="col">Message</th>
                        <th  scope="col">Amount Paid</th>
                        <th  scope="col">Paid Date</th>
                        <th  scope="col" width="50px;" ><input type="checkbox" id="selectall_chkapt"/></th>
                    </tr>
                </thead>
                <?php
                $queries = $wpdb->get_results($pastsqldata);
                ?>
                <tbody>
                    <?php
                    $as = new AptService();
                    if (!empty($queries)) {
                        if ($nr >= 1) {
                            foreach ($queries as $query) {
                                ?>
                                <tr>
                                    <th  scope="col"><?php echo $query->apt_data_service_name; ?></th>
                                    <th  scope="col"><?php echo $as->date_change_format($query->apt_data_date); ?></th>
                                    <th  scope="col"><?php echo $query->apt_data_time; ?></th>
                                    <th  scope="col"><?php echo $query->apt_data_persion_name; ?></th>
                                    <th  scope="col"><?php echo $query->apt_data_email; ?></th>
                                    <th  scope="col"><?php echo $query->apt_data_mobile; ?></th>
                                    <th  scope="col"><?php echo $query->apt_data_message; ?></th>
                                    <?php if ($query->apt_payment_method == 'paypal') { ?>
                                        <th  scope="col"><a href="<?php echo get_permalink() . "?page=aptservice&payment=" . $query->APTID; ?>"><?php echo $query->apt_data_price; ?></a></th>
                <?php } else { ?><th  scope="col"><?php echo $query->apt_data_price; ?></th> <?php } ?>
                                    <th  scope="col"><?php echo $query->apt_data_current_date; ?></th>
                                    <th class="textcenter"><input type="checkbox" class="chk_info" name="check_apt_list[]" value="<?php echo $query->APTID; ?>"/></th>
                                </tr>
            <?php }
        } ?>
                        <tr>
                            <th  colspan=10 scope="col"><a href="<?php echo get_permalink() . '?page=aptservice'; ?> ">Back to All Booked Appointments</a><input type='submit' id='chkall_aptsubmit' name='chkall_aptsubmit'  value='Delete Checked'/></th> 
                        </tr>
    <?php } ?>
                </tbody>
            </table>
        </form>
        <div class="apt-pegi">	<strong><?php echo $nr; ?>&nbsp Items</strong>&nbsp &nbsp <?php echo $paginationDisplay; ?> </div> 
    </div>
    <?php
}
?>