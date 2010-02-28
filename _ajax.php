<?PHP
/* 
	01-G�stebuch - Copyright 2009 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01gbook
	Dateiinfo: 	PopUp-Datei zum Ansehen und Bearbeiten von Eintr�gen
				Au�erdem: Bearbeiten von eingehenden Ajax-Requests
				(Workaround, da im 01ACP 1.0.0.2 bisher keine M�glichkeit besteht Modul-Dateien
				in einem PopUp-Fenster anzeigen zu lassen. Wird mit V 1.0.0.3 eingef�hrt und dann
				auch der Dateiinhalt dieser Datei aufgesplittet)
	#fv.1000#
*/


// Ajax-Requests bearbeiten
// Kommentare l�schen
if(isset($_REQUEST['ajaxaction']) && $_REQUEST['ajaxaction'] == "delentry" &&
   isset($_REQUEST['id']) && !empty($_REQUEST['id']) &&
   $userdata[$modul] == 1){
	mysql_query("DELETE FROM ".$mysql_tables['gb_entry']." WHERE id='".mysql_real_escape_string($_REQUEST['id'])."' LIMIT 1");
	
	echo "<script> Success_delfade('id".$_REQUEST['id']."'); </script>";
	}
// Kommentare freischalten
elseif(isset($_REQUEST['ajaxaction']) && $_REQUEST['ajaxaction'] == "freeentry" &&
   isset($_REQUEST['id']) && !empty($_REQUEST['id']) &&
   $userdata['editcomments'] == 1){
    mysql_query("UPDATE ".$mysql_tables['gb_entry']." SET frei='1' WHERE id='".mysql_real_escape_string($_REQUEST['id'])."' LIMIT 1");
	
	echo "<script> Success_CFree('free".$_REQUEST['id']."'); </script>";
	}
elseif(isset($_REQUEST['ajaxaction']) && !empty($_REQUEST['ajaxaction']))
	echo "<script> Failed_delfade(); </script>";
