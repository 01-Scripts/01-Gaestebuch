-- 01-Gästebuch - Copyright 2009-2014 by Michael Lorer - 01-Scripts.de
-- Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
-- Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

-- Modul:		01gbook
-- Dateiinfo:	SQL-Befehle für die Erstinstallation des 01-Gästebuchs
-- #fv.101#
--  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  *  *

-- --------------------------------------------------------

SET AUTOCOMMIT=0;
START TRANSACTION;

-- --------------------------------------------------------

-- 
-- Neue Einstellungs-Kategorie für Modul anlegen
-- Einstellungen importieren
-- 

INSERT INTO 01prefix_settings (modul,is_cat,catid,sortid,idname,name,exp,formename,formwerte,input_exp,standardwert,wert,nodelete,hide) VALUES 
('#modul_idname#', 1, 1, 1, 'gbooksettings', 'Einstellungen', NULL, '', '', NULL, NULL, NULL, 0, 0),
('#modul_idname#', 0, 1, 5, 'gbooksmilies', 'Smilies aktivieren?', '', 'Ja|Nein', '1|0', '', '1', '1', 0, 0),
('#modul_idname#', 0, 1, 3, 'gbooksendmail', 'E-Mail bei neuem Eintrag versenden?', 'Empf&auml;nger ist die in den globalen Einstellungen angegebene Kontakt-E-Mail-Adresse.', 'Ja|Nein', '1|0', '', '0', '0', 0, 0),
('#modul_idname#', 0, 1, 2, 'gbookantispam', 'Spamschutz aktivieren?', '', 'Ja|Nein', '1|0', '', '1', '1', 0, 0),
('#modul_idname#', 0, 1, 4, 'gbookfreeentries', 'Eintr&auml;ge freischalten?', '', 'Ja|Nein, sofort ver&ouml;ffentlichen', '0|1', '', '1', '1', 0, 0),
('#modul_idname#', 0, 1, 1, 'gbook_perpage', 'Eintr&auml;ge pro Seite:', '', 'text', '5', '', '25', '25', 0, 0),
('#modul_idname#', 0, 1, 6, 'gbookbbc', 'Einfachen BB-Code aktivieren?', '', 'Ja|Nein', '1|0', '', '1', '1', 0, 0),

