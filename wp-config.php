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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress_2');

/** Database username */
define('DB_USER', 'root');

/** Database password */
define('DB_PASSWORD', 'root');

/** Database hostname */
define('DB_HOST', '127.0.0.1');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

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
define(
    'AUTH_KEY',
    '0@]xs2 icZ2`!x0[EN<JK:,p6J((FHY/!G 9_q4Mi{Su$H-g3X8U`@abG*@XmP^r'
);
define(
    'SECURE_AUTH_KEY',
    '2%I[fl%a<X!jsqIi1K,M2Ev@`h|E;= aDuV=cE.^5L9.h`TMnWIjX[36P.aDM)}:'
);
define(
    'LOGGED_IN_KEY',
    't+C.`7AjM2d+AOZTijh&R>dvyc#Mz}M$iR. 5qoc7>k,-{w[+l?ki(+6vutQ|KdV'
);
define(
    'NONCE_KEY',
    'XNCj_N<*fV2]VRNL3~J?hR?TqDtMH9{;GZJWq$%[|XE8JexT:}b9zIdzdiR:ZM<L'
);
define(
    'AUTH_SALT',
    'GV z&)M^X%#++jsSsarkeOv QZr]|r;udjVMZl>{9;%C}}BB|#PUgT)jh/Dk{=6F'
);
define(
    'SECURE_AUTH_SALT',
    '}DLoCz]x2;0@0u5W4ukF]5~WQz3-u<K.j`Gv0XHGz;-B1.L|0}^rnW$$8OhG7B(='
);
define(
    'LOGGED_IN_SALT',
    'U+&>Mctk|Z*T WkJSo.O2mjna>Snu$E[^&kt35T:%*VkOhEw>7-o Y3=)):YbL+B'
);
define(
    'NONCE_SALT',
    '|^q[GuSNy37r)AT[`4%R0cm=[1ZV&xsX,-MG@{n)G[085$8h`#ZRbntnXEo@(y:-'
);

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define('WP_DEBUG', false);

/* Add any custom values between this line and the "stop editing" line. */

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}
define('FS_METHOD', 'direct');


/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
