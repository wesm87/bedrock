<?php
/**
 * Plugin Name:  Bedrock Toolkit
 * Description:  Core classes to extend from when developing plugins or themes.
 * Version:      1.0.0
 * Author:       Decisionary Tech
 * Author URI:   http://decisionarytech.com
 *
 * @package      Bedrock\Toolkit
 */

namespace Bedrock\Toolkit;

// Exit if called directly.
defined( 'ABSPATH' ) || exit;

/**
 * Base class.
 *
 * @since 1.0.0
 */
class Base {

	/**
	 * The plugin / theme version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const VERSION = '1.0.0';

	/**
	 * The base plugin / theme path.
	 *
	 * @var string
	 */
	public $base_path;

	/**
	 * The base plugin / theme URL.
	 *
	 * @var string
	 */
	public $base_url;

	/**
	 * Relative path to the includes folder.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $includes_dir = 'includes';

	/**
	 * Relative path to the templates folder.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $templates_dir = 'templates';

	/**
	 * Relative path to the assets folder.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $assets_dir = 'assets/dist';

	/**
	 * Prevents creating multiple instances of this class and any child classes.
	 *
	 * @since 1.0.0
	 *
	 * @return \BLR\Core\Base The class instance.
	 */
	public static function instance() {

		static $_instances = [];

		$class = get_called_class();

		if ( ! isset( $_instances[ $class ] ) ) {
			$_instances[ $class ] = new $class();
		}

		return $_instances[ $class ];
	}

	/**
	 * Prevent cloning.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__(
			'Cloning plugin / theme classes is forbidden.',
			'blr-core'
		), '1.0.0' );
	}

	/**
	 * Prevent serializing.
	 *
	 * @since 1.0.0
	 */
	public function __sleep() {
		_doing_it_wrong( __FUNCTION__, esc_html__(
			'Serializing plugin / theme classes is forbidden.',
			'blr-core'
		), '1.0.0' );
	}

	/**
	 * Prevent unserializing.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__(
			'Unserializing plugin / theme classes is forbidden.',
			'blr-core'
		), '1.0.0' );
	}

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->add_hook( 'init', 'wp_init' );
		$this->add_hook( 'wp_enqueue_scripts', 'scripts' );
		$this->add_hook( 'wp_enqueue_scripts', 'styles' );
		$this->add_hook( 'wp_enqueue_scripts', 'scripts_and_styles' );
	}

	/**
	 * Tasks one or more path segments and joins them together.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $segments,... The path segments.
	 * @return string               The joined path.
	 */
	public function join_path() {

		$segments = array_filter( array_map( 'trim', func_get_args() ) );
		$path     = implode( '/', $segments );

		return $path;
	}

	/**
	 * Shortcut function for `join_path()` for use with URLs.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $segments,... The URL segments.
	 * @return string               The joined URL.
	 */
	public function join_url() {
		return call_user_func_array( [ $this, 'join_path' ], func_get_args() );
	}

	/**
	 * Returns the base plugin / theme path. Optionally takes a relative path
	 * and appends it to the base path before returning.
	 *
	 * @since 1.0.0
	 *
	 * @param string $rel_path Optional. Relative path to the file.
	 * @return string
	 */
	public function base_path( $rel_path = '' ) {

		$args = func_get_args();

		array_unshift( $args, $this->base_path );

		return call_user_func_array( [ $this, 'join_path' ], $args );
	}

	/**
	 * Returns the base plugin / theme URL. Optionally takes a relative URL
	 * and appends it to the base URL before returning.
	 *
	 * @since 1.0.0
	 *
	 * @param string $rel_path Optional. Relative path to the file.
	 * @return string
	 */
	public function base_url( $rel_path = '' ) {

		$args = func_get_args();

		array_unshift( $args, $this->base_url );

		return call_user_func_array( [ $this, 'join_url' ], $args );
	}

	/**
	 * Returns the path to an includes file. If no file path is specified, the
	 * path to the includes folder is returned instead.
	 *
	 * @since 1.0.0
	 *
	 * @param string $rel_path Optional. Relative path to the file.
	 * @return string
	 */
	public function include_path( $rel_path = '' ) {
		return $this->base_path( $this->includes_dir, $rel_path );
	}

	/**
	 * Returns the URL to an includes file. If no file path is specified, the
	 * URL to the includes folder is returned instead.
	 *
	 * @since 1.0.0
	 *
	 * @param string $rel_path Optional. Relative path to the file.
	 * @return string
	 */
	public function include_url( $rel_path = '' ) {
		return $this->base_url( $this->includes_dir, $rel_path );
	}

	/**
	 * Returns the path to a template file. If no file path is specified, the
	 * path to the templates folder is returned instead.
	 *
	 * @since 1.0.0
	 *
	 * @param string $rel_path Optional. Relative path to the file.
	 * @return string
	 */
	public function template_path( $rel_path = '' ) {
		return $this->base_path( $this->templates_dir, $rel_path );
	}

	/**
	 * Returns the URL to a template file. If no file path is specified, the
	 * URL to the templates folder is returned instead.
	 *
	 * @since 1.0.0
	 *
	 * @param string $rel_path Optional. Relative path to the file.
	 * @return string
	 */
	public function template_url( $rel_path = '' ) {
		return $this->base_url( $this->templates_dir, $rel_path );
	}

