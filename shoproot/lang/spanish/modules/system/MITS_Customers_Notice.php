<?php
/**
 * --------------------------------------------------------------
 * File: MITS_Customers_Notice.php
 * Date: 25.09.2026
 *
 * Author: Hetfield
 * Copyright: (c) 2026 - MerZ IT-SerVice
 * Web: https://www.merz-it-service.de
 * Contact: info@merz-it-service.de
 * --------------------------------------------------------------
 */

$modulname = strtoupper('MITS_Customers_Notice');
$lang_array = array(
    'MODULE_' . $modulname . '_TITLE' => 'MITS Avisos para clientes <span style="white-space:nowrap;">&copy; by <span style="padding:2px;background:#ffe;color:#6a9;font-weight:bold;">Hetfield (MerZ IT-SerVice)</span></span>',
    'MODULE_' . $modulname . '_DESCRIPTION' => '
        <a href="https://www.merz-it-service.de/" target="_blank"><img src="' . DIR_WS_EXTERNAL . 'mits_customers_notice/images/merz-it-service.png" border="0" alt="MerZ IT-SerVice" style="display:block;max-width:100%;height:auto;" /></a><br />
        <p><strong>Avisos flexibles para clientes sin modificar el core</strong></p>
        <p>Avisos multiling&uuml;es, mensajes importantes, barras superiores, ventanas modales y toasts con programaci&oacute;n, segmentaci&oacute;n, filtros de p&aacute;gina, cuenta atr&aacute;s y frecuencia de visualizaci&oacute;n.</p>
        <p>Los templates est&aacute;ndar se encuentran en <code>includes/external/mits_customers_notice/templates/module</code>. Las sobreescrituras en <code>templates/&lt;TU_TEMPLATE&gt;/module/mits_customers_notice/</code> tienen prioridad autom&aacute;ticamente.</p>
        <p>Los datos existentes de la antigua extensi&oacute;n <code>customers_notice</code> pueden migrarse opcionalmente y de forma no destructiva.</p>',
    'MODULE_' . $modulname . '_STATUS_TITLE' => 'Status',
    'MODULE_' . $modulname . '_STATUS_DESC' => 'Activar MITS Avisos para clientes',
    'MODULE_' . $modulname . '_DEFAULT_SELECTOR_TITLE' => 'Selector CSS predeterminado',
    'MODULE_' . $modulname . '_DEFAULT_SELECTOR_DESC' => 'Destino para los avisos inline colocados autom&aacute;ticamente. Se utiliza el primer selector coincidente. Puede sobrescribirse por aviso.',
    'MODULE_' . $modulname . '_ASSET_MODE_TITLE' => 'Integraci&oacute;n CSS/JavaScript',
    'MODULE_' . $modulname . '_ASSET_MODE_DESC' => '<strong>external</strong>: cargar autom&aacute;ticamente los archivos del m&oacute;dulo almacenables en cach&eacute;.<br><strong>inline</strong>: insertar CSS y JavaScript directamente en la p&aacute;gina.<br><strong>manual</strong>: sin salida autom&aacute;tica; copie CSS y JavaScript al template activo e int&eacute;grelos all&iacute;, por ejemplo para utilizar su compresi&oacute;n.',
    'MODULE_' . $modulname . '_MAX_DISPLAY_RESULTS_TITLE' => 'Avisos por p&aacute;gina en administraci&oacute;n',
    'MODULE_' . $modulname . '_MAX_DISPLAY_RESULTS_DESC' => '&iquest;Cu&aacute;ntos avisos para clientes deben mostrarse por p&aacute;gina en la vista general de administraci&oacute;n?',
    'MODULE_' . $modulname . '_OPEN_ADMIN' => 'Gestionar avisos para clientes',
    'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => ' <span style="font-weight:bold;color:#900;background:#ff6;padding:2px;border:1px solid #900;">&iexcl;Actualice el m&oacute;dulo!</span>',
    'MODULE_' . $modulname . '_UPDATE_MODUL' => 'Actualizar m&oacute;dulo',
    'MODULE_' . $modulname . '_UPDATE_FINISHED' => 'MITS Avisos para clientes se ha actualizado.',
    'MODULE_' . $modulname . '_DELETE_MODUL' => 'Eliminar completamente MITS Avisos para clientes del servidor',
    'MODULE_' . $modulname . '_CONFIRM_DELETE_MODUL' => '&iquest;Desea realmente eliminar MITS Avisos para clientes junto con todos los archivos suministrados del m&oacute;dulo? Los archivos personalizados y las sobreescrituras del template activo se conservar&aacute;n.',
    'MODULE_' . $modulname . '_DELETE_FINISHED' => 'MITS Avisos para clientes y todos los archivos suministrados del m&oacute;dulo se han eliminado del servidor.'
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
