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
    'MODULE_' . $modulname . '_TITLE' => 'MITS Kundenhinweise <span style="white-space:nowrap;">&copy; by <span style="padding:2px;background:#ffe;color:#6a9;font-weight:bold;">Hetfield (MerZ IT-SerVice)</span></span>',
    'MODULE_' . $modulname . '_DESCRIPTION' => '
        <a href="https://www.merz-it-service.de/" target="_blank"><img src="' . DIR_WS_EXTERNAL . 'mits_customers_notice/images/merz-it-service.png" border="0" alt="MerZ IT-SerVice" style="display:block;max-width:100%;height:auto;" /></a><br />
        <p><strong>Flexible Kundenhinweise ohne Core-&Auml;nderungen</strong></p>
        <p>Mehrsprachige Hinweise, wichtige Meldungen, Top-Bars, Modals und Toasts mit Zeitsteuerung, Zielgruppen, Seitenfiltern, Countdown und Anzeigeh&auml;ufigkeit.</p>
        <p>Die Standardvorlagen liegen unter <code>includes/external/mits_customers_notice/templates/module</code>. Eigene Vorlagen im aktiven Template unter <code>templates/&lt;DEIN_TEMPLATE&gt;/module/mits_customers_notice/</code> werden automatisch bevorzugt.</p>
        <p>Vorhandene Daten des alten Fremdmoduls <code>customers_notice</code> k&ouml;nnen optional und nicht destruktiv &uuml;bernommen werden.</p>
        <div style="text-align:center;"><a style="background:#6a9;color:#444" target="_blank" href="https://www.merz-it-service.de/Kontakt.html" class="button" onclick="this.blur();">Kontaktseite auf MerZ-IT-SerVice.de</a></div>',
    'MODULE_' . $modulname . '_STATUS_TITLE' => 'Status',
    'MODULE_' . $modulname . '_STATUS_DESC' => 'MITS Kundenhinweise aktivieren',
    'MODULE_' . $modulname . '_DEFAULT_SELECTOR_TITLE' => 'Standard CSS-Selektor',
    'MODULE_' . $modulname . '_DEFAULT_SELECTOR_DESC' => 'Ziel f&uuml;r automatisch platzierte Inline-Hinweise. Der erste gefundene Selektor wird verwendet. Kann je Hinweis &uuml;berschrieben werden.',
    'MODULE_' . $modulname . '_ASSET_MODE_TITLE' => 'CSS-/JavaScript-Einbindung',
    'MODULE_' . $modulname . '_ASSET_MODE_DESC' => '<strong>external</strong>: Moduldateien automatisch und cachef&auml;hig laden.<br><strong>inline</strong>: CSS und JavaScript direkt in die Seite ausgeben.<br><strong>manual</strong>: keine automatische Asset-Ausgabe; CSS und JavaScript in das aktive Template kopieren und dort einbinden, z. B. f&uuml;r dessen Komprimierung.',
    'MODULE_' . $modulname . '_MAX_DISPLAY_RESULTS_TITLE' => 'Hinweise pro Seite in der Administration',
    'MODULE_' . $modulname . '_MAX_DISPLAY_RESULTS_DESC' => 'Wie viele Kundenhinweise sollen pro Seite in der Verwaltungs&uuml;bersicht angezeigt werden?',
    'MODULE_' . $modulname . '_OPEN_ADMIN' => 'Kundenhinweise verwalten',
    'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => ' <span style="font-weight:bold;color:#900;background:#ff6;padding:2px;border:1px solid #900;">Bitte Modulaktualisierung durchf&uuml;hren!</span>',
    'MODULE_' . $modulname . '_UPDATE_MODUL' => 'Modul aktualisieren',
    'MODULE_' . $modulname . '_UPDATE_FINISHED' => 'MITS Kundenhinweise wurde aktualisiert.',
    'MODULE_' . $modulname . '_DELETE_MODUL' => 'MITS Kundenhinweise komplett vom Server entfernen',
    'MODULE_' . $modulname . '_CONFIRM_DELETE_MODUL' => 'M&ouml;chten Sie MITS Kundenhinweise inklusive aller mitgelieferten Moduldateien wirklich vom Server l&ouml;schen? Eigene Dateien und Anpassungen im aktiven Shoptemplate bleiben erhalten.',
    'MODULE_' . $modulname . '_DELETE_FINISHED' => 'MITS Kundenhinweise wurde mit allen mitgelieferten Moduldateien vom Server entfernt.'
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
