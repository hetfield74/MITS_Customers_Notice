<?php
/**
 * --------------------------------------------------------------
 * File: MitsCustomersNotice.php
 * Date: 23.09.2026
 *
 * Author: Hetfield
 * Copyright: (c) 2026 - MerZ IT-SerVice
 * Web: https://www.merz-it-service.de
 * Contact: info@merz-it-service.de
 * --------------------------------------------------------------
 */

class MitsCustomersNotice
{
    private static bool $prepared = false;
    private static string $manualHtml = '';
    private static string $autoHtml = '';
    private static bool $needsCss = false;
    private static bool $needsJavascript = false;
    private static array $autoSessionKeys = array();

    public static function prepare(): void
    {
        if (self::$prepared) {
            return;
        }
        self::$prepared = true;

        if (!defined('MODULE_MITS_CUSTOMERS_NOTICE_STATUS') || MODULE_MITS_CUSTOMERS_NOTICE_STATUS !== 'true') {
            return;
        }
        if (!defined('TABLE_MITS_CUSTOMERS_NOTICE') || !self::tableExists(TABLE_MITS_CUSTOMERS_NOTICE)) {
            return;
        }

        $query = xtc_db_query(
            "SELECT *
               FROM " . TABLE_MITS_CUSTOMERS_NOTICE . "
              WHERE status = 1
                AND (start_at IS NULL OR start_at <= NOW())
                AND (end_at IS NULL OR end_at > NOW())
           ORDER BY sort_order ASC, notice_id ASC"
        );

        $notices = array();
        $ids = array();
        while ($row = xtc_db_fetch_array($query)) {
            $id = (int)$row['notice_id'];
            $notices[$id] = $row;
            $ids[] = $id;
        }
        if (empty($ids)) {
            return;
        }

        $idList = implode(',', array_map('intval', $ids));
        $languageId = isset($_SESSION['languages_id']) ? (int)$_SESSION['languages_id'] : 0;
        $descriptions = array();
        $descQuery = xtc_db_query(
            "SELECT notice_id, language_id, title, content, button_text, button_url
               FROM " . TABLE_MITS_CUSTOMERS_NOTICE_DESCRIPTION . "
              WHERE notice_id IN (" . $idList . ")
           ORDER BY notice_id ASC, (language_id = " . $languageId . ") DESC, language_id ASC"
        );
        while ($desc = xtc_db_fetch_array($descQuery)) {
            $id = (int)$desc['notice_id'];
            if (!isset($descriptions[$id]) || (int)$desc['language_id'] === $languageId) {
                $descriptions[$id] = $desc;
            }
        }

        $targets = array();
        $targetQuery = xtc_db_query(
            "SELECT notice_id, target_type, target_value
               FROM " . TABLE_MITS_CUSTOMERS_NOTICE_TARGETS . "
              WHERE notice_id IN (" . $idList . ")
           ORDER BY target_id ASC"
        );
        while ($target = xtc_db_fetch_array($targetQuery)) {
            $id = (int)$target['notice_id'];
            $type = (string)$target['target_type'];
            $targets[$id][$type][] = (string)$target['target_value'];
        }

        $context = self::buildContext();
        foreach ($notices as $id => $notice) {
            if (!isset($descriptions[$id])) {
                continue;
            }
            $description = $descriptions[$id];
            if (trim((string)$description['title']) === '' && trim((string)$description['content']) === '') {
                continue;
            }
            $noticeTargets = $targets[$id] ?? array();
            if (!self::matchesAudience($notice, $context)) {
                continue;
            }
            if (!self::matchesTargets($noticeTargets, $context)) {
                continue;
            }
            if (!self::matchesFrequency($notice)) {
                continue;
            }

            $html = self::renderNotice($notice, $description);
            if ($html === '') {
                continue;
            }

            if ((string)$notice['placement'] === 'manual') {
                self::$manualHtml .= $html;
            } else {
                self::$autoHtml .= $html;
                if ((string)$notice['frequency'] === 'session') {
                    self::$autoSessionKeys[self::storageKey($notice)] = 1;
                }
            }
            self::$needsCss = true;
            $displayType = self::normalizeDisplayType((string)$notice['display_type']);
            $frequency = self::normalizeFrequency((string)$notice['frequency']);
            if (
                self::normalizePlacement((string)$notice['placement']) !== 'manual'
                || in_array($displayType, array('modal', 'toast'), true)
                || $frequency !== 'always'
                || (int)$notice['dismissible'] === 1
                || ((int)$notice['countdown'] === 1 && !empty($notice['end_at']))
            ) {
                self::$needsJavascript = true;
            }
        }
    }

