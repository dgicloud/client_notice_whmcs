<?php

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

use WHMCS\Database\Capsule;

if (!function_exists('hcn_e')) {
    function hcn_e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('hcn_now')) {
    function hcn_now()
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('hcn_date_today')) {
    function hcn_date_today()
    {
        return date('Y-m-d');
    }
}

if (!function_exists('hcn_csv_to_array')) {
    function hcn_csv_to_array($value)
    {
        if (is_array($value)) {
            $parts = $value;
        } else {
            $parts = preg_split('/[,;\s]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        $clean = [];
        foreach ($parts as $part) {
            $part = trim((string) $part);
            if ($part !== '') {
                $clean[] = $part;
            }
        }

        return array_values(array_unique($clean));
    }
}

if (!function_exists('hcn_csv_to_int_array')) {
    function hcn_csv_to_int_array($value)
    {
        $items = hcn_csv_to_array($value);
        $ids = [];

        foreach ($items as $item) {
            $id = (int) $item;
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }
}

if (!function_exists('hcn_array_to_csv')) {
    function hcn_array_to_csv($value)
    {
        if (!is_array($value)) {
            return trim((string) $value);
        }

        $clean = [];
        foreach ($value as $item) {
            $item = trim((string) $item);
            if ($item !== '') {
                $clean[] = $item;
            }
        }

        return implode(',', array_values(array_unique($clean)));
    }
}

if (!function_exists('hcn_post_bool')) {
    function hcn_post_bool($name)
    {
        return isset($_POST[$name]) ? 1 : 0;
    }
}

if (!function_exists('hcn_clean_notice_key')) {
    function hcn_clean_notice_key($value)
    {
        $value = trim((string) $value);
        $value = preg_replace('/[^a-zA-Z0-9_\-]/', '', $value);

        if ($value === '') {
            $value = 'aviso-' . time();
        }

        return $value;
    }
}

if (!function_exists('hcn_clean_hex_color')) {
    function hcn_clean_hex_color($value, $default)
    {
        $value = trim((string) $value);
        if (preg_match('/^#[0-9a-fA-F]{6}$/', $value)) {
            return $value;
        }

        return $default;
    }
}

if (!function_exists('hcn_clean_int_range')) {
    function hcn_clean_int_range($value, $default, $min, $max)
    {
        $value = (int) $value;
        if ($value < $min || $value > $max) {
            return $default;
        }

        return $value;
    }
}

if (!function_exists('hcn_input_to_db_datetime')) {
    function hcn_input_to_db_datetime($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $timestamp = strtotime(str_replace('T', ' ', $value));
        if (!$timestamp) {
            return null;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }
}

if (!function_exists('hcn_db_to_datetime_input')) {
    function hcn_db_to_datetime_input($value)
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '0000-00-00 00:00:00') {
            return '';
        }

        $timestamp = strtotime($value);
        if (!$timestamp) {
            return '';
        }

        return date('Y-m-d\TH:i', $timestamp);
    }
}

if (!function_exists('hcn_sanitize_html')) {
    function hcn_sanitize_html($html)
    {
        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><a><span><h3><h4><blockquote>';
        $html = strip_tags((string) $html, $allowedTags);
        $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/\sstyle\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/href\s*=\s*("|\')\s*javascript:[^"\']*("|\')/i', 'href="#"', $html);
        $html = preg_replace('/<a\s+/i', '<a target="_blank" rel="noopener noreferrer" ', $html);

        return $html;
    }
}

if (!function_exists('hcn_table_exists')) {
    function hcn_table_exists($table)
    {
        try {
            return Capsule::schema()->hasTable($table);
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('hcn_get_default_notice_data')) {
    function hcn_get_default_notice_data()
    {
        return [
            'enabled' => 1,
            'title' => 'Aviso importante sobre backup',
            'notice_key' => 'backup-v1',
            'message' => "Os serviços de Cloud Server, VPS e máquinas virtuais da HAD Cloud não incluem backup automático dos dados, arquivos, sistemas, bancos de dados ou ambientes do cliente, salvo quando houver contratação específica de uma solução de backup.\n\nA responsabilidade pela criação, validação e manutenção das rotinas de backup é do cliente.\n\nPara maior segurança, recomendamos a contratação do HAD Vault ou a utilização de uma estratégia própria de backup.",
            'content_format' => 'text',
            'display_type' => 'modal',
            'icon' => 'warning',
            'primary_button_text' => 'Li e estou ciente',
            'secondary_button_text' => 'Conhecer backup',
            'secondary_button_url' => 'https://seudominio.com.br/backup',
            'show_once' => 1,
            'only_logged' => 1,
            'is_mandatory' => 1,
            'requires_checkbox' => 1,
            'checkbox_text' => 'Declaro que li e estou ciente deste aviso.',
            'start_at' => null,
            'end_at' => null,
            'product_rule' => 'all',
            'target_product_ids' => '',
            'backup_product_ids' => '',
            'service_statuses' => 'Active',
            'invoice_rule' => 'any',
            'target_client_group_ids' => '',
            'target_client_statuses' => 'Active',
            'header_bg' => '#4b1d95',
            'primary_color' => '#4b1d95',
            'secondary_color' => '#f97316',
            'text_color' => '#222222',
            'width' => 580,
            'sort_order' => 0,
            'created_at' => hcn_now(),
            'updated_at' => hcn_now(),
        ];
    }
}

if (!function_exists('had_client_notice_config')) {
function had_client_notice_config()
{
    return [
        'name' => 'HAD Avisos ao Cliente',
        'description' => 'Central de avisos customizáveis para clientes logados no WHMCS, com segmentação, agendamento e registro de aceite.',
        'version' => '2.1.0',
        'author' => 'HAD Cloud',
        'fields' => [],
    ];
}}


if (!function_exists('had_client_notice_activate')) {
function had_client_notice_activate()
{
    try {
        if (!Capsule::schema()->hasTable('mod_had_client_notice_notices')) {
            Capsule::schema()->create('mod_had_client_notice_notices', function ($table) {
                $table->increments('id');
                $table->boolean('enabled')->default(1);
                $table->string('title', 255);
                $table->string('notice_key', 120);
                $table->longText('message')->nullable();
                $table->string('content_format', 20)->default('text');
                $table->string('display_type', 40)->default('modal');
                $table->string('icon', 40)->default('warning');
                $table->string('primary_button_text', 100)->default('Entendi');
                $table->string('secondary_button_text', 100)->nullable();
                $table->text('secondary_button_url')->nullable();
                $table->boolean('show_once')->default(1);
                $table->boolean('only_logged')->default(1);
                $table->boolean('is_mandatory')->default(0);
                $table->boolean('requires_checkbox')->default(0);
                $table->string('checkbox_text', 255)->nullable();
                $table->dateTime('start_at')->nullable();
                $table->dateTime('end_at')->nullable();
                $table->string('product_rule', 60)->default('all');
                $table->text('target_product_ids')->nullable();
                $table->text('backup_product_ids')->nullable();
                $table->string('service_statuses', 255)->nullable();
                $table->string('invoice_rule', 60)->default('any');
                $table->text('target_client_group_ids')->nullable();
                $table->string('target_client_statuses', 255)->nullable();
                $table->string('header_bg', 20)->default('#4b1d95');
                $table->string('primary_color', 20)->default('#4b1d95');
                $table->string('secondary_color', 20)->default('#f97316');
                $table->string('text_color', 20)->default('#222222');
                $table->integer('width')->default(580);
                $table->integer('sort_order')->default(0);
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->index(['enabled']);
                $table->index(['notice_key']);
            });
        }

        if (!Capsule::schema()->hasTable('mod_had_client_notice_acceptances')) {
            Capsule::schema()->create('mod_had_client_notice_acceptances', function ($table) {
                $table->increments('id');
                $table->integer('notice_id')->unsigned();
                $table->integer('userid')->unsigned()->nullable();
                $table->string('notice_key', 120)->nullable();
                $table->string('title_snapshot', 255)->nullable();
                $table->char('message_hash', 64)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->dateTime('accepted_at');
                $table->timestamp('created_at')->useCurrent();
                $table->index(['notice_id', 'userid']);
                $table->index(['userid']);
            });
        }

        $hasNotice = Capsule::table('mod_had_client_notice_notices')->exists();
        if (!$hasNotice) {
            Capsule::table('mod_had_client_notice_notices')->insert(hcn_get_default_notice_data());
        }

        return [
            'status' => 'success',
            'description' => 'Módulo HAD Avisos ao Cliente ativado com sucesso.',
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'description' => 'Erro ao ativar o módulo: ' . $e->getMessage(),
        ];
    }
}}


if (!function_exists('had_client_notice_deactivate')) {
function had_client_notice_deactivate()
{
    return [
        'status' => 'success',
        'description' => 'Módulo desativado. As tabelas foram mantidas para preservar avisos e aceites.',
    ];
}}


if (!function_exists('hcn_get_products')) {
    function hcn_get_products()
    {
        try {
            return Capsule::table('tblproducts as p')
                ->leftJoin('tblproductgroups as g', 'g.id', '=', 'p.gid')
                ->select('p.id', 'p.name', 'p.hidden', 'g.name as group_name')
                ->orderBy('g.order', 'asc')
                ->orderBy('p.order', 'asc')
                ->orderBy('p.name', 'asc')
                ->get();
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('hcn_get_client_groups')) {
    function hcn_get_client_groups()
    {
        try {
            return Capsule::table('tblclientgroups')
                ->select('id', 'groupname')
                ->orderBy('groupname', 'asc')
                ->get();
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('hcn_render_checkbox_group')) {
    function hcn_render_checkbox_group($name, $items, $selected, $valueField, $labelCallback)
    {
        $selected = array_map('strval', hcn_csv_to_array($selected));
        $html = '<div class="hcn-checkbox-grid">';

        foreach ($items as $item) {
            $value = (string) $item->{$valueField};
            $checked = in_array($value, $selected, true) ? 'checked' : '';
            $label = call_user_func($labelCallback, $item);

            $html .= '<label class="hcn-check-item">';
            $html .= '<input type="checkbox" name="' . hcn_e($name) . '[]" value="' . hcn_e($value) . '" ' . $checked . '> ';
            $html .= $label;
            $html .= '</label>';
        }

        $html .= '</div>';

        return $html;
    }
}

if (!function_exists('hcn_get_notice_by_id')) {
    function hcn_get_notice_by_id($id)
    {
        if (!$id) {
            return null;
        }

        try {
            return Capsule::table('mod_had_client_notice_notices')->where('id', (int) $id)->first();
        } catch (Exception $e) {
            return null;
        }
    }
}

if (!function_exists('hcn_notice_data_from_post')) {
    function hcn_notice_data_from_post()
    {
        $allowedDisplayTypes = ['modal', 'banner_top', 'banner_bottom', 'toast', 'inline_panel'];
        $allowedProductRules = ['all', 'has_any', 'has_none', 'has_any_and_missing_backup'];
        $allowedInvoiceRules = ['any', 'overdue'];
        $allowedFormats = ['text', 'html'];
        $allowedIcons = ['none', 'warning', 'info', 'success', 'backup', 'maintenance'];

        $displayType = $_POST['display_type'] ?? 'modal';
        if (!in_array($displayType, $allowedDisplayTypes, true)) {
            $displayType = 'modal';
        }

        $productRule = $_POST['product_rule'] ?? 'all';
        if (!in_array($productRule, $allowedProductRules, true)) {
            $productRule = 'all';
        }

        $invoiceRule = $_POST['invoice_rule'] ?? 'any';
        if (!in_array($invoiceRule, $allowedInvoiceRules, true)) {
            $invoiceRule = 'any';
        }

        $contentFormat = $_POST['content_format'] ?? 'text';
        if (!in_array($contentFormat, $allowedFormats, true)) {
            $contentFormat = 'text';
        }

        $icon = $_POST['icon'] ?? 'warning';
        if (!in_array($icon, $allowedIcons, true)) {
            $icon = 'warning';
        }

        $title = trim((string) ($_POST['title'] ?? ''));
        if ($title === '') {
            $title = 'Aviso importante';
        }

        $primaryButtonText = trim((string) ($_POST['primary_button_text'] ?? ''));
        if ($primaryButtonText === '') {
            $primaryButtonText = 'Entendi';
        }

        $checkboxText = trim((string) ($_POST['checkbox_text'] ?? ''));
        if ($checkboxText === '') {
            $checkboxText = 'Declaro que li e estou ciente deste aviso.';
        }

        return [
            'enabled' => hcn_post_bool('enabled'),
            'title' => $title,
            'notice_key' => hcn_clean_notice_key($_POST['notice_key'] ?? ''),
            'message' => (string) ($_POST['message'] ?? ''),
            'content_format' => $contentFormat,
            'display_type' => $displayType,
            'icon' => $icon,
            'primary_button_text' => $primaryButtonText,
            'secondary_button_text' => trim((string) ($_POST['secondary_button_text'] ?? '')),
            'secondary_button_url' => trim((string) ($_POST['secondary_button_url'] ?? '')),
            'show_once' => hcn_post_bool('show_once'),
            'only_logged' => hcn_post_bool('only_logged'),
            'is_mandatory' => hcn_post_bool('is_mandatory'),
            'requires_checkbox' => hcn_post_bool('requires_checkbox'),
            'checkbox_text' => $checkboxText,
            'start_at' => hcn_input_to_db_datetime($_POST['start_at'] ?? ''),
            'end_at' => hcn_input_to_db_datetime($_POST['end_at'] ?? ''),
            'product_rule' => $productRule,
            'target_product_ids' => hcn_array_to_csv($_POST['target_product_ids'] ?? []),
            'backup_product_ids' => hcn_array_to_csv($_POST['backup_product_ids'] ?? []),
            'service_statuses' => hcn_array_to_csv($_POST['service_statuses'] ?? []),
            'invoice_rule' => $invoiceRule,
            'target_client_group_ids' => hcn_array_to_csv($_POST['target_client_group_ids'] ?? []),
            'target_client_statuses' => hcn_array_to_csv($_POST['target_client_statuses'] ?? []),
            'header_bg' => hcn_clean_hex_color($_POST['header_bg'] ?? '', '#4b1d95'),
            'primary_color' => hcn_clean_hex_color($_POST['primary_color'] ?? '', '#4b1d95'),
            'secondary_color' => hcn_clean_hex_color($_POST['secondary_color'] ?? '', '#f97316'),
            'text_color' => hcn_clean_hex_color($_POST['text_color'] ?? '', '#222222'),
            'width' => hcn_clean_int_range($_POST['width'] ?? 580, 580, 320, 1200),
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'updated_at' => hcn_now(),
        ];
    }
}

if (!function_exists('hcn_save_notice')) {
    function hcn_save_notice($id = 0)
    {
        $data = hcn_notice_data_from_post();
        $id = (int) $id;

        if ($id > 0) {
            Capsule::table('mod_had_client_notice_notices')->where('id', $id)->update($data);
            return $id;
        }

        $data['created_at'] = hcn_now();
        return Capsule::table('mod_had_client_notice_notices')->insertGetId($data);
    }
}

if (!function_exists('hcn_status_badge')) {
    function hcn_status_badge($enabled)
    {
        if ((int) $enabled === 1) {
            return '<span class="label label-success">Ativo</span>';
        }

        return '<span class="label label-default">Inativo</span>';
    }
}

if (!function_exists('hcn_display_type_label')) {
    function hcn_display_type_label($type)
    {
        $labels = [
            'modal' => 'Modal central',
            'banner_top' => 'Banner superior',
            'banner_bottom' => 'Banner inferior',
            'toast' => 'Toast lateral',
            'inline_panel' => 'Painel interno',
        ];

        return $labels[$type] ?? $type;
    }
}

if (!function_exists('hcn_product_rule_label')) {
    function hcn_product_rule_label($rule)
    {
        $labels = [
            'all' => 'Todos',
            'has_any' => 'Possui produto selecionado',
            'has_none' => 'Não possui produto selecionado',
            'has_any_and_missing_backup' => 'Possui produto-alvo e não possui backup',
        ];

        return $labels[$rule] ?? $rule;
    }
}

if (!function_exists('hcn_admin_css')) {
    function hcn_admin_css()
    {
        return <<<HTML
<style>
.hcn-wrap { max-width: 1280px; }
.hcn-actions { display:flex; gap:8px; flex-wrap:wrap; }
.hcn-card { background:#fff; border:1px solid #ddd; border-radius:8px; padding:18px; margin-bottom:18px; }
.hcn-grid-2 { display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:16px; }
.hcn-grid-3 { display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap:16px; }
.hcn-checkbox-grid { display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:6px 14px; max-height:220px; overflow:auto; padding:10px; border:1px solid #ddd; border-radius:6px; background:#fafafa; }
.hcn-check-item { font-weight:400; margin:0; }
.hcn-help { color:#777; font-size:12px; margin-top:5px; }
.hcn-section-title { margin: 24px 0 12px; font-weight:700; border-bottom:1px solid #e5e5e5; padding-bottom:8px; }
.hcn-color-row input[type=color] { width: 100%; height: 38px; padding: 2px; }
.hcn-table td { vertical-align: middle !important; }
@media(max-width: 900px) { .hcn-grid-2, .hcn-grid-3 { grid-template-columns: 1fr; } .hcn-checkbox-grid { grid-template-columns: 1fr; } }
</style>
HTML;
    }
}

if (!function_exists('hcn_admin_notice_list')) {
    function hcn_admin_notice_list($modulelink, $messageHtml = '')
    {
        $notices = Capsule::table('mod_had_client_notice_notices')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        echo hcn_admin_css();
        echo '<div class="hcn-wrap">';
        echo '<h2>HAD Avisos ao Cliente</h2>';
        echo '<p>Central de avisos para a área do cliente do WHMCS. Cadastre múltiplos avisos, segmente por produto/grupo/status, agende períodos e registre aceites.</p>';
        echo $messageHtml;
        echo '<p><a class="btn btn-primary" href="' . hcn_e($modulelink) . '&action=edit">+ Novo aviso</a> ';
        echo '<a class="btn btn-default" href="' . hcn_e($modulelink) . '&action=acceptances">Ver aceites</a></p>';

        echo '<div class="table-responsive">';
        echo '<table class="table table-striped table-hover hcn-table">';
        echo '<thead><tr>';
        echo '<th>ID</th><th>Status</th><th>Título</th><th>Chave</th><th>Tipo</th><th>Segmentação</th><th>Período</th><th>Ordem</th><th>Ações</th>';
        echo '</tr></thead><tbody>';

        if (count($notices) === 0) {
            echo '<tr><td colspan="9">Nenhum aviso cadastrado.</td></tr>';
        }

        foreach ($notices as $notice) {
            $period = 'Sempre';
            if ($notice->start_at || $notice->end_at) {
                $period = hcn_e($notice->start_at ?: 'agora') . ' até ' . hcn_e($notice->end_at ?: 'sem fim');
            }

            echo '<tr>';
            echo '<td>#' . (int) $notice->id . '</td>';
            echo '<td>' . hcn_status_badge($notice->enabled) . '</td>';
            echo '<td><strong>' . hcn_e($notice->title) . '</strong></td>';
            echo '<td><code>' . hcn_e($notice->notice_key) . '</code></td>';
            echo '<td>' . hcn_e(hcn_display_type_label($notice->display_type)) . '</td>';
            echo '<td>' . hcn_e(hcn_product_rule_label($notice->product_rule)) . '</td>';
            echo '<td>' . $period . '</td>';
            echo '<td>' . (int) $notice->sort_order . '</td>';
            echo '<td class="hcn-actions">';
            echo '<a class="btn btn-xs btn-default" href="' . hcn_e($modulelink) . '&action=edit&id=' . (int) $notice->id . '">Editar</a>';
            echo '<a class="btn btn-xs btn-info" href="' . hcn_e($modulelink) . '&action=acceptances&notice_id=' . (int) $notice->id . '">Aceites</a>';
            echo '<form method="post" action="' . hcn_e($modulelink) . '&action=delete" style="display:inline" onsubmit="return confirm(\'Tem certeza que deseja excluir este aviso? Os aceites já registrados serão preservados.\');">';
            echo '<input type="hidden" name="id" value="' . (int) $notice->id . '">';
            echo '<button type="submit" class="btn btn-xs btn-danger">Excluir</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody></table></div>';
        echo '<div class="alert alert-info"><strong>Dica:</strong> para fazer um aviso reaparecer para clientes que já aceitaram, altere a chave/versão do aviso, por exemplo de <code>backup-v1</code> para <code>backup-v2</code>.</div>';
        echo '</div>';
    }
}

if (!function_exists('hcn_admin_notice_form')) {
    function hcn_admin_notice_form($modulelink, $notice = null, $messageHtml = '')
    {
        $isEdit = $notice && !empty($notice->id);
        $data = (object) array_merge(hcn_get_default_notice_data(), $notice ? (array) $notice : []);
        $products = hcn_get_products();
        $clientGroups = hcn_get_client_groups();

        $serviceStatuses = [
            'Active' => 'Ativo',
            'Suspended' => 'Suspenso',
            'Pending' => 'Pendente',
            'Terminated' => 'Cancelado/Terminado',
        ];

        $clientStatuses = [
            'Active' => 'Ativo',
            'Inactive' => 'Inativo',
            'Closed' => 'Fechado',
        ];

        echo hcn_admin_css();
        echo '<div class="hcn-wrap">';
        echo '<h2>' . ($isEdit ? 'Editar aviso' : 'Novo aviso') . '</h2>';
        echo $messageHtml;
        echo '<form method="post" action="' . hcn_e($modulelink) . '&action=save">';
        echo '<input type="hidden" name="id" value="' . (int) ($data->id ?? 0) . '">';

        echo '<div class="hcn-card">';
        echo '<h3 class="hcn-section-title">Conteúdo</h3>';
        echo '<div class="checkbox"><label><input type="checkbox" name="enabled" value="1" ' . ((int) $data->enabled === 1 ? 'checked' : '') . '> Aviso ativo</label></div>';

        echo '<div class="hcn-grid-2">';
        echo '<div class="form-group"><label>Título</label><input type="text" name="title" class="form-control" value="' . hcn_e($data->title) . '" required></div>';
        echo '<div class="form-group"><label>Chave/versão do aviso</label><input type="text" name="notice_key" class="form-control" value="' . hcn_e($data->notice_key) . '" required><div class="hcn-help">Altere esta chave para o aviso aparecer novamente para todos.</div></div>';
        echo '</div>';

        echo '<div class="hcn-grid-3">';
        echo '<div class="form-group"><label>Formato do conteúdo</label><select name="content_format" class="form-control">';
        foreach (['text' => 'Texto simples', 'html' => 'HTML limitado'] as $value => $label) {
            echo '<option value="' . hcn_e($value) . '" ' . ($data->content_format === $value ? 'selected' : '') . '>' . hcn_e($label) . '</option>';
        }
        echo '</select></div>';

        echo '<div class="form-group"><label>Tipo de exibição</label><select name="display_type" class="form-control">';
        foreach ([
            'modal' => 'Modal central',
            'banner_top' => 'Banner superior',
            'banner_bottom' => 'Banner inferior',
            'toast' => 'Toast lateral',
            'inline_panel' => 'Painel interno na área do cliente',
        ] as $value => $label) {
            echo '<option value="' . hcn_e($value) . '" ' . ($data->display_type === $value ? 'selected' : '') . '>' . hcn_e($label) . '</option>';
        }
        echo '</select></div>';

        echo '<div class="form-group"><label>Ícone</label><select name="icon" class="form-control">';
        foreach ([
            'none' => 'Sem ícone',
            'warning' => 'Alerta',
            'info' => 'Informação',
            'success' => 'Sucesso',
            'backup' => 'Backup/Nuvem',
            'maintenance' => 'Manutenção',
        ] as $value => $label) {
            echo '<option value="' . hcn_e($value) . '" ' . ($data->icon === $value ? 'selected' : '') . '>' . hcn_e($label) . '</option>';
        }
        echo '</select></div>';
        echo '</div>';

        echo '<div class="form-group"><label>Mensagem</label><textarea name="message" class="form-control" rows="9">' . hcn_e($data->message) . '</textarea><div class="hcn-help">No modo HTML limitado, use apenas tags simples como &lt;p&gt;, &lt;strong&gt;, &lt;ul&gt;, &lt;li&gt; e &lt;a&gt;.</div></div>';
        echo '</div>';

        echo '<div class="hcn-card">';
        echo '<h3 class="hcn-section-title">Botões e aceite</h3>';
        echo '<div class="hcn-grid-2">';
        echo '<div class="form-group"><label>Texto do botão principal</label><input type="text" name="primary_button_text" class="form-control" value="' . hcn_e($data->primary_button_text) . '"></div>';
        echo '<div class="form-group"><label>Texto do checkbox</label><input type="text" name="checkbox_text" class="form-control" value="' . hcn_e($data->checkbox_text) . '"></div>';
        echo '</div>';
        echo '<div class="checkbox"><label><input type="checkbox" name="show_once" value="1" ' . ((int) $data->show_once === 1 ? 'checked' : '') . '> Exibir apenas uma vez por cliente/dispositivo</label></div>';
        echo '<div class="checkbox"><label><input type="checkbox" name="only_logged" value="1" ' . ((int) $data->only_logged === 1 ? 'checked' : '') . '> Exibir somente para clientes logados</label></div>';
        echo '<div class="checkbox"><label><input type="checkbox" name="is_mandatory" value="1" ' . ((int) $data->is_mandatory === 1 ? 'checked' : '') . '> Aviso obrigatório: não mostrar botão de fechar</label></div>';
        echo '<div class="checkbox"><label><input type="checkbox" name="requires_checkbox" value="1" ' . ((int) $data->requires_checkbox === 1 ? 'checked' : '') . '> Exigir marcação do checkbox antes do aceite</label></div>';
        echo '<hr>';
        echo '<h4>Botão secundário opcional</h4>';
        echo '<div class="hcn-grid-2">';
        echo '<div class="form-group"><label>Texto do botão secundário</label><input type="text" name="secondary_button_text" class="form-control" value="' . hcn_e($data->secondary_button_text) . '"></div>';
        echo '<div class="form-group"><label>URL do botão secundário</label><input type="url" name="secondary_button_url" class="form-control" value="' . hcn_e($data->secondary_button_url) . '"></div>';
        echo '</div>';
        echo '</div>';

        echo '<div class="hcn-card">';
        echo '<h3 class="hcn-section-title">Agendamento</h3>';
        echo '<div class="hcn-grid-3">';
        echo '<div class="form-group"><label>Exibir a partir de</label><input type="datetime-local" name="start_at" class="form-control" value="' . hcn_e(hcn_db_to_datetime_input($data->start_at)) . '"></div>';
        echo '<div class="form-group"><label>Exibir até</label><input type="datetime-local" name="end_at" class="form-control" value="' . hcn_e(hcn_db_to_datetime_input($data->end_at)) . '"></div>';
        echo '<div class="form-group"><label>Ordem de exibição</label><input type="number" name="sort_order" class="form-control" value="' . (int) $data->sort_order . '"></div>';
        echo '</div>';
        echo '</div>';

        echo '<div class="hcn-card">';
        echo '<h3 class="hcn-section-title">Segmentação</h3>';
        echo '<div class="hcn-grid-2">';
        echo '<div class="form-group"><label>Regra de produto/serviço</label><select name="product_rule" class="form-control">';
        foreach ([
            'all' => 'Todos os clientes',
            'has_any' => 'Mostrar para clientes que possuem qualquer produto selecionado',
            'has_none' => 'Mostrar para clientes que NÃO possuem os produtos selecionados',
            'has_any_and_missing_backup' => 'Mostrar para clientes que possuem produto-alvo e NÃO possuem backup',
        ] as $value => $label) {
            echo '<option value="' . hcn_e($value) . '" ' . ($data->product_rule === $value ? 'selected' : '') . '>' . hcn_e($label) . '</option>';
        }
        echo '</select></div>';
        echo '<div class="form-group"><label>Regra de fatura</label><select name="invoice_rule" class="form-control">';
        foreach ([
            'any' => 'Independente de faturas',
            'overdue' => 'Somente clientes com fatura vencida',
        ] as $value => $label) {
            echo '<option value="' . hcn_e($value) . '" ' . ($data->invoice_rule === $value ? 'selected' : '') . '>' . hcn_e($label) . '</option>';
        }
        echo '</select></div>';
        echo '</div>';

        echo '<h4>Produtos-alvo</h4>';
        echo '<p class="hcn-help">Exemplo: marque produtos Cloud Server/VPS para avisos de backup ou manutenção.</p>';
        echo hcn_render_checkbox_group('target_product_ids', $products, $data->target_product_ids, 'id', function ($product) {
            $hidden = ((int) $product->hidden === 1) ? ' <span class="text-muted">(oculto)</span>' : '';
            return '<strong>#' . (int) $product->id . '</strong> ' . hcn_e($product->group_name ?: 'Sem grupo') . ' / ' . hcn_e($product->name) . $hidden;
        });

        echo '<h4>Produtos de backup</h4>';
        echo '<p class="hcn-help">Usado na regra: possui produto-alvo e não possui backup. Marque aqui os produtos HAD Vault/Backup.</p>';
        echo hcn_render_checkbox_group('backup_product_ids', $products, $data->backup_product_ids, 'id', function ($product) {
            $hidden = ((int) $product->hidden === 1) ? ' <span class="text-muted">(oculto)</span>' : '';
            return '<strong>#' . (int) $product->id . '</strong> ' . hcn_e($product->group_name ?: 'Sem grupo') . ' / ' . hcn_e($product->name) . $hidden;
        });

        echo '<h4>Status dos serviços considerados</h4>';
        echo '<div class="hcn-checkbox-grid">';
        $selectedServiceStatuses = hcn_csv_to_array($data->service_statuses ?: 'Active');
        foreach ($serviceStatuses as $value => $label) {
            echo '<label class="hcn-check-item"><input type="checkbox" name="service_statuses[]" value="' . hcn_e($value) . '" ' . (in_array($value, $selectedServiceStatuses, true) ? 'checked' : '') . '> ' . hcn_e($label) . '</label>';
        }
        echo '</div>';

        echo '<h4>Grupos de clientes</h4>';
        echo '<p class="hcn-help">Deixe sem marcar para não filtrar por grupo.</p>';
        if (count($clientGroups) > 0) {
            echo hcn_render_checkbox_group('target_client_group_ids', $clientGroups, $data->target_client_group_ids, 'id', function ($group) {
                return '<strong>#' . (int) $group->id . '</strong> ' . hcn_e($group->groupname);
            });
        } else {
            echo '<p class="text-muted">Nenhum grupo de cliente encontrado.</p>';
        }

        echo '<h4>Status dos clientes</h4>';
        echo '<p class="hcn-help">Deixe sem marcar para não filtrar por status do cliente.</p>';
        echo '<div class="hcn-checkbox-grid">';
        $selectedClientStatuses = hcn_csv_to_array($data->target_client_statuses);
        foreach ($clientStatuses as $value => $label) {
            echo '<label class="hcn-check-item"><input type="checkbox" name="target_client_statuses[]" value="' . hcn_e($value) . '" ' . (in_array($value, $selectedClientStatuses, true) ? 'checked' : '') . '> ' . hcn_e($label) . '</label>';
        }
        echo '</div>';
        echo '</div>';

        echo '<div class="hcn-card">';
        echo '<h3 class="hcn-section-title">Visual</h3>';
        echo '<div class="hcn-grid-3 hcn-color-row">';
        echo '<div class="form-group"><label>Cor do cabeçalho</label><input type="color" name="header_bg" class="form-control" value="' . hcn_e($data->header_bg) . '"></div>';
        echo '<div class="form-group"><label>Cor do botão principal</label><input type="color" name="primary_color" class="form-control" value="' . hcn_e($data->primary_color) . '"></div>';
        echo '<div class="form-group"><label>Cor de destaque</label><input type="color" name="secondary_color" class="form-control" value="' . hcn_e($data->secondary_color) . '"></div>';
        echo '</div>';
        echo '<div class="hcn-grid-2">';
        echo '<div class="form-group"><label>Cor do texto</label><input type="color" name="text_color" class="form-control" value="' . hcn_e($data->text_color) . '"></div>';
        echo '<div class="form-group"><label>Largura máxima do aviso em pixels</label><input type="number" name="width" min="320" max="1200" class="form-control" value="' . (int) $data->width . '"></div>';
        echo '</div>';
        echo '</div>';

        echo '<p><button type="submit" class="btn btn-primary">Salvar aviso</button> <a class="btn btn-default" href="' . hcn_e($modulelink) . '">Voltar</a></p>';
        echo '</form>';
        echo '</div>';
    }
}

if (!function_exists('hcn_admin_acceptances')) {
    function hcn_admin_acceptances($modulelink, $noticeId = 0)
    {
        $noticeId = (int) $noticeId;
        $query = Capsule::table('mod_had_client_notice_acceptances as a')
            ->leftJoin('tblclients as c', 'c.id', '=', 'a.userid')
            ->select('a.*', 'c.firstname', 'c.lastname', 'c.email')
            ->orderBy('a.accepted_at', 'desc')
            ->limit(500);

        if ($noticeId > 0) {
            $query->where('a.notice_id', $noticeId);
        }

        $rows = $query->get();

        echo hcn_admin_css();
        echo '<div class="hcn-wrap">';
        echo '<h2>Aceites registrados</h2>';
        echo '<p><a class="btn btn-default" href="' . hcn_e($modulelink) . '">Voltar aos avisos</a></p>';
        echo '<div class="alert alert-info">Exibindo os últimos 500 registros' . ($noticeId ? ' do aviso #' . $noticeId : '') . '.</div>';
        echo '<div class="table-responsive"><table class="table table-striped table-hover">';
        echo '<thead><tr><th>Data/hora</th><th>Cliente</th><th>Aviso</th><th>Chave</th><th>IP</th><th>User-Agent</th></tr></thead><tbody>';

        if (count($rows) === 0) {
            echo '<tr><td colspan="6">Nenhum aceite registrado.</td></tr>';
        }

        foreach ($rows as $row) {
            $client = $row->userid ? '#' . (int) $row->userid . ' - ' . trim((string) $row->firstname . ' ' . (string) $row->lastname) . ' &lt;' . hcn_e($row->email) . '&gt;' : 'Visitante/não logado';
            echo '<tr>';
            echo '<td>' . hcn_e($row->accepted_at) . '</td>';
            echo '<td>' . $client . '</td>';
            echo '<td>#' . (int) $row->notice_id . ' - ' . hcn_e($row->title_snapshot) . '</td>';
            echo '<td><code>' . hcn_e($row->notice_key) . '</code></td>';
            echo '<td>' . hcn_e($row->ip_address) . '</td>';
            echo '<td style="max-width:360px; word-break:break-word;">' . hcn_e($row->user_agent) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table></div>';
        echo '</div>';
    }
}

if (!function_exists('had_client_notice_output')) {
function had_client_notice_output($vars)
{
    $modulelink = $vars['modulelink'];
    $action = $_REQUEST['action'] ?? 'list';
    $messageHtml = '';

    if (!hcn_table_exists('mod_had_client_notice_notices') || !hcn_table_exists('mod_had_client_notice_acceptances')) {
        echo '<div class="alert alert-danger">As tabelas do módulo não foram encontradas. Desative e ative o módulo novamente em Addon Modules.</div>';
        return;
    }

    try {
        if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            $savedId = hcn_save_notice($id);
            $messageHtml = '<div class="alert alert-success">Aviso #' . (int) $savedId . ' salvo com sucesso.</div>';
            hcn_admin_notice_form($modulelink, hcn_get_notice_by_id($savedId), $messageHtml);
            return;
        }

        if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                Capsule::table('mod_had_client_notice_notices')->where('id', $id)->delete();
                $messageHtml = '<div class="alert alert-success">Aviso excluído. Os aceites já registrados foram preservados.</div>';
            }
            hcn_admin_notice_list($modulelink, $messageHtml);
            return;
        }

        if ($action === 'edit') {
            $id = (int) ($_GET['id'] ?? 0);
            hcn_admin_notice_form($modulelink, hcn_get_notice_by_id($id));
            return;
        }

        if ($action === 'acceptances') {
            $noticeId = (int) ($_GET['notice_id'] ?? 0);
            hcn_admin_acceptances($modulelink, $noticeId);
            return;
        }

        hcn_admin_notice_list($modulelink, $messageHtml);
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Erro no módulo: ' . hcn_e($e->getMessage()) . '</div>';
    }
}}


if (!function_exists('hcn_client_has_any_service')) {
    function hcn_client_has_any_service($userid, array $productIds, array $statuses)
    {
        if (!$userid || count($productIds) === 0) {
            return false;
        }

        try {
            $query = Capsule::table('tblhosting')
                ->where('userid', (int) $userid)
                ->whereIn('packageid', $productIds);

            if (count($statuses) > 0) {
                $query->whereIn('domainstatus', $statuses);
            }

            return $query->exists();
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('hcn_client_has_overdue_invoice')) {
    function hcn_client_has_overdue_invoice($userid)
    {
        if (!$userid) {
            return false;
        }

        try {
            return Capsule::table('tblinvoices')
                ->where('userid', (int) $userid)
                ->where('status', 'Unpaid')
                ->where('duedate', '<', hcn_date_today())
                ->exists();
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('hcn_notice_already_accepted')) {
    function hcn_notice_already_accepted($noticeId, $userid, $noticeKey = null)
    {
        if (!$userid) {
            return false;
        }

        try {
            $query = Capsule::table('mod_had_client_notice_acceptances')
                ->where('notice_id', (int) $noticeId)
                ->where('userid', (int) $userid);

            if ($noticeKey !== null) {
                $query->where('notice_key', (string) $noticeKey);
            }

            return $query->exists();
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('hcn_notice_applies_to_user')) {
    function hcn_notice_applies_to_user($notice, $userid)
    {
        if ((int) $notice->only_logged === 1 && !$userid) {
            return false;
        }

        if ((int) $notice->show_once === 1 && $userid && hcn_notice_already_accepted($notice->id, $userid, $notice->notice_key)) {
            return false;
        }

        $clientGroupIds = hcn_csv_to_int_array($notice->target_client_group_ids);
        $clientStatuses = hcn_csv_to_array($notice->target_client_statuses);

        if ($userid && (count($clientGroupIds) > 0 || count($clientStatuses) > 0)) {
            try {
                $client = Capsule::table('tblclients')
                    ->select('id', 'groupid', 'status')
                    ->where('id', (int) $userid)
                    ->first();

                if (!$client) {
                    return false;
                }

                if (count($clientGroupIds) > 0 && !in_array((int) $client->groupid, $clientGroupIds, true)) {
                    return false;
                }

                if (count($clientStatuses) > 0 && !in_array((string) $client->status, $clientStatuses, true)) {
                    return false;
                }
            } catch (Exception $e) {
                return false;
            }
        }

        if ($notice->invoice_rule === 'overdue' && !hcn_client_has_overdue_invoice($userid)) {
            return false;
        }

        $targetProductIds = hcn_csv_to_int_array($notice->target_product_ids);
        $backupProductIds = hcn_csv_to_int_array($notice->backup_product_ids);
        $serviceStatuses = hcn_csv_to_array($notice->service_statuses ?: 'Active');
        $productRule = $notice->product_rule ?: 'all';

        if ($productRule === 'all') {
            return true;
        }

        if (!$userid) {
            return false;
        }

        if ($productRule === 'has_any') {
            return hcn_client_has_any_service($userid, $targetProductIds, $serviceStatuses);
        }

        if ($productRule === 'has_none') {
            return !hcn_client_has_any_service($userid, $targetProductIds, $serviceStatuses);
        }

        if ($productRule === 'has_any_and_missing_backup') {
            $hasTarget = hcn_client_has_any_service($userid, $targetProductIds, $serviceStatuses);
            $hasBackup = hcn_client_has_any_service($userid, $backupProductIds, $serviceStatuses);
            return $hasTarget && !$hasBackup;
        }

        return true;
    }
}

if (!function_exists('hcn_get_active_notices_for_client')) {
    function hcn_get_active_notices_for_client($userid)
    {
        if (!hcn_table_exists('mod_had_client_notice_notices') || !hcn_table_exists('mod_had_client_notice_acceptances')) {
            return [];
        }

        try {
            $now = hcn_now();
            $notices = Capsule::table('mod_had_client_notice_notices')
                ->where('enabled', 1)
                ->where(function ($query) use ($now) {
                    $query->whereNull('start_at')->orWhere('start_at', '<=', $now);
                })
                ->where(function ($query) use ($now) {
                    $query->whereNull('end_at')->orWhere('end_at', '>=', $now);
                })
                ->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $applicable = [];
            foreach ($notices as $notice) {
                if (hcn_notice_applies_to_user($notice, $userid)) {
                    $applicable[] = $notice;
                }
            }

            return $applicable;
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('hcn_notice_icon_html')) {
    function hcn_notice_icon_html($icon)
    {
        $icons = [
            'none' => '',
            'warning' => '&#9888;',
            'info' => '&#9432;',
            'success' => '&#10003;',
            'backup' => '&#9729;',
            'maintenance' => '&#9881;',
        ];

        $iconHtml = $icons[$icon] ?? $icons['warning'];
        if ($iconHtml === '') {
            return '';
        }

        return '<span class="hcn-icon" aria-hidden="true">' . $iconHtml . '</span>';
    }
}

if (!function_exists('hcn_render_notice_body')) {
    function hcn_render_notice_body($notice)
    {
        if ($notice->content_format === 'html') {
            return hcn_sanitize_html($notice->message);
        }

        return nl2br(hcn_e($notice->message));
    }
}

if (!function_exists('hcn_client_css')) {
    function hcn_client_css()
    {
        return <<<HTML
<style>
.hcn-notice { display:none; z-index:99999; font-family: Arial, Helvetica, sans-serif; color: var(--hcn-text-color); }
.hcn-notice.hcn-visible { display:flex; }
.hcn-card { width:100%; max-width: var(--hcn-width); background:#fff; color:var(--hcn-text-color); border-radius:18px; box-shadow:0 20px 60px rgba(0,0,0,.28); overflow:hidden; border:1px solid rgba(0,0,0,.08); }
.hcn-header { background: var(--hcn-header-bg); color:#fff; padding:20px 24px; display:flex; align-items:center; gap:12px; }
.hcn-header h2 { margin:0; font-size:21px; line-height:1.25; font-weight:700; color:#fff; }
.hcn-icon { width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; border-radius:999px; background:rgba(255,255,255,.16); flex:0 0 34px; font-size:20px; }
.hcn-body { padding:24px; font-size:15px; line-height:1.6; }
.hcn-body p:last-child { margin-bottom:0; }
.hcn-checkbox { margin-top:18px; padding:14px 16px; border-radius:10px; background:#f8fafc; border:1px solid #e5e7eb; }
.hcn-checkbox label { display:flex; gap:10px; align-items:flex-start; font-weight:600; margin:0; cursor:pointer; }
.hcn-checkbox input { margin-top:4px; }
.hcn-footer { padding:0 24px 24px; display:flex; gap:12px; justify-content:flex-end; align-items:center; flex-wrap:wrap; }
.hcn-btn { border:0; border-radius:10px; padding:12px 18px; font-weight:700; cursor:pointer; transition:.18s ease; text-decoration:none !important; display:inline-flex; align-items:center; justify-content:center; }
.hcn-btn-primary { background: var(--hcn-primary-color); color:#fff !important; }
.hcn-btn-primary:hover { filter:brightness(.92); color:#fff !important; }
.hcn-btn-primary:disabled { opacity:.5; cursor:not-allowed; }
.hcn-btn-secondary { background:#f3f4f6; color:#111 !important; }
.hcn-btn-secondary:hover { background:#e5e7eb; color:#111 !important; }
.hcn-close { position:absolute; top:10px; right:12px; border:0; background:transparent; color:#fff; font-size:26px; line-height:1; cursor:pointer; opacity:.8; }
.hcn-close:hover { opacity:1; }
.hcn-type-modal { position:fixed; inset:0; background:rgba(15,15,25,.72); align-items:center; justify-content:center; padding:20px; }
.hcn-type-modal .hcn-card { position:relative; animation:hcnPopupShow .22s ease-out; }
.hcn-type-banner_top, .hcn-type-banner_bottom { position:fixed; left:0; right:0; justify-content:center; padding:12px 16px; pointer-events:none; }
.hcn-type-banner_top { top:0; }
.hcn-type-banner_bottom { bottom:0; }
.hcn-type-banner_top .hcn-card, .hcn-type-banner_bottom .hcn-card { max-width: min(var(--hcn-width), calc(100vw - 32px)); border-radius:14px; pointer-events:auto; position:relative; }
.hcn-type-banner_top .hcn-header, .hcn-type-banner_bottom .hcn-header { padding:14px 18px; }
.hcn-type-banner_top .hcn-body, .hcn-type-banner_bottom .hcn-body { padding:16px 18px; }
.hcn-type-banner_top .hcn-footer, .hcn-type-banner_bottom .hcn-footer { padding:0 18px 18px; }
.hcn-type-toast { position:fixed; right:22px; bottom:22px; align-items:flex-end; justify-content:flex-end; width:min(var(--hcn-width), calc(100vw - 44px)); }
.hcn-type-toast .hcn-card { max-width:100%; position:relative; animation:hcnToastShow .22s ease-out; }
.hcn-type-toast .hcn-header { padding:16px 18px; }
.hcn-type-toast .hcn-header h2 { font-size:18px; }
.hcn-type-toast .hcn-body { padding:18px; }
.hcn-type-toast .hcn-footer { padding:0 18px 18px; }
.hcn-type-inline_panel { position:relative; width:100%; margin:0 0 20px; z-index:1; }
.hcn-type-inline_panel.hcn-visible { display:block; }
.hcn-type-inline_panel .hcn-card { max-width:100%; box-shadow:0 10px 28px rgba(0,0,0,.12); position:relative; }
@keyframes hcnPopupShow { from { opacity:0; transform: translateY(14px) scale(.98); } to { opacity:1; transform: translateY(0) scale(1); } }
@keyframes hcnToastShow { from { opacity:0; transform: translateY(14px); } to { opacity:1; transform: translateY(0); } }
@media(max-width: 640px) { .hcn-card { border-radius:14px; } .hcn-header h2 { font-size:18px; } .hcn-body { padding:18px; } .hcn-footer { padding:0 18px 18px; justify-content:stretch; } .hcn-btn { width:100%; } .hcn-type-toast { right:12px; bottom:12px; width:calc(100vw - 24px); } }
</style>
HTML;
    }
}

if (!function_exists('hcn_render_notice_html')) {
    function hcn_render_notice_html($notice)
    {
        $id = (int) $notice->id;
        $displayType = in_array($notice->display_type, ['modal', 'banner_top', 'banner_bottom', 'toast', 'inline_panel'], true) ? $notice->display_type : 'modal';
        $style = '--hcn-header-bg:' . hcn_clean_hex_color($notice->header_bg, '#4b1d95') . ';';
        $style .= '--hcn-primary-color:' . hcn_clean_hex_color($notice->primary_color, '#4b1d95') . ';';
        $style .= '--hcn-secondary-color:' . hcn_clean_hex_color($notice->secondary_color, '#f97316') . ';';
        $style .= '--hcn-text-color:' . hcn_clean_hex_color($notice->text_color, '#222222') . ';';
        $style .= '--hcn-width:' . hcn_clean_int_range($notice->width, 580, 320, 1200) . 'px;';

        $title = hcn_e($notice->title);
        $body = hcn_render_notice_body($notice);
        $primary = hcn_e($notice->primary_button_text ?: 'Entendi');
        $checkboxText = hcn_e($notice->checkbox_text ?: 'Declaro que li e estou ciente deste aviso.');
        $noticeKey = hcn_e($notice->notice_key);
        $isMandatory = (int) $notice->is_mandatory === 1;
        $requiresCheckbox = (int) $notice->requires_checkbox === 1;
        $showOnce = (int) $notice->show_once === 1;

        $secondaryButton = '';
        if (!empty($notice->secondary_button_text) && !empty($notice->secondary_button_url)) {
            $secondaryButton = '<a href="' . hcn_e($notice->secondary_button_url) . '" class="hcn-btn hcn-btn-secondary" target="_blank" rel="noopener noreferrer">' . hcn_e($notice->secondary_button_text) . '</a>';
        }

        $closeButton = '';
        if (!$isMandatory) {
            $closeButton = '<button type="button" class="hcn-close" data-hcn-close aria-label="Fechar">&times;</button>';
        }

        $checkbox = '';
        if ($requiresCheckbox) {
            $checkbox = '<div class="hcn-checkbox"><label><input type="checkbox" data-hcn-checkbox> <span>' . $checkboxText . '</span></label></div>';
        }

        $iconHtml = hcn_notice_icon_html($notice->icon);

        return <<<HTML
<div class="hcn-notice hcn-type-{$displayType}" id="hcn-notice-{$id}" data-hcn-notice data-hcn-id="{$id}" data-hcn-key="{$noticeKey}" data-hcn-show-once="{$showOnce}" data-hcn-required-checkbox="{$requiresCheckbox}" data-hcn-display-type="{$displayType}" style="{$style}">
    <div class="hcn-card" role="dialog" aria-modal="true" aria-labelledby="hcn-title-{$id}">
        {$closeButton}
        <div class="hcn-header">
            {$iconHtml}
            <h2 id="hcn-title-{$id}">{$title}</h2>
        </div>
        <div class="hcn-body">
            {$body}
            {$checkbox}
        </div>
        <div class="hcn-footer">
            {$secondaryButton}
            <button type="button" class="hcn-btn hcn-btn-primary" data-hcn-accept>{$primary}</button>
        </div>
    </div>
</div>
HTML;
    }
}

if (!function_exists('hcn_client_js')) {
    function hcn_client_js($ajaxUrl)
    {
        $ajaxUrlJson = json_encode($ajaxUrl);
        return <<<HTML
<script>
(function() {
    var ajaxUrl = {$ajaxUrlJson};

    function insertInlinePanel(notice) {
        if (notice.getAttribute('data-hcn-display-type') !== 'inline_panel') {
            return;
        }

        var targets = [
            document.querySelector('.main-content .container'),
            document.querySelector('.main-content'),
            document.querySelector('#main-body .container'),
            document.querySelector('#main-body'),
            document.querySelector('.container')
        ];

        for (var i = 0; i < targets.length; i++) {
            if (targets[i]) {
                targets[i].insertBefore(notice, targets[i].firstChild);
                return;
            }
        }
    }

    function postAcceptance(notice) {
        var noticeId = notice.getAttribute('data-hcn-id');
        var body = 'notice_id=' + encodeURIComponent(noticeId);

        if (!window.fetch) {
            return;
        }

        fetch(ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: body
        }).catch(function() {});
    }

    function hideNotice(notice) {
        notice.classList.remove('hcn-visible');
    }

    var notices = document.querySelectorAll('[data-hcn-notice]');

    for (var i = 0; i < notices.length; i++) {
        (function(notice) {
            insertInlinePanel(notice);

            var key = 'hcn_notice_' + notice.getAttribute('data-hcn-key');
            var showOnce = notice.getAttribute('data-hcn-show-once') === '1';
            var requiresCheckbox = notice.getAttribute('data-hcn-required-checkbox') === '1';
            var checkbox = notice.querySelector('[data-hcn-checkbox]');
            var acceptButton = notice.querySelector('[data-hcn-accept]');
            var closeButton = notice.querySelector('[data-hcn-close]');

            if (showOnce && window.localStorage && localStorage.getItem(key) === 'accepted') {
                return;
            }

            notice.classList.add('hcn-visible');

            if (requiresCheckbox && checkbox && acceptButton) {
                acceptButton.disabled = true;
                checkbox.addEventListener('change', function() {
                    acceptButton.disabled = !checkbox.checked;
                });
            }

            if (acceptButton) {
                acceptButton.addEventListener('click', function() {
                    if (requiresCheckbox && checkbox && !checkbox.checked) {
                        return;
                    }

                    if (showOnce && window.localStorage) {
                        localStorage.setItem(key, 'accepted');
                    }

                    postAcceptance(notice);
                    hideNotice(notice);
                });
            }

            if (closeButton) {
                closeButton.addEventListener('click', function() {
                    hideNotice(notice);
                });
            }
        })(notices[i]);
    }
})();
</script>
HTML;
    }
}

if (!function_exists('hcn_client_footer_output')) {
    function hcn_client_footer_output($vars)
    {
        $userid = !empty($_SESSION['uid']) ? (int) $_SESSION['uid'] : 0;
        $notices = hcn_get_active_notices_for_client($userid);

        if (count($notices) === 0) {
            return '';
        }

        $webRoot = isset($vars['WEB_ROOT']) ? rtrim((string) $vars['WEB_ROOT'], '/') : '';
        $ajaxUrl = $webRoot . '/modules/addons/had_client_notice/ajax.php';
        $html = hcn_client_css();

        foreach ($notices as $notice) {
            $html .= hcn_render_notice_html($notice);
        }

        $html .= hcn_client_js($ajaxUrl);

        return $html;
    }
}
