<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════

?><?php

	namespace Payamito\GravityForm;

	use PGF_Functions;


	/**
	 * Register an options panel.
	 *
	 * @package Payamito
	 */

	// Exit if accessed directly
	if (!defined('ABSPATH')) {
		exit;
	}

	class  Settings
	{
		/**
		 * Holds the options panel controller.
		 *
		 * @var object
		 */
		protected $panel;

		public $forms;

		public $tags;

		public $meta_keys;


		private static $_instance = null;

		public static function get_instance()
		{
			if (is_null(self::$_instance)) {

				self::$_instance = new self();
			}
			return self::$_instance;
		}
		/**
		 * Get things started.
		 */
		public function __construct()
		{
			add_filter('payamito_add_section', [$this, 'register_settings'], 1);

			add_action('admin_footer', [$this, "print_tags"]);
			add_action('kianfr_' . 'payamito' . '_save_before', [$this, 'option_save'], 10, 1);
			add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
		}

		public function admin_enqueue_scripts()
		{
			$url = PAYAMITO_GF_URL . "/inc/admin/assets/css/admin-style.css";
			wp_enqueue_style("payamito-gf-admin-style", $url);
			wp_enqueue_script("payamito-gf-admin-js", PAYAMITO_GF_URL . '/inc/admin/assets/js/admin-js.js', ['jquery'], false, true);
		}

		public function option_save($options)
		{
			$options_save = [];
			foreach ($options  as $index => $option) {
				$flag = strpos($index, 'payamito_gf_accordion_');
				if ($flag === 0) {
					$id = str_replace('payamito_gf_accordion_', '', $index);
					$options_save[$id]['active'] = $option['active'] == '1' ? true : false;
					/////////////////////////////////////Verification
					$options_save[$id]['verification_active'] = $option['verification_active'] == '1' ? true : false;
					$options_save[$id]['verification_field'] = !empty(trim($option['verification_filed'])) ? $option['verification_filed'] : '';

					/////////////////////////////completed
					$options_save[$id]['completed']['active'] = $option['CoPay_active'] == '1' ? true : false;
					$options_save[$id]['completed']['admin']['active'] = $option['CoPayAdmin_active'] == '1' ? true : false;
					$options_save[$id]['completed']['admin']['active_pattern'] = $option['CoPayAdmin_active_pattern'] == '1' ? true : false;
					$options_save[$id]['completed']['admin']['pattern_id'] = !empty(trim($option['CoPayAdmin_pattern_id'])) ? $option['CoPayAdmin_pattern_id'] : '';
					$options_save[$id]['completed']['admin']['pattern'] = is_array($option['CoPayAdmin_pattern']) ? $option['CoPayAdmin_pattern'] : [];
					$options_save[$id]['completed']['admin']['text'] = !empty(trim($option['CoPayAdmin_txt'])) ? $option['CoPayAdmin_txt'] : '';

					$options_save[$id]['completed']['user']['active'] = $option['CoPayUser_active'] == '1' ? true : false;
					$options_save[$id]['completed']['user']['active_pattern'] = $option['CoPayUser_active_pattern'] == '1' ? true : false;
					$options_save[$id]['completed']['user']['pattern_id'] = !empty(trim($option['CoPayUser_pattern_id'])) ? $option['CoPayUser_pattern_id'] : '';
					$options_save[$id]['completed']['user']['pattern'] = is_array($option['CoPayUser_pattern']) ? $option['CoPayUser_pattern'] : [];
					$options_save[$id]['completed']['user']['text'] = !empty(trim($option['CoPayUser_txt'])) ? $option['CoPayUser_txt'] : '';

					/////////////////////////////failed
					$options_save[$id]['failed']['active'] = $option['FaPay_active'] == '1' ? true : false;
					$options_save[$id]['failed']['admin']['active'] = $option['FaPayAdmin_active'] == '1' ? true : false;
					$options_save[$id]['failed']['admin']['active_pattern'] = $option['FaPayAdmin_active_pattern'] == '1' ? true : false;
					$options_save[$id]['failed']['admin']['pattern_id'] = !empty(trim($option['FaPayAdmin_pattern_id'])) ? $option['FaPayAdmin_pattern_id'] : '';
					$options_save[$id]['failed']['admin']['pattern'] = is_array($option['FaPayAdmin_pattern']) ? $option['FaPayAdmin_pattern'] : [];
					$options_save[$id]['failed']['admin']['text'] = !empty(trim($option['FaPayAdmin_txt'])) ? $option['FaPayAdmin_txt'] : '';

					$options_save[$id]['failed']['user']['active'] = $option['FaPayUser_active'] == '1' ? true : false;
					$options_save[$id]['failed']['user']['active_pattern'] = $option['FaPayUser_active_pattern'] == '1' ? true : false;
					$options_save[$id]['failed']['user']['pattern_id'] = !empty(trim($option['FaPayUser_pattern_id'])) ? $option['FaPayUser_pattern_id'] : '';
					$options_save[$id]['failed']['user']['pattern'] = is_array($option['FaPayUser_pattern']) ? $option['FaPayUser_pattern'] : [];
					$options_save[$id]['failed']['user']['text'] = !empty(trim($option['FaPayUser_txt'])) ? $option['FaPayUser_txt'] : '';

					////////////////////////////cancelled
					$options_save[$id]['cancelled']['active'] = $option['CanPay_active'] == '1' ? true : false;
					$options_save[$id]['cancelled']['admin']['active'] = $option['CanPayAdmin_active'] == '1' ? true : false;
					$options_save[$id]['cancelled']['admin']['active_pattern'] = $option['CanPayAdmin_active_pattern'] == '1' ? true : false;
					$options_save[$id]['cancelled']['admin']['pattern_id'] = !empty(trim($option['CanPayAdmin_pattern_id'])) ? $option['CanPayAdmin_pattern_id'] : '';
					$options_save[$id]['cancelled']['admin']['pattern'] = is_array($option['CanPayAdmin_pattern']) ? $option['CanPayAdmin_pattern'] : [];
					$options_save[$id]['cancelled']['admin']['text'] = !empty(trim($option['CanPayAdmin_txt'])) ? $option['CanPayAdmin_txt'] : '';

					$options_save[$id]['cancelled']['user']['active'] = $option['CanPayUser_active'] == '1' ? true : false;
					$options_save[$id]['cancelled']['user']['active_pattern'] = $option['CanPayUser_active_pattern'] == '1' ? true : false;
					$options_save[$id]['cancelled']['user']['pattern_id'] = !empty(trim($option['CanPayUser_pattern_id'])) ? $option['CanPayUser_pattern_id'] : '';
					$options_save[$id]['cancelled']['user']['pattern'] = is_array($option['CanPayUser_pattern']) ? $option['CanPayUser_pattern'] : [];
					$options_save[$id]['cancelled']['user']['text'] = !empty(trim($option['CanPayUser_txt'])) ? $option['CanPayUser_txt'] : '';
					////////////////////////////admin
					$options_save[$id]['admin']['active'] = $option['admin_active'] == '1' ? true : false;
					$options_save[$id]['admin_phone_number'] = is_array($option['admin_phone_number_repeater']) ? $option['admin_phone_number_repeater'] : [];
					$options_save[$id]['admin']['active_pattern'] = $option['admin_active_p'] == '1' ? true : false;
					$options_save[$id]['admin']['pattern_id'] = !empty(trim($option['admin_pattern_id'])) ? $option['admin_pattern_id'] : '';
					$options_save[$id]['admin']['pattern'] = is_array($option['admin_pattern']) ? $option['admin_pattern'] : [];
					$options_save[$id]['admin']['text'] = !empty(trim($option['text_admin'])) ? $option['text_admin'] : '';
					$options_save[$id]['admin']['conditional_active'] = $option['admin_active_conditional'] == '1' ? true : false;
					$options_save[$id]['admin']['conditional_type'] = $option['admin_active_conditional'] == '1' ? 'all' : 'any';
					$options_save[$id]['admin']['conditional'] = is_array($option['admin_conditional_repeater']) ? $option['admin_conditional_repeater'] : [];
					////////////////////////////user
					$options_save[$id]['user']['active'] = $option['user_active'] == '1' ? true : false;
					$options_save[$id]['user_phone_number'] = $option['user_phone_number'];
					$options_save[$id]['user']['active_pattern'] = $option['user_active_p'] == '1' ? true : false;
					$options_save[$id]['user']['pattern_id'] = !empty(trim($option['user_pattern_id'])) ? $option['user_pattern_id'] : '';
					$options_save[$id]['user']['pattern'] = is_array($option['user_pattern']) ? $option['user_pattern'] : [];
					$options_save[$id]['user']['text'] = !empty(trim($option['text_user'])) ? $option['text_user'] : '';
					$options_save[$id]['user']['conditional_active'] = $option['user_active_conditional'] == '1' ? true : false;
					$options_save[$id]['user']['conditional_type'] = $option['user_active_conditional'] == '1' ? 'all' : 'any';
					$options_save[$id]['user']['conditional'] = is_array($option['user_conditional_repeater']) ? $option['user_conditional_repeater'] : [];
				}
			}
			update_option('payamito_gf', $options_save);

			$options_save = [];
			$options_save['active'] = $options['payamito_gf_otp']['otp_active'] == '1' ? true : false;
			$options_save['active_pattern'] = $options['payamito_gf_otp']['otp_active_p'] == '1' ? true : false;
			$options_save['pattern'] = $options['payamito_gf_otp']['otp_repeater'];
			$options_save['pattern_id'] = $options['payamito_gf_otp']['otp_p'];
			$options_save['count'] = $options['payamito_gf_otp']['number_of_code'];
			$options_save['resend_time'] = $options['payamito_gf_otp']['again_send_time'];
			$options_save['text'] = $options['payamito_gf_otp']['otp_sms'];
			$options_save['title'] = $options['payamito_gf_otp']['otp_title'];
			$options_save['placeholder'] = $options['payamito_gf_otp']['otp_placeholder'];

			update_option('payamito_gf_otp', $options_save);
		}
		public function init_support_fields($form)
		{
			if (!is_array($form)) return [];
			$supports = [
				'number',
				'phone',
				'username',
				'text',
			];
			$supports = apply_filters("payamito_gf_support_fields", $supports);
			if (!is_array($supports)) return [];
			$fields=[];
			foreach ($form['fields'] as $field) {
				if(in_array($field->type,$supports)){
					$fields[$field->id]=$field->label;
				}
			}
			return $fields;
		}
		public function register_settings($section)
		{
			$this->forms = PGF_Functions::get_forms();
			$this->tags = PGF_Functions::get_tags();
			if (!class_exists('GFForms') || count($this->forms) <= 0) {
				$settings = [
					'title'  => esc_html__('Gravity Forms', 'payamito-gravity-form'),
					'fields' => [
						array(
							'type'    => 'heading',
							'content' => esc_html__('There is no active form. Create at least one form', 'payamito-gravity-form'),
						),
					],
				];
			} else {

				$settings = array(
					'title'  => esc_html__('Gravity Forms', 'payamito-gravity-form'),
					'fields' => array(
						array(
							'id'            => 'payamito_gf_otp',
							'type'          => 'accordion',
							'title'  => esc_html__('Verification', 'payamito-gravity-form'),
							'accordions'    => array(
								array(
									'title'   => esc_html__('Verification', 'payamito-gravity-form'),
									'fields'    => array(
										array(
											'type'       => 'notice',
											'style'      => 'warning',
											'content'    => esc_html__('"notice" send pattern need to help', 'payamito-gravity-form'),
											'dependency' => array("otp_active_p", '==', 'true'),
											'class' => 'pattern_background',
										),
										array(
											'id'    => 'otp_active_p',
											'type'  => 'switcher',
											'title'      => payamito_dynamic_text('pattern_active_title'),
											'desc'       => payamito_dynamic_text('pattern_active_desc'),
											'help'       => payamito_dynamic_text('pattern_active_help'),
											'class' => 'pattern_background',
										),
										array(
											'id'   => 'otp_p',
											'type'    => 'text',
											'title'      => payamito_dynamic_text('pattern_ID_title'),
											'desc'       => payamito_dynamic_text('pattern_ID_desc'),
											'help'       => payamito_dynamic_text('pattern_ID_help'),
											'dependency' => array("otp_active_p", '==', 'true'),
											'class' => 'pattern_background',

										),
										array(
											'id'     => 'otp_repeater',
											'type'   => 'repeater',
											'title'      => payamito_dynamic_text('pattern_Variable_title'),
											'desc'       => payamito_dynamic_text('pattern_Variable_desc'),
											'help'       => payamito_dynamic_text('pattern_Variable_help'),
											'dependency' => array("otp_active_p", '==', 'true'),
											'class' => 'pattern_background',
											'fields' => array(
												array(
													'id'   => 0,
													'placeholder' =>  esc_html__("Tags", "payamito-gravity-form"),
													'class' => 'pattern_background',
													'type' => 'select',
													'options' =>
													array(
														"{OTP}" => esc_html__('OTP', 'payamito-gravity-form'),
														"{site_name}" => esc_html__('Wordpress title', 'payamito-gravity-form'),

													)
												),
												array(
													'id'    => 1,
													'type'  => 'number',
													'placeholder' =>  esc_html__("Your tag", "payamito-gravity-form"),
													'class' => 'pattern_background',
													'default' => '0',
												),
											)
										),
										array(
											'id'   => 'otp_sms',
											'title'      => payamito_dynamic_text('send_content_title'),
											'desc'       => payamito_dynamic_text('send_content_desc'),
											'help'       => payamito_dynamic_text('send_content_help'),
											'default' => esc_html__('کاربر گرامی کد تایید ثبت نام {OTP} می باشد. ', 'payamito-gravity-form'),
											'class' => 'pattern_background',
											'type' => 'textarea',
											'dependency' => array("otp_active_p", '!=', 'true'),
											'desc' => esc_html__('Use {OTP},{site-name} tags', 'payamito-gravity-form'),
										),
										array(
											'id'   => 'number_of_code',
											'title' => esc_html__('Number of OTP code', 'payamito-gravity-form'),
											'desc' => esc_html__('Number of OTP code that you want send for user', 'payamito-gravity-form'),
											'type' => 'select',

											'options' => apply_filters("again_send_number", array(
												"4" => "4",
												"5" => "5",
												"6" => "6",
												"7" => "7",
												"8" => "8",
												"9" => "9",
												"10" => "10",
											)),
										),
										array(
											'id'   => 'again_send_time',
											'title' => esc_html__('Send Again', 'payamito-gravity-form'),
											'desc' => esc_html__('When you want the user to re-request OTP.', 'payamito-gravity-form'),
											'type' => 'select',

											'options' => apply_filters("again_send_time", array(
												"30" => "30",
												"60" => "60",
												"90" => "90",
												"120" => "120",
												"300" => "300",
											)),
										),
										array(
											'id'    => 'otp_title',
											'type'  => 'text',
											'title' => esc_html__('OTP field title', 'payamito-gravity-form'),
											'default' => 'OTP',

										),
										array(
											'id'    => "otp_placeholder",
											'type'  => 'text',
											'title' => esc_html__('OTP field Placeholder', 'payamito-gravity-form'),
											'default' => 'OTP',

										),
									)
								),
							)
						),
					)
				);
			}
			foreach ($this->forms as $form) {
				array_push($settings['fields'], $this->set_form_field($form));
			}
			array_push($section, $settings);

			return $section;
		}

		public function get_for_select_field($form = [])
		{

			$tags_select = [];
			if (is_array($this->tags)) {

				foreach ($this->tags as $index => $tag) {

					$default[$index] = $tag;
				}

				$fields = $this->form_fields($form);
				if (count($fields) != 0) {
					$merged = array_merge($tags_select, [__('Fields', 'payamito-gravity-form') => $fields], [__('Defualt', 'payamito-gravity-form') => $default]);
				} else {
					$merged = array_merge([__('Defualt', 'payamito-gravity-form') => $default]);
				}
				ksort($merged, SORT_STRING);

				return $merged;
			}
			return [];
		}

		/**
		 * print tags for modal
		 *
		 */
		public function print_tags()
		{

			if (!isset($_REQUEST['page']) ||  $_REQUEST['page'] != 'payamito') {
				return;
			}

			$html = "<div id='payamito-gravity-form-modal' class='modal ' >";
			$html .= "<div>";
			foreach ($this->tags as $index => $tag) {
				$html .= "<div class='payamito-tags-modal'><p class='payamito-gf-tag-modal' >" . "{" . $index . "}" . "</p>";
				$html .= "<span>" . $tag . "</span></div>";
			}
			$html .= '</div>';
			echo $html;
		}

		public  function option_set_pattern($slug, $form, $max = 15)
		{
			$dep = $slug . "_active_p|" . $slug . "_active";
			return array(
				'id'     => $slug . '_pattern',

				'type'   => 'repeater',

				'title'      => payamito_dynamic_text('pattern_Variable_title'),
				'desc'       => payamito_dynamic_text('pattern_Variable_desc'),
				'help'       => payamito_dynamic_text('pattern_Variable_help'),
				'max' => $max,
				'class' => "payamito-gravity-form-repeater pattern_background",
				'dependency' => array($dep, '==|==', 'true|true'),
				'fields' => array(
					array(
						'id'          => 0,
						'type'        => 'select',
						'placeholder' =>  esc_html__("Select tag", "payamito-gravity-form"),
						'options'     => $this->get_for_select_field($form),
					),
					array(
						'id'    => 1,
						'type'  => 'number',
						'placeholder' =>  esc_html__("Your tag", "payamito-gravity-form"),
						'default' => '0',
					),
				)
			);
		}

		function form_fields($form)
		{
				$fields = [];
			foreach ($form['fields'] as $field) {
				$fields[$field->id] = $field->label;
			}
			if (count($fields) == 0) {
				$fields['a'] = __('There is no field', 'payamito-gravity-form');
			}
			return $fields;
		}
		public function set_form_field($form)
		{
			$active = __("Active", "payamito-gravity-form");

			$title = (string)$form['title'];
			$slug = (string)$form['id'];

			return	array(
				'id'            => 'payamito_gf_accordion_' . $slug,
				'type'          => 'accordion',
				'title'     => esc_html__(ucfirst($title), 'payamito-gravity-form'),
				'accordions'    => array(
					array(
						'title'     => esc_html__(ucfirst($title), 'payamito-gravity-form'),
						'fields'    => array(
							array(
								'id'   =>  "active",
								'title' => ucfirst($title) . " " . $active,
								'type' => 'switcher'
							),
							array(
								'id'     => 'admin_phone_number_repeater',
								'type'   => 'repeater',
								'title' => esc_html__("Admin phone number", "payamito-gravity-form"),

								'dependency' => array("active", '==', 'true'),
								'fields' => array(
									array(
										'id'    => 'admin_phone_number',
										'type'  => 'text',
										'placeholder' => esc_html__("Admin Phone number ", "payamito-gravity-form"),
										'class' => 'payamito-gravity-form-phone-number ',
										'attributes'  => array(
											'type'      => 'tel',
											'maxlength' => 11,
											'minlength' => 11,
											"pattern" => "[0-9]{3}-[0-9]{3}-[0-9]{4}"
										),
									),
								),
							),
							array(
								'id'          => 'user_phone_number',
								'type'        => 'select',
								'title' => esc_html__('User phone number', 'payamito-gravity-form'),
								'placeholder' =>  esc_html__("Select tag", "payamito-gravity-form"),
								'options'     =>$this->init_support_fields($form),
								'dependency' => array("active", '==', 'true'),

							),
							///////////////////////////////////////////////////////////////////////////////////////////////////

							array(
								'type'    => 'heading',
								'content' => esc_html__('Verification', 'payamito-gravity-form'),
							),
							array(
								'id'    => 	 "verification_active",
								'type'  => 'switcher',
								'title' => esc_html__('Active', 'payamito-gravity-form'),


							),
							array(
								'id'          => 'verification_filed',
								'type'        => 'select',
								'title' => esc_html__('Verification Field', 'payamito-gravity-form'),
								'placeholder' =>  esc_html__("Select tag", "payamito-gravity-form"),
								'options'     => $this->init_support_fields($form),
								'dependency' => array("verification_active", '==', 'true'),

							),
							array(
								'type'    => 'heading',
								'content' => esc_html__('Submit form SMS', 'payamito-gravity-form'),
							),

							array(
								'type'    => 'subheading',
								'content' =>  esc_html__('Admin', 'payamito-gravity-form'),
							),

							array(
								'id'    => 	 "admin_active",
								'type'  => 'switcher',
								'title' => esc_html__('Admin SMS active', 'payamito-gravity-form'),

							),
							array(
								'type'       => 'notice',
								'style'      => 'warning',
								'content'    => esc_html__('"notice" send pattern need to help', 'payamito-gravity-form'),
								'dependency' => array("admin_active", '==', 'true'),
								'class' => 'pattern_background',
							),

							array(
								'id'    =>  "admin_active_p",
								'type'  => 'switcher',
								'title'      => payamito_dynamic_text('pattern_active_title'),
								'desc'       => payamito_dynamic_text('pattern_active_desc'),
								'help'       => payamito_dynamic_text('pattern_active_help'),
								'class' => 'pattern_background',
								'dependency' => array("admin_active", '==', 'true'),

							),
							array(
								'id'   =>  "text_admin",
								'title' => esc_html__('Admin SMS', 'payamito-gravity-form'),
								'default' => esc_html__('مشتری گرامی پرداخت شما با کد پیگیری {transaction_id} با موفقیت انجام شد.', 'payamito-gravity-form'),
								'class' => 'pattern_background',
								'type' => 'textarea',
								'dependency' => array("admin_active|admin_active_p", '==|!=', 'true|true'),
							),
							array(
								'id'   => 	 "admin_pattern_id",
								'type'    => 'text',
								'title'      => payamito_dynamic_text('pattern_ID_title'),
								'desc'       => payamito_dynamic_text('pattern_ID_desc'),
								'help'       => payamito_dynamic_text('pattern_ID_help'),
								'class' => 'pattern_background',
								'dependency' => array("admin_active_p|admin_active", '==|==', 'true|true'),
							),
							$this->option_set_pattern('admin', $form),

							array(
								'type'     => 'callback',
								'dependency' => array("admin_active_p|admin_active", '!=|==', 'true|true'),
								'function' => [$this, 'print_tags_front'],
							),
							array(
								'id'    => 	 "admin_active_conditional",
								'type'  => 'switcher',
								'title' => esc_html__('Conditional', 'payamito-gravity-form'),
								'dependency' => array("admin_active", '==', 'true'),
							),
							array(
								'id'          => 'admin_conditional_type',
								'type'        => 'select',
								'options'     => [
									'all' => esc_html__("All", "payamito-gravity-form"),
									'any' => esc_html__("Any", "payamito-gravity-form"),
								],
								'dependency' => array("admin_active_conditional|admin_active", '==|==', 'true|true'),
								'before' => __("Send SMS to admin if", "payamito-gravity-form"),
								'after' => __('Conditions is true', "payamito-gravity-form"),
							),
							array(
								'id'     =>  'admin_conditional_repeater',
								'type'   => 'repeater',
								'title'  => esc_html__("Admin conditional", "payamito-gravity-form"),
								'max' => '4',
								'class' => "payamito-gravity-form-repeater",
								'dependency' => array("admin_active_conditional|admin_active", '==|==', 'true|true'),
								'fields' => array(
									array(
										'id'          => 'field',
										'type'        => 'select',
										'options'     => $this->form_fields($form)
									),
									array(
										'id'    => 'operator',
										'type'  => 'select',
										'options' => [
											'is' => esc_html__("Is", "payamito-gravity-form"),
											'isnot' => esc_html__("Is not", "payamito-gravity-form"),
											'>' => esc_html__("Greater than", "payamito-gravity-form"),
											'<' => esc_html__("Less than", "payamito-gravity-form"),
											'contains' => esc_html__("Contains", "payamito-gravity-form"),
											'starts_with' => esc_html__("Starts with", "payamito-gravity-form"),
											'ends_with' => esc_html__("Ends with", "payamito-gravity-form"),
										]
									),
									array(
										'id'      => 'value',
										'type'    => 'text',
										'placeholder' => esc_html__("Value", "payamito-gravity-form"),
										'class' => 'payamito-gf-with',
									),
								),
							),
							array(
								'type'    => 'subheading',
								'content' =>  esc_html__('User', 'payamito-gravity-form'),
							),
							array(
								'id'    => 	 "user_active",
								'type'  => 'switcher',
								'title' => esc_html__('User SMS active', 'payamito-gravity-form'),

							),

							array(
								'type'       => 'notice',
								'style'      => 'warning',
								'content'    => esc_html__('"notice" send pattern need to help', 'payamito-gravity-form'),
								'dependency' => array("user_active", '==', 'true'),
								'class' => 'pattern_background',
							),
							array(
								'id'    =>  "user_active_p",
								'type'  => 'switcher',
								'title'      => payamito_dynamic_text('pattern_active_title'),
								'desc'       => payamito_dynamic_text('pattern_active_desc'),
								'help'       => payamito_dynamic_text('pattern_active_help'),
								'class' => 'pattern_background',
								'dependency' => array("user_active", '==', 'true'),

							),
							array(
								'id'   =>  "text_user",
								'title'      => payamito_dynamic_text('send_content_title'),
								'desc'       => payamito_dynamic_text('send_content_desc'),
								'help'       => payamito_dynamic_text('send_content_help'),
								'default' => esc_html__('مشتری گرامی پرداخت شما با کد پیگیری {transaction_id} با موفقیت انجام شد.', 'payamito-gravity-form'),
								'class' => 'pattern_background',
								'type' => 'textarea',
								'dependency' => array("user_active|user_active_p", '==|!=', 'true|true'),
							),
							array(
								'id'   => 	 "user_pattern_id",
								'type'    => 'text',
								'title'      => payamito_dynamic_text('pattern_ID_title'),
								'desc'       => payamito_dynamic_text('pattern_ID_desc'),
								'help'       => payamito_dynamic_text('pattern_ID_help'),
								'class' => 'pattern_background',
								'dependency' => array("user_active_p|user_active", '==|==', 'true|true'),
							),
							$this->option_set_pattern('user', $form),

							array(
								'type'     => 'callback',
								'dependency' => array("user_active_p|user_active", '!=|==', 'true|true'),
								'function' => [$this, 'print_tags_front'],
							),
							array(
								'id'    => 	 "user_active_conditional",
								'type'  => 'switcher',
								'title' => esc_html__('Conditional', 'payamito-gravity-form'),
								'dependency' => array("user_active", '==', 'true'),
							),
							array(
								'id'          => 'user_conditional_type',
								'type'        => 'select',
								'options'     => [
									'all' => esc_html__("All", "payamito-gravity-form"),
									'any' => esc_html__("Any", "payamito-gravity-form"),

								],
								'dependency' => array("user_active_conditional|user_active", '==|==', 'true|true'),
								'before' => __("Send SMS to user if", "payamito-gravity-form"),
								'after' => __('Conditions is true', "payamito-gravity-form"),
							),
							array(
								'id'     =>  'user_conditional_repeater',
								'type'   => 'repeater',
								'title'  => esc_html__("Admin conditional", "payamito-gravity-form"),
								'max' => '4',
								'class' => "payamito-gravity-form-repeater",
								'dependency' => array("user_active_conditional|user_active", '==|==', 'true|true'),
								'fields' => array(
									array(
										'id'          => 'field',
										'type'        => 'select',
										'options'     => $this->form_fields($form)
									),
									array(
										'id'    => 'operator',
										'type'  => 'select',
										'options' => [
											'is' => esc_html__("Is", "payamito-gravity-form"),
											'isnot' => esc_html__("Is not", "payamito-gravity-form"),
											'>' => esc_html__("Greater than", "payamito-gravity-form"),
											'<' => esc_html__("Less than", "payamito-gravity-form"),
											'contains' => esc_html__("Contains", "payamito-gravity-form"),
											'starts_with' => esc_html__("Starts with", "payamito-gravity-form"),
											'ends_with' => esc_html__("Ends with", "payamito-gravity-form"),
										]
									),
									array(
										'id'      => 'value',
										'type'    => 'text',
										'placeholder' => esc_html__("Value", "payamito-gravity-form"),
										'class' => 'payamito-gf-with',
									),
								),
							),
							array(
								'type'    => 'heading',
								'content' => esc_html__('Payment SMS', 'payamito-gravity-form'),
								'class' => 'pattern_background',
							),

							/////////////////////////////////////////////////////////////
							array(
								'id'    => 	"CoPay_active",
								'type'  => 'switcher',
								'title' => esc_html__('Completed', 'payamito-gravity-form'),
							),
							array(
								'id'    => "CoPayAdmin_active",
								'type'  => 'switcher',
								'title' => esc_html__('Send to admin', 'payamito-gravity-form'),
								'dependency' => array("CoPay_active", '==', 'true'),

							),
							array(
								'type'       => 'notice',
								'style'      => 'warning',
								'content'    => esc_html__('"notice" send pattern need to help', 'payamito-gravity-form'),
								'dependency' => array("CoPay_active|CoPayAdmin_active", '==|==', 'true|true'),
								'class' => 'pattern_background',
							),
							array(

								'id'    => "CoPayAdmin_active_pattern",
								'type'  => 'switcher',
								'title'      => payamito_dynamic_text('pattern_active_title'),
								'desc'       => payamito_dynamic_text('pattern_active_desc'),
								'help'       => payamito_dynamic_text('pattern_active_help'),
								'class' => 'pattern_background',
								'dependency' => array("CoPay_active|CoPayAdmin_active", '==|==', 'true|true'),

							),
							array(
								'id'   => 	"CoPayAdmin_pattern_id",
								'type'    => 'text',
								'title'      => payamito_dynamic_text('pattern_ID_title'),
								'desc'       => payamito_dynamic_text('pattern_ID_desc'),
								'help'       => payamito_dynamic_text('pattern_ID_help'),
								'class' => 'pattern_background',
								'dependency' => array("CoPay_active|CoPayAdmin_active|CoPayAdmin_active_pattern", '==|==|==', 'true|true|true'),
							),
							array(
								'id'     =>  'CoPayAdmin_pattern',
								'type'   => 'repeater',
								'title'      => payamito_dynamic_text('pattern_Variable_title'),
								'desc'       => payamito_dynamic_text('pattern_Variable_desc'),
								'help'       => payamito_dynamic_text('pattern_Variable_help'),
								'max' => '4',
								'class' => "payamito-gravity-form-repeater pattern_background",
								'dependency' => array("CoPay_active|CoPayAdmin_active|CoPayAdmin_active_pattern", '==|==|==', 'true|true|true'),
								'chosen'      => true,
								'fields' => array(
									array(
										'id'          => 0,
										'type'        => 'select',
										'placeholder' =>  esc_html__("Select tag", "payamito-gravity-form"),
										'options'     => $this->get_for_select_field($form),
									),
									array(
										'id'    => 1,
										'type'  => 'number',
										'placeholder' =>  esc_html__("Your tag", "payamito-gravity-form"),
										'default' => '0',
									),
								)
							),
							array(
								'id'   =>  "CoPayAdmin_txt",
								'title' => esc_html__('Admin message', 'payamito-gravity-form'),
								'default' => esc_html__('مشتری گرامی پرداخت شما با کد پیگیری {transaction_id} با موفقیت انجام شد.', 'payamito-gravity-form'),
								'class' => 'pattern_background',
								'type' => 'textarea',
								'dependency' => array("CoPay_active|CoPayAdmin_active_pattern|CoPayAdmin_active", '==|!=|==', 'true|true|true'),
							),

							array(
								'id'    => "CoPayUser_active",
								'type'  => 'switcher',
								'title' => esc_html__('Send to user', 'payamito-gravity-form'),
								'dependency' => array("CoPay_active", '==', 'true'),

							),
							array(
								'type'       => 'notice',
								'style'      => 'warning',
								'content'    => esc_html__('"notice" send pattern need to help', 'payamito-gravity-form'),
								'dependency' => array("CoPay_active|CoPayUser_active", '==|==', 'true|true'),
								'class' => 'pattern_background',
							),
							array(
								'id'    => "CoPayUser_active_pattern",
								'type'  => 'switcher',
								'title'      => payamito_dynamic_text('pattern_active_title'),
								'desc'       => payamito_dynamic_text('pattern_active_desc'),
								'help'       => payamito_dynamic_text('pattern_active_help'),
								'class' => 'pattern_background',
								'dependency' => array("CoPay_active|CoPayUser_active", '==|==', 'true|true'),

							),
							array(
								'id'   => 	"CoPayUser_pattern_id",
								'type'    => 'text',
								'title'      => payamito_dynamic_text('pattern_ID_title'),
								'desc'       => payamito_dynamic_text('pattern_ID_desc'),
								'help'       => payamito_dynamic_text('pattern_ID_help'),
								'class' => 'pattern_background',
								'dependency' => array("CoPay_active|CoPayUser_active_pattern|CoPayUser_active", '==|==|==', 'true|true|true'),
							),
							array(
								'id'     =>  'CoPayUser_pattern',
								'type'   => 'repeater',
								'title'      => payamito_dynamic_text('pattern_Variable_title'),
								'desc'       => payamito_dynamic_text('pattern_Variable_desc'),
								'help'       => payamito_dynamic_text('pattern_Variable_help'),
								'max' => '4',
								'class' => "payamito-gravity-form-repeater pattern_background",
								'dependency' => array("CoPay_active|CoPayUser_active_pattern|CoPayUser_active", '==|==|==', 'true|true|true'),
								'chosen'      => true,
								'fields' => array(
									array(
										'id'          => 0,
										'type'        => 'select',
										'placeholder' =>  esc_html__("Select tag", "payamito-gravity-form"),
										'class' => 'pattern_background',
										'options'     => $this->get_for_select_field($form),
									),
									array(
										'id'    => 1,
										'type'  => 'number',
										'placeholder' =>  esc_html__("Your tag", "payamito-gravity-form"),
										'class' => 'pattern_background',
										'default' => '0',
									),
								)
							),
							array(
								'id'   =>  "CoPayUser_txt",
								'title' => esc_html__('User message', 'payamito-gravity-form'),
								'default' => esc_html__('مشتری گرامی پرداخت شما با کد پیگیری {transaction_id} با موفقیت انجام شد.', 'payamito-gravity-form'),
								'class' => 'pattern_background',
								'type' => 'textarea',
								'dependency' => array("CoPay_active|CoPayUser_active_pattern|CoPayUser_active", '==|!=|==', 'true|true|true'),
							),


							///////////////////////////////////////////////////////////////////////////
							array(
								'type'    => 'subheading',
								'content' =>  esc_html__('Failed', 'payamito-gravity-form'),
							),
							array(
								'id'    => 	"FaPay_active",
								'type'  => 'switcher',
								'title' => esc_html__('Failed', 'payamito-gravity-form'),
							),
							array(
								'id'    => "FaPayAdmin_active",
								'type'  => 'switcher',
								'title' => esc_html__('Send to admin', 'payamito-gravity-form'),
								'dependency' => array("FaPay_active", '==', 'true'),

							),
							array(
								'type'       => 'notice',
								'style'      => 'warning',
								'content'    => esc_html__('"notice" send pattern need to help', 'payamito-gravity-form'),
								'dependency' => array("FaPay_active|FaPayAdmin_active", '==|==', 'true|true'),
								'class' => 'pattern_background',
							),
							array(

								'id'    => "FaPayAdmin_active_pattern",
								'type'  => 'switcher',
								'title'      => payamito_dynamic_text('pattern_active_title'),
								'desc'       => payamito_dynamic_text('pattern_active_desc'),
								'help'       => payamito_dynamic_text('pattern_active_help'),
								'class' => 'pattern_background',
								'dependency' => array("FaPay_active|FaPayAdmin_active", '==|==', 'true|true'),

							),
							array(
								'id'   => 	"FaPayAdmin_pattern_id",
								'type'    => 'text',
								'title'      => payamito_dynamic_text('pattern_ID_title'),
								'desc'       => payamito_dynamic_text('pattern_ID_desc'),
								'help'       => payamito_dynamic_text('pattern_ID_help'),
								'class' => 'pattern_background',
								'dependency' => array("FaPay_active|FaPayAdmin_active|FaPayAdmin_active_pattern", '==|==|==', 'true|true|true'),
							),
							array(
								'id'     =>  'FaPayAdmin_pattern',
								'type'   => 'repeater',
								'title'      => payamito_dynamic_text('pattern_Variable_title'),
								'desc'       => payamito_dynamic_text('pattern_Variable_desc'),
								'help'       => payamito_dynamic_text('pattern_Variable_help'),
								'max' => '4',
								'class' => "payamito-gravity-form-repeater pattern_background",
								'dependency' => array("FaPay_active|FaPayAdmin_active|FaPayAdmin_active_pattern", '==|==|==', 'true|true|true'),
								'chosen'      => true,
								'fields' => array(
									array(
										'id'          => 0,
										'type'        => 'select',
										'placeholder' =>  esc_html__("Select tag", "payamito-gravity-form"),
										'class' => 'pattern_background',
										'options'     => $this->get_for_select_field($form),
									),
									array(
										'id'    => 1,
										'type'  => 'number',
										'placeholder' =>  esc_html__("Your tag", "payamito-gravity-form"),
										'class' => 'pattern_background',
										'default' => '0',
									),
								)
							),
							array(
								'id'   =>  "FaPayAdmin_txt",
								'title' => esc_html__('Admin message', 'payamito-gravity-form'),
								'default' => esc_html__('مشتری گرامی پرداخت شما با کد پیگیری {transaction_id} با موفقیت انجام شد.', 'payamito-gravity-form'),
								'class' => 'pattern_background',
								'type' => 'textarea',
								'dependency' => array("FaPay_active|FaPayAdmin_active_pattern|FaPayAdmin_active", '==|!=|==', 'true|true|true'),
							),

							array(
								'id'    => "FaPayUser_active",
								'type'  => 'switcher',
								'title' => esc_html__('Send to user', 'payamito-gravity-form'),
								'dependency' => array("FaPay_active", '==', 'true'),

							),
							array(
								'type'       => 'notice',
								'style'      => 'warning',
								'content'    => esc_html__('"notice" send pattern need to help', 'payamito-gravity-form'),
								'dependency' => array("FaPay_active|FaPayUser_active", '==|==', 'true|true'),
								'class' => 'pattern_background',
							),
							array(
								'id'    => "FaPayUser_active_pattern",
								'type'  => 'switcher',
								'title'      => payamito_dynamic_text('pattern_active_title'),
								'desc'       => payamito_dynamic_text('pattern_active_desc'),
								'help'       => payamito_dynamic_text('pattern_active_help'),
								'class' => 'pattern_background',
								'dependency' => array("FaPay_active|FaPayUser_active", '==|==', 'true|true'),

							),
							array(
								'id'   => 	"FaPayUser_pattern_id",
								'type'    => 'text',
								'title'      => payamito_dynamic_text('pattern_ID_title'),
								'desc'       => payamito_dynamic_text('pattern_ID_desc'),
								'help'       => payamito_dynamic_text('pattern_ID_help'),
								'class' => 'pattern_background',
								'dependency' => array("FaPay_active|FaPayUser_active_pattern|FaPayUser_active", '==|==|==', 'true|true|true'),
							),
							array(
								'id'     =>  'FaPayUser_pattern',
								'type'   => 'repeater',
								'title'      => payamito_dynamic_text('pattern_Variable_title'),
								'desc'       => payamito_dynamic_text('pattern_Variable_desc'),
								'help'       => payamito_dynamic_text('pattern_Variable_help'),
								'max' => '4',
								'class' => "payamito-gravity-form-repeater pattern_background",
								'dependency' => array("FaPay_active|FaPayUser_active_pattern|FaPayUser_active", '==|==|==', 'true|true|true'),
								'chosen'      => true,
								'fields' => array(
									array(
										'id'          => 0,
										'type'        => 'select',
										'placeholder' =>  esc_html__("Select tag", "payamito-gravity-form"),
										'class' => 'pattern_background',
										'options'     => $this->get_for_select_field($form),
									),
									array(
										'id'    => 1,
										'type'  => 'number',
										'placeholder' =>  esc_html__("Your tag", "payamito-gravity-form"),
										'class' => 'pattern_background',
										'default' => '0',
									),
								)
							),
							array(
								'id'   =>  "FaPayUser_txt",
								'title' => esc_html__('User message', 'payamito-gravity-form'),
								'default' => esc_html__('مشتری گرامی پرداخت شما با کد پیگیری {transaction_id} با موفقیت انجام شد.', 'payamito-gravity-form'),
								'class' => 'pattern_background',
								'type' => 'textarea',
								'dependency' => array("FaPay_active|FaPayUser_active_pattern|FaPayUser_active", '==|!=|==', 'true|true|true'),
							),

							////////////////////////////////////////////////////////////////////////////
							array(
								'type'    => 'subheading',
								'content' =>  esc_html__('Cancell', 'payamito-gravity-form'),
							),
							array(
								'id'    => 	"CanPay_active",
								'type'  => 'switcher',
								'title' => esc_html__('Cancelled', 'payamito-gravity-form'),
							),
							array(
								'id'    => "CanPayAdmin_active",
								'type'  => 'switcher',
								'title' => esc_html__('Send to admin', 'payamito-gravity-form'),
								'dependency' => array("CanPay_active", '==', 'true'),

							),
							array(
								'type'       => 'notice',
								'style'      => 'warning',
								'content'    => esc_html__('"notice" send pattern need to help', 'payamito-gravity-form'),
								'dependency' => array("CanPay_active|CanPayAdmin_active", '==|==', 'true|true'),
								'class' => 'pattern_background',
							),
							array(

								'id'    => "CanPayAdmin_active_pattern",
								'type'  => 'switcher',
								'title'      => payamito_dynamic_text('pattern_active_title'),
								'desc'       => payamito_dynamic_text('pattern_active_desc'),
								'help'       => payamito_dynamic_text('pattern_active_help'),
								'class' => 'pattern_background',
								'dependency' => array("CanPay_active|CanPayAdmin_active", '==|==', 'true|true'),

							),
							array(
								'id'   => 	"CanPayAdmin_pattern_id",
								'type'    => 'text',
								'title'      => payamito_dynamic_text('pattern_ID_title'),
								'desc'       => payamito_dynamic_text('pattern_ID_desc'),
								'help'       => payamito_dynamic_text('pattern_ID_help'),
								'class' => 'pattern_background',
								'dependency' => array("CanPay_active|CanPayAdmin_active|CanPayAdmin_active_pattern", '==|==|==', 'true|true|true'),
							),
							array(
								'id'     =>  'CanPayAdmin_pattern',
								'type'   => 'repeater',
								'title'      => payamito_dynamic_text('pattern_Variable_title'),
								'desc'       => payamito_dynamic_text('pattern_Variable_desc'),
								'help'       => payamito_dynamic_text('pattern_Variable_help'),
								'max' => '4',
								'class' => "payamito-gravity-form-repeater pattern_background",
								'dependency' => array("CanPay_active|CanPayAdmin_active|CanPayAdmin_active_pattern", '==|==|==', 'true|true|true'),
								'chosen'      => true,
								'fields' => array(
									array(
										'id'          => 0,
										'type'        => 'select',
										'placeholder' =>  esc_html__("Select tag", "payamito-gravity-form"),
										'class' => 'pattern_background',
										'options'     => $this->get_for_select_field($form),
									),
									array(
										'id'    => 1,
										'type'  => 'number',
										'placeholder' =>  esc_html__("Your tag", "payamito-gravity-form"),
										'class' => 'pattern_background',
										'default' => '0',
									),
								)
							),
							array(
								'id'   =>  "CanPayAdmin_txt",
								'title' => esc_html__('Admin message', 'payamito-gravity-form'),
								'default' => esc_html__('مشتری گرامی پرداخت شما با کد پیگیری {transaction_id} با موفقیت انجام شد.', 'payamito-gravity-form'),
								'class' => 'pattern_background',
								'type' => 'textarea',
								'dependency' => array("CanPay_active|CanPayAdmin_active_pattern|CanPayAdmin_active", '==|!=|==', 'true|true|true'),
							),

							array(
								'id'    => "CanPayUser_active",
								'type'  => 'switcher',
								'title' => esc_html__('Send to user', 'payamito-gravity-form'),
								'dependency' => array("CanPay_active", '==', 'true'),

							),
							array(
								'type'       => 'notice',
								'style'      => 'warning',
								'content'    => esc_html__('"notice" send pattern need to help', 'payamito-gravity-form'),
								'dependency' => array("CanPay_active|CanPayUser_active", '==|==', 'true|true'),
								'class' => 'pattern_background',
							),
							array(
								'id'    => "CanPayUser_active_pattern",
								'type'  => 'switcher',
								'title'      => payamito_dynamic_text('pattern_active_title'),
								'desc'       => payamito_dynamic_text('pattern_active_desc'),
								'help'       => payamito_dynamic_text('pattern_active_help'),
								'class' => 'pattern_background',
								'dependency' => array("CanPay_active|CanPayUser_active", '==|==', 'true|true'),

							),
							array(
								'id'   => 	"CanPayUser_pattern_id",
								'type'    => 'text',
								'title'      => payamito_dynamic_text('pattern_ID_title'),
								'desc'       => payamito_dynamic_text('pattern_ID_desc'),
								'help'       => payamito_dynamic_text('pattern_ID_help'),
								'class' => 'pattern_background',
								'dependency' => array("CanPay_active|CanPayUser_active_pattern|CanPayUser_active", '==|==|==', 'true|true|true'),
							),
							array(
								'id'     =>  'CanPayUser_pattern',
								'type'   => 'repeater',
								'title'      => payamito_dynamic_text('pattern_Variable_title'),
								'desc'       => payamito_dynamic_text('pattern_Variable_desc'),
								'help'       => payamito_dynamic_text('pattern_Variable_help'),
								'max' => '4',
								'class' => "payamito-gravity-form-repeater pattern_background",
								'dependency' => array("CanPay_active|CanPayUser_active_pattern|CanPayUser_active", '==|==|==', 'true|true|true'),
								'chosen'      => true,
								'fields' => array(
									array(
										'id'          => 0,
										'type'        => 'select',
										'placeholder' =>  esc_html__("Select tag", "payamito-gravity-form"),
										'class' => 'pattern_background',
										'options'     => $this->get_for_select_field($form),
									),
									array(
										'id'    => 1,
										'type'  => 'number',
										'placeholder' =>  esc_html__("Your tag", "payamito-gravity-form"),
										'class' => 'pattern_background',
										'default' => '0',
									),
								)
							),
							array(
								'id'   =>  "CanPayUser_txt",
								'title' => esc_html__('User message', 'payamito-gravity-form'),
								'default' => esc_html__('مشتری گرامی پرداخت شما با کد پیگیری {transaction_id} با موفقیت انجام شد.', 'payamito-gravity-form'),
								'class' => 'pattern_background',
								'type' => 'textarea',
								'dependency' => array("CanPay_active|CanPayUser_active_pattern|CanPayUser_active", '==|!=|==', 'true|true|true'),
							),
						),
					),
				),
			);
		}

		public	function print_tags_front()
		{
			echo "<h3 class='payamito-tags payamito-gravity-form-modal' >" . esc_html__('Tags', 'payamito-gravity-form') . "</h3>";
		}
	}
