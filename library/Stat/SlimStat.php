<?php

class Stat_SlimStat  {
	
	/** Application version */
	static function app_version() {
		return '1.0.0';
	}
	
	/**
	 * Confirms the existence of the GeoIP database (http://www.maxmind.com/)
	 */
	static function is_geoip_installed() {
		return ( file_exists( SEED_LIB_ROOT.'/Stat/geoip.php' ) &&
		         file_exists( SEED_LIB_ROOT.'/Stat/GeoIP.dat' ) );
	}
	
	static function set_timezone( $_tz ) {
		if ( function_exists( 'date_default_timezone_set' ) ) {
			date_default_timezone_set( $_tz );
		} else {
			putenv( 'TZ='.$_tz );
		}
	}
	
	static function esc( $_str ) {
		if ( version_compare( phpversion(), '4.3.0', '>=' ) ) {
			return @mysql_real_escape_string( $_str );
		} else {
			return @mysql_escape_string( $_str );
		}
	}
	
	static function parse_version( $_raw_version, $_parts=2 ) {
		$version_numbers = explode( '.', $_raw_version );
		$value = '';

		for ( $x=0; $x<$_parts; $x++ ) {
			if ( sizeof( $version_numbers ) > $x ) {
				if ( $value != '' ) {
					$value .= '.';
				}
				$value .= $version_numbers[$x];
			}
		}

		return $value;
	}
	
	static function local_time_fields( $_fields ) {
		$gmdate = gmmktime(
			$_fields['hr'],
			( array_key_exists( 'mi', $_fields ) ) ? $_fields['mi'] : 0,
			( array_key_exists( 'sc', $_fields ) ) ? $_fields['sc'] : 0,
			$_fields['mo'],
			$_fields['dy'],
			$_fields['yr'] );
		list( $yr, $mo, $dy, $hr, $mi, $sc ) = explode( ' ', date( 'Y n j G i s', $gmdate ) );
		return array( 'yr' => $yr, 'mo' => $mo, 'dy' => $dy, 'hr' => $hr, 'mi' => $mi, 'sc' => $sc );
	}
	
	static function utf8_encode( $_str ) {
		$encoding = mb_detect_encoding( $_str );
		if ( $encoding == false || strtoupper( $encoding ) == 'UTF-8' || strtoupper( $encoding ) == 'ASCII' ) {
			return $_str;
		} else {
			return iconv( $encoding, 'UTF-8', $_str );
		}
	}
	
	static function build_cookie( $_username, $_password ) {
		return sha1( $_username.' '.$_password.' '.SlimStat::anonymise_ip( $_SERVER['REMOTE_ADDR'], '255.255.255.0' ) );
	}
	
	/**
	 * Anonymise an IP address.
	 * @param $_addr An IP address in string format, e.g. '192.168.1.1'.
	 * @param @_mask A mask in string format, e.g. '255.255.255.0'.
	 * @return IP address as string, masked with zeroes, e.g. '192.168.1.0'.
	 */
	static function anonymise_ip( $_addr, $_mask ) {
		$addr = long2ip( ip2long( $_addr ) & ip2long( $_mask ) );
		if ( $addr == '0.0.0.0' ) {
			$addr = '';
		}
		return $addr;
	}
	
	/**
	 * Determines the language used by the visitor’s browser.
	 */
	static function determine_language() {
		if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
			// Capture up to the first delimiter (comma found in Safari)
			preg_match( "/([^,;]*)/", $_SERVER['HTTP_ACCEPT_LANGUAGE'], $langs );
			$lang_choice = $langs[0];
		} else {
			$lang_choice = '';
		}
		
		return strtolower( $lang_choice );
	}
}
