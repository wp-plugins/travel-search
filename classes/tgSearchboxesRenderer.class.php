<?php
/**
 * Renderer Class for the HTML searchboxes for Travel-Search plugin
 *
 * @package Travel-Search
 * @subpackage Frontend Renderer Class
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */
class tgSearchboxesRenderer {
	/*		*/
	private $atts	= array();
	private $controller;
	private $defaultSize		= '300x250';
	private $defaultSelectedTab	= 'flights';
	private $subID			= '106';
	private $defaultAlignment	= 'alignnone';

	private static $nrOfBoxes;
	private $valuesToBeSet;
	
	/**	arrays for travelers, used in renderers	*/

	private $adults		= array(1, 2, 3, 4, 5);
	private $kids		= array(0, 1, 2, 3, 4);
	private $seniors	= array(0, 1, 2, 3, 4, 5);
	private $rooms		= array(1, 2, 3, 4, 5);
	private $flightsClass	= array('Economy', 'Business', 'First');
		
	
	function __construct($controller, $atts) {
		$this->controller	= $controller;
		$this->atts		= $atts;
		/**	make the subID hookable - only for internal uses	*/
		$this->subID		= apply_filters('tg_searchboxes_subID', $this->subID);
		/**	current number of boxes needed for incremental IDs for inputs / labels	*/
		self::$nrOfBoxes++;
		return;
	}
	
