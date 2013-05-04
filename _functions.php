<?PHP
/* 
	01-Gästebuch - Copyright 2009-2013 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01gbook
	Dateiinfo: 	Modulspezifische Funktionen
	#fv.101#
*/

/* SYNTAKTISCHER AUFBAU VON FUNKTIONSNAMEN BEACHTEN!!!
	_ModulName_beliebigerFunktionsname()
	Beispiel: 
	if(!function_exists("_example_TolleFunktion")){
		_example_TolleFunktion($parameter){ ... }
		}
*/

// Globale Funktionen - nötig!

// Funktion wird zentral aufgerufen, wenn ein Benutzer gelöscht wird.
/*$userid			UserID des gelöschten Benutzers
  $username			Username des gelöschten Benutzers
  $mail				E-Mail-Adresse des gelöschten Benutzers

RETURN: TRUE/FALSE
*/
if(!function_exists("_01gbook_DeleteUser")){
function _01gbook_DeleteUser($userid,$username,$mail){
return TRUE;
}
}







// Userstatistiken holen
/* @param int $userid			UserID, zu der die Infos geholt werden sollen

RETURN: Array(
			statcat[x] 		=> "Statistikbezeichnung für Frontend-Ausgabe"
			statvalue[x] 	=> "Auszugebender Wert"
			)
  */
if(!function_exists("_01gbook_getUserstats")){
function _01gbook_getUserstats($userid){
return false;
}
}





// Drop-Down zum Sortieren der Gästebuchfelder
/*$catids			Kommaseparierter CatID-String

RETURN: <option>-Tags für select-Feld
  */
if(!function_exists("_01gbook_FieldSortDropDown")){
function _01gbook_FieldSortDropDown($selected=1){
global $mysql_tables,$mysqli;

$return = "";
$menge = 0;
list($menge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['gb_fields']." WHERE hide = '0'")->fetch_array(MYSQLI_NUM);

for($x=1;$x<=$menge;$x++){
	if($x == $selected) $return .= "<option selected=\"selected\">".$x."</option>\n";
	else $return .= "<option>".$x."</option>\n";
	}

return $return;
}
}








// URL validieren
/*$url			URL, deren Syntax überprüft werden soll

RETURN: TRUE/FALSE
  */
if(!function_exists("_01gbook_check_url")){
function _01gbook_check_url($url){

if(empty($url)) return TRUE;
else return preg_match('/^(http|https|ftp):\/\/([A-Z0-9][ A-Z0-9_]*(?:\.[A-Z0-9][A-Z0-9_]*)+):?(\d+)?\/?/i', $url);

}
}








// Angelegte Felder in einen Asso. Array einlesen
/*$where			MySQL Where-Argumente

RETURN: Assoz. Array
  */
if(!function_exists("_01gbook_getFields")){
function _01gbook_getFields($where){
global $mysql_tables,$mysqli;

$fields = array();
$list = $mysqli->query("SELECT id,name,type,parse FROM ".$mysql_tables['gb_fields']."".$where." ORDER BY sortorder,name");
while($row = $list->fetch_assoc()){
	$fields[$row['id']]['id']		= $row['id'];
	$fields[$row['id']]['name']		= stripslashes($row['name']);
	$fields[$row['id']]['type']		= stripslashes($row['type']);
	$fields[$row['id']]['parse']	= stripslashes($row['parse']);
	}
	
return $fields;
}
}








// Einträge auflisten (ACP)
/*$query			gültiger MySQL-Query für die Eintragstabelle
  $option			Manipulator für zusätzliche / weniger Spalten

RETURN: Komplette Liste (HTML)
  */
