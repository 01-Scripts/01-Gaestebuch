<?PHP
/* 
	01-Gästebuch - Copyright 2009-2013 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01gbook
	Dateiinfo: 	Frontend-Ausgabe
	#fv.101#
*/

//Hinweis zum Einbinden des Gästebuchs per include();
/*Folgender PHP-Code nötig:

<?PHP
$subfolder 		= "01scripts/";
$modul			= "01gbook/";

include($subfolder."01module/".$modul."01gbook.php");
?>

*/

$frontp = 1;
$flag_acp = FALSE;
if(!isset($flag_utf8)) $flag_utf8 = FALSE;
if(!isset($flag_nocss)) $flag_nocss = FALSE;

if(isset($subfolder) && !empty($subfolder)){
    if(substr_count($subfolder, "/") < 1){ $subfolder .= "/"; }
	}
else
	$subfolder = "";

// Globale Config-Datei einbinden
include_once($subfolder."01_config.php");
include_once($subfolder."01acp/system/headinclude.php");

$modulvz = $modul."/";
// Modul-Config-Dateien einbinden
include_once($moduldir.$modulvz."_headinclude.php");
include_once($moduldir.$modulvz."_functions.php");

// Variablen ggf. vorbelegen
if(!isset($_GET[$names['gpage']])) $_GET[$names['gpage']] = "";

// Variablen
$tempdir	= $moduldir.$modulvz.$tempdir;			// Template-Verzeichnis

$filename		= $_SERVER['PHP_SELF'];
$sites			= 0;
$flag_showform	= false;
$message		= "";

$link_form		= addParameter2Link($filename,$names['gpage']."=".$_GET[$names['gpage']]);
$link_addentry	= addParameter2Link($filename,"doshow=addentry&amp;".$names['gpage']."=".$_GET[$names['gpage']]);


// externe CSS-Datei / CSS-Eigenschaften?
if(isset($settings['extern_css']) && !empty($settings['extern_css']) && $settings['extern_css'] != "http://" && !$flag_nocss)
	$echo_css = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$settings['extern_css']."\" />";
elseif(isset($settings['csscode']) && !empty($settings['csscode']) && !$flag_nocss)
	$echo_css = "<style type=\"text/css\">
".$settings['csscode']."
</style>";
else $echo_css = "";

// main_top einfügen
include($tempdir."main_top.html");







