<?php
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden', true, 403 );
	exit( '<!DOCTYPE html><html><body><h1>No direct access allowed</h1></body></html>' );
}

class YRPlugin {

	const short = 'yr';   	// Name of shortcode tag
	const hdrft = false;  	// Header and footer included?
	const cache = 'cache';	// Name of cache folder
	const curl  = true;   	// Try to use cURL extension?

	public function display( $atts, $content = null ) {
		extract( shortcode_atts(
			array(
				'url'     => 'http://www.yr.no/place/Norway/Oslo/Oslo/Meteorologisk_institutt',
				'name'    => null,
				'banner'  => false,
				'text'    => false,
				'links'   => false,
				'table'   => true,
				'maxage'  => 1200,
				'timeout' => 10,
			),
			$atts ) );
		// Sanitize variables:
		$url = untrailingslashit( trim ( $url ) );
		if ( strtolower( $table ) == 'no' || strtolower( $table ) == 'false' )
			$table = false;
		$maxage  = intval( $maxage );
		$timeout = intval( $timeout );

		$GLOBALS['yr_datadir'] = trailingslashit( dirname( __FILE__ ) ) . self::cache;
		$xml = new YRComms;
		$yr  = new YRDisplay;
		return $yr->generateHTMLCached( $url, $name, $xml, $url, self::curl, self::hdrft, self::hdrft, $banner, $text, $links, $table, $maxage, $timeout, $target );
	}

	public function register_shortcodes() {
		add_shortcode( self::short, array( $this, 'display' ) );
	}

	public function __construct() {
		add_action( 'init', array( $this, 'register_shortcodes' ) ) ;
	}

}