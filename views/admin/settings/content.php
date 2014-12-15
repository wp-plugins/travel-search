<?php
/**
 * main template/view for Admin pages (only)
 *
 * @package Travel-Search
 * @subpackage Admin Controller
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */
	// no direct loading of this file
	if( !defined('TG_SEARCHBOXES_ABSPATH') )
		exit();
?>
<?php
/*
		WHAT & WHY: view file used for the general settings page to render the settings form
		WHO & WHEN: Cipri on the 2nd of February 2012
*/
?>
<?php
require_once ( TG_SEARCHBOXES_ABSPATH.'classes/tgSearchboxesRenderer.class.php' );

/*
	WHAT & WHY: this part for the ftp credentials request is needed here because we want to integrated the ftp form returned in the Wordpress admin interface
	WHO & WHEN: Cipri on the 2nd of May 2012
*/

/*	WHAT & WHY: displaying the status of the saved options, if there were some errors then some error messages regarding the fields which were filled in ar displayed else a succesfully updated messages is displayed	*/
settings_errors();
?>
<?php /* WHAT & WHY: social media buttons; WHO & WHEN: Cipri on the 18th of June 2012 */ ?>
<span class="socialmediaButtons"><span class="plusone-button"><span id="plusone-div"></span><script type="text/javascript">gapi.plusone.render('plusone-div', {"size": "medium","count": "true", "href":"http://www.travelgrove.com"});</script></span><span class="facebook-like-button"><span id="fb-root"></span><script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#appId=187304151306561&amp;xfbml=true"></script><script type="text/javascript">document.write('<'+'fb:like'+' href="http://www.facebook.com/travelgrove" send="false" layout="button_count" show_faces="false" font="tahoma" width="90"></'+'fb:like'+'>');</script></span></span>
<ul class="tgsb_settings subsubsub">
	<li class="current"><a class="tgsb_settings current" href="#">Default Settings</a></li><li><a class="tgsb_shortcodeGenerator" href="#">Shortcode Generator</a></li><li class="noCnt">&nbsp;</li>
</ul>
<div class="clear"></div>
<div id="tgsb_settings">
<form action="options.php" method="post">
<?php
	/*	Output nonce, action, and option_page hidden fields for a settings page;	*/
	settings_fields('tg_searchboxes_options');
	/*	Prints out all settings sections added to a particular settings page;	*/
	do_settings_sections('tg_searchboxes_options');
?>
<br />
<?php	/*travelSearchUseJavaScript added | we should have default/global value for the flag that marks if searchbox should be loaded via JS or not | Tibi | 2013.04.23 */	?>
<input id="travelSearchUseJavaScript" style="margin-left:10px" type='checkbox' name='tg_searchboxes_options[usejavascript]'<?php echo ($this->options['usejavascript'] ? ' checked="checked"' : ''); ?> />
<label for="travelSearchUseJavaScript">Load Searchbox Using JavaScript (<i>beta version, for advanced users only</i>)</label> <br />
<input id="travelSearchNoConflict" style="margin-left:10px" type='checkbox' name='tg_searchboxes_options[noconflict]'<?php echo ($this->options['noconflict'] ? ' checked="checked"' : ''); ?> />
<label for="travelSearchNoConflict">Compatibility Mode (<i>Use this option for multiple jQuery instances, if our plugin does not work well</i>)</label> <br />
<?php /* div containing the color settings */ ?>
<div class="colorSettings">
	<label class="colorSet" for="brdcolor">border color:</label><br />
	<input class="i1" type="text" name="tg_searchboxes_options[brdcolor]" id="brdcolor" value="<?php echo esc_attr($this->options['brdcolor']); ?>" style="background-color:<?php echo esc_attr($this->options['brdcolor']); ?>" /><span class="colorwheal i1">&nbsp;</span><br />
	<label class="colorSet" for="txtcolor">text color:</label><br />
	<input class="i2" type="text" name="tg_searchboxes_options[txtcolor]" id="txtcolor" value="<?php echo esc_attr($this->options['txtcolor']); ?>" style="background-color:<?php echo esc_attr($this->options['txtcolor']); ?>" /><span class="colorwheal i2">&nbsp;</span><br />
	<label class="colorSet" for="bgdcolor">background color:</label><br />
	<input class="i3" type="text" name="tg_searchboxes_options[bgdcolor]" id="bgdcolor" value="<?php echo esc_attr($this->options['bgdcolor']); ?>" style="background-color:<?php echo esc_attr($this->options['bgdcolor']); ?>" /><span class="colorwheal i3">&nbsp;</span><br />
	<label class="colorSet" for="tbscolor">tabs color:</label><br />
	<input class="i4" type="text" name="tg_searchboxes_options[tbscolor]" id="tbscolor" value="<?php echo esc_attr($this->options['tbscolor']); ?>" style="background-color:<?php echo esc_attr($this->options['tbscolor']); ?>" /><span class="colorwheal i4">&nbsp;</span><br />
	<label class="colorSet" for="tbstxtcolor">tabs text color:</label><br />
	<input class="i5" type="text" name="tg_searchboxes_options[tbstxtcolor]" id="tbstxtcolor" value="<?php echo esc_attr($this->options['tbstxtcolor']); ?>" style="background-color:<?php echo esc_attr($this->options['tbstxtcolor']); ?>" /><span class="colorwheal i5">&nbsp;</span><br />
	<label class="colorSet" for="tbsbrdcolor">tabs border color:</label><br />
	<input class="i6" type="text" name="tg_searchboxes_options[tbsbrdcolor]" id="tbsbrdcolor" value="<?php echo esc_attr($this->options['tbsbrdcolor']); ?>" style="background-color:<?php echo esc_attr($this->options['tbsbrdcolor']); ?>" /><span class="colorwheal i6">&nbsp;</span>
	<div class="i1 nod"></div>
	<div class="i2 nod"></div>
	<div class="i3 nod"></div>
	<div class="i4 nod"></div>
	<div class="i5 nod"></div>
	<div class="i6 nod"></div>	
