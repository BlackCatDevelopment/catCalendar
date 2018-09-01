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
		private static $staticVars	= array( 'staticVars', 'modified', 'eventID', 'timestamp', 'instance' );

		private $calID				= NULL;
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
		private $allday				= NULL;
		private $modified			= NULL;
		private $createdID			= NULL;
		private $modifiedID			= NULL;
	
	
		public function __construct(int $eventID = NULL)
		{
			if ( $eventID === true )
			{
				self::initAdd();
			}
			# if no $eventID is given, try to get the global
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

		/**
		 * Set the private property eventID
		 *
		 * @access private
		 * @param  integer	$eventID	- ID of the event object
		 * @return object
		 *
		 **/
		private function setEventID($eventID=NULL)
		{
			if(!is_numeric($eventID)) return false;
			else $this->eventID	= intval($eventID);
			return $this;
		}	// setEventID

		/**
		 * Get the private property eventID
		 *
		 * @access private
		 * @return integer
		 *
		 **/
		private function getEventID()
		{
			return $this->eventID;
		}	// getEventID

		/**
		 * Save seo URL to database
		 *
		 * @access private
		 * @return integer
		 *
		 **/
		private function geteventURL()
		{
			if ( !$this->getEventID() ) return false;

			$getURL	= CAT_Helper_Page::getInstance()->db()->query(
				'SELECT `URL`, `newURL` FROM `:prefix:mod_catCalendarURL` ' .
					'WHERE `eventID` = :eventID AND `newURL` = ""',
				array(
					'eventID'	=> $this->getEventID()
				)
			);

			if( ( isset($getURL) && $getURL->numRows() > 0 )
				&& !false == ($row = $getURL->fetchRow() ) )
			{
				$this->setProperty( 'eventURL', $row['URL']);
			} else return NULL;
		}	// geteventURL

		/**
		 * Save seo URL to database
		 *
		 * @access private
		 * @return integer
		 *
		 **/
		private function setEventURL()
		{
			if ( !$this->getEventID() ) return false;

			if ( !$this->geteventURL() )
			{
				$this->setProperty(
					'eventURL',
					self::createTitleURL( $this->getProperty('title') )
				);
				$this->setProperty( 'newURL', '');
			}

			if ( CAT_Helper_Page::getInstance()->db()->query(
				'REPLACE `:prefix:mod_catCalendarURL` ' .
					'SET `eventID` = :eventID, `URL` = :URL, `newURL` = :newURL',
				array(
					'eventID'	=> $this->getEventID(),
					'URL'		=> $this->getProperty( 'eventURL' ),
					'newURL'	=> $this->getProperty( 'newURL' )
				)
			
			) ) return $this;
		}	// setEventURL

		/**
		 * save the event to database
		 * options: one single property, save by an array or save all properties having values
		 *
		 * @access public
		 * @param  array/string	$saveOne	
		 * 				- You can send an array to save,
		 * 				an string for one property
		 * 				or simple NULL to save all filled properties
		 * @return object
		 *
		 **/
		public function save( $saveOne = NULL )
		{
			if ( !$this->getEventID() ) return false;

			if( $this->getEventID() == -1 )
			{
				$saveProp	= array();
				$saveVal	= array();

				foreach( get_object_vars($this) as $key => $val )
				{
					if( $key == 'eventURL' && $val == '' ){
						$val	= self::createTitleURL( self::getProperty('title') );
					}
					if( !is_null( $val ) && !in_array($key,self::$staticVars) )
					{
						$saveProp[]		.= '`' . trim($key) . '`';
						$saveVal[$key]	 = "'".$val."'";
					}
				}
				if ( CAT_Helper_Page::getInstance()->db()->query(
					'INSERT INTO `:prefix:mod_catCalendar_events` ' .
						'( ' . implode(',', $saveProp) . ',`createdID`,`modifiedID`, `UID` ) VALUES ' .
						'( ' . implode(',', $saveVal) . ', :userID, :userID, :uid )',
						array(
							'userID'	=> CAT_Users::get_user_id(),
							'uid'		=> uniqid()
						)
				) ) return $this;
				else return false;
			} elseif ( !$saveOne )
			{
				$saveProp	= array();
				$saveVal	= array( 'eventID' => $this->getEventID() );

				foreach( get_object_vars($this) as $key => $val )
				{
					if( $key == 'eventURL' && $val == '' ){
						$val	= self::createTitleURL( self::getProperty('title') );
					}
					if( !is_null( $val ) && !in_array($key,self::$staticVars) )
					{
						$saveProp[]		.= '`' . $key . '` = :' . $key . ' ';
						$saveVal[$key]	 = $val;
					}
				}
				if ( CAT_Helper_Page::getInstance()->db()->query(
					'UPDATE `:prefix:mod_catCalendar_events`' .
						' SET ' . implode(',', $saveProp) . 
						' WHERE `eventID` = :eventID',
					$saveVal
				) ) return $this;
				else return false;
			}


			elseif ( is_array($saveOne) )
			{
				$saveProp	= array();
				$saveVal	= array( 'eventID' => $this->getEventID() );

				foreach( $saveOne as $key => $val )
				{
					if ( property_exists( 'catCalendarEvent', $key ) && !in_array($key,self::$staticVars) )
					{
						$saveProp[]		.= '`' . $key . '` = :' . $key . ' ';
						$saveVal[$key]	 = $val;
					}
				}

				if ( CAT_Helper_Page::getInstance()->db()->query(
					'UPDATE `:prefix:mod_catCalendar_events`' .
						' SET ' . implode(',', $saveProp) . 
						' WHERE `eventID` = :eventID',
					$saveVal
				) ) return $this;
				else return false;
			}


			elseif ( property_exists( 'catCalendarEvent', $saveOne )
				&& CAT_Helper_Page::getInstance()->db()->query(
				'UPDATE `:prefix:mod_catCalendar_events`' .
					' SET `' . $saveOne . '` = :val' .
					' WHERE `eventID`		= :eventID',
				array(
						'eventID'		=> $this->getEventID(),
						'val'			=> $this->getProperty( $saveOne )
				)
			) ) return $this;

			else return false;
		}


		/**
		 * Remove an event from database
		 *
		 * @access public
		 * @return bool
		 *
		 **/
		public function deleteEvent()
		{
			if ( !$this->getEventID() ) return false;
			return CAT_Helper_Page::getInstance()->db()->query(
				'DELETE FROM `:prefix:mod_catCalendar_events` ' .
					'WHERE `eventID` = :eventID',
				array(
					'eventID'	=> $this->getEventID()
			) );
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

		/**
		 * Fill the object with the values of an event from database
		 *
		 * @access public
		 * @param  bool		$returnArray	- option whether an array should be returned
		 * @return object/array
		 *
		 **/
		public static function getEventByUrl( string $url = '' )
		{
			if ($url == '') return false;

			$getEvent	= CAT_Helper_Page::getInstance()->db()->query(
				'SELECT  * FROM `:prefix:mod_catCalendar_events` ' .
					'WHERE `eventURL` = :url',
				array(
					'url'	=> $url
				)
			);

			if( ( isset($getEvent) && $getEvent->numRows() > 0 )
				&& !false == ($row = $getEvent->fetchRow() ) )
			{
				$start		= DateTime::createFromFormat("Y-m-d H:i:s", $row['start']);
				$end		= DateTime::createFromFormat("Y-m-d H:i:s", $row['end']);
				$timestamp	= DateTime::createFromFormat("Y-m-d H:i:s", $row['timestamp']);
				$modified	= DateTime::createFromFormat("Y-m-d H:i:s", $row['modified']);

				return array(
					'eventID'		=> $row['eventID'],
					'calID'			=> $row['calID'],
					'location'		=> $row['location'],
					'title'			=> $row['title'],
					'description'	=> $row['description'],
					'kind'			=> $row['kind'],
					'start'			=> $start->format('U'),
					'start_date'	=> $start->format('Y-m-d'),
					'start_day'		=> $start->format('d'),
					'start_time'	=> $start->format('H:i'),
					'end'			=> $end->format('U'),
					'end_date'		=> $end->format('Y-m-d'),
					'end_day'		=> $end->format('d'),
					'end_time'		=> $end->format('H:i'),
					'timestamp'		=> $row['timestamp'],
					'eventURL'		=> $row['eventURL'],
					'UID'			=> $row['UID'],
					'published'		=> $row['published'],
					'allday'		=> $row['allday'],
					'timestampDate'	=> $timestamp->format('d.m.Y'),
					'timestampTime'	=> $timestamp->format('H:i'),
					'modifiedDate'	=> $modified->format('d.m.Y'),
					'modifiedTime'	=> $modified->format('H:i'),
					'createdID'		=> CAT_Users::get_user_details( $row['createdID'], 'display_name' ),
					'modifiedID'	=> CAT_Users::get_user_details( $row['modifiedID'], 'display_name' )
				);
			} else return NULL;
		}

		/**
		 * Fill the object with the values of an event from database
		 *
		 * @access public
		 * @param  bool		$returnArray	- option whether an array should be returned
		 * @return object/array
		 *
		 **/
		public function getEvent( $returnArray = NULL )
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
				$this->setProperty( 'modified',		$row['modified']);
				$this->setProperty( 'createdID',	$row['createdID']);
				$this->setProperty( 'modifiedID',	$row['modifiedID']);
			}

			if ( $returnArray ) return $this->createReturnArray();
			else return $this;
		}

		/**
		 * create the array for callback if needed
		 *
		 * @access private
		 * @return array
		 *
		 **/
		private function createReturnArray()
		{
			return array(
				'calID'			=> $this->getProperty('calID'),
				'location'		=> $this->getProperty('location'),
				'title'			=> $this->getProperty('title'),
				'description'	=> $this->getProperty('description'),
				'kind'			=> $this->getProperty('kind'),
				'start_date'	=> $this->getDateTimeInput('start'),
				'start_day'		=> $this->getDateTimeInput('start','%d'),
				'start_time'	=> $this->getDateTimeInput('start','%H:%M'),
				'end_date'		=> $this->getDateTimeInput('end'),
				'end_day'		=> $this->getDateTimeInput('end','%d'),
				'end_time'		=> $this->getDateTimeInput('end','%H:%M'),
				'timestamp'		=> $this->getProperty('timestamp'),
				'eventURL'		=> $this->getProperty('eventURL'),
				'UID'			=> $this->getProperty('UID'),
				'published'		=> $this->getProperty('published'),
				'allday'		=> $this->getProperty('allday'),
				'timestampDate'	=> $this->getDateTimeInput('timestamp','%d.%m.%Y'),
				'timestampTime'	=> $this->getDateTimeInput('timestamp','%H:%M'),
				'modifiedDate'	=> $this->getDateTimeInput('modified'),
				'modifiedTime'	=> $this->getDateTimeInput('modified','%H:%M'),
				'createdID'		=> CAT_Users::get_user_details( $this->getProperty('createdID'), 'display_name' ),
				'modifiedID'	=> CAT_Users::get_user_details( $this->getProperty('modifiedID'), 'display_name' )
			);
		}

		/**
		 * Prepare a valid string from a property for input:date
		 *
		 * @access private
		 * @param  string	$prop	- property which should be converted
		 * @param  string	$format	- output format
		 * @return string
		 *
		 **/
		private function getDateTimeInput($prop=NULL,$format='%Y-%m-%d')
		{
			if (!$this->getProperty($prop)) return false;
			return strftime($format, strtotime($this->getProperty($prop)) );
		}

		/**
		 * Prepare a valid string from a property for DateTime in SQL
		 *
		 * @access private
		 * @param  string	$prop	- property which should be converted
		 * @return string
		 *
		 **/
		private function getDateTimeSQL($prop=NULL)
		{
			if (!$this->getProperty($prop)) return false;
			return strftime('%Y-%m-%d %H:%M:00', strtotime($this->getProperty($prop)));
		}



		/**
		 * Store a value to a property of an object
		 *
		 * @access public
		 * @param  string	$key	- attribute of class, that should be set
		 * @param  string	$value	- value for the attribute
		 * @return object
		 *
		 **/
		public function setProperty( $key = NULL, $value = NULL )
		{
			if ( !$this->getEventID()
				|| !property_exists( 'catCalendarEvent', $key )
				|| in_array($key,self::$staticVars)
			) return false;
			else {
				$this->$key	= $value;
				return $this;
			}
		}

		/**
		 * Get a value of a property of an object
		 *
		 * @access public
		 * @param  string	$key	- attribute of class, that should be got
		 * @return string
		 *
		 **/
		public function getProperty( $key = NULL )
		{
			if ( !$this->getEventID()
				|| !property_exists( 'catCalendarEvent', $key )
				|| in_array($key,self::$staticVars)
			) return false;
			else return $this->$key;

/*				'INSERT INTO `:prefix:mod_blacknews_content`
						(`page_id`, `section_id`, `news_id`, `title`, `subtitle`, `auto_generate_size`, `auto_generate` , `content`, `short`)
						VALUES (:page_id, :section_id, :news_id, :title, :subtitle, :auto_generate_size, :auto_generate, :content, :short )';*/
		}


		public function setEvent( $setArray = NULL )
		{
			foreach( $setArray as $i => $set )
			{
				$this->setProperty( $i, $set );
			}
			$this->addEvent();
		}

		/**
		 * add an event
		 *
		 * @access public
		 * @return object
		 *
		 **/
		private function addEvent()
		{

		} // end addEvent()


		/**
		 * copy an event
		 *
		 * @access public
		 * @return object
		 *
		 **/
		public function copyEvent()
		{
			if ( !$this->getEventID() ) return false;

			return $this;
		} // end publishEvent()


		/**
		 * (un)publish single column
		 *
		 * @access public
		 * @return bool
		 *
		 **/
		public function publishEvent()
		{
			if ( !$this->getEventID() ) return false;

			CAT_Helper_Page::getInstance()->db()->query(
				'UPDATE `:prefix:mod_catCalendar_events` ' .
					' SET `published` = 1 - `published` ' .
					'WHERE `eventID` = :eventID',
				array(
					'eventID'	=> $this->getEventID()
				)
			);

			$this->setProperty(
				'published',
				CAT_Helper_Page::getInstance()->db()->query(
					'SELECT `published` FROM `:prefix:mod_catCalendar_events` ' .
						'WHERE `eventID` = :eventID',
					array(
						'eventID'	=> $this->getEventID()
					)
				)->fetchColumn()
			);

			return $this->getProperty('published');
		} // end publishEvent()
	
		public function getCalEventURL()
		{
			// TODO: implement here
		}
	
		private function setCalEventURL()
		{
			// TODO: implement here
		}

		/**
		 * create TitleURL
		 *
		 * @access public
		 * @param  string  $title
		 * @return string
		 *
		 **/
		public static function createTitleURL( $title = NULL )
		{
			if ( !$title ) return false;

			return CAT_Helper_Page::getFilename( $title );
		} // end createTitleURL()
	}
}