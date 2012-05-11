<?php
/**
 * Front-end Controller for Travel-Search (Travelgrove Searchboxes)
 *
  * @package Travel-Search
 * @subpackage Frontend Controller
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */

/**	Include file with the Base Controller Class	*/
require_once ( TG_SEARCHBOXES_ABSPATH.'controllers/controller-base.php' );
/**	Include SearchBox Renderer Class	*/
require_once ( TG_SEARCHBOXES_ABSPATH.'classes/tgSearchboxesRenderer.class.php' );

/**	Frontend Controller class, extends Base Controller Class	*/
class Tg_Searchboxes_Controller_Frontend extends Tg_Searchboxes_Controller_Base {
/**	PHP4 class constructor, calls the PHP5 class constructor __construct()	*/
function Tg_Searchboxes_Controller_Frontend() {
	$this->__construct();
}

/**	PHP5 class constructor	*/
function __construct() {
	/**	call parent's constructor for reading the options from WP options table	*/
	parent::__construct();
//	add_action('wp_enqueue_scripts', array(&$this, 'jquery_ui'), 0);
	/**	adding to the dom the css files needed by the plugin in the frontend	*/
	$this->enqueue_tg_searchboxes_css();
	/**	adding to the dom the js files and variables needed by the plugin in the frontend	*/
	$this->enqueue_tg_searchboxes_js();
	/**	adding a hook for the shortcode "tg_searchboxes";
	* WP will automatically pass the found arguments: [shortcode argument1=value1 argument2=value2]	*/
	add_shortcode($this->shortcode_tg_searchboxes, array(&$this, 'tg_searchboxes_handle_tg_shortcode'));
	return;
}

/**	handle shortcodes via renderer class	*/
function tg_searchboxes_handle_tg_shortcode($attr) {
	/**	init the renderer w/ the params. found in the source, by WP fn.	*/
	$tgSearchboxesRenderer	= new tgSearchboxesRenderer(&$this, $attr);
	// return the generated searchbox
	return $tgSearchboxesRenderer->renderSearchboxes();
}


/**	enque the required CSS files	*/
function enqueue_tg_searchboxes_css() {
	/**	CSS of customized options	*/
	wp_enqueue_style('tg_searchboxes_color_style', plugins_url('/css/tg_searchboxes_color.css', TG_SEARCHBOXES__FILE__));
	/**	basic/main CSS rules for the boxes	*/
	wp_enqueue_style('tg_searchboxes_style', plugins_url('/css/tg_searchboxes.min.css', TG_SEARCHBOXES__FILE__));
	/**	CSS rules for the datepicker calendars	*/
	wp_enqueue_style('tgsb_datepicker_style', plugins_url('/css/ui-lightness/datepicker.min.css', TG_SEARCHBOXES__FILE__));
	return true;
}

/**	enque the required JS files	*/
function enqueue_tg_searchboxes_js() {
	/**	AutoSuggestion drop-down for aiports+cities; (name, path, dependencies) - dependent on jQuery	*/
	wp_enqueue_script('tgsb_autosuggestion', plugins_url('/js/autosuggestion.min.js', TG_SEARCHBOXES__FILE__), array('jquery'));
	/**	jQuery DatePicker; dependent on jQuery and jQuery UI	*/
	wp_enqueue_script('tgsb_datepicker_script', plugins_url('/js/jquery-ui-datepicker.min.js', TG_SEARCHBOXES__FILE__), array('jquery', 'jquery-ui-core'));
	/**	dynamic functionalities of the searchboxes; main JS file	*/
	wp_enqueue_script('tgsb_main_script',
			plugins_url( '/js/tg_searchboxes.min.js', TG_SEARCHBOXES__FILE__ ),
			array('tgsb_datepicker_script', 'tgsb_autosuggestion', 'jquery'),
			// version number
			'1.0',
			// adding it to footer to make sure it will appear AFTER inline variables are set
			true);
	/**	context-dependent variables needed for dynamic functionality, in the main JS	*/
	wp_localize_script(
			// the name of the script where are this variables attached to
			'tgsb_main_script',
			// js object name that will contain the variables
			'TG_Searchboxes_Variables',
			array(	'str_CalendarURL'	=> plugins_url('/images/tg_searchboxes/calendarnew.png', TG_SEARCHBOXES__FILE__),
				'str_ASAjaxURL'		=> plugins_url('/ajax/autosuggestion.php', TG_SEARCHBOXES__FILE__),
				/*	converting PHP-format date to JS-format	*/
				'str_dateFormat'	=> $this->options['date_format'] == 'd/m/Y' ? 'dd/mm/yy' : 'mm/dd/yy',
				'str_merchantsAjaxURL'	=> plugins_url('/ajax/merchants.php', TG_SEARCHBOXES__FILE__),
				'str_ajaxLoaderCircle'	=> plugins_url('/images/tg_searchboxes/ajax-loader-circle.gif', TG_SEARCHBOXES__FILE__),
				'str_ajaxLoaderBert'	=> plugins_url('/images/tg_searchboxes/ajax-loader-bert.gif', TG_SEARCHBOXES__FILE__))
	);
	return true;
}

}
?>