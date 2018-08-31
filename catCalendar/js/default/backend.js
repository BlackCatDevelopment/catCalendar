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

if(typeof cC_checkToggle !== 'function'){

	function cC_checkToggle($form,check,$tog,trueValue)
	{
		if (typeof trueValue === 'undefined') trueValue	= false;

		var $cur	= $form.find('input[name="' + check + '"]');

		if ( ( $cur.is(':checked') && trueValue === true )
			|| ( !$cur.is(':checked') && trueValue === false ) )
		{
			$tog.addClass('cC_Active')
				.find('input, textarea, select').prop('disabled', false);
		} else
		{
			$tog.removeClass('cC_Active')
				.find('input, textarea, select').prop('disabled', true);
		}
	}

}


$(document).ready(function()
{
	if (typeof catCalIDs !== 'undefined' && typeof catCalLoaded === 'undefined')
	{
		// This is a workaround if backend.js is loaded twice
		catCalLoaded	= true;
		$.each( catCalIDs, function( index, cCID )
		{
			var $catCal		= $('#catCal_' + cCID.section_id),
				$form		= $catCal.find('#catCalFrom_' + cCID.section_id),
				$catEvents	= $catCal.find('#catCalEvents_' + cCID.section_id),
				$catCals	= $catCal.find('#catCal_cals_' + cCID.section_id),
				$creatUser	= $('#cC_User_' + cCID.section_id),
				$creatTime	= $('#cC_Created_' + cCID.section_id),
				$butPub		= $('#ButPEvent_' + cCID.section_id);

			cC_checkToggle($form,'allday',$form.find('.cC_ADtoggle'),false);

			$catCal.find('.cc_toggle_set').next('form').hide();
			$catCal.find('.cc_toggle_set, .catCal_skin input:reset').unbind().click(function()
			{
				$(this).closest('.catCal_skin').children('form').slideToggle(200);
			});


			$form.on(
				'click', '#addCal_' + cCID.section_id, function(e)
			{
				e.preventDefault();
				var	$cur	= $(this),
					calName	= $cur.prev('input').val();

			});

			$form.on(
				'click', '#ButDEvent_' + cCID.section_id, function(e)
			{
				e.preventDefault();
			});
			$form.on(
				'click', '#ButSEvent_' + cCID.section_id + ', #ButSEvent2_' + cCID.section_id, function(e)
			{
				e.preventDefault();
				var dataForm	= {};
				$form.find('input, select, textarea').each( function()
				{
					var $cur		= $(this);
					switch($cur.attr('type'))
					{
						case 'select':
							dataForm[$cur.attr('name')] = $cur.children('option:selected').val();
							break;
						case 'radio':
							dataForm[$cur.attr('name')]	= $cur.prop('checked') ? $cur.val() : null;
							break;
						case 'checkbox':
							dataForm[$cur.attr('name')]	= $cur.prop('checked');
							break;
						default:
							dataForm[$cur.attr('name')]	= $cur.val();
					}
				});
				var ajaxData	= {
						page_id		: cCID.page_id,
						section_id	: cCID.section_id,
						eventid		: $form.data('eventid') ? $form.data('eventid') : -1,
						event		: dataForm,
						action		: 'saveEvent',
						_cat_ajax	: 1
					};

				$.ajax(
				{
					type:		'POST',
					context:	$(this),
					url:		CAT_URL + '/modules/catCalendar/save.php',
					dataType:	'JSON',
					data:		ajaxData,
					cache:		false,
					beforeSend:	function( data )
					{
						// Set activity and store in a vaxsriable to use it later
						data.process	= set_activity( 'Save event' );
					},
					success:	function( data, textStatus, jqXHR )
					{
						if ( data.success === true )
						{
							console.log(data.responseText, textStatus, jqXHR);
							return_success( jqXHR.process , data.message );
/*							var $cur	= $(this);
							if ( data.event == 1 )
							{
								$cur.addClass('published');
								$catEvents.find('dd').removeClass('active').filter($cur).addClass('active');
							}
							else {
								$cur.removeClass('published');
								$catEvents.find('dd').removeClass('active').filter($cur).addClass('active');	
							}

							console.log(data);*/
						}
						else {
							// return error
							return_error( jqXHR.process , data.message );
						}
					},
					error:		function( data, textStatus, jqXHR )
					{
						console.log( data, textStatus, jqXHR );
						return_error( jqXHR.process , data.message );
					}
				});
			});

			$form.on(
				'click', '#ButPEvent_' + cCID.section_id, function(e)
			{
				e.preventDefault();
				var ajaxData	= {
						page_id		: cCID.page_id,
						section_id	: cCID.section_id,
						eventid		: $form.data('eventid'),
						action		: 'publishEvent',
						_cat_ajax	: 1
					};

				$.ajax(
				{
					type:		'POST',
					context:	$(this),
					url:		CAT_URL + '/modules/catCalendar/save.php',
					dataType:	'JSON',
					data:		ajaxData,
					cache:		false,
					beforeSend:	function( data )
					{
						// Set activity and store in a vaxsriable to use it later
						data.process	= set_activity( 'Publish event' );
					},
					success:	function( data, textStatus, jqXHR )
					{
						if ( data.success === true )
						{
							return_success( jqXHR.process , data.message );
							var $cur	= $(this);
							if ( data.event == 1 )
							{
								$butPub.addClass('published');
/*								$catEvents.find('dd').removeClass('active').filter($cur).addClass('active');*/
							}
							else {
								$butPub.removeClass('published');
/*								$catEvents.find('dd').removeClass('active').filter($cur).addClass('active');	*/
							}
						}
						else {
							// return error
							return_error( jqXHR.process , data.message );
						}
					},
					error:		function( data, textStatus, jqXHR )
					{
						console.log( data, textStatus, jqXHR );
						return_error( jqXHR.process , data.message );
					}
				});

			});

			$form.on(
				'click', '#addEvent_' + cCID.section_id, function(e)
			{
				e.preventDefault();
				$form[0].reset();
				cC_checkToggle($form,'allday',$form.find('.cC_ADtoggle'),false);
				$form.find('input:text, textarea').first().focus();
				$catEvents.find('dd.active').removeClass('active');

				$creatUser.html('...');
				$creatTime.html('... um ...');
			});

			$form.on(
				'click', '#cC_allday_' + cCID.section_id, function()
			{
				cC_checkToggle($form,'allday',$form.find('.cC_ADtoggle'),false);
			});


			$catEvents.on(
				'click', 'dd', function()
			{
				var	$cur		= $(this),
					ajaxData	= {
						page_id		: cCID.page_id,
						section_id	: cCID.section_id,
						eventid		: $cur.data('eventid'),
						action		: 'getEvent',
						_cat_ajax	: 1
					};
			
				$.ajax(
				{
					type:		'POST',
					context:	$cur,
					url:		CAT_URL + '/modules/catCalendar/save.php',
					dataType:	'JSON',
					data:		ajaxData,
					cache:		false,
					beforeSend:	function( data )
					{
						// Set activity and store in a variable to use it later
						data.process	= set_activity( 'Getting event' );
					},
					success:	function( data, textStatus, jqXHR )
					{
						if ( data.success === true )
						{
							return_success( jqXHR.process , data.message );

							var $cur	= $(this);
							$form.data('eventid',$cur.data('eventid'));
							$catEvents.find('dd').removeClass('active').filter($cur).addClass('active');

							$creatUser.html(data.event.createdID);
							$creatTime.html(data.event.timestampDate + ' um ' + data.event.timestampTime);

							$.each( data.event, function(k,v)
							{
								var $i	= $form.find('input[name="'+k+'"], select[name="'+k+'"], textarea[name="'+k+'"]');
								if( $i.length > 0 )
								{
										switch($i.attr('type'))
										{
											case 'select':
												$i.children('option').prop('selected', false);
												$i.children('option[value="'+v+'"]').prop('selected', true);
												break;
											case 'radio':
												/*$i.prop('checked',v ? true : false);*/
												break;
											case 'checkbox':
												$i.prop('checked', v == 1 ? true : false );
												break;
											default:
												$i.val(v);
										}
								} else if ( k == 'published' )
								{
									if ( v == 1 )
										$butPub.addClass('published');
									else
										$butPub.removeClass('published');
								}
							});
							cC_checkToggle($form,'allday',$form.find('.cC_ADtoggle'),false);
						}
						else {
							// return error
							return_error( jqXHR.process , data.message );
						}
					},
					error:		function( data, textStatus, jqXHR )
					{
						console.log( data, textStatus, jqXHR );
						return_error( jqXHR.process , data.message );
					}
				});
			});

			$catCals.on(
				'click', 'span', function()
			{
				$catCals.toggleClass('cChide');
			});

			$catCals.on(
				'click', 'li', function()
			{
				var	$cur		= $(this),
					ajaxData	= {
						page_id		: cCID.page_id,
						section_id	: cCID.section_id,
						calid		: $cur.data('calid'),
						action		: 'getEvents',
						_cat_ajax	: 1
					};

				$.ajax(
				{
					type:		'POST',
					context:	$cur,
					url:		CAT_URL + '/modules/catCalendar/save.php',
					dataType:	'JSON',
					data:		ajaxData,
					cache:		false,
					beforeSend:	function( data )
					{
						// Set activity and store in a variable to use it later
						data.process	= set_activity( 'Getting calendar' );
					},
					success:	function( data, textStatus, jqXHR )
					{
						if ( data.success === true )
						{
							return_success( jqXHR.process , data.message );
							
							var $cur	= $(this),
								cur		= $catEvents.find('.active').data('eventid'),
								$act	= $catEvents.html(data.events).find('dd[data-eventid=' + cur + ']');

							$catCals.children('.ccIcons-menu-updown').text( ' ' + $cur.text() );

							// If the element exists in current calendar, add class active, otherwise activate the first element
							if( $act.length )
								$act.addClass('active');
							else $catEvents.find('dd:first').click();
						}
						else {
							// return error
							return_error( jqXHR.process , data.message );
						}
					},
					error:		function( data, textStatus, jqXHR )
					{
						console.log( data, textStatus, jqXHR );
						return_error( jqXHR.process , data.message );
					}
				});
			});
		});
	}
});