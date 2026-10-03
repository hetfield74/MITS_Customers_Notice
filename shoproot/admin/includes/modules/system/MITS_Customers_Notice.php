<?php
/**
 * --------------------------------------------------------------
 * File: MITS_Customers_Notice.php
 * Date: 23.09.2026
 *
 * Author: Hetfield
 * Copyright: (c) 2026 - MerZ IT-SerVice
 * Web: https://www.merz-it-service.de
 * Contact: info@merz-it-service.de
 * --------------------------------------------------------------
 */

class MITS_Customers_Notice
{
    public string $code;
    public string $name;
    public string $version;
    public mixed $sort_order;
    public string $title;
    public string $description;
    public mixed $do_update;
    public bool $enabled;
    private bool $_check;
    private string $default_columns;

    public function __construct()
    {
        $this->code = 'MITS_Customers_Notice';
        $this->name = 'MODULE_' . strtoupper($this->code);
        $this->version = '1.0.7';
        $this->sort_order = defined($this->name . '_SORT_ORDER') ? constant($this->name . '_SORT_ORDER') : 0;
        $this->enabled = defined($this->name . '_STATUS') && constant($this->name . '_STATUS') === 'true';
        $this->default_columns = 'configuration_key, configuration_value, configuration_group_id, sort_order, set_function';

        if (defined($this->name . '_VERSION') && $this->version !== (string)constant($this->name . '_VERSION')) {
            $this->do_update = defined($this->name . '_UPDATE_AVAILABLE_TITLE') ? constant($this->name . '_UPDATE_AVAILABLE_TITLE') : '';
        } else {
            $this->do_update = '';
        }

        $this->title = (defined($this->name . '_TITLE') ? constant($this->name . '_TITLE') : $this->code) . ' - v' . $this->version . $this->do_update;
        $this->description = '';
        if ($this->do_update !== '') {
            $this->description .= '<a class="button btnbox but_green" style="text-align:center;" onclick="this.blur();" href="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . (isset($_GET['set']) ? $_GET['set'] : 'system') . '&module=' . $this->code . '&action=update') . '">' . (defined($this->name . '_UPDATE_MODUL') ? constant($this->name . '_UPDATE_MODUL') : 'Update') . '</a><br>';
        }
        $this->description .= defined($this->name . '_DESCRIPTION') ? constant($this->name . '_DESCRIPTION') . '<hr style="margin:10px 0">' : '';

        if ($this->enabled && defined('FILENAME_MITS_CUSTOMERS_NOTICE')) {
            $this->description .= '<div style="text-align:center;margin:15px 0"><a class="button" href="' . xtc_href_link(FILENAME_MITS_CUSTOMERS_NOTICE) . '">' . (defined($this->name . '_OPEN_ADMIN') ? constant($this->name . '_OPEN_ADMIN') : 'Kundenhinweise verwalten') . '</a></div>';
        }

        if (!$this->enabled && defined($this->name . '_DELETE_MODUL') && defined($this->name . '_CONFIRM_DELETE_MODUL')) {
            $this->description .= '<div style="text-align:center;margin:30px 0"><a class="button but_red" style="text-align:center;" onclick="return confirmLink(\'' . constant($this->name . '_CONFIRM_DELETE_MODUL') . '\', \'\' ,this);" href="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=system&module=' . $this->code . '&action=custom') . '">' . constant($this->name . '_DELETE_MODUL') . '</a></div><br>';
        }

        $mitsUpdateClientFile = DIR_FS_CATALOG . 'includes/external/mits_module_update_client/MitsModuleUpdateClient.php';

