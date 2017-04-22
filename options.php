<?php
/**
 * Created by PhpStorm.
 * User: Bionic
 * Date: 22.04.2017
 * Time: 22:15
 */

add_action('admin_menu', 'add_plugin_page');
function add_plugin_page(){
    add_options_page( 'LiqPay Settings', 'LiqPay', 'manage_options', 'lpd_config', 'lpd_options_page' );
}

function lpd_options_page(){
    ?>
    <div class="wrap">
        <h2><?php echo get_admin_page_title() ?></h2>
        <form action="options.php" method="POST">
            <?php
            settings_fields('option_group');
            do_settings_sections('lpd_config_main');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', 'plugin_settings');
function plugin_settings(){
    // params: $option_group, $option_name, $sanitize_callback
    register_setting('option_group', 'option_name', 'sanitize_callback');
    register_setting('option_group', 'lpd_public_key', 'sanitize_callback');
    register_setting('option_group', 'lpd_private_key', 'sanitize_callback');
    register_setting('option_group', 'lpd_sandbox', 'sanitize_callback');

    // params: $id, $title, $callback, $page
    add_settings_section('section_id', 'Main Settings', '', 'lpd_config_main');

    // params: $id, $title, $callback, $page, $section, $args
    add_settings_field('lpd_public_key',  'Public Key',   'get_lpd_public_key',     'lpd_config_main', 'section_id');
    add_settings_field('lpd_private_key', 'Private Key',  'get_lpd_private_key',    'lpd_config_main', 'section_id');
    add_settings_field('lpd_amount',      'Amount',       'get_lpd_amount',         'lpd_config_main', 'section_id');
    add_settings_field('lpd_exp_time', 'Expiry Period (mins)', 'get_lpd_exp_time',  'lpd_config_main', 'section_id');
    add_settings_field('lpd_file_link', 'Link to downloading file', 'get_lpd_file_link', 'lpd_config_main', 'section_id');
    add_settings_field('lpd_sandbox', 'Sandbox (fake purchase)', 'get_lpd_sandbox', 'lpd_config_main', 'section_id');
}

function get_lpd_public_key(){
    $val = get_option('lpd_public_key');
    $val = $val['input'];
    ?>
    <input type="text" name="lpd_public_key[input]" value="<?php echo esc_attr( $val ) ?>" />
    <?php
}
function get_lpd_private_key(){
    $val = get_option('lpd_private_key');
    $val = $val['input'];
    ?>
    <input type="password" name="lpd_private_key[input]" value="<?php echo esc_attr( $val ) ?>" />
    <?php
}
function get_lpd_amount(){
    $val = get_option('lpd_amount');
    $val = $val['input'];
    ?>
    <input type="text" name="lpd_amount[input]" value="<?php echo esc_attr( $val ) ?>" />
    <?php
}
function get_lpd_exp_time(){
    $val = get_option('lpd_exp_time');
    $val = $val['input'];
    ?>
    <input type="text" name="lpd_exp_time[input]" value="<?php echo esc_attr( $val ) ?>" />
    <?php
}
function get_lpd_sandbox(){
    $val = get_option('lpd_sandbox');
    $val = $val['checkbox'];
    ?>
    <label><input type="checkbox" name="lpd_sandbox[checkbox]" value="1" <?php checked( 1, $val ) ?> /> yes</label>
    <?php
}
function get_lpd_file_link(){
    $val = get_option('lpd_file_link');
    $val = $val['input'];
    ?>
    <input type="text" name="lpd_file_link[input]" value="<?php echo esc_attr( $val ) ?>" />
    <?php
}

## Clearing data
function sanitize_callback( $options ){
    // clear
    foreach( $options as $name => & $val ){
        if( $name == 'input' )
            $val = strip_tags( $val );

        if( $name == 'checkbox' )
            $val = intval( $val );
    }
    //die(print_r( $options )); // Array ( [input] => aaaa [checkbox] => 1 )
    return $options;
}