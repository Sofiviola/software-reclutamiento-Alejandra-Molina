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

// Habilitar el modo de depuración de WordPress
define('WP_DEBUG', true);

// Registrar los errores y advertencias en el archivo debug.log
define('WP_DEBUG_LOG', true);

// Mostrar errores en la pantalla (desactívalo en producción)
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);

// Deshabilitar la edición directa de archivos del theme y plugins desde el admin
define('DISALLOW_FILE_EDIT', true);


define('AUTH_KEY',         'Buvl/aoviiTq/DFknf6Y2slp4l7hb2jj/IAM3ENtDRM50UAyPgBvcqZOuBX+MV2bJC2yNC0U4024FL+xWaunBQ==');
define('SECURE_AUTH_KEY',  '3X5pdd3QnfVPx+fDYhZILIjZow5fSP0V3I8tI2QjKgQ0nLJo5rgKsPxD+OK4Q2e6w8fu3aXQihkUhDIas0wUTA==');
define('LOGGED_IN_KEY',    '8T0icQ0uZYjNkJeoxaUmXRigBHZ29RPxLI1+I9NDtKlJqpL+JVZ98hwotig4mnJNTWYSAXblDYtgvQqBiRdMkA==');
define('NONCE_KEY',        '1chAoew9Zurwvj/3DRxZPQA6VsL6mRZFTaCx37ky76fh/YbEOW38xS98CmLagSdB5ptByaiLgRgdqSbyYAriFg==');
define('AUTH_SALT',        'N+c7aj1gO+pm2kYJS2JcloXEXG0vyTyko/2AKRgltQpgG4yvoy+92q5CSZZ24LnNHLtryhvTYSDWmqAMkmQAFw==');
define('SECURE_AUTH_SALT', 'ZVwO1k6zlPIXFAJepT8rolF+ZVt9e3HSkUdRpO/1AHO46gAv0TUG+EUCcWYMNeCUUmxCNWjJGXBEq4F7e/Ec4w==');
define('LOGGED_IN_SALT',   'YO366fXmO7Dcf23T0PDg1UMfsNO72LF8CkitJ1CWB85J5RSudsAQabgk+CdXPv+X6YD+Oeta2YmlhI/JJq6k8w==');
define('NONCE_SALT',       'B6pnNTey0XYWZfQ65XPN52pRw1tKprmpVBT4mOTtOBTJ1Q/ucl/ZfhL9YXpJ9HBmU/U8XnrKuusFI+f/ttRVnA==');
define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
