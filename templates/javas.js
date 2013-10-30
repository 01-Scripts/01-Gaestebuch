/* 
01-Gästebuch - Copyright 2009-2013 by Michael Lorer - 01-Scripts.de
Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
Modul:		01gbook
Dateiinfo:  Frontend-BB-Code-JavaScript-Funktion
Unkomprimierte Version der Datei: https://github.com/01-Scripts/01-Gaestebuch/blob/V1.0.1/01gbook/templates/javas.js
#fv.101#
*/


/*BB-Code-Funktion (c) by http://aktuell.de.selfhtml.org/artikel/javascript/bbcode/index.htm */
function bbcinsert(aTag, eTag) {
  var input = document.forms['gbuch'].elements['feld_4'];
  input.focus();
  /* für Internet Explorer */
  if(typeof document.selection != 'undefined') {
    /* Einfügen des Formatierungscodes */
    var range = document.selection.createRange();
    var insText = range.text;
    range.text = aTag + insText + eTag;
    /* Anpassen der Cursorposition */
    range = document.selection.createRange();
    if (insText.length == 0) {
      range.move('character', -eTag.length);
    } else {
      range.moveStart('character', aTag.length + insText.length + eTag.length);      
    }
    range.select();
  }
  /* für neuere auf Gecko basierende Browser */
  else if(typeof input.selectionStart != 'undefined')
  {
    /* Einfügen des Formatierungscodes */
    var start = input.selectionStart;
    var end = input.selectionEnd;
    var insText = input.value.substring(start, end);
    input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
    /* Anpassen der Cursorposition */
    var pos;
    if (insText.length == 0) {
      pos = start + aTag.length;
    } else {
      pos = start + aTag.length + insText.length + eTag.length;
    }
    input.selectionStart = pos;
    input.selectionEnd = pos;
  }
  /* für die übrigen Browser */
  else
  {
    /* Abfrage der Einfügeposition */
    var pos;
    var re = new RegExp('^[0-9]{0,3}$');
    while(!re.test(pos)) {
      pos = prompt("Einfügen an Position (0.." + input.value.length + "):", "0");
    }
    if(pos > input.value.length) {
      pos = input.value.length;
    }
    /* Einfügen des Formatierungscodes */
    var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
    input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
  }
}