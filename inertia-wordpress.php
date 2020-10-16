<?php
/**
 * Plugin Name:     Inertia Wordpress
 * Plugin URI:      https://github.com/finly/inertia-wordpress
 * Description:     The WordPress adapter for Inertia.js
 * Version:         1.0.0
 * Text Domain:     inertia-wordpress
 * Author:          FinLy
 * Author URI:      https://github.com/finly/
 * License:         GPLv2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 *
 */

 use InertiaWordPress\IwInertia as Inertia;

/**
 * No direct access
 */
if ( !defined ( 'ABSPATH' ) ) {
    die ( 'You are not allowed to call this page directly.' );
}

/**
 * Set required constants
 */
defined ( 'IW_PLUGIN_NAME' )    or define ( 'IW_PLUGIN_NAME',       'inertia-wordpress' );
defined ( 'IW_PLUGIN_PATH' )    or define ( 'IW_PLUGIN_PATH',       WP_PLUGIN_DIR . '/' . IW_PLUGIN_NAME );
defined ( 'IW_LIBRARIES_PATH' ) or define ( 'IW_LIBRARIES_PATH',    IW_PLUGIN_PATH . '/libraries' );

/**
 * InertiaWordPress class
 */
if ( !class_exists ( 'InertiaWordPress' ) ) {
    class InertiaWordPress {

        /**
         * Constructor
         */
        public function __construct () {
            require_once ( IW_LIBRARIES_PATH . '/IwInertia.php' );
            new Inertia;
        }

    }
}

/**
 * Run Inertia Wordpress Plugin
 * Reference: https://developer.wordpress.org/reference/hooks/plugins_loaded/
 *
 */
add_action ( 'plugins_loaded', new InertiaWordPress );
