<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'b@wsVN}Q@1o~iNU8h392}2KlLr2457HFdPa47O4CtG_$<|,1q+;m2wV7&M|@htaB' );
define( 'SECURE_AUTH_KEY',   '4cCev1[<^&)5e!&nJh@OaQ2~:$VrWB&$$(59EItWN1|QX61%|d{;INe>~E~),K(U' );
define( 'LOGGED_IN_KEY',     'hCPw<(hw ?gFbc[:w[b5JWjd$tJ2V~AK]!$fg!taDUj*JGMHqUPqqPHBIV1J~`ac' );
define( 'NONCE_KEY',         '-nQXn-Nn+RWmcHS]IZR%6 JaAaop=JE<$v:h_k:xem+sp}#^m[u$T/J_G#fg=$CK' );
define( 'AUTH_SALT',         '!`Wy+>at!jJ@wHU18Y:q~IWNuIcb%b{&48tw6NQj7u_yIE{Qj2J/euu1F>10[myi' );
define( 'SECURE_AUTH_SALT',  'LoW;0n}.oS-mez)-vT_l~` ss+0^Vx4(x#8/6yZ]n) S |J#U)U7c?i~h4u&-x9o' );
define( 'LOGGED_IN_SALT',    'Ish|AJ86`|EAC;(*fo4WLo&v-1L& wOU^blsD04WeW<K,7Wn1cED[Muqh*wd6k;J' );
define( 'NONCE_SALT',        'vs}*D2d*7<W1L$523#SroZ3eTg^Dbw%#HHvX)r@TRe;%wm&%8:P$cAWHK}<`VS+Z' );
define( 'WP_CACHE_KEY_SALT', '45l3q;`V2rBVwYyS;}vV3{JZuP|C>t~%T1K<4V/,It}sbAPcSiJE.uI7jU[FhPa|' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
