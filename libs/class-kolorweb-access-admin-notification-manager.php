<?php
/**
 * Access_Admin_Notification_Manager Class.
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

use KolorWeb\KWAccessAdminNotification\KolorWeb_IP_Manager;

/**
 * Handle Notifications
 *
 * @since 1.0.1
 */
class KolorWeb_Access_Admin_Notification_Manager {

	/**
	 * IP Manage Handler.
	 *
	 * @var KolorWeb_IP_Manager
	 */
	private $ip_manager;


	/**
	 * Constructor Method
	 */
	public function __construct() {

		\add_action( 'init', array( $this, 'load_textdomain' ) );
		\add_action( 'wp_login', array( $this, 'notify_access' ), 10, 2 );
		\add_action( 'wp_loaded', array( $this, 'rescue_actions' ) );
		\add_action( 'clear_auth_cookie', array( $this, 'clear_access_time' ) );  // On logout we prepare the rule for next login notification.

		$this->ip_manager = new KolorWeb_IP_Manager();

	}


	/**
	 * Load plugin textdomain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'kolorweb-access-admin-notification', false, KWACCESSADMINNOTIFICATION_BASE . '/languages' );
	}


	/**
	 * Clear Notify Access Time.
	 */
	public function clear_access_time() {
		\update_user_meta( get_current_user_id(), '_kolorweb_notify_access', 0 );
	}


	/**
	 * Send Email Notification.
	 *
	 * @param string   $user_login username.
	 * @param \WP_User $user User Data.
	 * @return    void
	 */
	public function notify_access( $user_login, \WP_User $user ) {

		// Do not send email if user is not administrator.
		if ( ! \user_can( $user->ID, 'administrator' ) ) {
			return;
		}

		$time                     = time();
		$default_notify_interval  = 15; // minutes.
		$kolorweb_notify_interval = \apply_filters( 'kolorweb_notify_interval', $default_notify_interval );

		if ( empty( intval( $kolorweb_notify_interval ) ) ) {
			$kolorweb_notify_interval = $default_notify_interval;
		}

		$kolorweb_last_notify_access = intval( \get_user_meta( $user->ID, '_kolorweb_notify_access', true ) );

		// Sessions token instance.
		$manager       = \WP_Session_Tokens::get_instance( $user->ID );
		$session_count = count( $manager->get_all() );

		// If the user is not already logged in or notify interval time is not reached, skip send the notification.
		if ( 1 > intval( $session_count ) && ( $time - $kolorweb_last_notify_access ) < $kolorweb_notify_interval * 60 ) {
			return;
		}

		\update_user_meta( $user->ID, '_kolorweb_notify_access', $time );

		$logout_check       = $time . 'kw_na' . \wp_hash( 'force_logout_' . $user->ID . '_' . $time );
		$logout_reset_check = $time . 'kw_na' . \wp_hash( 'force_logout_reset_' . $user->ID . '_' . $time );

		/* translators: %1$s logout nonce, %2$s logout and reset password nonce */
		$message = sprintf( __( 'Dear Admin of ##sitename##,<br/>We detected the access of an admin user:<br/>User: <strong>##user_login##</strong><br/>Detected IP DATA: <strong>##user_ip_data##</strong><br/>If it was not you, or you are not aware of this login, you can <a href="##site_url##?kwflo=1&u=##user_id##&kwaan_logout=%1$s">click here to force log out user</a> or <a href="##site_url##?kwflo=1&u=##user_id##&rup=true&kwaan_logout_reset=%2$s">click here to force logout and reset password too</a>.<br/><br/>Ignore this message if you are logged in and your ip is the same as the one reported in this email.', 'kolorweb-access-admin-notification' ), $logout_check, $logout_reset_check );

		$ip_data_details = '';
		$ip_data         = $this->ip_manager->get_ip_data_details();
		if ( ! empty( $ip_data ) && is_array( $ip_data ) ) {
			$ip_data_fields = array(
				'ip'        => __( 'IP Address', 'kolorweb-access-admin-notification' ),
				'continent' => __( 'Continent', 'kolorweb-access-admin-notification' ),
				'country'   => __( 'Country', 'kolorweb-access-admin-notification' ),
				'region'    => __( 'Region', 'kolorweb-access-admin-notification' ),
				'city'      => __( 'City', 'kolorweb-access-admin-notification' ),
			);
			foreach ( $ip_data_fields as $field => $label ) {
				$ip_data_details .= isset( $ip_data[ $field ] ) && ! empty( $ip_data[ $field ] ) ? '✅ ' . $label . ' 👉🏻 ' . sanitize_text_field( wp_unslash( $ip_data[ $field ] ) ) . '<br/>' : '';
			}
		}

		if ( empty( $ip_data_details ) ) {
			$ip_data_details = __( 'Not Available Data', 'kolorweb-access-admin-notification' );
		} else {
			$ip_data_details = '<br/>' . $ip_data_details;
		}

		// Send notification email.
		$subject = '[' . get_bloginfo( 'name' ) . '] - ⚠️ ' . __( 'WARNING: New Success Admin Login', 'kolorweb-access-admin-notification' );
		if ( is_array( $ip_data ) ) {

			$subject .= isset( $ip_data['ip'] ) && ! empty( $ip_data['ip'] ) ? ' ' . __( 'by IP', 'kolorweb-access-admin-notification' ) . ' ' . sanitize_text_field( wp_unslash( $ip_data['ip'] ) ) : '';

			$subject .= isset( $ip_data['country'] ) && ! empty( $ip_data['country'] ) ? ' ' . __( 'from', 'kolorweb-access-admin-notification' ) . ' ' . sanitize_text_field( wp_unslash( $ip_data['country'] ) ) : '';

		}

		$subject .= ' ' . __( 'on', 'kolorweb-access-admin-notification' ) . ' ' . wp_date( __( 'Y/m/d H:i', 'kolorweb-access-admin-notification' ) );

		$message = str_replace(
			array( '##user_id##', '##user_login##', '##user_ip_data##', '##site_url##', '##sitename##' ),
			array(
				$user->ID,
				$user->data->user_login,
				$ip_data_details,
				\site_url(),
				\get_bloginfo( 'name' ),
			),
			$message,
		);

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		$admin_email = \get_option( 'admin_email' );

		// Send email to admin.
		\wp_mail( $admin_email, $subject, $message, $headers );

		if ( $user->data->user_email !== $admin_email ) {

			// Send email to current user admin.
			\wp_mail( $user->data->user_email, $subject, $message, $headers );

		}
	}