</div>
<div class="SearchboxContainer">
<?php	
	$atts = array('options' =>'{"size":"300x250", "defaultSettings":true}');
	$tgSearchboxesRenderer = new tgSearchboxesRenderer($this, $atts);
	echo $tgSearchboxesRenderer->renderSearchboxes();
?>
</div>
<div class="SearchboxContainer" style="width:270px">
	<p>&larr;navigate to each tab by clicking it</p>
	<p>&larr;select flight type<br>(roundtrip versus one way)</p>
	<p>
		&larr;enter your default departure and return location or simple leave the fields empty.<br /><br />
		* You can also set these values later on individually for each box you use.<br /><br />
		* You have to enter the departure and return locations only once, empty fields will be filled in automatically.<br /><br />
		* Note: If you do not want to show the "travel search" link, <a href="#" class="showTravelSearchLink">click here</a>
	</p>
	<div class="travelSearchLink" style="display:none">
		<label for="travelSearchLink">Show "travel search" link:</label>
		<select name='tg_searchboxes_options[links]' id="travelSearchLink" style="width:50px">
			<option value='1'<?php echo ($this->options['links'] ? ' selected="selected"' : ''); ?>>yes</option>
			<option value='0'<?php echo (!$this->options['links'] ? ' selected="selected"' : ''); ?>>no</option>
		</select>
	</div>
</div>
<div class="spcr">&nbsp;</div>
<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	<input type="button" class="tgsb_shortcodeGenerator button-highlighted" value="<?php _e('Get Shortcode') ?>" />
</p>
</form>

<h3>Short Implementation Guide</h3>
<p>
<i>Implementing into a blog:</i>
<ol>
<li>Use the box customization tool on top to set up the right colors and parameters then hit "Save Changes".</li>
<li>Click on "add a new post".</li>
<li>Inside your post editor you'll see an orange palm button, click on it and select the box you want to add, the box will appear directly inside your post.</li>
</ol>
</p>
<br/>
<p>
<i>Implementing anywhere (i.e. your sidebar or header)</i>
<ol>
<li>Use the box customization tool on top to set up the right colors and parameters then hit "Save Changes".</li>
<li><a href="#" class="tgsb_shortcodeGenerator">Click here</a> and follow the additional implementation guidelines.</li>
</ol>
</p>
<br/>
<p>
Additional Notes for Customization:<br />
You may set up different values the search boxes will be filled in.<br />
Enter the values directly into the boxes or leave the input fields empty.<br />
Select how far the default dates should be in the future.<br />
The departure date will be relative to the current date, the return date will be relative to the departure date.<br />
<br />
To test how the boxes work add them to your pages or visit <a href="<?php echo admin_url().'admin.php?page=tg_searchboxes_demo'; ?>">this demo page</a>.
</p>

