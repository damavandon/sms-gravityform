<?php

// don't call the file directly
if (!defined('ABSPATH')) {

    die('direct access abort ');
}

if (!defined('PAYAMITO_GF_BASENAME')) {

    defined('PAYAMITO_GF_BASENAME') || define('PAYAMITO_GF_BASENAME',__DIR__);
}
if (!defined('PAYAMITO_GF_DIR')) {

    define('PAYAMITO_GF_DIR', PAYAMITO_GF_BASENAME);
}
if (!defined('PAYAMITO_GF_COR_DIR')) {

    define('PAYAMITO_GF_COR_DIR', PAYAMITO_GF_DIR.'/inc/core/payamito-core');
}
if (!defined('PAYAMITO_GF_URL')) {

    define('PAYAMITO_GF_URL',  plugin_dir_url( __FILE__));
}
if (!defined('PAYAMITO_GF_VER')) {

    define('PAYAMITO_GF_VER', '1.2.2');
}
if (!defined('PAYAMITO_GF_CORE_VER')) {
    define('PAYAMITO_GF_CORE_VER', '2.0.0');
}
