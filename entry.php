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

if($userdata[$modul] == 1){
?>

<script type="text/javascript">
function gbpopup(action,var1,var2,var3,w,h) {
window.open('_ajaxloader.php?modul=<?PHP echo $modul; ?>&action='+action+'&var1='+var1+'&var2='+var2+'&var3='+var3+'','_blank','width='+w+',height='+h+',scrollbars=yes,resizable=yes,status=no,toolbar=no,left=400,top=150');
}
</script>

<h1>Eintr&auml;ge verwalten</h1>

<p class="meldung_erfolg" id="del_erfolgsmeldung" style="display:none;">
	Der Eintrag wurde erfolgreich gel&ouml;scht.
</p>

<?PHP
// Selektierte Kommentare löschen
if(isset($_POST['delid']) && !empty($_POST['delid']) && 
   isset($_POST['delselected']) && $_POST['delselected'] == 1){
	$cup = 0;
	foreach($_POST['delid'] as $delid){
		mysql_query("DELETE FROM ".$mysql_tables['gb_entry']." WHERE id='".mysql_real_escape_string($delid)."' LIMIT 1");
		$cup++;
		}
	echo "<p class=\"meldung_erfolg\">Es wurden ".$cup." Eintr&auml;ge gel&ouml;scht</p>";
	}

$sites = 0;
$query = "SELECT id,timestamp,ip,frei,field_".$namefield_id.",field_".$eintragsfield_id." FROM ".$mysql_tables['gb_entry']." WHERE frei = '0' ORDER BY timestamp DESC";

$glist = mysql_query($query);
if(mysql_num_rows($glist) > 0){
	echo "<h2><img src=\"images/icons/ok.gif\" alt=\"gr&uuml;ner Haken\" title=\"Eintr&auml;ge freischalten\" /> Eintr&auml;ge freischalten</h2>";
	$query = makepages($query,$sites,"fsite",ACP_PER_PAGE);		
	$list = mysql_query($query);

	echo _01gbook_getEntries_acp($query,"free");

	echo echopages($sites,"80%","fsite","modul=".$modul."");
	}
?>

<h2><img src="images/icons/icon_edit.gif" alt="Stift+Papier" title="Eintrag bearbeiten" /> Eintr&auml;ge bearbeiten</h2>
<?PHP
$sites = 0;
$query = "SELECT id,timestamp,ip,frei,field_".$namefield_id.",field_".$eintragsfield_id." FROM ".$mysql_tables['gb_entry']." ORDER BY timestamp DESC";
$query = makepages($query,$sites,"site",ACP_PER_PAGE);		
$list = mysql_query($query);

echo _01gbook_getEntries_acp($query,"");

echo echopages($sites,"80%","site","modul=".$modul."");
?>














<?PHP
}else $flag_loginerror = true;

// 01-Gästebuch Copyright 2009 by Michael Lorer - 01-Scripts.de
?>