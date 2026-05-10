<?php
define( 'WP_CACHE', true );

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
define( 'DB_NAME', 'u293973646_Q4KEB' );

/** Database username */
define( 'DB_USER', 'u293973646_YCivM' );

/** Database password */
define( 'DB_PASSWORD', 'GdXGvi2ztI' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',          '*<$zO=I}4pTb5Cv]!) eAT1pVR<$X1I0mDVOmtUF?lmH*-$sWS 9k~lw&45{.AU1' );
define( 'SECURE_AUTH_KEY',   'hb %?1f@zHEH_F%2B}RWA6iHQ!xQoWj9}`{WCMfe,9Y3)@K]2,F@EF(6x#0;frC?' );
define( 'LOGGED_IN_KEY',     'Z84myNoV2_*#Je39G.N`phGRB X@SZAuU<ouqN%GM8ync,$ykA<.7Y$9PK+UQk` ' );
define( 'NONCE_KEY',         'H[NpO4=k~]% >eu&6jNnS,gmU;*5?u_%5gYIWOb0.|#]bI1X~r|/ z@2;Ev{J` A' );
define( 'AUTH_SALT',         'yUw,a2qdV~yf_R~8~Iy.&V?=H!X7%@b0`Z|(Se5YT <Nh4}`+ZZ{</.+C8f],`#;' );
define( 'SECURE_AUTH_SALT',  'W}AO4)!RXM9R=u^.Lq7{$`,~pP^/>YgHO)Zx!q[da8_NdQ8-V%G^J)D$GX97&xP$' );
define( 'LOGGED_IN_SALT',    'w=PvkS[{5q.jV0<{oOlCNFt/mbHg>8:OrHQZSgapB%XkGpkNZ>M;zPqKMrbV!yE;' );
define( 'NONCE_SALT',        ']tf~:^FI94%7tAw{a.idxY j]+DOq.p|p.}=ua2VKH&bl)ZQ]-9v[9F]El#MmUWP' );
define( 'WP_CACHE_KEY_SALT', '>AVbs8qu*2z+.T<3?DTACiDta]Sd![4=*aQgQSt<4hOpc!wI9Z%]tL5/Dr^*U$=v' );


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

define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', '80f8320b1d0bce3249fdefea330a925f' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
