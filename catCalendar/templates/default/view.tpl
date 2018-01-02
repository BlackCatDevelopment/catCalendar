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
{if $countImg}
<script type="text/javascript">
	if (typeof catCalIDs === 'undefined')
	\{
		catCalIDs	= [];
	}
	catCalIDs.push(
	\{
		'section_id'	: {$section_id}
	});
</script>
{/if}

<section class="catCalendar" id="catCalendar_{$section_id}">
	{foreach $dates event}{if $event.published}
	<article>
			<a href="#"><img src="{$imgURL}{$event.picture}" width="{$options.resize_x}" height="{$options.resize_y}" alt="{$event.options.alt}" /></a>
	</article>
	{/if}{/foreach}
</section>