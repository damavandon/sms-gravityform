<?php

/**
 * PayamitoGravityForm setup
 *
 * @package Payamito
 * @since   1.0.0
 */

// don't call the file directly
if (!defined('ABSPATH')) {

	die('direct access abort ');
}

final class PayamitoGravityForm
{

	/**
	 * PayamitoGravityForm version.
	 *
	 * @var string
	 */
	public $version = PAYAMITO_GF_VER ;

	/**
	 * Core  version.
	 *
	 * @var string
	 */
	public $core_version = '2.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @var PayamitoGravityForm
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Form instance.
	 *
	 * @var object
	 */
	public $form;

	/**
	 * Send instance.
	 *
	 * @var object
	 */
	public $send;

	/**
	 * Submit instance.
	 *
	 * @var object
	 */
	public $submit;

	/**
	 * Plugin slag.
	 *
	 * @var string
	 */
	public static $slug = 'payamito_gf';

	/**
	 * Main PayamitoGravityForm Instance.
	 *
	 * Ensures only one instance of PayamitoGravityForm is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see payamito_gf()
	 * @return PayamitoGravityForm - Main instance.
	 */
	public static function get_instance()
	{
		if (is_null(self::$_instance)) {

			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone()
	{
		_doing_it_wrong(__FUNCTION__, __('Cloning is forbidden.', 'payamito-gravity-form'), '1.0.0');
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup()
	{
		_doing_it_wrong(__FUNCTION__, __('Unserializing instances of this class is forbidden.', 'payamito-gravity-form'), '1.0.0');
	}

	public function __construct()
	{
		$this->includes();
		if (!_payamito_gf_is_installed())return _payamito_gf_no_intalled_gravity_form();
		$this->init_hooks();
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes()
	{
		require_once PAYAMITO_GF_DIR . '/inc/functions.php';
		require_once PAYAMITO_GF_DIR . '/inc/class-updater.php';
		require_once PAYAMITO_GF_DIR . '/inc/class-functions.php';
		require_once PAYAMITO_GF_DIR . '/inc/class-send.php';
		require_once PAYAMITO_GF_DIR . '/inc/admin/class-settings.php';
		require_once PAYAMITO_GF_DIR . '/inc/class-verification.php';
		require_once PAYAMITO_GF_DIR . '/inc/class-submit-form.php';
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */

	public function init_hooks()
	{
		$this->load_core();
		add_action('gform_loaded', [$this, 'init']);
		add_action("admin_init",["PGF_Updater","init"]);
	}
	public function load_core()
	{
		$path = payamito_gf_load_core() . '/payamito.php';
		require_once $path;
	}

	public function init()
	{

		PGF_Verification::get_instance();
		Payamito\GravityForm\Settings::get_instance();

		$this->submit = Payamito\GravityForm\Submit::get_instance();
		$this->submit->init_hooks();

		$this->get_options();
	}


	private function get_options()
	{
		global $payamito_gf_options;
		$payamito_gf_options = get_option('payamito_gf');
	}
}
