<?php
/*
Plugin Name: Weather forecast shortcode
Plugin URI: http://nytt-nettsted.no/yr/
Description: Weather forecast from yr.no, delivered by the Norwegian Meteorological Institute and the NRK. Creates a shortcode [yr] to display a weather forecast for a place (or city)
as a table on any page or post. It covers over <strong>9 million</strong> places all over the world, including 900 000 places in Norway. Based on the free weather data and follows the
terms and conditions stated by yr.no. More info at <a href="http://om.yr.no/verdata/free-weather-data/">yr.no</a>. Se Installtion tab for upgrade problems.
Author: Knut Sparhell and Lennart André Rolland
Version: 1.4
Author URI: http://nytt-nettsted.no/om-oss/
*/

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden', true, 403 );
	exit( '<!DOCTYPE html><html><body><h1>No direct access allowed</h1></body></html>' );
}

require_once 'class.yrcomms.php';
require_once 'class.yrdisplay.php';
require_once 'class.yrplugin.php';
new YRPlugin;