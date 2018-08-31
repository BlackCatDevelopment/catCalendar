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

// include class.secure.php to protect this file and the whole CMS!
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
// end include class.secure.php



require_once( 'inc/class.catCalendarObject.php' );

#include_once "import.php";

catCalendarObject::modify();
/*
require_once "inc/class.catCalendarObject.php";
require_once "inc/class.catCalendarEvent.php";

$catCalendar	= new catCalendarObject();

$parser_data	= array(
	'CAT_ADMIN_URL'		=> CAT_ADMIN_URL,
	'CAT_URL'			=> CAT_URL,
	'page_id'			=> $page_id,
	'section_id'		=> $section_id,
	'version'			=> CAT_Helper_Addons::getModuleVersion('catCalendar'),
	'variant'			=> $catCalendar->getVariant(),
	'module_variants'	=> catCalendarObject::getAllVariants(),
	'options'			=> $catCalendar->getOption(),
	'dates'				=> $catCalendar->getAllEvents(),
	'calendars'			=> $catCalendar->getCalendar()
);

#include_once "import.php";

$modifyPHPpath	= CAT_PATH . '/modules/' . catCalendarObject::$directory .'/modify/';

if ( file_exists( $modifyPHPpath . $catCalendar->getVariant() . '/modify.php' ) )
	include( $modifyPHPpath . $catCalendar->getVariant() . '/modify.php' );
elseif ( file_exists( $modifyPHPpath . 'default/modify.php' ) )
	include( $modifyPHPpath . 'default/modify.php' );

$templatePath	= CAT_PATH . '/modules/' . catCalendarObject::$directory .'/templates/';

if ( file_exists( $templatePath . $catCalendar->getVariant() . '/modify.tpl' ) )
	$parser->setPath( $templatePath . $catCalendar->getVariant() );
elseif ( file_exists( $templatePath . 'default/modify.tpl' ) )
	$parser->setPath( $templatePath . 'default/' );

$parser->setFallbackPath( $templatePath . 'default/' );

$parser->output(
	'modify',
	$parser_data
);
*/
?>