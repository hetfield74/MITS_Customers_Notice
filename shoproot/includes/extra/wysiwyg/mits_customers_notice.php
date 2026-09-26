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

$language_id = (isset($langID) && !isset($language_id)) ? $langID : ($language_id ?? '');
if (!empty($language_id) && isset($type) && $type === 'mits_customers_notice') {
    $editorName = 'content_' . (int)$language_id;
    $default_editor_height = 260;
}
