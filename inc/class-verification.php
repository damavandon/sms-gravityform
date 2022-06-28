<?php


if (!defined('ABSPATH')) {
	exit;
}
class PGF_Verification
{
	public static $_instance = null;


	public static function get_instance()
	{
		if (is_null(self::$_instance)) {

			self::$_instance = new self();
			self::$_instance->init();
		}
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		return self::$_instance;
	}
	public function init()
	{
		$this->front_hooks();
		$this->ajax_hooks();
	}

	public  function front_hooks()
	{
		if (is_admin()) {
			return;
		}
		add_filter('gform_field_validation', array($this, 'validation'), 10, 4);
		add_action('gform_field_input', array($this, 'input'), 10, 5);
	}
	public function init_ajax_action($content, $field, $value, $lead_id, $form_id)
	{
		if (!isset($_POST['gform_ajax'])) {
			return $content;
		}
		global	$payamito_gf_options;
		$payamito_gf_otp = get_option("payamito_gf_otp");
		$options = $payamito_gf_options[$form_id];
		$is_mobile_field = ($options['verification_field'] == $field['id']);

		if ($is_mobile_field === false) {
			return $content;
		}

		$content .= '<div  class="gfield gfield--width-full field_sublabel_below field_description_below gfield_visibility_visible" ><label class="gfield_label" for="">' . $payamito_gf_otp['title'] . '</label><div class="ginput_container ginput_container_text"><input name="otp" placeholder=' . $payamito_gf_otp['placeholder'] . ' id="otp" type="text" value="" class="large" aria-invalid="false"> </div></div>';
		$content .= "<div>";
		$content .= '<input type="button" id="ajax_send_otp" class="button payamito-gf-opt-button" style="margin: 1% 0%;"   name="send_otp" id="send_otp" style="padding: 6px;margin-right: 2px;" value="' . __("Resend", "payamito-gravity-form") . '" >';
		$content .= "</div>";
		return $content;
	}
	public function ajax_hooks()
	{
		add_action('wp_ajax_nopriv_payamito_gf_validation', [$this, 'ajax']);
		add_action('wp_ajax_payamito_gf_validation', [$this, 'ajax']);
	}

	public  function ajax()
	{

		$form_id = isset($_POST['form_id']) ? sanitize_text_field($_POST['form_id']) : false;
		if ($form_id === false) {
			die;
		}
		if (!wp_verify_nonce($_POST['nonce'], 'payamito_gf')) {
			die;
		}
		global	$payamito_gf_options;
		$otp_options = get_option('payamito_gf_otp');

		$options = $payamito_gf_options[$form_id];
		if (!$options['active'] || !$options['verification_active']) {
			die;
		}

		$mobile_value = isset($_POST['phone_number']) ? sanitize_text_field($_REQUEST['phone_number']) : '';
		$mobile = preg_replace("/[^0-9]/", "", payamito_to_english_number($mobile_value));
		$zaro = $mobile[0];
		if ($zaro == "0") {
			$mobile = substr_replace($mobile, "", 0, 1);
		}
		if (!payamito_verify_moblie_number($mobile)) {
			return  $this->ajax_response(-1, self::message(0));
		}
		$resend_time = PGF_Functions::resent_time_check($mobile, $otp_options['resend_time']);
		if ($resend_time !== true) {
			return  $this->ajax_response(-1, sprintf(__("Please wait %s seconds", 'payamito-gravity-form'), $resend_time));
		}
		$submit = payamito_gf()->submit;
		$submit->OTP_count = $otp_options['count'];
		$submit->is_verification = true;
		$message = $submit->is_ready_send($otp_options);

		if ($message === false) {
			return  $this->ajax_response(-1, self::message(6));
		} else {
			$send = $submit->start_send($message, $mobile);
			if ($send['result'] === true) {

				$OTP = $submit->get_OTP();
				if ($OTP != null) {
					Payamito_OTP::payamito_set_session($mobile, $OTP);
					$_SESSION[$mobile] = $OTP;
					$_SESSION[$mobile . '_M'] = $OTP;
				}
				return  $this->ajax_response(1, self::message(1));
			} else {
				return  $this->ajax_response(-1, $send['message']);
			}
		}
	}
	/**
	 * ajax response
	 *The response to the OTP request is given in Ajax
	 * @access public
	 * @since 1.0.0
	 * @static
	 */
	public  function  ajax_response(int $type = -1, $message, $redirect = null)
	{
		wp_send_json(array('e' => $type, 'message' => $message, "re" => $redirect));
		die;
	}
	/**
	 * ajax response message
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array
	 * @static
	 */
	public static function message($key)
	{
		$messages = array(
			__('Mobile number is incorrect', 'payamito-gravity-form'),
			__('OTP sent successfully', 'payamito-gravity-form'),
			__('Failed to send OTP ', 'payamito-gravity-form'),
			__('An unexpected error occurred. Please contact support ', 'payamito-gravity-form'),
			__('Enter OTP number ', 'payamito-gravity-form'),
			__(' OTP is Incorrect ', 'payamito-gravity-form'),
			__("The message could not be sent due to incorrect settings. Please contact support", "payamito-gravity-form")
		);
		return $messages[$key];
	}

