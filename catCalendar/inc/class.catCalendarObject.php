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

function catCalendarAutoload( $class )
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

	if (!class_exists('CAT_Addon_Page', false))
	{
		include(dirname(__FILE__) . '/../Engine/CAT_Addons/CAT_Addon_Page.php');
	}

	class catCalendarObject extends CAT_Addon_Page
	{
		/**
		 * @var void
		 */
		protected static	$instance		= NULL;

		protected static	$template		= 'modify';
		private static		$SEOUrl;
		private static		$categories;
		protected static	$options		= array();
		private static		$routeUrl		= NULL;
		private static		$routeQuery		= NULL;
		private static		$parsed			= NULL;

		/**
		 * @var void
		 */
		protected static $name			= 'catCalendar';
		protected static $directory		= 'catCalendar';
		protected static $version		= '0.1';
		protected static $author		= 'Matthias Glienke, creativecat';
		protected static $license		= '<a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License</a>';
		protected static $description	= 'The add on "catCalendar" provides a simple way to integrate a calendar on your website. For details see <a href="https://github.com/BlackCatDevelopment/catCalendar_for_BlackCatCMS" target="_blank">GitHub</a>.<br/><br/>Done by Matthias Glienke, <a class="icon-creativecat" href="http://creativecat.de"> creativecat</a>';
		protected static $guid			= '993b7c91-7bec-455f-9fdf-3313c0fbd3f7';
		protected static $home			= 'https://creativecat.de';
		protected static $platform		= '1.x';


		private $calURL					= NULL;
		private $TZID					= NULL;
		private $summertime				= true;
		private static $events			= array();
		private static $eventsByDay		= array();
		
		private static $calendars		= array();
		private static $locale			= 'de_DE';
		
		
		private $eventList				= '';
		private static $month			= array();

		protected static $allVariants	= array();
		private static $CalObj			= NULL;
		private static $eventObj		= array();
		private static $initOptions		= array(
			'variant'			=> 'default',
			'permalink'			=> 'kalender',
			'ics-public'		=> 1
		);

		public function __construct( int $section_id = NULL )
		{
			if ( $section_id === true )
			{
				global $section_id;
				self::$section_id	= intval( $section_id );
				self::initAdd();
			} else if ( !$section_id
					||!is_numeric($section_id)
				) global $section_id;

			self::setSectionID( $section_id );
			self::getVariant();

		/*	self::$CalObj	= new \Eluceo\iCal\Component\Calendar('www.example.com');
			self::$eventObj[]	= new \Eluceo\iCal\Component\Event();
			self::$eventObj[]	= new \Eluceo\iCal\Component\Event();

			self::$eventObj[0]->setDtStart(new \DateTime('2012-12-24'))
				->setDtEnd(new \DateTime('2012-12-24'))
				->setNoTime(true)
				->setSummary('Christmas');

			foreach( self::$eventObj as $ics)
				self::$CalObj->addComponent($ics);
			*/
			#self::output();
		}
		public function __destruct()
		{
			parent::__destruct();
		}

		public static function getInstance()
		{
			if (!self::$instance)
			{
				self::$instance = new self();
			}
			return self::$instance;
		}



		private static function initAdd()
		{
			if ( !self::getSectionID() ) return false;

			// Add a new catCalendar
			if ( CAT_Helper_Page::getInstance()->db()->query(
					'INSERT INTO `:prefix:mod_catCalendar`
						( `section_id` ) VALUES ( :secID )',
					array(
						'secID'	=> self::getSectionID()
					)
				)
			) {
				$return	= true;

				// Add initial options for gallery
				foreach( self::$initOptions as $name => $val )
				{
					if( !self::setOption( $name, $val ) )
						$return	= false;
				}
			}
			else return false;
			return $return;
		}
	
		/**
		 *
		 */
		public static function save()
		{
			if ( CAT_Helper_Validate::sanitizePost('_cat_ajax','numeric') == 1 )
			{
				header('Content-type: application/json');
				$backend	= CAT_Backend::getInstance('Pages', 'pages_modify', false);
			} else
				$backend	= CAT_Backend::getInstance('Pages', 'pages_modify');

			CAT_Helper_I18n::getInstance()->addFile(
				CAT_Helper_I18n::getInstance()->getLang().'.php',
				CAT_PATH  . '/modules/' . static::$directory . '/languages/'
			);

			$backend->updateWhenModified();

			$action		= CAT_Helper_Validate::sanitizePost('action');
			$return		= array();
			self::setIDs();

			if ( CAT_Helper_Page::getPagePermission( self::$page_id, 'admin' ) !== true )
			{
				$backend->print_error( 'You do not have permissions to modify this page!' );
			}


			/*
				This should be included later
			if ( file_exists( $savePHPpath . self::getVariant() . '/save.php' ) )
				include_once( $savePHPpath . self::getVariant() . '/save.php' );
			elseif ( file_exists( $savePHPpath .'save/default/save.php' ) )
				include_once( $savePHPpath .'save/default/save.php' );
			*/
			if ( $calID = CAT_Helper_Validate::getInstance()->sanitizePost( 'calid','numeric' ) )
			{
				switch ( $action )
				{
					case 'getEvents':
						$success		= self::getAllEvents( $calID != -1 ? $calID : NULL, true );
			
						$return	= array(
							'message'	=> CAT_Helper_I18n::getInstance()->translate( 'Calendar loaded successfully' ),
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
								if( !self::setOptions( $option, CAT_Helper_Validate::getInstance()->sanitizePost( $option ) )) $error = true;
							}
						}
						$return	= array(
							'message'	=> CAT_Helper_I18n::getInstance()->translate( 'Options saved successfully!' ),
							'success'	=> true
						);
						break;
					default:
						// =========================== 
						// ! save variant of catCalendar   
						// =========================== 
						self::setOptions( 'variant', CAT_Helper_Validate::getInstance()->sanitizePost('variant') );
			
						$return	= array(
							'message'	=> CAT_Helper_I18n::getInstance()->translate( 'Variant saved successfully!' ),
							'success'	=> true
						);
			
						break;
				}
			} elseif ( $eventID = CAT_Helper_Validate::getInstance()->sanitizePost( 'eventid','numeric' ) )
			{
				$catCalEvent	= new catCalendarEvent( intval($eventID) );

				switch ( $action )
				{
					case 'deleteEvent':
						$success		= $catCalEvent->deleteEvent();
			
						$return	= array(
							'message'	=> CAT_Helper_I18n::getInstance()->translate( 'Event deleted successfully' ),
							'success'	=> $success ? true : false
						);
						break;

					case 'getEvent':
						$success		=  $catCalEvent->getEvent( true );
			
						$return	= array(
							'message'	=> CAT_Helper_I18n::getInstance()->translate( 'Event loaded successfully' ),
							'event'		=> $success,
							'success'	=> is_array($success) && count($success) > 0 ? true : false
						);
						break;
					case 'publishEvent':
						$publish	= $catCalEvent->publishEvent();

						$return	= array(
							'message'	=> $publish
									? CAT_Helper_I18n::getInstance()->translate( 'Event published successfully' )
									: CAT_Helper_I18n::getInstance()->translate( 'Event unpublished successfully' ),
							'event'		=> $publish,
							'success'	=> !is_null($publish) ? true : false
						);
						break;
					default:
						// =========================== 
						// ! save event
						// =========================== 
						$start	= array();
						$end	= array();
						foreach( CAT_Helper_Validate::getInstance()->sanitizePost('event','array') as $key => $val )
						{
							switch($key)
							{
								case 'start_date':
									$start[0] = $val;
									break;
								case 'start_time':
									$start[1] = $val;
									break;
								case 'end_date':
									$end[0] = $val;
									break;
								case 'end_time':
									$end[1] = $val;
									break;
								case 'allday':
									$catCalEvent->setProperty( $key, $val !== 'false' ? 1 : 0 );
									break;
								default:
									$catCalEvent->setProperty( $key, $val );
									break;
							}
						}

						$test	= $catCalEvent->setProperty( 'start', implode(' ', $start) )->setProperty( 'end', implode(' ', $end) )->save();

						$return	= array(
							'message'	=> CAT_Helper_I18n::getInstance()->translate( 'Event saved successfully!' ),
							'shit'		=> $test,
							'success'	=> true
						);
			
						break;
				}
			} elseif ( $eventID = CAT_Helper_Validate::getInstance()->sanitizePost( 'calName' ) )
			{
				$success	= self::addCalender( 'calName' );
			
				$return	= array(
					'message'	=> CAT_Helper_I18n::getInstance()->translate( 'Event saved successfully!' ),
					'calName'	=> $success,
					'success'	=> $success ? true : false
				);
			} else {
				$return	= array(
					'message'	=> CAT_Helper_I18n::getInstance()->translate( 'You sent an invalid ID' ),
					'success'	=> false
				);
			}


			if( CAT_Helper_Validate::sanitizePost('_cat_ajax') == 1 )
			{
				print json_encode( $return );
				exit();
			} else {
				$backend->print_success(
					isset($return['message']) ? $return['message'] : $backend->lang()->translate('Saved successfully'),
					CAT_ADMIN_URL . '/pages/modify.php?page_id=' . self::$page_id
				);
				// Print admin footer
				$backend->print_footer();
			}
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
		public static function setOption( string $name = NULL, string $value = '' )
		{
			if ( !self::getSectionID()
				|| !$name
			) return false;

			if ( CAT_Helper_Page::getInstance()->db()->query(
				'REPLACE INTO `:prefix:mod_catCalendar_options` ' .
					'SET `section_id`	= :secID, ' .
						'`name`			= :name, ' .
						'`value`		= :value',
				array(
					'secID'	=> self::getSectionID(),
					'name'	=> $name,
					'value'	=> is_null($value) ? '' : $value
				)
			) ) return $this;
			else return false;
		} // end setOptions()


		/**
		 * get options for catCalendar
		 *
		 * @access public
		 * @param  string			$name - name for option
		 * @param  string			$value - value for option
		 * @return array()
		 *
		 **/
		public static function getOption( string $name = NULL )
		{
			if ( !self::getSectionID() ) return false;

			if ( $name && isset(self::$options[$name]) ) return self::$options[$name];

			$getOptions		= $name ? 
				CAT_Helper_Page::getInstance()->db()->query(
					'SELECT * FROM `:prefix:mod_catCalendar_options` ' .
						'WHERE `section_id` = :secID AND ' .
							'`name` = :name',
					array(
						'secID'	=> self::getSectionID(),
						'name'	=> $name
					)
				) : 
				CAT_Helper_Page::getInstance()->db()->query(
					'SELECT * FROM `:prefix:mod_catCalendar_options` ' .
						'WHERE `section_id` = :secID',
					array(
						'secID'	=> self::getSectionID()
					)
			);

			if ( isset($getOptions) && $getOptions->numRows() > 0)
			{
				while( !false == ($row = $getOptions->fetchRow( MYSQL_ASSOC ) ) )
				{
					self::$options[$row['name']]	= $row['value'];
				}

			}
			if ( $name )
			{
				if ( isset( self::$options[$name] ) )
					return self::$options[$name];
				else
					return NULL;
			}
			return self::$options;
		} // end getOptions()


		public function addCalender( string $calName = NULL )
		{
			if ( !self::getSectionID() ) return false;

			// Add a new catCalendar
			if ( CAT_Helper_Page::getInstance()->db()->query(
					'INSERT INTO `:prefix:mod_catCalendar`
						( `section_id`, `name` ) VALUES ( :secID, : :calName )',
					array(
						'secID'		=> self::getSectionID(),
						'calName'	=> $calName
					)
				)
			) {
				return array(
					'calID'	=> CAT_Helper_Page::getInstance()->db()->query(
								'SELECT `calID` FROM `:prefix:mod_catCalendar` ' .
									'WHERE `section_id` = :secID ' .
									'AND `name` = :calName',
								array(
									'secID'		=> self::getSectionID(),
									'calName'	=> $calName
								)
							)->fetchColumn(),
					'name'	=> $calName
				);
			}
			else return false;
		}

	
		public function setEvent()
		{
			// TODO: implement here
		}
	
		private function addEvent()
		{
			// TODO: implement here
		}
	
		/**
		 *
		 */
		private static function getEvent()
		{
			// TODO: Check if route is in database, else return 404
			// TODO: Add route to extra table with trigger to get history of files and automatically set 301

			// get query from url and eplxode it
			$q	= explode( '/', trim( str_replace(self::getOption('permalink') . '/','',self::$routeUrl), '/' ) );

			// Only if the given query is JJJJ/MM 
			if ( count($q) == 2 && ( strlen($q[0])==4 && strlen($q[1])==2 ) )
			{
				$d1		= DateTime::createFromFormat("Y-m-d", $q[0] . '-' . $q[1] . '-01');
				$d2		= DateTime::createFromFormat("Y-m-d", $q[0] . '-' . $q[1] . '-31');

				// If the given month is the current one, then set current day as active
				if ($d1->format('m') == date('n')) self::setParserValue('activeDay',date('d'));

				self::setParserValue('activeMonth',$d1->format('m'));
				self::setParserValue('activeYear',$d1->format('Y'));

				self::setParserValue(
					'dates',
					self::getEventsInPeriod(
						$d1->format('U'),
						$d2->format('U'),
						1,
						NULL,
						true
					)
				);
				self::setParserValue(
					'cal',
					catCalendarObject::getDaysInYearMonth($d1->format('U'))
				);

				static::$template	= 'view';
			}
			else { // If the given query is an event-URL
				self::setParserValue(
					'event',
					catCalendarEvent::getEventByUrl($q[0])
				);
			}
			#return blackNewsEntry::getEntryByURL(trim( str_replace(self::getOption('permalink') . '/','',self::$routeUrl), '/' ));

		}
		public static function getCalendar()
		{
			if ( !self::getSectionID() ) return false;

			$getCals	= CAT_Helper_Page::getInstance()->db()->query(
				'SELECT * FROM `:prefix:mod_catCalendar` ' .
					'WHERE `section_id` = :secID',
				array(
					'secID'	=> self::getSectionID()
				)
			);

			self::$calendars	= array();

			if ( isset($getCals) && $getCals->numRows() > 0)
			{
				while( !false == ($row = $getCals->fetchRow( MYSQL_ASSOC ) ) )
				{
					self::$calendars[]	= $row;
				}
			}
			return self::$calendars;
		}

		
		public static function getAllEvents( int $calID = NULL, bool $output = NULL, bool $asc = NULL )
		{
			if ( !self::getSectionID() ) return false;

			self::$events	= array();

			if( is_array($calID) )
				$sqlAdd	= '';
			elseif( is_numeric($calID) )
				$sqlAdd	= ' = ' . intval( $calID );
			else $sqlAdd	= 'IN (SELECT `calID` FROM `:prefix:mod_catCalendar` WHERE `section_id` = :secID)';

			$getEvents	= CAT_Helper_Page::getInstance()->db()->query(
				'SELECT *, day(start) AS sD, month(start) AS sM, year(start) AS sY FROM `:prefix:mod_catCalendar_events` ' .
					'WHERE `calID` ' . 
					$sqlAdd .
					' ORDER BY year(start) DESC, month(start) DESC, day(start) ' . ($asc ? 'ASC' : 'DESC'),
				array(
					'secID'	=> self::getSectionID()
				)
			);
			if ( isset($getEvents) && $getEvents->numRows() > 0)
			{
				while( !false == ($row = $getEvents->fetchRow( MYSQL_ASSOC ) ) )
				{
					$start		= DateTime::createFromFormat("Y-m-d H:i:s", $row['start']);
					$end		= DateTime::createFromFormat("Y-m-d H:i:s", $row['end']);
					$timestamp	= DateTime::createFromFormat("Y-m-d H:i:s", $row['timestamp']);
					$modified	= DateTime::createFromFormat("Y-m-d H:i:s", $row['modified']);

					self::$events[$start->format('d.m.Y')][]	= array(
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
				}
			}
			if ($output) return self::getEventsList();
			else return self::$events;
		}


		
		public static function getEventsInPeriod( int $from = NULL, int $to = NULL, int $calID = NULL, bool $output = NULL, bool $asc = NULL )
		{
			if ( !self::getSectionID() ) return false;

			if( is_array($calID) )
				$sqlAdd	= '';
			elseif( is_numeric($calID) )
				$sqlAdd	= ' = ' . intval( $calID );
			else $sqlAdd	= 'IN (SELECT `calID` FROM `:prefix:mod_catCalendar` WHERE `section_id` = :secID)';

			$getEvents	= CAT_Helper_Page::getInstance()->db()->query(
				'SELECT *, day(start) AS sD, month(start) AS sM, year(start) AS sY FROM `:prefix:mod_catCalendar_events` ' .
					'WHERE `calID` ' . 
					$sqlAdd .
					' AND ( `start` BETWEEN FROM_UNIXTIME(:from) AND FROM_UNIXTIME(:to) ' .
					'OR `end` BETWEEN FROM_UNIXTIME(:from) AND FROM_UNIXTIME(:to) ) ' . 
					'OR ( `start` < FROM_UNIXTIME(:from) AND `end` > FROM_UNIXTIME(:to) ) ' . 
					'ORDER BY year(start) DESC, month(start) DESC, day(start) ' . ($asc ? 'ASC' : 'DESC'),
				array(
					'secID'	=> self::getSectionID(),
					'from'	=> $from,
					'to'	=> $to
				)
			);
			if ( isset($getEvents) && $getEvents->numRows() > 0)
			{
				while( !false == ($row = $getEvents->fetchRow( MYSQL_ASSOC ) ) )
				{
					$start		= DateTime::createFromFormat("Y-m-d H:i:s", $row['start']);
					$end		= DateTime::createFromFormat("Y-m-d H:i:s", $row['end']);
					$timestamp	= DateTime::createFromFormat("Y-m-d H:i:s", $row['timestamp']);
					$modified	= DateTime::createFromFormat("Y-m-d H:i:s", $row['modified']);


					$event	= array(
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
					$p = new DatePeriod(
						$start,
						new DateInterval('P1D'),
						$end
					);
					foreach ($p as $k => $v) {
						self::$eventsByDay[$v->format('d.m.Y')][]	= $event;
					}
					self::$events[$start->format('d.m.Y')][]	= $event;
				}
			}
			if ($output) return self::getEventsList();
			else return self::$events;

		}

		private static function getEventsList()
		{
			global $parser;
			if( !self::getSectionID() || !$parser ) return false;

			$templatePath	= CAT_PATH . '/modules/' . self::$directory .'/templates/';
			
			if ( file_exists( $templatePath . self::getVariant() . '/listEvent.tpl' ) )
				$parser->setPath( $templatePath . self::getVariant() );
			elseif ( file_exists( $templatePath . 'default/listEvent.tpl' ) )
				$parser->setPath( $templatePath . 'default/' );
			
			$parser->setFallbackPath( $templatePath . 'default/' );

			$return	= '';
			foreach (self::$events as $date => $events)
			{
				$data	= array(
					'date'		=> $date,
					'events'	=> $events
				);
				$return	.= $parser->get( 'listEvent', $data );
			}
			return $return;
		}

		private function getSummerTime()
		{
			// TODO: implement here
		}
	
		private function setSummerTime()
		{
			// TODO: implement here
		}

		private static function getSectionID()
		{
			if ( !self::$section_id ) return false;
			else return self::$section_id;
		}
	
		private function setSectionID( int $section_id = NULL )
		{
			if ( !$section_id ) return false;
			self::$section_id	= intval( $section_id );
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
			self::ouput();
		}

		private function output()
		{
			header_remove();
			header('Content-Type: text/calendar; charset=utf-8');
			header('Content-Disposition: attachment; filename="cal.ics"');

			echo self::$CalObj->render();
		}

		public static function getMonth()
		{
			return IntlDateFormatter::formatObject(
				DateTime::createFromFormat("U", time()),
				'MMMM',
				self::$locale
			);
		}

		public static function getMonths()
		{
			$arr	= array();
			for($i=1; $i<=12; $i++)
			{
				$dObj	= DateTime::createFromFormat("d-m-Y", "01-".$i."-1982");
				$arr[$dObj->format('m')] = IntlDateFormatter::formatObject($dObj, 'MMMM',self::$locale);
			}
			return $arr;
		}
		public static function getDays()
		{
			$arr	= array();
			for($i=1; $i<=7; $i++)
			{
				$dObj	= DateTime::createFromFormat("d-m-Y", $i."-03-1982");
				$arr[$dObj->format('d')] = IntlDateFormatter::formatObject($dObj, 'E', self::$locale);
			}
			return $arr;
		}

		public static function getDaysInYearMonth( int $time = NULL )
		{

			$time	= is_numeric($time) ? $time : time();
			$dt		= DateTime::createFromFormat("U", $time);
			$i		= 0;

			$firstDay	= DateTime::createFromFormat("Y-m-d", $dt->format('Y-m') . '-01');
			$dt			= DateTime::createFromFormat("U", ($firstDay->format('U')-(($firstDay->format('N')-1)*60*60*24)));

			for($w=1; $w<7; $w++)
			{
				for($d=1; $d<8; $d++)
				{
					$dObj	= DateTime::createFromFormat("U", $dt->format("U")+($i++)*60*60*24);
					self::$month[$w][$d]	= array(
						'date_day'		=> IntlDateFormatter::formatObject($dObj, 'dd'		,self::$locale),
						'date_month'	=> IntlDateFormatter::formatObject($dObj, 'MM'		,self::$locale),
						'date_wday'		=> IntlDateFormatter::formatObject($dObj, 'e'		,self::$locale),
						'date_name'		=> IntlDateFormatter::formatObject($dObj, 'eeee'	,self::$locale),
						'date_week'		=> IntlDateFormatter::formatObject($dObj, 'ww'		,self::$locale),
						'eventCount'	=> isset(self::$events[$dObj->format('d.m.Y')])
							? count(self::$events[$dObj->format('d.m.Y')]) : 0,
						'events'		=> isset(self::$events[$dObj->format('d.m.Y')])
							? self::$events[$dObj->format('d.m.Y')] : NULL,
						'eventsByDayCount'	=> isset(self::$eventsByDay[$dObj->format('d.m.Y')])
							? count(self::$eventsByDay[$dObj->format('d.m.Y')]) : 0,
						'eventsByDay'		=> isset(self::$eventsByDay[$dObj->format('d.m.Y')])
							? self::$eventsByDay[$dObj->format('d.m.Y')] : NULL
					);
				}
			}
			return self::$month;
		}


		/**
		 * retrieve the route
		 *
		 * @access private
		 * @return
		 **/
		private static function getRoute($remove_prefix=NULL)
		{
			foreach(array_values(array('REQUEST_URI','REDIRECT_SCRIPT_URL','SCRIPT_URL','ORIG_PATH_INFO','PATH_INFO')) as $key)
			{
				if(isset($_SERVER[$key]))
				{
					self::$routeUrl = parse_url($_SERVER[$key],PHP_URL_PATH);
					self::$routeQuery = parse_url($_SERVER[$key],PHP_URL_QUERY);
					if ( self::$routeQuery > '' ) self::$routeUrl = self::getOption('permalink') .'/'. str_replace('q=','',self::$routeQuery);
					break;
				}
			}
			if(!self::$routeUrl) { self::$routeUrl = '/'; }

			// remove params
			if(stripos(self::$routeUrl,'?'))
				list(self::$routeUrl,$ignore) = explode('?',self::$routeUrl,2);
			
			$path_prefix = str_ireplace(
				CAT_Helper_Directory::sanitizePath($_SERVER['DOCUMENT_ROOT']),
				'',
				CAT_Helper_Directory::sanitizePath(CAT_PATH)
			);


			// if there's a prefix to remove
			if($remove_prefix)
			{
				self::$routeUrl = str_replace(trim( $remove_prefix, '/' ),'',self::$routeUrl);
			}

			// Remove leading and ending "/"
			self::$routeUrl	= trim( self::$routeUrl, '/');

			return array(self::$routeUrl,self::$routeQuery);
		}   // end function initRoute()

		public static function checkRedirect()
		{
			$rUrl	= CAT_Registry::get('USE_SHORT_URLS') ?
					CAT_URL . '/' . self::$routeUrl
					: CAT_URL . '/' . trim(PAGES_DIRECTORY,'/') . '/' . self::$routeUrl;
			$sUrl	= CAT_Helper_Page::getLink(self::$page_id);

			if( self::getOption('permalink') . '.php' == self::$routeUrl )
			{
				$redirect	= CAT_Registry::get('USE_SHORT_URLS') ?
					CAT_URL  . '/' . self::getOption('permalink')
					: CAT_URL . '/' . trim(PAGES_DIRECTORY,'/') . '/' . self::getOption('permalink');
				header("HTTP/1.1 301 Moved Permanently");
				header("Location:" . $redirect );
				exit();
			}

/*			if( $rUrl == $sUrl && ( $rUrl == CAT_URL . $_SERVER['REQUEST_URI'] ) )
			{
				$redirect	= CAT_Registry::get('USE_SHORT_URLS') ?
					CAT_URL  . '/' . self::getOption('permalink') . '/'
					: CAT_URL . '/' . trim(PAGES_DIRECTORY,'/') . '/' . self::getOption('permalink') . '/';
				header("HTTP/1.1 301 Moved Permanently");
				header("Location:" . $redirect );
				exit();
			}*/
		}

		/**
		 *
		 */
		public static function view()
		{
			global $parser;

			self::setIDs();
			self::getRoute(PAGES_DIRECTORY);
			self::checkRedirect();

			$rUrl	= CAT_Registry::get('USE_SHORT_URLS') ?
					CAT_URL . '/' . self::$routeUrl
					: CAT_URL . '/' . trim(PAGES_DIRECTORY,'/') . '/' . self::$routeUrl;

			if ( file_exists( CAT_PATH . '/modules/lib_mdetect/mdetect/mdetect.php' ) )
			{
				require_once( CAT_PATH . '/modules/lib_mdetect/mdetect/mdetect.php' );
				$uagent_obj = new uagent_info();
				if( $uagent_obj->DetectMobileQuick() )
				{
					self::setParserValue('is_mobile', true);
				}
			} else {
					self::setParserValue('is_mobile', NULL);
			}

			self::setParserValue('calendars',self::getCalendar());
			self::setParserValue('months',self::getMonths());
			self::setParserValue('days',self::getDays());
			self::setParserValue('options',self::getOption());

			self::setParserValue('page_link', CAT_URL . CAT_Helper_Page::getInstance()->properties( self::$page_id, 'link' ));
			self::setParserValue(
				'section_name',
				str_replace( array('ä', 'ö', 'ü', 'ß'), array('ae', 'oe', 'ue', 'ss'), strtolower( $section['name'] ) )
			);
			if ( self::$routeUrl == self::getOption('permalink') )
				#|| !self::$routeQuery )#$rUrl == CAT_Helper_Page::getLink(self::$page_id) )
			{
				self::setParserValue('activeDay',date("d"));
				self::setParserValue('activeMonth',date("n"));
				self::setParserValue('activeYear',date("Y"));
				self::setParserValue('dates',
					self::getEventsInPeriod( strtotime(date('01-m-Y')), strtotime(date('t-m-Y')), 1, NULL, true )
				);
				self::setParserValue('cal', catCalendarObject::getDaysInYearMonth());
				static::$template	= 'view';
			} else {
				static::$template	= 'viewEvent';
				self::getEvent();
			}

			self::setParserValue(
				'prevY',
				date(
					'Y',
					strtotime( self::getParserValue('activeYear') . '-' . self::getParserValue('activeMonth') . '-01') - 60*60*24
				)
			);
			self::setParserValue(
				'prevM',
				date(
					'm',
					strtotime( self::getParserValue('activeYear') . '-' . self::getParserValue('activeMonth') . '-01') - 60*60*24
				)
			);
			self::setParserValue(
				'nextY',
				date(
					'Y',
					strtotime( self::getParserValue('activeYear') . '-' . self::getParserValue('activeMonth') . '-28') + 60*60*24*4
				)
			);
			self::setParserValue(
				'nextM',
				date(
					'm',
					strtotime( self::getParserValue('activeYear') . '-' . self::getParserValue('activeMonth') . '-28') + 60*60*24*5
				)
			);

/*
	Should be checked later
	it should be able to modify values inside the object (therefor it should be an object!)....
	*/
			if( file_exists( CAT_PATH . '/modules/' . static::$directory . '/view/' . self::getVariant() . '/view.php' ) )
				include CAT_PATH . '/modules/' . static::$directory . '/view/' . self::getVariant() . '/view.php';


			$parser->setPath( CAT_PATH . '/modules/' . static::$directory . '/templates/' . self::getVariant() );
			$parser->setFallbackPath( CAT_PATH . '/modules/' . static::$directory . '/templates/default' );

			if( !self::$parsed )
				$parser->output(
					static::$template,
					self::getParserValue()
				);

			self::$parsed = true;
		}


		/**
		 *
		 */
		public static function modify()
		{
			global $parser;

			self::setIDs();
			self::setParserValue('options',		self::getOption());
			self::setParserValue('dates',		self::getAllEvents());
			self::setParserValue('calendars',	self::getCalendar());
			self::setParserValue('dates',		self::getAllEvents());
			self::setParserValue('catCal_WYSIWYG',	'catCal_WYSIWYG_' . self::getSectionID());

			if ( file_exists(  CAT_PATH . '/modules/' . static::$directory . '/modify/' . self::getVariant() . '/modify.php' ) )
				include(  CAT_PATH . '/modules/' . static::$directory . '/modify/' . self::getVariant() . '/modify.php' );
			elseif ( file_exists( CAT_PATH . '/modules/' . static::$directory . '/modify/default/modify.php' ) )
				include( CAT_PATH . '/modules/' . static::$directory . '/modify/default/modify.php' );
			

			$parser->setPath( CAT_PATH . '/modules/' . static::$directory . '/templates/' . self::getVariant() );
			$parser->setFallbackPath( CAT_PATH . '/modules/' . static::$directory . '/templates/default' );

			if( !self::$parsed )
				$parser->output(
					static::$template,
					self::getParserValue()
				);

			self::$parsed = true;
		}

		/**
		 *
		 */
		public static function upgrade()
		{
			// TODO: implement here
		}

		/**
		 * get variant of gallery
		 *
		 * @access public
		 * @return string
		 *
		 **/
		public static function getVariant()
		{
			if ( isset( self::$options['variant'] ) )
				return self::$options['variant'];

			return self::getOption('variant');
		} // getVariant()

	}
}

require_once "class.catCalendarEvent.php";
?>