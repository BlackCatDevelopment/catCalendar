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

if (!class_exists('catCalendarObject', false))
{
	@include dirname(__FILE__) . '/class.catCalendarObject.php';
}

if ( ! class_exists( 'catCalendarEvent', false ) ) {

	class catCalendarEvent
	{
	
		public $eventID				= NULL;
		private $sectionID			= NULL;
		private static $instance	= NULL;
		private $location			= NULL;
		private $title				= NULL;
		private $description		= NULL;
		private $kind				= NULL;
		private $start				= NULL;
		private $end				= NULL;
		private $timestamp			= NULL;
		private $eventURL			= NULL;
		private $UID				= NULL;
		private $published			= NULL;
		private $fullDay			= NULL;
		private $modified			= NULL;
		private $createdID			= NULL;
		private $modifiedID			= NULL;
	
	
		public function __construct($section_id = NULL)
		{
			if ( $section_id === true )
			{
				self::initAdd();
			}
			# if no $section_id is given, try to get the global
			if ( !$section_id
				||!is_numeric($section_id)
				) global $section_id;
			
			
		}
	
		public function __destruct()
		{
			// TODO: implement here
		}
	
		public static function getInstance()
		{
			if (!self::$instance)
				self::$instance	= new self();
			return self::$instance;
		}
	
		public function save()
		{
			// TODO: implement here
		}
	
		public function remove()
		{
			// TODO: implement here
		}
	
		public function setOption()
		{
			// TODO: implement here
		}
	
		public function getOption()
		{
			// TODO: implement here
		}
	
		public function getEvent()
		{
			// TODO: implement here
		}
	
		public function setEvent()
		{
			// TODO: implement here
		}
	
		private function publishEvent()
		{
			// TODO: implement here
		}
	
		public function getCalEventURL()
		{
			// TODO: implement here
		}
	
		private function setCalEventURL()
		{
			// TODO: implement here
		}
	}
}