<?php
/**
 * Created by PhpStorm.
 * User: Bionic
 * Date: 22.04.2017
 * Time: 10:51
 */

// POST!!! https://itstest.ml/wp-content/plugins/wpliqpay/post_echo.php
$private_key = "8zmMxw0qJLPHCRPc2c1lkYU4OalUEASGS4i4DaJU";
if ($_POST) {
    $file = '/var/www/liqpay/wp-content/plugins/wpliqpay/purchase_data.txt';
    $sign = base64_encode( sha1(
        $private_key .
        $_POST["data"] .
        $private_key
        , 1 ));
    if ($sign == $_POST["signature"]){
        $decoded_data = base64_decode($_POST["data"]);
        echo "Signature true. Decoded data\n" . $decoded_data;
        $current = file_get_contents($file);
        $current .= $decoded_data . "\n\n";
        file_put_contents($file, $current);
    }else {
        echo "Signature fail.";
    }

}else{
    echo "It's not POST query";
}
