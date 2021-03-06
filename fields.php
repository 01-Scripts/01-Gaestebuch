<?PHP
/* 
	01-G�stebuch - Copyright 2009-2014 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01gbook
	Dateiinfo: 	Eigene Eingabefelder f�r das G�stebuch definieren oder bearbeiten
	#fv.101#
*/

if($userdata['settings'] == 1){
?>

<h1>G&auml;stebuch-Felder verwalten</h1>

<?PHP
// Ausf�hren: Neues Feld anlegen
if(isset($_POST['do']) && $_POST['do'] == "addfield" &&
   isset($_POST['type']) && ($_POST['type'] == "text" || $_POST['type'] == "textarea" || $_POST['type'] == "select") &&
   isset($_POST['feldname']) && !empty($_POST['feldname'])){
    
	// Standardwert
	if(($_POST['type'] == "text" || $_POST['type'] == "select") && isset($_POST['wert']) && !empty($_POST['wert']))
		$mysql_wert = $mysqli->escape_string($_POST['wert']);
	else
		$mysql_wert = "";
		
	// Parsing?
	if($_POST['type'] == "text" && isset($_POST['specialparse']) && !empty($_POST['specialparse']))
		$parse = $mysqli->escape_string($_POST['specialparse']);
	else
		$parse = "";
		
	// Feldl�nge (bzw. Spalten/Rows)
	if($_POST['type'] == "text" && isset($_POST['size']) && is_numeric($_POST['size']))
		$size = $mysqli->escape_string($_POST['size']);
	elseif($_POST['type'] == "textarea" && isset($_POST['rows']) && is_numeric($_POST['rows']) && isset($_POST['cols']) && is_numeric($_POST['cols']))
		$size = $mysqli->escape_string($_POST['rows']."|".$_POST['cols']);
	elseif($_POST['type'] == "textarea")
		$size = "7|50";
	else
		$size = "";
		
	// Max. Eingabel�nge
	if($_POST['type'] == "text" && isset($_POST['length']) && is_numeric($_POST['length']) && $_POST['length'] > 0 && $_POST['length'] <= 255)
		$maxlength = $mysqli->escape_string($_POST['length']);
	else
		$maxlength = "";

	// Pflichtfeld?
	if(!isset($_POST['pflicht']) || isset($_POST['pflicht']) && empty($_POST['pflicht']))
		$_POST['pflicht'] = 0;

	// Inhalt �ffentlich anzeigen?
	if(!isset($_POST['public']) || isset($_POST['public']) && empty($_POST['public']))
		$_POST['public'] = 0;
	
    // Eintragung in Datenbank vornehmen:
	$sql_insert = "INSERT INTO ".$mysql_tables['gb_fields']." (name,fieldtype,wert,parse,size,length,pflicht,public,hide) VALUES (
					'".$mysqli->escape_string($_POST['feldname'])."',
					'".$mysqli->escape_string($_POST['type'])."',
					'".$mysql_wert."',
					'".$parse."',
					'".$size."',
					'".$maxlength."',
					'".$mysqli->escape_string(intval($_POST['pflicht']))."',
					'".$mysqli->escape_string(intval($_POST['public']))."',
					'0'
					)";
	$mysqli->query($sql_insert) OR die($mysqli->error);
	$inserted_id = $mysqli->insert_id;

	
	if($inserted_id > 0){
		if($_POST['type'] == "textarea")
			$mysqli->query("ALTER TABLE `".$mysql_tables['gb_entry']."` ADD `field_".$mysqli->escape_string($inserted_id)."` TEXT NULL COMMENT '".$mysqli->escape_string($_POST['feldname'])."'");
		elseif($_POST['type'] == "text" && $maxlength > 0 && $maxlength <= 255)
			$mysqli->query("ALTER TABLE `".$mysql_tables['gb_entry']."` ADD `field_".$mysqli->escape_string($inserted_id)."` VARCHAR( ".$maxlength." ) NOT NULL COMMENT '".$mysqli->escape_string($_POST['feldname'])."'");
		else
			$mysqli->query("ALTER TABLE `".$mysql_tables['gb_entry']."` ADD `field_".$mysqli->escape_string($inserted_id)."` VARCHAR( 255 ) NOT NULL COMMENT '".$mysqli->escape_string($_POST['feldname'])."'");
		
		echo "<p class=\"meldung_erfolg\">Neues Feld wurde erfolgreich angelegt.</p>";
		}
	else
		echo "<p class=\"meldung_error\">Beim Anlegen des Feldes trat ein Fehler auf!</p>";
    }
elseif(isset($_POST['do']) && $_POST['do'] == "addfield")
	echo "<p class=\"meldung_error\">Fehler: Sie haben nicht alle ben&ouml;tigten Felder ausgef&uuml;llt!<br />
	<a href=\"javascript:history.back();\">Zur&uuml;ck</a></p>";
	
	
	
	
