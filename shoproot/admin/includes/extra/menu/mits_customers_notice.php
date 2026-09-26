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

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

if (defined('MODULE_MITS_CUSTOMERS_NOTICE_STATUS') && MODULE_MITS_CUSTOMERS_NOTICE_STATUS == 'true') {
    $add_contents[BOX_CUSTOMERS][] = array(
        'admin_access_name' => 'mits_customers_notice',
        'filename' => FILENAME_MITS_CUSTOMERS_NOTICE,
        'boxname' => defined('BOX_MITS_CUSTOMERS_NOTICE') ? BOX_MITS_CUSTOMERS_NOTICE : 'MITS Kundenhinweise',
        'parameters' => '',
        'ssl' => ''
    );
}
