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
    'MODULE_' . $modulname . '_TITLE' => 'MITS Avis clients <span style="white-space:nowrap;">&copy; by <span style="padding:2px;background:#ffe;color:#6a9;font-weight:bold;">Hetfield (MerZ IT-SerVice)</span></span>',
    'MODULE_' . $modulname . '_DESCRIPTION' => '
        <a href="https://www.merz-it-service.de/" target="_blank"><img src="' . DIR_WS_EXTERNAL . 'mits_customers_notice/images/merz-it-service.png" border="0" alt="MerZ IT-SerVice" style="display:block;max-width:100%;height:auto;" /></a><br />
        <p><strong>Avis clients flexibles sans modification du core</strong></p>
        <p>Avis multilingues, messages importants, barres sup&eacute;rieures, fen&ecirc;tres modales et toasts avec planification, ciblage, filtres de pages, compte &agrave; rebours et fr&eacute;quence d\'affichage.</p>
        <p>Les templates standard se trouvent dans <code>includes/external/mits_customers_notice/templates/module</code>. Les surcharges dans <code>templates/&lt;VOTRE_TEMPLATE&gt;/module/mits_customers_notice/</code> sont automatiquement prioritaires.</p>
        <p>Les donn&eacute;es existantes de l\'ancienne extension <code>customers_notice</code> peuvent &ecirc;tre migr&eacute;es de mani&egrave;re facultative et non destructive.</p>',
    'MODULE_' . $modulname . '_STATUS_TITLE' => 'Status',
    'MODULE_' . $modulname . '_STATUS_DESC' => 'Activer MITS Avis clients',
    'MODULE_' . $modulname . '_DEFAULT_SELECTOR_TITLE' => 'S&eacute;lecteur CSS par d&eacute;faut',
    'MODULE_' . $modulname . '_DEFAULT_SELECTOR_DESC' => 'Cible pour les avis en ligne plac&eacute;s automatiquement. Le premier s&eacute;lecteur correspondant est utilis&eacute;. Peut &ecirc;tre remplac&eacute; pour chaque avis.',
    'MODULE_' . $modulname . '_ASSET_MODE_TITLE' => 'Int&eacute;gration CSS/JavaScript',
    'MODULE_' . $modulname . '_ASSET_MODE_DESC' => '<strong>external</strong> : charger automatiquement les fichiers du module pouvant &ecirc;tre mis en cache.<br><strong>inline</strong> : ins&eacute;rer CSS et JavaScript directement dans la page.<br><strong>manual</strong> : aucune sortie automatique ; copier CSS et JavaScript dans le template actif et les int&eacute;grer &agrave; cet endroit, par exemple pour utiliser sa compression.',
    'MODULE_' . $modulname . '_MAX_DISPLAY_RESULTS_TITLE' => 'Avis par page dans l\'administration',
    'MODULE_' . $modulname . '_MAX_DISPLAY_RESULTS_DESC' => 'Combien d\'avis clients doivent &ecirc;tre affich&eacute;s par page dans l\'aper&ccedil;u de l\'administration ?',
    'MODULE_' . $modulname . '_OPEN_ADMIN' => 'G&eacute;rer les avis clients',
    'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => ' <span style="font-weight:bold;color:#900;background:#ff6;padding:2px;border:1px solid #900;">Veuillez mettre &agrave; jour le module !</span>',
    'MODULE_' . $modulname . '_UPDATE_MODUL' => 'Mettre &agrave; jour le module',
    'MODULE_' . $modulname . '_UPDATE_FINISHED' => 'MITS Avis clients a &eacute;t&eacute; mis &agrave; jour.',
    'MODULE_' . $modulname . '_DELETE_MODUL' => 'Supprimer compl&egrave;tement MITS Avis clients du serveur',
    'MODULE_' . $modulname . '_CONFIRM_DELETE_MODUL' => 'Voulez-vous vraiment supprimer MITS Avis clients ainsi que tous les fichiers du module fournis du serveur ? Les fichiers personnalis&eacute;s et les surcharges du template actif sont conserv&eacute;s.',
    'MODULE_' . $modulname . '_DELETE_FINISHED' => 'MITS Avis clients et tous les fichiers du module fournis ont &eacute;t&eacute; supprim&eacute;s du serveur.'
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
