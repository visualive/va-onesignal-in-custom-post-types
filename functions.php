<?php
/**
 * Plugin Name: VA OneSignal in Custom Post Types
 * Plugin URI: https://github.com/visualive/va-onesignal-in-custom-post-types
 * Description: This plugin is an Addon that makes "OneSignal Push Notifications" compatible with custom post types.
 * Author: KUCKLU
 * Version: 1.0.0
 * Author URI: http://visualive.jp/
 * Text Domain: va-onesignal-in-custom-post-types
 * Domain Path: /langs
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package    WordPress
 * @subpackage VA OneSignal in Custom Post Types
 * @author     KUCKLU <kuck1u@visualive.jp>
 *             Copyright (C) 2015 KUCKLU & VisuAlive.
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VAOSICPT_ONESIGNAL_PLUGIN', 'onesignal-free-web-push-notifications/onesignal.php' );
define( 'VAOSICPT_ONESIGNAL_PLUGIN_PATH', plugin_dir_path( dirname( dirname( __FILE__ ) ) . '/onesignal-free-web-push-notifications' ) );

$vaosicpt_onesignal_admin    = dirname( dirname( __FILE__ ) ) . '/onesignal-free-web-push-notifications/onesignal-admin.php';
$vaosicpt_onesignal_settings = dirname( dirname( __FILE__ ) ) . '/onesignal-free-web-push-notifications/onesignal-settings.php';

if ( file_exists( $vaosicpt_onesignal_admin ) && file_exists( $vaosicpt_onesignal_settings ) ) {
	require_once $vaosicpt_onesignal_admin;
	require_once $vaosicpt_onesignal_settings;
	require_once dirname( __FILE__ ) . '/incs/singleton.php';
	require_once dirname( __FILE__ ) . '/incs/admin.php';
}

/**
 * Run plugin.
 *
 * @since 1.0.0
 */
add_action( 'plugins_loaded', function () use ( $vaosicpt_onesignal_admin, $vaosicpt_onesignal_settings ) {
	if ( file_exists( $vaosicpt_onesignal_admin ) && file_exists( $vaosicpt_onesignal_settings ) ) {
		new \VAONESIGNALINCUSTOMPOSTTYPES\Modules\VAONESIGNALINCUSTOMPOSTTYPES_Admin();
	}
} );
