<?php
/**
 * IP Manager Class.
 *
 * @package   KWAccessAdminNotification
 * @author    Vincenzo Casu <vincenzo.casu@gmail.com>
 * @link      https://kolorweb.it
 * @copyright GPL v2 or later
 */

namespace KolorWeb\KWAccessAdminNotification;

if ( ! defined( 'KWACCESSADMINNOTIFICATION_BASE' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}


/**
 * Handle IP Data
 *
 * @since 1.0.1
 */
class KolorWeb_IP_Manager {

	/**
	 * Get IP Address Data Details.
	 *
	 * @return array $ip_data
	 *
	 * @since 1.0.1
	 */
	public function get_ip_data_details() {
		$ip = $this->get_ip_address();
		return ! empty( $ip ) ? $this->get_ip_data( $ip ) : '';
	}


	/**
	 * Get ip address.
	 *
	 * @return string $ip Current IP.
	 *
	 * @since 1.0.1
	 */
	public function get_ip_address() {
		$ip = '';
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) && $this->is_valid_ip_address( \sanitize_text_field( \wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) ) ) ) {
			// check for shared ISP IP.
			$ip = \sanitize_text_field( \wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// check for IPs passing through proxy servers.
			// check if multiple IP addresses are set and take the first one.
			$ip_address_list = $this->clean_data( explode( ',', wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ), 'sanitize_text_field' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			foreach ( $ip_address_list as $current_ip ) {
				if ( $this->is_valid_ip_address( $current_ip ) ) {
					$ip = $current_ip;
					break;
				}
			}
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED'] ) && $this->is_valid_ip_address( \sanitize_text_field( \wp_unslash( $_SERVER['HTTP_X_FORWARDED'] ) ) ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] ) && $this->is_valid_ip_address( \sanitize_text_field( \wp_unslash( $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] ) ) ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_FORWARDED_FOR'] ) && $this->is_valid_ip_address( \sanitize_text_field( \wp_unslash( $_SERVER['HTTP_FORWARDED_FOR'] ) ) ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_FORWARDED_FOR'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_FORWARDED'] ) && $this->is_valid_ip_address( \sanitize_text_field( \wp_unslash( $_SERVER['HTTP_FORWARDED'] ) ) ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_FORWARDED'] ) );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) && $this->is_valid_ip_address( \sanitize_text_field( \wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}
		return $ip;
	}

	/**
	 * Clean Data
	 *
	 * @since 1.0.1
	 *
	 * @param array  $ip_list IP List array.
	 * @param string $callback calback function.
	 * @return array
	 */
	public function clean_data( $ip_list, $callback ) {

		$new_data = array();

		foreach ( $ip_list as $key => $val ) {
			if ( is_array( $val ) ) {
				$new[ $key ] = $this->clean_data( $val, $callback );
			} else {
				$new[ $key ] = call_user_func( $callback, $val );
			}
		}

		return $new_data;

	}


	/**
	 * Validate IP Address.
	 *
	 * @param string $ip Current IP.
	 * @return bool valid ip check.
	 * @since 1.0.1
	 */
	public function is_valid_ip_address( $ip ) {
		return filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE );
	}

	/**
	 * Get IP Address Data.
	 *
	 * @param string $ip Current IP.
	 * @return array $ip_who_is_response IP data response.
	 *
	 * @since 1.0.1
	 */
	public function get_ip_data( $ip ) {
		$ip_who_is_response = wp_remote_get( 'http://ipwhois.app/json/' . $ip );
		return isset( $ip_who_is_response['body'] ) ? json_decode( \wp_unslash( $ip_who_is_response['body'] ), true ) : array();
	}
}