	function renderSearchboxes() {
	
		if(empty($this->atts)) {
			foreach($this->controller->options as $option => $optionValue) {
				$this->atts[$option] = $optionValue;
			}
			$this->atts['size']		= $this->defaultSize;
			$this->atts['alignment']	= $this->defaultAlignment;
		}
		
		if(!empty($this->atts['options'])) {
			$this->atts			= json_decode($this->atts['options'], true);
		}
		
		if(empty($this->atts['size'])) {
			$this->atts['size']		= $this->defaultSize;
		}
		
		if(empty($this->atts['selectedTab'])) {
			$this->atts['selectedTab']	= $this->defaultSelectedTab;
		}
		
		if(empty($this->atts['alignment'])) {
			$this->atts['alignment']	= $this->defaultAlignment;
		}

		$departureDateTimestamp = strtotime('+'.$this->controller->options['departure_date']);
		$departureDate = date($this->controller->options['date_format'], $departureDateTimestamp);
		$returnDate = date($this->controller->options['date_format'], strtotime($this->controller->options['return_date'], $departureDateTimestamp));
		
		if(!empty($this->atts['departure_date']) || !empty($this->atts['return_date'])) {

			// default departure timestamp and return timestamp were set if one of those attributes are present in the shortcode
			$setDepartureDateTimestamp = 0;
			$setReturnDateTimestamp = 0;
		}
		if(!empty($this->atts['departure_date'])) {
			$date = $this->getDateDetails($departureDate);
			$nowDateTimestamp = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
			// returning the departure date shortcode attribute in an array(year, month, day, y/m/d)
			$date = $this->getDateDetails($this->atts['departure_date']);
			// setting the departure date timestamp
			$setDepartureDateTimestamp = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
			// if the departure date timestamp is bigger then the current timestamp then set the departure date from the shortcode's attribute
			if($setDepartureDateTimestamp > $nowDateTimestamp) {
				$departureDate	= $this->atts['departure_date'];
			/**	@note	if we don't use the dep. date specified as param, we shouldn't use it later to
					determine if the return date is correct or not - later we will use the default
					departure date to validate the return date
				@date	2013.03.06
				@author	Tibi	*/
			} else {
				$setDepartureDateTimestamp	= 0;
			}
		}
		if(!empty($this->atts['return_date'])) {
			// if the departure date timestamp is empty
			if(empty($setDepartureDateTimestamp)) {
				// returning the departure date set value in an array(year, month, day, y/m/d)
				$date = $this->getDateDetails($departureDate);
				// setting the departure date timestamp
				$setDepartureDateTimestamp = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
			}
			// returning the return date shortcode attribute in an array(year, month, day, y/m/d)
			$date = $this->getDateDetails($this->atts['return_date']);
			// setting the return date timestamp
			$setReturnDateTimestamp = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
			// if the return date timestamp is bigger then the departure date timestamp set the return date from the shortcode's attribute
			if($setReturnDateTimestamp > $setDepartureDateTimestamp) {
				$returnDate	= $this->atts['return_date'];
			}
		}
		
		$this->valuesToBeSet = array(
			'from_air'	=> (empty($this->atts['from_air']) ? 
						$this->controller->options['from_air'] : 
						$this->atts['from_air']
					), 
			'to_air'	=> (empty($this->atts['to_air']) ?
						$this->controller->options['to_air'] : 
						$this->atts['to_air']
					), 
			'departure_date'=> $departureDate, 
			'return_date'	=> $returnDate, 
			'adults'	=> (empty($this->atts['adults']) ?
						$this->controller->options['adults'] : 
						$this->atts['adults']
					), 
			'kids'		=> (!isset($this->atts['kids']) ?
						$this->controller->options['kids'] : 
						$this->atts['kids']
					), 
			'seniors'	=> (!isset($this->atts['seniors']) ?
						$this->controller->options['seniors'] : 
						$this->atts['seniors']
					), 
			'rooms'		=> (empty($this->atts['rooms']) ?
						$this->controller->options['rooms'] : 
						$this->atts['rooms']
					), 
			'rtow'		=> (!isset($this->atts['rtow']) ?
						$this->controller->options['rtow'] : 			
						$this->atts['rtow']
					)
		);
		
		$output .=	'<div class="tg_searchbox '.$this->atts['alignment'].' m'.$this->atts['size'].'" id="tgsb_'.self::$nrOfBoxes.'">';
		$output .=		'<ul class="tg_tabs">';
		$output .=			'<li><span class="flights'.(($this->atts['selectedTab'] == 'flights') ? ' sel' : '').'">'.(($this->atts['size']=='160x600') ? 'Air' : 'Flights').'</span></li>';
		$output .=			'<li><span class="hotels'.(($this->atts['selectedTab'] == 'hotels') ? ' sel' : '').'">'.(($this->atts['size']=='160x600') ? 'Hotel' : 'Hotels').'</span></li>';
		$output .=			(($this->atts['size']=='160x600') ? '' : '<li><span class="packages'.(($this->atts['selectedTab'] == 'packages') ? ' sel' : '').'">Packages</span></li>');
		$output .=			'<li><span class="cars'.(($this->atts['selectedTab'] == 'cars') ? ' sel' : '').'">'.(($this->atts['size']=='160x600') ? 'Car' : 'Cars').'</span></li>';
		$output .=		'</ul>';
		$output .=		'<div class="tg_container">';
		$output .=			($this->atts['defaultSettings']) ? '<div class="flights sel">' : '<form method="post" action="" class="flights'.(($this->atts['selectedTab'] == 'flights') ? ' sel' : '').'">';
		$output .=			$this->renderFlightsSearchbox();
		$output .=			($this->atts['defaultSettings']) ? '</div>' : '</form>';
		$output .=			($this->atts['defaultSettings']) ? '<div class="hotels">' : '<form class="hotels'.(($this->atts['selectedTab'] == 'hotels') ? ' sel' : '').'" method="post" action="">';
		$output .=			$this->renderHotelsSearchbox();
		$output .=			($this->atts['defaultSettings']) ? '</div>' : '</form>';
		
		if($this->atts['size'] != '160x600') {
			// we don't have the packages searchbox on the 160x600  box
			$output .=			($this->atts['defaultSettings']) ? '<div class="packages">' : '<form class="packages'.(($this->atts['selectedTab'] == 'packages') ? ' sel' : '').'" method="post" action="">';
			$output .=			$this->renderPackagesSearchbox();
			$output .=			($this->atts['defaultSettings']) ? '</div>' : '</form>';
		}
		
		$output .=			($this->atts['defaultSettings']) ? '<div class="cars">' : '<form class="cars'.(($this->atts['selectedTab'] == 'cars') ? ' sel' : '').'" method="post" action="">';
		$output .=			$this->renderCarsSearchbox();
		$output .=			($this->atts['defaultSettings']) ? '</div>' : '</form>';		
		// the "get this widget" link will be present only on the Dynamic Sized Box 
		$output .=			'<div class="pwr">'.
						($this->controller->options['links'] ?
							'<a href="http://www.travelgrove.com/">travel search</a>' :
							'travel search').
						' by Travelgrove';
		if($this->atts['size'] == 'dynamic' && $this->controller->options['links'])
			$output .=		' (<a class="wdg" href="http://labs.travelgrove.com/wordpress-plugins/travel-search/">get this widget</a>)';
		$output .=			'</div>';
		'</div>';
		$output .=		'</div>';
		$output .=	'</div>';
		return $output;
	}
	
