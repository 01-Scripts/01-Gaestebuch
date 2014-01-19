<?PHP
/* 
	01-Gästebuch - Copyright 2009-2014 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01gbook
	Dateiinfo: 	Bearbeiten von eingehenden Ajax-Requests zur Löschung und Freischaltung von Einträgen
	#fv.101#
*/

// Security: Only allow calls from _ajaxloader.php!
if(basename($_SERVER['SCRIPT_FILENAME']) != "_ajaxloader.php") exit;

// Ajax-Requests bearbeiten
// Kommentare löschen
if(isset($_REQUEST['ajaxaction']) && $_REQUEST['ajaxaction'] == "delentry" &&
   isset($_REQUEST['id']) && !empty($_REQUEST['id']) &&
   $userdata[$modul] == 1){
	$mysqli->query("DELETE FROM ".$mysql_tables['gb_entry']." WHERE id='".$mysqli->escape_string($_REQUEST['id'])."' LIMIT 1");
	
	echo "<script> Success_delfade('id".$_REQUEST['id']."'); </script>";
	}
// Kommentare freischalten
elseif(isset($_REQUEST['ajaxaction']) && $_REQUEST['ajaxaction'] == "freeentry" &&
   isset($_REQUEST['id']) && !empty($_REQUEST['id']) &&
   $userdata['editcomments'] == 1){
    $mysqli->query("UPDATE ".$mysql_tables['gb_entry']." SET frei='1' WHERE id='".$mysqli->escape_string($_REQUEST['id'])."' LIMIT 1");
	
	echo "<script> Success_CFree('free".$_REQUEST['id']."'); </script>";
	}
elseif(isset($_REQUEST['ajaxaction']) && !empty($_REQUEST['ajaxaction']))
	echo "<script> Failed_delfade(); </script>";
?>