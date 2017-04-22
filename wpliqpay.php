<?php

/*
Plugin Name: Wpliqpay
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: LiqPay purchase and unique download link
Version: 0.1
Author: Bionic
Author URI: https://farbio.xyz
License: GNU GPL 3.0
*/

define('BUYLP_DIR', plugin_dir_path(__FILE__));
define('BUYLP_URL', plugin_dir_url(__FILE__));

/*
function msp_helloworld_load(){

    if(is_admin()) // подключаем файлы администратора, только если он авторизован
        require_once(MSP_HELLOWORLD_DIR.'includes/admin.php');

    require_once(MSP_HELLOWORLD_DIR.'includes/core.php');
}
msp_helloworld_load();
*/

/*
register_activation_hook(__FILE__, 'msp_helloworld_activation');
register_deactivation_hook(__FILE__, 'msp_helloworld_deactivation');

function msp_helloworld_activation() {

    // действие при активации
}

function msp_helloworld_deactivation() {
    // при деактивации
}
*/

function liqpay_notice(){
    echo "<div id='liqpay-test'>Test Message</div>";
}
add_action('wp_header', 'liqpay_notice');