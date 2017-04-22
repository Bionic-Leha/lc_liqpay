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

function liqpay_notice()
{
    print("<div id='liqpay-test' style='float: right;'>
            <br>Plugin url: " . BUYLP_URL . "<br>Plugin dir: " . BUYLP_DIR . "<br>
            <a onclick=\"window.open('https://www.liqpay.com/ru/checkout/card/liqpaybuy','','Toolbar=1,Location=0,Directories=0,Status=0,Menubar=0,Scrollbars=0,Resizable=0,Width=1280,Height=720');\"><button>Buy via LiqPay</button></a>
            <!--<iframe id='liqpay-frame' src='https://www.liqpay.com/ru/checkout/card/liqpaybuy' style='background-color: transparent;' scrolling='no' frameborder='0'></iframe> -->
            <form method=\"POST\" accept-charset=\"utf-8\" action=\"https://www.liqpay.com/api/3/checkout\">
	            <input type=\"hidden\" name=\"data\" value=\"eyJ2ZXJzaW9uIjozLCJhY3Rpb24iOiJwYXkiLCJwdWJsaWNfa2V5IjoiaTgzODE2NDc5MDc4IiwiYW1vdW50IjoiNSIsImN1cnJlbmN5IjoiVUFIIiwiZGVzY3JpcHRpb24iOiLQnNC+0Lkg0YLQvtCy0LDRgCIsInR5cGUiOiJidXkiLCJsYW5ndWFnZSI6InJ1In0=\" />
	            <input type=\"hidden\" name=\"signature\" value=\"c9eV0+SUdftjHQRWgbPPCFA7o3M=\" />
	            <button style=\"border: none !important; display:inline-block !important;text-align: center !important;padding: 7px 20px !important;
	            	color: #fff !important; font-size:16px !important; font-weight: 600 !important; font-family:OpenSans, sans-serif; cursor: pointer !important; border-radius: 2px !important;
	            	background: rgb(122,183,43) !important;\"onmouseover=\"this.style.opacity='0.5';\" onmouseout=\"this.style.opacity='1';\">
	            	<img src=\"https://static.liqpay.com/buttons/logo-small.png\" name=\"btn_text\"
	            		style=\"margin-right: 7px !important; vertical-align: middle !important;\"/>
	            	<span style=\"vertical-align:middle; !important\">Оплатить 5 UAH</span>
	            </button>
            </form>
           </div>");
}

add_action('wp_print_scripts', 'liqpay_notice');
//add_action('init', 'liqpay_notice');

// test