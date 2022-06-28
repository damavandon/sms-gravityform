<?php


defined('ABSPATH') || exit;

if (!class_exists('PGF_Send')) {

	class PGF_Send
	{
		private static $_instance = null;

		public $OTP;

		public $phone_number;

		public static function get_instance()
		{
			if (is_null(self::$_instance)) {

				self::$_instance = new self();
			}
			return self::$_instance;
		}
		/**
		 * Respanse gateway message
		 * 	 * @param $r param
		 */
		public  function Message($r)
		{
			if ($r === true) {
				return __('success', 'payamito-gravity-form');
			}
			$r = intval($r);

			$messages = array(
				12 => "مدارک کاربر کامل نمی باشد",
				11 => ".ارسال نشده",
				10 => "کاربرمورد نظرفعال نمی باشد.",
				7 => "متن حاوی کلمه فیلتر شده می باشد، با واحد اداری تماس بگیرید",
				6 => "سامانه در حال بروزرسانی می باشد.",
				5 => "شماره فرستنده معتبرنمی باشد",
				4 => "محدودیت در حجم ارسال",
				3 => "حدودیت در ارسال روزانه",
				2 => ":اعتبار کافی نمی باشد",
				1 => "درخواست با موفقیت انجام شد",
				0 => "نام کاربری یا رمز عبور صحیح نمی باشد",
				-1 => "دسترسی برای استفاده از این وبسرویس غیرفعال است، با پشتیبانی تماس بگیرید.",
				-2 => "محدودیت تعداد شماره، محدودیت هر بار ارسال 1 شماره موبایل می باشد",
				-3 => "خط ارسالی در سیستم تعریف نشده است، با پشتیبانی سامانه تماس بگیرید.",
				-4 => "کد متن ارسالی صحیح نمی باشد و یا توسط مدیر سامانه تایید نشده است.",
				-5 => "تن ارسالی با توجه به متغیر های مشخص شده در متن پیشفرض همخوانی ندارد",
				-6 => "خطای داخلی رخ داده است با پشتیبانی تماس بگیرید",
				-7 => "خطایی در شماره فرستنده رخ داده است با پشتیبانی تماس بگیرید",
				-100, 'حساب شما امکان ارسال بدون الگو  را ندارد'
			);

			foreach ($messages as $index => $m) {
				if ($index == $r) {
					return $m;
				}
			}
			return __("Not Fount Message", "payamito-gravity-form");
		}
		/**
		 * get gateway name
		 * 	 * @param $r param
		 */
		public function getName()
		{
			return "Payamito";
		}

		/**
		 * Send sms
		 */
		public function Send_pattern($toNum, $messageContent, $pattern_id)
		{

			$result = payamito_send_pattern($toNum, $messageContent, $pattern_id, payamito_gf()::$slug);

			if ($result > 2000) {

				$this->success = true;

				$result = true;
			} else {
				$result = json_decode($result);
			}

			return array("result" => $result, "message" => $this->Message($result));
		}
		
		public  function Send($toNum, $message)
		{
			
			$result = payamito_send(array($toNum), $message, payamito_gf()::$slug);
			$result = json_decode($result);
			if ($result >10000) {
				$result = true;
			}

			return array("result" => $result, "message" => $this->Message($result));
		}
	}
}
