<?php
/**
 * Element Include: VersionCheck
 * Methods to check if current version is the latest
 *
 * @package    NoNumber! Elements
 * @version    v1.0.4
 *
 * @author     Peter van Westen <peter@nonumber.nl>
 * @link       http://www.nonumber.nl
 * @copyright  Copyright (C) 2009 NoNumber! All Rights Reserved
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Version Check Class (Include file)
 */
class NoNumberVersionCheck
{
	function setMessage( $version_file = '', $version_path = '', $version_url = '', $download_url = '' )
	{
		global $mainframe;

		$messageQueue = $mainframe->getMessageQueue();

		if ( $version_file ) {
			$_version = NoNumberVersionCheck::getVersions( $version_file, $version_path, $version_url );
			$_has_newer = NoNumberVersionCheck::checkVersion( $_version );
			if ( $_has_newer ) {
				// set message
				$_msg = JText::sprintf( 'A newer version is available', $download_url, $_version['1'] );
				$_message_set = 0;
				foreach ( $messageQueue as $_queue_message ) {
					if ( $_queue_message['type'] == 'notice' && $_queue_message['message'] == $_msg ) {
						$_message_set = 1;
						break;
					}
				}
				if ( !$_message_set ) {
					$mainframe->enqueueMessage( $_msg, 'notice' );
				}
			}
		}
	}

	function getVersions( $version_file = '', $version_path = '', $version_url = '' )
	{
		$version = array( '', '' );

		if ( !$version_file ) {
			return $version;
		}

		$_cookieName	= JUtility::getHash( $version_file.'_version' );

		// open the current version file
		$_current_version_file = @fopen( dirname( __FILE__ ).DS.$version_path.$version_file, 'r' );

		if ( !$_current_version_file ) {
			return $version;
		}
		// read the contents of the version files ( 10 chars must be enough)
		$_current_version = NoNumberVersionCheck::cleanString( fread( $_current_version_file, 10 ) );

		if ( !$_current_version ) {
			return $version;
		}

		$version['0'] = $_current_version;
		$version['1'] = $_current_version;

		$_cookie = JRequest::getString( $_cookieName, '', 'COOKIE' );

		if ( $_cookie ) {
			$version[1] = NoNumberVersionCheck::cleanString( $_cookie );
			return $version;
		}

		// the url of the new version file
		$_new_version_url	= $version_url.'/'.$version_file;

		$_new_version = '';
		$_timeout = 1;

		//Version Checker
		if( function_exists( 'curl_init' ) ){
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $_new_version_url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, $_timeout );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 1 );
			$_new_version = curl_exec( $ch );
			curl_close( $ch );
		} else {
			// Set timeout
			// Doesn't work in SAFE_MODE ON
			$_old_timeout = ini_set( 'default_socket_timeout', $_timeout );
			$_new_version_file = @fopen( $_new_version_url, 'r' );
			if ( $_new_version_file ) {
				ini_set( 'default_socket_timeout', $_old_timeout );
				stream_set_timeout( $_new_version_file, $_timeout );
				stream_set_blocking( $_new_version_file, 0 );
				$_new_version = fread( $_new_version_file, 10 );
			}
		}

		if ( $_new_version ) {
			$version['1'] = NoNumberVersionCheck::cleanString( $_new_version );
		}

		$_lifetime = time() + 60*60; // 1 hour
		setcookie( $_cookieName, $version['1'], $_lifetime );

		return $version;
	}

	function checkVersion( $version )
	{
		$has_newer = 0;

		if ( !$version['0'] || !$version['1'] ) {
			return $has_newer;
		}

		$_v_cur = NoNumberVersionCheck::convertToNumberArray( $version['0'] );
		$_v_new = NoNumberVersionCheck::convertToNumberArray( $version['1'] );

		$_count = count( $_v_cur );
		for ( $_i = 0; $_i < $_count; $_i++ ) {
			 if ( $_v_cur[$_i] != $_v_new[$_i] ) {
			 	if ( $_v_cur[$_i] < $_v_new[$_i] ) {
					$has_newer = 1;
				}
				break;
			}
		}

		return $has_newer;
	}

	function convertToNumberArray( $nr )
	{
		/*
		 * v1.0.0 is newer that v1.0.0a
		 * because v1.0.0a is the first development version of v1.0.0
		 */
		$_nr_array = array( 0, 0, 0, 0, 0 );
		$nr = explode( '.', $nr );
		$_count = count( $_nr_array );
		for( $_i = 0; $_i < $_count; $_i++ ) {
			if ( !isset( $nr[$_i] ) || $nr[$_i] == 0 ) {
				$_nr_part = 0.1;
			} else {
				$_nr_part = $nr[$_i];
				if ( is_numeric( $nr[$_i] ) ) {
						$_nr_part += 0.1;
				} else {
					$_nr_part = preg_replace( '#^([0-9]*)#', '\1.', $_nr_part );
					$_nr_part_array = explode( '.', $_nr_part );
					$_nr_part = intval( $_nr_part_array['0'] );
					if ( isset( $_nr_part_array['1'] ) && $_nr_part_array['1'] ) {
						// if letter is set, convert it to a number and ad it as a tenthousandth
						$_nr_part += ( ord( $_nr_part_array[1] ) ) / 100000;
					} else {
						// if no letter is set, ad a tenth
						$_nr_part += 0.1;
					}
				}
			}
			$_nr_array[$_i] = $_nr_part;
		}
		return $_nr_array;
	}

	function cleanString( $str = '' )
	{
		$str = preg_replace( '#[^0-9a-z\.]#', '', strtolower( $str ) );
		return $str;
	}
}