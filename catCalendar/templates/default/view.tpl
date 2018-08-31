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

<section class="catCalSec">
	<h1>Veranstaltungskalender</h1>
	<p><img alt="" src="data:image/jpeg;base64,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" style="float:left; height:124px; width:140px"></p>
	<p>BCJ.Bayern ist nicht der einzige Akteur im Themenfeld Christentum-Judentum, Deutschland-Israel!</p>
	<p>Viele weitere Einzelpersonen, Organisationen und Institutionen sind im christlich-jüdischen Dialog engagiert,  auf ihre Veranstaltungen möchten wir Sie ebenfalls gerne hinweisen.</p>
	<p>Sie möchten Ihren Termin eintragen lassen oder uns auf ein Ereignis aufmerksam machen? Dann <a href="{cmsplink(66)}">kontaktieren</a> Sie uns bitte, wir freuen uns über Ihren Beitrag!</p>

	<p>Für Richtigkeit und Vollständigkeit der Informationen wird keine Haftung übernommen.</p>
	<hr>

</section>

<section class="catCalendar" id="catCalendar_{$section_id}">
	<nav>
		<ol>
			<li class="active"><a href="{$page_link}/{$prevY}/{$activeMonth}" class="catCal-arrow-left"></a> {$activeYear} <a href="{$page_link}/{$nextY}/{$activeMonth}" class="catCal-arrow-right"></a></li>
		</ol>
		<ol>
		{foreach $months i m}<li{if $i==$activeMonth} class="active"{/if}><a href="{$page_link}/{$activeYear}/{$i}">{if $i==$activeMonth}{$m}{else}{$m.0}{/if}</a></li>{/foreach}
		</ol>
	</nav>
	<table>
		<thead>
			<tr><th></th>
		{foreach $days i d}<th>{$d.0}{$d.1}</th>{/foreach}
			</tr>
		</thead>
		<tbody>
		{$week=00}
		{foreach $cal w}
			<tr>
				{foreach $w d}
				{if $week<$d.week}<td>{$week=$d.week}{$week}</td>{/if}
				<td{if $d.date_day==$activeDay&&$d.date_month==$activeMonth} class="active"{/if}><span class="{if $d.date_month!=$activeMonth} gray{/if}{if $d.events && $activeMonth==$d.date_month} isEvent{/if}">{if $d.events && $activeMonth==$d.date_month} {*$d.eventCount*}{/if}{$d.date_day}</span></td>{/foreach}
			</tr>
		{/foreach}
		</tbody>
	</table>
	<div class="cC_articles">
		{foreach $dates ind events}{*<h2>{$ind}</h2>*}{foreach $events event}{if $event.published}
		<article>
			<div>
				{if $event.picture}<a href="#"><img src="{$imgURL}{$event.picture}" width="{$options.resize_x}" height="{$options.resize_y}" alt="{$event.options.alt}" /></a>{/if}

				<h3>{$event.title}</h3>
				<span class="cC_artTime">{if strftime('%d.%m.',$event.start) == strftime('%d.%m.',$event.end)}{strftime('%d.%m.',$event.start)}{else}{strftime('%d.%m.',$event.start)} - {strftime('%d.%m.',$event.end)}{/if}</span>
				<div>{truncateHTML($event.description,100)}</div>
				{if $event.eventURL}<a href="{cat_url}/{$options.permalink}/{$event.eventURL}">{translate('Read more')}...</a>{/if}
			</div>
		</article>
		{/if}{/foreach}{/foreach}
	</div>
</section>