    public static function assignSmarty(): void
    {
        self::prepare();
        if (isset($GLOBALS['smarty']) && is_object($GLOBALS['smarty']) && method_exists($GLOBALS['smarty'], 'assign')) {
            $GLOBALS['smarty']->assign('MITS_CUSTOMERS_NOTICE', self::$manualHtml);
        }
    }

    public static function renderStylesheetAsset(): string
    {
        $mode = self::assetMode();
        if ($mode === 'manual') {
            return '';
        }
        self::prepare();
        if (!self::$needsCss) {
            return '';
        }
        if ($mode === 'inline') {
            $css = self::readAsset(self::getCssFilePath());
            return $css === '' ? '' : '<style id="mits-cn-styles">' . $css . '</style>' . PHP_EOL;
        }
        return '<link rel="stylesheet" href="' . self::h(self::versionedAssetUrl(self::getCssUrl(), self::getCssFilePath())) . '" type="text/css" media="screen">' . PHP_EOL;
    }

    public static function renderAutoOutput(): string
    {
        self::prepare();
        if (self::$autoHtml === '' && !self::$needsJavascript) {
            return '';
        }

        $html = '';
        if (self::$autoHtml !== '') {
            $html .= '<div id="mits-cn-auto-root" hidden>' . self::$autoHtml . '</div>';
        }
        if (self::$needsJavascript) {
            $mode = self::assetMode();
            if ($mode === 'inline') {
                $javascript = self::readAsset(self::getJavascriptFilePath());
                if ($javascript !== '') {
                    $html .= '<script id="mits-cn-script">' . $javascript . '</script>';
                }
            } elseif ($mode === 'external') {
                $html .= '<script src="' . self::h(self::versionedAssetUrl(self::getJavascriptUrl(), self::getJavascriptFilePath())) . '" defer></script>';
            }
        }
        if (self::$autoHtml !== '' && !empty(self::$autoSessionKeys)) {
            if (!isset($_SESSION['mits_customers_notice_shown']) || !is_array($_SESSION['mits_customers_notice_shown'])) {
                $_SESSION['mits_customers_notice_shown'] = array();
            }
            foreach (self::$autoSessionKeys as $key => $unused) {
                $_SESSION['mits_customers_notice_shown'][$key] = 1;
            }
        }
        return $html;
    }

    public static function getCssFilePath(): string
    {
        $templateName = defined('CURRENT_TEMPLATE') ? (string)CURRENT_TEMPLATE : '';
        $catalogPath = self::catalogPath();
        if ($templateName !== '') {
            $override = $catalogPath . 'templates/' . $templateName . '/css/mits_customers_notice.css';
            if (is_file($override)) {
                return $override;
            }
        }
        return $catalogPath . 'includes/external/mits_customers_notice/css/mits_customers_notice.css';
    }

    public static function getCssUrl(): string
    {
        $templateName = defined('CURRENT_TEMPLATE') ? (string)CURRENT_TEMPLATE : '';
        $catalogPath = self::catalogPath();
        if ($templateName !== '') {
            $override = $catalogPath . 'templates/' . $templateName . '/css/mits_customers_notice.css';
            if (is_file($override)) {
                return self::baseUrl() . 'templates/' . $templateName . '/css/mits_customers_notice.css';
            }
        }
        return self::baseUrl() . 'includes/external/mits_customers_notice/css/mits_customers_notice.css';
    }

