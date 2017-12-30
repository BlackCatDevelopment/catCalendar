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

#if (!class_exists('catCalendarEvent', false))
#{
#	@include dirname(__FILE__) . '/class.catCalendarEvent.php';
#}

function catCalendarAutoload($class)
{
	$url	= explode('\\',$class);
	if ( $url[0] == 'Eluceo' )
	{
		array_shift($url);
		require_once dirname(__FILE__) . '/' . implode('/', $url) . '.php';
	}
}

spl_autoload_register('catCalendarAutoload');

if ( ! class_exists( 'catCalendarObject', false ) ) {

	class catCalendarObject
	{
		public static $directory		= 'catCalendar';
		public static $name				= 'catCalendar';
		public static $version			= '0.1';
		public static $plattform		= '1.1.x';
		public static $function			= 'page';
		public static $author			= 'Matthias Glienke, creativecat.de';
		public static $license			= '<a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License</a>';
		public static $description		= 'The add on "catCalendar" provides a simple way to integrate a calendar on your website. For details see <a href="https://github.com/BlackCatDevelopment/catCalendar_for_BlackCatCMS" target="_blank">GitHub</a>.<br/><br/>Done by Matthias Glienke, <a class="icon-creativecat" href="http://creativecat.de"> creativecat</a>';
		public static $guid				= '993b7c91-7bec-455f-9fdf-3313c0fbd3f7';

		private $section_id				= NULL;
		private static $instance		= NULL;
		private $calURL					= NULL;
		private $TZID					= NULL;
		private $summertime				= true;
		private $events					= array();
		private $calendars				= array();
		
		private $eventList				= '';

		protected $options				= array();
		protected $variant				= 'default';
		protected static $allVariants	= array();
		private static $CalObj			= NULL;
		private static $eventObj		= array();
		private static $initOptions		= array(
			'variant'			=> 'default',
			'ics-public'		=> 1
		);
	
		public function __construct( $section_id = NULL )
		{
			if ( $section_id === true )
			{
				global $section_id;
				$this->section_id	= intval( $section_id );
				self::initAdd();
			} else if ( !$section_id
					||!is_numeric($section_id)
				) global $section_id;

			$this->setSectionID( $section_id );

			self::$CalObj	= new \Eluceo\iCal\Component\Calendar('www.example.com');
			self::$eventObj[]	= new \Eluceo\iCal\Component\Event();
			self::$eventObj[]	= new \Eluceo\iCal\Component\Event();

			self::$eventObj[0]->setDtStart(new \DateTime('2012-12-24'))
				->setDtEnd(new \DateTime('2012-12-24'))
				->setNoTime(true)
				->setSummary('Christmas');

			foreach( self::$eventObj as $ics)
				self::$CalObj->addComponent($ics);
			
			#self::output();
		}
	
		public function __destruct() {}
	
		public static function getInstance()
		{
			if (!self::$instance)
				self::$instance	= new self();
			return self::$instance;
		}

		private function initAdd()
		{
			if ( !$this->getSectionID() ) return false;

			// Add a new catCalendar
			if ( CAT_Helper_Page::getInstance()->db()->query(
					'INSERT INTO `:prefix:mod_catCalendar`
						( `section_id` ) VALUES ( :secID )',
					array(
						'secID'	=> $this->getSectionID()
					)
				)
			) {
				$return	= true;

				// Add initial options for gallery
				foreach( self::$initOptions as $name => $val )
				{
					if( !$this->setOption( $name, $val ) )
						$return	= false;
				}
			}
			else return false;
			return $return;
		}
	
		public function save()
		{
			// TODO: implement here
		}
	
		public function remove()
		{
			// TODO: implement here
		}

		/**
		 * save options for catCalendar
		 *
		 * @access public
		 * @param  string			$name - name for option
		 * @param  string			$value - value for option
		 * @return bool true/false
		 *
		 **/
		public function setOption( $name = NULL, $value = '' )
		{
			if ( !$this->getSectionID()
				|| !$name
			) return false;

			if ( CAT_Helper_Page::getInstance()->db()->query(
				'REPLACE INTO `:prefix:mod_catCalendar_options` ' .
					'SET `section_id`	= :secID, ' .
						'`name`			= :name, ' .
						'`value`		= :value',
				array(
					'secID'	=> $this->getSectionID(),
					'name'	=> $name,
					'value'	=> is_null($value) ? '' : $value
				)
			) ) return $this;
			else return false;
		} // end saveOptions()


		/**
		 * get options for catCalendar
		 *
		 * @access public
		 * @param  string			$name - name for option
		 * @param  string			$value - value for option
		 * @return array()
		 *
		 **/
		public function getOption( $name = NULL )
		{
			if ( !$this->getSectionID() ) return false;

			if ( $name && isset($this->options[$name]) ) return $this->options[$name];

			$getOptions		= $name ? 
				CAT_Helper_Page::getInstance()->db()->query(
					'SELECT * FROM `:prefix:mod_catCalendar_options` ' .
						'WHERE `section_id` = :secID AND ' .
							'`name` = :name',
					array(
						'secID'	=> $this->getSectionID(),
						'name'	=> $name
					)
				) : 
				CAT_Helper_Page::getInstance()->db()->query(
					'SELECT * FROM `:prefix:mod_catCalendar_options` ' .
						'WHERE `section_id` = :secID',
					array(
						'secID'	=> $this->getSectionID()
					)
			);

			if ( isset($getOptions) && $getOptions->numRows() > 0)
			{
				while( !false == ($row = $getOptions->fetchRow( MYSQL_ASSOC ) ) )
				{
					$this->options[$row['name']]	= $row['value'];
				}
			}
			if ( $name )
			{
				if ( isset( $this->options[$name] ) )
					return $this->options[$name];
				else
					return NULL;
			}
			return $this->options;
		} // end getOptions()



		public function setVariant( $variant = '' )
		{
			$this->options['_variant']	= $variant;
			$this->saveOptions( 'variant', $variant );
		}

		/**
		 * get variant of gallery
		 *
		 * @access public
		 * @return string
		 *
		 **/
		public function getVariant()
		{
			if ( isset( $this->options['_variant'] ) )
				return $this->options['_variant'];

			$this->getOption('variant');

			$this->options['_variant']	= $this->options['variant'] != '' ? $this->options['variant'] : 'default';

			return $this->options['_variant'];
		} // getVariant()

		/**
		 * Get all available variants of an addon by checking the templates-folder
		 */
		public static function getAllVariants()
		{
			if ( count(self::$allVariants) > 0 )  return self::$allVariants;
			foreach( CAT_Helper_Directory::getInstance()->setRecursion(false)
				->scanDirectory( CAT_PATH . '/modules/' . static::$directory . '/templates/' ) as $path)
			{
				self::$allVariants[]	= basename($path);
			}
			return self::$allVariants;
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

		public function getCalendar()
		{
			if ( !$this->getSectionID() ) return false;

			$getCals	= CAT_Helper_Page::getInstance()->db()->query(
				'SELECT * FROM `:prefix:mod_catCalendar` ' .
					'WHERE `section_id` = :secID',
				array(
					'secID'	=> $this->getSectionID()
				)
			);

			$this->calendars	= array();

			if ( isset($getCals) && $getCals->numRows() > 0)
			{
				while( !false == ($row = $getCals->fetchRow( MYSQL_ASSOC ) ) )
				{
					$this->calendars[]	= $row;
				}
			}
			return $this->calendars;
		}

		public function getAllEvents()
		{
			if ( !$this->getSectionID() ) return false;

			$this->events	= array();

			$getEvents	= CAT_Helper_Page::getInstance()->db()->query(
				'SELECT *, day(start) AS sD, month(start) AS sM, year(start) AS sY FROM `:prefix:mod_catCalendar_events` ' .
					'WHERE `calID` IN ' .
						'(SELECT `calID` FROM `:prefix:mod_catCalendar` WHERE `section_id` = :secID)' . 
					'ORDER BY year(start), month(start), day(start)',
				array(
					'secID'	=> $this->getSectionID()
				)
			);
			if ( isset($getEvents) && $getEvents->numRows() > 0)
			{
				while( !false == ($row = $getEvents->fetchRow( MYSQL_ASSOC ) ) )
				{
					$this->events[$row['sD'].'.'.$row['sM'].'.'.$row['sY']][]	= array(
						'eventID'		=> $row['eventID'],
						'calID'			=> $row['calID'],
						'location'		=> $row['location'],
						'title'			=> $row['title'],
						'description'	=> $row['description'],
						'kind'			=> $row['kind'],
						'start_date'	=> strftime('%Y-%m-%d',strtotime($row['start'])),
						'start_day'		=> strftime('%d',strtotime($row['start'])),
						'start_time'	=> strftime('%H:%M',strtotime($row['start'])),
						'end_date'		=> strftime('%Y-%m-%d',strtotime($row['end'])),
						'end_day'		=> strftime('%d',strtotime($row['end'])),
						'end_time'		=> strftime('%H:%M',strtotime($row['end'])),
						'timestamp'		=> $row['timestamp'],
						'eventURL'		=> $row['eventURL'],
						'UID'			=> $row['UID'],
						'published'		=> $row['published'],
						'allday'		=> $row['allday'],
						'timestampDate'	=> strftime('%d.%m.%Y',strtotime($row['timestamp'])),
						'timestampTime'	=> strftime('%H:%M',strtotime($row['timestamp'])),
						'modifiedDate'	=> strftime('%Y-%m-%d',strtotime($row['modified'])),
						'modifiedTime'	=> strftime('%H:%M',strtotime($row['modified'])),
						'createdID'		=> CAT_Users::get_user_details($row['createdID'],'display_name'),
						'modifiedID'	=> CAT_Users::get_user_details($row['modifiedID'],'display_name')
					);
				}
			}
			return $this->events;
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
			if ( !$this->section_id ) return false;
			else return $this->section_id;
		}
	
		private function setSectionID( $section_id = NULL )
		{
			if ( !$section_id ) return false;
			$this->section_id	= intval( $section_id );
			return $this;
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
			$this->ouput();
		}

		private function output()
		{
			header_remove();
			header('Content-Type: text/calendar; charset=utf-8');
			header('Content-Disposition: attachment; filename="cal.ics"');

			echo self::$CalObj->render();
		}


		/**
		 * include headers.inc.php
		 *
		 * @access public
		 * @return string
		 *
		 **/
		public function getHeader()
		{
			if ( file_exists( CAT_PATH . '/modules/' . self::$directory .'/headers_inc/' . $this->getVariant() . '/headers.inc.php' ) )
				return CAT_PATH . '/modules/' . self::$directory . '/headers_inc/' . $this->getVariant() . '/headers.inc.php';
			elseif ( file_exists( CAT_PATH . '/modules/' . self::$directory .'/headers_inc/default/headers.inc.php' ) )
				return CAT_PATH . '/modules/' . self::$directory .'/headers_inc/default/headers.inc.php';
		}
	}
}
?>