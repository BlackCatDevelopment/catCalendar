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
	
		private $eventID			= NULL;
		private static $instance	= NULL;

		private $calID				= NULL;
		private $location			= NULL;
		private $title				= NULL;
		private $description		= '';
		private $kind				= NULL;
		private $start				= NULL;
		private $end				= NULL;
		private $timestamp			= NULL;
		private $eventURL			= NULL;
		private $UID				= NULL;
		private $published			= NULL;
		private $allday				= NULL;
		private $modified			= NULL;
		private $createdID			= NULL;
		private $modifiedID			= NULL;
	
	
		public function __construct($eventID = NULL)
		{
			if ( $eventID === true )
			{
				self::initAdd();
			}
			# if no $section_id is given, try to get the global
			if ( !$eventID ) return false;
			else return $this->setEventID($eventID);
		}
	
		public function __destruct() {}
	
		public static function getInstance()
		{
			if (!self::$instance)
				self::$instance	= new self();
			return self::$instance;
		}

		private function setEventID($eventID=NULL)
		{
			if(!is_numeric($eventID)) return false;
			else $this->eventID	= $eventID;
			return $this;
		}

		private function getEventID()
		{
			return $this->eventID;
		}


		public function save( $saveOne = NULL )
		{
			if ( !$this->getEventID() ) return false;

			if ( !$saveAll && CAT_Helper_Page::getInstance()->db()->query(
				'UPDATE `:prefix:mod_catCalendar_events`' .
					' SET `calID`			= :calID' .
					' WHERE `eventID`		= :eventID',
				array(
						'eventID'		=> $this->getEventID(),
						'calID'			=> $this->getProperty( 'calID' ),
						'location'		=> $this->getProperty( 'location' ),
						'title'			=> $this->getProperty( 'title' ),
						'description'	=> $this->getProperty( 'description' ),
						'kind'			=> $this->getProperty( 'kind' ),
						'start'			=> $this->getProperty( 'start' ),
						'end'			=> $this->getProperty( 'end' ),
#						'timestamp'		=> $this->getProperty( 'timestamp' ),
						'eventURL'		=> $this->getProperty( 'eventURL' ),
						'UID'			=> $this->getProperty( 'UID' ),
#						'published'		=> $this->getProperty( 'published' ),
						'allday'		=> $this->getProperty( 'allday' ),
						'modified'		=> $this->getProperty( 'modified' ),
#						'createdID'		=> $this->getProperty( 'createdID' ),
						'modifiedID'	=> $this->getProperty( 'modifiedID' )
			) )
			) return $this;


			elseif ( property_exists( 'catCalendarEvent', $saveOne )
				&& CAT_Helper_Page::getInstance()->db()->query(
				'UPDATE `:prefix:mod_catCalendar_events`' .
					' SET `' . $saveOne . '` = :val' .
					' WHERE `eventID`		= :eventID',
				array(
						'eventID'		=> $this->getEventID(),
						'val'			=> $this->getProperty( $saveOne )
/*						'location'		=> $this->getProperty( 'location' ),
						'title'			=> $this->getProperty( 'title' ),
						'description'	=> $this->getProperty( 'description' ),
						'kind'			=> $this->getProperty( 'kind' ),
						'start'			=> $this->getProperty( 'start' ),
						'end'			=> $this->getProperty( 'end' ),
#						'timestamp'		=> $this->getProperty( 'timestamp' ),
						'eventURL'		=> $this->getProperty( 'eventURL' ),
						'UID'			=> $this->getProperty( 'UID' ),
#						'published'		=> $this->getProperty( 'published' ),
						'allday'		=> $this->getProperty( 'allday' ),
						'modified'		=> $this->getProperty( 'modified' ),
#						'createdID'		=> $this->getProperty( 'createdID' ),
						'modifiedID'	=> $this->getProperty( 'modifiedID' )*/
			) )
			) return $this;


			else return false;
		}
	
		public function remove()
		{
			// TODO: implement here
		}

