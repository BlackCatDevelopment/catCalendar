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

<div class="catCal_form" id="catCal_{$section_id}">
	{include(modify/set_skin.tpl)}
	{include(modify/add_event.tpl)}
	{include(modify/set_general.tpl)}
	<div class="catCal_wrapper">
		<nav>
			<ol>
				<li>
					<dl>
						<dt>27.12.2017</dt>
						<dd class="cal_1">Ampelgespräche Abschlussschüler <span>13:30 - 14:00</span></dd>
						<dd class="cal_1">Frank Unterrichtsbesuch Mirjam <span>13:30 - 14:00</span></dd>
						<dd class="cal_1">Kino mit der Klasse <span>13:30 - 14:00</span></dd>
					</dl>
					<dl>
						<dt>28.12.2017</dt>
						<dd class="cal_1">SAD EVA Teamtreffen <span>13:30 - 14:00</span></dd>
					</dl>
				{*foreach $events as event}
				{include(modify/event.tpl)}
				{/foreach}
				{$event = NULL}
				{include(modify/event.tpl)*}
				<li>
			</ol>
			<div class="catCal_cals">
				<span class="icon-menu"> {translate('All calendars')}</span>
				<ul>
					<li>{translate('All calendars')}</li>
					{*foreach *}<li>Privat</li>{*/foreach*}
					{*foreach *}<li>Lorem ipsum</li>{*/foreach*}
				</ul>
			</div>
		</nav>
		{include(modify/event.tpl)}
	</div>
	<aside><small>Modulversion: {$version}</small></aside>
</div>

{*include(modify/wysiwyg.tpl)*}