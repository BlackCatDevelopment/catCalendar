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

<form action="{$CAT_URL}/modules/catCalendar/save.php" method="post" class="ajaxForm">
	<input type="hidden" name="section_id" value="{$section_id}">
	<input type="hidden" name="action" value="saveEvent">
	<input type="hidden" name="event_options" value="title,place,allday">
	<input type="hidden" name="_cat_ajax" value="1">

	<label for="cC_title_{$section_id}">{translate('Event title')}:</label>
	<input id="cC_title_{$section_id}" type="text" name="title" value="{if $event.options.title}{$event.options.title}{/if}" placeholder="{translate('Event title')}">

	<label for="cC_place_{$section_id}">{translate('Place')}:</label>
	<input id="cC_place_{$section_id}" type="text" name="place" value="{if $event.options.place}{$event.options.place}{/if}" placeholder="{translate('Place')}">

	<label for="cC_allday_{$section_id}">{translate('All day')}:</label>
	<input id="cC_allday_{$section_id}" type="checkbox" name="allday"{if $event.options.allday} checked="checked"{/if}><br>


	<label for="cC_start_date_{$section_id}">{translate('From')}:</label>
	<input id="cC_start_date_{$section_id}" type="date" name="start_date" value="{if $event.options.start_date}{$event.options.start_date}{/if}" placeholder="{translate('from date')}">
	<input id="cC_start_time_{$section_id}" type="datetime" name="start_time" value="{if $event.options.start_time}{$event.options.start_time}{/if}" placeholder="{translate('from time')}"><br>
	
	<label for="cC_end_date_{$section_id}">{translate('Until')}:</label>
	<input id="cC_end_date_{$section_id}" type="date" name="end_date" value="{if $event.options.end_date}{$event.options.end_date}{/if}" placeholder="{translate('until date')}">
	<input id="cC_end_time_{$section_id}" type="datetime" name="end_time" value="{if $event.options.end_time}{$event.options.end_time}{/if}" placeholder="{translate('until time')}"><br>

	<label for="cC_calendar_{$section_id}">{translate('Calendar')}:</label>
	<select id="cC_calendar_{$section_id}" name="calendar">
		<option{if $event.options.calendar} selected="selected"{/if}>
			Kalender 1
		</option>
		<option{if $event.options.calendar} selected="selected"{/if}>
			Kalender 2
		</option>
	</select><br>

	{*<label for="cC_description_{$section_id}">{translate('Description')}:</label><br>*}
	<textarea id="cC_description_{$section_id}" type="text" name="description" placeholder="{translate('Description')}" rows="6" cols="80">{if $event.options.description}{$event.options.description}{/if}</textarea><br>

	<hr>
		<small>Erstellt am $created von $user</small>
	<hr>

	<input type="submit" class="input_100p" value="{translate('Save event')}">
	<input type="submit" class="input_100p" value="{translate('Publish event')}">
	<input type="submit" class="input_100p" value="{translate('Duplicate event')}">
</form>