	/**
	 * Force Logout / Force Logout and reset Password.
	 *
	 * @return void
	 */
	public function rescue_actions() {

		if ( isset( $_GET['kwflo'] ) && 1 === intval( $_GET['kwflo'] ) ) { //phpcs:ignore;

			$user_id = isset( $_GET['u'] ) ? intval( $_GET['u'] ) : 0; //phpcs:ignore;
			if ( 0 === $user_id ) {
				return;
			}

			$logout = false;
			if ( isset( $_GET['kwaan_logout'] ) ) { //phpcs:ignore;
				$kwaan_logout       = \sanitize_text_field( \wp_unslash( $_GET['kwaan_logout'] ) ); //phpcs:ignore;
				$kwaan_logout       = explode( 'kw_na', $kwaan_logout );
				$force_logout_check = \wp_hash( 'force_logout_' . $user_id . '_' . $kwaan_logout[0] );
				$logout             = $kwaan_logout[1] === $force_logout_check;
			}

			$logout_reset = false;
			if ( isset( $_GET['kwaan_logout_reset'] ) ) { //phpcs:ignore;
				$kwaan_logout_reset       = \sanitize_text_field( \wp_unslash( $_GET['kwaan_logout_reset'] ) ); //phpcs:ignore;
				$kwaan_logout_reset       = explode( 'kw_na', $kwaan_logout_reset );
				$kwaan_logout_reset_check = \wp_hash( 'force_logout_reset_' . $user_id . '_' . $kwaan_logout_reset[0] );
				$logout_reset             = $kwaan_logout_reset[1] === $kwaan_logout_reset_check;
			}

			$user = \get_user_by( 'id', $user_id );

			if ( false !== $user && \user_can( $user->ID, 'administrator' ) ) {

				if ( $logout ) {

					/**
					* Fired before Force Logout User action happens.
					*
					* @since 1.0.0
					*/
					\do_action( 'before_kolorweb_force_logout_user' );

					$sessions = \WP_Session_Tokens::get_instance( $user->ID );
					$sessions->destroy_all();

					/**
					* Fired after Force Logout User action happens.
					*
					* @since 1.0.0
					*/
					\do_action( 'after_kolorweb_force_logout_user' );

				} elseif ( $logout_reset ) {

					/**
					* Fired before Force Logout and Reset Password action happens.
					*
					* @since 1.0.0
					*/
					\do_action( 'before_kolorweb_force_logout_reset_user' );

					$new_password = \wp_generate_password( 12, true, true );
					\wp_set_password( $new_password, $user->ID );

					$sessions = \WP_Session_Tokens::get_instance( $user->ID );
					$sessions->destroy_all();

					$message_admin = __( 'Dear Admin of ##sitename##,<br/>A new password has been set for this admin user:<br/>User: <strong>##user_login##</strong><br/>New Password: <strong>Sent to ##user_login##</strong>.<br/>Please write down your password in a safe place and change it often by choosing random combinations of letters, numbers and special characters.<br/><strong>Verify that there are no unknown and dubious malware files on the website.</strong>', 'kolorweb-access-admin-notification' );
					$message_user  = __( 'Dear Admin of ##sitename##,<br/>A new password has been set for this admin user:<br/>User: <strong>##user_login##</strong><br/>New Password: <strong>##user_password##</strong><br/>Please write down your password in a safe place and change it often by choosing random combinations of letters, numbers and special characters.<br/><strong>Verify that there are no unknown and dubious malware files on the website.</strong>', 'kolorweb-access-admin-notification' );

					// Send notification email.
					$message_admin = str_replace(
						array( '##user_login##', '##site_url##', '##sitename##' ),
						array(
							$user->data->user_login,
							\site_url(),
							\get_bloginfo( 'name' ),
						),
						$message_admin,
					);

					$message_user = str_replace(
						array( '##user_login##', '##user_password##', '##site_url##', '##sitename##' ),
						array(
							$user->data->user_login,
							$new_password,
							\site_url(),
							\get_bloginfo( 'name' ),
						),
						$message_user,
					);

					$subject_admin = '⚠️' . __( 'WARNING: New Security Password setted for an admin user account', 'kolorweb-access-admin-notification' );
					$subject_user  = '⚠️' . __( 'WARNING: New Security Password setted for your account', 'kolorweb-access-admin-notification' );
					$admin_email   = \get_option( 'admin_email' );
					$headers       = array( 'Content-Type: text/html; charset=UTF-8' );

					// send email to admin.
					\wp_mail( $admin_email, $subject_admin, $message_admin, $headers );

					// send email to user admin.
					\wp_mail( $user->data->user_email, $subject_user, $message_user, $headers );

					/**
					* Fired after Force Logout and Reset Password action happens.
					*
					* @since 1.0.0
					*/
					\do_action( 'after_kolorweb_force_logout_reset_user' );

				}
			}
		}
	}
}