// Neuen Eintrag hinzufügen
if(isset($_POST['send_entry']) && $_POST['send_entry'] == 1 &&
   isset($_POST['uid']) && !empty($_POST['uid']) && strlen($_POST['uid']) == 32 &&
   (isset($_POST['antispam']) && md5($_POST['antispam']) == $_SESSION['antispam01'] && $settings['gbookantispam'] == 1 || $settings['gbookantispam'] == 0)){

	// UID überprüfen
	$_POST['uid'] = strip_tags($_POST['uid']);
	$glist = $mysqli->query("SELECT id FROM ".$mysql_tables['gb_entry']." WHERE uid='".$mysqli->escape_string($_POST['uid'])."'");
	if($glist->num_rows == 0){
	
		$error = FALSE;
		// Pflichtfelder & parsing überprüfen
		$list = $mysqli->query("SELECT id,parse,pflicht FROM ".$mysql_tables['gb_fields']." WHERE pflicht = '1' OR parse != ''");
		while($row = $list->fetch_assoc()){

			if($row['pflicht'] == 1){
				if(!isset($_POST['feld_'.$row['id']]) || isset($_POST['feld_'.$row['id']]) && empty($_POST['feld_'.$row['id']])){
					$error = TRUE;
					break;
					}
				}
				
			if(!empty($row['parse'])){
				switch($row['parse']){
				  case "email":
				    if(isset($_POST['feld_'.$row['id']]) && !empty($_POST['feld_'.$row['id']]))
						if(!check_mail($_POST['feld_'.$row['id']])) $error = TRUE;
				  break;
				  case "url":
				    if(isset($_POST['feld_'.$row['id']]) && !empty($_POST['feld_'.$row['id']])){
						if(!strchr($_POST['feld_'.$row['id']],"http://"))
							$_POST['feld_'.$row['id']] = "http://".strip_tags($_POST['feld_'.$row['id']]);
						
						if(!_01gbook_check_url($_POST['feld_'.$row['id']])) $error = TRUE;
						}
				  break;
				  }
				
				if($error) break;
				}
			}
		
		// Wenn alle Pflichtfelder ausgefüllt waren und die parsing-Felder korrekt sind Eintrag speichern
		if(!$error){
			// Build Insert-Query
			$query_fields	= "";
			$query_werte	= "";
			$email_werte	= "";
			$list = $mysqli->query("SELECT id,name,fieldtype,length FROM ".$mysql_tables['gb_fields']." ORDER BY id");
			while($row = $list->fetch_assoc()){
				$query_fields .= ", field_".$row['id'];
				
				if(isset($_POST['feld_'.$row['id']]) && !empty($_POST['feld_'.$row['id']])){
					if($row['fieldtype'] == "text" && !empty($row['length']) && $row['length'] > 0)
						$value = substr($_POST['feld_'.$row['id']],0,$row['length']);
					else
						$value = $_POST['feld_'.$row['id']];

					if($flag_utf8)
						$value = htmlentities(utf8_decode($value), $htmlent_flags, $htmlent_encoding_pub);
					else
						$value = htmlentities($value, $htmlent_flags, $htmlent_encoding_pub);

					$query_werte .= ",\n'".$mysqli->escape_string($value)."'";
					}
				else
					$query_werte .= ",\n''";
					
				if($settings['gbooksendmail'] == 1 && isset($_POST['feld_'.$row['id']]) && !empty($_POST['feld_'.$row['id']])){
					$email_werte .= stripslashes($row['name'])."\n".$_POST['feld_'.$row['id']]."\n\n";
					}
				}
			
			if(isset($_POST['deaktiv_bbc']) && $_POST['deaktiv_bbc'] == 1) $deaktiv = 1;
			else $deaktiv = 0;

			// Eintragung in Datenbank vornehmen:
			$sql_insert = "INSERT INTO ".$mysql_tables['gb_entry']." (utimestamp,uid,ip,frei,bbc_smile_deaktiv".$query_fields.") VALUES (
							'".time()."',
							'".$mysqli->escape_string($_POST['uid'])."',
							'".$mysqli->escape_string($_SERVER['REMOTE_ADDR'])."',
							'".$mysqli->escape_string($settings['gbookfreeentries'])."',
							'".$deaktiv."'
							".$query_werte."
							)";
			$mysqli->query($sql_insert) OR die($mysqli->error);
			$inserted_id = $mysqli->insert_id;
			
			if($inserted_id > 0){
				if($settings['gbookfreeentries'] == 0){
					$message = "saved_locked";
					$add2mail = "Der neue Eintrag muss vor der Veröffentlichung von Ihnen freigeschaltet werden.\nBitte loggen Sie sich dazu in den Administrationsbereich ein.";
					}
				else{
					$message = "saved_free";
					$add2mail = "";
					}
					
				// E-Mail verschicken?
				if($settings['gbooksendmail'] == 1){
					$headerFields = array(
"From:".$settings['email_absender']."<".$settings['email_absender'].">",
"MIME-Version: 1.0",
"Content-Type: text/html;charset=".$htmlent_encoding_pub.""
);
					$email_betreff = $settings['sitename']." - Neuer Eintrag im Gästebuch";
					$email_werte = preg_replace("/(content-type:|bcc:|cc:|to:|from:)/im","",$email_werte);
					$email_content = "Guten Tag,

es wurde soeben ein neuer Eintrag im Gästebuch hinterlassen.
".$add2mail."

Inhalt des Eintrags:
".$email_werte."

Zum Administrationsbereich gelangen Sie unter:
".$settings['absolut_url']."01acp/
---
Webmailer";
					mail($emailempf,$email_betreff,$email_content,implode("\r\n", $headerFields));
					}
				}
			else
				$message = "unknown_error";
			}
		else{
			$message = "pflicht_failed";
			$flag_showform = TRUE;
			}
		}// Ende: UID überprüft
	}
