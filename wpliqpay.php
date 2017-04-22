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
    pay_status TEXT,
    user_sig TEXT,
    buy_date INT NULL
    )");

    $wpdb->get_var("CREATE TABLE IF NOT EXISTS lp_conf(
    file_link TEXT,
    public_key TEXT,
    private_key TEXT,
    amount INT,
    currency TEXT,
    description TEXT,
    expiry_period INT,
    )");
}

function readPostData()
{
    if ($_POST) {
        global $wpdb;
        // SELECT sub_id FROM bot_rol WHERE id=\'{int(user_id)}\'
        isset($_COOKIE["download_token"]) ? $token = $_COOKIE["download_token"] : $token = null;
        $private_key = "8zmMxw0qJLPHCRPc2c1lkYU4OalUEASGS4i4DaJU";
        $file = '/var/www/liqpay/wp-content/plugins/wpliqpay/purchase_data.txt';
        if ($token){
            $sign = base64_encode(sha1($private_key . $_POST["data"] . $private_key, 1));
            // SELECT sub_id FROM bot_rol WHERE id='{user_id}'
            $buy_date = $wpdb->get_var("SELECT buy_date FROM lp_dload WHERE token='$token'");
            if ($sign == $_POST["signature"]){
                //SELECT UNIX_TIMESTAMP()
                if (!$buy_date){
                $decoded_data = json_decode(base64_decode($_POST["data"]));
                $stat = $decoded_data->{"status"};
                $wpdb->get_var("UPDATE lp_dload SET buy_date=UNIX_TIMESTAMP(), pay_status='$stat' WHERE token='$token'");
//                echo "<script>alert('Signature verified')</script>";
                $current = file_get_contents($file);
                $current .= base64_decode($_POST["data"]) . "\n\n";
                file_put_contents($file, $current);
                }else{
                    echo "<script>alert('Buy date already exist')</script>";
                }
            }else {
                echo "<script>alert('Signature not verified!')</script>";
            }
        }else{
            echo "<script>alert('Token not found')</script>";
        }
    }

    if ($_GET){
        if ($_GET['download'] == '1'){
            global $wpdb;
//            echo "<script>alert('Token: {$_GET['token']}')</script>";
            $buy_date = $wpdb->get_var("SELECT buy_date FROM lp_dload WHERE token='{$_GET['token']}'");
            if ($buy_date){
                if (($buy_date + 3600) > date_timestamp_get(date_create())){
                    echo "<script>alert('Downloading will start now')</script>";
                    $file = ("file.pdf");
                    header ("Content-Type: application/octet-stream");
                    header ("Accept-Ranges: bytes");
                    header ("Content-Length: ".filesize($file));
                    header ("Content-Disposition: attachment; filename=".$file);
                    readfile($file);
                }else {
                    echo "<script>alert('Link lifetime ended')</script>";
                }
            }else {
                echo "<script>alert('Date of buy not found or broken token')</script>";
            }
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
        'result_url' => 'https://itstest.ml/?download=1&token=' . $token,
        'verifycode' => 'Y',
        //'info' => 'https://itstest.ml/?download=1&token=' . $token
    );
    $html = $liqpay->cnb_form($lp_data);
    $calc_sig = base64_encode(sha1($private_key.$lp_data.$private_key, 1));
    $wpdb->get_var("UPDATE lp_dload SET user_sig='{$calc_sig}' WHERE token='$token'");
    print("<div id='liqpay-test' style='float: right;'>You token: {$token}<br>{$html}<br></div>");
}
add_action('wp_print_scripts', 'liqpay_notice');

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
