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
<?php require_once ( TG_SEARCHBOXES_ABSPATH.'classes/tgSearchboxesRenderer.class.php' ); ?>
<?php 
/*	WHAT & WHY: displaying the status of the saved options, if there were some errors then some error messages regarding the fields which were filled in ar displayed else a succesfully updated messages is displayed	*/
settings_errors();
?>
<form action="options.php" method="post">
<?php
	/*	Output nonce, action, and option_page hidden fields for a settings page;	*/
	settings_fields('tg_searchboxes_options');
	/*	Prints out all settings sections added to a particular settings page;	*/
	do_settings_sections('tg_searchboxes_options');
?>
<br />
<?php /* div containing the color settings */ ?>
<div class="colorSettings">
	<label class="colorSet">border color:</label><br />
	<input class="i1" type="text" name="tg_searchboxes_options[brdcolor]" value="<?php echo esc_attr($this->options['brdcolor']); ?>" style="background-color:<?php echo esc_attr($this->options['brdcolor']); ?>" /><span class="colorwheal i1">&nbsp;</span><br />
	<label class="colorSet">text color:</label><br />
	<input class="i2" type="text" name="tg_searchboxes_options[txtcolor]"  value="<?php echo esc_attr($this->options['txtcolor']); ?>" style="background-color:<?php echo esc_attr($this->options['txtcolor']); ?>" /><span class="colorwheal i2">&nbsp;</span><br />
	<label class="colorSet">background color:</label><br />
	<input class="i3" type="text" name="tg_searchboxes_options[bgdcolor]" value="<?php echo esc_attr($this->options['bgdcolor']); ?>" style="background-color:<?php echo esc_attr($this->options['bgdcolor']); ?>" /><span class="colorwheal i3">&nbsp;</span><br />
	<label class="colorSet">tabs color:</label><br />
	<input class="i4" type="text" name="tg_searchboxes_options[tbscolor]" value="<?php echo esc_attr($this->options['tbscolor']); ?>" style="background-color:<?php echo esc_attr($this->options['tbscolor']); ?>" /><span class="colorwheal i4">&nbsp;</span><br />
	<label class="colorSet">tabs text color:</label><br />
	<input class="i5" type="text" name="tg_searchboxes_options[tbstxtcolor]" value="<?php echo esc_attr($this->options['tbstxtcolor']); ?>" style="background-color:<?php echo esc_attr($this->options['tbstxtcolor']); ?>" /><span class="colorwheal i5">&nbsp;</span><br />
	<label class="colorSet">tabs border color:</label><br />
	<input class="i6" type="text" name="tg_searchboxes_options[tbsbrdcolor]" value="<?php echo esc_attr($this->options['tbsbrdcolor']); ?>" style="background-color:<?php echo esc_attr($this->options['tbsbrdcolor']); ?>" /><span class="colorwheal i6">&nbsp;</span>
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
	$tgSearchboxesRenderer = new tgSearchboxesRenderer(&$this, $atts);
	echo $tgSearchboxesRenderer->renderSearchboxes();
?>
</div>
<div class="SearchboxContainer" style="width:270px">
	<p>&larr;navigate to each tab by clicking it</p>
	<p>&larr;select flight type<br>(roundtrip versus one way)</p>
	<p>
		&larr;enter your default departure and return location or simple leave the fields empty.<br><br>
		* You can also set these values later on individually for each box you use.<br><br>
		* You have to enter the departure and return locations only once, empty fields will be filled in automatically.
	</p>
</div>
<div class="spcr">&nbsp;</div>
<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>
<h3>Short Implementation Guide</h3>
<p>
<i>Implementing into a blog:</i>
<ul>
	<li>Click on "add a new post".</li>
	<li>Inside your post editor you'll see an orange palm button, click on it and select the box you want to add, the box will appear directly inside your post.</li>
</ul>
</p>
<br/>
<p>
<i>Implementing anywhere (i.e. your sidebar or header)</i>
<ul>
	<li>Click on "Appearance" then select "Editor".</li>
	<li>Select the file that you would like to edit.</li>
	<li>Paste the shortcode [tg_searchboxes] where you would like the box to appear.</li>
</ul>
</p>
<br/>
<p>
Set up the default values the searchboxes will be filled in with.<br /> 
Enter the values directly into the boxes or leave the inputs empty.<br />
Select how far the default dates should be in the future.<br />
First date is relative to the current date; second date is relative to the first date.<br />
<br />
To test how the boxes work add them to your pages or visit <a href="<?php echo admin_url().'admin.php?page=tg_searchboxes_demo'; ?>">this demo page</a>.
</p>
