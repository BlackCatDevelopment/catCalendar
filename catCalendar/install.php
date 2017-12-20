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

if(defined('CAT_URL'))
{
	/**
	 * parse SQL file and execute the statements
	 **/
	$errors = array();
	$import = file_get_contents(dirname(__FILE__).'/inc/db/structure.sql');
	$import = preg_replace( "%/\*(.*)\*/%Us", '', $import );
	$import = preg_replace( "%^--(.*)\n%mU", '', $import );
	$import = preg_replace( "%^$\n%mU", '', $import );
	foreach (split_sql_file($import, ';') as $imp)
	{
		if ($imp != '' && $imp != ' ') {
			$ret = CAT_Helper_Page::getInstance()->db()->query($imp);
			if ( CAT_Helper_Page::getInstance()->db()->isError() )
				$errors[] = CAT_Helper_Page::getInstance()->db()->getError();
		}
	}

	if ( count($errors) > 0 )
		print_r($errors);

#	$gallery_path	= CAT_PATH . MEDIA_DIRECTORY . '/catCalendar';
#	if ( !file_exists($gallery_path) )
#		CAT_Helper_Directory::getInstance()->createDirectory( $gallery_path . '/temp', NULL, true );

/*	// Activate search for image_contents
	$insert_search = CAT_Helper_Page::getInstance()->db()->query( sprintf(
			"SELECT * FROM `%ssearch`
				WHERE `value` = '%s'",
			CAT_TABLE_PREFIX,
			'catCalendar'
		)
	);

	if( $insert_search->numRows() == 0 )
	{
		// Insert info into the search table
		// Module query info
		$field_info = array(
			'page_id'			=> 'page_id',
			'title'				=> 'page_title',
			'link'				=> 'link',
			'description'		=> 'description',
			'modified_when'		=> 'modified_when',
			'modified_by'		=> 'modified_by'
		);

		$field_info = serialize($field_info);

		CAT_Helper_Page::getInstance()->db()->query( sprintf(
				"INSERT INTO `%ssearch`
					( `name`, `value`, `extra` ) VALUES
					( 'module', 'catCalendar', '%s' )",
				CAT_TABLE_PREFIX,
				$field_info
			)
		);
		// Query start
		$query_start_code = "SELECT [TP]pages.page_id, [TP]pages.page_title, [TP]pages.link, [TP]pages.description, [TP]pages.modified_when, [TP]pages.modified_by FROM [TP]mod_catCalendar_contents, [TP]pages WHERE ";

		CAT_Helper_Page::getInstance()->db()->query( sprintf(
				"INSERT INTO `%ssearch`
					( `name`, `value`, `extra` ) VALUES
					( 'query_start', '%s', '%s' )",
				CAT_TABLE_PREFIX,
				$query_start_code,
				'catCalendar'
			)
		);
		// Query body
		$query_body_code = " [TP]pages.page_id = [TP]mod_catCalendar_contents.page_id AND [TP]mod_catCalendar_contents.text [O] \'[W][STRING][W]\' AND [TP]pages.searching = \'1\'";

		CAT_Helper_Page::getInstance()->db()->query( sprintf(
				"INSERT INTO `%ssearch`
					( `name`, `value`, `extra` ) VALUES
					( 'query_body', '%s', '%s' )",
				CAT_TABLE_PREFIX,
				$query_body_code,
				'mod_catCalendar_contents'
			)
		);

		// Query end
		$query_end_code = "";
		CAT_Helper_Page::getInstance()->db()->query( sprintf(
				"INSERT INTO `%ssearch`
					( `name`, `value`, `extra` ) VALUES
					( 'query_end', '%s', '%s' )",
				CAT_TABLE_PREFIX,
				$query_end_code,
				'mod_catCalendar_contents'
			)
		);

	}
*/


	// add files to class_secure
	$addons_helper = new CAT_Helper_Addons();
	foreach(
		array(
			'save.php'
		)
		as $file
	) {
		if ( false === $addons_helper->sec_register_file( 'catCalendar', $file ) )
		{
			 error_log( "Unable to register file -$file-!" );
		}
	}
}


/**
 * Credits: http://stackoverflow.com/questions/147821/loading-sql-files-from-within-php
 **/
function split_sql_file($sql, $delimiter)
{
   // Split up our string into "possible" SQL statements.
   $tokens = explode($delimiter, $sql);

  // try to save mem.
   $sql = "";
   $output = array();

   // we don't actually care about the matches preg gives us.
   $matches = array();

   // this is faster than calling count($oktens) every time thru the loop.
   $token_count = count($tokens);
   for ($i = 0; $i < $token_count; $i++)
   {
      // Don't wanna add an empty string as the last thing in the array.
      if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0)))
      {
         // This is the total number of single quotes in the token.
         $total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
         // Counts single quotes that are preceded by an odd number of backslashes,
         // which means they're escaped quotes.
         $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);

         $unescaped_quotes = $total_quotes - $escaped_quotes;

         // If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
         if (($unescaped_quotes % 2) == 0)
         {
            // It's a complete sql statement.
            $output[] = $tokens[$i];
            // save memory.
            $tokens[$i] = "";
         }
         else
         {
            // incomplete sql statement. keep adding tokens until we have a complete one.
            // $temp will hold what we have so far.
            $temp = $tokens[$i] . $delimiter;
            // save memory..
            $tokens[$i] = "";

            // Do we have a complete statement yet?
            $complete_stmt = false;

            for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++)
            {
               // This is the total number of single quotes in the token.
               $total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
               // Counts single quotes that are preceded by an odd number of backslashes,
               // which means they're escaped quotes.
               $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);

               $unescaped_quotes = $total_quotes - $escaped_quotes;

               if (($unescaped_quotes % 2) == 1)
               {
                  // odd number of unescaped quotes. In combination with the previous incomplete
                  // statement(s), we now have a complete statement. (2 odds always make an even)
                  $output[] = $temp . $tokens[$j];

                  // save memory.
                  $tokens[$j] = "";
                  $temp = "";

                  // exit the loop.
                  $complete_stmt = true;
                  // make sure the outer loop continues at the right point.
                  $i = $j;
               }
               else
               {
                  // even number of unescaped quotes. We still don't have a complete statement.
                  // (1 odd and 1 even always make an odd)
                  $temp .= $tokens[$j] . $delimiter;
                  // save memory.
                  $tokens[$j] = "";
               }

            } // for..
         } // else
      }
   }

   // remove empty
   for ( $i = count($output)+1; $i>=0; $i-- )
   {
       if ( isset($output[$i]) && trim($output[$i]) == '' )
       {
           array_splice($output, $i, 1);
       }
   }

   return $output;
}


?>