else{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title>01acp - Popup</title>

<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
<meta http-equiv="Content-Language" content="de" />

<?PHP
// modulspezifische CSS-Datei
if(isset($addCSSFile) && file_exists($modulpath.$addCSSFile) && !is_dir($modulpath.$addCSSFile) && !empty($addCSSFile))
	echo "<link href=\"".$modulpath.$addCSSFile."\" rel=\"stylesheet\" type=\"text/css\" />";

$filename = "_ajaxloader.php?modul=".$modul."";
$flag_ispopup = TRUE;
?>
<link rel="stylesheet" type="text/css" href="system/default.css" />
<script src="system/js/javas.js" type="text/javascript"></script>
</head>

<body style="padding:0 10px;">

<div class="contentbox">
	<br />
	<div style="position:absolute; right:20px; top:25px;">
	<a href="javascript: window.close();"><img src="images/icons/icon_exit.gif" alt="Standby-Icon" title="Fenster schlie�en" /></a>
	</div>

<?PHP
// Kommentar anzeigen
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "show_entry" && $userdata[$modul] == 1 &&
	isset($_REQUEST['var1']) && !empty($_REQUEST['var1']) && is_numeric($_REQUEST['var1'])){
	
	if(isset($_REQUEST['var2']) && $_REQUEST['var2'] == "erfolg")
		echo "<p class=\"meldung_erfolg\">Eintrag wurde erfolgreich bearbeitet!
				<a href=\"javascript:window.close();\">Schlie&szlig;en</a></p>";

	// Einmalig Felder in Array einlesen
	$fields = _01gbook_getFields("");

	// Eintrag aus DB holen
	$query = "SELECT * FROM ".$mysql_tables['gb_entry']." WHERE id = '".mysql_real_escape_string($_REQUEST['var1'])."' LIMIT 1";
	$list = mysql_query($query);
	while($row = mysql_fetch_array($list)){
		$feldinhalte = "";
		echo "<p>Geschrieben von <b>".stripslashes($row['field_'.$namefield_id])."</b> (".$row['ip'].") 
				am <b>".date("d.m.Y",$row['timestamp'])."</b>, <b>".date("H:i",$row['timestamp'])."</b> Uhr</p>";
		
		echo "<p style=\"float:right;\">
		<a href=\"".$filename."&amp;action=edit_entry&amp;var1=".$_REQUEST['var1']."\"><img src=\"images/icons/icon_edit.gif\" alt=\"Stift+Papier\" title=\"Eintrag bearbeiten\" /></a>
		<img src=\"images/layout/spacer.gif\" alt=\"spacer\" width=\"15\" />
		<a href=\"".$filename."&amp;action=del_entry&amp;var1=".$_REQUEST['var1']."\"><img src=\"images/icons/icon_delete.gif\" alt=\"rotes Kreuz\" title=\"Eintrag l&ouml;schen\" /></a>
		</p>";
		
		// Angelegte Felder durchgehen
		foreach($fields as $field){
			if($field['id'] != $namefield_id && !empty($row['field_'.$field['id']])){
				$feldinhalte .= "\n\n<p class=\"entryfieldline\">\n";
				
				// Feldnamen ausgeben (au�er beim Nachrichtenfeld)
				if($field['id'] != $eintragsfield_id)
					$feldinhalte .= "<b>".$field['name']."</b> ";
				
				switch($field['type']){
				  case "text":
				  case "select":
				    if($field['parse'] == "email")
						$feldinhalte .= "<a href=\"mailto:".stripslashes($row['field_'.$field['id']])."\">".stripslashes($row['field_'.$field['id']])."</a>";
					elseif($field['parse'] == "url")
						$feldinhalte .= "<a href=\"".stripslashes($row['field_'.$field['id']])."\" target=\"_blank\">".stripslashes($row['field_'.$field['id']])."</a>";
					else
						$feldinhalte .= stripslashes($row['field_'.$field['id']]);
				  break;
				  case "textarea":
					if($field['id'] == $eintragsfield_id){
						// BB-Code & Smilies
		                $inhalt = stripslashes($row['field_'.$field['id']]);
		                if($row['bbc_smile_deaktiv'] == 0 && $settings['gbooksmilies'] == 1 && $settings['gbookbbc'] == 1)
							$inhalt = bb_code_comment($inhalt,1,1,1);
						elseif($row['bbc_smile_deaktiv'] == 0 && $settings['gbookbbc'] == 1)
							$inhalt = bb_code_comment($inhalt,1,1,0);
						elseif($row['bbc_smile_deaktiv'] == 0 && $settings['gbooksmilies'] == 1)
							$inhalt = bb_code_comment($inhalt,1,0,1);
						else 
							$inhalt = nl2br($inhalt);
						
						$feldinhalte .= $inhalt;
						}
					else{
						$feldinhalte .= "<br />";
						$feldinhalte .= nl2br(stripslashes($row['field_'.$field['id']]));
						}
				  break;
				  }
				$feldinhalte .= "</p>";
				}
			}
		echo $feldinhalte;
		}
	}
	
	
	
	
	
	
	
	
