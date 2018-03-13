<?php
function showdata() {
    global $wpdb, $appointment_data;
    $booking_date = date("d/m/Y");
    $todaydate = date("dmY");
    $aptsql = mysql_query("SELECT * FROM $appointment_data WHERE ddmmyyy >= '$todaydate' ");
    $nr = mysql_num_rows($aptsql);
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
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
        } else if ($pn == $lastPage) {
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <span class="selectpeginumber" class="pagNumActive">' . $pn . '</span> &nbsp;';
        } else if ($pn > 2 && $pn < ($lastPage - 1)) {
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&pn=' . $sub2 . '">' . $sub2 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <span class="selectpeginumber" class="pagNumActive">' . $pn . '</span> &nbsp;';
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&pn=' . $add2 . '">' . $add2 . '</a> &nbsp;';
        } else if ($pn > 1 && $pn < $lastPage) {
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <span class="selectpeginumber" class="pagNumActive">' . $pn . '</span> &nbsp;';
            $centerPages .= '&nbsp; <a class="inkpeginumber"  href="' . get_permalink() . '?page=aptservice&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
        }
        $limit = 'LIMIT ' . ($pn - 1) * $itemsPerPage . ',' . $itemsPerPage;
//$sql2 = mysql_query("SELECT * FROM $appointment_data ORDER BY APTID DESC $limit");
        $sqldata = "SELECT * FROM $appointment_data  WHERE ddmmyyy >= '$todaydate' ORDER BY ddmmyyy ASC $limit";
        $paginationDisplay = "";
        if ($lastPage != "1") {
//$paginationDisplay .= 'Page <strong>' . $pn . '</strong> of ' . $lastPage . '&nbsp;  &nbsp;  &nbsp; ';
            if ($pn != 1) {
                $previous = $pn - 1;
                $paginationDisplay .= '&nbsp;  <a class="inkpegi" href="' . get_permalink() . '?page=aptservice&pn=' . $previous . '">«</a> ';
            }
            $paginationDisplay .= '<span  class="paginationNumbers">' . $centerPages . '</span>';
            if ($pn != $lastPage) {
                $nextPage = $pn + 1;
                $paginationDisplay .= '&nbsp;  <a class="inkpegi" href="' . get_permalink() . '?page=aptservice&pn=' . $nextPage . '">»</a> ';
            }
        }
    }
    /*     * * Csv file Download * */
    $uploads = wp_upload_dir();
    $aptfile = $uploads[basedir];
    $import_path = $uploads[baseurl];
    $import_appointment = $import_path . "/appointment_data.csv";
    $aptfilename = $aptfile . "/appointment_data.csv";
    $handle = fopen($aptfilename, 'w+');
    fputcsv($handle, array('Sr.No.', 'Appointment Date', 'Appointment Time', 'Person Name', 'Email Address', 'Contact Number', 'Message', 'Amount Paid', 'Payer Paypal Email', 'Transaction ID', 'Paid Date'));
    $count = 1;