	function renderFlightsSearchbox() {
		$output = array(
					'160x600'	=> 'renderFlightsSearchbox160x600', 
					'300x250'	=> 'renderFlightsSearchbox300x250', 
					'300x533'	=> 'renderFlightsSearchbox300x533', 
					'728x90'	=> 'renderFlightsSearchbox728x90',
					'dynamic'	=> 'renderFlightsSearchboxDynamic'
			);
		$method = $output[$this->atts['size']];
		if(!array_key_exists($this->atts['size'], $output) || !method_exists($this, $method))
			return '';
		return $this->$method();	
	}
	
	function renderHotelsSearchbox() {
		$output = array(
					'160x600'	=> 'renderHotelsSearchbox160x600', 
					'300x250'	=> 'renderHotelsSearchbox300x250', 
					'300x533'	=> 'renderHotelsSearchbox300x533', 
					'728x90'	=> 'renderHotelsSearchbox728x90',
					'dynamic'	=> 'renderHotelsSearchboxDynamic'
			);
		$method = $output[$this->atts['size']];
		if(!array_key_exists($this->atts['size'], $output) || !method_exists($this, $method))
			return '';
		return $this->$method();
	}
	
	function renderPackagesSearchbox() {
		$output = array( 
					'300x250'	=> 'renderPackagesSearchbox300x250', 
					'300x533'	=> 'renderPackagesSearchbox300x533', 
					'728x90'	=> 'renderPackagesSearchbox728x90',
					'dynamic'	=> 'renderPackagesSearchboxDynamic'
			);
		$method = $output[$this->atts['size']];
		if(!array_key_exists($this->atts['size'], $output))
			return '';
		return $this->$method();
	}
	
