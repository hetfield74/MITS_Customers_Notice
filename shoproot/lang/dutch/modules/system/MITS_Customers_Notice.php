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
    'MODULE_' . $modulname . '_TITLE' => 'MITS Klantmeldingen <span style="white-space:nowrap;">&copy; by <span style="padding:2px;background:#ffe;color:#6a9;font-weight:bold;">Hetfield (MerZ IT-SerVice)</span></span>',
    'MODULE_' . $modulname . '_DESCRIPTION' => '
        <a href="https://www.merz-it-service.de/" target="_blank"><img src="' . DIR_WS_EXTERNAL . 'mits_customers_notice/images/merz-it-service.png" border="0" alt="MerZ IT-SerVice" style="display:block;max-width:100%;height:auto;" /></a><br />
        <p><strong>Flexibele klantmeldingen zonder core-wijzigingen</strong></p>
        <p>Meertalige meldingen, belangrijke berichten, bovenbalken, modals en toasts met planning, targeting, paginafilters, aftellen en weergavefrequentie.</p>
        <p>De standaardtemplates staan in <code>includes/external/mits_customers_notice/templates/module</code>. Overrides in <code>templates/&lt;JOUW_TEMPLATE&gt;/module/mits_customers_notice/</code> krijgen automatisch voorrang.</p>
        <p>Bestaande gegevens uit de oude extensie <code>customers_notice</code> kunnen optioneel en niet-destructief worden gemigreerd.</p>',
    'MODULE_' . $modulname . '_STATUS_TITLE' => 'Status',
    'MODULE_' . $modulname . '_STATUS_DESC' => 'MITS Klantmeldingen activeren',
    'MODULE_' . $modulname . '_DEFAULT_SELECTOR_TITLE' => 'Standaard CSS-selector',
    'MODULE_' . $modulname . '_DEFAULT_SELECTOR_DESC' => 'Doel voor automatisch geplaatste inline-meldingen. De eerste passende selector wordt gebruikt. Kan per melding worden overschreven.',
    'MODULE_' . $modulname . '_ASSET_MODE_TITLE' => 'CSS/JavaScript-integratie',
    'MODULE_' . $modulname . '_ASSET_MODE_DESC' => '<strong>external</strong>: automatisch cachebare modulebestanden laden.<br><strong>inline</strong>: CSS en JavaScript direct in de pagina uitvoeren.<br><strong>manual</strong>: geen automatische uitvoer; kopieer CSS en JavaScript naar het actieve template en integreer ze daar, bijvoorbeeld om de compressie daarvan te gebruiken.',
    'MODULE_' . $modulname . '_MAX_DISPLAY_RESULTS_TITLE' => 'Meldingen per pagina in beheer',
    'MODULE_' . $modulname . '_MAX_DISPLAY_RESULTS_DESC' => 'Hoeveel klantmeldingen moeten per pagina in het beheeroverzicht worden weergegeven?',
    'MODULE_' . $modulname . '_OPEN_ADMIN' => 'Klantmeldingen beheren',
    'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => ' <span style="font-weight:bold;color:#900;background:#ff6;padding:2px;border:1px solid #900;">Werk de module bij!</span>',
    'MODULE_' . $modulname . '_UPDATE_MODUL' => 'Module bijwerken',
    'MODULE_' . $modulname . '_UPDATE_FINISHED' => 'MITS Klantmeldingen is bijgewerkt.',
    'MODULE_' . $modulname . '_DELETE_MODUL' => 'MITS Klantmeldingen volledig van de server verwijderen',
    'MODULE_' . $modulname . '_CONFIRM_DELETE_MODUL' => 'Wilt u MITS Klantmeldingen inclusief alle meegeleverde modulebestanden echt van de server verwijderen? Eigen bestanden en overrides in het actieve winkeltemplate blijven behouden.',
    'MODULE_' . $modulname . '_DELETE_FINISHED' => 'MITS Klantmeldingen en alle meegeleverde modulebestanden zijn van de server verwijderd.'
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