    public static function getJavascriptFilePath(): string
    {
        return self::catalogPath() . 'includes/external/mits_customers_notice/js/mits_customers_notice.js';
    }

    public static function getJavascriptUrl(): string
    {
        return self::baseUrl() . 'includes/external/mits_customers_notice/js/mits_customers_notice.js';
    }

    public static function resolveTemplateFile(string $file): string
    {
        $file = basename($file);
        if (!preg_match('/^[A-Za-z0-9._-]+\\.html$/', $file)) {
            return '';
        }

        $templateName = defined('CURRENT_TEMPLATE') ? (string)CURRENT_TEMPLATE : '';
        $catalogPath = defined('DIR_FS_CATALOG') ? rtrim((string)DIR_FS_CATALOG, '/\\') . DIRECTORY_SEPARATOR : dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR;
        $modulePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR;
        $paths = array();

        if ($templateName !== '') {
            $paths[] = $catalogPath . 'templates/' . $templateName . '/module/mits_customers_notice/' . $file;
        }
        $paths[] = $modulePath . $file;

        foreach ($paths as $path) {
            if (is_file($path)) {
                return $path;
            }
        }
        return '';
    }

    private static function renderNotice(array $notice, array $description): string
    {
        $displayType = self::normalizeDisplayType((string)$notice['display_type']);
        $defaultTemplate = array(
            'notice' => 'notice.html',
            'important' => 'important.html',
            'topbar' => 'topbar.html',
            'modal' => 'modal.html',
            'toast' => 'toast.html'
        );
        $templateFile = trim((string)$notice['template_file']);
        if ($templateFile === '') {
            $templateFile = $defaultTemplate[$displayType];
        }

        $resolved = self::resolveTemplateFile($templateFile);
        if ($resolved === '') {
            $resolved = self::resolveTemplateFile($defaultTemplate[$displayType]);
        }
        if ($resolved === '') {
            return '';
        }

        $endTimestamp = !empty($notice['end_at']) ? strtotime((string)$notice['end_at']) : 0;
        $effectiveDismissible = (int)$notice['dismissible'] === 1 || (string)$notice['frequency'] === 'dismiss';
        $vars = array(
            'notice_id' => (int)$notice['notice_id'],
            'title' => self::h((string)$description['title']),
            'content' => (string)$description['content'],
            'button_text' => self::h((string)$description['button_text']),
            'button_url' => self::safeUrl((string)$description['button_url']),
            'dismissible' => $effectiveDismissible,
            'countdown' => (int)$notice['countdown'] === 1 && $endTimestamp > 0,
            'no_snippet' => (int)($notice['no_snippet'] ?? 1) === 1,
            'toast_position' => self::normalizeToastPosition((string)($notice['toast_position'] ?? 'bottom-right')),
            'end_timestamp' => $endTimestamp,
            'label_close' => self::label('MITS_CUSTOMERS_NOTICE_CLOSE', 'Close'),
            'label_days' => self::label('MITS_CUSTOMERS_NOTICE_DAYS', 'days'),
            'label_hours' => self::label('MITS_CUSTOMERS_NOTICE_HOURS', 'hours'),
            'label_minutes' => self::label('MITS_CUSTOMERS_NOTICE_MINUTES', 'minutes'),
            'label_seconds' => self::label('MITS_CUSTOMERS_NOTICE_SECONDS', 'seconds')
        );

        $body = self::renderSmartyFile($resolved, $vars);
        if ($body === '') {
            return '';
        }

        $placement = self::normalizePlacement((string)$notice['placement']);
        $selector = trim((string)$notice['selector']);
        if ($selector === '') {
            $selector = defined('MODULE_MITS_CUSTOMERS_NOTICE_DEFAULT_SELECTOR') ? (string)MODULE_MITS_CUSTOMERS_NOTICE_DEFAULT_SELECTOR : '#main-content, main, .content_big, .content_full, #col_right .col_right_inner, #col_right, #col_full, #contentwrap, #content, #layout_content, #main';
        }
        $insertMethod = self::normalizeInsertMethod((string)$notice['insert_method']);
        $frequency = self::normalizeFrequency((string)$notice['frequency']);
        $cssClass = preg_replace('/[^A-Za-z0-9 _-]/', '', (string)$notice['css_class']);
        $storageKey = self::storageKey($notice);
        $classes = trim('mits-cn-item mits-cn-type-' . $displayType . ' ' . $cssClass);

        return '<div class="' . self::h($classes) . '"'
            . ' data-mits-cn-item="1"'
            . ' data-notice-id="' . (int)$notice['notice_id'] . '"'
            . ' data-display-type="' . self::h($displayType) . '"'
            . ' data-placement="' . self::h($placement) . '"'
            . ' data-selector="' . self::h($selector) . '"'
            . ' data-insert-method="' . self::h($insertMethod) . '"'
            . ' data-frequency="' . self::h($frequency) . '"'
            . ' data-storage-key="' . self::h($storageKey) . '"'
            . ' data-dismissible="' . ($effectiveDismissible ? '1' : '0') . '"'
            . ((int)($notice['no_snippet'] ?? 1) === 1 ? ' data-nosnippet' : '')
            . '>' . $body . '</div>';
    }