	function renderCarsSearchbox() {
		$output = array(
					'160x600'	=> 'renderCarsSearchbox160x600', 
					'300x250'	=> 'renderCarsSearchbox300x250', 
					'300x533'	=> 'renderCarsSearchbox300x533', 
					'728x90'	=> 'renderCarsSearchbox728x90',
					'dynamic'	=> 'renderCarsSearchboxDynamic'
			);
		$method = $output[$this->atts['size']];
		if(!array_key_exists($this->atts['size'], $output) || !method_exists($this, $method))
			return '';
		$carsSearchbox = $this->$method();
		return $carsSearchbox;
	}
	



/* Flights Renderisation Functions */

private function renderFlightsSearchbox160x600() {
			$output = 
			'<input type="radio" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" id="tgsb_'.self::$nrOfBoxes.'_rt" value=""'.((empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_rt">Roundtrip</label>
			<input class="oneway" type="radio" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" id="tgsb_'.self::$nrOfBoxes.'_ow" value="on"'.((!empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_ow">One Way</label>
			<div class="hr"></div>
			<label for="tgsb_'.self::$nrOfBoxes.'_from_f">From:</label>
			<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'inp_dep_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_f" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			<label for="tgsb_'.self::$nrOfBoxes.'_to_f">To:</label>
			<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'inp_arr_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_to_f" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" />
			<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_f">Depart:</label>';
			$output .= (($this->atts['defaultSettings']) ? $this->date_input('flights_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_f') :
			'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_f" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_f">Return:</label>';
			$output .= (($this->atts['defaultSettings']) ? $this->date_input('flights_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_f', false) : 
			'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_f" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" '.((!empty($this->valuesToBeSet['rtow'])) ? ' disabled="disabled"' : '').' />');
			$output .= '<br />
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_f">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_f', 'adults', $this->adults, $this->valuesToBeSet['adults']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_f">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'inp_child_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_children_f', 'kids', $this->kids, $this->valuesToBeSet['kids']);
				$output .= '</span>';
				$output .= '<div class="mrcList nod"></div>';
				$output .= '<div class="help">&nbsp;</div>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
			$output .= $this->renderSubmitButton();
			// removing the spaces between the tags
			$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
			return $output;
	}

	private function renderFlightsSearchbox300x250() {
			$output = '<div class="formContent">';
			$output .= '<input type="radio" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" id="tgsb_'.self::$nrOfBoxes.'_rt" value=""'.((empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_rt">Roundtrip</label>
			<input type="radio" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" id="tgsb_'.self::$nrOfBoxes.'_ow" value="on"'.((!empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_ow">One Way</label>
			<div class="hr"></div>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_f">From:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'inp_dep_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_f" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_f">To:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'inp_arr_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_to_f" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_f">Depart:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_f" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_f">Return:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_f" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" '.((!empty($this->valuesToBeSet['rtow'])) ? ' disabled="disabled"' : '').' />';
				$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_f">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_f', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_f">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'inp_child_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_children_f', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_f">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'inp_senior_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_seniors_f', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
				$output .= '</div>';
				$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
				
			$output .= $this->renderSubmitButton();
			$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
			return $output;
	}
	
	private function renderFlightsSearchbox300x533() {
			$output = 
			'<input type="radio" id="tgsb_'.self::$nrOfBoxes.'_rt" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" value=""'.((empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_rt">Roundtrip</label>
			<input type="radio" id="tgsb_'.self::$nrOfBoxes.'_ow" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" value="on"'.((!empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_ow">One Way</label>
			<div class="hr"></div>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_f">From:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'inp_dep_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_f" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_f">To:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'inp_arr_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_to_f" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_f">Depart:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('flights_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_f') :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_f" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_f">Return:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('flights_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_f', false) : 
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_f" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" '.((!empty($this->valuesToBeSet['rtow'])) ? ' disabled="disabled"' : '').' />');
				$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_f">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_f', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_f">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'inp_child_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_children_f', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_f">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'inp_senior_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_seniors_f', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span>';
				$output .= '<div class="mrcList nod"></div>';
				$output .= '<div class="help">&nbsp;</div>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
			$output .= $this->renderSubmitButton();
			// removing the spaces between the tags
			$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
			return $output;
	}

	private function renderFlightsSearchbox728x90() {
		$output .= '<div class="formContent">';
		$output .= '<span>
			<label for="tgsb_'.self::$nrOfBoxes.'_from_f">From:</label>
			<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'inp_dep_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_f" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			<label for="tgsb_'.self::$nrOfBoxes.'_to_f">To:</label>
			<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'inp_arr_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_to_f" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
		</span>
		<span>
			<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_f">Depart:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('flights_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_f') :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_f" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_f">Return:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('flights_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_f', false) : 
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_f" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
				$output .= '</span>
			<span class="akstt">	
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_f">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_f', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_f">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'inp_child_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_children_f', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_f">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'inp_senior_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_seniors_f', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span>
			<span class="s">	
				<label for="tgsb_'.self::$nrOfBoxes.'_rtow">Trip type:</label>';	
				$output .= '<select class="tt" name="oneway" id="tgsb_'.self::$nrOfBoxes.'_rtow">';
					$output .= '<option value=""'.((empty($this->valuesToBeSet['rtow'])) ? ' selected="selected"' : '').'>round-trip</option>';
					$output .= '<option value="on" '.((!empty($this->valuesToBeSet['rtow'])) ? ' selected="selected"' : '').'>oneway</option>';			
				$output .= '</select>
			</span>
			</span>';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
			$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
			$output .= '</div>';
			$output .= '<div class="mrcList nod"></div>';
			$output .= $this->renderSubmitButton();
			// removing the spaces between the tags
			$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
			return $output;
	}
	
	private function renderFlightsSearchboxDynamic() {
			$output = '<div class="formContent">';
			$output .= '<input type="radio" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" id="tgsb_'.self::$nrOfBoxes.'_rt" value=""'.((empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_rt">Roundtrip</label>
			<input type="radio" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" id="tgsb_'.self::$nrOfBoxes.'_ow" value="on"'.((!empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_ow">One Way</label>
			<div class="hr"></div>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_f">From:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'inp_dep_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_f" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_f">To:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'inp_arr_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_to_f" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" />
			</span><br />
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_f">Depart:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_f" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_f">Return:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_f" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" '.((!empty($this->valuesToBeSet['rtow'])) ? ' disabled="disabled"' : '').' />';
				$output .= '
			</span><br />
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_f">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_f', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_f">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'inp_child_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_children_f', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_f">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'inp_senior_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_seniors_f', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_class_f">Class:</label>';
				$output .= $this->createSelectTag('class', 'tgsb_'.self::$nrOfBoxes.'_class_f', 'tt', $this->flightsClass, 'Economy', true);
				$output .='</span>';
				
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';				
				$output .= '</div>';
				$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
			$output .= $this->renderSubmitButton();
			$output .= '<div class="spcr"></div>';
			$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
			return $output;
	}	
	
/* Hotels Renderisation Functions */	

private function renderHotelsSearchbox160x600() {
		$output =
		'<label for="tgsb_'.self::$nrOfBoxes.'_city_h">City:</label>
		<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'airport')).'" id="tgsb_'.self::$nrOfBoxes.'_city_h" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addASH asTo" />
		<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_h">Check-In:</label>';
		$output .= (($this->atts['defaultSettings']) ? $this->date_input('hotels_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_h') : 
		'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'start_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_h" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
		$output .= '<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_h">Check-Out:</label>';
		$output .= (($this->atts['defaultSettings']) ? $this->date_input('hotels_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_h', false) :
		'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'end_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_h" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
		$output .= '<span class="s">
			<label for="tgsb_'.self::$nrOfBoxes.'_adults_h">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_h', 'adults', $this->adults, $this->valuesToBeSet['adults']);
		$output .= '</span>
		<span class="s">
			<label for="tgsb_'.self::$nrOfBoxes.'_rooms_h">Rooms:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'no_room')), 'tgsb_'.self::$nrOfBoxes.'_rooms_h', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
		$output .= '</span>';
		$output .= '<div class="mrcList nod"></div>';
		$output .= '<div class="help">&nbsp;</div>';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
		$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}


	private function renderHotelsSearchbox300x250() {
		$output = '<div class="formContent">';
		$output .='<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_city_h">City:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'airport')).'" id="tgsb_'.self::$nrOfBoxes.'_city_h" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addASH asTo" />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_h">Check-In:</label>';
		$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'start_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_h" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
		$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_h">Check-Out:</label>';
		$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'end_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_h" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />';
		$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_h">Adults:</label>';
		$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_h', 'adults', $this->adults, $this->valuesToBeSet['adults']);
		$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_h">Rooms:</label>';
		$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'no_room')), 'tgsb_'.self::$nrOfBoxes.'_rooms_h', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
		$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_h">Kids:</label>';
		$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'no_child')), 'tgsb_'.self::$nrOfBoxes.'_children_h', 'kids', $this->kids, $this->valuesToBeSet['kids']);
		$output .= '</span>';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
		$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= '</div>';
		$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	
	private function renderHotelsSearchbox300x533() {
		$output =
		'<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_city_h">City:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'airport')).'" id="tgsb_'.self::$nrOfBoxes.'_city_h" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addASH asTo" />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_h">Check-In:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('hotels_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_h') : 
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'start_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_h" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_h">Check-Out:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('hotels_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_h', false) :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'end_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_h" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
				$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_h">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_h', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_h">Rooms:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'no_room')), 'tgsb_'.self::$nrOfBoxes.'_rooms_h', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_h">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'no_child')), 'tgsb_'.self::$nrOfBoxes.'_children_h', 'kids', $this->kids, $this->valuesToBeSet['kids']);
				$output .= '</span>';

		$output .= '<div class="mrcList nod"></div>';
		$output .= '<div class="help">&nbsp;</div>';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
		$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);		
		return $output;
	}
	
	private function renderHotelsSearchbox728x90() {
		$output .= '<div class="formContent">';
		$output .= '<div>
				<label for="tgsb_'.self::$nrOfBoxes.'_city_h">City:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'airport')).'" id="tgsb_'.self::$nrOfBoxes.'_city_h" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addASH asTo" /><br />
				<span>
					<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_h">Check-In:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('hotels_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_h') : 
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'start_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_h" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '</span>
				<span class="r">
					<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_h">Check-Out:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('hotels_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_h', false) :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'end_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_h" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
				$output .= '</span>
			</div>
			<span class="akstt">
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_h">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_h', 'adults', $this->adults, $this->valuesToBeSet['adults']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_h">Rooms:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'no_room')), 'tgsb_'.self::$nrOfBoxes.'_rooms_h', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_h">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'no_child')), 'tgsb_'.self::$nrOfBoxes.'_children_h', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span></span>';

		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
		$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= '<div class="spcr">&nbsp;</div>';
		$output .= '</div>';
		$output .= '<div class="mrcList nod"></div>';
		$output .= $this->renderSubmitButton();
		$output .= '<div class="spcr">&nbsp;</div>';
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);		
		return $output;
	}

	private function renderHotelsSearchboxDynamic() {
		$output = '<div class="formContent">';
		$output .='
				<label for="tgsb_'.self::$nrOfBoxes.'_city_h">City:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'airport')).'" id="tgsb_'.self::$nrOfBoxes.'_city_h" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addASH asTo" />
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_h">Check-In:</label>';
		$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'start_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_h" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
		$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_h">Check-Out:</label>';
		$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'end_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_h" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />';
		$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_h">Adults:</label>';
		$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_h', 'adults', $this->adults, $this->valuesToBeSet['adults']);
		$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_h">Rooms:</label>';
		$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'no_room')), 'tgsb_'.self::$nrOfBoxes.'_rooms_h', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
		$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_h">Kids:</label>';
		$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'no_child')), 'tgsb_'.self::$nrOfBoxes.'_children_h', 'kids', $this->kids, $this->valuesToBeSet['kids']);
		$output .= '</span>';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
		$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= '</div>';
		$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	
	
