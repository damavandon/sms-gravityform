<?php



// don't call the file directly
if (!defined('ABSPATH')) {
    die();
}
if (!class_exists('Functions')) :

    class PGF_Functions
    {
        public static function get_forms()
        {
            $forms = GFAPI::get_forms();
            if (count($forms) == 0) {
                return [];
            }
            return $forms;
        }
        public static function form_type(){

            $types=['register','login'];
            return $types;
        }
        public static function get_tags()
        {
            $tags = [
                'payment_method' => __('Payment Method', 'payamito-gravity-form'),
                'payment_status' => __('Payment Status', 'payamito-gravity-form'),
                'transaction_id' => __('Transaction Id', 'payamito-gravity-form'),
                'ip' => __('IP', 'payamito-gravity-form'),
                'date_mdy' => __('Date (dd/mm/yyyy)', 'payamito-gravity-form'),
                'post_id' => __('Embed Post/Page Id', 'payamito-gravity-form'),
                'post_title' => __('Embed Post/Page Title', 'payamito-gravity-form'),
                'embed_url' => __('Embed URL', 'payamito-gravity-form'),
                'entry_url' => __('Entry URL', 'payamito-gravity-form'),
                'form_id' => __('Form Id', 'payamito-gravity-form'),
                'form_title' => __('Form Title', 'payamito-gravity-form'),
                'display_name' => __('User Display Name', 'payamito-gravity-form'),
                'user_email' => __('User Email', 'payamito-gravity-form'),
                'user_login' => __('User Login', 'payamito-gravity-form'),
               
            ];
            return $tags;
        }
        public static function condition($entry, $form, $config, $who = '')
        {

            if (count($config['conditional']) == 0) {

                return true;
            }
            $conditions = $config['conditional'];
            $type = $config['conditional_type'];

            foreach ($conditions as $i => $con) {
                $field_id = $con['field'];
                if (empty($field_id)) {
                    continue;
                }

                $field = RGFormsModel::get_field($form, $field_id);
                if (empty($field)) {
                    continue;
                }

                $value    = $con['value'];
                $operator = $con['operator'];

                $is_visible     = !RGFormsModel::is_field_hidden($form, $field, array());
                $field_value    = GFFormsModel::get_lead_field_value($entry, $field);
                $is_value_match = RGFormsModel::is_value_match($field_value, $value, $operator);

                $check = $is_value_match && $is_visible;

                if ($type == 'any' && $check) {
                    return true;
                } else if ($type == 'all' && !$check) {
                    return false;
                }
            }

            if ($type == 'any') {
                return false;
            } else {
                return true;
            }
        }

        public static function get_mobile($field)
        {
            $field  = (array) $field;
            $mobile = rgar($field, "payamito_gf_verify_mobile");
            $mobile = str_replace('.', '_', $mobile);
            $mobile = "input_{$mobile}";
            $mobile = !rgempty($mobile) ? sanitize_text_field(rgpost($mobile)) : '';
            return $mobile;
        }

        public static function resent_time_check($mobile,$time=0)
        {
            
            if (!isset($_SESSION[$mobile.'T'])) {
                return true;
            }
            $period_send = (int)$time;
            $time_send = (int)$_SESSION[$mobile . "T"];
            $R = time() - $time_send;
            if ($R < $period_send) {
                return ($period_send- $R) ;
            }
            return true;
        }

        
    }
endif;
