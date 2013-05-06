<h2><?PHP echo $form_data['title']; ?></h2>

<p><a href="_loader.php?modul=01gbook&amp;action=&amp;loadpage=fields">&laquo; Zur&uuml;ck</a></p>

<?PHP if($form_data['do'] == "addfield"){ ?>
<p class="meldung_hinweis">
Bitte beachten Sie: Einmal angelegte G&auml;stebuch-Felder k&ouml;nnen sp&auml;ter 
<b>nur in begrenztem Ma&szlig;e bearbeitet werden</b>.<br />
Bitte &uuml;berpr&uuml;fen Sie deshalb Ihre Eingaben sorgf&auml;ltig!
</p>
<?PHP }else{ ?>
<p class="meldung_hinweis">
&Auml;nderungen an diesem Feld haben keine Auswirkung auf den bereits bestehenden Inhalt dieses Feldes.
</p>
<?PHP } ?>
	
<form action="<?PHP echo $filename; ?>" method="post">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen trab">	
	<tr>
		<td><b>Feldname*:</b></td>
		<td><input type="text" name="feldname" size="50" value="<?PHP echo $form_data['feldname']; ?>" maxlength="100" /></td>
	</tr>
<?PHP
if($_POST['fieldtype'] == "textarea") echo "
	<tr>
		<td><b>Zeilen x Spalten</b></td>
		<td><input type=\"text\" name=\"rows\" value=\"".$form_data['zeilen']."\" size=\"5\" /> x <input type=\"text\" name=\"cols\" value=\"".$form_data['spalten']."\" size=\"5\" /></td>
	</tr>";
elseif($_POST['fieldtype'] == "text"){ echo "
	<tr>
		<td><b>Feldl&auml;nge:</b></td>
		<td><input type=\"text\" name=\"size\" value=\"".$form_data['size']."\" size=\"5\" /></td>
	</tr>";
	
	if($form_data['do'] == "addfield")
		echo "
	<tr>
		<td><b>Maximale Länge des eingegebenen Textes:</b> (max. 255)</td>
		<td><input type=\"text\" name=\"length\" value=\"".$form_data['length']."\" size=\"5\" /></td>
	</tr>";
	}
	
if($_POST['fieldtype'] == "text"){ echo "
	<tr>
		<td><b>Vorgegebener Feldinhalt:</b></td>
		<td><input type=\"text\" name=\"wert\" value=\"".$form_data['wert']."\" size=\"50\" /></td>
	</tr>
	<tr>
		<td><b>Feldinhalt...</b></td>
		<td>
			<select name=\"specialparse\" size=\"1\" class=\"input_select\">";
?>
				<option value=""<?PHP if($form_data['parse'] == "") echo " selected=\"selected\""; ?>>...kann beliebig sein</option>
				<option value="email"<?PHP if($form_data['parse'] == "email") echo " selected=\"selected\""; ?>>...muss eine syntaktisch korrekte E-Mail-Adresse sein</option>
				<option value="url"<?PHP if($form_data['parse'] == "url") echo " selected=\"selected\""; ?>>...muss eine syntaktisch gültige URL sein</option>
<?PHP echo "
			</select>
		</td>
	</tr>";
	}
elseif($_POST['fieldtype'] == "select") echo "
	<tr>
		<td><b>Werte für das DropDown-Feld:</b></td>
		<td>
			Bitte geben Sie pro Zeile ein <i>Auswahlkriterium</i> an:<br />
			<textarea name=\"wert\" rows=\"7\" cols=\"50\" style=\"font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-style: normal;\">".$form_data['wert']."</textarea>
		</td>
	</tr>";
	
	
?>
	<tr>
		<td><b>Feld ist ein Pflichtfeld</b></td>
		<td><input type="checkbox" name="pflicht" value="1"<?PHP if($form_data['pflicht'] == 1) echo " checked=\"checked\""; ?> /></td>
	</tr>
	
	<tr>
		<td ><b>Eingabe &ouml;ffentlich ausgeben?</b><br />Anderenfalls ist die Eingabe nur im ACP zu sehen</td>
		<td ><input type="checkbox" name="public" value="1"<?PHP if($form_data['public'] == 1) echo " checked=\"checked\""; ?> /></td>
	</tr>
	
	<tr>
		<td><input type="reset" value="Reset" class="input" /></td>
		<td align="right">
			<input type="hidden" name="do" value="<?PHP echo $form_data['do']; ?>" />
			<input type="hidden" name="id" value="<?PHP echo $form_data['id']; ?>" />
			<input type="hidden" name="type" value="<?PHP echo $form_data['type']; ?>" />
			<input type="submit" value="<?PHP echo $form_data['sendbutton']; ?>" class="input" />
		</td>
	</tr>

</table>
</form>