if(!function_exists("_01gbook_getEntries_acp")){
function _01gbook_getEntries_acp($query,$option){
global $_GET,$modul,$filename,$namefield_id,$eintragsfield_id,$mysqli;

if(!isset($_GET['site'])) $_GET['site'] = 0;

$return = "<form action=\"".$filename."&amp;site=".$_GET['site']."\" method=\"post\">\n";
$return .= "<table border=\"0\" align=\"center\" width=\"100%\" cellpadding=\"3\" cellspacing=\"5\" class=\"rundrahmen\">

    <tr>
		<td class=\"tra\" colspan=\"2\"><b>G&auml;stebuch-Eintr&auml;ge</b></td>\n";

if($option == "free") $return .= "<td class=\"tra\" width=\"25\"><!-- Freischalten -->&nbsp;</td>\n";

$return .="		<td class=\"tra\" width=\"25\"><!-- Ansehen -->&nbsp;</td>
		<td class=\"tra\" width=\"25\"><!-- Bearbeiten -->&nbsp;</td>
		<td class=\"tra\" width=\"25\" align=\"center\"><img src=\"images/icons/icon_trash.gif\" alt=\"M&uuml;lleimer\" title=\"Kommentar l&ouml;schen\" /></td>
	</tr>\n\n";

$count = 0;
$list = $mysqli->query($query);
while($row = $list->fetch_assoc()){
	if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
	if($option == "free" && $row['frei'] == 1)
		$colspan = " colspan=\"2\"";
	else
		$colspan = "";
	
	if(isset($row['bbc_smile_deaktiv']) && $row['bbc_smile_deaktiv'] == 0) $x = 1;
	else $x = 0;
	
	$return .= "<tr id=\"id".$row['id']."\">
	<td class=\"".$class."\" width=\"25\" align=\"center\"><input type=\"checkbox\" name=\"delid[]\" value=\"".$row['id']."\" /></td>
	<td class=\"".$class."\"".$colspan." onclick=\"gbpopup('show_entry','".$row['id']."','','',580,450);\" style=\"cursor: pointer;\">
		Am ".date("d.m.Y - H:i",$row['timestamp'])."Uhr von <b>".stripslashes($row['field_'.$namefield_id])."</b> (".$row['ip'].") verfasst:<br />
		".substr(strip_tags(bb_code_comment(stripslashes($row['field_'.$eintragsfield_id]),1,$x,$x)),0,250)." [...]
	</td>\n";
	
	if($option == "free" && $row['frei'] == 0) $return .= "<td class=\"".$class."\" align=\"center\"><img src=\"images/icons/ok.gif\" alt=\"OK\" title=\"Eintrag freischalten\" id=\"free".$row['id']."\" onclick=\"AjaxRequest.send('modul=".$modul."&ajaxaction=freeentry&id=".$row['id']."');\" /></td>\n";
	
	$return .= "<td class=\"".$class."\" align=\"center\"><a href=\"javascript:gbpopup('show_entry','".$row['id']."','','',580,450);\"><img src=\"images/icons/icon_show.gif\" alt=\"Auge\" title=\"Eintrag ansehen\" /></a></td>
	<td class=\"".$class."\" align=\"center\"><a href=\"javascript:gbpopup('edit_entry','".$row['id']."','','',580,450);\"><img src=\"images/icons/icon_edit.gif\" alt=\"Stift+Papier\" title=\"Eintrag bearbeiten\" /></a></td>
	<td class=\"".$class."\" align=\"center\" nowrap=\"nowrap\"><img src=\"images/icons/icon_delete.gif\" alt=\"L&ouml;schen - rotes X\" title=\"Eintrag l&ouml;schen\" class=\"fx_opener\" style=\"border:0; float:left;\" align=\"left\" /><div class=\"fx_content tr_red\" style=\"width:60px; display:none;\"><a href=\"#foo\" onclick=\"AjaxRequest.send('modul=".$modul."&ajaxaction=delentry&id=".$row['id']."');\">Ja</a> - <a href=\"#foo\">Nein</a></div></td>
	</tr>\n\n";
	}

$return .= "</table>";	
$return .= "<p>
<input type=\"checkbox\" name=\"delselected\" value=\"1\" />
<input type=\"submit\" value=\"Ausgewählte Eintr&auml;ge l&ouml;schen\" class=\"input\" />
Es erfolgt <b>keine</b> weitere Abfrage!
</p>";
$return .= "</form>";
	
return $return;
}
}

?>