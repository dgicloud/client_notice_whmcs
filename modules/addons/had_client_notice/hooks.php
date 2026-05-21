<?php

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

if (defined('HCN_POPUP_HOOK_REGISTERED')) {
    return;
}

define('HCN_POPUP_HOOK_REGISTERED', true);

require_once __DIR__ . '/had_client_notice.php';

add_hook('ClientAreaFooterOutput', 1, function ($vars) {
    if (!function_exists('hcn_client_footer_output')) {
        return '';
    }

    return hcn_client_footer_output($vars);
});