// Kommentar bearbeiten (Formular)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "edit_entry" && $userdata[$modul] == 1 &&
	isset($_REQUEST['var1']) && !empty($_REQUEST['var1']) && is_numeric($_REQUEST['var1'])){
	
	echo "<h2>Eintrag bearbeiten</h2>";
	
	// Eintrag aus DB holen
	$query = "SELECT * FROM ".$mysql_tables['gb_entry']." WHERE id = '".mysql_real_escape_string($_REQUEST['var1'])."' LIMIT 1";
	$list = mysql_query($query);
	while($row = mysql_fetch_array($list)){
		if($row['bbc_smile_deaktiv'] == 1) $c1 = " checked=\"checked\"";
		else $c1 = "";
		
		echo "<form action=\"".$filename."\" method=\"post\" name=\"post\">
	<table border=\"0\" align=\"center\" width=\"100%\" cellpadding=\"3\" cellspacing=\"5\" class=\"rundrahmen\">";

		$felder = "";
		$count = 0;
		$listfields = mysql_query("SELECT * FROM ".$mysql_tables['gb_fields']." ORDER BY sortorder,name");
		while($rowf = mysql_fetch_array($listfields)){
			if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
			
			// Sonderfeld (Eingabefeld) ID == $eintragsfield_id ?
			if($rowf['id'] == $eintragsfield_id){
				if($settings['gbookbbc'] == 1){
					$felder .= "
    <tr>
        <td colspan=\"2\">
            <input type=\"button\" value=\"B\" style=\"font-weight: bold;\" onclick=\"bbcinsert('[B]', '[/B]')\" class=\"input\" />
            <input type=\"button\" value=\"I\" style=\"font-style: italic;\" onclick=\"bbcinsert('[I]', '[/I]')\" class=\"input\" />
            <input type=\"button\" value=\"U\" style=\"font-style: underline;\" onclick=\"bbcinsert('[U]', '[/U]')\" class=\"input\" />
            <input type=\"button\" value=\"Center\" onclick=\"bbcinsert('[CENTER]', '[/CENTER]')\" class=\"input\" />
            &nbsp;&nbsp;&nbsp;&nbsp;
            <input type=\"button\" value=\"Zitat\" onclick=\"bbcinsert('[QUOTE]', '[/QUOTE]')\" class=\"input\" />
            <input type=\"button\" value=\"URL\" onclick=\"bbcinsert('[URL=http://]', '[/URL]')\" class=\"input\" />
            <input type=\"button\" value=\"@\" onclick=\"bbcinsert('[EMAIL=@]', '[/EMAIL]')\" class=\"input\" />
        </td>
    </tr>";
					}
				if($settings['gbooksmilies'] == 1){
					$felder .= "
    <tr>
        <td colspan=\"2\">
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :eek: ', '');\"><img src=\"".$smiliedir."1.gif\" alt=\"Smilie :eek:\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :D ', '');\"><img src=\"".$smiliedir."2.gif\" alt=\"Smilie :D\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :p ', '');\"><img src=\"".$smiliedir."3.gif\" alt=\"Smilie *Zunge rausstreck*\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :?: ', '');\"><img src=\"".$smiliedir."4.gif\" alt=\"Smilie ???\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' 8) ', '');\"><img src=\"".$smiliedir."5.gif\" alt=\"Smilie mit cooler Sonnenbrille\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :( ', '');\"><img src=\"".$smiliedir."6.gif\" alt=\"Smilie sad\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :x: ', '');\"><img src=\"".$smiliedir."7.gif\" alt=\"Smilie :x:\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :O_o: ', '');\"><img src=\"".$smiliedir."8.gif\" alt=\"Smilie oO\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :o ', '');\"><img src=\"".$smiliedir."9.gif\" alt=\"Smilie :o\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :lol: ', '');\"><img src=\"".$smiliedir."10.gif\" alt=\"Smilie *lol*\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :x( ', '');\"><img src=\"".$smiliedir."11.gif\" alt=\"Smilie :x(\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :no: ', '');\"><img src=\"".$smiliedir."12.gif\" alt=\"Smilie *Kopfsch&uuml;ttel*\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :-: ', '');\"><img src=\"".$smiliedir."13.gif\" alt=\"Smilie :-:\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :rolleyes: ', '');\"><img src=\"".$smiliedir."14.gif\" alt=\"Smilie, der mit den Augen rollt\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' ;( ', '');\"><img src=\"".$smiliedir."15.gif\" alt=\"Weinender Smilie (sad)\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :) ', '');\"><img src=\"".$smiliedir."16.gif\" alt=\"Normaler lachender Smilie\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' ;)D ', '');\"><img src=\"".$smiliedir."17.gif\" alt=\"Smilie ;)D\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' X-X ', '');\"><img src=\"".$smiliedir."18.gif\" alt=\"Smilie X-X\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' ;) ', '');\"><img src=\"".$smiliedir."19.gif\" alt=\"Zwinkernder Smilie\" style=\"border:0;\" /></a>
            <a href=\"#textarea\" onclick=\"javascript:bbcinsert(' :yes: ', '');\"><img src=\"".$smiliedir."20.gif\" alt=\"Smilie :yes:\" style=\"border:0;\" /></a>
        </td>
    </tr>";
					}
				}
			
			// Pflichtfeld ?
			if($rowf['pflicht'] == 1) $pfl = "*";
			else $pfl = "";

			if($rowf['type'] == "text" || $rowf['type'] == "select")
				$felder .= "    <tr class=\"".$class."\">
				<td><b>".stripslashes($rowf['name']).$pfl."</b></td>";
			else
				$felder .= "    <tr class=\"".$class."\">
				<td colspan=\"2\"><b>".stripslashes($rowf['name']).$pfl."</b><br />\n";
			
			switch($rowf['type']){
			  case "text":
				// Maximale L�nge
				if(isset($rowf['length']) && !empty($rowf['length']) && is_numeric($rowf['length']))
					$maxlength = " maxlength=\"".$rowf['length']."\"";
				else $maxlength = "";
				
				$felder .= "
				<td><input type=\"text\" name=\"feld_".$rowf['id']."\"".$maxlength." value=\"".$row['field_'.$rowf['id']]."\" size=\"30\" /></td>";
			  break;
			  case "select":
				$options = "";
				$optionarray = explode("\n",$rowf['wert']);
				if(is_array($optionarray)){
					foreach($optionarray as $option){
						if(chop($option) == $row['field_'.$rowf['id']]) $sel = " selected=\"selected\"";
						else $sel = "";
						
						$options .= "<option".$sel.">".chop($option)."</option>\n";
						}
					}
				else $options = "<option>".$optionarray."</option>\n";
				
				$felder .= "
				<td><select name=\"feld_".$rowf['id']."\" class=\"input_select\">".$options."</select></td>";
			  break;
			  case "textarea":
				if($rowf['id'] == $eintragsfield_id) $fieldname = "newsfeld";
				else $fieldname = "feld_".$rowf['id'];
				$felder .= "<textarea name=\"".$fieldname."\" rows=\"10\" cols=\"65\" rows=\"10\" cols=\"65\" style=\"font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-style: normal;\">".stripslashes($row['field_'.$rowf['id']])."</textarea>";
				
				if($settings['gbookbbc'] == 1 || $settings['gbooksmilies'] == 1){
					if($row['bbc_smile_deaktiv'] == 0) $c = "";
					else $c = " checked=\"checked\"";
					
					$felder .= "<br /><input type=\"checkbox\" name=\"deaktiv_bbc\" value=\"1\"".$c." /> Smilies &amp; BB-Code <b>de</b>aktivieren?";
					}
				
				$felder .= "</td>";
			  break;
			  }
			
			$felder .= "
			</tr>";
			}
		echo $felder;
		echo "
		<tr>
			<td class=\"tra\"><input type=\"reset\" value=\"Reset\" class=\"input\" /></td>
			<td class=\"tra\" align=\"right\">
				<input type=\"hidden\" name=\"var1\" value=\"".$row['id']."\" />
				<input type=\"hidden\" name=\"action\" value=\"save_entry\" />
				<input type=\"submit\" name=\"submit\" value=\"Speichern &raquo;\" class=\"input\" />
			</td>
		</tr>
	</table>
</form>";
		}
	}
	
	
	
	
	
	
	
	