	public  function input($input, $field, $value, $entry_id, $form_id)
	{
		global	$payamito_gf_options;
		$options = $payamito_gf_options[$form_id];
		$is_mobile_field = ($options['verification_field'] == $field['id']);
		if ($is_mobile_field === false) {
			return $input;
		}
		$form_id  = empty($form_id) ? rgget("id") : $form_id;
		$otp_options = get_option('payamito_gf_otp');

		if (isset($_REQUEST['gf_page']) && $_REQUEST['gf_page'] = 'preview') {
			$this->front_js();
		}
		wp_enqueue_script('payamito-gf-form', PAYAMITO_GF_URL . '/assets/js/form.js', array('jquery'), false, true);
		wp_enqueue_style('payamito-gf-form', PAYAMITO_GF_URL . '/assets/css/form.css',);

		wp_localize_script('payamito-gf-form', 'payamito_gf_form', [
			'ajaxurl' => admin_url('admin-ajax.php'),
			'form_id' => $form_id,
			'field_id' => $options['verification_field'],
			'resend_time' => $otp_options['resend_time'],
			'nonce' => wp_create_nonce('payamito_gf'),
			'text' => __('Resend', 'payamito-gravity-form'),
			"OTP_Success" => __("Send OTP success", "payamito-gravity-form"),
			"OTP_Fail" => __("Send OTP failed", "payamito-gravity-form"),
			'Send' => __("Send request failed please contact with support team ", "payamito-gravity-form"),
			'OTP_Wrong' => __("OTP is wrong", "payamito-gravity-form"),
			'OTP_Correct' => __("OTP is wrong", "payamito-gravity-form"),
			'invalid' => __("Mobile number is incorrct", "payamito-gravity-form"),
			'error' => __("Error", "payamito-gravity-form"),
			'success' => __("Success", "payamito-gravity-form"),
			"warning" => __("Warning", "payamito-gravity-form"),
			'enter' => __('Enter OTP number ', 'payamito-gravity-form'),
			'second' => __('Second', 'payamito-gravity-form'),
			'sended' => false,
			'title' => $otp_options['title'],
			'placeholder' => $otp_options['placeholder'],
		]);


		return $input;
	}
	public function front_js()
	{
		wp_enqueue_script("payamito-notification-js",  PAYAMITO_URL . "/assets/js/notification.js", array('jquery'), false, true);
		wp_enqueue_script("payamito-spinner-js",  PAYAMITO_URL . "/assets/js/spinner.js", array('jquery'), false, true);

		wp_enqueue_style("payamito-notification-css",  PAYAMITO_URL . "/assets/css/notification.css");
		wp_enqueue_style("payamito-spinner-css",  PAYAMITO_URL . "/assets/css/spinner.css", array());
	}