// Ausf�hren: Feld aktualisieren
if(isset($_POST['do']) && $_POST['do'] == "editfield" &&
   isset($_POST['feldname']) && !empty($_POST['feldname']) &&
   isset($_POST['id']) && !empty($_POST['id'])){
    
	// Standardwert
	if(($_POST['type'] == "text" || $_POST['type'] == "select") && isset($_POST['wert']) && !empty($_POST['wert']))
		$mysql_wert = $mysqli->escape_string($_POST['wert']);
	else
		$mysql_wert = "";
		
	// Parsing?
	if($_POST['type'] == "text" && isset($_POST['specialparse']) && !empty($_POST['specialparse']))
		$parse = $mysqli->escape_string($_POST['specialparse']);
	else
		$parse = "";
	
	// Feldl�nge (bzw. Spalten/Rows)
	if($_POST['type'] == "text" && isset($_POST['size']) && is_numeric($_POST['size']))
		$size = $mysqli->escape_string($_POST['size']);
	elseif($_POST['type'] == "textarea" && isset($_POST['rows']) && is_numeric($_POST['rows']) && isset($_POST['cols']) && is_numeric($_POST['cols']))
		$size = $mysqli->escape_string($_POST['rows']."|".$_POST['cols']);
	elseif($_POST['type'] == "textarea")
		$size = "7|50";
	else
		$size = "";

	// Pflichtfeld?
	if(!isset($_POST['pflicht']) || isset($_POST['pflicht']) && empty($_POST['pflicht']))
		$_POST['pflicht'] = 0;

	// Inhalt �ffentlich anzeigen?
	if(!isset($_POST['public']) || isset($_POST['public']) && empty($_POST['public']))
		$_POST['public'] = 0;
	
	$mysqli->query("UPDATE ".$mysql_tables['gb_fields']." SET 
				name 		= '".$mysqli->escape_string($_POST['feldname'])."',
				wert 		= '".$mysql_wert."',
				parse		= '".$parse."',
				size 		= '".$size."',
				pflicht 	= '".$mysqli->escape_string(intval($_POST['pflicht']))."',
				public	 	= '".$mysqli->escape_string(intval($_POST['public']))."'
				WHERE id = '".$mysqli->escape_string(intval($_POST['id']))."' AND hide = '0' LIMIT 1");
	
	echo "<p class=\"meldung_erfolg\">Feld wurder aktualisiert</p>";
	}









// Formular: Neues Feld anlegen (Formular)
if(isset($_POST['addfield']) && !empty($_POST['addfield']) &&
   isset($_POST['fieldtype']) && !empty($_POST['fieldtype'])){
	$form_data = array( "do"			=> "addfield",
						"title"			=> "Neues Feld hinzuf&uuml;gen",
						"sendbutton"	=> "Feld anlegen",
						"id"			=> "",
						"feldname"		=> "",
						"zeilen"		=> "7",
						"spalten"		=> "50",
						"size"			=> "50",
						"length"		=> "100",
						"wert"			=> "",
						"parse"			=> "",
						"pflicht"		=> 0,
						"public"		=> 1,
						"type"			=> $_POST['fieldtype']);

	include_once("fields_form.php");
    }
// Feld bearbeiten: Editier-Formular
elseif(isset($_GET['do']) && $_GET['do'] == "editfieldform" &&
       isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
	
	$list = $mysqli->query("SELECT * FROM ".$mysql_tables['gb_fields']." WHERE hide = '0' AND id = '".$mysqli->escape_string($_GET['id'])."'");
	while($row = $list->fetch_assoc()){
		if($row['fieldtype'] == "textarea"){
			$array = explode("|",$row['size']);
			$zeilen = $array[0];
			$spalten = $array[1];
			$size = "";
			}
		else{
			$size = $row['size'];
			$zeilen = "";
			$spalten = "";
		}
			
		if($row['fieldtype'] == "text")
			$maxlength = $row['length'];
		else $maxlength = "";

		$_POST['fieldtype'] = stripslashes($row['fieldtype']);
		$form_data = array( "do"			=> "editfield",
							"title"			=> "Feld bearbeiten",
							"sendbutton"	=> "Feld bearbeiten",
							"id"			=> $_GET['id'],
							"feldname"		=> stripslashes($row['name']),
							"zeilen"		=> $zeilen,
							"spalten"		=> $spalten,
							"size"			=> $size,
							"length"		=> $maxlength,
							"wert"			=> stripslashes($row['wert']),
							"parse"			=> stripslashes($row['parse']),
							"pflicht"		=> $row['pflicht'],
							"public"		=> $row['public'],
							"fieldtype"		=> stripslashes($row['fieldtype']));
		include_once("fields_form.php");
		}
	}
else{

// L�sch-Abfrage
if(isset($_GET['do']) && $_GET['do'] == "delfield" && isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']))
	echo "<p class=\"meldung_frage\">Wollen Sie dieses Feld wirklich l&ouml;schen?<br />
			<b>Alle bisher in dieses Feld eingetragene Daten gehen unwiderruflich verloren!</b><br /><br />
			<a href=\"".$filename."&amp;do=dodelfield&amp;id=".$_GET['id']."\">Ja, Feld l�schen</a> | <a href=\"".$filename."\">Nein, zur&uuml;ck</a></p>";

// L�schen durchf�hren
if(isset($_GET['do']) && $_GET['do'] == "dodelfield" && isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
	$mysqli->query("DELETE FROM ".$mysql_tables['gb_fields']." WHERE id = '".$mysqli->escape_string($_GET['id'])."' AND nodelete = '0' LIMIT 1");
	
	if($mysqli->affected_rows > 0){
		$mysqli->query("ALTER TABLE `".$mysql_tables['gb_entry']."` DROP `field_".$mysqli->escape_string($_GET['id'])."`");
		echo "<p class=\"meldung_erfolg\">Feld wurde erfolgreich gel&ouml;scht.</p>";
		}
	else
		echo "<p class=\"meldung_error\"><b>Fehler:</b> Feld konnte nicht gel&ouml;scht werden!</p>";
	}









// Auflistung
?>

<form action="<?PHP echo $filename; ?>" method="post">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">
	<tr>
		<td class="tra">
			<img src="images/icons/add.gif" alt="Plus-Zeichen" title="Neues Feld hinzuf&uuml;gen" style="margin-right:15px;" />
			<select name="fieldtype" size="1" class="input_select">
				<option value="text">Normales, einzeiliges Textfeld</option>
				<option value="textarea">Mehrzeiliges Textfeld (Textarea)</option>
				<option value="select">DropDown-Box</option>
			</select>
			<input type="submit" name="addfield" value="+ Neues Feld hinzuf&uuml;gen" class="input" style="margin-left:30px;" />
		</td>
	</tr>
</table>
</form>

<form action="<?PHP echo $filename; ?>" method="post">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen trab">
	<tr>
		<td class="tra" width="90" align="center"><b>Reihenfolge</b></td>
		<td class="tra"><b>Name</b></td>
		<td class="tra"><b>Feldtyp</b></td>
		<td class="tra" width="90" align="center"><b>Pflichtfeld</b></td>
		<td class="tra" width="90" align="center"><b>Ausgeben?</b></td>
		<td class="tra" width="25"><!-- Bearbeiten --></td>
		<td class="tra" width="25"><!-- L�schen --></td>
	</tr>
	
<?PHP
// Sortierung vornehmen
if(isset($_POST['sort']) && !empty($_POST['sort'])){
	$list = $mysqli->query("SELECT id,sortorder FROM ".$mysql_tables['gb_fields']." WHERE hide = '0'");
	while($row = $list->fetch_assoc()){
		$mysqli->query("UPDATE ".$mysql_tables['gb_fields']." SET sortorder='".$mysqli->escape_string($_POST['field_'.$row['id']])."' WHERE id='".$row['id']."' LIMIT 1");
		}
	}

$list = $mysqli->query("SELECT id,sortorder,name,fieldtype,pflicht,public,nodelete FROM ".$mysql_tables['gb_fields']." WHERE hide = '0' ORDER BY sortorder,name");
while($row = $list->fetch_assoc()){
	
	// Feldtyp
	switch($row['fieldtype']){
	  case "text":
	    $fieldtype = "Textfeld";
	  break;
	  case "textarea":
	    $fieldtype = "Textarea";
	  break;
	  case "select":
	    $fieldtype = "DropDown-Box";
	  break;
	  }
	  
	// Pflichtfeld
	if($row['pflicht'] == 1) $pfl = "Ja";
	else $pfl = "Nein";
	
	// �ffentlich ausgeben?
	if($row['public'] == 1) $pub = "Ja";
	else $pub = "Nein";
	
	if($row['nodelete'] == 0) $del = "<a href=\"".$filename."&amp;do=delfield&amp;id=".$row['id']."\"><img src=\"images/icons/icon_delete.gif\" alt=\"L&ouml;schen - rotes X\" title=\"Feld l&ouml;schen\" /></a>";
	else $del = "&nbsp;";
	
	echo "    <tr>
		<td align=\"center\"><select name=\"field_".$row['id']."\" size=\"1\" class=\"input_select\">"._01gbook_FieldSortDropDown($row['sortorder'])."</select></td>
		<td>".stripslashes($row['name'])."</td>
		<td>".$fieldtype."</td>
		<td align=\"center\">".$pfl."</td>
		<td align=\"center\">".$pub."</td>
		<td align=\"center\"><a href=\"".$filename."&amp;do=editfieldform&amp;id=".$row['id']."\"><img src=\"images/icons/icon_edit.gif\" alt=\"Stift\" title=\"Feld bearbeiten\" /></a></td>
		<td align=\"center\">".$del."</td>
	</tr>\n\n";
	}
?>
	<tr>
		<td align="center"><input type="submit" name="sort" value="Sortieren" class="input" /></td>
		<td colspan="6">&nbsp;</td>
	</tr>	

</table>
</form>

<?PHP
	}
?>

<?PHP
}else $flag_loginerror = true;

?>