    private static function renderSmartyFile(string $templateFile, array $vars): string
    {
        $smarty = null;
        if (isset($GLOBALS['smarty']) && is_object($GLOBALS['smarty']) && method_exists($GLOBALS['smarty'], 'assign') && method_exists($GLOBALS['smarty'], 'fetch')) {
            $smarty = clone $GLOBALS['smarty'];
        } elseif (class_exists('Smarty')) {
            $smarty = new Smarty();
        }

        if ($smarty === null) {
            return '';
        }
        foreach ($vars as $key => $value) {
            $smarty->assign($key, $value);
        }
        if (isset($_SESSION['language'])) {
            $smarty->assign('language', (string)$_SESSION['language']);
        }
        return (string)$smarty->fetch('file:' . $templateFile);
    }

    private static function buildContext(): array
    {
        global $category_depth;

        $script = isset($_SERVER['SCRIPT_NAME']) ? basename((string)$_SERVER['SCRIPT_NAME']) : '';
        $script = preg_replace('/\\.php$/i', '', $script);
        $page = $script;
        if ($script === 'index') {
            $page = isset($category_depth) && $category_depth !== '' && $category_depth !== 'top' ? 'category' : 'index';
        } elseif (preg_match('/^(account|address)_/', $script)) {
            $page = 'account';
        } elseif (preg_match('/^checkout_/', $script)) {
            $page = 'checkout';
        }

        $categoryId = 0;
        if (isset($_GET['cPath'])) {
            $parts = array_filter(explode('_', preg_replace('/[^0-9_]/', '', (string)$_GET['cPath'])), 'strlen');
            if (!empty($parts)) {
                $categoryId = (int)end($parts);
            }
        }

        return array(
            'logged_in' => isset($_SESSION['customer_id']) && (int)$_SESSION['customer_id'] > 0,
            'customer_id' => isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : 0,
            'customer_status' => isset($_SESSION['customers_status']['customers_status_id']) ? (int)$_SESSION['customers_status']['customers_status_id'] : 0,
            'country' => isset($_SESSION['customer_country_id']) ? (int)$_SESSION['customer_country_id'] : 0,
            'page' => $page,
            'script' => $script,
            'category_id' => $categoryId,
            'category_ids' => $categoryId > 0 ? array($categoryId) : array(),
            'product_id' => isset($_GET['products_id']) ? (int)$_GET['products_id'] : 0,
            'manufacturer_id' => isset($_GET['manufacturers_id']) ? (int)$_GET['manufacturers_id'] : 0,
            'newsletter' => null
        );
    }

    private static function matchesAudience(array $notice, array $context): bool
    {
        $audience = (string)$notice['audience'];
        if ($audience === 'guest') {
            return !$context['logged_in'];
        }
        if ($audience === 'logged_in') {
            return $context['logged_in'];
        }
        return true;
    }

