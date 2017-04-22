<?php

/*
Plugin Name: Wpliqpay
Plugin URI: localhost
Description: LiqPay Purchase with unique download link
Version: 0.1
Author: Bionic
Author URI: https://farbio.xyz
License: GNU GPL 3.0
*/

define('BUYLP_DIR', plugin_dir_path(__FILE__));
define('BUYLP_URL', plugin_dir_url(__FILE__));

require(BUYLP_DIR . 'LiqPay.php');

if ( is_admin() ){
    require(BUYLP_DIR . 'options.php');
}

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
    amount INT,
    currency TEXT,
    description TEXT,
    expiry_period INT,
    )");
}

$dload_button = '';
$token = '';

function readPostData()
{
    global $dload_button, $token;
    isset($_COOKIE["download_token"]) ? $token = $_COOKIE["download_token"] : $token = generateLink();
    if ($_POST) {
        global $wpdb;
        $private_key = get_option('lpd_private_key')['input'];
        $file = '/var/www/liqpay/wp-content/plugins/wpliqpay/purchase_data.txt';
        if ($token){
            $sign = base64_encode(sha1($private_key . $_POST["data"] . $private_key, 1));
            $buy_date = $wpdb->get_var("SELECT buy_date FROM lp_dload WHERE token='$token'");
            if ($sign == $_POST["signature"]){
                if (!$buy_date){
                    $decoded_data = json_decode(base64_decode($_POST["data"]));
                    $stat = $decoded_data->{"status"};
                    $wpdb->get_var("UPDATE lp_dload SET buy_date=UNIX_TIMESTAMP(), pay_status='$stat' WHERE token='$token'");
                    $current = file_get_contents($file);
                    $current .= base64_decode($_POST["data"]) . "\n\n";
                    file_put_contents($file, $current);
                }else{
                    echo "<script>alert('Buy date already exist')</script>";
                }
            }else {
                echo "<script>alert('Signature not verified!')</script>";
            }
        }
    }
    if ($_GET){
        global $wpdb;
        if ($_GET['download'] == '1'){
            $buy_date = $wpdb->get_var("SELECT buy_date FROM lp_dload WHERE token='{$_GET['token']}'");
            if ($buy_date){
                if (($buy_date + 3600) > date_timestamp_get(date_create())){
                    $dload_button = "<div class='page-promo__text-buttons'>
                        <a href='https://itstest.ml/download-page/?download=2&token={$token}' class='btn btn-lg btn-lightblue page-promo__text-button'>Скачать книгу</a>
                    </div>";
                }else {
                    $dload_button = "Link lifetime ended";
                }
            }else {
                $dload_button = "Date of buy not found or broken token";
            }
        }
        if ($_GET['download'] == '2'){
            $buy_date = $wpdb->get_var("SELECT buy_date FROM lp_dload WHERE token='{$_GET['token']}'");
            if ($buy_date){
                if (($buy_date + 3600) > date_timestamp_get(date_create())){
                    $file = ("file.pdf");
                    header ("Content-Type: application/octet-stream");
                    header ("Accept-Ranges: bytes");
                    header ("Content-Length: ".filesize($file));
                    header ("Content-Disposition: attachment; filename=".$file);
                    readfile($file);
                }else {
                    $dload_button = "Link lifetime ended";
                }
            }else {
                $dload_button = "Date of buy not found or broken token";
            }
        }
    }
}
add_action('init', 'readPostData');


function liqpay_notice()
{
    global $wpdb, $token;
    $public_key = get_option('lpd_public_key')['input'];
    $private_key = get_option('lpd_private_key')['input'];
    $liqpay = new LiqPay($public_key, $private_key);
    get_option('lpd_sandbox')['checkbox'] == '1' ? $sand_box = '1' : $sand_box = '0';
    $lp_data = array(
        'action' => 'pay',
        'amount' => '1',
        'currency' => 'USD',
        'description' => 'description text',
        //'order_id'       => '0000000001',
        'version' => '3',
        'sandbox' => $sand_box,
        'server_url' => 'https://itstest.ml/wp-content/plugins/wpliqpay/post_echo.php',
        'result_url' => 'https://itstest.ml/download-page/?download=1&token=' . $token,
        'verifycode' => 'Y',
        //'info' => 'https://itstest.ml/?download=1&token=' . $token
    );
    $html = $liqpay->cnb_form($lp_data);
    $calc_sig = base64_encode(sha1($private_key.$lp_data.$private_key, 1));
    $wpdb->get_var("UPDATE lp_dload SET user_sig='{$calc_sig}' WHERE token='$token'");
    echo "<div id='liqpay-test' style='float: right;'>";
    if ($sand_box == '1'){
        print("Testing mode");
    }
    echo "{$html}";
    echo "</div>";
}
add_shortcode('liqpay_buy_button', 'liqpay_notice');


function downloadButton(){
    global $dload_button;
    echo $dload_button;
}
add_shortcode('liqpay_download_button', 'downloadButton');


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
