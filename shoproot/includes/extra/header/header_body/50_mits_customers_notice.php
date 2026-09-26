<?php
/**
 * --------------------------------------------------------------
 * File: 50_mits_customers_notice.php
 * Date: 23.09.2026
 *
 * Author: Hetfield
 * Copyright: (c) 2026 - MerZ IT-SerVice
 * Web: https://www.merz-it-service.de
 * Contact: info@merz-it-service.de
 * --------------------------------------------------------------
 */

if (defined('MODULE_MITS_CUSTOMERS_NOTICE_STATUS') && MODULE_MITS_CUSTOMERS_NOTICE_STATUS === 'true') {
    $mitsCustomersNoticeClass = DIR_FS_CATALOG . 'includes/external/mits_customers_notice/classes/MitsCustomersNotice.php';
    if (!class_exists('MitsCustomersNotice') && is_file($mitsCustomersNoticeClass)) {
        require_once $mitsCustomersNoticeClass;
    }
    if (class_exists('MitsCustomersNotice')) {
        MitsCustomersNotice::assignSmarty();
    }
}