    private static function matchesTargets(array $targets, array &$context): bool
    {
        foreach ($targets as $type => $values) {
            if (empty($values)) {
                continue;
            }
            switch ($type) {
                case 'customer_status':
                    if (!in_array((string)$context['customer_status'], $values, true)) {
                        return false;
                    }
                    break;
                case 'customer_id':
                    if (!in_array((string)$context['customer_id'], $values, true)) {
                        return false;
                    }
                    break;
                case 'country':
                    if (!in_array((string)$context['country'], $values, true)) {
                        return false;
                    }
                    break;
                case 'page':
                    if (!in_array((string)$context['page'], $values, true) && !in_array((string)$context['script'], $values, true)) {
                        return false;
                    }
                    break;
                case 'category_id':
                    if (empty($context['category_ids']) && $context['product_id'] > 0) {
                        $context['category_ids'] = self::getProductCategoryIds((int)$context['product_id']);
                    }
                    $categoryValues = array_map('strval', $context['category_ids']);
                    if (empty(array_intersect($values, $categoryValues))) {
                        return false;
                    }
                    break;
                case 'product_id':
                    if (!in_array((string)$context['product_id'], $values, true)) {
                        return false;
                    }
                    break;
                case 'manufacturer_id':
                    if ($context['manufacturer_id'] <= 0 && $context['product_id'] > 0) {
                        $context['manufacturer_id'] = self::getProductManufacturerId((int)$context['product_id']);
                    }
                    if (!in_array((string)$context['manufacturer_id'], $values, true)) {
                        return false;
                    }
                    break;
                case 'newsletter':
                    if ($context['newsletter'] === null) {
                        $context['newsletter'] = self::getNewsletterState($context);
                    }
                    if (!in_array((string)$context['newsletter'], $values, true)) {
                        return false;
                    }
                    break;
            }
        }
        return true;
    }


    private static function getProductCategoryIds(int $productId): array
    {
        if ($productId <= 0 || !defined('TABLE_PRODUCTS_TO_CATEGORIES')) {
            return array();
        }
        $result = array();
        $query = xtc_db_query(
            "SELECT categories_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = " . $productId
        );
        while ($row = xtc_db_fetch_array($query)) {
            $categoryId = (int)$row['categories_id'];
            if ($categoryId > 0) {
                $result[$categoryId] = $categoryId;
            }
        }
        return array_values($result);
    }

    private static function getProductManufacturerId(int $productId): int
    {
        if ($productId <= 0 || !defined('TABLE_PRODUCTS')) {
            return 0;
        }
        $query = xtc_db_query(
            "SELECT manufacturers_id FROM " . TABLE_PRODUCTS . " WHERE products_id = " . $productId . " LIMIT 1"
        );
        if (xtc_db_num_rows($query) < 1) {
            return 0;
        }
        $row = xtc_db_fetch_array($query);
        return isset($row['manufacturers_id']) ? (int)$row['manufacturers_id'] : 0;
    }

    private static function getNewsletterState(array $context): string
    {
        if (!$context['logged_in'] || $context['customer_id'] <= 0 || !defined('TABLE_NEWSLETTER_RECIPIENTS') || !defined('TABLE_CUSTOMERS')) {
            return 'unknown';
        }

        $customerQuery = xtc_db_query("SELECT customers_email_address FROM " . TABLE_CUSTOMERS . " WHERE customers_id = " . (int)$context['customer_id'] . " LIMIT 1");
        if (xtc_db_num_rows($customerQuery) < 1) {
            return 'unknown';
        }
        $customer = xtc_db_fetch_array($customerQuery);
        $email = (string)$customer['customers_email_address'];
        if ($email === '') {
            return 'unknown';
        }

        $newsletterQuery = xtc_db_query(
            "SELECT customers_id
               FROM " . TABLE_NEWSLETTER_RECIPIENTS . "
              WHERE customers_email_address = '" . xtc_db_input($email) . "'
                AND mail_status = 1
              LIMIT 1"
        );
        return xtc_db_num_rows($newsletterQuery) > 0 ? 'subscribed' : 'unsubscribed';
    }