// Eintrag bearbeiten (Speichern)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "save_entry" && $userdata[$modul] == 1 &&
	isset($_REQUEST['var1']) && !empty($_REQUEST['var1']) && is_numeric($_REQUEST['var1'])){
	$_REQUEST['feld_'.$eintragsfield_id] = $_REQUEST['newsfeld'];
	
	// Pflichtfelder & parsing �berpr�fen
	$error = false;
	$list = mysql_query("SELECT id,parse,pflicht FROM ".$mysql_tables['gb_fields']." WHERE pflicht = '1' OR parse != ''");
	while($row = mysql_fetch_array($list)){

		if($row['pflicht'] == 1){
			if(!isset($_REQUEST['feld_'.$row['id']]) || isset($_REQUEST['feld_'.$row['id']]) && empty($_REQUEST['feld_'.$row['id']])){
				$error = true;
				break;
				}
			}
			
		if(!empty($row['parse'])){
			switch($row['parse']){
			  case "email":
				if(isset($_REQUEST['feld_'.$row['id']]) && !empty($_REQUEST['feld_'.$row['id']]))
					if(!check_mail($_REQUEST['feld_'.$row['id']])) $error = true;
			  break;
			  case "url":
				if(isset($_REQUEST['feld_'.$row['id']]) && !empty($_REQUEST['feld_'.$row['id']])){
					if(!strchr($_REQUEST['feld_'.$row['id']],"http://"))
						$_REQUEST['feld_'.$row['id']] = "http://".$_REQUEST['feld_'.$row['id']];
					
					if(!_01gbook_check_url($_REQUEST['feld_'.$row['id']])) $error = true;
					}
			  break;
			  }
			
			if($error) break;
			}
		}
		
	if(!$error){
		if(!isset($_REQUEST['deaktiv_bbc']) || isset($_REQUEST['deaktiv_bbc']) && empty($_REQUEST['deaktiv_bbc'])) $_REQUEST['deaktiv_bbc'] = 0;
		// Build Update-Query
		$query_werte = "";
		$list = mysql_query("SELECT id,name,type,length FROM ".$mysql_tables['gb_fields']." ORDER BY id");
		while($row = mysql_fetch_array($list)){
			if(isset($_REQUEST['feld_'.$row['id']]) && !empty($_REQUEST['feld_'.$row['id']])){
				$query_werte .= ",\nfield_".$row['id']." = ";
				if($row['type'] == "text" && !empty($row['length']) && $row['length'] > 0)
					$query_werte .= "'".mysql_real_escape_string(substr($_REQUEST['feld_'.$row['id']],0,$row['length']))."'";
				else
					$query_werte .= "'".mysql_real_escape_string($_REQUEST['feld_'.$row['id']])."'";
				}
			else
				$query_werte .= "";
			}

		mysql_query("UPDATE ".$mysql_tables['gb_entry']." SET 
					bbc_smile_deaktiv 	=	'".mysql_real_escape_string($_REQUEST['deaktiv_bbc'])."'
					".$query_werte."
					WHERE id = '".mysql_real_escape_string($_REQUEST['var1'])."' LIMIT 1");

		echo "
<script type=\"text/javascript\">
document.location.href = '".$filename."&action=show_entry&var1=".$_REQUEST['var1']."&var2=erfolg';
</script>";
		}
	else// Fehlermeldung: Pflichtfelder / parsing
		echo "<p class=\"meldung_error\"><b>Fehler:</b> Es wurden nicht alle ben&ouml;tigen Pflichtfelder
				ausgef&uuml;llt. Bitte gehen Sie <a href=\"javascript:history.back();\">zur&uuml;ck</a>.</p>";
	}
	
	
	
	
	
	
	
// Eintrag l�schen (Abfrage)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "del_entry" && $userdata[$modul] == 1 &&
	isset($_REQUEST['var1']) && !empty($_REQUEST['var1']) && is_numeric($_REQUEST['var1'])){
	
	echo "<p class=\"meldung_frage\">M�chten Sie den Eintrag wirklich l�schen?<br /><br />
			<a href=\"".$filename."&amp;action=dodel_entry&amp;var1=".$_REQUEST['var1']."\">JA</a> | <a href=\"javascript:window.close();\">Nein</a></p>";
	}
	
	
	
	
	
	
// Eintrag l�schen (l�schen)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "dodel_entry" && $userdata[$modul] == 1 &&
	isset($_REQUEST['var1']) && !empty($_REQUEST['var1']) && is_numeric($_REQUEST['var1'])){
	
	mysql_query("DELETE FROM ".$mysql_tables['gb_entry']." WHERE id='".mysql_real_escape_string($_REQUEST['var1'])."' LIMIT 1");
	
	echo "
<script type=\"text/javascript\">
opener.document.getElementById('del_erfolgsmeldung').style.display = 'block';
opener.document.getElementById('id".$_REQUEST['var1']."').style.display = 'none';
self.window.close();
</script>";
	}
?>

</div>
</body>
</html>

<?PHP
}//Ende Popup-if-Abfrage

// 01-G�stebuch Copyright 2009 by Michael Lorer - 01-Scripts.de
?>