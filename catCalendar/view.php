<?php
/**
 *
 *   This program is free software; you can redistribute it and/or view
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

catCalendarObject::view();

/*
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
	'dates'				=> $catCalendar->getEventsInPeriod(strtotime('2018-06-01'),strtotime('2018-06-30'),1),
	'calendars'			=> $catCalendar->getCalendar(),
	'page_link'			=> CAT_Helper_Page::getInstance()->properties( $page_id, 'link' ),
	'section_name'		=> str_replace( array('ä', 'ö', 'ü', 'ß'), array('ae', 'oe', 'ue', 'ss'), strtolower( $section['name'] ) ),
	'cal'				=> catCalendarObject::getDaysInYearMonth(strtotime('2018-06-03'))
);

#echo '<pre>';
#print_r(catCalendarObject::getDaysInYearMonth());
#echo '</pre>';
if ( file_exists( CAT_PATH . '/modules/lib_mdetect/mdetect/mdetect.php' ) )
{
	require_once( CAT_PATH . '/modules/lib_mdetect/mdetect/mdetect.php' );
	$uagent_obj = new uagent_info();
	if( $uagent_obj->DetectMobileQuick() )
	{
		$parser_data['options']['is_mobile']	= true;
	}
} else {
	$parser_data['options']['is_mobile']	= NULL;
}

$viewPHPpath	= CAT_PATH . '/modules/' . catCalendarObject::$directory .'/view/';

if ( file_exists( $viewPHPpath . $catCalendar->getVariant() . '/view.php' ) )
	include( $viewPHPpath . $catCalendar->getVariant() . '/view.php' );
elseif ( file_exists( $viewPHPpath . 'default/view.php' ) )
	include( $viewPHPpath . 'default/view.php' );

$templatePath	= CAT_PATH . '/modules/' . catCalendarObject::$directory .'/templates/';

if ( file_exists( $templatePath . $catCalendar->getVariant() . '/view.tpl' ) )
	$parser->setPath( $templatePath . $catCalendar->getVariant() );
elseif ( file_exists( $templatePath . 'default/view.tpl' ) )
	$parser->setPath( $templatePath . 'default/' );

$parser->setFallbackPath( $templatePath . 'default/' );

$parser->output(
	'view',
	$parser_data
);
*/
?>
