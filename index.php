<?PHP
/* 
	01-Gästebuch - Copyright 2009-2014 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01gbook
	Dateiinfo: 	ACP-Startseite des 01-Gästebuch
	#fv.101#
*/
?>

<div class="acp_startbox">
<p align="center"><b class="yellow"><?PHP echo $module[$modul]['instname']; ?></b></p>

<div class="acp_innerbox">
	<h4>Informationen</h4>
	<p>
	<b>Modul-Version:</b> <?PHP echo $module[$modul]['version']; ?><br /><br />
	<b>Eintr&auml;ge (freigeschaltet):</b> <?PHP list($emenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['gb_entry']." WHERE frei = '1'")->fetch_array(MYSQLI_NUM); echo $emenge; ?><br />
	<b>Eintr&auml;ge (nicht freigeschaltet):</b> <?PHP list($emenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['gb_entry']." WHERE frei = '0'")->fetch_array(MYSQLI_NUM); echo "<a href=\"_loader.php?modul=".$modul."&amp;action=&amp;loadpage=entry\">".$emenge."</a>"; ?><br />
	</p>
</div>

<div class="acp_innerbox">
	<h4>5 neueste Eintr&auml;ge</h4>

	<?PHP
	$query = "SELECT id,utimestamp,field_".$namefield_id.",field_".$eintragsfield_id." FROM ".$mysql_tables['gb_entry']." WHERE frei = '1' ORDER BY utimestamp DESC LIMIT 5";
	$list = $mysqli->query($query);
	while($row = $list->fetch_assoc()){
		echo "<p><i>".date("d.m.y, G:i",$row['utimestamp'])."</i> &bull; <b>".stripslashes($row['field_'.$namefield_id])."</b><br />
		".substr(stripslashes(strip_tags($row['field_'.$eintragsfield_id])),0,100)."...
		</p>";
		}
	?>
</div>

<br />

</div>