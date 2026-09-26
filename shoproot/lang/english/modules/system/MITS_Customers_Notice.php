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

$modulname = strtoupper('MITS_Customers_Notice');
$lang_array = array(
    'MODULE_' . $modulname . '_TITLE' => 'MITS Customer Notices <span style="white-space:nowrap;">&copy; by <span style="padding:2px;background:#ffe;color:#6a9;font-weight:bold;">Hetfield (MerZ IT-SerVice)</span></span>',
    'MODULE_' . $modulname . '_DESCRIPTION' => '
        <a href="https://www.merz-it-service.de/" target="_blank"><img src="' . DIR_WS_EXTERNAL . 'mits_customers_notice/images/merz-it-service.png" border="0" alt="MerZ IT-SerVice" style="display:block;max-width:100%;height:auto;" /></a><br />
        <p><strong>Flexible customer notices without core changes</strong></p>
        <p>Multilingual notices, important messages, top bars, modals and toasts with scheduling, targeting, page filters, countdowns and display frequency.</p>
        <p>Default templates are stored in <code>includes/external/mits_customers_notice/templates/module</code>. Overrides in <code>templates/&lt;YOUR_TEMPLATE&gt;/module/mits_customers_notice/</code> are preferred automatically.</p>
        <p>Existing data from the legacy <code>customers_notice</code> extension can be migrated optionally and non-destructively.</p>',
    'MODULE_' . $modulname . '_STATUS_TITLE' => 'Status',
    'MODULE_' . $modulname . '_STATUS_DESC' => 'Enable MITS Customer Notices',
    'MODULE_' . $modulname . '_DEFAULT_SELECTOR_TITLE' => 'Default CSS selector',
    'MODULE_' . $modulname . '_DEFAULT_SELECTOR_DESC' => 'Target for automatically placed inline notices. The first matching selector is used. Can be overridden per notice.',
    'MODULE_' . $modulname . '_ASSET_MODE_TITLE' => 'CSS/JavaScript integration',
    'MODULE_' . $modulname . '_ASSET_MODE_DESC' => '<strong>external</strong>: automatically load cacheable module files.<br><strong>inline</strong>: output CSS and JavaScript directly in the page.<br><strong>manual</strong>: no automatic asset output; copy CSS and JavaScript into the active template and integrate them there, for example to use its compression.',
    'MODULE_' . $modulname . '_MAX_DISPLAY_RESULTS_TITLE' => 'Notices per page in administration',
    'MODULE_' . $modulname . '_MAX_DISPLAY_RESULTS_DESC' => 'How many customer notices should be displayed per page in the administration overview?',
    'MODULE_' . $modulname . '_OPEN_ADMIN' => 'Manage customer notices',
    'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => ' <span style="font-weight:bold;color:#900;background:#ff6;padding:2px;border:1px solid #900;">Please update the module!</span>',
    'MODULE_' . $modulname . '_UPDATE_MODUL' => 'Update module',
    'MODULE_' . $modulname . '_UPDATE_FINISHED' => 'MITS Customer Notices has been updated.',
    'MODULE_' . $modulname . '_DELETE_MODUL' => 'Remove MITS Customer Notices completely from the server',
    'MODULE_' . $modulname . '_CONFIRM_DELETE_MODUL' => 'Do you really want to remove MITS Customer Notices including all supplied module files from the server? Custom files and overrides in the active shop template will be kept.',
    'MODULE_' . $modulname . '_DELETE_FINISHED' => 'MITS Customer Notices and all supplied module files have been removed from the server.'
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
