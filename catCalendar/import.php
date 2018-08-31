<?php 

$oldPosts	= array();

$result 	= CAT_Helper_Page::getInstance()->db()->query("SELECT * FROM `wb_mod_procalendar_actions` ORDER BY `date_start` DESC");

if( $result && $result->rowCount() > 0 )
{
	while ( false !== ( $row = $result->fetch() ) )
	{
		$oldPosts[$row['id']]	= $row;
	}
}

$count	= 0;
foreach ( $oldPosts as $ind => $post )
{
	 $post['custom1']	= explode( '-', str_replace(
		array('ab','uhr','tba', ' ', 'bis', 'ausstellungserÃ¶ffnung20.02.18um','jeweilsdonnerstags,','sieheÃ–ffnungszeitendesmuseums','zudenÃ–ffnungszeitendesns','dokumentationszentrums','beginn:freitag,17.30,ende:samstag,16','12.30bzw.13.15','.00','.30','.15','20:15(einlass19:45)','5.,6.und8.november2012','14:00und16:00','.45','.48'),
		array('','','','','-','','','','','','17:30 - 16:00','12:30',':00',':30',':15','20:15','','14:00',':45','.´:48'),
		strtolower($post['custom1'])
	) );
	$allday		= 0;

	if ( count($post['custom1']) == 2 )
	{
		$post['start']		= $post['date_start'] . ' ' . trim($post['custom1'][0]) .
			( strpos($post['custom1'][0],':') === false ? ':00' : '' );
		$post['end']		= $post['date_end'] . ' '  . trim($post['custom1'][1]) .
			( strpos($post['custom1'][1],':') === false ? ':00' : '' );
	} else if ( count($post['custom1']) == 1 && $post['custom1'][0] != '' )
	{
		$post['start']		= $post['date_start'] . ' ' . trim($post['custom1'][0]) .
			( strpos($post['custom1'][0],':') === false ? ':00' : '' );
		$post['end']		= $post['date_end'] . ' ' . trim($post['custom1'][0]) .
			( strpos($post['custom1'][0],':') === false ? ':00' : '' );
	} else {
		$post['start']		= $post['date_start'];
		$post['end']		= $post['date_end'];
		$allday				= 1;
	}	

	// Insert initial entry
	CAT_Helper_Page::getInstance()->db()->query(
		"INSERT INTO `:prefix:mod_catCalendar_events` " .
			"(`calID`, `published`, `location`, `title`, `description`, `start`, `end`, `modified`, `createdID`, `modifiedID`, `eventURL`, `allday`, `UID` ) " .
			"VALUES (:calID, :pub, :location, :title, :description, FROM_UNIXTIME(:start), FROM_UNIXTIME(:end), FROM_UNIXTIME(:modified), :createdID, :modifiedID, :eventURL, :allday, :UID)",
		array(
			'calID'			=> 1,
			'pub'			=> 1,
			'location'		=> strip_tags( str_replace(array_keys(getUmlauteArray()), getUmlauteArray(), $post['custom2'] ) ),
			'title'			=> strip_tags( str_replace(array_keys(getUmlauteArray()), getUmlauteArray(), $post['name'] ) ),
			'description'	=> str_replace(array_keys(getUmlauteArray()), getUmlauteArray(), $post['description'] ),
			'start'			=> strtotime($post['start']),
			'end'			=> strtotime($post['end']),
			'modified'		=> time(),
			'createdID'		=> $post['owner'],
			'modifiedID'	=> $post['owner'],
			'eventURL'		=> CAT_Helper_Page::getFilename($post['name']),
			'allday'		=> $allday,
			'UID'			=> uniqid()
		)
	);
/*	$last_id = CAT_Helper_Page::getInstance()->db()->query('SELECT LAST_INSERT_ID()')->fetchColumn();

	CAT_Helper_Page::getInstance()->db()->query(
		"INSERT INTO `:prefix:mod_blackNewsEntryOptions` " .
			"(`entryID`, `name`, `value` ) " .
			"VALUES (:entryID, :short, :value ) ",
		array(
			'entryID'		=> $last_id,
			'short'			=> 'short',
			'value'			=> str_replace(array_keys(getUmlauteArray()), getUmlauteArray(), $post['content_short'] )
		)
	);
*/
}


function getUmlauteArray() { return array( 'Ã¼'=>'ü', 'Ã¤'=>'ä', 'Ã¶'=>'ö', 'Ã–'=>'Ö', 'ÃŸ'=>'ß', 'Ã '=>'à', 'Ã¡'=>'á', 'Ã¢'=>'â', 'Ã£'=>'ã', 'Ã¹'=>'ù', 'Ãº'=>'ú', 'Ã»'=>'û', 'Ã™'=>'Ù', 'Ãš'=>'Ú', 'Ã›'=>'Û', 'Ãœ'=>'Ü', 'Ã²'=>'ò', 'Ã³'=>'ó', 'Ã´'=>'ô', 'Ã¨'=>'è', 'Ã©'=>'é', 'Ãª'=>'ê', 'Ã«'=>'ë', 'Ã€'=>'À', 'Ã'=>'Á', 'Ã‚'=>'Â', 'Ãƒ'=>'Ã', 'Ã„'=>'Ä', 'Ã…'=>'Å', 'Ã‡'=>'Ç', 'Ãˆ'=>'È', 'Ã‰'=>'É', 'ÃŠ'=>'Ê', 'Ã‹'=>'Ë', 'ÃŒ'=>'Ì', 'Ã'=>'Í', 'ÃŽ'=>'Î', 'Ã'=>'Ï', 'Ã‘'=>'Ñ', 'Ã’'=>'Ò', 'Ã“'=>'Ó', 'Ã”'=>'Ô', 'Ã•'=>'Õ', 'Ã˜'=>'Ø', 'Ã¥'=>'å', 'Ã¦'=>'æ', 'Ã§'=>'ç', 'Ã¬'=>'ì', 'Ã­'=>'í', 'Ã®'=>'î', 'Ã¯'=>'ï', 'Ã°'=>'ð', 'Ã±'=>'ñ', 'Ãµ'=>'õ', 'Ã¸'=>'ø', 'Ã½'=>'ý', 'Ã¿'=>'ÿ', 'â‚¬'=>'€', 'â€ž' => '„', 'â€œ' => '“', 'â€¦' => '…', 'â–º' => '>', 'â€š' => '‚', 'â€˜' => '‘', 'â€“' => '–', 'â€¨' => ' ', 'Â´' => '´', '&uuml;' => 'ü', '&auml;' => 'ä','&ouml;' => 'ö', '<p>' => '', '</p>' => '' );
}

?>
