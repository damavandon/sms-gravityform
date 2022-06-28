<?php

// don't call the file directly
if (!defined('ABSPATH')) {
    die();
}

if (!function_exists('payamito_gf_load_core')) {

    function payamito_gf_load_core()
    {
        $core = get_option("payamito_core_version");
        if ($core === false) {
            return PAYAMITO_GF_COR_DIR;
        }
        if (!function_exists('is_plugin_active')) {

            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        $core = unserialize($core);
        if (
            file_exists($core['core_path'])
             &&
            is_plugin_active($core['absolute_path'])
        ) {
           PAYAMITO_GF_COR_DIR;
        } else {
            return PAYAMITO_GF_COR_DIR;
        }
        return PAYAMITO_GF_COR_DIR;
    }
}

if (!function_exists("_payamito_gf_no_intalled_gravity_form")) {
    function _payamito_gf_no_intalled_gravity_form()
    {
        $url = "https://abzarwp.com/downloads/gravity-forms-wordpress-plugin/";
        $message =  __('Payamito gravity forms not working because you need to activate the Gravity Forms  ', ' payamito-gravity-form');
?>
        <div class="notice notice-error is-dismissible" style="padding: 2%;border: 2px solid #e39e06;">
            <p style="text-align: center;font-size: 19px;font-weight: 700;"><?php esc_html_e($message); ?></p>
            <p><a target="_blank" href="<?php echo esc_url($url) ?>" class="button-primary"> <?php esc_html_e('Install Gravity Forms  Now', ' payamito-gravity-form'); ?></a></p>
        </div>
    <?php
    }
}
if (!function_exists("_payamito_gf_is_installed")) {
  function _payamito_gf_is_installed()
{
    return defined('GF_MIN_WP_VERSION');
}
}