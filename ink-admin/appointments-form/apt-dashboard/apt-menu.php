<?php
add_action('admin_menu', 'ink_apt_styles');
add_action('admin_init', 'ink_apt_js');
// Add scripts and stylesheet
function ink_apt_styles() {
    wp_register_style('apt', CSS_PLUGIN . 'apt-dashboard-style.css');
    wp_enqueue_style('apt');
    wp_enqueue_style('apt-admin-css', INK_ADMIN . "/js/cal/jquery.calendars.picker.css", '', '', 'all');
    wp_enqueue_style('apt-tip-skyblue', ABS_URL_PATH . "tip-twitter/tip-twitter.css", '', '', 'all');
}
function ink_apt_js() {
    wp_enqueue_script('jquery-calender-admin', INK_ADMIN . '/js/cal/jquery.calendars.js', array('jquery'));
    wp_enqueue_script('jquery-ui-plus-admin', INK_ADMIN . '/js/cal/jquery.calendars.plus.js', array('jquery'));
    wp_enqueue_script('jquery-ui-cal-admin', INK_ADMIN . '/js/cal/jquery.calendars.picker.js', array('jquery'));
    wp_enqueue_script('jquery-poshytip-admin', ABS_URL_PATH . 'jquery.poshytip.js', array('jquery'));
}
// Add admin menu page to the menu
add_action('admin_menu', 'appointment_services');
function appointment_services() {
    if (function_exists('add_menu_page'))
        add_menu_page('Appointments', 'Appointments', 10, 'aptservice', 'showdata', INK_ADMIN . '/images/menuicon.png', 58);
    add_submenu_page('aptservice', 'Create Appointments', 'Create Appointments', 10, 'createappoitment', 'create_appointment');
    add_submenu_page('aptservice', 'Booked Appointments', 'Booked Appointments', 10, 'aptservice', 'showdata');
   
    add_submenu_page('aptservice', 'Settings', 'Settings', 10, 'paymentsettings', 'appointment_setting');
    add_submenu_page('aptservice', '', '', 10, 'pasttrans', 'past_appointment_detail');
    remove_submenu_page('aptservice', 'aptservice');
    /* add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position ); 
      add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
      $page_title=write on page title
      $menu_title= Menu name on display Dashboard
      $capability= user Level Capability in access point top capability is 10 and low is 0.
      $menu_slug=The slug name to refer to this menu by (should be unique for this menu).
      $function= new function creat on dashboard.
      $icon_url=icon url.
      $position=position menu on dashboard.
      2 Dashboard,4 Separator, 5 Posts, 10 Media, 15 Links, 20 Pages, 25 Comments, 59 Separator, 60 Appearance, 65 Plugins, 70 Users,
      75 Tools, 80 Settings, 99 Separator
     */
}
// Create the page
?>