	/**
	 * Returns the path to an asset file. If no file path is specified, the path
	 * to the assets folder is returned instead.
	 *
	 * @since 1.0.0
	 *
	 * @param string $rel_path Relative path to the asset file.
	 * @return string
	 */
	public function asset_path( $rel_path = '' ) {
		return $this->base_path( $this->assets_dir, $rel_path );
	}

	/**
	 * Returns the URL to an asset file. If no file path is specified, the URL
	 * to the assets folder is returned instead.
	 *
	 * @since 1.0.0
	 *
	 * @param string $rel_path Relative path to the asset file.
	 * @return string
	 */
	public function asset_url( $rel_path = '' ) {
		return $this->base_url( $this->assets_dir, $rel_path );
	}

	/**
	 * Shortcut for the `get_template_part()` core function that prepends
	 * the template folder path automatically.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template The template name.
	 * @param string $variant  The template variant.
	 */
	public function get_template( $template, $variant = '' ) {
		get_template_part( $this->template_path( $template ), $variant );
	}

	/**
	 * Add hook callback for a class methods if it exists.
	 *
	 * @since 0.1.0
	 *
	 * @param string $hook   The action or filter name.
	 * @param mixed  $method The method name or callback.
	 */
	public function add_hook( $hook, $method ) {

		if ( is_string( $method ) ) {
			$method = [ $this, $method ];
		}

		if ( is_callable( $method ) ) {
			add_action( $hook, $method );
		}
	}

	/**
	 * Logs a message.
	 *
	 * @since 0.1.0
	 *
	 * @param  mixed $message The message to log.
	 * @param  mixed $level   The log level.
	 */
	public function log( $message, $level = E_USER_NOTICE ) {

		// Make sure debug mode is enabled and we're not in a unit test.
		if ( false === WP_DEBUG || 'unit-test' === WP_ENV ) {
			return false;
		}

		$backtrace = debug_backtrace();
		$caller_function = $backtrace[1]['function'];
		$error_message_pre_wrap = '::' . $caller_function . ':: [ "';
		$error_message_post_wrap = '" ]';
		$errors = [];

		switch ( true ) {

			case ( is_wp_error( $message ) ) : {
				$errors = $message->get_error_messages();

				break;
			}

			case ( is_array( $message ) || is_object( $message ) ) : {
				// @codingStandardsIgnoreStart
				$errors[] = str_replace( '=>', ':', print_r( $message, true ) );
				// @codingStandardsIgnoreEnd

				break;
			}

			default : {
				$errors[] = $message;

				break;
			}
		}

		foreach ( $errors as $error ) {
			$message = $error_message_pre_wrap . $error . $error_message_post_wrap;
			trigger_error( wp_kses_data( $message ), $level );
		}
	}

	/**
	 * A function that does nothing. Optionally takes a value and returns it.
	 *
	 * @since 0.5.2
	 *
	 * @param mixed $val Optional. The value to be returned. Default is null.
	 * @return mixed
	 */
	public function noop( $val = null ) {
		return $val;
	}

	/**
	 * Returns true if `WP_DEBUG` is enabled, false if not.
	 *
	 * @since 0.5.2
	 *
	 * @return boolean
	 */
	protected function is_debug() {
		return ( defined( 'WP_DEBUG' ) && WP_DEBUG );
	}

	/**
	 * Returns true if `SCRIPT_DEBUG` is enabled, false if not.
	 *
	 * @since 0.5.2
	 *
	 * @return boolean
	 */
	protected function is_script_debug() {
		return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );
	}
}


/**
 * Core plugin class.
 *
 * @since 1.0.0
 */
class Plugin extends Base {

	/**
	 * The plugin ID.
	 *
	 * This provides a consistent ID string for things like option names.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	const PLUGIN_ID = 'toolkit_plugin';

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct();

		// Set base plugin path / URL.
		$this->base_file = __FILE__;
		$this->base_name = plugin_basename( __FILE__ );
		$this->base_path = plugin_dir_path( __FILE__ );
		$this->base_url  = plugin_dir_url( __FILE__ );

		$this->add_hook( 'plugins_loaded', 'plugin_init' );
		$this->add_hook( 'plugins_loaded', 'includes' );
		$this->add_hook( 'init', 'register_post_type' );
		$this->add_hook( 'init', 'register_post_types' );
		$this->add_hook( 'init', 'register_taxonomy' );
		$this->add_hook( 'init', 'register_taxonomies' );
		$this->add_hook( 'post_updated_messages', 'post_updated_messages' );
		$this->add_hook( 'post_updated_messages', 'post_type_updated_messages' );
	}
}

/**
 * Core theme class.
 *
 * @since 1.0.0
 */
class Theme extends Base {

	/**
	 * The theme ID.
	 *
	 * This provides a consistent ID string for things like option names.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	const THEME_ID = 'toolkit_theme';

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct();

		// Set base plugin path / URL.
		$this->base_path = get_stylesheet_directory();
		$this->base_url  = get_stylesheet_directory_uri();

		$this->add_hook( 'switch_theme', 'on_theme_activated' );
		$this->add_hook( 'after_setup_theme', 'theme_init' );
		$this->add_hook( 'after_setup_theme', 'includes' );
	}
}
