# MITS Kundenhinweise für modified eCommerce Shopsoftware

(c) Copyright 2026 by Hetfield - MerZ IT-SerVice

* **Author:** Hetfield - https://www.merz-it-service.de
* **Modulversion:** 1.0.6
* **Shopversion:** modified eCommerce Shopsoftware ab Version 2.0.6.0
* **PHP:** 8.x

---

Mit **MITS Kundenhinweise** lassen sich wichtige Informationen, Aktionen und Mitteilungen gezielt in der **modified eCommerce Shopsoftware** anzeigen. Hinweise können nach Seiten, Kundengruppen, Zeitraum und weiteren Bedingungen gesteuert und passend zum Einsatzzweck als Hinweisbox, wichtiger Hinweis, Top-Bar, Modal/Popup oder Toast ausgegeben werden.

Das Modul wurde als eigenständige MITS-Lösung entwickelt und benötigt **keine Änderungen an Core-Dateien**. Eine manuelle SQL-Installation ist ebenfalls nicht erforderlich.

## Funktionsübersicht

* fünf Darstellungsarten: **Hinweisbox, wichtiger Hinweis, Top-Bar, Modal/Popup und Toast**
* sechs wählbare Toast-Positionen: oben/unten sowie links, mittig oder rechts
* mehrsprachige Überschrift, HTML-Inhalte, Buttontexte und Button-URLs
* separater interner Titel für eine übersichtliche Verwaltung
* zeitgesteuerte Hinweise mit Start- und Endzeit
* optionaler Countdown bis zum Ende eines Hinweises
* Anzeigehäufigkeit steuerbar: immer, einmal pro Session, einmal pro Tag oder bis zum Schließen
* optionale Aktionsbuttons innerhalb der Hinweise
* gezielte Anzeige nach **Seiten und Kundengruppen**
* weitere Zielgruppen und Filter nach Loginstatus, einzelnen Kunden, Ländern und Newsletterstatus
* Zuordnung zu Kategorien, Artikeln und Herstellern möglich
* optionales `data-nosnippet` je Hinweis
* automatische oder manuelle Platzierung in der modified eCommerce Shopsoftware
* frei definierbarer CSS-Selektor und Einfügemethode für die automatische Platzierung
* eigene CSS-Klasse je Hinweis möglich
* WYSIWYG-Editor für die komfortable Pflege der Inhalte
* Standard-Templates für alle Darstellungsarten
* updatesichere Template-Overrides im aktiven Shoptemplate
* zusätzliche eigene HTML-Templates werden automatisch erkannt und im Dropdown angeboten
* drei Möglichkeiten zur Einbindung von CSS und JavaScript: **external, inline oder manual**
* im manuellen Modus Einbindung in die vorhandene CSS-/JavaScript-Kombinierung des Shoptemplates möglich
* moderne MITS-Verwaltungsoberfläche mit Suche und Filtern
* Paginierung mit einstellbarer Anzahl von Einträgen pro Seite
* sortierbare Tabellenansicht
* Sortierreihenfolge per Drag & Drop
* **Speichern** oder **Speichern & weiter bearbeiten**
* Nutzung des in der modified eCommerce Shopsoftware vorhandenen CSRF-Schutzes für schreibende Adminaktionen
* optionale, nicht destruktive Migration aus dem älteren Fremdmodul `customers_notice`
* keine manuelle SQL-Installation notwendig
* keine Änderungen an Core-Dateien erforderlich

## Sprachen

Das Modul liefert Sprachdateien für folgende Sprachen mit:

* Deutsch
* Englisch
* Französisch
* Italienisch
* Spanisch
* Niederländisch
* Polnisch

Die eigentlichen Kundenhinweise können wie gewohnt für alle in der modified eCommerce Shopsoftware aktivierten Sprachen gepflegt werden.

## Unterstützte Shoptemplates

Das Modul ist für die üblichen Template-Strukturen der modified eCommerce Shopsoftware ausgelegt und unterstützt unter anderem:

* **MITS Responsive Modern**
* **tpl_modified_responsive**
* **tpl_modified_nova**
* **Bootstrap5**
* **Bootstrap5a**

Auch weitere bzw. individuell angepasste Shoptemplates können verwendet werden. Für die automatische Platzierung kann bei Bedarf ein eigener CSS-Selektor angegeben werden. Eigene Template-Dateien und CSS-Anpassungen können updatesicher im aktiven Shoptemplate hinterlegt werden.

## Installation und Bedienung

Die ausführliche Installationsanleitung ist hier zu finden:

**[Installationsanleitung MITS Kundenhinweise](https://docs.merz-it-service.de/mits_customers_notice/installationsanleitung.html)**

Die ausführliche Bedienungsanleitung ist hier zu finden:

**[Bedienungsanleitung MITS Kundenhinweise](https://docs.merz-it-service.de/mits_customers_notice/bedienungsanleitung.html)**

Vor der Installation oder einem Update sollte grundsätzlich eine vollständige Sicherung der Dateien und der Datenbank der modified eCommerce Shopsoftware erstellt werden.

## Migration aus dem älteren Modul `customers_notice`

Sind die Tabellen des älteren Fremdmoduls `customers_notice` vorhanden, kann das Modul vorhandene Kundenhinweise optional übernehmen.

Vor der Migration wird eine Vorschau angezeigt. Die vorhandenen Tabellen und Daten des Fremdmoduls werden dabei **nicht verändert oder gelöscht**. Bereits übernommene Hinweise werden bei einem erneuten Import erkannt und nicht doppelt angelegt.

Nach erfolgreicher Kontrolle der übernommenen Kundenhinweise sollte die Ausgabe des alten Fremdmoduls deaktiviert werden, damit Hinweise nicht doppelt in der modified eCommerce Shopsoftware ausgegeben werden.

## Deinstallation

Beim normalen Entfernen des Systemmoduls bleiben die angelegten Kundenhinweise und die zugehörigen MITS-Datentabellen absichtlich erhalten. Dadurch gehen die Daten bei einer späteren Neuinstallation nicht verloren.

Nach der Deinstallation steht zusätzlich die MITS-typische Funktion zur vollständigen Entfernung der mitgelieferten Moduldateien vom Server zur Verfügung.

Eigene Template-Overrides und manuell in das aktive Shoptemplate kopierte CSS-/JavaScript-Dateien werden dabei bewusst nicht gelöscht, damit individuelle Anpassungen erhalten bleiben.

## Support

Wir hoffen, das Modul **MITS Kundenhinweise** für die modified eCommerce Shopsoftware gefällt Ihnen!

Benötigen Sie Unterstützung bei der Installation, Einrichtung oder individuellen Anpassung des Moduls oder haben Sie Probleme bei der Verwendung? Gerne können Sie unseren kostenpflichtigen Support in Anspruch nehmen.

Kontaktieren Sie uns einfach über:

**https://www.merz-it-service.de/Kontakt.html**

---

<img src="https://www.merz-it-service.de/images/logo.png" alt="MerZ IT-SerVice" title="MerZ IT-SerVice" />

**MerZ IT-SerVice**
Nicole Grewe - Am Berndebach 35a - D-57439 Attendorn
Telefon: 0 27 22 - 63 13 63 - Telefax: 0 27 22 - 63 14 00
E-Mail: [Info(at)MerZ-IT-SerVice.de](https://www.merz-it-service.de/Kontakt.html) - Internet: [www.MerZ-IT-SerVice.de](https://www.merz-it-service.de)
