<?php
/**
 * @package Travel-Search
 * @subpackage Admin View for Demo page
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */
/*	no direct loading of this file	*/
if( !defined('TG_SEARCHBOXES_ABSPATH') )
	exit();
?>
<?php require_once ( TG_SEARCHBOXES_ABSPATH.'classes/tgSearchboxesRenderer.class.php' ); ?>
<p>See Travelgrove's Searchboxes in action - To add one of the boxes anywhere to your blog just copy the shortcode below the box and paste it to your theme template files.</p>
<p>If you want to have a specific box inside one of your posts or one of your static pages, just go to the editing interface and insert the box via the Travelgrove Button on your visual editor.</p>
<h2>Dynamic-Width Searchbox:</h2>
<?php
	$atts	= array( 'options' => '{"size":"dynamic"}');
	$tgSearchboxesRenderer	= new tgSearchboxesRenderer($this, $atts);
	echo $tgSearchboxesRenderer->renderSearchboxes();
?>
<br />
Shortcode:<input type="text" size="100" value="[tg_searchboxes options='{&quot;size&quot;:&quot;dynamic&quot;}']" onclick="this.select();" /><br />
<br>
<h2>SkyScraper Searchbox (160x600 pixels):</h2>
<?php
	$atts = array( 'options' => '{"size":"160x600"}');
	$tgSearchboxesRenderer = new tgSearchboxesRenderer($this, $atts);
	echo $tgSearchboxesRenderer->renderSearchboxes();
?>
<br />
Shortcode:<input type="text" size="100" value="[tg_searchboxes options='{&quot;size&quot;:&quot;160x600&quot;}']" onclick="this.select();" /><br />
<br />

<h2>300x250 Searchbox:</h2>
<?php
	$atts = array( 'options' => '{"size":"300x250"}');
	$tgSearchboxesRenderer = new tgSearchboxesRenderer($this, $atts);
	echo $tgSearchboxesRenderer->renderSearchboxes();
?>
<br />
Shortcode:<input type="text" size="100" value="[tg_searchboxes options='{&quot;size&quot;:&quot;300x250&quot;}']" onclick="this.select();" /><br />
<br />
<h2>300x533 Searchbox:</h2>
<?php
	$atts = array( 'options' => '{"size":"300x533"}');
	$tgSearchboxesRenderer = new tgSearchboxesRenderer($this, $atts);
	echo $tgSearchboxesRenderer->renderSearchboxes();
?>
<br />
Shortcode:<input type="text" size="100" value="[tg_searchboxes options='{&quot;size&quot;:&quot;300x533&quot;}']" onclick="this.select();" /><br />
<br />
<h2>Banner-sized Searchbox (728x90):</h2>
<?php
	$atts = array( 'options' => '{"size":"728x90"}');
	$tgSearchboxesRenderer = new tgSearchboxesRenderer($this, $atts);
	echo $tgSearchboxesRenderer->renderSearchboxes();
?>
<br />
Shortcode:<input type="text" size="100" value="[tg_searchboxes options='{&quot;size&quot;:&quot;728x90&quot;}']" onclick="this.select();" /><br />