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

<div class="cc_catG_form" id="cc_catG_{$section_id}">
	{include(modify/set_skin.tpl)}
	<div class="clear"></div>
	<div class="cc_catG_settings">
		<ul class="cc_catG_nav fc_br_left" id="cc_catG_nav_{$section_id}">
			<li class="active fc_br_topleft">{translate('Upload new events')}</li>
			<li>{translate('Options for frontend')}</li>
			<li class="fc_br_bottomleft">{translate('Event option')}</li>
		</ul>
		<ul class="cc_catG_tabs fc_br_right">
			<li class="cc_catG_tab active">{include(modify/set_dropzone.tpl)}</li>
			<li class="cc_catG_tab">{include(modify/set_frontend.tpl)}</li>
			<li class="cc_catG_tab">{include(modify/set_event.tpl)}</li>
		</ul>
		<div class="clear"></div>
	</div>
	<p class="catG_IMG_y">{translate('Existing events')}</p>
	<p class="catG_IMG_n">{translate('No events available')}</p>
	<ul id="cc_catG_imgs_{$section_id}" class="cc_catG_imgs">
		{foreach $events as event}
		{include(modify/event.tpl)}
		{/foreach}
		{$event = NULL}
		{include(modify/event.tpl)}
	</ul>
</div>

{include(modify/wysiwyg.tpl)}