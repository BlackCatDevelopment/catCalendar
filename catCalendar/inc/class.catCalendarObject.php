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

if ( ! class_exists( 'catCalendar', false ) ) {
class catCalendarObject
{

	private $sectionID				= NULL;
	private static $instance		= NULL;
	private $calURL					= NULL;
	private $TZID					= NULL;
	private $summertime				= true;
	private $events					= array();
	protected $options				= array();
	protected static $directory		= 'catCalendar';
	protected $variant				= 'default';
	protected static $allVariants	= array();
	private static $classICS		= NULL;
	private static $initOptions		= array(
		'variant'		=> 'default'
	);

	public function __construct($section_id = NULL)
	{
		$vCalendar = new \iCal\Component\Calendar('www.example.com');
		// TODO: implement here
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

	private function initAdd()
	{
		// TODO: implement here
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

	public function setVariant()
	{
		// TODO: implement here
	}

	public function getVariant()
	{
		// TODO: implement here
	}

	public function getAllVariants()
	{
		// TODO: implement here
	}

	public function setEvent()
	{
		// TODO: implement here
	}

	private function addEvent()
	{
		// TODO: implement here
	}

	public function getEvent()
	{
		// TODO: implement here
	}

	public function getAllEvents()
	{
		// TODO: implement here
	}

	private function getSummerTime()
	{
		// TODO: implement here
	}

	private function setSummerTime()
	{
		// TODO: implement here
	}

	private function getSectionID()
	{
		// TODO: implement here
	}

	private function setSectionID()
	{
		// TODO: implement here
	}

	private function getCalURL()
	{
		// TODO: implement here
	}

	private function setCalURL()
	{
		// TODO: implement here
	}

	private function createURL()
	{
		// TODO: implement here
	}

	public function getICS()
	{
		// TODO: implement here
	}

	private function createICS()
	{
		// TODO: implement here
	}
}

}
?>