('#modul_idname#', 1, 2, 2, 'csssettings','CSS-Einstellungen','','','','','','',0,0),
('#modul_idname#', 0, 2, 1, 'extern_css','Externe CSS-Datei','Geben Sie einen absoluten Pfad inkl. <b>http://</b> zu einer externen CSS-Datei an.\nLassen Sie dieses Feld leer um die nachfolgend definierten CSS-Eigenschaften zu verwenden.','text','50','','','',0,0),
('#modul_idname#', 0, 2, 2, 'csscode', 'CSS-Eigenschaften', 'Nachfolgende CSS-Definitionen werden nur ber&uuml;cksichtigt, wenn <b>keine</b> URL zu einer externen CSS-Datei eingegeben wurde!', 'textarea', '18|100', '', '', '/* Äußere Box für den gesamten Gästebuch-Bereich - DIV selber (id = _01gbook) */\r\n#_01gbook{\r\n	text-align:left;\r\n	}\r\n\r\n.box_out{\r\n	width: 600px;\r\n	margin: 0 auto;\r\n	color:#000;\r\n	text-align:left;\r\n	font-family: Verdana, Arial, Helvetica, sans-serif;\r\n	font-size:10pt;\r\n	}\r\n\r\n/* Link-Definitionen (box_out) */\r\n.box_out a:link,.box_out a:visited  {\r\n	text-decoration: underline;\r\n	color: #000;\r\n}\r\n.box_out a:hover  {\r\n	text-decoration: none;\r\n	color: #000;\r\n}\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n/* Definition für Eintrag-Hinzufügen-Tabelle */\r\n.addentrybox {\r\n	width:98%;\r\n	text-align:left;\r\n	border: 1px dotted #999;\r\n	padding:8px;\r\n	margin-bottom:5px;\r\n	}\r\n\r\n/* Definition für Seiten-Navigations-Tabelle */\r\n.table_page {\r\n	width:98%;\r\n	padding-top:15px;\r\n	border:0;\r\n	}\r\n	\r\n/* Textdefinition für Eintrag-Hinzufügen-Tabelle */\r\n.page_text {\r\n	font-size:12px;\r\n	text-decoration:none;\r\n	}\r\n	\r\n/* Textdefinition für Feldnamen in Eintrag-Hinzufügen-Tabelle */\r\ntd.fieldname, span.fieldname {\r\n	font-weight:bold;\r\n}\r\n\r\n/* CSS-Definition für Tabellenzeilen der Eintrag-Hinzufügen-Tabelle */\r\ntr.fieldrow {\r\n\r\n}\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n/* CSS-Klasse für Statusmeldungen nach dem Absenden eines neuen Eintrags */\r\ndiv.gbuchmessages {\r\n	font-weight:bold;\r\n}\r\n\r\n/* CSS-Klasse für Erfolgsmeldungen innerhalb von .gbuchmessages{} */\r\n.g_erfolg {\r\n	color:green;\r\n}\r\n\r\n/* CSS-Klasse für Fehlermeldungen innerhalb von .gbuchmessages{} */\r\n.g_error {\r\n	color:red;\r\n}\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n/* CSS-Klasse für DIV-Box um die einzelnen GB-Einträge (templates/entrybit.html) */\r\ndiv.gbentries {\r\n	width:95%;\r\n	border: 1px dotted #999;\r\n	padding:8px;\r\n	margin: 0 auto;\r\n	margin-bottom:5px;\r\n	\r\n	text-align:left;\r\n	font-size:11px;\r\n}\r\n\r\n/* Die einzelnen im ACP angelegten Eingabefelder werden bei der Ausgabe jeweils in einem\r\n   einzelnen p-Block mit der CSS-Klasse gbfrontend_zeile ausgegeben. Die Eigenschaft dieser Blöcke kann\r\n   hier verändert werden */\r\n.gbfrontend_zeile {\r\n	margin:0;\r\n	margin-bottom:4px;\r\n	padding:0;\r\n}\r\n\r\n/* Zusätzlich zu .gbfrontend_zeile CSS-Definition für die 1. Zeile bei der Ausgabe von Einträgen (Name + Datum) */\r\n.gbtitlezeile {\r\n	margin-bottom:6px;\r\n}\r\n\r\n/* Die Feldbezeichnungen werden bei der Ausgabe in einem SPAN-Tag der Klasse gbfrontend_fieldname ausgegeben */\r\n.gbfrontend_fieldname {\r\n	font-weight:bold;\r\n}\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n/* Formular-Elemente */\r\n/* Normales Textfeld */\r\n.input_field {\r\n\r\n	}\r\n	\r\n/* CSS-Klasse für mehrzeilige Eingabefelder (textareas) */\r\n.textareafeld {\r\n	font-size: 10pt;\r\n	font-family: Verdana, Arial, Helvetica, sans-serif;\r\n}\r\n	\r\n/* Formular-Buttons */\r\n.input_button {\r\n\r\n	}\r\n	\r\n/* Dropdown-Boxen */\r\n.input_selectfield {\r\n	\r\n	}\r\n	\r\n	\r\n	\r\n	\r\n	\r\n	\r\n	\r\n	\r\n/* Copyright-Hinweis */\r\n/* Sichtbare Hinweis darf ohne eine entsprechende Lizenz NICHT entfernt werden! */\r\n.copyright {\r\n	padding-top:15px;\r\n	font-size:11px;\r\n	text-decoration:none;\r\n	}', 0, 0);



-- --------------------------------------------------------

-- 
-- Menüeinträge anlegen
-- 

INSERT INTO 01prefix_menue (name,link,modul,sicherheitslevel,rightname,rightvalue,sortorder,subof,hide) VALUES 
('Eintr&auml;ge verwalten', '_loader.php?modul=#modul_idname#&amp;action=&amp;loadpage=entry', '#modul_idname#', '1', '#modul_idname#', '1', '1', '0', '0'),
('Felder verwalten', '_loader.php?modul=#modul_idname#&amp;action=&amp;loadpage=fields', '#modul_idname#', '1', 'settings', '1', '2', '0', '0');



-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `01modulprefix_gbfields`
-- 

