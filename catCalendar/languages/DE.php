<?php
/**
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 3 of the License, or (at
 *   your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful, but
 *   WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 *   General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, see <http://www.gnu.org/licenses/>.
 *
 *   @author			Matthias Glienke, creativecat
 *   @copyright			2018, Black Cat Development
 *   @link				https://blackcat-cms.org
 *   @license			http://www.gnu.org/licenses/gpl.html
 *   @category			CAT_Modules
 *   @package			catCalendar
 *
 */

if (defined('CAT_PATH')) {	
	include(CAT_PATH.'/framework/class.secure.php'); 
} else {
	$oneback = "../";
	$root = $oneback;
	$level = 1;
	while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
		$root .= $oneback;
		$level += 1;
	}
	if (file_exists($root.'/framework/class.secure.php')) { 
		include($root.'/framework/class.secure.php'); 
	} else {
		trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
	}
}

$module_description	  = 'Dieses Addon bietet eine einfache M&ouml;glichkeit einen Bildslider oder eine einfache Gallerie auf ihrer Internetseite einzubinden. F&uuml;r mehr Informationen lesen Sie <a href="https://github.com/BlackCatDevelopment/catGallery_for_BlackCatCMS" target="_blank">GitHub</a>.<br/><br/>Done by Matthias Glienke, <a class="icon-creativecat" href="http://creativecat.de"> creativecat</a>';

$LANG = array(
	'Set skin'						=> 'Variante setzen',
	'Save skin &amp; reload'		=> 'Speichern &amp; Neuladen',

// --- modify ---				
	'Options for frontend'			=> 'Optionen f&uuml;rs Frontend',
	'Cancel'						=> 'Abbrechen',
	'Calendar'						=> 'Kalender',
	'All calendars'					=> 'Alle Kalender',
	'Event title'					=> 'Name des Termins',
	'Place'							=> 'Ort hinzuf&uuml;gen',
	'All day'						=> 'Ganzt&auml;gig',
	'From'							=> 'Von',
	'from date'						=> 'von Datum',
	'from time'						=> 'von Zeit',
	'Until'							=> 'Bis',
	'until date'					=> 'bis Datum',
	'until time'					=> 'bis Zeit',
	'Description'					=> 'Beschreibung',
	'Add event'						=> 'Termin hinzuf&uuml;gen',
	'Reset Form'					=> 'Formular leeren',


// --- save ---				
	'Save event'					=> 'Termin speichern',
	'Delete event'					=> 'Termin l&ouml;schen',
	'Duplicate event'				=> 'Termin kopieren',
	'An error occoured!'			=> 'Es ist ein Fehler aufgetraten',
	'Content loaded'				=> 'Inhalt geladen',
	'Content saved successfully'	=> 'Inhalt erfolgreich gespeichert',
	'Options saved successfully!'	=> 'Optionen erfolgreich gespeichert',
	'Variant saved successfully!'	=> 'Variante erfolgreich gespeichert',
	'You sent an invalid ID'		=> 'Es wurde keine gültige ID übermittelt.',
	'Publish event'					=> 'Termin freigeben',
	'Unpublish event'				=> 'Termin verstecken',
	
	'Will be automatically set on first save'	=> 'Wird automatisch beim ersten Speichern erstellt',

// --- frontend ---				
	'Read more'						=> 'Mehr lesen'

);
