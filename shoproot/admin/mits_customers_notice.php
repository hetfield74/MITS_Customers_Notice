<?php
/**
 * --------------------------------------------------------------
 * File: mits_customers_notice.php
 * Date: 23.09.2026
 *
 * Author: Hetfield
 * Copyright: (c) 2026 - MerZ IT-SerVice
 * Web: https://www.merz-it-service.de
 * Contact: info@merz-it-service.de
 * --------------------------------------------------------------
 */

require_once 'includes/application_top.php';
require_once DIR_FS_INC . 'xtc_wysiwyg.inc.php';

function mits_cn_text(string $constant, string $fallback): string
{
    return defined($constant) ? (string)constant($constant) : $fallback;
}

function mits_cn_h($value): string
{
    if (function_exists('encode_htmlspecialchars')) {
        return (string)encode_htmlspecialchars((string)$value, ENT_QUOTES);
    }
    return htmlspecialchars((string)$value, ENT_QUOTES);
}

function mits_cn_table_exists(string $table): bool
{
    $table = str_replace('`', '', $table);
    $table = str_replace(array('\\', '_', '%'), array('\\\\', '\_', '\%'), $table);
    $query = xtc_db_query("SHOW TABLES LIKE '" . xtc_db_input($table) . "'");
    return xtc_db_num_rows($query) > 0;
}

function mits_cn_column_exists(string $table, string $column): bool
{
    $query = xtc_db_query("SHOW COLUMNS FROM `" . str_replace('`', '', $table) . "` LIKE '" . xtc_db_input($column) . "'");
    return xtc_db_num_rows($query) > 0;
}

function mits_cn_date_from_input($value): ?string
{
    $value = trim((string)$value);
    if ($value === '') {
        return null;
    }
    $value = str_replace('T', ' ', $value);
    if (strlen($value) === 16) {
        $value .= ':00';
    }
    $timestamp = strtotime($value);
    return $timestamp === false ? null : date('Y-m-d H:i:s', $timestamp);
}

function mits_cn_date_for_input($value): string
{
    if (empty($value) || $value === '0000-00-00 00:00:00') {
        return '';
    }
    $timestamp = strtotime((string)$value);
    return $timestamp ? date('Y-m-d\TH:i', $timestamp) : '';
}

function mits_cn_date_for_list($value): string
{
    if (empty($value) || $value === '0000-00-00 00:00:00') {
        return '&ndash;';
    }
    $timestamp = strtotime((string)$value);
    return $timestamp ? date('d.m.Y H:i', $timestamp) : mits_cn_h($value);
}

function mits_cn_int_values($value, bool $allowZero = false): array
{
    $items = is_array($value) ? $value : preg_split('/[\s,;]+/', (string)$value, -1, PREG_SPLIT_NO_EMPTY);
    $result = array();
    foreach ($items as $item) {
        if (!is_numeric($item)) {
            continue;
        }
        $number = (int)$item;
        if (($allowZero && $number >= 0) || (!$allowZero && $number > 0)) {
            $result[(string)$number] = (string)$number;
        }
    }
    return array_values($result);
}

function mits_cn_string_values($value): array
{
    $items = is_array($value) ? $value : explode(',', (string)$value);
    $result = array();
    foreach ($items as $item) {
        $item = trim((string)$item);
        if ($item !== '') {
            $result[$item] = $item;
        }
    }
    return array_values($result);
}

function mits_cn_clean_legacy_frontend_title($title): string
{
    $title = trim((string)$title);
    return strcasecmp($title, 'NOTITLE') === 0 ? '' : $title;
}

function mits_cn_plain_excerpt($html, int $length = 72): string
{
    $text = html_entity_decode(strip_tags((string)$html), ENT_QUOTES, 'UTF-8');
    $text = trim((string)preg_replace('/\s+/u', ' ', $text));
    if ($text === '') {
        return '';
    }
    if (function_exists('mb_strlen') && mb_strlen($text, 'UTF-8') > $length) {
        return rtrim(mb_substr($text, 0, $length - 1, 'UTF-8')) . '...';
    }
    return strlen($text) > $length ? rtrim(substr($text, 0, $length - 3)) . '...' : $text;
}

function mits_cn_legacy_internal_title(int $legacyId, array $descriptions, int $preferredLanguageId): string
{
    if (isset($descriptions[$preferredLanguageId])) {
        $title = mits_cn_clean_legacy_frontend_title($descriptions[$preferredLanguageId]['title'] ?? '');
        if ($title !== '') {
            return $title;
        }
    }
    foreach ($descriptions as $description) {
        $title = mits_cn_clean_legacy_frontend_title($description['title'] ?? '');
        if ($title !== '') {
            return $title;
        }
    }
    if (isset($descriptions[$preferredLanguageId])) {
        $excerpt = mits_cn_plain_excerpt($descriptions[$preferredLanguageId]['description'] ?? '');
        if ($excerpt !== '') {
            return $excerpt;
        }
    }
    foreach ($descriptions as $description) {
        $excerpt = mits_cn_plain_excerpt($description['description'] ?? '');
        if ($excerpt !== '') {
            return $excerpt;
        }
    }
    return sprintf(mits_cn_text('MITS_CN_LEGACY_INTERNAL_FALLBACK', 'Legacy notice #%d'), $legacyId);
}

function mits_cn_map_legacy_template(string $template): array
{
    $template = basename($template);
    $map = array(
        'display_type' => 'notice',
        'countdown' => 0,
        'frequency' => 'always',
        'audience' => 'all',
        'label' => mits_cn_text('MITS_CN_TYPE_NOTICE', 'Notice'),
        'known' => true
    );
    if ($template === 'important_notice.html') {
        $map['display_type'] = 'important';
        $map['label'] = mits_cn_text('MITS_CN_TYPE_IMPORTANT', 'Important notice');
    } elseif ($template === 'countdown.html') {
        $map['display_type'] = 'important';
        $map['countdown'] = 1;
        $map['label'] = mits_cn_text('MITS_CN_TYPE_IMPORTANT', 'Important notice') . ' + Countdown';
    } elseif ($template === 'newsletter.html') {
        $map['display_type'] = 'modal';
        $map['frequency'] = 'session';
        $map['audience'] = 'logged_in';
        $map['label'] = mits_cn_text('MITS_CN_TYPE_MODAL', 'Modal') . ' + Newsletter';
    } elseif ($template !== '' && $template !== 'default.html') {
        $map['label'] = mits_cn_text('MITS_CN_MIGRATION_UNKNOWN_TEMPLATE', 'Standard notice - check legacy template');
        $map['known'] = false;
    }
    return $map;
}


function mits_cn_available_templates(): array
{
    $templates = array();
    $modulePath = defined('DIR_FS_EXTERNAL')
        ? rtrim((string)DIR_FS_EXTERNAL, '/\\') . DIRECTORY_SEPARATOR . 'mits_customers_notice' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR
        : '';
    $templateName = defined('CURRENT_TEMPLATE') ? (string)CURRENT_TEMPLATE : '';
    $overridePath = (defined('DIR_FS_CATALOG') && $templateName !== '')
        ? rtrim((string)DIR_FS_CATALOG, '/\\') . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $templateName . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'mits_customers_notice' . DIRECTORY_SEPARATOR
        : '';

    foreach (array($modulePath => 'module', $overridePath => 'override') as $path => $source) {
        if ($path === '' || !is_dir($path)) {
            continue;
        }
        $files = glob($path . '*.html');
        if (!is_array($files)) {
            continue;
        }
        foreach ($files as $file) {
            $name = basename((string)$file);
            if (!preg_match('/^[A-Za-z0-9._-]+\\.html$/', $name)) {
                continue;
            }
            if (!isset($templates[$name])) {
                $templates[$name] = array('file' => $name, 'module' => false, 'override' => false);
            }
            $templates[$name][$source] = true;
        }
    }
    ksort($templates, SORT_NATURAL | SORT_FLAG_CASE);
    return $templates;
}

function mits_cn_toast_position_label(string $position): string
{
    $labels = array(
        'bottom-right' => mits_cn_text('MITS_CN_TOAST_BOTTOM_RIGHT', 'Bottom right'),
        'bottom-left' => mits_cn_text('MITS_CN_TOAST_BOTTOM_LEFT', 'Bottom left'),
        'bottom-center' => mits_cn_text('MITS_CN_TOAST_BOTTOM_CENTER', 'Bottom center'),
        'top-right' => mits_cn_text('MITS_CN_TOAST_TOP_RIGHT', 'Top right'),
        'top-left' => mits_cn_text('MITS_CN_TOAST_TOP_LEFT', 'Top left'),
        'top-center' => mits_cn_text('MITS_CN_TOAST_TOP_CENTER', 'Top center')
    );
    return $labels[$position] ?? $position;
}

function mits_cn_save_targets(int $noticeId, array $targets): void
{
    xtc_db_query("DELETE FROM " . TABLE_MITS_CUSTOMERS_NOTICE_TARGETS . " WHERE notice_id = " . $noticeId);
    foreach ($targets as $type => $values) {
        foreach ($values as $value) {
            xtc_db_query(
                "INSERT INTO " . TABLE_MITS_CUSTOMERS_NOTICE_TARGETS . " (notice_id, target_type, target_value)
                 VALUES (" . $noticeId . ", '" . xtc_db_input((string)$type) . "', '" . xtc_db_input((string)$value) . "')"
            );
        }
    }
}

function mits_cn_next_sort_order(): int
{
    $query = xtc_db_query("SELECT COALESCE(MAX(sort_order), 0) AS max_sort FROM " . TABLE_MITS_CUSTOMERS_NOTICE);
    $row = xtc_db_fetch_array($query);
    return ((int)$row['max_sort']) + 10;
}

function mits_cn_load_notice(int $noticeId, array $languages): array
{
    $notice = array();
    if ($noticeId > 0) {
        $query = xtc_db_query("SELECT * FROM " . TABLE_MITS_CUSTOMERS_NOTICE . " WHERE notice_id = " . $noticeId . " LIMIT 1");
        if (xtc_db_num_rows($query) > 0) {
            $notice = xtc_db_fetch_array($query);
        }
    }
    if (empty($notice)) {
        $notice = array(
            'notice_id' => 0,
            'internal_title' => '',
            'status' => 1,
            'sort_order' => mits_cn_next_sort_order(),
            'start_at' => null,
            'end_at' => null,
            'display_type' => 'notice',
            'template_file' => '',
            'placement' => 'auto',
            'selector' => '',
            'insert_method' => 'prepend',
            'audience' => 'all',
            'frequency' => 'always',
            'dismissible' => 0,
            'countdown' => 0,
            'no_snippet' => 1,
            'toast_position' => 'bottom-right',
            'css_class' => '',
            'legacy_notice_id' => null,
            'description' => array(),
            'targets' => array()
        );
    }

    $notice['description'] = array();
    foreach ($languages as $language) {
        $notice['description'][(int)$language['id']] = array('title' => '', 'content' => '', 'button_text' => '', 'button_url' => '');
    }
    if ($noticeId > 0) {
        $descQuery = xtc_db_query("SELECT * FROM " . TABLE_MITS_CUSTOMERS_NOTICE_DESCRIPTION . " WHERE notice_id = " . $noticeId);
        while ($desc = xtc_db_fetch_array($descQuery)) {
            $notice['description'][(int)$desc['language_id']] = $desc;
        }
    }

    $notice['targets'] = array();
    if ($noticeId > 0) {
        $targetQuery = xtc_db_query("SELECT target_type, target_value FROM " . TABLE_MITS_CUSTOMERS_NOTICE_TARGETS . " WHERE notice_id = " . $noticeId . " ORDER BY target_id");
        while ($target = xtc_db_fetch_array($targetQuery)) {
            $notice['targets'][(string)$target['target_type']][] = (string)$target['target_value'];
        }
    }
    return $notice;
}

function mits_cn_type_label(string $type): string
{
    $labels = array(
        'notice' => mits_cn_text('MITS_CN_TYPE_NOTICE', 'Notice'),
        'important' => mits_cn_text('MITS_CN_TYPE_IMPORTANT', 'Important'),
        'topbar' => mits_cn_text('MITS_CN_TYPE_TOPBAR', 'Top bar'),
        'modal' => mits_cn_text('MITS_CN_TYPE_MODAL', 'Modal'),
        'toast' => mits_cn_text('MITS_CN_TYPE_TOAST', 'Toast')
    );
    return $labels[$type] ?? $type;
}

