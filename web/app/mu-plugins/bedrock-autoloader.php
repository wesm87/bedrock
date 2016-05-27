<?php
/**
 * Plugin Name:  Bedrock Autoloader
 * Description:  An autoloader that enables standard plugins to be required just like must-use plugins. The autoloaded plugins are included during mu-plugin loading. An asterisk (*) next to the name of the plugin designates the plugins that have been autoloaded.
 * Version:      1.0.0
 * Author:       Decisionary Tech
 * Author URI:   http://decisionarytech.com
 * License:      MIT License
 *
 * @package      Bedrock\Plugin\Autoloader
 */

namespace Bedrock;

/**
 * Class Autoloader.
 */
class Autoloader {

	/**
	 * Store Autoloader cache and site option.
	 *
	 * @var array
	 */
	private static $cache;

	/**
	 * Autoloaded plugins.
	 *
	 * @var array
	 */
	private static $auto_plugins;

	/**
	 * Autoloaded mu-plugins.
	 *
	 * @var array
	 */
	private static $mu_plugins;

	/**
	 * Number of plugins.
	 *
	 * @var int
	 */
	private static $count;

	/**
	 * Newly activated plugins.
	 *
	 * @var array
	 */
	private static $activated;

	/**
	 * Relative path to the mu-plugins dir.
	 *
	 * @var string
	 */
	private static $relative_path;

	/**
	 * Singleton instance.
	 *
	 * @var static
	 */
	private static $_single;

	/**
	 * Create singleton, populate vars, and set WordPress hooks
	 */
	public function __construct() {

		if ( isset( self::$_single ) ) {
			return;
		}

		self::$_single       = $this;
		self::$relative_path = '/../' . basename( __DIR__ );

		if ( is_admin() ) {
			add_filter( 'show_advanced_plugins', [ $this, 'showInAdmin' ], 0, 2 );
		}

		$this->load_plugins();
	}

	/**
	 * Run some checks then autoload our plugins.
	 */
	public function load_plugins() {

		$this->check_cache();
		$this->validate_plugins();
		$this->count_plugins();

		foreach ( self::$cache['plugins'] as $plugin_file => $plugin_info ) {
			include_once( WPMU_PLUGIN_DIR . '/' . $plugin_file );
		}

		$this->plugin_hooks();
	}

	/**
	 * Filter show_advanced_plugins to display the autoloaded plugins.
	 *
	 * @param bool   $show Whether to show the advanced plugins for the specified plugin type.
	 * @param string $type The plugin type, i.e., `mustuse` or `dropins`.
	 * @return bool  We return `false` to prevent WordPress from overriding our work.
	 * {@internal We add the plugin details ourselves, so we return false to disable the filter.}
	 */
	public function show_in_admin( $show, $type ) {
		$screen = get_current_screen();
		$current = is_multisite() ? 'plugins-network' : 'plugins';

		if ( $screen->{'base'} !== $current || 'mustuse' !== $type || ! current_user_can( 'activate_plugins' ) ) {
			return $show;
		}

		$this->update_cache();

		self::$auto_plugins = array_map(function( $auto_plugin ) {
			$auto_plugin['Name'] .= ' *';
			return $auto_plugin;
		}, self::$auto_plugins );

		$GLOBALS['plugins']['mustuse'] = array_unique(
			array_merge( self::$auto_plugins, self::$mu_plugins ),
			SORT_REGULAR
		);

		return false;
	}

	/**
	 * This sets the cache or calls for an update
	 */
	private function check_cache() {
		$cache = get_site_option( 'bedrock_autoloader' );

		if ( false === $cache ) {
			$this->update_cache();
			return;
		}

		self::$cache = $cache;
	}

	/**
	 * Get the plugins and mu-plugins from the mu-plugin path and remove duplicates.
	 * Check cache against current plugins for newly activated plugins.
	 * After that, we can update the cache.
	 */
	private function update_cache() {

		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		self::$auto_plugins = get_plugins( self::$relative_path );
		self::$mu_plugins   = get_mu_plugins();
		$plugins            = array_diff_key( self::$auto_plugins, self::$mu_plugins );
		$rebuild            = ! is_array( self::$cache['plugins'] );
		self::$activated    = ( $rebuild ) ? $plugins : array_diff_key( $plugins, self::$cache['plugins'] );
		self::$cache        = array( 'plugins' => $plugins, 'count' => $this->count_plugins() );

		update_site_option( 'bedrock_autoloader', self::$cache );
	}

	/**
	 * This accounts for the plugin hooks that would run if the plugins were
	 * loaded as usual. Plugins are removed by deletion, so there's no way
	 * to deactivate or uninstall.
	 */
	private function plugin_hooks() {

		if ( ! is_array( self::$activated ) ) {
			return;
		}

		foreach ( self::$activated as $plugin_file => $plugin_info ) {
			do_action( 'activate_' . $plugin_file );
		}
	}

	/**
	 * Check that the plugin file exists, if it doesn't update the cache.
	 */
	private function validate_plugins() {

		foreach ( self::$cache['plugins'] as $plugin_file => $plugin_info ) {
			if ( ! file_exists( WPMU_PLUGIN_DIR . '/' . $plugin_file ) ) {
				$this->update_cache();
				break;
			}
		}
	}

	/**
	 * Count the number of autoloaded plugins.
	 *
	 * Count our plugins (but only once) by counting the top level folders in the
	 * mu-plugins dir. If it's more or less than last time, update the cache.
	 *
	 * @return int Number of autoloaded plugins.
	 */
	private function count_plugins() {

		if ( isset( self::$count ) ) {
			return self::$count;
		}

		$count = count( glob( WPMU_PLUGIN_DIR . '/*/', GLOB_ONLYDIR | GLOB_NOSORT ) );

		if ( ! isset( self::$cache['count'] ) || $count !== self::$cache['count'] ) {
			self::$count = $count;
			$this->update_cache();
		}

		return self::$count;
	}
}

if ( is_blog_installed() ) {
	new Autoloader();
}
