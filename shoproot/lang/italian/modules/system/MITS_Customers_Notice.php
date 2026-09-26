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
    'MODULE_' . $modulname . '_TITLE' => 'MITS Avvisi clienti <span style="white-space:nowrap;">&copy; by <span style="padding:2px;background:#ffe;color:#6a9;font-weight:bold;">Hetfield (MerZ IT-SerVice)</span></span>',
    'MODULE_' . $modulname . '_DESCRIPTION' => '
        <a href="https://www.merz-it-service.de/" target="_blank"><img src="' . DIR_WS_EXTERNAL . 'mits_customers_notice/images/merz-it-service.png" border="0" alt="MerZ IT-SerVice" style="display:block;max-width:100%;height:auto;" /></a><br />
        <p><strong>Avvisi clienti flessibili senza modifiche al core</strong></p>
        <p>Avvisi multilingua, messaggi importanti, barre superiori, finestre modali e toast con programmazione, targeting, filtri pagina, conto alla rovescia e frequenza di visualizzazione.</p>
        <p>I template standard si trovano in <code>includes/external/mits_customers_notice/templates/module</code>. Gli override in <code>templates/&lt;IL_TUO_TEMPLATE&gt;/module/mits_customers_notice/</code> hanno automaticamente la priorit&agrave;.</p>
        <p>I dati esistenti della vecchia estensione <code>customers_notice</code> possono essere migrati facoltativamente e in modo non distruttivo.</p>',
    'MODULE_' . $modulname . '_STATUS_TITLE' => 'Status',
    'MODULE_' . $modulname . '_STATUS_DESC' => 'Attiva MITS Avvisi clienti',
    'MODULE_' . $modulname . '_DEFAULT_SELECTOR_TITLE' => 'Selettore CSS predefinito',
    'MODULE_' . $modulname . '_DEFAULT_SELECTOR_DESC' => 'Destinazione per gli avvisi inline posizionati automaticamente. Viene utilizzato il primo selettore corrispondente. Pu&ograve; essere sovrascritto per ogni avviso.',
    'MODULE_' . $modulname . '_ASSET_MODE_TITLE' => 'Integrazione CSS/JavaScript',
    'MODULE_' . $modulname . '_ASSET_MODE_DESC' => '<strong>external</strong>: carica automaticamente i file del modulo memorizzabili nella cache.<br><strong>inline</strong>: inserisce CSS e JavaScript direttamente nella pagina.<br><strong>manual</strong>: nessun output automatico; copiare CSS e JavaScript nel template attivo e integrarli l&igrave;, ad esempio per utilizzare la relativa compressione.',
    'MODULE_' . $modulname . '_MAX_DISPLAY_RESULTS_TITLE' => 'Avvisi per pagina nell\'amministrazione',
    'MODULE_' . $modulname . '_MAX_DISPLAY_RESULTS_DESC' => 'Quanti avvisi clienti devono essere visualizzati per pagina nella panoramica amministrativa?',
    'MODULE_' . $modulname . '_OPEN_ADMIN' => 'Gestisci avvisi clienti',
    'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => ' <span style="font-weight:bold;color:#900;background:#ff6;padding:2px;border:1px solid #900;">Aggiornare il modulo!</span>',
    'MODULE_' . $modulname . '_UPDATE_MODUL' => 'Aggiorna modulo',
    'MODULE_' . $modulname . '_UPDATE_FINISHED' => 'MITS Avvisi clienti &egrave; stato aggiornato.',
    'MODULE_' . $modulname . '_DELETE_MODUL' => 'Rimuovi completamente MITS Avvisi clienti dal server',
    'MODULE_' . $modulname . '_CONFIRM_DELETE_MODUL' => 'Rimuovere davvero MITS Avvisi clienti e tutti i file del modulo forniti dal server? I file personalizzati e gli override nel template attivo verranno mantenuti.',
    'MODULE_' . $modulname . '_DELETE_FINISHED' => 'MITS Avvisi clienti e tutti i file del modulo forniti sono stati rimossi dal server.'
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