function mits_cn_audience_label(string $audience): string
{
    $labels = array(
        'all' => mits_cn_text('MITS_CN_AUDIENCE_ALL', 'Everyone'),
        'guest' => mits_cn_text('MITS_CN_AUDIENCE_GUEST', 'Guests'),
        'logged_in' => mits_cn_text('MITS_CN_AUDIENCE_LOGGED_IN', 'Logged-in customers')
    );
    return $labels[$audience] ?? $audience;
}

function mits_cn_frequency_label(string $frequency): string
{
    $labels = array(
        'always' => mits_cn_text('MITS_CN_FREQUENCY_ALWAYS', 'Always'),
        'session' => mits_cn_text('MITS_CN_FREQUENCY_SESSION', 'Once per session'),
        'day' => mits_cn_text('MITS_CN_FREQUENCY_DAY', 'Once per day'),
        'dismiss' => mits_cn_text('MITS_CN_FREQUENCY_DISMISS', 'Until dismissed')
    );
    return $labels[$frequency] ?? $frequency;
}

function mits_cn_time_state(array $row): array
{
    $now = time();
    $start = !empty($row['start_at']) ? strtotime((string)$row['start_at']) : false;
    $end = !empty($row['end_at']) ? strtotime((string)$row['end_at']) : false;
    if ($start !== false && $start > $now) {
        return array('scheduled', mits_cn_text('MITS_CN_TIME_SCHEDULED', 'Scheduled'));
    }
    if ($end !== false && $end < $now) {
        return array('expired', mits_cn_text('MITS_CN_TIME_EXPIRED', 'Expired'));
    }
    return array('current', mits_cn_text('MITS_CN_TIME_CURRENT', 'Current'));
}

function mits_cn_query(array $values): string
{
    $clean = array();
    foreach ($values as $key => $value) {
        if ($value !== '' && $value !== null) {
            $clean[$key] = $value;
        }
    }
    return http_build_query($clean, '', '&');
}

$moduleInstalled = defined('TABLE_MITS_CUSTOMERS_NOTICE') && mits_cn_table_exists(TABLE_MITS_CUSTOMERS_NOTICE);
$schemaReady = $moduleInstalled
    && mits_cn_column_exists(TABLE_MITS_CUSTOMERS_NOTICE, 'internal_title')
    && mits_cn_column_exists(TABLE_MITS_CUSTOMERS_NOTICE, 'no_snippet')
    && mits_cn_column_exists(TABLE_MITS_CUSTOMERS_NOTICE, 'toast_position');
$action = isset($_GET['action']) ? (string)$_GET['action'] : '';
$noticeId = isset($_POST['nid']) ? (int)$_POST['nid'] : (isset($_GET['nid']) ? (int)$_GET['nid'] : 0);
$languages = xtc_get_languages();
$currentLanguageId = (int)$_SESSION['languages_id'];
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$cfgMaxDisplayResultsKey = 'MODULE_MITS_CUSTOMERS_NOTICE_MAX_DISPLAY_RESULTS';
$pageMaxDisplayResults = function_exists('xtc_cfg_save_max_display_results') ? (int)xtc_cfg_save_max_display_results($cfgMaxDisplayResultsKey) : 20;
if ($pageMaxDisplayResults <= 0) {
    $pageMaxDisplayResults = 20;
}
$availableTemplates = mits_cn_available_templates();

$search = trim((string)($_GET['search'] ?? ''));
$statusFilter = isset($_GET['status']) && in_array((string)$_GET['status'], array('0', '1'), true) ? (string)$_GET['status'] : '';
$typeFilter = isset($_GET['type']) && in_array((string)$_GET['type'], array('notice', 'important', 'topbar', 'modal', 'toast'), true) ? (string)$_GET['type'] : '';
$audienceFilter = isset($_GET['audience']) && in_array((string)$_GET['audience'], array('all', 'guest', 'logged_in'), true) ? (string)$_GET['audience'] : '';
$timeFilter = isset($_GET['time']) && in_array((string)$_GET['time'], array('current', 'scheduled', 'expired', 'unlimited'), true) ? (string)$_GET['time'] : '';
$sortKey = isset($_GET['sort']) && in_array((string)$_GET['sort'], array('position', 'title', 'status', 'type', 'start', 'end', 'updated'), true) ? (string)$_GET['sort'] : 'position';
$sortDir = isset($_GET['dir']) && strtolower((string)$_GET['dir']) === 'desc' ? 'desc' : 'asc';
$listParams = array('search' => $search, 'status' => $statusFilter, 'type' => $typeFilter, 'audience' => $audienceFilter, 'time' => $timeFilter, 'sort' => $sortKey, 'dir' => $sortDir, 'page' => $page);
$listQueryString = mits_cn_query($listParams);