elseif(isset($_POST['send_entry']) && $_POST['send_entry'] == 1){
	$message = "antispam_failed";
	$flag_showform = TRUE;
	}
	


include($tempdir."messages.html");



// Felder auflisten
if(isset($_GET['doshow']) && $_GET['doshow'] == "addentry" || $flag_showform){
	$felder = "";
	$list = $mysqli->query("SELECT * FROM ".$mysql_tables['gb_fields']." ORDER BY sortorder,name");
	while($row = $list->fetch_assoc()){
		if(!isset($_POST['feld_'.$row['id']])) $_POST['feld_'.$row['id']] = "";
		
		// Sonderfeld (Eingabefeld) ID == $eintragsfield_id ?
		if($row['id'] == $eintragsfield_id){
			if($settings['gbookbbc'] == 1){
				$fp = fopen($tempdir.$snip_bbc, "r");
				$felder .= fread($fp, filesize($tempdir.$snip_bbc));
				fclose($fp);
				}
			if($settings['gbooksmilies'] == 1){
				$fp = fopen($tempdir.$snip_smilies, "r");
				$felder .= str_replace("\$smiliedir",$smiliedir,fread($fp, filesize($tempdir.$snip_smilies)));
				fclose($fp);
				}
			}
		
		// Pflichtfeld ?
		if($row['pflicht'] == 1) $pfl = "*";
		else $pfl = "";

		if($row['fieldtype'] == "text" || $row['fieldtype'] == "select")
			$felder .= "    <tr class=\"fieldrow\">
			<td class=\"fieldname\">".stripslashes($row['name']).$pfl."</td>";
		else
			$felder .= "    <tr class=\"fieldrow\">
			<td colspan=\"2\" class=\"colspanfield\"><span class=\"fieldname\">".stripslashes($row['name']).$pfl."</span><br />\n";
		
		
		switch($row['fieldtype']){
		  case "text":
			// Size 
			if(isset($row['size']) && !empty($row['size']) && is_numeric($row['size']))
				$size = " size=\"".$row['size']."\"";
			else $size = "";
			
			// Maximale Länge
			if(isset($row['length']) && !empty($row['length']) && is_numeric($row['length']))
				$maxlength = " maxlength=\"".$row['length']."\"";
			else $maxlength = "";
			
			// Vorgabewert bzw. $_POST-Wert übernehmen
			if(isset($_POST['feld_'.$row['id']]) && !empty($_POST['feld_'.$row['id']]))
				$wert = " value=\"".parse_SafeString($_POST['feld_'.$row['id']])."\"";
			elseif(isset($row['wert']) && !empty($row['wert']))
				$wert = " value=\"".stripslashes($row['wert'])."\"";
			else $wert = "";
			

			$felder .= "
			<td class=\"fieldinput\"><input type=\"text\" name=\"feld_".$row['id']."\"".$size.$maxlength.$wert." class=\"input_field\" /></td>";
		  break;
		  case "select":
			$options = "";
			$optionarray = explode("\n",$row['wert']);
			if(is_array($optionarray)){
				foreach($optionarray as $option){
					if(isset($_POST['feld_'.$row['id']]) && $_POST['feld_'.$row['id']] == rtrim($option)) $sel = " selected=\"selected\"";
					else $sel = "";
					$options .= "<option".$sel.">".rtrim($option)."</option>\n";
					}
				}
			else $options = "<option>".$optionarray."</option>\n";
			
			$felder .= "
			<td class=\"fieldinput\"><select name=\"feld_".$row['id']."\" class=\"input_selectfield\">".$options."</select></td>";
		  break;
		  case "textarea":
			$size = explode("|",$row['size']);
			$felder .= "<textarea name=\"feld_".$row['id']."\" rows=\"".$size[0]."\" cols=\"".$size[1]."\" class=\"textareafeld\">".parse_SafeString($_POST['feld_'.$row['id']])."</textarea>";
			
			if($settings['gbookbbc'] == 1 || $settings['gbooksmilies'] == 1)
				$felder .= "<br /><input type=\"checkbox\" name=\"deaktiv_bbc\" value=\"1\" /> Smilies &amp; BB-Code <b>de</b>aktivieren?";
			
			$felder .= "</td>";
		  break;
		  }
		
		$felder .= "
		</tr>";
		}

	$zahl = mt_rand(1, 9999999999999);
	$uid = md5(time().$_SERVER['REMOTE_ADDR'].$zahl);
	// Formular für neuen Eintrag einfügen
	include($tempdir."addentry.html");
	}




	
	
	
