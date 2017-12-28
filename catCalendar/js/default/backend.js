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

$(document).ready(function()
{
	if (typeof catCalIDs !== 'undefined' && typeof catCalLoaded === 'undefined')
	{
		// This is a workaround if backend.js is loaded twice
		catCalLoaded	= true;
		$.each( catCalIDs, function( index, cCID )
		{
			var $catCal		= $('#catCal_' + cCID.section_id),
				$imgUL		= $catCal.children('#catCal_imgs_'  + cCID.section_id),
				$WYSIWYG	= $('#catG_WYSIWYG_' + cCID.section_id),
				$catNav		= $('#catCal_nav_' + cCID.section_id);

				
			$catCal.find('.cc_toggle_set').next('form').hide();
			$catCal.find('.cc_toggle_set, .catCal_skin input:reset').unbind().click(function()
			{
				$(this).closest('.catCal_skin').children('form').slideToggle(200);
			});
		
			/**/
			$imgUL.on( 'click',
				'.icon-remove',
			function()
			{
				$(this).closest('div').children('p').slideToggle(100);
			});

			$imgUL.on( 'click',
				'.cG_publish',
			function()
			{
				var $cur		= $(this),
					$li			= $cur.closest('li'),
					$inputs		= $li.find('input'),
					ajaxData	= {
						page_id		: cCID.page_id,
						section_id	: cCID.section_id,
						gallery_id	: cCID.section_id,
						imgID		: $inputs.filter('input[name=imgID]').val(),
						action		: 'publishIMG',
						_cat_ajax	: 1
					};
				dialog_ajax(
					'Bild veröffentlichen',
					CAT_URL + '/modules/catCalendar/save.php',
					ajaxData,
					'POST',
					'JSON',
					false, function(data)
					{
						if( data.published == 1 )
							$cur.addClass('active');
						else
							$cur.removeClass('active');
					}, $cur
				);
			});


			$imgUL.on( 'click',
				'.catCal_del_conf',
			function()
			{
				var	$cur		= $(this),
					$li			= $cur.closest('li'),
					$inputs		= $li.find('input'),
					ajaxData	= {
						page_id		: cCID.page_id,
						section_id	: cCID.section_id,
						gallery_id	: cCID.section_id,
						imgID		: $inputs.filter('input[name=imgID]').val(),
						action		: 'removeIMG',
						_cat_ajax	: 1
					};
			
				$.ajax(
				{
					type:		'POST',
					context:	$li,
					url:		CAT_URL + '/modules/catCalendar/save.php',
					dataType:	'JSON',
					data:		ajaxData,
					cache:		false,
					beforeSend:	function( data )
					{
						// Set activity and store in a variable to use it later
						data.process	= set_activity( 'Deleting event' );
					},
					success:	function( data, textStatus, jqXHR )
					{
						if ( data.success === true )
						{
							$(this).slideUp(300,function(){
								$(this).remove();
								ceckIMG( $imgUL );
							});
							return_success( jqXHR.process , data.message );
						}
						else {
							// return error
							return_error( jqXHR.process , data.message );
						}
					},
					error:		function( data, textStatus, jqXHR )
					{
						return_error( jqXHR.process , data.message );
					}
				});
			});
		
			$imgUL.on( 'click',
				'.catCal_del_res',
			function()
			{
				$(this).closest('div').children('p').slideUp(100);
			});
			
			$imgUL.on( 'click',
				'.toggleWYSIWYG',
			function(e)
			{
				e.preventDefault();
			
				var $par		= $(this).closest('li');
			
				$WYSIWYG.hide();
			
				if ( $par.hasClass('catCal_WYSIWYG') )
				{
					$par.removeClass('catCal_WYSIWYG fc_gradient1');
				} else {
					$imgUL.children('li').removeClass('catCal_WYSIWYG fc_gradient1');
			
					var	pos			= $par.position(),
						widthImg	= $par.find('.catCal_left').outerWidth()
						widthLi		= $par.outerWidth(),
						widthWY		= $WYSIWYG.outerWidth(),
						heightLi	= $par.outerHeight(),
						posLeft		= ( pos.left - ( widthLi / 2 ) ) < 0 ? 0 : ( pos.left - ( widthLi / 2 ) ),
						ajaxData	= {
							page_id		: cCID.page_id,
							section_id	: cCID.section_id,
							gallery_id	: cCID.section_id,
							imgID		: $par.find('input[name=imgID]').val(),
							action		: 'getContent',
							_cat_ajax	: 1
						};
			
					$WYSIWYG.css({
						top:	( pos.top + heightLi - 20 ) + "px"
					}).find('input[name=imgID]').val(ajaxData.imgID);
			
					$par.addClass('catCal_WYSIWYG');
					$.ajax(
					{
						type:		'POST',
						context:	$par,
						url:		CAT_URL + '/modules/catCalendar/save.php',
						dataType:	'JSON',
						data:		ajaxData,
						cache:		false,
						beforeSend:	function( data )
						{
							// Set activity and store in a variable to use it later
							data.process	= set_activity( 'Loading content' );
						},
						success:	function( data, textStatus, jqXHR )
						{
							if ( data.success === true )
							{
								$(this).addClass('fc_gradient1');
								$WYSIWYG.fadeIn(400);
								CKEDITOR.instances['wysiwyg_' + ajaxData.section_id].setData( data.event.event_content );
								CKEDITOR.instances['wysiwyg_' + ajaxData.section_id].updateElement();
								return_success( jqXHR.process , data.message );
							}
							else {
								$par.removeClass('catCal_WYSIWYG ');
								// return error
								return_error( jqXHR.process , data.message );
							}
						},
						error:		function( data, textStatus, jqXHR )
						{
							$par.removeClass('catCal_WYSIWYG ');
							return_error( jqXHR.process , data.message );
						}
					});
				}
			});
			
			$WYSIWYG.on( 'click',
				'input:reset',
			function(e)
			{
				e.preventDefault();
				$imgUL.children('li').removeClass('catCal_WYSIWYG fc_gradient1');
				$WYSIWYG.hide();
			});

			$catNav.children('li').click( function()
			{
				var $curr	= $(this),
					cur_ind	= $curr.index(),
					$nav	= $curr.closest('ul'),
					$tabs	= $nav.next('ul'),
					$currT	= $tabs.children('li').eq(cur_ind);
				$nav.children('li').removeClass('active').filter($curr).addClass('active');
				$tabs.children('li').removeClass('active').filter($currT).addClass('active');
			});
		});
	}
});