if ($schemaReady) {
    if ($action === 'toggle' && $_SERVER['REQUEST_METHOD'] === 'POST' && $noticeId > 0) {
        $flag = isset($_POST['flag']) && (int)$_POST['flag'] === 1 ? 1 : 0;
        xtc_db_query("UPDATE " . TABLE_MITS_CUSTOMERS_NOTICE . " SET status = " . $flag . ", updated_at = NOW() WHERE notice_id = " . $noticeId);
        xtc_redirect(xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE, $listQueryString));
    }

    if ($action === 'sort' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $ids = mits_cn_int_values($_POST['sort_ids'] ?? array());
        $sortPage = isset($_POST['sort_page']) ? max(1, (int)$_POST['sort_page']) : 1;
        $allIds = array();
        $allQuery = xtc_db_query("SELECT notice_id FROM " . TABLE_MITS_CUSTOMERS_NOTICE . " ORDER BY sort_order, notice_id");
        while ($allRow = xtc_db_fetch_array($allQuery)) {
            $allIds[] = (string)(int)$allRow['notice_id'];
        }
        $offset = ($sortPage - 1) * $pageMaxDisplayResults;
        $pageIds = array_slice($allIds, $offset, $pageMaxDisplayResults);
        $submitted = $ids;
        $expected = $pageIds;
        sort($submitted, SORT_NUMERIC);
        sort($expected, SORT_NUMERIC);
        if (!empty($ids) && $submitted === $expected && count($ids) === count($pageIds)) {
            array_splice($allIds, $offset, count($pageIds), $ids);
            foreach ($allIds as $index => $id) {
                xtc_db_query("UPDATE " . TABLE_MITS_CUSTOMERS_NOTICE . " SET sort_order = " . (($index + 1) * 10) . " WHERE notice_id = " . (int)$id);
            }
            $messageStack->add_session(mits_cn_text('MITS_CN_SORT_SAVED', 'Sort order saved.'), 'success');
        } else {
            $messageStack->add_session(mits_cn_text('MITS_CN_SORT_FAILED', 'Sort order could not be saved. Please reload the list.'), 'error');
        }
        xtc_redirect(xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE, 'page=' . $sortPage));
    }

    if ($action === 'delete_confirm' && $_SERVER['REQUEST_METHOD'] === 'POST' && $noticeId > 0) {
        xtc_db_query("DELETE FROM " . TABLE_MITS_CUSTOMERS_NOTICE_TARGETS . " WHERE notice_id = " . $noticeId);
        xtc_db_query("DELETE FROM " . TABLE_MITS_CUSTOMERS_NOTICE_DESCRIPTION . " WHERE notice_id = " . $noticeId);
        xtc_db_query("DELETE FROM " . TABLE_MITS_CUSTOMERS_NOTICE . " WHERE notice_id = " . $noticeId);
        $messageStack->add_session(mits_cn_text('MITS_CN_DELETED', 'The customer notice has been deleted.'), 'success');
        xtc_redirect(xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE, $listQueryString));
    }

    if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $editId = isset($_POST['notice_id']) ? (int)$_POST['notice_id'] : 0;
        $internalTitle = trim((string)($_POST['internal_title'] ?? ''));
        if ($internalTitle === '') {
            $messageStack->add_session(mits_cn_text('MITS_CN_ERROR_INTERNAL_TITLE', 'Please enter an internal title.'), 'error');
            xtc_redirect(xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE, ($editId > 0 ? 'action=edit&nid=' . $editId : 'action=new') . ($listQueryString !== '' ? '&' . $listQueryString : '')));
        }
        if (function_exists('mb_substr')) {
            $internalTitle = mb_substr($internalTitle, 0, 255, 'UTF-8');
        } else {
            $internalTitle = substr($internalTitle, 0, 255);
        }
        $status = isset($_POST['status']) ? 1 : 0;
        $sortOrder = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : mits_cn_next_sort_order();
        $displayType = isset($_POST['display_type']) && in_array($_POST['display_type'], array('notice', 'important', 'topbar', 'modal', 'toast'), true) ? $_POST['display_type'] : 'notice';
        $placement = isset($_POST['placement']) && $_POST['placement'] === 'manual' ? 'manual' : 'auto';
        $insertMethod = isset($_POST['insert_method']) && in_array($_POST['insert_method'], array('before', 'prepend', 'append', 'after'), true) ? $_POST['insert_method'] : 'prepend';
        $audience = isset($_POST['audience']) && in_array($_POST['audience'], array('all', 'guest', 'logged_in'), true) ? $_POST['audience'] : 'all';
        $frequency = isset($_POST['frequency']) && in_array($_POST['frequency'], array('always', 'session', 'day', 'dismiss'), true) ? $_POST['frequency'] : 'always';
        $dismissible = isset($_POST['dismissible']) ? 1 : 0;
        $countdown = isset($_POST['countdown']) ? 1 : 0;
        $noSnippet = isset($_POST['no_snippet']) ? 1 : 0;
        $toastPositions = array('bottom-right', 'bottom-left', 'bottom-center', 'top-right', 'top-left', 'top-center');
        $toastPosition = isset($_POST['toast_position']) && in_array((string)$_POST['toast_position'], $toastPositions, true) ? (string)$_POST['toast_position'] : 'bottom-right';
        $selector = trim((string)($_POST['selector'] ?? ''));
        $cssClass = preg_replace('/[^A-Za-z0-9 _-]/', '', (string)($_POST['css_class'] ?? ''));
        $templateFile = basename(trim((string)($_POST['template_file'] ?? '')));
        if ($templateFile !== '' && !isset($availableTemplates[$templateFile])) {
            $templateFile = '';
        }
        $saveMode = isset($_POST['save_mode']) && $_POST['save_mode'] === 'continue' ? 'continue' : 'overview';
        $startAt = mits_cn_date_from_input($_POST['start_at'] ?? '');
        $endAt = mits_cn_date_from_input($_POST['end_at'] ?? '');
        $startSql = $startAt === null ? 'NULL' : "'" . xtc_db_input($startAt) . "'";
        $endSql = $endAt === null ? 'NULL' : "'" . xtc_db_input($endAt) . "'";

        if ($editId > 0) {
            xtc_db_query(
                "UPDATE " . TABLE_MITS_CUSTOMERS_NOTICE . " SET
                    internal_title = '" . xtc_db_input($internalTitle) . "',
                    status = " . $status . ",
                    sort_order = " . $sortOrder . ",
                    start_at = " . $startSql . ",
                    end_at = " . $endSql . ",
                    display_type = '" . xtc_db_input($displayType) . "',
                    template_file = '" . xtc_db_input($templateFile) . "',
                    placement = '" . xtc_db_input($placement) . "',
                    selector = '" . xtc_db_input($selector) . "',
                    insert_method = '" . xtc_db_input($insertMethod) . "',
                    audience = '" . xtc_db_input($audience) . "',
                    frequency = '" . xtc_db_input($frequency) . "',
                    dismissible = " . $dismissible . ",
                    countdown = " . $countdown . ",
                    no_snippet = " . $noSnippet . ",
                    toast_position = '" . xtc_db_input($toastPosition) . "',
                    css_class = '" . xtc_db_input($cssClass) . "',
                    updated_at = NOW()
                 WHERE notice_id = " . $editId
            );
            $savedId = $editId;
        } else {
            xtc_db_query(
                "INSERT INTO " . TABLE_MITS_CUSTOMERS_NOTICE . "
                    (internal_title, status, sort_order, start_at, end_at, display_type, template_file, placement, selector, insert_method, audience, frequency, dismissible, countdown, no_snippet, toast_position, css_class, created_at, updated_at)
                 VALUES
                    ('" . xtc_db_input($internalTitle) . "', " . $status . ", " . $sortOrder . ", " . $startSql . ", " . $endSql . ", '" . xtc_db_input($displayType) . "', '" . xtc_db_input($templateFile) . "', '" . xtc_db_input($placement) . "', '" . xtc_db_input($selector) . "', '" . xtc_db_input($insertMethod) . "', '" . xtc_db_input($audience) . "', '" . xtc_db_input($frequency) . "', " . $dismissible . ", " . $countdown . ", " . $noSnippet . ", '" . xtc_db_input($toastPosition) . "', '" . xtc_db_input($cssClass) . "', NOW(), NOW())"
            );
            $savedId = (int)xtc_db_insert_id();
        }

        foreach ($languages as $language) {
            $languageId = (int)$language['id'];
            $title = trim((string)($_POST['title'][$languageId] ?? ''));
            $content = (string)($_POST['content'][$languageId] ?? '');
            $buttonText = (string)($_POST['button_text'][$languageId] ?? '');
            $buttonUrl = (string)($_POST['button_url'][$languageId] ?? '');
            xtc_db_query("DELETE FROM " . TABLE_MITS_CUSTOMERS_NOTICE_DESCRIPTION . " WHERE notice_id = " . $savedId . " AND language_id = " . $languageId);
            if ($title !== '' || trim($content) !== '' || trim($buttonText) !== '' || trim($buttonUrl) !== '') {
                xtc_db_query(
                    "INSERT INTO " . TABLE_MITS_CUSTOMERS_NOTICE_DESCRIPTION . " (notice_id, language_id, title, content, button_text, button_url)
                     VALUES (" . $savedId . ", " . $languageId . ", '" . xtc_db_input($title) . "', '" . xtc_db_input($content) . "', '" . xtc_db_input($buttonText) . "', '" . xtc_db_input($buttonUrl) . "')"
                );
            }
        }

        $targets = array(
            'customer_status' => mits_cn_int_values($_POST['target_customer_status'] ?? array(), true),
            'country' => mits_cn_int_values($_POST['target_country'] ?? array()),
            'page' => mits_cn_string_values($_POST['target_page'] ?? array()),
            'customer_id' => mits_cn_int_values($_POST['target_customer_ids'] ?? ''),
            'category_id' => mits_cn_int_values($_POST['target_category_ids'] ?? ''),
            'product_id' => mits_cn_int_values($_POST['target_product_ids'] ?? ''),
            'manufacturer_id' => mits_cn_int_values($_POST['target_manufacturer_ids'] ?? '')
        );
        $newsletter = isset($_POST['target_newsletter']) ? (string)$_POST['target_newsletter'] : '';
        if (in_array($newsletter, array('subscribed', 'unsubscribed'), true)) {
            $targets['newsletter'] = array($newsletter);
        }
        mits_cn_save_targets($savedId, $targets);

        $messageStack->add_session(mits_cn_text('MITS_CN_SAVED', 'The customer notice has been saved.'), 'success');
        if ($saveMode === 'continue') {
            xtc_redirect(xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE, 'action=edit&nid=' . $savedId . ($listQueryString !== '' ? '&' . $listQueryString : '')));
        }
        xtc_redirect(xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE, $listQueryString));
    }

    if ($action === 'migrate' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!mits_cn_table_exists('customers_notice') || !mits_cn_table_exists('customers_notice_description')) {
            $messageStack->add_session(mits_cn_text('MITS_CN_MIGRATION_NONE', 'No importable legacy data was found.'), 'error');
            xtc_redirect(xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE));
        }

        $imported = 0;
        $skipped = 0;
        $legacyQuery = xtc_db_query("SELECT * FROM customers_notice ORDER BY customers_notice_id");
        while ($legacy = xtc_db_fetch_array($legacyQuery)) {
            $legacyId = (int)$legacy['customers_notice_id'];
            $existsQuery = xtc_db_query("SELECT notice_id FROM " . TABLE_MITS_CUSTOMERS_NOTICE . " WHERE legacy_notice_id = " . $legacyId . " LIMIT 1");
            if (xtc_db_num_rows($existsQuery) > 0) {
                $skipped++;
                continue;
            }

            $legacyDescriptions = array();
            $legacyDescQuery = xtc_db_query("SELECT * FROM customers_notice_description WHERE customers_notice_id = " . $legacyId);
            while ($legacyDesc = xtc_db_fetch_array($legacyDescQuery)) {
                $legacyDescriptions[(int)$legacyDesc['languages_id']] = $legacyDesc;
            }
            $internalTitle = mits_cn_legacy_internal_title($legacyId, $legacyDescriptions, $currentLanguageId);
            $legacyTemplate = isset($legacy['template']) ? basename((string)$legacy['template']) : '';
            $legacyMap = mits_cn_map_legacy_template($legacyTemplate);
            $displayType = $legacyMap['display_type'];
            $countdown = (int)$legacyMap['countdown'];
            $frequency = $legacyMap['frequency'];
            $audience = $legacyMap['audience'];
            $startAt = !empty($legacy['startdate']) && $legacy['startdate'] !== '0000-00-00 00:00:00' ? (string)$legacy['startdate'] : null;
            $endAt = !empty($legacy['enddate']) && $legacy['enddate'] !== '0000-00-00 00:00:00' ? (string)$legacy['enddate'] : null;
            $startSql = $startAt === null ? 'NULL' : "'" . xtc_db_input($startAt) . "'";
            $endSql = $endAt === null ? 'NULL' : "'" . xtc_db_input($endAt) . "'";

            xtc_db_query(
                "INSERT INTO " . TABLE_MITS_CUSTOMERS_NOTICE . "
                    (internal_title, status, sort_order, start_at, end_at, display_type, template_file, placement, selector, insert_method, audience, frequency, dismissible, countdown, no_snippet, toast_position, css_class, legacy_notice_id, created_at, updated_at)
                 VALUES
                    ('" . xtc_db_input($internalTitle) . "', " . (isset($legacy['status']) ? (int)$legacy['status'] : 0) . ", " . (isset($legacy['position']) ? (int)$legacy['position'] : 0) . ", " . $startSql . ", " . $endSql . ", '" . xtc_db_input($displayType) . "', '', 'auto', '', 'prepend', '" . xtc_db_input($audience) . "', '" . xtc_db_input($frequency) . "', 0, " . $countdown . ", 1, 'bottom-right', '', " . $legacyId . ", NOW(), NOW())"
            );
            $newId = (int)xtc_db_insert_id();

            foreach ($legacyDescriptions as $legacyDesc) {
                $frontendTitle = mits_cn_clean_legacy_frontend_title($legacyDesc['title'] ?? '');
                xtc_db_query(
                    "INSERT INTO " . TABLE_MITS_CUSTOMERS_NOTICE_DESCRIPTION . " (notice_id, language_id, title, content, button_text, button_url)
                     VALUES (" . $newId . ", " . (int)$legacyDesc['languages_id'] . ", '" . xtc_db_input($frontendTitle) . "', '" . xtc_db_input((string)$legacyDesc['description']) . "', '', '')"
                );
            }

            $targets = array();
            $specificCustomerId = isset($legacy['customers_id']) ? (int)$legacy['customers_id'] : 0;
            if ($specificCustomerId > 0) {
                $targets['customer_id'] = array((string)$specificCustomerId);
            } else {
                $targets['customer_status'] = mits_cn_int_values($legacy['customers_status'] ?? '', true);
                $targets['country'] = mits_cn_int_values($legacy['countries'] ?? '');
            }
            $targets['page'] = mits_cn_string_values($legacy['pages'] ?? '');
            if ($legacyTemplate === 'newsletter.html') {
                $targets['newsletter'] = array('unsubscribed');
            }
            mits_cn_save_targets($newId, $targets);
            $imported++;
        }

        $messageStack->add_session(sprintf(mits_cn_text('MITS_CN_MIGRATION_DONE', '%d notices imported, %d existing records skipped.'), $imported, $skipped), 'success');
        $messageStack->add_session(mits_cn_text('MITS_CN_MIGRATION_DISABLE_OLD', 'After checking the imported notices, disable the storefront output of the old module to prevent duplicate notices.'), 'warning');
        xtc_redirect(xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE));
    }
}

$editMode = $schemaReady && in_array($action, array('new', 'edit'), true);
$migrationPreview = $schemaReady && $action === 'migration_preview' && mits_cn_table_exists('customers_notice') && mits_cn_table_exists('customers_notice_description');
$notice = $editMode ? mits_cn_load_notice($noticeId, $languages) : array();

$customerStatuses = array();
$countries = array();
if ($schemaReady) {
    $statusQuery = xtc_db_query("SELECT customers_status_id, customers_status_name FROM " . TABLE_CUSTOMERS_STATUS . " WHERE language_id = " . $currentLanguageId . " ORDER BY customers_status_id");
    while ($row = xtc_db_fetch_array($statusQuery)) {
        $customerStatuses[] = $row;
    }
    $countryQuery = xtc_db_query("SELECT countries_id, countries_name FROM " . TABLE_COUNTRIES . " ORDER BY countries_name");
    while ($row = xtc_db_fetch_array($countryQuery)) {
        $countries[] = $row;
    }
}

$pageOptions = array(
    'index' => mits_cn_text('MITS_CN_PAGE_HOME', 'Home'),
    'category' => mits_cn_text('MITS_CN_PAGE_CATEGORY', 'Category'),
    'product_info' => mits_cn_text('MITS_CN_PAGE_PRODUCT', 'Product details'),
    'shop_content' => mits_cn_text('MITS_CN_PAGE_CONTENT', 'Content pages'),
    'shopping_cart' => mits_cn_text('MITS_CN_PAGE_CART', 'Shopping cart'),
    'account' => mits_cn_text('MITS_CN_PAGE_ACCOUNT', 'Account'),
    'checkout' => mits_cn_text('MITS_CN_PAGE_CHECKOUT', 'Checkout'),
    'login' => mits_cn_text('MITS_CN_PAGE_LOGIN', 'Login'),
    'create_account' => mits_cn_text('MITS_CN_PAGE_CREATE_ACCOUNT', 'Create account'),
    'advanced_search' => mits_cn_text('MITS_CN_PAGE_SEARCH', 'Search')
);

$legacyCount = 0;
if ($schemaReady && mits_cn_table_exists('customers_notice') && mits_cn_table_exists('customers_notice_description')) {
    $legacyCountQuery = xtc_db_query("SELECT COUNT(*) AS total FROM customers_notice cn LEFT JOIN " . TABLE_MITS_CUSTOMERS_NOTICE . " mn ON mn.legacy_notice_id = cn.customers_notice_id WHERE mn.notice_id IS NULL");
    if ($legacyCountRow = xtc_db_fetch_array($legacyCountQuery)) {
        $legacyCount = (int)$legacyCountRow['total'];
    }
}

$stats = array('total' => 0, 'enabled' => 0, 'current' => 0, 'scheduled' => 0);
if ($schemaReady) {
    $statsQuery = xtc_db_query(
        "SELECT COUNT(*) AS total,
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS enabled,
                SUM(CASE WHEN status = 1 AND (start_at IS NULL OR start_at <= NOW()) AND (end_at IS NULL OR end_at >= NOW()) THEN 1 ELSE 0 END) AS current_count,
                SUM(CASE WHEN status = 1 AND start_at IS NOT NULL AND start_at > NOW() THEN 1 ELSE 0 END) AS scheduled
           FROM " . TABLE_MITS_CUSTOMERS_NOTICE
    );
    $statsRow = xtc_db_fetch_array($statsQuery);
    $stats = array(
        'total' => (int)$statsRow['total'],
        'enabled' => (int)$statsRow['enabled'],
        'current' => (int)$statsRow['current_count'],
        'scheduled' => (int)$statsRow['scheduled']
    );
}

require DIR_WS_INCLUDES . 'head.php';

