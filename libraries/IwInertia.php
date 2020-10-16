<?php
/**
 * File:            IwInertia.php
 * Description:     Inertia class
 * Version:         1.0.0
 * Author:          FinLy
 * Author URI:      https://github.com/finly/
 *
 */

namespace InertiaWordPress;

use InertiaWordPress\IwHelper as Helper;

/**
 * No direct access
 */
if ( !defined ( 'ABSPATH' ) ) {
    die ( 'You are not allowed to call this page directly.' );
}

/**
 * IwInertia Class
 */
if ( !class_exists ( 'IwInertia' ) ) {

    class IwInertia {

        protected static $version;

        protected static $request;

        protected static $url;

        protected static $component;

        protected static $props;

        protected static $shared_props = [];

        /**
         * Constructor
         */
        public function __construct () {

            // Load Helper
            require_once ( IW_LIBRARIES_PATH . '/IwHelper.php');

            // Send Inertia additional headers
            Helper::send_inertia_headers();

        }

        /**
         * Render Inertia Page
         */
        public static function render (string $component, array $props = []) {

            global $inertia_page;

            self::setRequest();

            self::setUrl();

            self::setComponent($component);

            self::setProps($props);

            $inertia_page = [
                'url'       => self::$url,
                'props'     => self::$props,
                'version'   => self::$version,
                'component' => self::$component,
            ];

            if ( Helper::$isInertiaRequest ) {
                wp_send_json ( $inertia_page );
            }

            self::inject();

        }

        /**
         * Set Inertia shared properties
         */
        public static function share ( $key, $value = null ) {
            if ( is_array ( $key ) ) {
                self::$shared_props = array_merge ( self::$shared_props, $key );
            } else {
                Helper::set_array_item ( self::$shared_props, $key, $value );
            }
        }

        /**
         * Inject Inertia Page
         */
        protected static function inject (string $id = 'app') {

            global $inertia_page;

            if ( !isset ( $inertia_page ) ) return;

            $page = htmlspecialchars (
                json_encode($inertia_page),
                ENT_QUOTES,
                'UTF-8',
                true
            );

            echo "<div id='{$id}' data-page='{$page}'></div>";

        }

        /**
         * Add version for cache busting
         */
        public static function version (string $version = '') {

            self::$version = $version;

        }

        /**
         * Set request for Inertia WordPress
         */
        protected static function setRequest() {

            global $wp;

            self::$request = array_merge([
                'InertiaWordPress' => (array) $wp
            ], getallheaders());

        }

        /**
         * Set the main URL for Inertia page
         */
        protected static function setUrl() {

            self::$url = get_site_url() . '/' . Helper::get_array_item(self::$request, 'InertiaWordPress.request');

        }

        /**
         * Set the component name
         */
        protected static function setComponent(string $component) {

            self::$component = $component;

        }

        /**
         * Set the properties
         */
        protected static function setProps(array $props) {

            $props = array_merge($props, self::$shared_props);

            $only = array_filter(explode(',', Helper::get_array_item(self::$request, 'X-Inertia-Partial-Data')));

            $props = ($only && Helper::get_array_item(self::$request, 'X-Inertia-Partial-Component') === self::$component)
                ? Arr::only($props, $only)
                : $props;

            array_walk_recursive($props, function (&$prop) {
                if ($prop instanceof Closure) {
                    $prop = $prop();
                }
            });

            self::$props = $props;

        }

    }

}
