<?PHP
/* 
	01-Artikelsystem V3 - Copyright 2006-2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01article
	Dateiinfo: 	Artikelsystem - Modul-Startseite (acp)
	#fv.3002#
*/
?>

<div class="acp_startbox">
<p align="center"><b class="yellow"><?PHP echo $module[$modul]['instname']; ?></b></p>

<div class="acp_innerbox">
	<h4>Informationen</h4>
	<p>
	<b>Modul-Version:</b> <?PHP echo $module[$modul]['version']; ?><br /><br />
	
	<b>Eintr&auml;ge (freigeschaltet):</b> <?PHP list($emenge) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM ".$mysql_tables['gb_entry']." WHERE frei = '1'")); echo $emenge; ?><br />
	<b>Eintr&auml;ge (nicht freigeschaltet):</b> <?PHP list($emenge) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM ".$mysql_tables['gb_entry']." WHERE frei = '0'")); echo "<a href=\"_loader.php?modul=".$modul."&amp;action=&amp;loadpage=entry\">".$emenge."</a>"; ?><br />
	</p>
</div>

<div class="acp_innerbox">
	<h4>5 neueste Eintr&auml;ge</h4>

	<?PHP
	$query = "SELECT id,timestamp,field_".$namefield_id.",field_".$eintragsfield_id." FROM ".$mysql_tables['gb_entry']." WHERE frei = '1' ORDER BY timestamp DESC LIMIT 5";
	$list = mysql_query($query);
	while($row = mysql_fetch_array($list)){
		echo "<p><i>".date("d.m.y, G:i",$row['timestamp'])."</i> &bull; <b>".stripslashes($row['field_'.$namefield_id])."</b><br />
		".substr(stripslashes(strip_tags($row['field_'.$eintragsfield_id])),0,100)."...
		</p>";
		}
	?>
</div>

<br />

</div>