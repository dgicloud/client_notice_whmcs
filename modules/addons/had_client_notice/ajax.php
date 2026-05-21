<?php

require_once dirname(__DIR__, 3) . '/init.php';
require_once __DIR__ . '/had_client_notice.php';

use WHMCS\Database\Capsule;

header('Content-Type: application/json; charset=utf-8');

function hcn_ajax_response($ok, $message, array $extra = [])
{
    echo json_encode(array_merge([
        'ok' => (bool) $ok,
        'message' => $message,
    ], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    hcn_ajax_response(false, 'Método não permitido.');
}

if (!hcn_table_exists('mod_had_client_notice_notices') || !hcn_table_exists('mod_had_client_notice_acceptances')) {
    hcn_ajax_response(false, 'Tabelas do módulo não encontradas.');
}

$userid = !empty($_SESSION['uid']) ? (int) $_SESSION['uid'] : 0;
$noticeId = (int) ($_POST['notice_id'] ?? 0);

if ($noticeId <= 0) {
    hcn_ajax_response(false, 'Aviso inválido.');
}

try {
    $notice = Capsule::table('mod_had_client_notice_notices')
        ->where('id', $noticeId)
        ->where('enabled', 1)
        ->first();

    if (!$notice) {
        hcn_ajax_response(false, 'Aviso não encontrado ou inativo.');
    }

    if ((int) $notice->only_logged === 1 && !$userid) {
        hcn_ajax_response(false, 'Cliente não autenticado.');
    }

    if ($userid) {
        $exists = Capsule::table('mod_had_client_notice_acceptances')
            ->where('notice_id', $noticeId)
            ->where('userid', $userid)
            ->where('notice_key', (string) $notice->notice_key)
            ->exists();

        if (!$exists) {
            Capsule::table('mod_had_client_notice_acceptances')->insert([
                'notice_id' => $noticeId,
                'userid' => $userid,
                'notice_key' => (string) $notice->notice_key,
                'title_snapshot' => (string) $notice->title,
                'message_hash' => hash('sha256', (string) $notice->message),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'accepted_at' => hcn_now(),
            ]);
        }
    }

    hcn_ajax_response(true, 'Aceite registrado.');
} catch (Exception $e) {
    hcn_ajax_response(false, 'Erro ao registrar aceite.');
}
