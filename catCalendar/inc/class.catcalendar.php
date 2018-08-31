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
	class catCalendar
	{
		private static $instance;
		protected static $section_id	= NULL;

		public $contents			= array();
		public $options				= array();

		public $variant				= 'default';
		public static $directory	= 'catCalendar';
		public static $allVariants	= array();


		protected static $initOptions		= array(
			'variant'		=> 'default'
		);

		public static function getInstance()
		{
			if (!self::$instance)
				self::$instance	= new self();
			return self::$instance;
		}

		public function __construct( $section_id = NULL )
		{
			if ( ($section_id && is_numeric($section_id)) )
			{
				self::setCalID($section_id);
			} elseif ( $gallery_id && is_numeric($gallery_id) && $is_header ) {
				self::setSectionID($gallery_id);
				self::setCalID();
			} elseif ( $gallery_id === true ) {
				global $section_id;
				self::setSectionID( $section_id );
				self::initAdd();
				self::setCalID();
			} else {
				global $section_id;
				self::setSectionID($section_id);
				self::setCalID();
			}
		}

		/**
		 * set the $section_id by self:$gallery_id
		 *
		 * @access public
		 * @return integer
		 *
		 **/
		private function setSectionID( $sectionID = NULL )
		{
			if ( $sectionID )
			{
				self::$section_id	= intval($sectionID);
			} else {
				// Get columns in this section
				$sectionID	= CAT_Helper_Page::getInstance()->db()->query(
						'SELECT `section_id` ' .
							'FROM `:prefix:mod_catCalendar` ' .
							'WHERE `gallery_id` = :galID',
					array(
						'galID'	=> self::$gallery_id
					)
				)->fetchColumn();

				self::$section_id	= intval($sectionID);
			}

			return $this;
		} // end setCalID()



		/**
		 * set the $gallery_id by self:$sectionid
		 *
		 * @access public
		 * @return integer
		 *
		 **/
		private function setCalID( $galleryID = NULL )
		{
			if ( $galleryID )
			{
				self::$gallery_id	= intval($galleryID);
				self::setSectionID();
			} else {
				// Get columns in this section
				$gallery_id	= CAT_Helper_Page::getInstance()->db()->query(
						'SELECT `gallery_id` ' .
							'FROM `:prefix:mod_catCalendar` ' .
							'WHERE `section_id` = :section_id',
					array(
						'section_id'	=> self::$section_id
					)
				)->fetchColumn();
				self::$gallery_id	= intval($gallery_id);
			}
			self::setGalleryFolder();
			return $this;
		} // end setCalID()

		/**
		 * set the $gallery_id by self:$sectionid
		 *
		 * @access public
		 * @return integer
		 *
		 **/
		public function getGalleryID()
		{
			if ( !self::$gallery_id )
				$this->setCalID();
			return self::$gallery_id;
		} // end getGalleryID()

		public function __destruct() {}

		/**
		 * return, if in a current object all important values are existing (section_id, gallery_id)
		 *
		 * @access public
		 * @param  integer  $image_id - optional check for $image_id to be numeric
		 * @return boolean true/false
		 *
		 **/
		private function checkIDs( $image_id = NULL )
		{
			if ( !self::$section_id ||
				!self::$gallery_id ||
				( $image_id && !is_numeric( $image_id ) )
			) return false;
			else return true;
		}

		/**
		 * add new catCalendar
		 *
		 * @access public
		 * @return integer
		 *
		 **/
		private function initAdd()
		{
			if ( !self::$section_id ) return false;

			// Add a new catCalendar
			if ( CAT_Helper_Page::getInstance()->db()->query(
					'INSERT INTO `:prefix:mod_catCalendar` ' .
						'( `section_id` ) VALUES ' .
						'( :section_id )',
					array(
						'section_id'	=> self::$section_id
					)
				)
			) {
				$return	= true;
				$this->setCalID();

				// Add initial options for gallery
				foreach( self::$initOptions as $name => $val )
				{
					if( !$this->saveOptions( $name, $val ) )
						$return	= false;
				}
				if ( $return &&
					CAT_Helper_Directory::getInstance()->createDirectory( $this->getFolder(), NULL, true ) )
					return self::$gallery_id;
				else
					return false;
			}
			else return false;
		} // initAdd()

		/**
		 * delete a catCalendar
		 *
		 * @access public
		 * @return integer
		 *
		 **/
		public function deleteCalendar()
		{
			if( !$this->checkIDs() ) return false;

			$return	= true;

			// Delete complete record from the database
			if( !CAT_Helper_Page::getInstance()->db()->query(
					'DELETE FROM `:prefix:mod_catCalendar` ' .
						'WHERE `section_id` = :section_id AND ' .
							'`gallery_id` = :gallery_id',
					array(
						'section_id'	=> self::$section_id,
						'gallery_id'	=> self::$gallery_id
					)
				) ) $return = false;
			// Delete folder
			if ( $return )
				if( CAT_Helper_Directory::getInstance()->removeDirectory( $this->getFolder() ) );
					else return true;
				else return false;
			return false;
		}



		/**
		 * get option of an image
		 *
		 * @access public
		 * @param  integer  $image_id - optional id of an image
		 * @return array()
		 *
		 **/
		private function getImgOptions( $image_id = NULL )
		{
			if ( !$this->checkIDs() ) return false;

			$select	= '';

			if ( $image_id
				&& isset($this->images[$image_id]['options']))
					return $this->images[$image_id]['options'];


			if ( !$image_id && count( $this->images ) > 0 )
			{
				foreach ( array_keys( $this->images ) as $id )
				{
					$select	.= " OR `image_id` = '" . intval( $id ) . "'";
				}
				$select		= "AND (" . substr( $select, 3 ) . ")";
			}
			elseif ( $image_id )
			{
				$select		= "AND `image_id` = '" . intval( $image_id ) . "'";
			}
			else return false;

			$opts	= CAT_Helper_Page::getInstance()->db()->query( sprintf(
					'SELECT * FROM `:prefix:mod_catCalendar_images_options`
							WHERE `gallery_id` = :gallery_id %s',
					$select
				),
				array(
					'gallery_id'	=> self::$gallery_id
				)
			);

			$options							= array();
			if ( $image_id )
				$this->images[$image_id]['options']	= array();

			if ( $opts && $opts->rowCount() > 0)
			{
				while( !false == ($row = $opts->fetch() ) )
				{
					$options[$row['image_id']][$row['name']]		= $row['value'];

					if ( isset($this->images[$row['image_id']]['options']) )
						$this->images[$row['image_id']]['options']	= array_merge(
							$this->images[$row['image_id']]['options'],
							array(
								$row['name']		=> $row['value']
							)
						);
					else $this->images[$row['image_id']]['options']	= array(
							$row['name']	=> $row['value']
						);

				}
			}
			if ( $image_id )
				return $this->images[$image_id]['options'];
			else
				return $options;
		} // end getImgOptions()

		/**
		 * get content of an image
		 *
		 * @access public
		 * @param  string  $image_id - optional id of an image
		 * @return array()
		 *
		 **/
		private function getImgContent( $image_id = NULL )
		{
			if ( !$this->checkIDs( $image_id ) ) return false;

			$select	= '';

			if ( !$image_id && count( $this->images ) > 0 )
			{
				foreach ( array_keys( $this->images ) as $id )
				{
					$select	.= " OR `image_id` = '" . intval( $id ) . "'";
				}
				$select		= "(" . substr( $select, 3 ) . ")";
			}
			elseif ( $image_id )
			{
				$select		= "`image_id` = '" . intval( $image_id ) . "'";
			}
			else return false;

			$conts	= CAT_Helper_Page::getInstance()->db()->query(
					'SELECT `content`, `image_id` FROM `:prefix:mod_catCalendar_contents`
						WHERE ' . $select
			);

			$contents	= array();

			if ( $conts && $conts->rowCount() > 0)
			{
				while( !false == ($row = $conts->fetch() ) )
				{
					$contents[$row['image_id']]['content']		= stripcslashes( $row['content'] );
					$this->images[$row['image_id']]['content']	= stripcslashes( $row['content'] );
				}
			}
			if ( $image_id )
				return $this->images[$image_id]['content'];
			else
				return $contents;
		} // end getImgContent()




		/**
		 * save options for single image to database
		 *
		 * @access public
		 * @param  string/array		$image_id - id of image
		 * @param  string			$name - name for option
		 * @param  string			$value - value for option
		 * @return bool true/false
		 *
		 **/
#		public function saveImgOptions( $image_id = NULL, $name = NULL, $value = '' )
#		{
#
#			if( !$this->checkIDs( $image_id ) ||
#					!$name ) return false;
#
#			if ( CAT_Helper_Page::getInstance()->db()->query(
#				'REPLACE INTO `:prefix:mod_catCalendar_images_options` ' .
#					'SET `gallery_id`	= :gallery_id, ' .
#						'`image_id`		= :image_id, ' .
#						'`name`			= :name, ' .
#						'`value`		= :value',
#				array(
#					'gallery_id'	=> self::$gallery_id,
#					'image_id'		=> $image_id,
#					'name'			=> $name,
#					'value'			=> $value
#				)
#			) ) return true;
#			else return false;
#
#		} // end saveContentOptions()



		/**
		 * get options for catCalendar
		 *
		 * @access public
		 * @param  string			$name - name for option
		 * @param  string			$value - value for option
		 * @return array()
		 *
		 **/
		public function getOptions( $name = NULL )
		{

			if ( !$this->checkIDs() ) return false;

			if ( $name && isset($this->options[$name]) ) return $this->options[$name];

			$getOptions		= $name ? 
				CAT_Helper_Page::getInstance()->db()->query(
					'SELECT * FROM `:prefix:mod_catCalendar_options` ' .
						'WHERE `gallery_id` = :gallery_id AND ' .
							'`name` = :name',
					array(
						'gallery_id'	=> self::$gallery_id,
						'name'			=> $name
					)
				) : 
				CAT_Helper_Page::getInstance()->db()->query(
					'SELECT * FROM `:prefix:mod_catCalendar_options` ' .
						'WHERE `gallery_id` = :gallery_id',
					array(
						'gallery_id'	=> self::$gallery_id
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


		/**
		 * save options for catCalendar
		 *
		 * @access public
		 * @param  string			$name - name for option
		 * @param  string			$value - value for option
		 * @return bool true/false
		 *
		 **/
		public function saveOptions( $name = NULL, $value = '' )
		{
			if ( !$this->checkIDs() ||
				!$name
			) return false;

			if ( CAT_Helper_Page::getInstance()->db()->query(
				'REPLACE INTO `:prefix:mod_catCalendar_options` ' .
					'SET `gallery_id`	= :gallery_id, ' .
						'`name`			= :name, ' .
						'`value`		= :value',
				array(
					'gallery_id'	=> self::$gallery_id,
					'name'			=> $name,
					'value'			=> is_null($value) ? '' : $value
				)
			) ) return true;
			else return false;
		} // end saveOptions()


		/**
		 * get ID of object gallery
		 *
		 * @access public
		 * @return integer
		 *
		 **/
		public function getID()
		{
			return self::$gallery_id;
		} // getID()





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

	}
}

?>