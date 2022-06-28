<?php

namespace Payamito\GravityForm;

use PGF_Functions;
use PGF_Send;
use RGFormsModel;
use GFCommon;
use Payamito_OTP;

if (!class_exists('Submit')) {

    class Submit
    {
        public $is_verification = false;
        /**
         * The single instance of the class.
         * @var Submit
         * @since 1.0.0
         */
        public static $_instance = null;

        public $form;

        public $fields;

        public $entry;

        public $send;

        public $OTP_count;

        private $OTP = null;

        public static function get_instance()
        {
            if (is_null(self::$_instance)) {

                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function init_hooks()
        {
            add_filter('gform_after_submission', array($this, 'after_submit'), -1, 4);
            add_action('gform_post_payment_status', array($this, 'after_payment'), -1, 4);
        }

        private function clone_SESSION($entry, $form)
        {
            global $payamito_gf_options;
            $option = $payamito_gf_options[$form['id']];
            if ($option['verification_active'] === false) {
                return;
            }
            $id = $option['verification_field'];
            $mobile = $entry[$id];
            $zaro = $mobile[0];
            if ($zaro == "0") {
                $mobile = substr_replace($mobile, "", 0, 1);
            }
            if (isset($_SESSION[$mobile . '_validated'])) {
                unset($_SESSION[$mobile . '_validated']);
            }
            return;
        }
        public  function after_submit($entry, $form)
        {
            $this->clone_SESSION($entry, $form);

            $init = $this->init_sms($form, $entry);
            if ($init === false) {
                return;
            }
            $this->human_sms($form, $entry);
        }
        public  function init_sms($form, $entry)
        {
            global $payamito_gf_options;

            if (!isset($payamito_gf_options[$form['id']])) {
                return false;
            }
            $this->options = $payamito_gf_options[$form['id']];

            if ($this->options['active'] === false) {
                return false;
            }
            $this->send = PGF_Send::get_instance();
            $this->form = $form;
            $this->fields = $form['fields'];
            $this->entry = $entry;

            return true;
        }
        public  function human_sms($form, $entry)
        {
            $this->admin_sms($this->options['admin']);
            $this->user_sms($this->options['user']);
        }

        public  function after_payment($config, $entry, $status, $transaction_id)
        {
            if (empty($entry['payment_method'])) {
                return;
            }
            $form = $form = RGFormsModel::get_form_meta($entry['form_id']);
            $init = $this->init_sms($form, $entry);

            if ($init === false) {
                return;
            }
            $this->payment_sms($this->options[strtolower($status)], strtolower($status));
        }
        public function admin_sms($options, $condition_check = true, $message = null)
        {

            if ($options['active'] === false) {
                return;
            }
            $phones = $this->check_admin_phone_number($this->options['admin_phone_number']);
            if ($phones === false) {
                return;
            }
            if ($condition_check === true) {

                if ($options['conditional_active'] === true) {

                    $condition = PGF_Functions::condition($this->entry, $this->form, $options, 'admin');
                } else {
                    $condition = true;
                }
                if ($condition === false) {
                    return;
                }
            }
            if (is_null($message)) {
                $message = $this->is_ready_send($options);
            }
            if ($message == false) {

                return;
            }
            if (is_array($phones)) {
                foreach ($phones as $phone) {
                    $this->start_send($message, $phone);
                }
            }
        }
        public function user_sms($options, $condition_check = true, $message = null)
        {
            if ($options['active'] === false) {
                return;
            }
            if (!isset($_REQUEST['input_' . $this->options['user_phone_number']])) {
                return;
            }
            $phone_number = sanitize_text_field($_REQUEST['input_' . $this->options['user_phone_number']]);


            if (payamito_verify_moblie_number($phone_number) === false) {
                return;
            }

            if ($condition_check === true) {

                if ($options['conditional_active'] === true) {

                    $condition = PGF_Functions::condition($this->entry, $this->form, $options, 'user');
                } else {
                    $condition = true;
                }
                if ($condition === false) {
                    return;
                }
            }

            if (is_null($message)) {
                $message = $this->is_ready_send($options);
            }
            if ($message == false) {

                return;
            }
            $this->start_send($message, $phone_number);
        }
        public function payment_sms($options, $status)
        {
            if ($options['active'] === false) {
                return false;
            }
            $this->admin_sms($options['admin'], false);
            $this->user_sms($options['user'], false);
        }

        public function check_admin_phone_number($phones)
        {

            if (!is_array($phones) || count($phones) == 0) {

                return false;
            }
            $phones = array_column($phones, 'admin_phone_number');
            $phones = array_unique($phones);

            return $phones;
        }
        public function get_tag_value($tag)
        {
            switch ($tag) {
                case 'payment_status':
                case '{payment_status}':
                    return empty($this->entry['payment_status']) ? '' : $this->entry['payment_status'];
                    break;
                case 'payment_method':
                case '{payment_method}':
                    return empty($this->entry['payment_method']) ? '' : $this->entry['payment_method'];
                    break;
                case 'transaction_id':
                case '{transaction_id}':
                    return empty($this->entry['transaction_id']) ? '' : $this->entry['transaction_id'];

                    break;
                case 'ip':
                case '{ip}':
                    $value = GFCommon::replace_variables('{ip}', $this->form, $this->form, false, true, false, 'text');
                    return $value;
                    break;
                case 'date_mdy':
                case '{date_mdy}':
                    $value = GFCommon::replace_variables('{date_mdy}', $this->form, $this->form, false, true, false, 'text');
                    return $value;
                    break;
                case 'post_id':
                case '{post_id}':
                    $value = GFCommon::replace_variables('{embed_post:ID}', $this->form, $this->form, false, true, false, 'text');
                    return $value;
                    break;
                case 'post_title':
                case '{post_title}':
                    $value = GFCommon::replace_variables('{embed_post:post_title}', $this->form, $this->form, false, true, false, 'text');
                    return $value;
                    break;
                case 'embed_url':
                case '{embed_url}':
                    $value = GFCommon::replace_variables('{embed_url}', $this->form, $this->form, false, true, false, 'text');
                    return $value;
                    break;
                case 'entry_url':
                case '{entry_url}':
                    $value = GFCommon::replace_variables('entry_url', $this->form, $this->form, false, true, false, 'text');
                    return $value;
                    break;
                case 'form_id':
                case '{form_id}':
                    return   $this->form['id'];
                    break;
                case 'form_title':
                case '{form_title}':
                    return   $this->form['title'];
                    break;
                case 'display_name':
                case '{user_email}':
                    $value = GFCommon::replace_variables('{user:user_email}', $this->form, $this->form, false, true, false, 'text');
                    return $value;
                    break;
                case 'user_email':
                case '{user_email}':
                    $value = GFCommon::replace_variables('{user:user_email}', $this->form, $this->form, false, true, false, 'text');
                    return $value;
                    break;
                case 'user_login':
                case '{user_login}':
                    $value = GFCommon::replace_variables('{user:user_login}', $this->form, $this->form, false, true, false, 'text');
                    return $value;
                    break;

                case 'OTP':
                case '{OTP}':
                    $value = Payamito_OTP::payamito_generate_otp($this->OTP_count);
                    $this->OTP = $value;
                    return $value;
                    break;

                case 'site_name':
                case '{site_name}':
                    $value = get_bloginfo('name');
                    return $value;
                    break;
                default:
                    $value = $this->entry[$tag];
                    return $value;
            }
        }
        public function start_send($message, $phone_number)
        {
            if (is_null($this->send)) {

                $this->send = PGF_Send::get_instance();
            }
            $result = [];
            $note = "";
            switch ($message['type']) {

                case 1:
                    $send_pattern = $this->set_pattern($message['message']);

                    $result = $this->send->Send_pattern($phone_number, $send_pattern, $message['pattern_id']);

                    $note = sprintf(__('Sending an SMS to phone number  %s was accompanied by this message| %s', 'payamito-gravity-form'), $phone_number, $result['message']);

                    break;

                case 2:

                    $result = $this->send->Send($phone_number, $message['message']);
                    $note = sprintf(__('Sending an SMS to phone number  %s was accompanied by this message %s', 'payamito-gravity-form'), $phone_number, $result['message']);
                    break;
            }
            if (isset($this->entry["id"])) {
                RGFormsModel::add_note($this->entry["id"], 0, __('Payamito', 'payamito-gravity-form'), $note);
            }

            return $result;
        }

        public function set_value($text)
        {
            $tags = PGF_Functions::get_tags();


            $value = [];

            foreach ($tags as $index => $tag) {

                array_push($value, $this->get_tag_value($index));
            }

            $message = str_replace($tags, $value, $text);

            return $message;
        }

        public  function set_pattern($pattern)
        {
            $send_pattern = [];

            foreach ($pattern as $index => $item) {

                $send_pattern[$item[1]] = $this->get_tag_value($item[0]);
            }
            return $send_pattern;
        }

        public  function is_ready_send($option)
        {
            $message = $this->set_message($option);

            if (is_null($message)) {

                return false;
            }

            return $message;
        }

        public  function set_message($option)
        {

            if ($option['active_pattern'] === true) {

                $pattern = $option['pattern'];

                $pattern_id = trim($option["pattern_id"]);

                if (is_array($pattern) && count($pattern) > 0 && is_numeric($pattern_id)) {

                    return array('type' => 1, 'message' => $pattern, 'pattern_id' => $pattern_id);
                } else {

                    return null;
                }
            } else {

                $text = trim($option['text']);

                if ($text == '') {

                    return null;
                } else {
                    if ($this->is_verification === true) {
                        $message = $this->otp_set_text_value($text);
                    } else
                        $message = $this->set_value($text);
                    return array('type' => 2, 'message' => $message);
                }
            }
        }

        public function otp_set_text_value($text)
        {

            $text = str_replace(["{OTP}", "{site-name}"], [$this->get_tag_value("{OTP}"), $this->get_tag_value("{site_name}")], $text);
            return $text;
        }

        public function get_OTP()
        {
            return $this->OTP;
        }
    }
}
