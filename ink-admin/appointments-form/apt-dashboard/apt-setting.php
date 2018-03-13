<?php
function appointment_setting(){ 
global $wpdb, $apt_currency;
$c_queries = $wpdb->get_results("SELECT * FROM $apt_currency");
				if (isset($_POST['submit'])) {
    if(isset($_POST['apt_currency']) && $_POST['apt_currency'] != ''){
        $currency_symbol = $_POST['apt_currency'];
$c_chk = $wpdb->get_row("SELECT * FROM $apt_currency Where apt_c_code='$currency_symbol'",ARRAY_N);
        update_option('apt_currency_code', $currency_symbol);
        update_option('apt_currency_symbol', $c_chk[3]);
    }
	
	if(isset($_POST['merchaint_email']) && $_POST['merchaint_email'] != ''){
	$merchaint_email = $_POST['merchaint_email'];
	//update_option('apt_merchaint_email', $merchaint_email);
	}
	if(isset($_POST['payment_mode']) && $_POST['payment_mode'] != ''){
	$payment_mode = $_POST['payment_mode'];
	$apt_form_head = $_POST['apt_form_head'];
	$apt_fix_date = $_POST['apt_fix_date'];
	$apt_custom_msg = $_POST['apt_custom_msg'];
	update_option('apt_paypal', $payment_mode);
	update_option('apt_form_head', $apt_form_head);
	update_option('apt_fix_date', $apt_fix_date);
	update_option('apt_custom_msg', $apt_custom_msg);
	}
	$apt_cpt = $_POST['cpt_on'];
	update_option('cpt_enable', $apt_cpt);
}
?>
 <div class="wrap" id="of_container">
        <div id="of-popup-save" class="of-save-popup">
            <div class="of-save-save"></div>
        </div>
        <div id="of-popup-reset" class="of-save-popup">
            <div class="of-save-reset"></div>
        </div>
        <div id="header">
            <div class="logo">
                <h2><?php echo APT_ADV_SETTING; ?></h2>
            </div>
            <a href="http://www.inkthemes.com" target="_new">
                <div class="icon-option"> </div>
            </a>
            <div class="clear"></div>
        </div>
        <form enctype="multipart/form-data" id="ofform" name="price_form" method="post">
                      <div id="main">
                <div id="of-nav">
                    <ul>
                        <li> <a  class="pn-view-a" href="#of-option-paypalsetting" title="paypalsetting"><?php echo PAYPAL_SETTING; ?></a></li>
                       
                       
					   <li> <a  class="pn-view-a" href="#of-option-useful-link" title="useful-link"><?php echo "Useful Links"; ?></a></li>
                    </ul>
                </div>
                <div id="content">
                <div class="group" id="of-option-paypalsetting"> 
		
			    <div class="section section-text ">
        <h3 class="heading"><?php echo APT_FIX_HEAD; ?></h3>
        <div class="option">
            <div class="controls">
                <input name="apt_fix_date" type="text" id="apt_fix_date" value="<?php echo get_option('apt_fix_date'); ?>" class="of-input" />
                <br/>
                <span id="pkg_error"></span>
            </div>
            <div class="explain"><?php echo APT_FIX_DES; ?></div>
            <div class="clear"> </div>
        </div>
    </div>
	   <div class="section section-text ">
        <h3 class="heading"><?php echo APT_MSG_HEAD; ?></h3>
        <div class="option">
            <div class="controls">
                <input name="apt_custom_msg" type="text" id="apt_custom_msg" value="<?php echo get_option('apt_custom_msg'); ?>" class="of-input" />
                <br/>
                <span id="pkg_error"></span>
            </div>
            <div class="explain"><?php echo APT_MSG_DES; ?></div>
            <div class="clear"> </div>
        </div>
    </div>
  
    <div class="section section-text ">
        <h3 class="heading"><?php echo PYMT_MODE; ?></h3>
        <div class="option">
            <div class="controls">
                 <select name="payment_mode" id="payment_mode" class="of-input" >
				
				 <option <?php if(get_option('apt_paypal')=='cash') echo 'selected="selected"' ?> value="cash"> <?php echo CASH_OPT; ?></option>
				 </select>
                <br/>
                <span id="pkg_error"></span>
            </div>
            <div class="explain"><?php echo PYPL_PYMT_DES; ?></div>
            <div class="clear"> </div>
        </div>
    </div>                        
    <div class="clear"> </div>
  </div>  
   <div class="group" id="of-option-manage-currency"> 
 <div class="section section-text ">
        <h3 class="heading"><?php echo SET_CURR; ?></h3>
        <div class="option">
            <div class="controls">
                <select name="apt_currency" type="text" id="apt_currency"  class="of-input" >
				<?php 
				$symbol = get_option('apt_currency_code');
				foreach($c_queries as $query){
					
				if(($query->apt_c_code != '') && ($i<=24)) {
				
				?>
				<option <?php if($symbol == $query->apt_c_code) echo 'selected="selected"' ?> value="<?php echo $query->apt_c_code; ?>"><?php echo $query->apt_c_name; ?>&nbsp &nbsp(<?php echo $query->apt_c_code; ?>)</option>
				<?php 
				
				} }?>
				</select>
            </div>
            <div class="explain"><?php echo SET_CURR_DES; ?></div>
            <div class="clear"> </div>
			<h3 class="heading"><?php echo SUP_CURR; ?></h3>
<div class="currencysata">
<table id="currencysata" class="wp-list-table widefat fixed pages" >
<thead >
<tr>
<th  scope="col" style="width:40px;">ID</th>
<th  scope="col" style="width:200px;">Currency </th>
<th  scope="col">Code</th>
<th  scope="col">Symbol</th>
</tr>
</thead>
<tbody>
<?php 
 $i=1;
foreach($c_queries as $query){  
if($i<25) {
?>
<tr>
<th  scope="col" ><?php echo $i++; ?></th>
<th  scope="col"><?php echo $query->apt_c_name; ?></th>
<th  scope="col"><?php echo $query->apt_c_code; ?></th>
<th  scope="col"><?php echo $query->apt_c_symbol; ?></th>
</tr>
<?php }  }?>
</tbody>
</table>
 </div>
        </div>
    </div>
	</div>  
	   <div class="group" id="of-option-other-setting"> 
	 <div class="section section-text ">
        <h3 class="heading"><?php echo _e('Captcha On/Off', 'appointway');  ?></h3>
        <div class="option">
            <div class="controls">
			    <div class="wrap-cpt">
                  <input type="radio" class="of-radio" name="cpt_on" id="apt-radio" value="on" <?php if(get_option('cpt_enable')=='on') echo 'checked'; ?> ><span>On</span></br>
                    <input type="radio" class="of-radio" name="cpt_on" id="apt-radio" value="off" <?php if(get_option('cpt_enable')=='off') echo 'checked'; ?> > <span>Off</span>
					</div>
				</div>
					<div class="explain"><?php echo _e('By default captcha is activated. Turn it off to deactivate this', 'leadcapture'); ?></div>
            <div class="clear"></div>
			</div>
			</div>
				</div>
				
				 <div class="group" id="of-option-useful-link"> 
	 <div class="section section-text ">
        <h3 class="heading"><?php echo "Useful links that help you to improve your website" ;  ?></h3>
        <div class="option">
            <div class="controls">
			    <div class="wrap-cpt" style="width:550px !important;">
                  <p class="notify"><a href="http://www.inkthemes.com/wp-themes/appointway-wordpress-theme/" target="_blank">Appointway WordPress Theme</a> is a very simple to use theme, integrated with complete appointment booking system</p>
	<h4>Useful Plugin </h4>			  
	<ul>	
	<li><a href="" target="_blank">FormGet Contact Form</a></li>
	<li><a href="" target="_blank">Sliding Contact Form By FormGet</a></li>
	<li><a href="" target="_blank">Contact Form Integrated With Google Maps</a></li>
	</ul>
					</div>
				</div>
					
            <div class="clear"></div>
			</div>
			</div>
				</div>
				
				
		  </div>
                <div class="clear"></div>
            </div>
            <div class="save_bar_top">
                <img style="display:none" src="<?php echo ADMINURL; ?>/admin/images/loading-bottom.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working..." />
                <input type="submit" id="submit" name="submit" value="<?php echo SAVE_ALL_CHNG; ?>" class="button-primary" />   
        </form> 
        <form action="<?php echo esc_attr($_SERVER['REQUEST_URI']) ?>" method="post" style="display:inline" id="ofform-reset">
            <span class="submit-footer-reset">
                <input name="reset" type="submit" value="Reset Options" class="button submit-button reset-button" onclick="return confirm('Click OK to reset. Any settings will be lost!');" />
                <input type="hidden" name="of_save" value="reset" />
            </span>
        </form>
    </div>
    </div> 
	
	
	
	<script type="text/javascript">
			jQuery(document).ready(function(){
			
			var flip = 0;
				
			jQuery('#expand_options').click(function(){
				if(flip == 0){
					flip = 1;
					jQuery('#of_container #of-nav').hide();
					jQuery('#of_container #content').width(755);
					jQuery('#of_container .group').add('#of_container .group h2').show();
	
					jQuery(this).text('[-]');
					
				} else {
					flip = 0;
					jQuery('#of_container #of-nav').show();
					jQuery('#of_container #content').width(595);
					jQuery('#of_container .group').add('#of_container .group h2').hide();
					jQuery('#of_container .group:first').show();
					jQuery('#of_container #of-nav li').removeClass('current');
					jQuery('#of_container #of-nav li:first').addClass('current');
					
					jQuery(this).text('[+]');
				
				}
			
			});
			
				jQuery('.group').hide();
				jQuery('.group:first').fadeIn();
				
				jQuery('.group .collapsed').each(function(){
					jQuery(this).find('input:checked').parent().parent().parent().nextAll().each( 
						function(){
           					if (jQuery(this).hasClass('last')) {
           						jQuery(this).removeClass('hidden');
           						return false;
           					}
           					jQuery(this).filter('.hidden').removeClass('hidden');
           				});
           		});
           					
				jQuery('.group .collapsed input:checkbox').click(unhideHidden);
				
				function unhideHidden(){
					if (jQuery(this).attr('checked')) {
						jQuery(this).parent().parent().parent().nextAll().removeClass('hidden');
					}
					else {
						jQuery(this).parent().parent().parent().nextAll().each( 
							function(){
           						if (jQuery(this).filter('.last').length) {
           							jQuery(this).addClass('hidden');
									return false;
           						}
           						jQuery(this).addClass('hidden');
           					});
           					
					}
				}
				
				jQuery('.of-radio-img-img').click(function(){
					jQuery(this).parent().parent().find('.of-radio-img-img').removeClass('of-radio-img-selected');
					jQuery(this).addClass('of-radio-img-selected');
					
				});
				jQuery('.of-radio-img-label').hide();
				jQuery('.of-radio-img-img').show();
				jQuery('.of-radio-img-radio').hide();
				jQuery('#of-nav li:first').addClass('current');
				jQuery('#of-nav li a').click(function(evt){
				
						jQuery('#of-nav li').removeClass('current');
						jQuery(this).parent().addClass('current');
						
						var clicked_group = jQuery(this).attr('href');
		 
						jQuery('.group').hide();
						
							jQuery(clicked_group).fadeIn();
		
						evt.preventDefault();
						
					});
							});
		</script>
	
	<?php
}
?>