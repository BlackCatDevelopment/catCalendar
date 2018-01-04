{**
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
 *}

<form action="{$CAT_URL}/modules/catCalendar/save.php" method="post" class="ajaxForm" data-eventid="" id="catCalFrom_{$section_id}">
	<button type="submit" name="action" id="ButSEvent2_{$section_id}" value="saveEvent" class="hidden">{translate('Save event')}</button>
	<button type="submit" name="addEvent" id="addEvent_{$section_id}" class="ccIcons-plus">{translate('Add event')} / {translate('Reset Form')} </button>
	<button type="submit" name="action" id="ButDEvent_{$section_id}" value="duplicateEvent" class="ccIcons-copy right">{translate('Duplicate event')}</button>
	<hr>
	<input type="hidden" name="action" value="saveEvent">
{*	<input type="hidden" name="event_options" value="title, location, allday, start_date, start_time, end_date, end_time, calendar, description">*}
	<input type="hidden" name="_cat_ajax" value="1">


	<label for="cC_title_{$section_id}">{translate('Event title')}:</label>
	<input id="cC_title_{$section_id}" type="text" name="title" value="{if $event.options.title}{$event.options.title}{/if}" placeholder="{translate('Event title')}">

	<label for="cC_place_{$section_id}">{translate('Place')}:</label>
	<input id="cC_place_{$section_id}" type="text" name="location" value="{if $event.options.place}{$event.options.place}{/if}" placeholder="{translate('Place')}">

	<hr>

	<label for="cC_allday_{$section_id}" class="label80">{translate('All day')}:</label>
	<input id="cC_allday_{$section_id}" type="checkbox" name="allday"{if $event.options.allday} checked="checked"{/if} class="input_25p"><br>

	<label for="cC_start_date_{$section_id}" class="label80">{translate('From')}:</label>
	<input id="cC_start_date_{$section_id}" type="date" name="start_date" value="{if $event.options.start_date}{$event.options.start_date}{/if}" placeholder="{translate('from date')}" class="input_25p">
	<span class="cC_ADtoggle">
		<input id="cC_start_time_{$section_id}" type="time" step="60" name="start_time" value="{if $event.options.start_time}{$event.options.start_time}{/if}" placeholder="{translate('from time')}" class="input_25p"><br>
	</span>
	<label for="cC_end_date_{$section_id}" class="label80">{translate('Until')}:</label>
	<input id="cC_end_date_{$section_id}" type="date" name="end_date" value="{if $event.options.end_date}{$event.options.end_date}{/if}" placeholder="{translate('until date')}" class="input_25p">
	<span class="cC_ADtoggle">
		<input id="cC_end_time_{$section_id}" type="time" step="60" name="end_time" value="{if $event.options.end_time}{$event.options.end_time}{/if}" placeholder="{translate('until time')}" class="input_25p">
	</span>
	<hr>

	<label for="cC_calendar_{$section_id}" class="label80">{translate('Calendar')}:</label>
	<select id="cC_calendar_{$section_id}" name="calID" class="input_25p">
		{foreach $calendars cal}<option value="{$cal.calID}">
			{$cal.name}
		</option>{/foreach}
	</select>

	<hr>

	{*<label for="cC_description_{$section_id}">{translate('Description')}:</label><br>*}
	<textarea id="cC_description_{$section_id}" type="text" name="description" placeholder="{translate('Description')}" rows="6" cols="80">{if $event.options.description}{$event.options.description}{/if}</textarea><br>

	<hr>

	<button name="action" id="ButPEvent_{$section_id}" value="publishEvent" class="fc_gradient1 fc_gradient_hover ccIcons-feed right">
		<span class="is_published">{translate('Published')}</span>
		<span class="not_published">{translate('Unpublished')}</span>
	</button>

	<button type="submit" name="action" id="ButSEvent_{$section_id}" value="saveEvent">{translate('Save event')}</button>
<div class="clear"></div>
	<small>Erstellt am <strong id="cC_Created_{$section_id}">... um ...</strong> Uhr von <strong id="cC_User_{$section_id}">...</strong></small>
</form>