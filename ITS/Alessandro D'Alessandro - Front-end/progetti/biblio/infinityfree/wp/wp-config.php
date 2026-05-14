<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'if041729165_wp644' );

/** Database username */
define( 'DB_USER', '41729165_4' );

/** Database password */
define( 'DB_PASSWORD', 'Wp1k86-S.g' );

/** Database hostname */
define( 'DB_HOST', 'sql301.byetcluster.com' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'dpn9cprm1g7act4cnhfxgwyf7zwptgpkd08cwmvtuj7as8ssup4pngy4eak3uocb' );
define( 'SECURE_AUTH_KEY',  'jgc9bgmfu13upuvfl6prouohsuztkha7gs49kpgilzbx1yjw1mtktdvxq6f2wfla' );
define( 'LOGGED_IN_KEY',    'qoh99havzkgoervhq9sjyzdzes9fwqkwrrvoefdzvb2nfkvmmd7hkfcjbnms7qh1' );
define( 'NONCE_KEY',        'h8nrq11w7gy0pkugjbjs4qiclchqmxmicf8b9yeshj423ao0owdonmcpxarynyke' );
define( 'AUTH_SALT',        'sdfrg4mygcfz2vdhl545twxp4fgimcxtvzpragitsxqlbjuiqomfn3p1al7bujqe' );
define( 'SECURE_AUTH_SALT', 'phos5d1y0k5kfbxajukbub8otlfaloy0gjip7brojtpcosbh3kb2slcbb0a8hjj5' );
define( 'LOGGED_IN_SALT',   'mqs3qlyvt71aui2j9cfuf1b7qogsm9peyag2tjkjm4vstkwkhlom5a7utiwljbsh' );
define( 'NONCE_SALT',       'pfhrka83ohbazyczirrzdd7ieskbprlkjzpmc6vobctlupetjjryqvl67sm6uxas' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wpct_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
@ini_set( 'display_errors', 1 );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
