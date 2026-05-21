<?php

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * Bootstrap tradicional do hook do módulo HAD Avisos ao Cliente.
 *
 * Este arquivo existe para compatibilidade com instalações WHMCS que carregam
 * hooks principalmente pela pasta /includes/hooks.
 *
 * Ele NÃO depende da versão 1 do módulo. Apenas carrega o hooks.php da própria
 * versão 2, localizado em /modules/addons/had_client_notice/hooks.php.
 */

$moduleHook = ROOTDIR . '/modules/addons/had_client_notice/hooks.php';

if (is_readable($moduleHook)) {
    require_once $moduleHook;
}
