<?php
function apt_trans() {
    global $wpdb, $apt_transaction;
    //pegination
    $transaptsql = mysql_query("SELECT * FROM $apt_transaction");
    $nr = mysql_num_rows($transaptsql);
    if ($nr >= 1) {
        if (isset($_GET['pn'])) {
            $pn = preg_replace('#[^0-9]#i', '', $_GET['pn']);
        } else {
            $pn = 1;
        }
        $itemsPerPage = 15;
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
            $centerPages .= '&nbsp; <span class="pagNumActive selectpeginumber">' . $pn . '</span> &nbsp;';
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=trans&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
        } else if ($pn == $lastPage) {
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=trans&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <span class="pagNumActive selectpeginumber">' . $pn . '</span> &nbsp;';
        } else if ($pn > 2 && $pn < ($lastPage - 1)) {
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=trans&pn=' . $sub2 . '">' . $sub2 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=trans&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <span class="pagNumActive selectpeginumber">' . $pn . '</span> &nbsp;';
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=trans&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=trans&pn=' . $add2 . '">' . $add2 . '</a> &nbsp;';
        } else if ($pn > 1 && $pn < $lastPage) {
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=trans&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
            $centerPages .= '&nbsp; <span class="pagNumActive selectpeginumber">' . $pn . '</span> &nbsp;';
            $centerPages .= '&nbsp; <a class="inkpeginumber" href="' . get_permalink() . '?page=trans&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
        }
        $limit = 'LIMIT ' . ($pn - 1) * $itemsPerPage . ',' . $itemsPerPage;
//$sql2 = mysql_query("SELECT * FROM $appointment_data ORDER BY APTID DESC $limit");
        $sqldata = $wpdb->prepare("SELECT * FROM $apt_transaction ORDER BY TXNID DESC $limit");
        $paginationDisplay = "";
        if ($lastPage != "1") {
//$paginationDisplay .= 'Page <strong>' . $pn . '</strong> of ' . $lastPage . '&nbsp;  &nbsp;  &nbsp; ';
            if ($pn != 1) {
                $previous = $pn - 1;
                $paginationDisplay .= '&nbsp;  <a class="inkpeginumber" href="' . get_permalink() . '?page=trans&pn=' . $previous . '">«</a> ';
            }
            $paginationDisplay .= '<span class="paginationNumbers">' . $centerPages . '</span>';
            if ($pn != $lastPage) {
                $nextPage = $pn + 1;
                $paginationDisplay .= '&nbsp;  <a class="inkpeginumber" href="' . get_permalink() . '?page=trans&pn=' . $nextPage . '">»</a> ';
            }
        }
    }
    ?>
    <div class="showdata">
        <h3>Payment Transaction Details</h3>
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
                $queries = $wpdb->get_results($sqldata);
                ?>
                <tbody>
                    <?php
                    if (!empty($queries)) {
                        foreach ($queries as $query) {
                            ?>
                            <tr>
                                <th  scope="col"> <label><?php echo $query->apt_txn_service_name; ?></label> </th>
                                <th  scope="col"><?php echo $query->apt_txn_payer_email; ?></th>
                                <th  scope="col"> <label><?php echo $query->apt_txn_price; ?></label></th>
                                <th  scope="col"><?php echo $query->apt_txn_booking_date; ?></th>
                                <th  scope="col"><?php echo $query->apt_txn_txnid; ?></th>
                                <th  scope="col"><?php echo $query->apt_txn_status; ?></th>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <th  colspan='6' scope="col">Not Found Data</th> </tr>
    <?php } ?>
                </tbody>
            </table>
        </form>
        <div class="apt-pegi">	<strong><?php echo $nr; ?>&nbsp Items</strong>&nbsp &nbsp <?php echo $paginationDisplay; ?> </div> 
    </div>
    <?php
}