//data fetch for CSV file	
    global $apt_transaction, $wpdb;
    $csvsql = mysql_query("SELECT * FROM $appointment_data");
    while ($row = mysql_fetch_array($csvsql)) {
        $aptid = $row['APTID'];
        $apt_ink = $wpdb->get_row("SELECT * FROM $apt_transaction WHERE TXN_ID ='$aptid'", ARRAY_N);
        $handle = fopen($aptfilename, 'a');
        fputcsv($handle, array($count, $row["apt_data_date"], $row["apt_data_time"], $row["apt_data_persion_name"], $row["apt_data_email"], $row["apt_data_mobile"], $row["apt_data_message"], $row["apt_data_price"], $apt_ink[6], $apt_ink[7], $row["apt_data_current_date"]));
        fclose($handle);
        $count++;
    }
    ?>
	  <?php if ($_GET['page'] == 'aptservice') {
	//wp_enqueue_script('jquery-chk-show', INK_ADMIN . '/js/jquery.min.js', array('jquery'));	
	 if (isset($_POST['chkall_sub'])) {
        if (!empty($_POST['check_apt_show'])) {
            foreach ($_POST['check_apt_show'] as $checked) {
                $wpdb->query($wpdb->prepare("DELETE FROM $appointment_data WHERE APTID = %d", $checked));
                $wpdb->query($wpdb->prepare("DELETE FROM $apt_transaction WHERE TXN_ID = %d", $checked));
            }
        }
    }
		?>
      
        <SCRIPT language="javascript">
            $(function(){
         
                // add multiple select / deselect functionality
                $("#show_chkapt").click(function () {
		          $('.apt_chk').attr('checked', this.checked);
                });
         
                // if all checkbox are selected, check the selectall checkbox
                // and viceversa
                $(".apt_chk").click(function(){
         alert("gg");
                    if($(".apt_chk").length == $(".apt_chk:checked").length) {
                        $("#show_chkapt").attr("checked", "checked");
        			
                    } else {
                        $("#show_chkapt").removeAttr("checked");
                    }
         
                });
            });
        </SCRIPT>
    <?php } ?>
    <div class="showdata">
        <h3>Booked Appointments</h3>
        <form action="#" method="post" >
            <table id="apt_data_show" class="wp-list-table widefat fixed pages" >
                <thead >
                    <tr>
                        <th  scope="col">Service Name</th>
                        <th  scope="col">Appointment Date</th>
                        <th  scope="col">Appointment Time</th>
                        <th  scope="col">Client Name</th>
                        <th  scope="col">Contact Email</th>
                        <th  scope="col">Contact Number</th>
                        <th  scope="col">Message</th>
                        <th  scope="col">Amount Paid</th>
                        <th  scope="col">Paid Date</th>
						<th  scope="col" width="50px;" ><input type="checkbox" id="show_chkapt"/></th>
                    </tr>
                </thead>
                <?php
                $queries = $wpdb->get_results($sqldata);
                ?>
                <tbody>
                    <?php
                    $as = new AptService();
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
								<th class="textcenter"><input type="checkbox" class="apt_chk" name="check_apt_show[]" value="<?php echo $query->APTID; ?>"/></th>
                            </tr>
                        <?php }
                    } ?>
                    <tr>
                        <th  colspan=7 scope="col"><a href="<?php echo get_permalink() . '?page=aptservice&showtendata'; ?> ">View All Recent Appointments</a></th> 
                        <th   colspan=2 scope="col">
						<a href="<?php echo get_permalink() . '?page=pasttrans'; ?> ">View All Past Appointments</a></th>
						<th><input type='submit' id='chkall_sub' name='chkall_sub'  value='Delete'/></th>
                    </tr>
                </tbody>
            </table>
        </form>
        <div class="apt-pegi">	<strong><?php echo $nr; ?>&nbsp Items</strong>&nbsp &nbsp <?php echo $paginationDisplay; ?> </div>
        <div >
            <table class="wp-list-table widefat fixed pages" style="width:280px; height:20px;" >
                <thead><tr> <th scope="col" style="text-align:center;">Download CSV File</th>		 
                        <th scope="col" style="text-align:center;"> <a id="csvfile" href="<?php echo $import_appointment; ?>" alt="Download CSV File"><img src="<?php echo INK_ADMIN; ?>/images/export.png" alt="Download CSV File" height="32" width="32" /></a>	</th>
                </thead></table> 
        </div>
    </div>
    <?php
    global $wpdb, $apt_transaction;
    if (isset($_GET['payment'])) {
        $aptid = $_GET['payment'];
        ?>
        <style> .showdata{display:none;} </style>
        <h3><a href='<?php echo get_permalink() . "?page=createappoitment&service" ?>'>Create New Service</a>
            <form action="<?php echo $apturl . '&service=service'; ?>" method="post" >
                <table id="apt_data_show" class="wp-list-table widefat fixed pages" >
                    <thead >
                        <tr>
                            <th  scope="col"> <label>Service Taken</label> </th>
                            <th  scope="col">Payer Paypal Email</th>
                            <th  scope="col"> <label>Amount Paid</label></th>
                            <th  scope="col">Transaction Date</th>
                            <th  scope="col">Transaction ID</th>
                            <th  scope="col">Status</th>
                        </tr>
                    </thead>
                    <?php
                    $sqldata = $wpdb->prepare("SELECT * FROM $apt_transaction WHERE TXN_ID='$aptid'");
                    $queries = $wpdb->get_results($sqldata);
                    ?>
                    <tbody>
                        <?php foreach ($queries as $query) { ?>
                            <tr>
                                <th  scope="col"> <label><?php echo $query->apt_txn_service_name; ?></label> </th>
                                <th  scope="col"><?php echo $query->apt_txn_payer_email; ?></th>
                                <th  scope="col"> <label><?php echo $query->apt_txn_price; ?></label></th>
                                <th  scope="col"><?php echo $query->apt_txn_booking_date; ?></th>
                                <th  scope="col"><?php echo $query->apt_txn_txnid; ?></th>
                                <th  scope="col"><?php echo $query->apt_txn_status; ?></th>
                            </tr>
                            <tr><td colspan=6> <a href="<?php echo get_permalink() . "?page=aptservice"; ?>">Back to All Booked Appointments</a></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </form>
            <?php
        }
//Show Recent DATA
        if (isset($_GET['showtendata'])) {
            $aptid = $_GET['payment'];
            $recenttodaydate = date("dmY");
            //pegination
            $recentaptsql = mysql_query("SELECT * FROM $appointment_data WHERE ddmmyyy >= '$recenttodaydate' ");
            $nr = mysql_num_rows($recentaptsql);
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
                    $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&showtendata&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
                } else if ($pn == $lastPage) {
                    $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&showtendata&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
                    $centerPages .= '&nbsp; <span class="selectpeginumber" class="pagNumActive">' . $pn . '</span> &nbsp;';
                } else if ($pn > 2 && $pn < ($lastPage - 1)) {
                    $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&showtendata&pn=' . $sub2 . '">' . $sub2 . '</a> &nbsp;';
                    $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&showtendata&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
                    $centerPages .= '&nbsp; <span class="selectpeginumber" class="pagNumActive">' . $pn . '</span> &nbsp;';
                    $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&showtendata&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
                    $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&showtendata&pn=' . $add2 . '">' . $add2 . '</a> &nbsp;';
                } else if ($pn > 1 && $pn < $lastPage) {
                    $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&showtendata&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
                    $centerPages .= '&nbsp; <span  class="selectpeginumber" class="pagNumActive">' . $pn . '</span> &nbsp;';
                    $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&showtendata&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
                }
                $limit = 'LIMIT ' . ($pn - 1) * $itemsPerPage . ',' . $itemsPerPage;
//$sql2 = mysql_query("SELECT * FROM $appointment_data ORDER BY APTID DESC $limit");
                $recentsqldata = "SELECT * FROM $appointment_data WHERE ddmmyyy >= '$recenttodaydate' ORDER BY APTID DESC $limit";
                $paginationDisplay = "";
                if ($lastPage != "1") {
//$paginationDisplay .= 'Page <strong>' . $pn . '</strong> of ' . $lastPage . '&nbsp;  &nbsp;  &nbsp; ';
                    if ($pn != 1) {
                        $previous = $pn - 1;
                        $paginationDisplay .= '&nbsp;  <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&showtendata&pn=' . $previous . '">«</a> ';
                    }
                    $paginationDisplay .= '<span class="paginationNumbers">' . $centerPages . '</span>';
                    if ($pn != $lastPage) {
                        $nextPage = $pn + 1;
                        $paginationDisplay .= '&nbsp;  <a class="inkpeginumber" href="' . get_permalink() . '?page=aptservice&showtendata&pn=' . $nextPage . '">»</a> ';
                    }
                }
            }
            ?>
            <style> .showdata{display:none;} </style>
            <div class="showtendata">
                <h3>Recent 10 Appointments</h3>
                <form action="#" method="post" >
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
                            </tr>
                        </thead>
                        <?php
                        global $wpdb, $appointment_data;
                        $queries = $wpdb->get_results($recentsqldata);
                        if (!empty($queries)) {
                            ?>
                            <tbody>
                                <?php
                                $ij = 0;
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
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            <tr>
                                <th  colspan=9 scope="col"><a href="<?php echo get_permalink() . '?page=aptservice'; ?> ">Back to All Booked Appointments </a></th> </tr>
                        </tbody>
                    </table>
                </form>
                <div class="apt-pegi">	<strong><?php echo $nr; ?>&nbsp Items</strong>&nbsp &nbsp <?php echo $paginationDisplay; ?> </div> 
            </div>
            <?php
        }
    }
    ?>