        if (is_file($mitsUpdateClientFile)) {
            require_once $mitsUpdateClientFile;
            MitsModuleUpdateClient::integrate($this);
        }
    }

    public function process($file = ''): void
    {
    }

    public function display(): array
    {
        return array(
            'text' => '<br><div align="center">' . xtc_button(BUTTON_SAVE) . xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . (isset($_GET['set']) ? $_GET['set'] : 'system') . '&module=' . $this->code)) . '</div>'
        );
    }

    public function check()
    {
        if (!isset($this->_check)) {
            if (defined($this->name . '_STATUS')) {
                $this->_check = true;
            } else {
                $query = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = '" . $this->name . "_STATUS'");
                $this->_check = xtc_db_num_rows($query) > 0;
            }
        }
        return $this->_check;
    }

    public function install(): void
    {
        $this->dbChanges();
    }

    public function update()
    {
        $this->dbChanges();
        return defined($this->name . '_UPDATE_FINISHED') ? constant($this->name . '_UPDATE_FINISHED') : '';
    }

    public function remove(): void
    {
        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE '" . $this->name . "_%'");
        if ($this->columnExists(TABLE_ADMIN_ACCESS, 'mits_customers_notice')) {
            xtc_db_query("ALTER TABLE " . TABLE_ADMIN_ACCESS . " DROP COLUMN `mits_customers_notice`");
        }
    }

    public function custom(): void
    {
        global $messageStack;

        $this->remove();
        $this->removeModuleFiles();

        if (defined($this->name . '_DELETE_FINISHED')) {
            $messageStack->add_session(constant($this->name . '_DELETE_FINISHED'), 'success');
        }
    }

    public function keys(): array
    {
        return array(
            $this->name . '_STATUS',
            $this->name . '_DEFAULT_SELECTOR',
            $this->name . '_ASSET_MODE',
            $this->name . '_MAX_DISPLAY_RESULTS'
        );
    }

    private function dbChanges(): void
    {
        if (!defined($this->name . '_STATUS')) {
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_STATUS', 'true', 6, 1, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        }
        if (!defined($this->name . '_DEFAULT_SELECTOR')) {
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('" . $this->name . "_DEFAULT_SELECTOR', '#main-content, main, .content_big, .content_full, #col_right .col_right_inner, #col_right, #col_full, #contentwrap, #content, #layout_content, #main', 6, 2, now())");
        }
        if (!defined($this->name . '_ASSET_MODE')) {
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_ASSET_MODE', 'external', 6, 3, 'xtc_cfg_select_option(array(\'external\', \'inline\', \'manual\'), ', now())");
        }
        if (!defined($this->name . '_MAX_DISPLAY_RESULTS')) {
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('" . $this->name . "_MAX_DISPLAY_RESULTS', '20', 6, 4, now())");
        }
        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = '" . $this->name . "_LOAD_CSS'");
        if (!defined($this->name . '_VERSION')) {
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('" . $this->name . "_VERSION', '" . $this->version . "', 6, 99, now())");
        } else {
            xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '" . $this->version . "' WHERE configuration_key = '" . $this->name . "_VERSION'");
        }

        if (!$this->columnExists(TABLE_ADMIN_ACCESS, 'mits_customers_notice')) {
            xtc_db_query("ALTER TABLE " . TABLE_ADMIN_ACCESS . " ADD `mits_customers_notice` INT(1) NOT NULL DEFAULT '0'");
            xtc_db_query("UPDATE " . TABLE_ADMIN_ACCESS . " SET `mits_customers_notice` = 1 WHERE customers_id != 'groups'");
        }

        $engine = defined('DB_SERVER_ENGINE') ? ' ENGINE=' . DB_SERVER_ENGINE : '';
        $charset = defined('DB_SERVER_CHARSET') ? ' DEFAULT CHARSET=' . DB_SERVER_CHARSET . ' COLLATE=' . DB_SERVER_CHARSET . '_general_ci' : '';

        xtc_db_query(
            "CREATE TABLE IF NOT EXISTS `mits_customers_notice` (
                `notice_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `internal_title` VARCHAR(255) NOT NULL DEFAULT '',
                `status` TINYINT(1) NOT NULL DEFAULT 0,
                `sort_order` INT NOT NULL DEFAULT 0,
                `start_at` DATETIME NULL,
                `end_at` DATETIME NULL,
                `display_type` VARCHAR(32) NOT NULL DEFAULT 'notice',
                `template_file` VARCHAR(128) NOT NULL DEFAULT '',
                `placement` VARCHAR(16) NOT NULL DEFAULT 'auto',
                `selector` VARCHAR(255) NOT NULL DEFAULT '',
                `insert_method` VARCHAR(16) NOT NULL DEFAULT 'prepend',
                `audience` VARCHAR(16) NOT NULL DEFAULT 'all',
                `frequency` VARCHAR(16) NOT NULL DEFAULT 'always',
                `dismissible` TINYINT(1) NOT NULL DEFAULT 0,
                `countdown` TINYINT(1) NOT NULL DEFAULT 0,
                `no_snippet` TINYINT(1) NOT NULL DEFAULT 1,
                `toast_position` VARCHAR(20) NOT NULL DEFAULT 'bottom-right',
                `css_class` VARCHAR(128) NOT NULL DEFAULT '',
                `legacy_notice_id` INT UNSIGNED NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`notice_id`),
                UNIQUE KEY `idx_mits_cn_legacy` (`legacy_notice_id`),
                KEY `idx_mits_cn_active` (`status`, `start_at`, `end_at`),
                KEY `idx_mits_cn_sort` (`sort_order`, `notice_id`)
            )" . $engine . $charset
        );

        $internalTitleAdded = false;
        if (!$this->columnExists('mits_customers_notice', 'internal_title')) {
            xtc_db_query("ALTER TABLE `mits_customers_notice` ADD `internal_title` VARCHAR(255) NOT NULL DEFAULT '' AFTER `notice_id`");
            $internalTitleAdded = true;
        }

        if (!$this->columnExists('mits_customers_notice', 'no_snippet')) {
            xtc_db_query("ALTER TABLE `mits_customers_notice` ADD `no_snippet` TINYINT(1) NOT NULL DEFAULT 1 AFTER `countdown`");
        }
        if (!$this->columnExists('mits_customers_notice', 'toast_position')) {
            xtc_db_query("ALTER TABLE `mits_customers_notice` ADD `toast_position` VARCHAR(20) NOT NULL DEFAULT 'bottom-right' AFTER `no_snippet`");
        }

        xtc_db_query(
            "CREATE TABLE IF NOT EXISTS `mits_customers_notice_description` (
                `notice_id` INT UNSIGNED NOT NULL,
                `language_id` INT UNSIGNED NOT NULL,
                `title` VARCHAR(255) NOT NULL DEFAULT '',
                `content` MEDIUMTEXT NULL,
                `button_text` VARCHAR(255) NOT NULL DEFAULT '',
                `button_url` VARCHAR(1024) NOT NULL DEFAULT '',
                PRIMARY KEY (`notice_id`, `language_id`),
                KEY `idx_mits_cn_desc_lang` (`language_id`)
            )" . $engine . $charset
        );

        if ($internalTitleAdded) {
            xtc_db_query(
                "UPDATE `mits_customers_notice` n
                    SET n.`internal_title` = COALESCE(
                        NULLIF((
                            SELECT d.`title`
                              FROM `mits_customers_notice_description` d
                             WHERE d.`notice_id` = n.`notice_id`
                               AND TRIM(d.`title`) <> ''
                               AND UPPER(TRIM(d.`title`)) <> 'NOTITLE'
                          ORDER BY d.`language_id`
                             LIMIT 1
                        ), ''),
                        CONCAT('Kundenhinweis #', n.`notice_id`)
                    )
                  WHERE n.`internal_title` = ''"
            );
        }

        xtc_db_query(
            "CREATE TABLE IF NOT EXISTS `mits_customers_notice_targets` (
                `target_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `notice_id` INT UNSIGNED NOT NULL,
                `target_type` VARCHAR(32) NOT NULL DEFAULT '',
                `target_value` VARCHAR(255) NOT NULL DEFAULT '',
                PRIMARY KEY (`target_id`),
                KEY `idx_mits_cn_target_notice` (`notice_id`, `target_type`)
            )" . $engine . $charset
        );
    }

    private function columnExists(string $table, string $column): bool
    {
        $query = xtc_db_query("SHOW COLUMNS FROM `" . str_replace('`', '', $table) . "` LIKE '" . xtc_db_input($column) . "'");
        return xtc_db_num_rows($query) > 0;
    }

    private function deleteDirectory(string $directory): bool
    {
        if (!file_exists($directory)) {
            return true;
        }
        if (!is_dir($directory)) {
            return @unlink($directory);
        }

        $files = array_diff(scandir($directory), array('.', '..'));
        foreach ($files as $file) {
            $path = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }

        return @rmdir($directory);
    }

    private function removeModuleFiles(): void
    {
        $adminDirectory = defined('DIR_ADMIN') ? DIR_ADMIN : 'admin/';
        $adminDirectory = rtrim($adminDirectory, '/\\') . '/';
        $root = rtrim(DIR_FS_DOCUMENT_ROOT, '/\\') . DIRECTORY_SEPARATOR;

        $files = array(
            $root . $adminDirectory . 'includes/modules/system/MITS_Customers_Notice.php',
            $root . $adminDirectory . 'includes/extra/filenames/mits_customers_notice.php',
            $root . $adminDirectory . 'includes/extra/menu/mits_customers_notice.php',
            $root . $adminDirectory . 'mits_customers_notice.php',
            $root . 'includes/extra/application_bottom/50_mits_customers_notice.php',
            $root . 'includes/extra/database_tables/mits_customers_notice.php',
            $root . 'includes/extra/header/header_body/50_mits_customers_notice.php',
            $root . 'includes/extra/header/header_head/50_mits_customers_notice.php',
            $root . 'includes/extra/wysiwyg/mits_customers_notice.php',
        );

        $languages = array('german', 'english', 'french', 'italian', 'spanish', 'dutch', 'polish');
        foreach ($languages as $language) {
            $files[] = $root . 'lang/' . $language . '/extra/admin/mits_customers_notice.php';
            $files[] = $root . 'lang/' . $language . '/extra/mits_customers_notice.php';
            $files[] = $root . 'lang/' . $language . '/modules/system/MITS_Customers_Notice.php';
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        $this->deleteDirectory($root . 'includes/external/mits_customers_notice');
    }
}
