<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://payamito.com/
 * @since             1.1.0
 * @package           Payamito
 * Plugin Name:       Payamito:Gravity Forms
 * Description:       Payamito:Gravity Forms Version
 * Version:           1.2.2
 * Core Version       2.0.0
 * Author:            payamito
 * Author URI:        https://payamito.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       payamito-gravity-form
 * Domain Path:       /languages
 */

// don't call the file directly
if (!defined('ABSPATH')) {

    die('direct access abort ');
}
if (!defined('PAYAMITO_GF_PLUGIN_FILE')) {

    define('PAYAMITO_GF_PLUGIN_FILE', __FILE__);
}


require_once __DIR__ . '/Define-constants.php';
require_once __DIR__ . '/inc/Autoloader.php';

register_activation_hook(__FILE__, 'payamito_gf_activate');
register_deactivation_hook(__FILE__, 'payamito_gf_deactivate');



if (!function_exists("payamito_gf_set_locale")) {
    function payamito_gf_set_locale()
    {
        	
		$dirname = str_replace('//', '/', wp_normalize_path(dirname(__FILE__))) ;
		$mo = $dirname . '/languages/' . 'payamito-gravity-form-' . get_locale() . '.mo';
		load_textdomain('payamito-gravity-form', $mo);
    }
}
payamito_gf_set_locale();

function payamito_gf_activate()
{
    do_action("payamito_gf_activate");
    require_once PAYAMITO_GF_DIR . '/inc/functions.php';
    require_once PAYAMITO_GF_DIR . '/inc/class-install.php';
    Payamito\GravityForm\Install::install(PAYAMITO_GF_CORE_VER,PAYAMITO_GF_PLUGIN_FILE,PAYAMITO_GF_COR_DIR);
    require_once payamito_gf_load_core(). '/includes/class-payamito-activator.php';
    Payamito_Activator::activate();
}
function payamito_gf_deactivate()
{
    do_action("payamito_gf_deactivate");
    require_once payamito_gf_load_core(). '/includes/class-payamito-deactivator.php';
    Payamito_Deactivator::deactivate();
}

if (!class_exists('PayamitoGravityForm')) {

    include_once PAYAMITO_GF_DIR . '/inc/payamito-gf.php';
}

/**
 * @return object|PayamitoGravityForm|null
 */
function payamito_gf()
{
    return PayamitoGravityForm::get_instance();
}

payamito_gf();