CREATE TABLE IF NOT EXISTS `01modulprefix_gbfields` (
`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`sortorder` INT( 5 ) NOT NULL DEFAULT '1',
`name` VARCHAR( 100 ) NOT NULL ,
`fieldtype` VARCHAR( 25 ) NOT NULL ,
`wert` TEXT NULL ,
`parse` VARCHAR( 10 ) NULL ,
`size` VARCHAR( 25 ) NULL ,
`length` VARCHAR( 10 ) NULL ,
`pflicht` TINYINT( 1 ) NOT NULL DEFAULT '0',
`public` TINYINT( 1 ) NOT NULL DEFAULT '1' COMMENT 'Feld oeffentlich ausgeben?',
`hide` TINYINT( 1 ) NOT NULL DEFAULT '0',
`nodelete` TINYINT( 1 ) NOT NULL DEFAULT '0'
) ENGINE=MYISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `01modulprefix_gbfields`
-- Vorgegebene Felder (inkl. Eintragsfeld)
-- 

INSERT INTO `01modulprefix_gbfields` (`id`, `sortorder`, `name`, `fieldtype`, `wert`, `parse`, `size`, `length`, `pflicht`, `public`, `hide`, `nodelete`) VALUES
(1, 1, 'Name:', 'text', '', '', '30', '50', 1, 1, 0, 1),
(2, 2, 'E-Mail:', 'text', '', 'email', '30', '50', 0, 0, 0, 0),
(3, 3, 'Homepage:', 'text', '', 'url', '30', '50', 0, 1, 0, 0),
(4, 4, 'Ihr Eintrag:', 'textarea', '', '', '7|52', '', 1, 1, 0, 1);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `01modulprefix_gbentries`
-- 

CREATE TABLE IF NOT EXISTS `01modulprefix_gbentries` (
  `id` int(10) NOT NULL auto_increment,
  `utimestamp` int(10) NOT NULL DEFAULT '0',
  `uid` varchar(32) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `frei` TINYINT(1) NOT NULL DEFAULT '0',
  `bbc_smile_deaktiv` TINYINT(1) NOT NULL DEFAULT '0',
  `field_1` varchar(50) NOT NULL COMMENT 'Name',
  `field_2` varchar(50) NOT NULL COMMENT 'E-Mail:',
  `field_3` varchar(50) NOT NULL COMMENT 'Internetseite:',
  `field_4` text COMMENT 'Ihr Eintrag:',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `01modulprefix_gbentries`
-- 1. Dummypost
-- 

INSERT INTO `01modulprefix_gbentries` (`utimestamp`, `uid`, `ip`, `frei`, `bbc_smile_deaktiv`, `field_1`, `field_2`, `field_3`, `field_4`) VALUES
(1398074400, '06883695fbe9c3a5281bf08a06e26b41', '0.0.0.0', 1, 0, '01-Scripts.de', '', 'http://www.01-scripts.de', '[B]Vielen Dank, dass Sie sich f&uuml;r das 01-G&auml;stebuch entschieden haben![/B]\r\nDiesen ersten Eintrag k&ouml;nnen Sie l&ouml;schen, nachdem Sie sich in den Administrationsbereich eingeloggt haben.\r\n\r\nBei Fragen oder Problemen rund um das [B]01-G&auml;stebuch[/B] oder das [B]01acp[/B] stehe ich Ihnen gerne im [URL=http://board.01-scripts.de]01-Supportforum[/URL] oder per E-Mail zu Verf&uuml;gung.\r\n\r\nBitte beachten Sie die [URL=http://www.01-scripts.de/lizenz.php]g&uuml;ltigen Lizenzbestimmungen[/URL]! Das 01-G&auml;stebuch und das 01acp werden unter der Creative-Commons-Lizenz [I]\\&quot;[URL=http://creativecommons.org/licenses/by-nc-sa/3.0/de/]Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland[/URL]\\&quot;[/I] ver&ouml;ffentlicht.\r\n\r\nInformationen zum Erwerb einer Lizenz zur kommerziellen Nutzung (Gestattet den Einsatz auf kommerziellen Seiten und/oder Firmenseiten) oder eine Non-Copyright-Lizenz (die zum Entfernen des sichtbaren Urheberrechts-Hinweises berechtigt) entnehmen Sie bitte [URL=http://www.01-scripts.de/preise.php]dieser Seite[/URL].\r\n\r\nMfG,\r\nMichael Lorer\r\nWeb: http://www.01-scripts.de\r\nMail: info@01-scripts.de');

-- --------------------------------------------------------

COMMIT;