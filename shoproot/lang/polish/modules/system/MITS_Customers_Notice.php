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
    'MODULE_' . $modulname . '_TITLE' => 'MITS Komunikaty dla klient&oacute;w <span style="white-space:nowrap;">&copy; by <span style="padding:2px;background:#ffe;color:#6a9;font-weight:bold;">Hetfield (MerZ IT-SerVice)</span></span>',
    'MODULE_' . $modulname . '_DESCRIPTION' => '
        <a href="https://www.merz-it-service.de/" target="_blank"><img src="' . DIR_WS_EXTERNAL . 'mits_customers_notice/images/merz-it-service.png" border="0" alt="MerZ IT-SerVice" style="display:block;max-width:100%;height:auto;" /></a><br />
        <p><strong>Elastyczne komunikaty dla klient&oacute;w bez zmian w core</strong></p>
        <p>Wieloj&#281;zyczne komunikaty, wa&#380;ne informacje, g&oacute;rne paski, okna modalne i toasty z harmonogramem, kierowaniem, filtrami stron, odliczaniem i cz&#281;stotliwo&#347;ci&#261; wy&#347;wietlania.</p>
        <p>Standardowe template\'y znajduj&#261; si&#281; w <code>includes/external/mits_customers_notice/templates/module</code>. Nadpisania w <code>templates/&lt;TWOJ_TEMPLATE&gt;/module/mits_customers_notice/</code> maj&#261; automatycznie pierwsze&#324;stwo.</p>
        <p>Istniej&#261;ce dane ze starego rozszerzenia <code>customers_notice</code> mo&#380;na opcjonalnie migrowa&#263; bez destrukcyjnych zmian.</p>',
    'MODULE_' . $modulname . '_STATUS_TITLE' => 'Status',
    'MODULE_' . $modulname . '_STATUS_DESC' => 'Aktywuj MITS Komunikaty dla klient&oacute;w',
    'MODULE_' . $modulname . '_DEFAULT_SELECTOR_TITLE' => 'Domy&#347;lny selektor CSS',
    'MODULE_' . $modulname . '_DEFAULT_SELECTOR_DESC' => 'Cel dla automatycznie umieszczanych komunikat&oacute;w inline. U&#380;ywany jest pierwszy pasuj&#261;cy selektor. Mo&#380;na go nadpisa&#263; dla ka&#380;dego komunikatu.',
    'MODULE_' . $modulname . '_ASSET_MODE_TITLE' => 'Integracja CSS/JavaScript',
    'MODULE_' . $modulname . '_ASSET_MODE_DESC' => '<strong>external</strong>: automatycznie &#322;aduje pliki modu&#322;u, kt&oacute;re mog&#261; by&#263; buforowane.<br><strong>inline</strong>: umieszcza CSS i JavaScript bezpo&#347;rednio na stronie.<br><strong>manual</strong>: bez automatycznego wyj&#347;cia; skopiuj CSS i JavaScript do aktywnego template\'u i zintegruj je tam, np. aby korzysta&#263; z jego kompresji.',
    'MODULE_' . $modulname . '_MAX_DISPLAY_RESULTS_TITLE' => 'Komunikaty na stron&#281; w administracji',
    'MODULE_' . $modulname . '_MAX_DISPLAY_RESULTS_DESC' => 'Ile komunikat&oacute;w dla klient&oacute;w ma by&#263; wy&#347;wietlanych na jednej stronie przegl&#261;du administracyjnego?',
    'MODULE_' . $modulname . '_OPEN_ADMIN' => 'Zarz&#261;dzaj komunikatami dla klient&oacute;w',
    'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => ' <span style="font-weight:bold;color:#900;background:#ff6;padding:2px;border:1px solid #900;">Zaktualizuj modu&#322;!</span>',
    'MODULE_' . $modulname . '_UPDATE_MODUL' => 'Aktualizuj modu&#322;',
    'MODULE_' . $modulname . '_UPDATE_FINISHED' => 'MITS Komunikaty dla klient&oacute;w zosta&#322; zaktualizowany.',
    'MODULE_' . $modulname . '_DELETE_MODUL' => 'Usu&#324; MITS Komunikaty dla klient&oacute;w ca&#322;kowicie z serwera',
    'MODULE_' . $modulname . '_CONFIRM_DELETE_MODUL' => 'Czy na pewno usun&#261;&#263; MITS Komunikaty dla klient&oacute;w wraz ze wszystkimi dostarczonymi plikami modu&#322;u? W&#322;asne pliki i nadpisania w aktywnym template sklepu zostan&#261; zachowane.',
    'MODULE_' . $modulname . '_DELETE_FINISHED' => 'MITS Komunikaty dla klient&oacute;w i wszystkie dostarczone pliki modu&#322;u zosta&#322;y usuni&#281;te z serwera.'
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