	public  function validation($result, $value, $form, $field)
	{

		global	$payamito_gf_options;

		$options = $payamito_gf_options[$form['id']];
		$active = $options['active'] == false;
		$verification_active = $options['verification_active'] == false;

		if ($active) {
			return $result;
		}

		if ($verification_active) {
			return $result;
		}

		$verification_field = (int)$options['verification_field'];
		$field_id = $field['id'];
		if ($verification_field !== $field_id) {
			$result;
		}
		$is_mobile_field = ($options['verification_field'] == $field['id']);
		if ($is_mobile_field === false) {
			return $result;
		}
		add_filter('gform_validation_message', array(__CLASS__, 'change_message'), 10, 2);

		wp_enqueue_script('payamito-gf-form', PAYAMITO_GF_URL . '/assets/js/form.js', array('jquery'), false, true);

		$mobile_value  = isset($_REQUEST['input_' . $options['verification_field']]) ? sanitize_text_field($_REQUEST['input_' . $options['verification_field']]) : '';
		$mobile = preg_replace("/[^0-9]/", "", payamito_to_english_number($mobile_value));
		$zaro = $mobile[0];
		if ($zaro == "0") {
			$mobile = substr_replace($mobile, "", 0, 1);
		}
		$validated = isset($_SESSION[$mobile . '_validated']) && $_SESSION[$mobile . '_validated'] === true ? true : false;
		if ($validated === true) {
			return $result;
		}
		if (!payamito_verify_moblie_number($mobile)) {
			$result["is_valid"] = false;
			$result["message"]  = __("Please enter a valide mobile number in Field related to mobile number", "payamito-gravity-form");
			return $result;
		}

		$isset_session = isset($_SESSION[$mobile . "_sended"]);
		if ($isset_session) {
			$sended = is_null($_SESSION[$mobile . "_sended"]) ? false : $_SESSION[$mobile . "_sended"];
		} else {
			$sended = false;
		}
		if (!$isset_session && $sended != true) {
			$this->submit_send($mobile);
			add_filter('gform_field_content', array($this, 'init_ajax_action'), 10, 5);
		}
		if (isset($_REQUEST['otp'])) {
			$code = payamito_to_english_number(sanitize_text_field($_REQUEST['otp']));
		} else {
			$code = "";
		}

		$validate = Payamito_OTP::payamito_validation_session($mobile . '_M', $code);
		if ($validate == true) {
			unset($_SESSION[$mobile . 'T']);
			unset($_SESSION[$mobile]);
			unset($_SESSION[$mobile . '_M']);
			unset($_SESSION[$mobile . '_sended']);
			wp_localize_script('payamito-gf-form', 'payamito_gf_submit', [
				'ajaxurl' => admin_url('admin-ajax.php'),
				'sended'  => false,
				'show' => false

			]);
			$_SESSION[$mobile . '_validated'] = true;
			return $result;
		} else {
			$result["is_valid"] = false;
			wp_localize_script('payamito-gf-form', 'payamito_gf_submit', [
				'ajaxurl' => admin_url('admin-ajax.php'),
				'sended'  => false,
				'show' => true

			]);
			wp_add_inline_script('payamito-gf-form', 'const MYSCRIPT = ' . json_encode(array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'otherParam' => 'some value',
			)), 'before');

			if (empty($code)) {

				$result["message"]  = sprintf(__("Enter the code sent to %s", "payamito-gravity-form"), $mobile);
				add_filter('gform_field_content', array($this, 'init_ajax_action'), 10, 5);
			}
			if (!empty($code)) {

				$result["message"]  = sprintf(__("The code entered is for %s incorrect", "payamito-gravity-form"), $mobile);
				add_filter('gform_field_content', array($this, 'init_ajax_action'), 10, 5);
			}

			return $result;
		}
		return $result;
	}
	public static function change_message($message, $form)
	{
		$message = __("Please confirm your phone ", 'payamito-gravity-form');
		return sprintf("<p style='color: #c02b0a; font-weight: 700;'>%s</p>", $message);
	}
	public function submit_send($mobile)
	{

		$result = [];
		$result['is_valid'] = false;
		$otp_options = get_option('payamito_gf_otp');
		$resend_time = PGF_Functions::resent_time_check($mobile, $otp_options['resend_time']);
		if ($resend_time !== true) {
			$result['message'] = sprintf(__("Please wait %s seconds", 'payamito-gravity-form'), $resend_time);
			return $result;
		}
		$submit = payamito_gf()->submit;
		$submit->OTP_count = $otp_options['count'];
		$submit->is_verification = true;
		$message = $submit->is_ready_send($otp_options);

		if ($message === false) {
			return 	$result['message'] = self::message(6);
		} else {
			$send = $submit->start_send($message, $mobile);
			if ($send['result'] === true) {
				$OTP = $submit->get_OTP();
				if ($OTP != null) {
					Payamito_OTP::payamito_set_session($mobile, $OTP);
					$_SESSION[$mobile . "_M"] = $OTP;
					$_SESSION[$mobile . "_sended"] = true;
				}
				wp_enqueue_script('payamito-gf-form', PAYAMITO_GF_URL . '/assets/js/form.js', array('jquery'), false, true);
				wp_localize_script('payamito-gf-form', 'payamito_gf_submit', [
					'ajaxurl' => admin_url('admin-ajax.php'),
					'sended'  => true,
					'show'  => true,
				]);

				$result['message'] = sprintf(__('A message containing the verification code was sent to %s. Enter the verification code in the box above', 'payamito-gravity-form'), $mobile);
				return $result;
			} else {
				wp_enqueue_script('payamito-gf-form', PAYAMITO_GF_URL . '/assets/js/form.js', array('jquery'), false, true);
				wp_localize_script('payamito-gf-form', 'payamito_gf_submit', [
					'ajaxurl' => admin_url('admin-ajax.php'),
					'sended'  => false,
					'show'  => false,
					'message' => $send['message']
				]);
				$result['message'] = $send['message'];
				return $result;
			}
		}
	}
}
