<?PHP
/* 
	01-Gästebuch - Copyright 2009 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01gbook
	Dateiinfo: 	Modulspezifische Grundeinstellungen, Variablendefinitionen etc.
				Wird automatisch am Anfang jeden Modulaufrufs automatisch includiert.
	#fv.1000#
*/

if($userdata['settings'] == 1){
?>

<h1>G&auml;stebuch-Felder verwalten</h1>

<?PHP
// Ausführen: Neues Feld anlegen
if(isset($_POST['do']) && $_POST['do'] == "addfield" &&
   isset($_POST['type']) && ($_POST['type'] == "text" || $_POST['type'] == "textarea" || $_POST['type'] == "select") &&
   isset($_POST['feldname']) && !empty($_POST['feldname'])){
    
	// Standardwert
	if(($_POST['type'] == "text" || $_POST['type'] == "select") && isset($_POST['wert']) && !empty($_POST['wert']))
		$mysql_wert = mysql_real_escape_string($_POST['wert']);
	else
		$mysql_wert = "";
		
	// Parsing?
	if($_POST['type'] == "text" && isset($_POST['specialparse']) && !empty($_POST['specialparse']))
		$parse = mysql_real_escape_string($_POST['specialparse']);
	else
		$parse = "";
		
	// Feldlänge (bzw. Spalten/Rows)
	if($_POST['type'] == "text" && isset($_POST['size']) && is_numeric($_POST['size']))
		$size = mysql_real_escape_string($_POST['size']);
	elseif($_POST['type'] == "textarea" && isset($_POST['rows']) && is_numeric($_POST['rows']) && isset($_POST['cols']) && is_numeric($_POST['cols']))
		$size = mysql_real_escape_string($_POST['rows']."|".$_POST['cols']);
	elseif($_POST['type'] == "textarea")
		$size = "7|50";
	else
		$size = "";
		
	// Max. Eingabelänge
	if($_POST['type'] == "text" && isset($_POST['length']) && is_numeric($_POST['length']) && $_POST['length'] > 0 && $_POST['length'] <= 255)
		$maxlength = mysql_real_escape_string($_POST['length']);
	else
		$maxlength = "";
	
    // Eintragung in Datenbank vornehmen:
	$sql_insert = "INSERT INTO ".$mysql_tables['gb_fields']." (name,type,wert,parse,size,length,pflicht,public,hide) VALUES (
					'".mysql_real_escape_string($_POST['feldname'])."',
					'".mysql_real_escape_string($_POST['type'])."',
					'".$mysql_wert."',
					'".$parse."',
					'".$size."',
					'".$maxlength."',
					'".mysql_real_escape_string($_POST['pflicht'])."',
					'".mysql_real_escape_string($_POST['public'])."',
					'0'
					)";
	$result = mysql_query($sql_insert) OR die(mysql_error());
	$inserted_id = mysql_insert_id();

	
	if($inserted_id > 0){
		if($_POST['type'] == "textarea")
			mysql_query("ALTER TABLE `".$mysql_tables['gb_entry']."` ADD `field_".mysql_real_escape_string($inserted_id)."` TEXT NULL COMMENT '".mysql_real_escape_string($_POST['feldname'])."'");
		elseif($_POST['type'] == "text" && $maxlength > 0 && $maxlength <= 255)
			mysql_query("ALTER TABLE `".$mysql_tables['gb_entry']."` ADD `field_".mysql_real_escape_string($inserted_id)."` VARCHAR( ".$maxlength." ) NOT NULL COMMENT '".mysql_real_escape_string($_POST['feldname'])."'");
		else
			mysql_query("ALTER TABLE `".$mysql_tables['gb_entry']."` ADD `field_".mysql_real_escape_string($inserted_id)."` VARCHAR( 255 ) NOT NULL COMMENT '".mysql_real_escape_string($_POST['feldname'])."'");
		
		echo "<p class=\"meldung_erfolg\">Neues Feld wurde erfolgreich angelegt.</p>";
		}
	else
		echo "<p class=\"meldung_error\">Beim Anlegen des Feldes trat ein Fehler auf!</p>";
    }
elseif(isset($_POST['do']) && $_POST['do'] == "addfield")
	echo "<p class=\"meldung_error\">Fehler: Sie haben nicht alle ben&ouml;tigten Felder ausgef&uuml;llt!<br />
	<a href=\"javascript:history.back();\">Zur&uuml;ck</a></p>";
	
	
	
	
