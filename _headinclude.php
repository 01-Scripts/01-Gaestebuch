<?PHP
/* 
	01-Gästebuch - Copyright 2009-2013 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01gbook
	Dateiinfo: 	Modulspezifische Grundeinstellungen, Variablendefinitionen etc.
				Wird automatisch am Anfang jeden Modulaufrufs automatisch includiert.
	#fv.101#
*/

// Modul-Spezifische MySQL-Tabellen
$mysql_tables['gb_fields'] 	= "01_".$instnr."_".$module[$modul]['nr']."_gbfields";
$mysql_tables['gb_entry']	= "01_".$instnr."_".$module[$modul]['nr']."_gbentries";

$addJSFile 	= "";					// Zusätzliche modulspezifische JS-Datei (im Modulverzeichnis!)
$addCSSFile = "modul.css";			// Zusätzliche modulspezifische CSS-Datei (im Modulverzeichnis!)
$mootools_use = array("moo_core","moo_more","moo_slideh","moo_request");

// Welche PHP-Seiten sollen abhängig von $_REQUEST['loadpage'] includiert werden?
$loadfile['index'] 		= "index.php";			// Standardseite, falls loadpage invalid ist
$loadfile['fields'] 	= "fields.php";
$loadfile['entry']		= "entry.php";

// Weitere Pfadangaben
$iconpf 	= "images/icons/";		// Verzeichnis mit Icon-Dateien
$tempdir	= "templates/";			// Template-Verzeichnis

// Weitere Variablen
$namefield_id		= 1;			// Feld-ID, des Name-Eingabefeldes (wird in spezieller Weise ausgegeben)
$eintragsfield_id 	= 4;			// Feld-ID, des Eingabefeldes, welches für den Gästebucheintragstext zuständig ist (nodelete)
if(isset($settings['email_absender'])) $emailempf = $settings['email_absender'];	//E-Mail-Empfänger bei neuen Einträgen ins Gästebuch
$snip_bbc = "snippet_bbcode.html";
$snip_smilies = "snippet_smilies.html";

// Variablennamen-Deklaration
$names['gpage']	= "gpage";

?>