/* Packages Renderisation Functions */

	/* we don't have a 160x600 packages searchbox as I saw the screenshot got from Peter */
	
	private function renderPackagesSearchbox300x250() {
		$output = '<div class="formContent">';
		$output .= '<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_p">From:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'city1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_p" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_p">To:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'arr')).'" id="tgsb_'.self::$nrOfBoxes.'_to_p" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_p">Depart:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_p" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_p">Return:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_p" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />';
				$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_p">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'adults')), 'tgsb_'.self::$nrOfBoxes.'_adults_p', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .='</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_p">Rooms:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'rooms')), 'tgsb_'.self::$nrOfBoxes.'_rooms_p', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
				$output .= '
			</span>
			<span class="s kd">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_p">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'childrens')), 'tgsb_'.self::$nrOfBoxes.'_children_p', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_p">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'seniors')), 'tgsb_'.self::$nrOfBoxes.'_seniors_p', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
				$output .= '</div>';
				$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
				$output .= $this->renderSubmitButton();	
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	
	private function renderPackagesSearchbox300x533() {
		$output = 
		'<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_p">From:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'city1'/*'inp_dep_arp_cd_1'*/)).'" id="tgsb_'.self::$nrOfBoxes.'_from_p" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_p">To:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'arr'/*'inp_arr_arp_cd_1'*/)).'" id="tgsb_'.self::$nrOfBoxes.'_to_p" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_p">Depart:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('packages_depeparture_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_p') :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_p" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_p">Return:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('packages_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_p', false) :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_p" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
				$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_p">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'adults'/*'inp_adult_pax_cnt'*/)), 'tgsb_'.self::$nrOfBoxes.'_adults_p', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .='</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_p">Rooms:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'no_rooms')), 'tgsb_'.self::$nrOfBoxes.'_rooms_p', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
				$output .= '
			</span>
			<span class="s kd">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_p">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'childrens'/*'inp_child_pax_cnt'*/)), 'tgsb_'.self::$nrOfBoxes.'_children_p', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_p">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'seniors'/*'inp_senior_pax_cnt'*/)), 'tgsb_'.self::$nrOfBoxes.'_seniors_p', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span>';
				$output .= '<div class="mrcList nod"></div>';
				$output .= '<div class="help">&nbsp;</div>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
				$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	
	
	private function renderPackagesSearchbox728x90() {
		$output = '<div class="formContent">';
		$output .= '<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_p">From:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'city1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_p" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
				<label for="tgsb_'.self::$nrOfBoxes.'_to_p">To:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'arr')).'" id="tgsb_'.self::$nrOfBoxes.'_to_p" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_p">Depart:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('packages_depeparture_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_p') :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_p" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_p">Return:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('packages_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_p', false) :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_p" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
				$output .= '<br />
			</span>
			<span class="akstt">
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_p">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'adults')), 'tgsb_'.self::$nrOfBoxes.'_adults_p', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .='</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_p">Rooms:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'rooms')), 'tgsb_'.self::$nrOfBoxes.'_rooms_p', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_p">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'childrens')), 'tgsb_'.self::$nrOfBoxes.'_children_p', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_p">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'seniors')), 'tgsb_'.self::$nrOfBoxes.'_seniors_p', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span></span>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
			$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= '</div>';
		$output .= '<div class="mrcList nod"></div>';
		$output .= $this->renderSubmitButton();	
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	
	private function renderPackagesSearchboxDynamic() {
		$output = '<div class="formContent">';
		$output .= '<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_p">From:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'city1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_p" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_p">To:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'arr')).'" id="tgsb_'.self::$nrOfBoxes.'_to_p" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_p">Depart:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_p" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_p">Return:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_p" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />';
				$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_p">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'adults')), 'tgsb_'.self::$nrOfBoxes.'_adults_p', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .='</span>
			<span class="s rm">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_p">Rooms:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'rooms')), 'tgsb_'.self::$nrOfBoxes.'_rooms_p', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
				$output .= '
			</span>
			<span class="s kd">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_p">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'childrens')), 'tgsb_'.self::$nrOfBoxes.'_children_p', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_p">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'seniors')), 'tgsb_'.self::$nrOfBoxes.'_seniors_p', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
				$output .= '</div>';
				$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
				$output .= $this->renderSubmitButton();	
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}

