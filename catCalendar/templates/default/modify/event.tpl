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

<li class="dz-preview dz-event-preview fc_border_all fc_shadow_small fc_br_all {if !$event}prevTemp prevTemp_{$gallery_id}{/if}" id="catG_{if !$event}__event_id__{else}{$event.event_id}{/if}">
	<div class="catG_IMG_options">
		<p class="drag_corner icon-resize" title="{translate('Reorder event')}"></p>
		<p class="cG_icon-feed cG_publish{if $event.published} active{/if}" title="{translate('Publish this event')}"></p>
		<div class="cc_catG_del">
			<span class="icon-remove" title="{translate('Delete this event')}"></span>
			<p class="fc_br_right fc_shadow_small">
				<span class="cc_catG_del_res">{translate('Keep it!')}</span>
				<strong> | </strong>
				<span class="cc_catG_del_conf">{translate('Confirm delete')}</span>
			</p>
		</div>
	</div>
	<form action="{$CAT_URL}/modules/cc_catgallery/save.php" method="post" class="ajaxForm">
		<input type="hidden" name="page_id" value="{$page_id}">
		<input type="hidden" name="section_id" value="{$section_id}">
		<input type="hidden" name="imgID" value="{if !$event}__event_id__{else}{$event.event_id}{/if}">
		<input type="hidden" name="action" value="saveEvent">
		<input type="hidden" name="event_options" value="alt">
		<input type="hidden" name="_cat_ajax" value="1">
		<div class="cc_catG_left dz-details">
			<p class="cc_catG_event">
				<img data-dz-thumbnail="" src="{$event.thumb}" width="auto" height="120" ><br>
			</p>
			<p class="dz-filename">
				<strong>{translate('Name of event')}: </strong><span data-dz-name="">{$event.picture}</span>
			</p>
			<p{if !$event} class="cc_catG_disabled"{/if}>
				<strong>{translate('Alternative text')}:<br></strong>
				<input type="text" name="alt" value="{if $event.options.alt}{$event.options.alt}{/if}" {if !$event}disabled{/if}>
			</p>
		</div>
		<button class="toggleWYSIWYG input_50p fc_br_bottomleft fc_gradient1 fc_gradient_hover" {if !$event}disabled{/if}>{translate('Modify description')}</button>
		<input type="submit" class="input_50p fc_br_bottomright" value="{translate('Save event')}" {if !$event}disabled{/if}>
	</form>
	<div class="clear"></div>
	{if !$event}<div class="dz-progress fc_br_top"><span class="dz-upload fc_br_all" data-dz-uploadprogress=""></span></div>
	<div class="dz-error-message"><span data-dz-errormessage=""></span></div>{/if}
</li>
