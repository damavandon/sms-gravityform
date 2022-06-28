<?php
if (!class_exists('PGF_Updater')) {
    class PGF_Updater
    {
        public static function init()
        {
            if (!class_exists("Puc_v4_Factory")) {
                include_once PAYAMITO_GF_DIR . '/inc/lib/plugin-update-checker-master/plugin-update-checker.php';
            }
            self::update_cheker();
        }

        public static function update_cheker()
        {
            
            $server = 'http://updater.payamito.com/?action=download&slug=payamito-sms-gravity-form';
            $bootstrap_path = PAYAMITO_GF_PLUGIN_FILE;
            $slug = 'payamito-sms-gravity-form';

            try {
                Puc_v4_Factory::buildUpdateChecker($server, $bootstrap_path, $slug);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}
