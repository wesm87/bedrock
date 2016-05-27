<?php
/**
 * Plugin Name:  Disallow Indexing
 * Description:  Disallow indexing of your site on non-production environments.
 * Version:      1.0.0
 * Author:       Roots
 * Author URI:   https://roots.io
 * License:      MIT License
 *
 * @package      Bedrock\Disallow_Indexing
 */

if ( WP_ENV !== 'production' && ! is_admin() ) {
	add_action( 'pre_option_blog_public', '__return_zero' );
}
