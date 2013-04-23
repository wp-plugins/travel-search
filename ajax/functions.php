<?php
/**
 * PHP Proxy Script for AJAX requests to Travelgrove from the Travel-Search plugin
 *
 * @package Travel-Search
 * @subpackage AJAX Requests
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */

/**	defining base path to Travelgrove Server	*/
if(!defined('TG_SERVER_BASEPATH'))
	define('TG_SERVER_BASEPATH', 'http://www.travelgrove.com/');
/**	defining rel path to Provider Logos	*/
if(!defined('TG_PROVIDER_IMAGES'))
	define('TG_PROVIDER_IMAGES', 'images/merchants_small/');
/**	defining rel path to Autosuggestion AJAX handler	*/
if(!defined('TG_ASAJAX_PATH'))
	define('TG_ASAJAX_PATH', 'ajax/');
/**	defining rel path to Provider AJAX handler	*/
if(!defined('TG_PROVIDER_PATH'))
	define('TG_PROVIDER_PATH', 'ajax/merchants/wpplugin/');

/**	sending the headers in the AJAX response	*/
function setHeaders($headers) {
	if(empty($headers))
		return false;
	foreach($headers as $header) {
		// WHAT & WHY: the Content-Length should not be set because it is changed in the file merchants.php the content is manipulated by the function merchantsJSONToHTML() which can generate a bigger Content-Length
		// WHO & WHEN: Cipri on the 24th of April 2012
		if (preg_match('/^Content-Length:/',$header))
			continue;
		header($header);
	}
	return true;
}
/**	actual HTTP PHP proxy script	*/
function http_post($url, $data) {
	if(!$url)
		return false;
	if(!empty($data) && !empty($data['action'])) {
		// stripslashes should be used if on the action parameter is present an ' then it will be sent like this \' which is wrong so the slash should be striped
		if(get_magic_quotes_gpc())
			$data['action']	= stripslashes($data['action']);
	}
	/*	building up the query string	*/
	$data_url		= http_build_query($data);
	// print('dataURL: '.$data_url."<br>\n");
	$data_len		= strlen($data_url);
	/*	headers that should be pass to Travelgrove	*/
	$headersToBeSent	= array('Accept', 'Accept-Encoding', 'Accept-Language', 'Content-Length', 'Content-Type', 'User-Agent', 'Referer', 'Connection', 'X-Requested-With');
	$headersString		= '';
	/*	getting the headers that were sent for this request	*/
	$headers		= apache_request_headers();
	/*	building up the headers that should be passed to Travelgrove	*/
	foreach($headers as $headerKey => $headerValue) {
		if(!in_array($headerKey, $headersToBeSent))
			continue;
		if($headerKey == 'Connection') {
			$headersString .= "Connection: close\r\n";
			continue;
		}
		if($headerKey == 'Content-Length') {
			$headerValue = $data_len;
		}
		$headersString	.= $headerKey.': '.$headerValue."\r\n";
	}
	$headersString	= (empty($headersString)) ? "Connection: close\r\nContent-Length: $data_len\r\n" : $headersString;
	return array(	'content'	=> file_get_contents (
   					$url,
					false,
					stream_context_create(
						array(	'http'	=> array('method'	=> 'POST',
									'header'	=> $headersString,
									'content'	=> $data_url)
						)
            				)
				),
			/** $http_response_header reserved variable will contain the headers automatically	*/
            		'headers'	=> $http_response_header);
}

/**	trasforming the response of JSON into JSON containing the merchants as HTML, to render the merchants	*/
function merchantsJSONToHTML($jsonData, $sbSize) {
	if(empty($jsonData))
		return (string)false;
	$responseArray	= @json_decode($jsonData, true);
	if(empty($responseArray) || empty($responseArray['merchants']))
		return (string)false;
	$out	= array();
	$output	= '';
	$i	= 1;
	foreach($responseArray['merchants'] as $merchant) {
		$output .=	'<span title="'.$merchant['name'].'" rel="'.$merchant['code'].'"'.
					($sbSize != '160x600' && $i%2 == 1 ? ' class="mrr"' : '').'>'.
					'<img width="86" height="21" alt="'.$merchant['url'].'" src="'.TG_SERVER_BASEPATH.TG_PROVIDER_IMAGES.$merchant['logo'].'" />'.
				'</span>';
		$i++;
	}
	$out['merchants']	= $output;
	$out['trackingPixel']	= $responseArray['trackingPixel'];
	return $out;
}


// WHAT & WHY: it seams that the function is not present on all php versions, but on php.net it says (PHP 4 >= 4.3.0, PHP 5) that's why we have to implement it here if it does not exist
// WHO & WHEN: Cipri on the 24th of April 2012
if( !function_exists('apache_request_headers') ) {
	function apache_request_headers() {
		$arh = array();
		$rx_http = '/\AHTTP_/';
		foreach($_SERVER as $key => $val) {
			if( preg_match($rx_http, $key) ) {
				$arh_key = preg_replace($rx_http, '', $key);
				$rx_matches = array();
				// do some nasty string manipulations to restore the original letter case
				// this should work in most cases
				$rx_matches = explode('_', $arh_key);
				if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
					foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
						$arh_key = implode('-', $rx_matches);
				}
				$arh[$arh_key] = $val;
			}
		}
	return( $arh );
	}
}
?>