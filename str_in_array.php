<?php
/**
 * A replacement for the worthless piece of shit called in_array()
 */
  
if( !function_exists('str_in_array') ) {
	function str_in_array( $string, $array, $ignore_case = false ) {
		$return = false;
		
		if( !is_string( $string ) ) { // try to convert given value to string
			switch( gettype($string) ) {
				case 'integer':
				case 'double':
				case 'float':
					$string = (string) "$string";
					break;
				case 'boolean':
					$string = (string) ($string === false ? '1' : '0');
					break;
			}
				
		}
		
		
		
		if( is_string( $string) != false && is_array($array) != false && !empty($array) && !empty($string) ) {
			$needle = $string;
			$haystack = implode(' ', $array);
			
			$result = ( $ignore_case != false ? stripos( $haystack, $needle) : strpos( $haystack, $needle ) );
			
			if($result !== false) {
				$return = true;
			}
			
		}
		
		
		return $return;
	}
}

/**
 * Simple ignore case wrapper for str_in_array()
 * @see str_in_array
 */

if( !function_exists('stri_in_array') && function_exists('str_in_array') !== false ) {
	function stri_in_array( $string, $array ) {
		return str_in_array( $string, $array, true );
	}
}



/**
 * Many people look for in_string which does not exist in PHP, so, here's the most efficient form of in_string() (that works in both PHP 4/5) that I can think of.
 * @see http://www.php.net/manual/en/function.strpos.php#45088
 */
if( !function_exists('in_string') ) {
 
	function in_string($needle, $haystack, $insensitive = 0) {
		if ($insensitive) {
			return (false !== stristr($haystack, $needle)) ? true : false;
		} else {
			return (false !== strpos($haystack, $needle))  ? true : false;
		}
	}
	
}
