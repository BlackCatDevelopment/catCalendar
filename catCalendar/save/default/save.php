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

if ( CAT_Helper_Page::getPagePermission( $page_id, 'admin' ) !== true )
{
	$backend->print_error( 'You do not have permissions to modify this page!' );
}

// ============================= 
// ! Get the current gallery_id 
// ============================= 

$action		= CAT_Helper_Validate::getInstance()->sanitizePost( 'action' );

if ( $calID = CAT_Helper_Validate::getInstance()->sanitizePost( 'calid','numeric' ) )
{
	// ====================================== 
	// ! Upload images and save to database
	// ====================================== 
	switch ( $action )
	{
		case 'getEvents':
			$success		= $catCalendar->getAllEvents( $calID != -1 ? $calID : NULL, true );

			$ajax_return	= array(
				'message'	=> $lang->translate( 'Calendar loaded successfully' ),
				'events'	=> $success,
				'success'	=> $success ? true : false
			);
			break;
		case 'saveOptions':
			$options		= CAT_Helper_Validate::getInstance()->sanitizePost('options');

			// =========================== 
			// ! save options for gallery   
			// =========================== 
			if ( $options != '' )
			{
				foreach( array_filter( explode(',', $options) ) as $option )
				{
					if( !$catCalendar->saveOptions( $option, CAT_Helper_Validate::getInstance()->sanitizePost( $option ) )) $error = true;
				}
			}
			$ajax_return	= array(
				'message'	=> $lang->translate( 'Options saved successfully!' ),
				'success'	=> true
			);
			break;
		case 'publishIMG':
			// =========================== 
			// ! save options for gallery   
			// =========================== 
			$success		= $catCalendar->publishImg( $imgID );
			$ajax_return	= array(
				'message'	=> $success	? $lang->translate( 'Image published successfully!' ) : $lang->translate( 'Image unpublished successfully!' ),
				'published'	=> $success,
				'success'	=> true
			);
			break;
		default:
			// =========================== 
			// ! save variant of images   
			// =========================== 
			$catCalendar->saveOptions( 'variant', CAT_Helper_Validate::getInstance()->sanitizePost('variant') );

			$ajax_return	= array(
				'message'	=> $lang->translate( 'Variant saved successfully!' ),
				'success'	=> true
			);

			break;
	}
} elseif ( $eventID = CAT_Helper_Validate::getInstance()->sanitizePost( 'eventid','numeric' ) )
{
	$catCalEvent	= new catCalendarEvent( $eventID );

	switch ( $action )
	{
		case 'getEvent':
			$success		=  $catCalEvent->getEvent(true);

			$ajax_return	= array(
				'message'	=> $lang->translate( 'Got event!' ),
				'event'		=> $success,
				'success'	=> is_array($success) && count($success) > 0 ? true : false
			);
			break;
		default:
			// =========================== 
			// ! save variant of images   
			// =========================== 
			$catCalendar->saveOptions( 'variant', CAT_Helper_Validate::getInstance()->sanitizePost('variant') );

			$ajax_return	= array(
				'message'	=> $lang->translate( 'Variant saved successfully!' ),
				'success'	=> true
			);

			break;
	}
} else {
	$backend->print_error(
		$lang->translate( 'You sent an invalid ID' ),
		CAT_ADMIN_URL . '/pages/modify.php?page_id=' . $page_id
	);
}



?>