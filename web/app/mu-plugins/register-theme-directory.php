<?php
/**
 * Plugin Name:  Register Theme Directory
 * Description:  Register default theme directory
 * Version:      1.0.0
 * Author:       Decisionary Tech
 * Author URI:   http://decisionarytech.com
 * License:      MIT License
 *
 * @package      Bedrock\Plugin\Register_Theme_Directory
 */

if ( ! defined( 'WP_DEFAULT_THEME' ) ) {
	register_theme_directory( ABSPATH . 'wp-content/themes' );
}
