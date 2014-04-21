<?PHP
// 1.0.0.0 --> 1.0.1
if(isset($_REQUEST['update']) && $_REQUEST['update'] == "1000_zu_101"){
	// Spaltenname 'timestamp' umbenennen in 'utimestamp' #693
	$mysqli->query("ALTER TABLE ".$mysql_tables['gb_entry']." CHANGE `timestamp` `utimestamp` INT( 10 ) NOT NULL DEFAULT '0'");
	// Spaltenname 'type' umbenennen in 'fieldtype' #693
	$mysqli->query("ALTER TABLE ".$mysql_tables['gb_fields']." CHANGE `type` `fieldtype` VARCHAR( 25 ) NOT NULL");

	// Versionsnummer aktualisieren
	$mysqli->query("UPDATE ".$mysql_tables['module']." SET version = '1.0.1' WHERE idname = '".$mysqli->escape_string($modul)."' LIMIT 1");
?>
<h2>Update Version 1.0.0.0 nach 1.0.1</h2>

<p class="meldung_erfolg">
	Das Update von Version 1.0.0.0 auf Version 1.0.1 wurde erfolgreich durchgef&uuml;hrt.<br />
	<br />
	<a href="module.php">Zur&uuml;ck zur Modul-&Uuml;bersicht &raquo;</a>
</p>
<?PHP
	}
?>