// Einträge auflisten
// Einmalig Felder in Array einlesen (für weniger Querys)
$fields = _01gbook_getFields(" WHERE public = '1'");

// Einträge aus DB holen
$query = "SELECT * FROM ".$mysql_tables['gb_entry']." WHERE frei = '1' ORDER BY utimestamp DESC";
echo "<!-- 2559ad821dde361560dbf967c3406f51 -->";
makepages($query,$sites,$names['gpage'],$settings['gbook_perpage']);

// Einträge auflisten
$list = $mysqli->query($query);
while($row = $list->fetch_assoc()){
	$feldinhalte= "";
	$datum		= date("d.m.y",$row['utimestamp']);
	$uhrzeit	= date("G:i",$row['utimestamp']);
	echo "<!-- 2559ad821dde361560dbf967c3406f51 -->";
	$name = stripslashes($row['field_'.$namefield_id]);
	
	// Angelegte Felder durchgehen
	foreach($fields as $field){
		if($field['id'] != $namefield_id && !empty($row['field_'.$field['id']])){
			$feldinhalte .= "\n\n<p class=\"gbfrontend_zeile\">\n";
			
			// Feldnamen ausgeben (außer beim Nachrichtenfeld)
			if($field['id'] != $eintragsfield_id)
				$feldinhalte .= "<span class=\"gbfrontend_fieldname\">".$field['name']."</span> ";
			
			switch($field['fieldtype']){
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
	include($tempdir."entrybit.html");
	}
	
// Ausgabe der Seiten
if($sites > 1){
    $option_sites = "";
	for($o=1;$o<=$sites;$o++){
		if(isset($_GET[$names['gpage']]) && $o == $_GET[$names['gpage']]) $sel = " selected=\"selected\"";
		else $sel = "";
		
        $option_sites .= "<option".$sel.">".$o."</option>\n";
        }

    if(isset($_GET[$names['gpage']]) && $_GET[$names['gpage']] > 1){
        $sz = $_GET[$names['gpage']]-1;

        if($sz != 1)
			$szl1 = addParameter2Link($filename,$names['gpage']."=1");
		else $szl1 = "";

		$szl2 = addParameter2Link($filename,$names['gpage']."=".$sz);
        }
    else{ $szl1 = ""; $szl2 = ""; }
	
    if(!isset($_GET[$names['gpage']]) || isset($_GET[$names['gpage']]) && empty($_GET[$names['gpage']])){
        $current = 1;
        if($sites > 1){ $sv = 2; }
        }
    else{
        $current = $_GET[$names['gpage']];
        $sv = $_GET[$names['gpage']]+1;
        }
    
	if(isset($_GET[$names['gpage']]) && $_GET[$names['gpage']] < $sites || !isset($_GET[$names['gpage']]) && $sites > 1){
        
		$svl1 = addParameter2Link($filename,$names['gpage']."=".$sv);
        if($sv != $sites) 
			$svl2 = addParameter2Link($filename,$names['gpage']."=".$sites);
        }
    else{
		$svl1 = "";
		$svl2 = "";
		}
    }
include_once($tempdir."pages.html");
	

include_once($tempdir."main_bottom.html");
?>