if ($editMode && defined('USE_WYSIWYG') && USE_WYSIWYG == 'true') {
    $codeQuery = xtc_db_query("SELECT code FROM " . TABLE_LANGUAGES . " WHERE languages_id = " . $currentLanguageId . " LIMIT 1");
    $codeRow = xtc_db_fetch_array($codeQuery);
    echo PHP_EOL . (!function_exists('editorJSLink') ? '<script type="text/javascript" src="includes/modules/ckeditor/ckeditor.js"></script>' : '') . PHP_EOL;
    foreach ($languages as $language) {
        echo xtc_wysiwyg('mits_customers_notice', (string)$codeRow['code'], (int)$language['id']);
    }
}
?>
<style>
.mits-cn-admin{--mits-ci-primary:#6a9;--mits-ci-primary-dark:#4f8e7e;--mits-ci-primary-soft:#edf7f4;--mits-ci-primary-soft-2:#f7fbfa;--mits-ci-line:#d4e7e0;--mits-ci-line-strong:#b9d6cb;--mits-ci-ink:#444;--mits-ci-heading:#30534b;--mits-ci-muted:#6d7b77;--mits-ci-shadow:rgba(76,110,101,.10);--mits-ci-danger-bg:#fdeeed;--mits-ci-danger-text:#a3483f;--mits-ci-warning-bg:#fff8e5;--mits-ci-warning-line:#f0d28a;--mits-ci-warning-text:#7a5a00;--mits-ci-success-bg:#e9f7f1;--mits-ci-success-text:#2c715d;padding:18px 18px 28px;color:var(--mits-ci-ink);font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.45}
.mits-cn-admin *{box-sizing:border-box}.mits-cn-admin input,.mits-cn-admin select,.mits-cn-admin textarea,.mits-cn-admin button{font-family:Arial,Helvetica,sans-serif}
.mits-cn-hero{display:flex;justify-content:space-between;gap:18px;align-items:flex-start;padding:24px;border:1px solid var(--mits-ci-line);border-radius:20px;background:linear-gradient(135deg,#fff 0%,var(--mits-ci-primary-soft) 100%);box-shadow:0 10px 25px var(--mits-ci-shadow);margin-bottom:18px}.mits-cn-hero h1{margin:0 0 7px;font-size:26px;line-height:1.2;color:var(--mits-ci-heading)}.mits-cn-hero p{margin:0;color:var(--mits-ci-muted);max-width:820px;line-height:1.55}.mits-cn-hero__brand{display:inline-block;margin-bottom:8px;padding:4px 8px;border-radius:999px;background:var(--mits-ci-primary-dark);color:#fff;font-size:10px;font-weight:700;letter-spacing:.12em}.mits-cn-hero__actions{display:flex;gap:9px;flex-wrap:wrap;justify-content:flex-end}
.mits-cn-button{display:inline-flex;align-items:center;justify-content:center;gap:6px;box-sizing:border-box;height:34px;min-height:34px;margin:0;padding:0 13px;border:1px solid var(--mits-ci-primary-dark);border-radius:10px;background:var(--mits-ci-primary-dark);color:#fff!important;text-decoration:none!important;font-family:Arial,Helvetica,sans-serif;font-weight:700;font-size:12px;line-height:1;vertical-align:middle;white-space:nowrap;cursor:pointer;box-shadow:none;-webkit-appearance:none;appearance:none}.mits-cn-button::-moz-focus-inner{border:0;padding:0}.mits-cn-button:hover{background:#3f766a;color:#fff!important}.mits-cn-button--soft{background:#fff;color:var(--mits-ci-heading)!important;border-color:var(--mits-ci-line-strong)}.mits-cn-button--soft:hover{background:var(--mits-ci-primary-soft);color:var(--mits-ci-heading)!important}.mits-cn-button--danger{background:#fff;color:var(--mits-ci-danger-text)!important;border-color:#e7c4bf}.mits-cn-button--danger:hover{background:var(--mits-ci-danger-bg);color:var(--mits-ci-danger-text)!important}.mits-cn-button--small{height:29px;min-height:29px;padding:0 9px;font-size:11px}
.mits-cn-stats{display:grid;grid-template-columns:repeat(4,minmax(150px,1fr));gap:14px;margin-bottom:18px}.mits-cn-stat{padding:17px 18px;border-radius:18px;border:1px solid var(--mits-ci-line);background:#fff;box-shadow:0 8px 22px var(--mits-ci-shadow)}.mits-cn-stat__label{display:block;font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--mits-ci-muted);margin-bottom:7px}.mits-cn-stat__value{display:block;font-size:25px;font-weight:700;color:var(--mits-ci-heading);line-height:1.1}.mits-cn-stat__meta{display:block;margin-top:7px;color:var(--mits-ci-muted);font-size:11px}
.mits-cn-card{background:#fff;border:1px solid var(--mits-ci-line);border-radius:20px;box-shadow:0 10px 24px var(--mits-ci-shadow);overflow:hidden;margin-bottom:18px}.mits-cn-card__header{display:flex;justify-content:space-between;gap:12px;align-items:flex-start;padding:17px 21px;border-bottom:1px solid var(--mits-ci-line);background:var(--mits-ci-primary-soft-2)}.mits-cn-card__title{margin:0;font-size:18px;color:var(--mits-ci-heading)}.mits-cn-card__subtitle{margin:5px 0 0;color:var(--mits-ci-muted);font-size:12px;line-height:1.5}.mits-cn-card__body{padding:20px 21px}.mits-cn-card__tools{display:flex;gap:8px;align-items:center;flex-wrap:wrap;justify-content:flex-end}
.mits-cn-alert{padding:14px 16px;border-radius:16px;border:1px solid var(--mits-ci-line);background:#fff;margin-bottom:16px;line-height:1.55}.mits-cn-alert strong{color:var(--mits-ci-heading)}.mits-cn-alert--warning{border-color:var(--mits-ci-warning-line);background:var(--mits-ci-warning-bg);color:var(--mits-ci-warning-text)}.mits-cn-alert--success{border-color:#c7dfd6;background:var(--mits-ci-success-bg);color:var(--mits-ci-success-text)}.mits-cn-alert--info{background:var(--mits-ci-primary-soft-2)}
.mits-cn-filter{display:grid;grid-template-columns:minmax(220px,2fr) repeat(4,minmax(135px,1fr)) auto;gap:10px;align-items:end}.mits-cn-field label{display:block;margin:0 0 5px;font-weight:700;color:var(--mits-ci-heading);font-size:12px}.mits-cn-field small{display:block;margin-top:5px;color:var(--mits-ci-muted);line-height:1.45}.mits-cn-field input[type=text],.mits-cn-field input[type=url],.mits-cn-field input[type=number],.mits-cn-field input[type=datetime-local],.mits-cn-field select,.mits-cn-field textarea{width:100%;max-width:100%;min-height:36px;border:1px solid var(--mits-ci-line-strong);border-radius:10px;background:#fff;padding:7px 9px;color:var(--mits-ci-ink)}.mits-cn-field textarea{min-height:110px;resize:vertical}.mits-cn-field input:focus,.mits-cn-field select:focus,.mits-cn-field textarea:focus{outline:0;border-color:var(--mits-ci-primary-dark);box-shadow:0 0 0 2px var(--mits-ci-primary-soft)}.mits-cn-required{color:#a3483f}.mits-cn-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px 18px}.mits-cn-form-grid--3{grid-template-columns:repeat(3,minmax(0,1fr))}.mits-cn-span-2{grid-column:span 2}.mits-cn-span-3{grid-column:span 3}
.mits-cn-switches{display:flex;gap:18px;flex-wrap:wrap;padding:12px 13px;border:1px solid var(--mits-ci-line);border-radius:12px;background:#fcfefd}.mits-cn-switches label{display:flex;align-items:center;gap:7px;font-weight:700;color:var(--mits-ci-heading)}.mits-cn-switches input{margin:0}
.mits-cn-lang-tabs{display:flex;gap:7px;flex-wrap:wrap;margin-bottom:13px}.mits-cn-lang-tab{display:flex;align-items:center;gap:7px;border:1px solid var(--mits-ci-line-strong);border-radius:10px;background:#fff;padding:8px 11px;color:var(--mits-ci-heading);cursor:pointer;font-weight:700}.mits-cn-lang-tab.active{background:var(--mits-ci-primary-dark);border-color:var(--mits-ci-primary-dark);color:#fff}.mits-cn-lang-tab img{width:auto;max-width:22px;max-height:14px}.mits-cn-lang-panel{display:none}.mits-cn-lang-panel.active{display:block}
.mits-cn-check-title{margin:18px 0 7px;font-size:13px;color:var(--mits-ci-heading)}.mits-cn-check-list{max-height:230px;overflow:auto;border:1px solid var(--mits-ci-line);border-radius:14px;background:#fff;padding:7px;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:2px 8px}.mits-cn-check-row{display:flex;align-items:center;gap:7px;padding:7px 8px;border-radius:9px;color:var(--mits-ci-heading)}.mits-cn-check-row:hover{background:var(--mits-ci-primary-soft-2)}.mits-cn-check-row input{margin:0}
.mits-cn-table-wrap{overflow:auto}.mits-cn-table{width:100%;border-collapse:separate;border-spacing:0;min-width:980px}.mits-cn-table th,.mits-cn-table td{padding:12px 11px;border-bottom:1px solid var(--mits-ci-line);text-align:left;vertical-align:middle}.mits-cn-table th{background:#fbfdfc;color:var(--mits-ci-heading);font-size:11px;text-transform:uppercase;letter-spacing:.035em;white-space:nowrap}.mits-cn-table tbody tr:last-child td{border-bottom:0}.mits-cn-table tbody tr:hover td{background:#fcfefd}.mits-cn-table th a{color:var(--mits-ci-heading)!important;text-decoration:none!important}.mits-cn-table th a:hover{text-decoration:underline!important}.mits-cn-title-main{font-weight:700;color:var(--mits-ci-heading);font-size:13px}.mits-cn-title-meta{margin-top:4px;color:var(--mits-ci-muted);font-size:11px;line-height:1.45}.mits-cn-badges{display:flex;gap:5px;flex-wrap:wrap}.mits-cn-badge{display:inline-flex;align-items:center;padding:4px 7px;border-radius:999px;background:var(--mits-ci-primary-soft);color:var(--mits-ci-heading);font-size:10px;font-weight:700;white-space:nowrap}.mits-cn-badge--off{background:#f1f1f1;color:#777}.mits-cn-badge--current{background:var(--mits-ci-success-bg);color:var(--mits-ci-success-text)}.mits-cn-badge--scheduled{background:var(--mits-ci-warning-bg);color:var(--mits-ci-warning-text)}.mits-cn-badge--expired{background:var(--mits-ci-danger-bg);color:var(--mits-ci-danger-text)}.mits-cn-actions{display:flex;gap:6px;align-items:center;flex-wrap:wrap}.mits-cn-inline-form{display:inline-flex;align-items:center;margin:0;padding:0;line-height:1;vertical-align:middle}.mits-cn-status-toggle{border:0;background:transparent;padding:0;cursor:pointer}.mits-cn-drag-cell{width:38px;text-align:center!important}.mits-cn-drag{display:inline-flex;width:28px;height:28px;align-items:center;justify-content:center;border:1px solid var(--mits-ci-line);border-radius:8px;background:#fff;color:var(--mits-ci-muted);cursor:grab;font-size:18px;line-height:1;user-select:none}.mits-cn-drag:active{cursor:grabbing}.mits-cn-row-dragging{opacity:.45}.mits-cn-row-drop td{background:var(--mits-ci-primary-soft)!important}.mits-cn-empty{padding:34px;text-align:center;color:var(--mits-ci-muted)}
.mits-cn-pagination{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin:0 0 18px;padding:12px 16px;border:1px solid var(--mits-ci-line);border-radius:16px;background:#fff}.mits-cn-pagination .smallText{float:none!important;padding:0!important}.mits-cn-pagination .clear{display:none}.mits-cn-pagination form{display:inline-flex;align-items:center;gap:7px;margin:0}.mits-cn-pagination input[type=text]{height:29px!important;min-height:29px!important;margin:0!important;padding:0 7px!important;border:1px solid var(--mits-ci-line-strong)!important;border-radius:8px!important;background:#fff!important;color:var(--mits-ci-ink)!important;line-height:27px!important;vertical-align:middle!important}.mits-cn-pagination .button{display:inline-flex!important;align-items:center!important;justify-content:center!important;height:29px!important;min-height:29px!important;margin:0!important;padding:0 9px!important;border:1px solid var(--mits-ci-primary-dark)!important;border-radius:8px!important;background:var(--mits-ci-primary-dark)!important;color:#fff!important;font-family:Arial,Helvetica,sans-serif!important;font-size:11px!important;font-weight:700!important;line-height:1!important;vertical-align:middle!important;cursor:pointer!important;box-shadow:none!important;-webkit-appearance:none!important;appearance:none!important}.mits-cn-pagination .button:hover{background:#3f766a!important}
.mits-cn-edit-layout{display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:18px;align-items:start}.mits-cn-edit-main{min-width:0}.mits-cn-edit-side{position:sticky;top:12px}.mits-cn-note-list{margin:0;padding-left:18px}.mits-cn-note-list li{margin-bottom:8px}.mits-cn-code{font-family:Consolas,Monaco,monospace;background:#f4f7f6;padding:2px 5px;border-radius:5px}.mits-cn-footer-actions{display:flex;gap:9px;justify-content:flex-end;margin-top:18px}.mits-cn-preview-notitle{color:var(--mits-ci-muted);font-style:italic}
@media(max-width:1180px){.mits-cn-filter{grid-template-columns:repeat(3,minmax(0,1fr))}.mits-cn-edit-layout{grid-template-columns:1fr}.mits-cn-edit-side{position:static}.mits-cn-stats{grid-template-columns:repeat(2,minmax(150px,1fr))}}
@media(max-width:760px){.mits-cn-admin{padding:12px}.mits-cn-hero{display:block;padding:17px}.mits-cn-hero__actions{justify-content:flex-start;margin-top:14px}.mits-cn-stats,.mits-cn-filter,.mits-cn-form-grid,.mits-cn-form-grid--3{grid-template-columns:1fr}.mits-cn-span-2,.mits-cn-span-3{grid-column:auto}.mits-cn-check-list{grid-template-columns:1fr}.mits-cn-card__body,.mits-cn-card__header{padding:15px}.mits-cn-card__header{display:block}.mits-cn-card__tools{justify-content:flex-start;margin-top:10px}}
</style>
</head>
<body>
<?php require DIR_WS_INCLUDES . 'header.php'; ?>
<table class="tableBody">
<tr>
<?php if (USE_ADMIN_TOP_MENU == 'false') { ?>
<td class="columnLeft2"><?php require_once DIR_WS_INCLUDES . 'column_left.php'; ?></td>
<?php } ?>
<td class="boxCenter" width="100%" valign="top">
<div class="mits-cn-admin">
  <div class="mits-cn-hero">
    <div>
      <span class="mits-cn-hero__brand">MITS MODULE</span>
      <h1><?php echo $editMode ? ($notice['notice_id'] > 0 ? mits_cn_h($notice['internal_title']) : mits_cn_text('MITS_CN_HEADING_NEW', 'Create customer notice')) : mits_cn_text('MITS_CN_HEADING_TITLE', 'MITS Customer Notices'); ?></h1>
      <p><?php echo $editMode ? mits_cn_text('MITS_CN_EDIT_DESC', 'Manage content, display rules and target groups independently.') : mits_cn_text('MITS_CN_HEADING_DESC', 'Scheduled and targeted notices for the storefront'); ?> &middot; v1.0.6</p>
    </div>
    <div class="mits-cn-hero__actions">
      <?php if ($editMode) { ?>
        <a class="mits-cn-button mits-cn-button--soft" href="<?php echo xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE, $listQueryString); ?>"><?php echo mits_cn_text('MITS_CN_BUTTON_BACK', 'Back to overview'); ?></a>
        <button class="mits-cn-button" type="submit" form="mits-cn-edit-form" name="save_mode" value="overview"><?php echo mits_cn_text('MITS_CN_BUTTON_SAVE', 'Save'); ?></button>
        <button class="mits-cn-button mits-cn-button--soft" type="submit" form="mits-cn-edit-form" name="save_mode" value="continue"><?php echo mits_cn_text('MITS_CN_BUTTON_SAVE_CONTINUE', 'Save & continue editing'); ?></button>
      <?php } elseif ($schemaReady) { ?>
        <?php if ($legacyCount > 0) { ?><a class="mits-cn-button mits-cn-button--soft" href="<?php echo xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE, 'action=migration_preview'); ?>"><?php echo sprintf(mits_cn_text('MITS_CN_BUTTON_MIGRATE_COUNT', 'Migration (%d)'), $legacyCount); ?></a><?php } ?>
        <a class="mits-cn-button" href="<?php echo xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE, 'action=new'); ?>"><?php echo mits_cn_text('MITS_CN_BUTTON_NEW', 'Create notice'); ?></a>
      <?php } ?>
    </div>
  </div>

  <?php if (!$moduleInstalled) { ?>
    <div class="mits-cn-alert mits-cn-alert--warning"><strong><?php echo mits_cn_text('MITS_CN_MODULE_MISSING_TITLE', 'Module not installed'); ?></strong><br><?php echo mits_cn_text('MITS_CN_MODULE_MISSING_TEXT', 'Please install the MITS Customer Notices system module first.'); ?></div>
  <?php } elseif (!$schemaReady) { ?>
    <div class="mits-cn-alert mits-cn-alert--warning"><strong><?php echo mits_cn_text('MITS_CN_MODULE_UPDATE_TITLE', 'Module update required'); ?></strong><br><?php echo mits_cn_text('MITS_CN_MODULE_UPDATE_TEXT', 'Please run the update for the MITS Customer Notices system module. The new internal title field is required.'); ?></div>
  <?php } elseif ($editMode) { ?>
    <?php echo xtc_draw_form('mits_cn_notice', FILENAME_MITS_CUSTOMERS_NOTICE, 'action=save' . ($listQueryString !== '' ? '&' . $listQueryString : ''), 'post', 'id="mits-cn-edit-form"'); ?>
      <input type="hidden" name="notice_id" value="<?php echo (int)$notice['notice_id']; ?>">
      <div class="mits-cn-edit-layout">
        <div class="mits-cn-edit-main">
          <div class="mits-cn-card">
            <div class="mits-cn-card__header"><div><h2 class="mits-cn-card__title"><?php echo mits_cn_text('MITS_CN_SECTION_BASIC', 'Basic settings'); ?></h2><p class="mits-cn-card__subtitle"><?php echo mits_cn_text('MITS_CN_SECTION_BASIC_DESC', 'Internal organization, activation and schedule.'); ?></p></div></div>
            <div class="mits-cn-card__body">
              <div class="mits-cn-form-grid">
                <div class="mits-cn-field mits-cn-span-2"><label><?php echo mits_cn_text('MITS_CN_INTERNAL_TITLE', 'Internal title'); ?> <span class="mits-cn-required">*</span></label><input type="text" name="internal_title" maxlength="255" required value="<?php echo mits_cn_h($notice['internal_title']); ?>"><small><?php echo mits_cn_text('MITS_CN_INTERNAL_TITLE_DESC', 'Required for the admin overview only. It is never displayed in the storefront.'); ?></small></div>
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_START_AT', 'Start'); ?></label><input type="datetime-local" name="start_at" value="<?php echo mits_cn_h(mits_cn_date_for_input($notice['start_at'])); ?>"></div>
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_END_AT', 'End'); ?></label><input type="datetime-local" name="end_at" value="<?php echo mits_cn_h(mits_cn_date_for_input($notice['end_at'])); ?>"></div>
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_SORT_ORDER', 'Sort position'); ?></label><input type="number" name="sort_order" value="<?php echo (int)$notice['sort_order']; ?>"><small><?php echo mits_cn_text('MITS_CN_SORT_ORDER_DESC', 'Can also be changed by drag & drop in the overview.'); ?></small></div>
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_AUDIENCE', 'Audience'); ?></label><select name="audience"><option value="all" <?php echo $notice['audience'] === 'all' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_AUDIENCE_ALL', 'Everyone'); ?></option><option value="guest" <?php echo $notice['audience'] === 'guest' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_AUDIENCE_GUEST', 'Guests'); ?></option><option value="logged_in" <?php echo $notice['audience'] === 'logged_in' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_AUDIENCE_LOGGED_IN', 'Logged-in customers'); ?></option></select></div>
              </div>
              <div class="mits-cn-switches" style="margin-top:15px"><label><input type="checkbox" name="status" value="1" <?php echo (int)$notice['status'] === 1 ? 'checked' : ''; ?>> <?php echo mits_cn_text('MITS_CN_STATUS_ACTIVE', 'Active'); ?></label><label><input type="checkbox" name="dismissible" value="1" <?php echo (int)$notice['dismissible'] === 1 ? 'checked' : ''; ?>> <?php echo mits_cn_text('MITS_CN_DISMISSIBLE', 'Dismissible'); ?></label><label><input type="checkbox" name="countdown" value="1" <?php echo (int)$notice['countdown'] === 1 ? 'checked' : ''; ?>> <?php echo mits_cn_text('MITS_CN_COUNTDOWN', 'Show countdown'); ?></label><label><input type="checkbox" name="no_snippet" value="1" <?php echo (int)$notice['no_snippet'] === 1 ? 'checked' : ''; ?>> <?php echo mits_cn_text('MITS_CN_NO_SNIPPET', 'Exclude from search snippets'); ?></label></div>
            </div>
          </div>

          <div class="mits-cn-card">
            <div class="mits-cn-card__header"><div><h2 class="mits-cn-card__title"><?php echo mits_cn_text('MITS_CN_SECTION_CONTENT', 'Storefront content'); ?></h2><p class="mits-cn-card__subtitle"><?php echo mits_cn_text('MITS_CN_SECTION_CONTENT_DESC', 'The storefront headline is optional. Content remains language-specific.'); ?></p></div></div>
            <div class="mits-cn-card__body">
              <div class="mits-cn-lang-tabs">
                <?php foreach ($languages as $index => $language) { ?>
                  <button type="button" class="mits-cn-lang-tab <?php echo $index === 0 ? 'active' : ''; ?>" data-lang-tab="<?php echo (int)$language['id']; ?>"><?php echo xtc_image(DIR_WS_LANGUAGES . $language['directory'] . '/admin/images/' . $language['image'], $language['name']); ?><span><?php echo mits_cn_h($language['name']); ?></span></button>
                <?php } ?>
              </div>
              <?php foreach ($languages as $index => $language) { $lid = (int)$language['id']; $desc = $notice['description'][$lid]; ?>
                <div class="mits-cn-lang-panel <?php echo $index === 0 ? 'active' : ''; ?>" data-lang-panel="<?php echo $lid; ?>">
                  <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_FRONTEND_TITLE', 'Storefront headline'); ?> <span style="font-weight:normal;color:var(--mits-ci-muted)"><?php echo mits_cn_text('MITS_CN_OPTIONAL', 'optional'); ?></span></label><input type="text" name="title[<?php echo $lid; ?>]" value="<?php echo mits_cn_h($desc['title']); ?>"></div>
                  <div class="mits-cn-field" style="margin-top:14px"><label><?php echo mits_cn_text('MITS_CN_CONTENT', 'Content'); ?></label><textarea id="content_<?php echo $lid; ?>" name="content[<?php echo $lid; ?>]" rows="10"><?php echo mits_cn_h($desc['content']); ?></textarea></div>
                  <div class="mits-cn-form-grid" style="margin-top:14px">
                    <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_BUTTON_TEXT', 'Button text'); ?></label><input type="text" name="button_text[<?php echo $lid; ?>]" value="<?php echo mits_cn_h($desc['button_text']); ?>"></div>
                    <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_BUTTON_URL', 'Button URL'); ?></label><input type="text" name="button_url[<?php echo $lid; ?>]" value="<?php echo mits_cn_h($desc['button_url']); ?>"></div>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>

          <div class="mits-cn-card">
            <div class="mits-cn-card__header"><div><h2 class="mits-cn-card__title"><?php echo mits_cn_text('MITS_CN_SECTION_DISPLAY', 'Display'); ?></h2><p class="mits-cn-card__subtitle"><?php echo mits_cn_text('MITS_CN_SECTION_DISPLAY_DESC', 'Select the visual type, placement and display frequency independently from the content.'); ?></p></div></div>
            <div class="mits-cn-card__body">
              <div class="mits-cn-form-grid mits-cn-form-grid--3">
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_DISPLAY_TYPE', 'Display type'); ?></label><select name="display_type" data-mits-cn-display-type-select><option value="notice" <?php echo $notice['display_type'] === 'notice' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_TYPE_NOTICE', 'Notice'); ?></option><option value="important" <?php echo $notice['display_type'] === 'important' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_TYPE_IMPORTANT', 'Important notice'); ?></option><option value="topbar" <?php echo $notice['display_type'] === 'topbar' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_TYPE_TOPBAR', 'Top bar'); ?></option><option value="modal" <?php echo $notice['display_type'] === 'modal' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_TYPE_MODAL', 'Modal'); ?></option><option value="toast" <?php echo $notice['display_type'] === 'toast' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_TYPE_TOAST', 'Toast'); ?></option></select></div>
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_FREQUENCY', 'Frequency'); ?></label><select name="frequency"><option value="always" <?php echo $notice['frequency'] === 'always' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_FREQUENCY_ALWAYS', 'Always'); ?></option><option value="session" <?php echo $notice['frequency'] === 'session' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_FREQUENCY_SESSION', 'Once per session'); ?></option><option value="day" <?php echo $notice['frequency'] === 'day' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_FREQUENCY_DAY', 'Once per day'); ?></option><option value="dismiss" <?php echo $notice['frequency'] === 'dismiss' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_FREQUENCY_DISMISS', 'Until dismissed'); ?></option></select></div>
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_PLACEMENT', 'Placement'); ?></label><select name="placement"><option value="auto" <?php echo $notice['placement'] === 'auto' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_PLACEMENT_AUTO', 'Automatic'); ?></option><option value="manual" <?php echo $notice['placement'] === 'manual' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_PLACEMENT_MANUAL', 'Manual via Smarty'); ?></option></select></div>
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_SELECTOR', 'CSS selector'); ?></label><input type="text" name="selector" value="<?php echo mits_cn_h($notice['selector']); ?>"><small><?php echo mits_cn_text('MITS_CN_SELECTOR_DESC', 'Empty uses the module default selector.'); ?></small></div>
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_INSERT_METHOD', 'Insert method'); ?></label><select name="insert_method"><option value="before" <?php echo $notice['insert_method'] === 'before' ? 'selected' : ''; ?>>before</option><option value="prepend" <?php echo $notice['insert_method'] === 'prepend' ? 'selected' : ''; ?>>prepend</option><option value="append" <?php echo $notice['insert_method'] === 'append' ? 'selected' : ''; ?>>append</option><option value="after" <?php echo $notice['insert_method'] === 'after' ? 'selected' : ''; ?>>after</option></select></div>
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_CSS_CLASS', 'Additional CSS class'); ?></label><input type="text" name="css_class" value="<?php echo mits_cn_h($notice['css_class']); ?>"></div>
                <div class="mits-cn-field" data-mits-cn-toast-position-field><label><?php echo mits_cn_text('MITS_CN_TOAST_POSITION', 'Toast position'); ?></label><select name="toast_position"><?php foreach (array('bottom-right', 'bottom-left', 'bottom-center', 'top-right', 'top-left', 'top-center') as $toastPositionOption) { ?><option value="<?php echo mits_cn_h($toastPositionOption); ?>" <?php echo $notice['toast_position'] === $toastPositionOption ? 'selected' : ''; ?>><?php echo mits_cn_h(mits_cn_toast_position_label($toastPositionOption)); ?></option><?php } ?></select><small><?php echo mits_cn_text('MITS_CN_TOAST_POSITION_DESC', 'Used only for the Toast display type.'); ?></small></div>
                <div class="mits-cn-field mits-cn-span-2"><label><?php echo mits_cn_text('MITS_CN_TEMPLATE_FILE', 'Template'); ?></label><select name="template_file"><option value=""><?php echo mits_cn_text('MITS_CN_TEMPLATE_AUTO', 'Automatic for selected display type'); ?></option><?php foreach ($availableTemplates as $templateName => $templateInfo) { $sourceLabel = !empty($templateInfo['override']) ? mits_cn_text('MITS_CN_TEMPLATE_SOURCE_OVERRIDE', 'Template override') : mits_cn_text('MITS_CN_TEMPLATE_SOURCE_MODULE', 'Module'); ?><option value="<?php echo mits_cn_h($templateName); ?>" <?php echo $notice['template_file'] === $templateName ? 'selected' : ''; ?>><?php echo mits_cn_h($templateName . ' - ' . $sourceLabel); ?></option><?php } ?></select><small><?php echo mits_cn_text('MITS_CN_TEMPLATE_FILE_DESC', 'Only existing templates can be selected. Overrides in the active shop template have priority.'); ?></small></div>
              </div>
            </div>
          </div>

          <div class="mits-cn-card">
            <div class="mits-cn-card__header"><div><h2 class="mits-cn-card__title"><?php echo mits_cn_text('MITS_CN_SECTION_TARGETS', 'Targeting'); ?></h2><p class="mits-cn-card__subtitle"><?php echo mits_cn_text('MITS_CN_SECTION_TARGETS_DESC', 'Empty groups do not restrict the notice. Values inside a group are OR, different groups are AND.'); ?></p></div></div>
            <div class="mits-cn-card__body">
              <h3 class="mits-cn-check-title" style="margin-top:0"><?php echo mits_cn_text('MITS_CN_PAGES', 'Pages'); ?></h3>
              <div class="mits-cn-check-list">
                <?php $selectedPages = $notice['targets']['page'] ?? array(); foreach ($pageOptions as $value => $label) { ?>
                  <label class="mits-cn-check-row"><input type="checkbox" name="target_page[]" value="<?php echo mits_cn_h($value); ?>" <?php echo in_array($value, $selectedPages, true) ? 'checked' : ''; ?>> <span><?php echo mits_cn_h($label); ?></span></label>
                <?php } ?>
              </div>

              <h3 class="mits-cn-check-title"><?php echo mits_cn_text('MITS_CN_CUSTOMER_GROUPS', 'Customer groups'); ?></h3>
              <div class="mits-cn-check-list">
                <?php $selectedStatuses = $notice['targets']['customer_status'] ?? array(); foreach ($customerStatuses as $row) { $sid = (string)$row['customers_status_id']; ?>
                  <label class="mits-cn-check-row"><input type="checkbox" name="target_customer_status[]" value="<?php echo mits_cn_h($sid); ?>" <?php echo in_array($sid, $selectedStatuses, true) ? 'checked' : ''; ?>> <span><?php echo mits_cn_h($row['customers_status_name']); ?> (#<?php echo mits_cn_h($sid); ?>)</span></label>
                <?php } ?>
              </div>

              <div class="mits-cn-form-grid" style="margin-top:16px">
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_NEWSLETTER_STATUS', 'Newsletter status'); ?></label><select name="target_newsletter"><option value=""><?php echo mits_cn_text('MITS_CN_NO_RESTRICTION', 'No restriction'); ?></option><option value="subscribed" <?php echo in_array('subscribed', $notice['targets']['newsletter'] ?? array(), true) ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_NEWSLETTER_SUBSCRIBED', 'Newsletter subscriber'); ?></option><option value="unsubscribed" <?php echo in_array('unsubscribed', $notice['targets']['newsletter'] ?? array(), true) ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_NEWSLETTER_UNSUBSCRIBED', 'Not subscribed'); ?></option></select><small><?php echo mits_cn_text('MITS_CN_NEWSLETTER_DESC', 'Evaluated only for logged-in customers.'); ?></small></div>
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_CUSTOMER_IDS', 'Customer IDs'); ?></label><input type="text" name="target_customer_ids" value="<?php echo mits_cn_h(implode(',', $notice['targets']['customer_id'] ?? array())); ?>" placeholder="12,45,77"></div>
              </div>

              <h3 class="mits-cn-check-title"><?php echo mits_cn_text('MITS_CN_COUNTRIES', 'Countries'); ?></h3>
              <div class="mits-cn-check-list">
                <?php $selectedCountries = $notice['targets']['country'] ?? array(); foreach ($countries as $row) { $cid = (string)$row['countries_id']; ?>
                  <label class="mits-cn-check-row"><input type="checkbox" name="target_country[]" value="<?php echo mits_cn_h($cid); ?>" <?php echo in_array($cid, $selectedCountries, true) ? 'checked' : ''; ?>> <span><?php echo mits_cn_h($row['countries_name']); ?></span></label>
                <?php } ?>
              </div>

              <div class="mits-cn-form-grid mits-cn-form-grid--3" style="margin-top:16px">
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_CATEGORY_IDS', 'Category IDs'); ?></label><input type="text" name="target_category_ids" value="<?php echo mits_cn_h(implode(',', $notice['targets']['category_id'] ?? array())); ?>"></div>
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_PRODUCT_IDS', 'Product IDs'); ?></label><input type="text" name="target_product_ids" value="<?php echo mits_cn_h(implode(',', $notice['targets']['product_id'] ?? array())); ?>"></div>
                <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_MANUFACTURER_IDS', 'Manufacturer IDs'); ?></label><input type="text" name="target_manufacturer_ids" value="<?php echo mits_cn_h(implode(',', $notice['targets']['manufacturer_id'] ?? array())); ?>"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="mits-cn-edit-side">
          <div class="mits-cn-card">
            <div class="mits-cn-card__header"><div><h2 class="mits-cn-card__title"><?php echo mits_cn_text('MITS_CN_HELP_TITLE', 'Quick guide'); ?></h2></div></div>
            <div class="mits-cn-card__body">
              <ul class="mits-cn-note-list">
                <li><strong><?php echo mits_cn_text('MITS_CN_INTERNAL_TITLE', 'Internal title'); ?>:</strong> <?php echo mits_cn_text('MITS_CN_HELP_INTERNAL', 'mandatory, admin only.'); ?></li>
                <li><strong><?php echo mits_cn_text('MITS_CN_FRONTEND_TITLE', 'Storefront headline'); ?>:</strong> <?php echo mits_cn_text('MITS_CN_HELP_FRONTEND', 'optional and language-specific.'); ?></li>
                <li><?php echo mits_cn_text('MITS_CN_HELP_TEMPLATE', 'Template overrides can be placed in'); ?> <span class="mits-cn-code">templates/&lt;TEMPLATE&gt;/module/mits_customers_notice/</span></li>
                <li><?php echo mits_cn_text('MITS_CN_HELP_MANUAL', 'Manual notices are available through'); ?> <span class="mits-cn-code">{$MITS_CUSTOMERS_NOTICE}</span>.</li>
              </ul>
              <?php if (!empty($notice['legacy_notice_id'])) { ?><div class="mits-cn-alert mits-cn-alert--info" style="margin:16px 0 0"><strong>Legacy-ID #<?php echo (int)$notice['legacy_notice_id']; ?></strong><br><?php echo mits_cn_text('MITS_CN_HELP_LEGACY', 'This notice was imported from the legacy module.'); ?></div><?php } ?>
            </div>
          </div>
        </div>
      </div>
      <div class="mits-cn-footer-actions"><a class="mits-cn-button mits-cn-button--soft" href="<?php echo xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE, $listQueryString); ?>"><?php echo mits_cn_text('MITS_CN_BUTTON_CANCEL', 'Cancel'); ?></a><button class="mits-cn-button" type="submit" name="save_mode" value="overview"><?php echo mits_cn_text('MITS_CN_BUTTON_SAVE', 'Save'); ?></button><button class="mits-cn-button mits-cn-button--soft" type="submit" name="save_mode" value="continue"><?php echo mits_cn_text('MITS_CN_BUTTON_SAVE_CONTINUE', 'Save & continue editing'); ?></button></div>
    </form>
  <?php } elseif ($migrationPreview) { ?>
    <div class="mits-cn-card">
      <div class="mits-cn-card__header"><div><h2 class="mits-cn-card__title"><?php echo mits_cn_text('MITS_CN_MIGRATION_PREVIEW_TITLE', 'Migration preview'); ?></h2><p class="mits-cn-card__subtitle"><?php echo mits_cn_text('MITS_CN_MIGRATION_PREVIEW_DESC', 'Legacy data is copied non-destructively. Existing legacy tables remain unchanged.'); ?></p></div><div class="mits-cn-card__tools"><a class="mits-cn-button mits-cn-button--soft" href="<?php echo xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE); ?>"><?php echo mits_cn_text('MITS_CN_BUTTON_CANCEL', 'Cancel'); ?></a></div></div>
      <div class="mits-cn-table-wrap"><table class="mits-cn-table"><thead><tr><th>Legacy-ID</th><th><?php echo mits_cn_text('MITS_CN_INTERNAL_TITLE', 'Internal title'); ?></th><th><?php echo mits_cn_text('MITS_CN_FRONTEND_TITLE', 'Storefront headline'); ?></th><th><?php echo mits_cn_text('MITS_CN_MIGRATION_LEGACY_TEMPLATE', 'Legacy template'); ?></th><th><?php echo mits_cn_text('MITS_CN_DISPLAY_TYPE', 'Display type'); ?></th><th><?php echo mits_cn_text('MITS_CN_STATUS', 'Status'); ?></th></tr></thead><tbody>
        <?php
        $previewQuery = xtc_db_query("SELECT cn.* FROM customers_notice cn LEFT JOIN " . TABLE_MITS_CUSTOMERS_NOTICE . " mn ON mn.legacy_notice_id = cn.customers_notice_id WHERE mn.notice_id IS NULL ORDER BY cn.customers_notice_id");
        while ($preview = xtc_db_fetch_array($previewQuery)) {
            $legacyId = (int)$preview['customers_notice_id'];
            $previewDescriptions = array();
            $previewDescQuery = xtc_db_query("SELECT * FROM customers_notice_description WHERE customers_notice_id = " . $legacyId);
            while ($previewDesc = xtc_db_fetch_array($previewDescQuery)) {
                $previewDescriptions[(int)$previewDesc['languages_id']] = $previewDesc;
            }
            $previewInternal = mits_cn_legacy_internal_title($legacyId, $previewDescriptions, $currentLanguageId);
            $rawTitle = isset($previewDescriptions[$currentLanguageId]['title']) ? trim((string)$previewDescriptions[$currentLanguageId]['title']) : '';
            $frontendTitle = mits_cn_clean_legacy_frontend_title($rawTitle);
            $map = mits_cn_map_legacy_template((string)($preview['template'] ?? ''));
            ?>
            <tr>
              <td>#<?php echo $legacyId; ?></td>
              <td><span class="mits-cn-title-main"><?php echo mits_cn_h($previewInternal); ?></span></td>
              <td><?php if ($frontendTitle !== '') { echo mits_cn_h($frontendTitle); } elseif (strcasecmp($rawTitle, 'NOTITLE') === 0) { ?><span class="mits-cn-preview-notitle">NOTITLE &rarr; <?php echo mits_cn_text('MITS_CN_NO_FRONTEND_TITLE', 'no storefront headline'); ?></span><?php } else { ?><span class="mits-cn-preview-notitle"><?php echo mits_cn_text('MITS_CN_NO_FRONTEND_TITLE', 'no storefront headline'); ?></span><?php } ?></td>
              <td><?php echo mits_cn_h((string)$preview['template']); ?><?php echo !$map['known'] ? '<div class="mits-cn-title-meta">' . mits_cn_text('MITS_CN_MIGRATION_CHECK_TEMPLATE', 'Please check after import') . '</div>' : ''; ?></td>
              <td><span class="mits-cn-badge"><?php echo mits_cn_h($map['label']); ?></span></td>
              <td><span class="mits-cn-badge <?php echo (int)$preview['status'] === 1 ? 'mits-cn-badge--current' : 'mits-cn-badge--off'; ?>"><?php echo (int)$preview['status'] === 1 ? mits_cn_text('MITS_CN_STATUS_ACTIVE', 'Active') : mits_cn_text('MITS_CN_STATUS_INACTIVE', 'Inactive'); ?></span></td>
            </tr>
        <?php } ?>
      </tbody></table></div>
      <div class="mits-cn-card__body" style="border-top:1px solid var(--mits-ci-line)"><?php echo xtc_draw_form('mits_cn_migrate', FILENAME_MITS_CUSTOMERS_NOTICE, 'action=migrate', 'post', 'class="mits-cn-inline-form" onsubmit="return confirm(\'' . mits_cn_h(mits_cn_text('MITS_CN_MIGRATION_CONFIRM', 'Import legacy data now?')) . '\');"'); ?><button class="mits-cn-button" type="submit"><?php echo mits_cn_text('MITS_CN_BUTTON_MIGRATE', 'Import legacy customer notices'); ?></button></form></div>
    </div>
  <?php } else { ?>
    <div class="mits-cn-stats">
      <div class="mits-cn-stat"><span class="mits-cn-stat__label"><?php echo mits_cn_text('MITS_CN_STAT_TOTAL', 'Notices'); ?></span><span class="mits-cn-stat__value"><?php echo $stats['total']; ?></span><span class="mits-cn-stat__meta"><?php echo mits_cn_text('MITS_CN_STAT_TOTAL_META', 'total records'); ?></span></div>
      <div class="mits-cn-stat"><span class="mits-cn-stat__label"><?php echo mits_cn_text('MITS_CN_STAT_ENABLED', 'Enabled'); ?></span><span class="mits-cn-stat__value"><?php echo $stats['enabled']; ?></span><span class="mits-cn-stat__meta"><?php echo mits_cn_text('MITS_CN_STAT_ENABLED_META', 'status active'); ?></span></div>
      <div class="mits-cn-stat"><span class="mits-cn-stat__label"><?php echo mits_cn_text('MITS_CN_STAT_CURRENT', 'Current'); ?></span><span class="mits-cn-stat__value"><?php echo $stats['current']; ?></span><span class="mits-cn-stat__meta"><?php echo mits_cn_text('MITS_CN_STAT_CURRENT_META', 'active within schedule'); ?></span></div>
      <div class="mits-cn-stat"><span class="mits-cn-stat__label"><?php echo mits_cn_text('MITS_CN_STAT_SCHEDULED', 'Scheduled'); ?></span><span class="mits-cn-stat__value"><?php echo $stats['scheduled']; ?></span><span class="mits-cn-stat__meta"><?php echo mits_cn_text('MITS_CN_STAT_SCHEDULED_META', 'future start'); ?></span></div>
    </div>

    <?php if ($legacyCount > 0) { ?><div class="mits-cn-alert mits-cn-alert--info"><strong><?php echo sprintf(mits_cn_text('MITS_CN_MIGRATION_FOUND', 'Legacy module detected: %d notices can be imported.'), $legacyCount); ?></strong><br><?php echo mits_cn_text('MITS_CN_MIGRATION_FOUND_DESC', 'Use the migration preview to see how internal titles, storefront headlines and legacy templates will be converted.'); ?></div><?php } ?>

    <div class="mits-cn-card">
      <div class="mits-cn-card__header"><div><h2 class="mits-cn-card__title"><?php echo mits_cn_text('MITS_CN_FILTER_TITLE', 'Filter notices'); ?></h2><p class="mits-cn-card__subtitle"><?php echo mits_cn_text('MITS_CN_FILTER_DESC', 'Search and narrow the overview without changing the stored sort order.'); ?></p></div></div>
      <div class="mits-cn-card__body">
        <?php echo xtc_draw_form('mits_cn_filter', FILENAME_MITS_CUSTOMERS_NOTICE, '', 'get'); ?>
          <div class="mits-cn-filter">
            <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_FILTER_SEARCH', 'Search'); ?></label><input type="text" name="search" value="<?php echo mits_cn_h($search); ?>" placeholder="<?php echo mits_cn_h(mits_cn_text('MITS_CN_FILTER_SEARCH_PLACEHOLDER', 'Internal or storefront title')); ?>"></div>
            <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_STATUS', 'Status'); ?></label><select name="status"><option value=""><?php echo mits_cn_text('MITS_CN_FILTER_ALL', 'All'); ?></option><option value="1" <?php echo $statusFilter === '1' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_STATUS_ACTIVE', 'Active'); ?></option><option value="0" <?php echo $statusFilter === '0' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_STATUS_INACTIVE', 'Inactive'); ?></option></select></div>
            <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_DISPLAY_TYPE', 'Display type'); ?></label><select name="type"><option value=""><?php echo mits_cn_text('MITS_CN_FILTER_ALL', 'All'); ?></option><?php foreach (array('notice','important','topbar','modal','toast') as $type) { ?><option value="<?php echo $type; ?>" <?php echo $typeFilter === $type ? 'selected' : ''; ?>><?php echo mits_cn_h(mits_cn_type_label($type)); ?></option><?php } ?></select></div>
            <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_AUDIENCE', 'Audience'); ?></label><select name="audience"><option value=""><?php echo mits_cn_text('MITS_CN_FILTER_ALL', 'All'); ?></option><?php foreach (array('all','guest','logged_in') as $aud) { ?><option value="<?php echo $aud; ?>" <?php echo $audienceFilter === $aud ? 'selected' : ''; ?>><?php echo mits_cn_h(mits_cn_audience_label($aud)); ?></option><?php } ?></select></div>
            <div class="mits-cn-field"><label><?php echo mits_cn_text('MITS_CN_FILTER_TIME', 'Schedule'); ?></label><select name="time"><option value=""><?php echo mits_cn_text('MITS_CN_FILTER_ALL', 'All'); ?></option><option value="current" <?php echo $timeFilter === 'current' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_TIME_CURRENT', 'Current'); ?></option><option value="scheduled" <?php echo $timeFilter === 'scheduled' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_TIME_SCHEDULED', 'Scheduled'); ?></option><option value="expired" <?php echo $timeFilter === 'expired' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_TIME_EXPIRED', 'Expired'); ?></option><option value="unlimited" <?php echo $timeFilter === 'unlimited' ? 'selected' : ''; ?>><?php echo mits_cn_text('MITS_CN_TIME_UNLIMITED', 'No schedule'); ?></option></select></div>
            <div class="mits-cn-actions"><button class="mits-cn-button" type="submit"><?php echo mits_cn_text('MITS_CN_BUTTON_FILTER', 'Filter'); ?></button><a class="mits-cn-button mits-cn-button--soft" href="<?php echo xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE); ?>"><?php echo mits_cn_text('MITS_CN_BUTTON_RESET', 'Reset'); ?></a></div>
          </div>
          <input type="hidden" name="sort" value="<?php echo mits_cn_h($sortKey); ?>"><input type="hidden" name="dir" value="<?php echo mits_cn_h($sortDir); ?>">
        </form>
      </div>
    </div>

    <?php
    $where = array('1=1');
    if ($search !== '') {
        $safeSearch = xtc_db_input($search);
        $where[] = "(n.internal_title LIKE '%" . $safeSearch . "%' OR d.title LIKE '%" . $safeSearch . "%')";
    }
    if ($statusFilter !== '') {
        $where[] = 'n.status = ' . (int)$statusFilter;
    }
    if ($typeFilter !== '') {
        $where[] = "n.display_type = '" . xtc_db_input($typeFilter) . "'";
    }
    if ($audienceFilter !== '') {
        $where[] = "n.audience = '" . xtc_db_input($audienceFilter) . "'";
    }
    if ($timeFilter === 'current') {
        $where[] = '(n.start_at IS NULL OR n.start_at <= NOW()) AND (n.end_at IS NULL OR n.end_at >= NOW())';
    } elseif ($timeFilter === 'scheduled') {
        $where[] = 'n.start_at IS NOT NULL AND n.start_at > NOW()';
    } elseif ($timeFilter === 'expired') {
        $where[] = 'n.end_at IS NOT NULL AND n.end_at < NOW()';
    } elseif ($timeFilter === 'unlimited') {
        $where[] = 'n.start_at IS NULL AND n.end_at IS NULL';
    }
    $sortMap = array('position' => 'n.sort_order', 'title' => 'n.internal_title', 'status' => 'n.status', 'type' => 'n.display_type', 'start' => 'n.start_at', 'end' => 'n.end_at', 'updated' => 'n.updated_at');
    $orderColumn = $sortMap[$sortKey];
    $orderDirection = strtoupper($sortDir) === 'DESC' ? 'DESC' : 'ASC';
    $listSql = "SELECT n.*, d.title AS frontend_title
                  FROM " . TABLE_MITS_CUSTOMERS_NOTICE . " n
             LEFT JOIN " . TABLE_MITS_CUSTOMERS_NOTICE_DESCRIPTION . " d ON d.notice_id = n.notice_id AND d.language_id = " . $currentLanguageId . "
                 WHERE " . implode(' AND ', $where) . "
              ORDER BY " . $orderColumn . " " . $orderDirection . ", n.notice_id ASC";
    $listQueryNumrows = 0;
    $listSplit = new splitPageResults($page, $pageMaxDisplayResults, $listSql, $listQueryNumrows);
    $listQuery = xtc_db_query($listSql);
    $resultCount = (int)$listQueryNumrows;
    if ($page < 1) {
        $page = 1;
    }

    $listRows = array();
    $listIds = array();
    while ($listRow = xtc_db_fetch_array($listQuery)) {
        $listRow['target_count'] = 0;
        $listRows[] = $listRow;
        $listIds[] = (int)$listRow['notice_id'];
    }
    if (!empty($listIds)) {
        $targetCountQuery = xtc_db_query(
            "SELECT notice_id, COUNT(*) AS target_count
               FROM " . TABLE_MITS_CUSTOMERS_NOTICE_TARGETS . "
              WHERE notice_id IN (" . implode(',', $listIds) . ")
           GROUP BY notice_id"
        );
        $targetCounts = array();
        while ($targetCountRow = xtc_db_fetch_array($targetCountQuery)) {
            $targetCounts[(int)$targetCountRow['notice_id']] = (int)$targetCountRow['target_count'];
        }
        foreach ($listRows as $index => $listRow) {
            $rowId = (int)$listRow['notice_id'];
            $listRows[$index]['target_count'] = $targetCounts[$rowId] ?? 0;
        }
    }

    $listParams['page'] = $page;
    $listQueryString = mits_cn_query($listParams);
    $dragEnabled = $search === '' && $statusFilter === '' && $typeFilter === '' && $audienceFilter === '' && $timeFilter === '' && $sortKey === 'position' && $sortDir === 'asc';
    $sortLink = static function (string $key, string $label) use ($search, $statusFilter, $typeFilter, $audienceFilter, $timeFilter, $sortKey, $sortDir): string {
        $dir = ($sortKey === $key && $sortDir === 'asc') ? 'desc' : 'asc';
        $arrow = $sortKey === $key ? ($sortDir === 'asc' ? ' &#9650;' : ' &#9660;') : '';
        $query = mits_cn_query(array('search' => $search, 'status' => $statusFilter, 'type' => $typeFilter, 'audience' => $audienceFilter, 'time' => $timeFilter, 'sort' => $key, 'dir' => $dir));
        return '<a href="' . xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE, $query) . '">' . $label . $arrow . '</a>';
    };
    ?>

    <div class="mits-cn-card">
      <div class="mits-cn-card__header">
        <div><h2 class="mits-cn-card__title"><?php echo mits_cn_text('MITS_CN_LIST_TITLE', 'Customer notices'); ?> <span style="font-size:12px;font-weight:normal;color:var(--mits-ci-muted)">(<?php echo $resultCount; ?>)</span></h2><p class="mits-cn-card__subtitle"><?php echo $dragEnabled ? mits_cn_text('MITS_CN_DRAG_HELP', 'Drag rows by the handle to change the global sort order. The new order is saved automatically.') : mits_cn_text('MITS_CN_DRAG_DISABLED', 'Drag & drop is available in the unfiltered default view sorted by position ascending.'); ?></p></div>
        <div class="mits-cn-card__tools"><?php if (!$dragEnabled) { ?><a class="mits-cn-button mits-cn-button--soft mits-cn-button--small" href="<?php echo xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE); ?>"><?php echo mits_cn_text('MITS_CN_SHOW_SORT_VIEW', 'Open sort view'); ?></a><?php } ?></div>
      </div>
      <div class="mits-cn-table-wrap">
        <table class="mits-cn-table">
          <thead><tr><th class="mits-cn-drag-cell"></th><th><?php echo $sortLink('title', mits_cn_text('MITS_CN_INTERNAL_TITLE', 'Internal title')); ?></th><th><?php echo $sortLink('status', mits_cn_text('MITS_CN_STATUS', 'Status')); ?></th><th><?php echo $sortLink('type', mits_cn_text('MITS_CN_DISPLAY_TYPE', 'Type')); ?></th><th><?php echo mits_cn_text('MITS_CN_AUDIENCE', 'Audience'); ?></th><th><?php echo $sortLink('start', mits_cn_text('MITS_CN_SCHEDULE', 'Schedule')); ?></th><th><?php echo mits_cn_text('MITS_CN_TARGET_RULES', 'Rules'); ?></th><th><?php echo $sortLink('position', mits_cn_text('MITS_CN_POSITION', 'Position')); ?></th><th><?php echo mits_cn_text('MITS_CN_ACTIONS', 'Actions'); ?></th></tr></thead>
          <tbody id="mits-cn-sortable" data-sort-enabled="<?php echo $dragEnabled ? '1' : '0'; ?>">
          <?php if ($resultCount === 0) { ?><tr><td colspan="9" class="mits-cn-empty"><?php echo mits_cn_text('MITS_CN_LIST_EMPTY', 'No matching customer notices found.'); ?></td></tr><?php } ?>
          <?php foreach ($listRows as $row) { $id = (int)$row['notice_id']; $isActive = (int)$row['status'] === 1; [$timeClass, $timeLabel] = mits_cn_time_state($row); ?>
            <tr data-notice-id="<?php echo $id; ?>">
              <td class="mits-cn-drag-cell"><?php if ($dragEnabled) { ?><span class="mits-cn-drag" draggable="true" title="<?php echo mits_cn_h(mits_cn_text('MITS_CN_DRAG_HANDLE', 'Drag to sort')); ?>">&#8942;&#8942;</span><?php } ?></td>
              <td><div class="mits-cn-title-main"><?php echo mits_cn_h($row['internal_title']); ?></div><div class="mits-cn-title-meta"><?php echo !empty($row['frontend_title']) ? mits_cn_text('MITS_CN_FRONTEND_SHORT', 'Storefront') . ': ' . mits_cn_h($row['frontend_title']) : mits_cn_text('MITS_CN_NO_FRONTEND_TITLE', 'no storefront headline'); ?><?php echo $row['legacy_notice_id'] ? '<br>Legacy-ID #' . (int)$row['legacy_notice_id'] : ''; ?></div></td>
              <td><?php echo xtc_draw_form('mits_cn_toggle_' . $id, FILENAME_MITS_CUSTOMERS_NOTICE, mits_cn_query(array_merge($listParams, array('action' => 'toggle'))), 'post', 'class="mits-cn-inline-form"'); ?><input type="hidden" name="nid" value="<?php echo $id; ?>"><input type="hidden" name="flag" value="<?php echo $isActive ? '0' : '1'; ?>"><button type="submit" class="mits-cn-status-toggle" title="<?php echo mits_cn_h(mits_cn_text('MITS_CN_STATUS_TOGGLE', 'Change status')); ?>"><span class="mits-cn-badge <?php echo $isActive ? 'mits-cn-badge--current' : 'mits-cn-badge--off'; ?>"><?php echo $isActive ? mits_cn_text('MITS_CN_STATUS_ACTIVE', 'Active') : mits_cn_text('MITS_CN_STATUS_INACTIVE', 'Inactive'); ?></span></button></form></td>
              <td><div class="mits-cn-badges"><span class="mits-cn-badge"><?php echo mits_cn_h(mits_cn_type_label((string)$row['display_type'])); ?></span><span class="mits-cn-badge mits-cn-badge--off"><?php echo mits_cn_h(mits_cn_frequency_label((string)$row['frequency'])); ?></span></div></td>
              <td><?php echo mits_cn_h(mits_cn_audience_label((string)$row['audience'])); ?></td>
              <td><div class="mits-cn-badges"><span class="mits-cn-badge mits-cn-badge--<?php echo $timeClass; ?>"><?php echo mits_cn_h($timeLabel); ?></span></div><div class="mits-cn-title-meta"><?php echo mits_cn_date_for_list($row['start_at']); ?> &rarr; <?php echo mits_cn_date_for_list($row['end_at']); ?></div></td>
              <td><span class="mits-cn-badge"><?php echo (int)$row['target_count']; ?></span></td>
              <td><?php echo (int)$row['sort_order']; ?></td>
              <td><div class="mits-cn-actions"><a class="mits-cn-button mits-cn-button--soft mits-cn-button--small" href="<?php echo xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE, mits_cn_query(array_merge($listParams, array('action' => 'edit', 'nid' => $id)))); ?>"><?php echo mits_cn_text('MITS_CN_BUTTON_EDIT', 'Edit'); ?></a><?php echo xtc_draw_form('mits_cn_delete_' . $id, FILENAME_MITS_CUSTOMERS_NOTICE, mits_cn_query(array_merge($listParams, array('action' => 'delete_confirm'))), 'post', 'class="mits-cn-inline-form" onsubmit="return confirm(\'' . mits_cn_h(mits_cn_text('MITS_CN_DELETE_CONFIRM', 'Delete this notice?')) . '\');"'); ?><input type="hidden" name="nid" value="<?php echo $id; ?>"><button class="mits-cn-button mits-cn-button--danger mits-cn-button--small" type="submit"><?php echo mits_cn_text('MITS_CN_BUTTON_DELETE', 'Delete'); ?></button></form></div></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mits-cn-pagination">
      <div><?php echo $listSplit->display_count($resultCount, $pageMaxDisplayResults, $page, mits_cn_text('MITS_CN_DISPLAY_COUNT', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> Hinweisen)')); ?></div>
      <div><?php echo $listSplit->display_links($resultCount, $pageMaxDisplayResults, MAX_DISPLAY_PAGE_LINKS, $page, mits_cn_query(array('search' => $search, 'status' => $statusFilter, 'type' => $typeFilter, 'audience' => $audienceFilter, 'time' => $timeFilter, 'sort' => $sortKey, 'dir' => $sortDir))); ?></div>
      <div><?php echo function_exists('draw_input_per_page') ? draw_input_per_page($PHP_SELF, $cfgMaxDisplayResultsKey, $pageMaxDisplayResults) : ''; ?></div>
    </div>

    <?php if ($dragEnabled && $resultCount > 1) { ?>
      <?php echo xtc_draw_form('mits_cn_sort_form', FILENAME_MITS_CUSTOMERS_NOTICE, 'action=sort', 'post', 'id="mits-cn-sort-form" style="display:none"'); ?><input type="hidden" name="sort_page" value="<?php echo (int)$page; ?>"><div id="mits-cn-sort-fields"></div></form>
    <?php } ?>
  <?php } ?>
</div>
</td>
</tr>
</table>
<script>
(function(){
  var buttons=document.querySelectorAll('[data-lang-tab]');
  for(var i=0;i<buttons.length;i++){
    buttons[i].addEventListener('click',function(){
      var id=this.getAttribute('data-lang-tab');
      var allButtons=document.querySelectorAll('[data-lang-tab]');
      var panels=document.querySelectorAll('[data-lang-panel]');
      for(var b=0;b<allButtons.length;b++){allButtons[b].classList.remove('active');}
      for(var p=0;p<panels.length;p++){panels[p].classList.remove('active');}
      this.classList.add('active');
      var panel=document.querySelector('[data-lang-panel="'+id+'"]');
      if(panel){panel.classList.add('active');}
    });
  }

  var displayTypeSelect=document.querySelector('[data-mits-cn-display-type-select]');
  var toastPositionField=document.querySelector('[data-mits-cn-toast-position-field]');
  function syncToastPosition(){
    if(!displayTypeSelect || !toastPositionField){return;}
    toastPositionField.style.display=displayTypeSelect.value==='toast'?'':'none';
  }
  if(displayTypeSelect){displayTypeSelect.addEventListener('change',syncToastPosition);syncToastPosition();}

  var tbody=document.getElementById('mits-cn-sortable');
  var form=document.getElementById('mits-cn-sort-form');
  var fields=document.getElementById('mits-cn-sort-fields');
  if(!tbody || !form || !fields || tbody.getAttribute('data-sort-enabled')!=='1'){return;}
  var dragged=null;
  var changed=false;
  tbody.addEventListener('dragstart',function(event){
    var row=event.target;
    while(row && row.tagName!=='TR'){row=row.parentNode;}
    if(!row || !row.getAttribute('data-notice-id')){return;}
    dragged=row;
    changed=false;
    row.classList.add('mits-cn-row-dragging');
    if(event.dataTransfer){event.dataTransfer.effectAllowed='move';event.dataTransfer.setData('text/plain',row.getAttribute('data-notice-id'));}
  });
  tbody.addEventListener('dragover',function(event){
    if(!dragged){return;}
    event.preventDefault();
    var row=event.target;
    while(row && row.tagName!=='TR'){row=row.parentNode;}
    if(!row || row===dragged || !row.getAttribute('data-notice-id')){return;}
    var rect=row.getBoundingClientRect();
    var before=event.clientY < rect.top + rect.height/2;
    if(before){tbody.insertBefore(dragged,row);}else{tbody.insertBefore(dragged,row.nextSibling);}
    changed=true;
  });
  tbody.addEventListener('dragend',function(){
    if(!dragged){return;}
    dragged.classList.remove('mits-cn-row-dragging');
    dragged=null;
    if(!changed){return;}
    fields.innerHTML='';
    var rows=tbody.querySelectorAll('tr[data-notice-id]');
    for(var r=0;r<rows.length;r++){
      var input=document.createElement('input');
      input.type='hidden';
      input.name='sort_ids[]';
      input.value=rows[r].getAttribute('data-notice-id');
      fields.appendChild(input);
    }
    form.submit();
  });
}());
</script>
<?php require DIR_WS_INCLUDES . 'footer.php'; ?>
<?php require DIR_WS_INCLUDES . 'application_bottom.php'; ?>
