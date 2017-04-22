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

require(BUYLP_DIR . 'LiqPay.php');

register_activation_hook(__FILE__, 'mainInit');
function mainInit() {
    global $wpdb;
    $wpdb->get_var("CREATE TABLE IF NOT EXISTS lp_dload(
    token TEXT NOT NULL,
    expiry INT,
    pay_status TEXT,
    user_sig TEXT,
    lp_sig TEXT,
    active INT DEFAULT 0,
    buy_date TEXT NULL
    )");

    $wpdb->get_var("CREATE TABLE IF NOT EXISTS lp_conf(
    file_link TEXT,
    expiry_period INT,
    )");
}

function readPostData()
{
    if ($_POST) {
        global $wpdb;
        $user_count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->users");
        echo "<script>alert('User count is {$user_count}')</script>";
        $private_key = "8zmMxw0qJLPHCRPc2c1lkYU4OalUEASGS4i4DaJU";
        $file = '/var/www/liqpay/wp-content/plugins/wpliqpay/purchase_data.txt';
        $sign = base64_encode( sha1(
            $private_key .
            $_POST["data"] .
            $private_key
            , 1 ));
        if ($sign == $_POST["signature"]){
            $decoded_data = base64_decode($_POST["data"]);
            echo "<script>alert('Signature true. Decoded data: {$decoded_data}')</script>";
            $current = file_get_contents($file);
            $current .= $decoded_data . "\n\n";
            file_put_contents($file, $current);
        }else {
            echo "<script>alert('Signature fail.')</script>";
        }
    }
}
add_action('init', 'readPostData');

function liqpay_notice()
{
    global $wpdb;
    $user_count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->users");
    $public_key = "i83816479078";
    $private_key = "8zmMxw0qJLPHCRPc2c1lkYU4OalUEASGS4i4DaJU";
    isset($_COOKIE["download_token"]) ? $token = $_COOKIE["download_token"] : $token = generateLink();
    $liqpay = new LiqPay($public_key, $private_key);
    $lp_data = array(
        'action' => 'pay',
        'amount' => '1',
        'currency' => 'USD',
        'description' => 'description text',
        //'order_id'       => '0000000001',
        'version' => '3',
        'sandbox' => '1',
        'server_url' => 'https://itstest.ml/wp-content/plugins/wpliqpay/post_echo.php',
        'result_url' => 'https://itstest.ml',
        'verifycode' => 'Y',
        'info' => '22814881997'
    );
    $html = $liqpay->cnb_form($lp_data);
    $calc_sign = base64_encode(sha1($private_key.$lp_data.$private_key, 1));
    $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->users");
    print("<div id='liqpay-test' style='float: right;'>
               <br>Plugin url: " . BUYLP_URL . "<br>Plugin dir: " . BUYLP_DIR . "<br>
               User count: {$user_count}<br>
               {$html}<br>
               Test token: {$token}
           </div>");
}
add_action('wp_print_scripts', 'liqpay_notice');
//add_action('init', 'liqpay_notice');
// test

function generateLink(){
    global $wpdb;
    $chars = 'qwertyuiopasdfghjklzxcvbnm1234567890QAZWSXEDCRFVTGBYHNUJMIKOLP';
    $numChars = strlen($chars);
    $string = '';
    for ($i = 0; $i < 30; $i++) {
        $string .= substr($chars, rand(1, $numChars) - 1, 1);
    }
    $wpdb->get_var("INSERT INTO lp_dload (token) VALUE ('{$string}')");
    setcookie("download_token", $string,time()+3600);
    return $string;
}
