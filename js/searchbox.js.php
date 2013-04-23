<?php
$wpLoad	= dirname(__FILE__).'/../../../../wp-load.php';
if (!file_exists($wpLoad))
	exit('Directory structure doesn\'t follow WP standards!');
require_once($wpLoad);
?>