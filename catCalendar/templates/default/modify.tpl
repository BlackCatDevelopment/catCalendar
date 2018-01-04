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

{include(modify/javascript.tpl)}

<div class="catCal_form" id="catCal_{$section_id}" data-display_name="{display_name}">
	{include(modify/set_skin.tpl)}
	{include(modify/add_event.tpl)}
	{include(modify/set_general.tpl)}
	<div class="catCal_wrapper">
		<nav>
			<div id="catCalEvents_{$section_id}" class="catCalEvents">
				{foreach $dates date events}{include(modify/listEvent.tpl)}{/foreach}
			</div>
			<div id="catCal_cals_{$section_id}" class="catCal_cals cChide">
				<span class="ccIcons-menu-updown"> {translate('All calendars')}</span>
				<ul>
					<li data-calid="-1" class="ccIcons-calendar">{translate('All calendars')}</li>
					{foreach $calendars cal}<li data-calid="{$cal.calID}" class="ccIcons-calendar">
						{$cal.name}
					</li>{/foreach}
				</ul>
				<form>
					<input type="text" name="newCalendar"><button type="submit" class="icon-plus" id="addCal_{$section_id}"></button>
				</form>
			</div>
		</nav>
		{include(modify/event.tpl)}
	</div>
	<aside><small>Modulversion: {$version}</small></aside>
</div>

{*include(modify/wysiwyg.tpl)*}