// Ausführen: Feld aktualisieren
if(isset($_POST['do']) && $_POST['do'] == "editfield" &&
   isset($_POST['feldname']) && !empty($_POST['feldname']) &&
   isset($_POST['id']) && !empty($_POST['id'])){
    
	// Standardwert
	if(($_POST['type'] == "text" || $_POST['type'] == "select") && isset($_POST['wert']) && !empty($_POST['wert']))
		$mysql_wert = mysql_real_escape_string($_POST['wert']);
	else
		$mysql_wert = "";
		
	// Parsing?
	if($_POST['type'] == "text" && isset($_POST['specialparse']) && !empty($_POST['specialparse']))
		$parse = mysql_real_escape_string($_POST['specialparse']);
	else
		$parse = "";
	
	// Feldlänge (bzw. Spalten/Rows)
	if($_POST['type'] == "text" && isset($_POST['size']) && is_numeric($_POST['size']))
		$size = mysql_real_escape_string($_POST['size']);
	elseif($_POST['type'] == "textarea" && isset($_POST['rows']) && is_numeric($_POST['rows']) && isset($_POST['cols']) && is_numeric($_POST['cols']))
		$size = mysql_real_escape_string($_POST['rows']."|".$_POST['cols']);
	elseif($_POST['type'] == "textarea")
		$size = "7|50";
	else
		$size = "";
	
	mysql_query("UPDATE ".$mysql_tables['gb_fields']." SET 
				name 		= '".mysql_real_escape_string($_POST['feldname'])."',
				wert 		= '".$mysql_wert."',
				parse		= '".$parse."',
				size 		= '".$size."',
				pflicht 	= '".mysql_real_escape_string($_POST['pflicht'])."',
				public	 	= '".mysql_real_escape_string($_POST['public'])."'
				WHERE id = '".mysql_real_escape_string($_POST['id'])."' AND hide = '0' LIMIT 1");
	
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
	
	$list = mysql_query("SELECT * FROM ".$mysql_tables['gb_fields']." WHERE hide = '0' AND id = '".mysql_real_escape_string($_GET['id'])."'");
	while($row = mysql_fetch_array($list)){
		if($row['type'] == "textarea"){
			$array = explode("|",$row['size']);
			$zeilen = $array[0];
			$spalten = $array[1];
			$size = "";
			}
		else
			$size = $row['size'];
			
		if($row['type'] == "text")
			$maxlength = $row['length'];
		else $maxlength = "";

		$_POST['fieldtype'] = stripslashes($row['type']);
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
							"type"			=> stripslashes($row['type']));
		include_once("fields_form.php");
		}
	}
else{

// Lösch-Abfrage
if(isset($_GET['do']) && $_GET['do'] == "delfield" && isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']))
	echo "<p class=\"meldung_frage\">Wollen Sie dieses Feld wirklich l&ouml;schen?<br />
			<b>Alle bisher in dieses Feld eingetragene Daten gehen unwiderruflich verloren!</b><br /><br />
			<a href=\"".$filename."&amp;do=dodelfield&amp;id=".$_GET['id']."\">Ja, Feld löschen</a> | <a href=\"".$filename."\">Nein, zur&uuml;ck</a></p>";

// Löschen durchführen
if(isset($_GET['do']) && $_GET['do'] == "dodelfield" && isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
	mysql_query("DELETE FROM ".$mysql_tables['gb_fields']." WHERE id = '".mysql_real_escape_string($_GET['id'])."' AND nodelete = '0' LIMIT 1");
	
	if(mysql_affected_rows() > 0){
		mysql_query("ALTER TABLE `".$mysql_tables['gb_entry']."` DROP `field_".mysql_real_escape_string($_GET['id'])."`");
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
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">
	<tr>
		<td class="tra" width="90" align="center"><b>Reihenfolge</b></td>
		<td class="tra"><b>Name</b></td>
		<td class="tra"><b>Feldtyp</b></td>
		<td class="tra" width="90" align="center"><b>Pflichtfeld</b></td>
		<td class="tra" width="90" align="center"><b>Ausgeben?</b></td>
		<td class="tra" width="25"><!-- Bearbeiten --></td>
		<td class="tra" width="25"><!-- Löschen --></td>
	</tr>
	
<?PHP
// Sortierung vornehmen
if(isset($_POST['sort']) && !empty($_POST['sort'])){
	$list = mysql_query("SELECT id,sortorder FROM ".$mysql_tables['gb_fields']." WHERE hide = '0'");
	while($row = mysql_fetch_array($list)){
		mysql_query("UPDATE ".$mysql_tables['gb_fields']." SET sortorder='".mysql_real_escape_string($_POST['field_'.$row['id']])."' WHERE id='".$row['id']."' LIMIT 1");
		}
	}

$count = 0;
$list = mysql_query("SELECT id,sortorder,name,type,pflicht,public,nodelete FROM ".$mysql_tables['gb_fields']." WHERE hide = '0' ORDER BY sortorder,name");
while($row = mysql_fetch_array($list)){
	if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
	
	// Feldtyp
	switch($row['type']){
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
	
	// Öffentlich ausgeben?
	if($row['public'] == 1) $pub = "Ja";
	else $pub = "Nein";
	
	if($row['nodelete'] == 0) $del = "<a href=\"".$filename."&amp;do=delfield&amp;id=".$row['id']."\"><img src=\"images/icons/icon_delete.gif\" alt=\"L&ouml;schen - rotes X\" title=\"Feld l&ouml;schen\" /></a>";
	else $del = "&nbsp;";
	
	echo "    <tr>
		<td class=\"".$class."\" align=\"center\"><select name=\"field_".$row['id']."\" size=\"1\" class=\"input_select\">"._01gbook_FieldSortDropDown($row['sortorder'])."</select></td>
		<td class=\"".$class."\">".stripslashes($row['name'])."</td>
		<td class=\"".$class."\">".$fieldtype."</td>
		<td class=\"".$class."\" align=\"center\">".$pfl."</td>
		<td class=\"".$class."\" align=\"center\">".$pub."</td>
		<td class=\"".$class."\" align=\"center\"><a href=\"".$filename."&amp;do=editfieldform&amp;id=".$row['id']."\"><img src=\"images/icons/icon_edit.gif\" alt=\"Stift\" title=\"Feld bearbeiten\" /></a></td>
		<td class=\"".$class."\" align=\"center\">".$del."</td>
	</tr>\n\n";
	}
if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
?>
	<tr>
		<td class="<?PHP echo $class; ?>" align="center"><input type="submit" name="sort" value="Sortieren" class="input" /></td>
		<td class="<?PHP echo $class; ?>" colspan="6">&nbsp;</td>
	</tr>	

</table>
</form>

<?PHP
	}
?>

<?PHP
}else $flag_loginerror = true;

// 01-Gästebuch Copyright 2009 by Michael Lorer - 01-Scripts.de
?>