    private static function matchesFrequency(array $notice): bool
    {
        if ((string)$notice['frequency'] !== 'session') {
            return true;
        }
        if (self::normalizePlacement((string)$notice['placement']) === 'manual') {
            return true;
        }
        $key = self::storageKey($notice);
        return empty($_SESSION['mits_customers_notice_shown'][$key]);
    }

    private static function storageKey(array $notice): string
    {
        $version = substr(sha1((string)$notice['updated_at']), 0, 12);
        return 'mits-cn-' . (int)$notice['notice_id'] . '-' . $version;
    }

    private static function normalizeDisplayType(string $value): string
    {
        return in_array($value, array('notice', 'important', 'topbar', 'modal', 'toast'), true) ? $value : 'notice';
    }

    private static function normalizePlacement(string $value): string
    {
        return in_array($value, array('auto', 'manual'), true) ? $value : 'auto';
    }

    private static function normalizeInsertMethod(string $value): string
    {
        return in_array($value, array('before', 'prepend', 'append', 'after'), true) ? $value : 'prepend';
    }

    private static function normalizeFrequency(string $value): string
    {
        return in_array($value, array('always', 'session', 'day', 'dismiss'), true) ? $value : 'always';
    }

    private static function normalizeToastPosition(string $value): string
    {
        return in_array($value, array('bottom-right', 'bottom-left', 'bottom-center', 'top-right', 'top-left', 'top-center'), true) ? $value : 'bottom-right';
    }

    private static function assetMode(): string
    {
        $mode = defined('MODULE_MITS_CUSTOMERS_NOTICE_ASSET_MODE') ? strtolower((string)MODULE_MITS_CUSTOMERS_NOTICE_ASSET_MODE) : 'external';
        return in_array($mode, array('external', 'inline', 'manual'), true) ? $mode : 'external';
    }

    private static function catalogPath(): string
    {
        return defined('DIR_FS_CATALOG')
            ? rtrim((string)DIR_FS_CATALOG, '/\\') . DIRECTORY_SEPARATOR
            : dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR;
    }

    private static function baseUrl(): string
    {
        $base = defined('DIR_WS_BASE') ? (string)DIR_WS_BASE : (defined('DIR_WS_CATALOG') ? (string)DIR_WS_CATALOG : '/');
        return $base === '' ? '' : rtrim($base, '/') . '/';
    }

    private static function versionedAssetUrl(string $url, string $path): string
    {
        if ($url === '' || $path === '' || !is_file($path)) {
            return $url;
        }
        $mtime = filemtime($path);
        return $mtime === false ? $url : $url . (strpos($url, '?') === false ? '?' : '&') . 'v=' . (int)$mtime;
    }

    private static function readAsset(string $path): string
    {
        if ($path === '' || !is_file($path) || !is_readable($path)) {
            return '';
        }
        $content = file_get_contents($path);
        return $content === false ? '' : trim((string)$content);
    }

    private static function tableExists(string $table): bool
    {
        $table = str_replace('`', '', $table);
        $table = str_replace(array('\\', '_', '%'), array('\\\\', '\_', '\%'), $table);
        $query = xtc_db_query("SHOW TABLES LIKE '" . xtc_db_input($table) . "'");
        return xtc_db_num_rows($query) > 0;
    }

    private static function label(string $constant, string $fallback): string
    {
        return defined($constant) ? (string)constant($constant) : $fallback;
    }

    private static function safeUrl(string $value): string
    {
        $value = trim($value);
        if ($value === '' || preg_match('/^(?:javascript|data):/i', $value)) {
            return '';
        }
        return self::h($value);
    }

    private static function h(string $value): string
    {
        if (function_exists('encode_htmlspecialchars')) {
            return (string)encode_htmlspecialchars($value, ENT_QUOTES);
        }
        return htmlspecialchars($value, ENT_QUOTES);
    }
}
