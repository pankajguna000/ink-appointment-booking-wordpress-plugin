<?php
/*
  Plugin Name: Ink Appointment Booking
  Plugin URI: https://www.inkthemes.com/
  Description: With Ink Appointment Plugin you can book the appointment of the clients directly through your Website.Moreover,  the clients will be notified instantly regarding the appointment details.
  Version: 1.3
  Author: InkThemes
  Author URI: https://www.inkthemes.com
 */
$basepatrh = plugin_dir_path(__FILE__) . '/ink-admin/appointments-form/database/';
define(CSV_PATH, $basepatrh);
require(plugin_basename('/ink-admin/appointments-form/database/apt-database.php'));
include(plugin_dir_path(__FILE__) . 'ink-admin/apt-tiny/apt-tiny.php');
add_action('init', 'leads_capture_create_table');
add_action('wp_enqueue_scripts', 'info_head');
add_action('admin_menu', 'info_admin');
//add css and js frontend
function info_head() {
    if (!is_admin()) {
        wp_enqueue_style('cal-css-ui', plugins_url('ink-admin/js/cal-front/jquery-ui.css', __FILE__));
        wp_enqueue_style('ink-form-css', plugins_url('ink-admin/css/ink-form.css', __FILE__));
       wp_enqueue_script('jquery-ui-datepicker'); 
        wp_enqueue_script('ink-required', plugins_url('ink-admin/js/ink-required.js', __FILE__), array('jquery'));
		add_action('wp_print_scripts', 'apt_ajax_load_scripts');
    }
}
//add css and js admin	
function info_admin() {
    if (is_admin()) {
       
    }
}

function leads_capture_create_table() {
    define('APTINK_CLASS', plugin_dir_path(__FILE__) . 'ink-admin/');
    require_once (plugin_dir_path(__FILE__) . 'control.php');
    $pluginpath = plugins_url('/ink-admin/css/', __FILE__);
    $inkadmin = plugins_url('/ink-admin', __FILE__);
    define('CSS_PLUGIN', $pluginpath);
    define('INK_ADMIN', $inkadmin);
}
function apt_ajax_load_scripts() {
    // load our jquery file that sends the $.post request
	wp_enqueue_script('jquery-apt-ajax', plugins_url('ink-admin/js/ink-apt-ajax.js', __FILE__), array('jquery'));
    // make the ajaxurl var available to the above script
    wp_localize_script('jquery-apt-ajax', 'script_call', array('ajaxurl' => admin_url('admin-ajax.php')));
}

class Ink_Appointment_Widget extends WP_Widget {
    function __construct() {
        $params = array(
            'name' => 'Ink Appointment Widget',
            'description' => 'Just drag and drop the widget to Ink Appointmen in the page'
        );
        parent::__construct('Ink_Appointment_Widget', '', $params);
    }
    public function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', '');
        $number = strip_tags($instance['number']);
        echo $before_widget;
        if ($title)
            echo $before_title . $title . $after_title;
        ink_appoitment();
        //echo $after_widget;
    }
}
add_action('widgets_init', create_function('', 'return register_widget("Ink_Appointment_Widget");'));
?>