</div>
<div id="tgsb_shortcodeGenerator" class="nod">
<h3>Shortcode generator</h3>
Using this tool will allow you to add a box anywhere on your blog i.e. the sidebar, header, footer etc..<br /><br />
1) Change your searchbox size as needed, so our system can generate your preferred shortcodes.
<br class="clear" />
<ul class="subsubsub measuresChooser" id="tgsb_measuresChooser">
	<li><a class="300x250  current" href="#">300x250</a> | </li>
	<li><a class="300x533" href="#">300x533</a> | </li>
	<li><a class="160x600" href="#">160x600</a> | </li>
	<li><a class="728x90" href="#">728x90</a>  | </li>
	<li><a class="dynamic" href="#">dynamic</a></li>
</ul><br class="clear" />

<div class="tgsb sb160x600">
	<?php require_once TG_SEARCHBOXES_ABSPATH. 'views/admin/tg_searchboxes/160x600/content.php'; ?>
</div>
<div class="tgsb sb300x250 crnt">
	<?php require_once TG_SEARCHBOXES_ABSPATH. 'views/admin/tg_searchboxes/300x250/content.php'; ?>
</div>
<div class="tgsb sb300x533">
	<?php require_once TG_SEARCHBOXES_ABSPATH. 'views/admin/tg_searchboxes/300x533/content.php'; ?>
</div>
<div class="tgsb sb728x90">
	<?php require_once TG_SEARCHBOXES_ABSPATH. 'views/admin/tg_searchboxes/728x90/content.php'; ?>
</div>
<div class="tgsb sbdynamic">
	<?php require_once TG_SEARCHBOXES_ABSPATH. 'views/admin/tg_searchboxes/dynamic/content.php'; ?>
	<br /><br />
	<i>* The minimum width of the dynamic box is 300 pixels.</i>
</div>
<br />
<br class="clear" />
<br />
<i>2) Customize more (optional):</i><br />
* Click on a tab you want to show as your default tab: Flights, Hotels, Packages or Cars<br />
* enter departure/destination cities (optional)<br />
* select travel dates (optional)<br />
* select the number and type of travelers (optional)<br /><br />
<?php	/*travelSearchUseJavaScript added | we should have default/global value for the flag that marks if searchbox should be loaded via JS or not | Tibi | 2013.04.23 */	?>
<input id="travelSearchShortcodeUseJavaScript" name='travelSearchShortcodeUseJavaScript' type='checkbox' />
<label for="travelSearchShortcodeUseJavaScript">Load Searchbox Using JavaScript (<i>beta version, for advanced users only</i>)</label> <br />
<br /><br />
<i><strong>Implement anywhere on your blog</strong></i><br />
a) Copy the following PHP shortcode:<br /><br />
<input type="text" size="80" value="&lt;?php echo do_shortcode('[tg_searchboxes]'); ?&gt;" id="tgsb_shortcode_php" onclick="this.select();" /> <input type="button" class="button-highlighted" value="<?php _e('Select') ?>" onclick="javascript:jQuery(this).prev().select();" /><br /><br />
b) On the left side menu, click on "Appearance" then select "Editor".<br /><br />
c) Select the file that you would like to add the box to.<br /><br />
d) Add the shortcode anywhere inside your code where the box is supposed to appear.<br /><br /><br />

<i><strong>Implement inside a post</strong></i><br />
a) copy the following shortcode:<br /><br />
<input type="text" size="80" value="[tg_searchboxes]" id="tgsb_shortcode" onclick="this.select();" /> <input type="button" class="button-highlighted" value="<?php _e('Select') ?>" onclick="javascript:jQuery(this).prev().select();" /> <br /><br />
b) paste it anywhere when you post a new article.<br /><br />
</div>
<style type="text/css">
<?php /**	while loading via AJAX the calendar must have a higher z-index than the actual popup, otherwise it won't appear;
		also since the box has position fixed the autosuggestion should also have pos fixed.	*/ ?>
ul.asMargin{position:fixed}
#ui-datepicker-div{z-index:110 !important}
.tgsb{display:none}
.crnt{display:block}
ul.measuresChooser#tgsb_measuresChooser li a:link,ul.measuresChooser#tgsb_measuresChooser li a:visited{color:#21759B}
ul.measuresChooser#tgsb_measuresChooser li a:hover,ul.measuresChooser#tgsb_measuresChooser li a:active{color:#000}
</style>