/*		public function setOption()
		{
			// TODO: implement here
		}
	
		public function getOption()
		{
			// TODO: implement here
		}
*/
		public function getEvent( $return = NULL )
		{
			$getEvent	= CAT_Helper_Page::getInstance()->db()->query(
				'SELECT * FROM `:prefix:mod_catCalendar_events` ' .
					'WHERE `eventID` = :eventID',
				array(
					'eventID'	=> $this->getEventID()
				)
			);

			if( ( isset($getEvent) && $getEvent->numRows() > 0 )
				&& !false == ($row = $getEvent->fetchRow() ) )
			{
				$this->setProperty( 'calID',		$row['calID']);
				$this->setProperty( 'location',		$row['location']);
				$this->setProperty( 'title',		$row['title']);
				$this->setProperty( 'description',	$row['description']);
				$this->setProperty( 'kind',			$row['kind']);
				$this->setProperty( 'start',		$row['start']);
				$this->setProperty( 'end',			$row['end']);
				$this->setProperty( 'timestamp',	$row['timestamp']);
				$this->setProperty( 'eventURL',		$row['eventURL']);
				$this->setProperty( 'UID',			$row['UID']);
				$this->setProperty( 'published',	$row['published']);
				$this->setProperty( 'allday',		$row['allday']);
				$this->setProperty( 'modified',		$row['timestamp']);
				$this->setProperty( 'createdID',	$row['createdID']);
				$this->setProperty( 'modifiedID',	$row['modifiedID']);
			}

			if ( $return ) return $this->createArray();
			else return $this;
		}


		private function createArray()
		{
			return array(
				'calendar'		=> $this->getProperty('calID'),
				'location'		=> $this->getProperty('location'),
				'title'			=> $this->getProperty('title'),
				'description'	=> $this->getProperty('description'),
				'kind'			=> $this->getProperty('kind'),
				'start_date'	=> strftime('%Y-%m-%d',	strtotime($this->getProperty('start')) ),
				'start_day'		=> strftime('%d',		strtotime($this->getProperty('start')) ),
				'start_time'	=> strftime('%H:%M',	strtotime($this->getProperty('start')) ),
				'end_date'		=> strftime('%Y-%m-%d',	strtotime($this->getProperty('end')) ),
				'end_day'		=> strftime('%d',		strtotime($this->getProperty('end')) ),
				'end_time'		=> strftime('%H:%M',	strtotime($this->getProperty('end')) ),
				'timestamp'		=> $this->getProperty('timestamp'),
				'eventURL'		=> $this->getProperty('eventURL'),
				'UID'			=> $this->getProperty('UID'),
				'published'		=> $this->getProperty('published'),
				'allday'		=> $this->getProperty('allday'),
				'timestampDate'	=> strftime('%d.%m.%Y',	strtotime($this->getProperty('timestamp')) ),
				'timestampTime'	=> strftime('%H:%M',	strtotime($this->getProperty('timestamp')) ),
				'modifiedDate'	=> strftime('%Y-%m-%d',	strtotime($this->getProperty('timestamp')) ),
				'modifiedTime'	=> strftime('%H:%M',	strtotime($this->getProperty('timestamp')) ),
				'createdID'		=> CAT_Users::get_user_details( $this->getProperty('createdID'), 'display_name' ),
				'modifiedID'	=> CAT_Users::get_user_details( $this->getProperty('modifiedID'), 'display_name' )
			);
		}


		public function setProperty( $key = NULL, $value = NULL )
		{
			if ( !$this->getEventID()
				|| !property_exists( 'catCalendarEvent', $key )
			) return false;
			else {
				$this->$key	= $value;
				return $this;
			}
		}

		public function getProperty( $key = NULL )
		{
			if ( !$this->getEventID()
				|| !property_exists( 'catCalendarEvent', $key )
			) return false;
			else return $this->$key;

/*				'INSERT INTO `:prefix:mod_blacknews_content`
						(`page_id`, `section_id`, `news_id`, `title`, `subtitle`, `auto_generate_size`, `auto_generate` , `content`, `short`)
						VALUES (:page_id, :section_id, :news_id, :title, :subtitle, :auto_generate_size, :auto_generate, :content, :short )';*/
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