/* Cars Renderisation Functions */

	private function renderCarsSearchbox160x600(){
		$output = '
				<label for="tgsb_'.self::$nrOfBoxes.'_from_c">Pick-Up:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'where')).'" id="tgsb_'.self::$nrOfBoxes.'_from_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asFrom" />
				<label for="tgsb_'.self::$nrOfBoxes.'_to_c">Drop-Off:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'cr_drp_off_cty_name')).'" id="tgsb_'.self::$nrOfBoxes.'_to_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" />
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_c">Pick-Up Date:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('cars_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_c') :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_c" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_c">Drop-Off Date:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('cars_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_c', false) :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_c" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
			$output .= '<div class="mrcList nod"></div>';
			$output .= '<div class="help">&nbsp;</div>';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
			$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);		
		return $output;
	}


	private function renderCarsSearchbox300x250(){
		$output = '<div class="formContent">';
		$output .= '<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_c">Pick-Up:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'where')).'" id="tgsb_'.self::$nrOfBoxes.'_from_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_c">Drop-Off:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'cr_drp_off_cty_name')).'" id="tgsb_'.self::$nrOfBoxes.'_to_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_c">Pick-Up Date:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_c" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_c">Drop-Off Date:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_c" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />';
				$output .= '<br />
			</span>
			';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
			$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
			$output .= '</div>';
			$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	
	private function renderCarsSearchbox300x533(){
		$output = '<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_c">Pick-Up:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'where')).'" id="tgsb_'.self::$nrOfBoxes.'_from_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_c">Drop-Off:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'cr_drp_off_cty_name')).'" id="tgsb_'.self::$nrOfBoxes.'_to_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_c">Pick-Up Date:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('cars_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_c') :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_c" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_c">Drop-Off Date:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('cars_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_c', false) :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_c" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
				$output .= '<br />
			</span>
			';
			$output .= '<div class="mrcList nod"></div>';
			$output .= '<div class="help">&nbsp;</div>';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
			$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);		
		return $output;
	}
	
	private function renderCarsSearchbox728x90() {
		$output = '<div class="formContent">';
		$output .= '<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_c">Pick-Up:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'where')).'" id="tgsb_'.self::$nrOfBoxes.'_from_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asFrom" /><br />
				<label for="tgsb_'.self::$nrOfBoxes.'_to_c">Drop-Off:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'cr_drp_off_cty_name')).'" id="tgsb_'.self::$nrOfBoxes.'_to_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>			
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_c">Pick-Up Date:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('cars_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_c') :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_c" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '<br />
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_c">Drop-Off Date:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('cars_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_c', false) :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_c" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
				$output .= '<br />
			</span>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= '</div>';
		$output .= '<div class="mrcList nod"></div>';		
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	
	private function renderCarsSearchboxDynamic(){
		$output = '<div class="formContent">';
		$output .= '<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_c">Pick-Up:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'where')).'" id="tgsb_'.self::$nrOfBoxes.'_from_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_c">Drop-Off:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'cr_drp_off_cty_name')).'" id="tgsb_'.self::$nrOfBoxes.'_to_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_c">Pick-Up Date:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_c" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_c">Drop-Off Date:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_c" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />';
				$output .= '<br />
			</span>
			';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
			$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
			$output .= '</div>';
			$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	

	/**	HTML for the submit button	*/
	private function renderSubmitButton() {
		
		$button = array(
			'160x600'	=> '<input class="tgsb_submit_button" type="submit" value="find sites" />', 
			'300x250'	=> '<input class="tgsb_submit_button" type="submit" value="find sites" />', 
			'300x533'	=> '<input class="tgsb_submit_button" type="submit" value="find sites" />', 
			'728x90'	=> '<input class="tgsb_submit_button" type="submit" value="find sites" />',
			'dynamic'	=> '<input class="tgsb_submit_button" type="submit" value="find sites" />',
			);
			
		if(!empty($this->atts['defaultSettings']))
			return '<img src="'.plugins_url('/images/tg_searchboxes/search_button1.png', TG_SEARCHBOXES__FILE__).'" width="79" height="30" alt="compare" class="compareImage" />';
			
		if(!array_key_exists($this->atts['size'], $button) || empty($button[$this->atts['size']]))
			return '<input class="tgsb_submit_button" type="image" src="'.plugins_url('/images/tg_searchboxes/search_button1.png', TG_SEARCHBOXES__FILE__).'" />';
		return $button[$this->atts['size']];
	}

	/**	rendering the html code needed for a select tag	*/
	private function createSelectTag($selectTagName, $selectTagId, $selectTagClass, $options = array(), $selectedOption, $useOptKeyAsValue = false) {
		if(empty($options))
			return '';
		$output = '';
		foreach($options as $idx => $optionContent) {
			$output .= '<option value="'.esc_attr((($useOptKeyAsValue) ? $idx : $optionContent)).'"'.(($selectedOption == $optionContent ) ? ' selected="selected"' : '').'>'.esc_attr($optionContent).'</option>';
		}
		
		$output = '<select'.((!empty($selectTagName)) ? ' name="'.$selectTagName.'"' : '').((!empty($selectTagId)) ? ' id="'.$selectTagId.'"' : '').((!empty($selectTagClass)) ? ' class="'.$selectTagClass.'"' : '').' >'.$output.'</select>';
		return $output;
	}
	
	/**	setting the date input for the settings page	*/
	function date_input($optionName, $tagId, $departFlag = true) {
		if(empty($optionName))
			return false;
		$dr_dates = $departFlag ? $this->controller->departure_dates : $this->controller->return_dates;
		$output .= "<select ".($departFlag ? "class='depDate'" : "class='retDate'")." name='tg_searchboxes_options[".$optionName."]' ".((!empty($tagId)) ? " id='".$tagId."'" : '').">";
		foreach($dr_dates as $dr_date) {
			$output .= "<option value='".esc_attr($dr_date)."'".(($this->controller->options[($departFlag ? 'departure_date' : 'return_date')] == $dr_date) ? " selected='selected'" : '').">".esc_attr($dr_date)."</option>";
		}
		$output .= "</select>";
		return $output;
	}
	
		/**
	 * gets an array with the details substracted from a date string
	 * @param String the date string MM/DD/YYYY if search_opt is us, DD/MM/YYYY otherwise
	 * @return Array with 3 entry, as follows: array(year, month, day, y/m/d)
	*/
	function getDateDetails($date){
		if($this->controller->options['date_format'] == 'm/d/Y') {
			if(!preg_match('/^([01]?[0-9])\/([0-3]?[0-9])\/(20[0-9]{2})$/',$date,$match))
				return false;
			return array((int)$match[3],(int)$match[1],(int)$match[2],"$match[1]/$match[2]/$match[3]");
		};
		if(!preg_match('/^([0-3]?[0-9])\/([01]?[0-9])\/(20[0-9]{2})$/',$date,$match))
			return false;
		return array((int)$match[3],(int)$match[2],(int)$match[1],"$match[1]/$match[2]/$match[3]");
	} 

	function __destruct() {
		unset($this->atts);
	}
}
?>