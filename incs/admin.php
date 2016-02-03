<?php
/**
 * WordPress plugin admin class.
 *
 * @package    WordPress
 * @subpackage VA OneSignal in Custom Post Types
 * @author     KUCKLU <kuck1u@visualive.jp>
 *             Copyright (C) 2015 KUCKLU and VisuAlive.
 *             This program is free software; you can redistribute it and/or modify
 *             it under the terms of the GNU General Public License as published by
 *             the Free Software Foundation; either version 2 of the License, or
 *             (at your option) any later version.
 *             This program is distributed in the hope that it will be useful,
 *             but WITHOUT ANY WARRANTY; without even the implied warranty of
 *             MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *             GNU General Public License for more details.
 *             You should have received a copy of the GNU General Public License along
 *             with this program; if not, write to the Free Software Foundation, Inc.,
 *             51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *             It is also available through the world-wide-web at this URL:
 *             http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace VAONESIGNALINCUSTOMPOSTTYPES\Modules;

use VAONESIGNALINCUSTOMPOSTTYPES\VAONESIGNALINCUSTOMPOSTTYPES_Singleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ADMIN.
 *
 * @since 1.0.0
 */
class VAONESIGNALINCUSTOMPOSTTYPES_Admin extends \OneSignal_Admin {
	use VAONESIGNALINCUSTOMPOSTTYPES_Singleton;

	/**
	 * This hook is called once any activated themes have been loaded.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings If the set value is required, pass a value in an array.
	 */
	public function __construct( $settings = array() ) {
		//VAONESIGNALINCUSTOMPOSTTYPES_Singleton

		if ( self::is_plugin_active( VAOSICPT_ONESIGNAL_PLUGIN ) ) {
			remove_action( 'init', array( 'OneSignal_Admin', 'init' ) );
			add_action( 'init', array( &$this, 'init' ) );
		}
	}

	public static function init() {
		$onesignal = new parent();

		if ( current_user_can( 'update_plugins' ) ) {
			add_action( 'admin_menu', array( 'OneSignal_Admin', 'add_admin_page' ) );
		}

		if ( current_user_can( 'publish_posts' ) || current_user_can( 'edit_published_posts' ) ) {
			add_action( 'add_meta_boxes', array( __CLASS__, 'add_onesignal_post_options' ) );
		}

		add_action( 'transition_post_status', array( __CLASS__, 'on_transition_post_status' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( 'OneSignal_Admin', 'admin_styles' ) );

		return $onesignal;
	}

	public static function add_onesignal_post_options() {
		$post_types = array_values( get_post_types( array(
			'public' => true,
		) ) );
		$attachment = array_search( 'attachment', $post_types );

		if ( false !== $attachment ) {
			unset( $post_types[ $attachment ] );
		}

		foreach ( $post_types as $post_type ) {
			add_meta_box( 'onesignal_notif_on_post', 'OneSignal', array(
				'\OneSignal_Admin',
				'onesignal_notif_on_post_html_view'
			), $post_type, 'side', 'high' );
		}
	}

	public static function send_notification_on_wp_post( $new_status, $old_status, $post ) {
		$post_types = array_values( get_post_types( array(
			'public' => true,
		) ) );
		$attachment = array_search( 'attachment', $post_types );

		if ( false !== $attachment ) {
			unset( $post_types[ $attachment ] );
		}

		if ( empty( $post ) || ! in_array( get_post_type( $post ), $post_types ) || $new_status !== "publish" ) {
			return $new_status;
		}

		$onesignal_wp_settings = \OneSignal::get_onesignal_settings();

		if ( isset( $_POST['has_onesignal_setting'] ) ) {
			if ( array_key_exists( 'send_onesignal_notification', $_POST ) ) {
				$send_onesignal_notification = $_POST['send_onesignal_notification'];
			} else {
				$send_onesignal_notification = false;
			}
		} elseif ( $old_status !== "publish" ) {
			$send_onesignal_notification = $onesignal_wp_settings['notification_on_post_from_plugin'];
		}

		if ( $send_onesignal_notification === true || $send_onesignal_notification === "true" ) {
			$notif_content = html_entity_decode( get_the_title( $post->ID ), ENT_QUOTES, 'UTF-8' );

			$fields = array(
				'app_id'            => $onesignal_wp_settings['app_id'],
				'included_segments' => array( 'All' ),
				'isAnyWeb'          => true,
				'url'               => get_permalink( $post->ID ),
				'contents'          => array( "en" => $notif_content )
			);

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications" );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Authorization: Basic ' . $onesignal_wp_settings['app_rest_api_key']
			) );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HEADER, false );
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

			$response = curl_exec( $ch );
			curl_close( $ch );

			return $response;
		}

		return $new_status;
	}

	public static function on_transition_post_status( $new_status, $old_status, $post ) {
		self::send_notification_on_wp_post( $new_status, $old_status, $post );
	}
}
