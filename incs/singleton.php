<?php
/**
 * WordPress plugin singleton trait.
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

namespace VAONESIGNALINCUSTOMPOSTTYPES;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Trait SINGLETON.
 *
 * @since 1.0.0
 */
trait VAONESIGNALINCUSTOMPOSTTYPES_Singleton {
	/**
	 * Holds the singleton instance of this class
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $instances = array();

	/**
	 * Instance.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $settings If the set value is required, pass a value in an array.
	 *
	 * @return self
	 */
	public static function instance( $settings = array() ) {
		$class_name = get_called_class();

		if ( ! isset( self::$instances[ $class_name ] ) ) {
			self::$instances[ $class_name ] = new $class_name( $settings );
		}

		return self::$instances[ $class_name ];
	}

	/**
	 * This hook is called once any activated themes have been loaded.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings If the set value is required, pass a value in an array.
	 */
	protected function __construct( $settings = array() ) {
	}

	/**
	 * Check whether the plugin is active by checking the active_plugins list.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin
	 *
	 * @return bool
	 */
	public static function is_plugin_active( $plugin ) {
		if ( function_exists( 'is_plugin_active' ) ) {
			$result = is_plugin_active( $plugin );
		} else {
			if ( ! is_multisite() ) {
				$result = in_array( $plugin, get_option( 'active_plugins', array() ) );
			} else {
				$plugins = get_site_option( 'active_sitewide_plugins', array() );
				$result  = isset( $plugins[ $plugin ] );
			}
		}

		return $result;
	}

	/**
	 * Check if a plugin is installed. Does not take must-use plugins into account.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin Plugin slug.
	 *
	 * @return bool
	 */
	public static function is_plugin_installed( $plugin ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();

		return ( ! empty( $plugins[ $plugin ] ) );
	}
}
