<?php
/**
 * Plugin Name:  Register Theme Directory
 * Description:  Register default theme directory
 * Version:      1.0.0
 * Author:       Roots
 * Author URI:   https://roots.io
 * License:      MIT License
 *
 * @package      Bedrock\Register_Theme_Directory
 */

if ( ! defined( 'WP_DEFAULT_THEME' ) ) {
	register_theme_directory( ABSPATH . 'wp-content/themes' );
}
