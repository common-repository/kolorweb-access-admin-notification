<?php
/**
 * KolorWeb Access Admin Notification
 *
 * @package           KWAccessAdminNotification
 * @author            Vincenzo Casu
 * @copyright         KolorWeb di Vincenzo Casu
 * @license           GPL v2 or later
 *
 * @wordpress-plugin
 * Plugin Name:       KolorWeb Access Admin Notification
 * Plugin URI:        https://github.com/vincenzocasu/kolorweb-access-admin-notification
 * Description:       Extreme rescue for unauthorized admin logins
 * Version:           1.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Vincenzo Casu
 * Author URI:        https://kolorweb.it/
 * Text Domain:       kolorweb-access-admin-notification
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'WPINC' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}

/**
 * Define Constants.
 */
define( 'KWACCESSADMINNOTIFICATION_VERSION', '1.0.1' );
define( 'KWACCESSADMINNOTIFICATION_BASE', dirname( __FILE__ ) . '/' );
define( 'KWACCESSADMINNOTIFICATION_URL', plugin_dir_url( __FILE__ ) );

$deps = array(
	'kolorweb-access-admin-notification-manager',
	'kolorweb-ip-manager',
);

foreach ( $deps as $class ) {
	$f = KWACCESSADMINNOTIFICATION_BASE . '/libs/class-' . $class . '.php';
	if ( file_exists( $f ) ) {
		require_once $f;
	}
}

use KolorWeb\KWAccessAdminNotification\KolorWeb_Access_Admin_Notification_Manager;

new KolorWeb_Access_Admin_Notification_Manager();
