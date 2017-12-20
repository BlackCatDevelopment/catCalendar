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


<form action="{$CAT_URL}/modules/cc_catgallery/save.php" method="post" class="ajaxForm">
	<input type="hidden" name="page_id" value="{$page_id}" />
	<input type="hidden" name="section_id" value="{$section_id}" />
	<input type="hidden" name="gallery_id" value="{$gallery_id}" />
	<input type="hidden" name="action" value="saveOptions" />
	<input type="hidden" name="_cat_ajax" value="1" />
	<input type="hidden" name="options" value="effect,animSpeed,pauseTime,random,label" />
	<input type="hidden" name="image_options" value="alt" />
	<span class="cc_In200px">{translate('Kind of animation')}:</span>
	<select name="effect">
		<option value="0"{if !$options.effect} selected="selected"{/if}>{translate('No effect selected...')}</option>
		{foreach $effects as option}
		<option value="{$option}"{if $options.effect == $option} selected="selected"{/if}>{$option}</option>
		{/foreach}
	</select><br/>
	<span class="cc_In200px">{translate('Time until animation')}:</span>
	<input type="text" class="cc_In100px" name="pauseTime" value="{if $options.pauseTime}{$options.pauseTime}{else}8000{/if}" /> ms<br/>
	<span class="cc_In200px">{translate('Time for animation')}:</span>
	<input type="text" class="cc_In100px" name="animSpeed" value="{if $options.animSpeed}{$options.animSpeed}{else}3000{/if}" /> ms<br/>
	<span class="cc_In200px">{translate('Width of label (Set to 0 for no labels)')}:</span>
	<input type="text" class="cc_In100px" name="label" value="{if $options.label}{$options.label}{else}0{/if}" /> px<br/>
	<p class="cc_In300px">
		<input id="random_{$section_id}" class="fc_checkbox_jq" type="checkbox" name="random" value="1" {if $options.random}checked="checked" {/if}/>
		<label for="random_{$section_id}">{translate('Show images by chance')}:</label>
	</p><br/>
	<input type="submit" name="speichern" value="{translate('Save')}" />
</form>
