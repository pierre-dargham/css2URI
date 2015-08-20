<?php
/**
 * Script Name:         css2URI
 * Script URI:          https://github.com/pierre-dargham/css2URI
 * Description:         Convert your images urls into data-uri base64 strings in a css file
 * Author:              Pierre Dargham
 * Author URI:          https://github.com/pierre-dargham/
 *
 * Version:             1.0.0
 */

/* ------------ INSTRUCTIONS --------*/

/**
 *
 * 1. Place this script in the same directory of your stylesheet file
 *
 * 2. Change CSS_FILENAME in the configuration below to match your css stylesheet
 *
 * 3. Open a terminal, go to the right directory, and run :
 *
 * 4. `php css2URI.php > my-new-stylesheet.css`
 *
 * 5. Or run it from a web-browser and paste the result in a new css file
 *
 */


/* ------------ CONFIG --------------*/

define( 'CSS_FILENAME', 'style.css' );


/* ------------ SCRIPT --------------*/

$file = realpath( dirname( __FILE__ ) ) . '/' . CSS_FILENAME;
$css_str = file_get_contents( $file );

$urls = get_images_url( $css_str );
$css = get_css_with_data_uri( $css_str, $urls );

echo $css;


/* ------------ FUNCTIONS -----------*/

function get_images_url( $css_str ) {
	preg_match_all( '/url\(([\s])?([\"|\'])?(.*?)([\"|\'])?([\s])?\)/i', $css_str, $matches, PREG_PATTERN_ORDER );
	if ( $matches ) {
		foreach ( $matches[3] as $key => $match ) {
	    	if ( ! is_image_url( $match ) ) {
	    		unset( $matches[3][ $key ] );
	    	}
		}
	}
	return $matches[3];
}

function is_image_url( $url ) {
	if ( in_array( strtoupper( substr( $url, -4 ) ), array( '.PNG', '.JPG', '.GIF' ) ) ) {
		return true;
	}
	if ( in_array( strtoupper( substr( $url, -5 ) ), array( '.JPEG' ) ) ) {
		return true;
	}
	return false;
}

function get_css_with_data_uri( $css_str, $urls ) {
	foreach (  $urls as $url ) {
		$path = realpath( dirname( __FILE__ ) ) . '/' . $url;
		$type = pathinfo( $path, PATHINFO_EXTENSION );
		$data = file_get_contents( $path );
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode( $data );
		$css_str = str_replace( $url, $base64, $css_str );
	}

	return $css_str;
}
