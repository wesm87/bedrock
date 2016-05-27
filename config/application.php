<?php
/**
 * The main configuration file.
 *
 * @package Bedrock\Config
 */

/**
 * Project root directory.
 *
 * @var string
 */
$root_dir = dirname( __DIR__ );

/**
 * Website root directory.
 *
 * @var string
 */
$webroot_dir = $root_dir . '/web';

/**
 * Expose global env() function from oscarotero/env
 */
Env::init();

/**
 * Use Dotenv to set required environment variables and load .env file in root
 */
$dotenv = new Dotenv\Dotenv( $root_dir );
if ( file_exists( $root_dir . '/.env' ) ) {
	$dotenv->load();
	$dotenv->required( [ 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'WP_HOME', 'WP_SITEURL' ] );
}

/**
 * Set up our global environment constant and load its config first
 * Default: development
 */
define( 'WP_ENV', env( 'WP_ENV' ) ?: 'development' );

$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if ( file_exists( $env_config ) ) {
	require_once( $env_config );
}

/**
 * Environment URLs for the WP Stage Switcher plugin.
 */
$envs = [
	'development' => env( 'DEVELOPMENT_URL' ),
	'staging'     => env( 'STAGING_URL' ),
	'production'  => env( 'PRODUCTION_URL' ),
];
define( 'ENVIRONMENTS', serialize( $envs ) );


/**
 * URLs
 */
define( 'WP_HOME', env( 'WP_HOME' ) );
define( 'WP_SITEURL', env( 'WP_SITEURL' ) );

/**
 * Custom content directory
 */
define( 'CONTENT_DIR', '/app' );
define( 'WP_CONTENT_DIR', $webroot_dir . CONTENT_DIR );
define( 'WP_CONTENT_URL', WP_HOME . CONTENT_DIR );

/**
 * DB settings
 */
define( 'DB_NAME', env( 'DB_NAME' ) );
define( 'DB_USER', env( 'DB_USER' ) );
define( 'DB_PASSWORD', env( 'DB_PASSWORD' ) );
define( 'DB_HOST', env( 'DB_HOST' ) ?: 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );
$table_prefix = env( 'DB_PREFIX' ) ?: 'wp_';

/**
 * Authentication unique keys and salts
 */
define( 'AUTH_KEY', env( 'AUTH_KEY' ) );
define( 'SECURE_AUTH_KEY', env( 'SECURE_AUTH_KEY' ) );
define( 'LOGGED_IN_KEY', env( 'LOGGED_IN_KEY' ) );
define( 'NONCE_KEY', env( 'NONCE_KEY' ) );
define( 'AUTH_SALT', env( 'AUTH_SALT' ) );
define( 'SECURE_AUTH_SALT', env( 'SECURE_AUTH_SALT' ) );
define( 'LOGGED_IN_SALT', env( 'LOGGED_IN_SALT' ) );
define( 'NONCE_SALT', env( 'NONCE_SALT' ) );

/**
 * Project settings.
 */
define( 'WP_DEFAULT_THEME', env( 'WP_DEFAULT_THEME' ) );
define( 'WP_PROJECT_TYPE', env( 'WP_PROJECT_TYPE' ) );

/**
 * VersionPress settings.
 */
define( 'VP_PROJECT_ROOT', $webroot_dir );
define( 'VP_ENVIRONMENT', WP_ENV );

/**
 * Log paths.
 */
define( 'WP_DEBUG_LOG_PATH', env( 'WP_DEBUG_LOG_PATH' ) ?: WP_CONTENT_DIR . '/log' );
define( 'WC_LOG_DIR', env( 'WC_LOG_DIR' ) ?: WP_DEBUG_LOG_PATH . '/woocommerce' );

/**
 * Custom settings
 */
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'DISABLE_WP_CRON', env( 'DISABLE_WP_CRON' ) ?: false );
define( 'DISALLOW_FILE_EDIT', true );


/**
 * Bootstrap WordPress
 */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', $webroot_dir . '/wp/' );
}
