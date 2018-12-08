<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'union-website-staging');

/** MySQL database username */
define('DB_USER', 'adam-rob');

/** MySQL database password */
define('DB_PASSWORD', 'shalagin38755#');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'lYd#I07u,)MCg?BuO[2*?f+4J=hZ4v e<x$?=:2a]YTu/4jf-`I*c/$|5rb}1494');
define('SECURE_AUTH_KEY',  'Y%Znt=jZ-pz@@I+hj+fx`[2s@x^ .4G-Vt(P}1AY`s;^by]:K?>2mg.zHG[sb#[N');
define('LOGGED_IN_KEY',    '04}LS;%EnE$w6!GG,=~`aRKWPh+ B)OO}VnO&/mH8n59In=%SbYpHtd`mGkF:T41');
define('NONCE_KEY',        'k2>/./Vzh?~;6Y%<O0?q::6Is>g?0E{cy1b .8C:NB5#V7Nbn(CBG|Fq n]mq!&G');
define('AUTH_SALT',        '#>K0qv[mySHE%/q*`!Z)E=;=2Z_PAb4b;%qfZ]o>qDTh-{6q^tHs7a:S--x._q J');
define('SECURE_AUTH_SALT', 'Ofgz8&9%rJt=+/qnsE2Pd.>TbN{H-GZ!Ap<y^*YhKxAR]?2/+RmmYqkJGMN{{Ck&');
define('LOGGED_IN_SALT',   '<Y%=KSa,k(@_xX2HCW}_|i+{YO{Jzl9g8zr,sR</[LcR|U@amq&d5;O#0CE`hV.Y');
define('NONCE_SALT',       'MxS-R`).d`>[}}v@)QoJkFljh/+u$de!tremXI#HMc#ez^pK1o17@a_7Isyc~=jW');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
