<?php
/**
 * File:            IwHelper.php
 * Description:     Helper class
 * Version:         1.0.0
 * Author:          FinLy
 * Author URI:      https://github.com/finly/
 *
 */

namespace InertiaWordPress;

/**
 * No direct access
 */
if ( !defined ( 'ABSPATH' ) ) {
    die ( 'You are not allowed to call this page directly.' );
}

/**
 * Inertia Class
 */
if ( !class_exists ( 'IwHelper' ) ) {

    class IwHelper {

        public static $isInertiaRequest;

        /**
         * Send Inertia Headers
         */
        public static function send_inertia_headers () {

            self::checkInertiaRequest();

            if ( self::$isInertiaRequest ) {
                add_action ( 'send_headers', function () {
                    header ( 'Vary: Accept' );
                    header ( 'X-Inertia: true' );
                });
            }

        }

        /**
         * Check if the request is from Inertia
         */
        public static function checkInertiaRequest () {

            $headers = getallheaders();

            if ( isset ( $headers [ 'X-Requested-With' ] ) && $headers [ 'X-Requested-With' ] === 'XMLHttpRequest' && isset ( $headers [ 'X-Inertia' ] ) && $headers [ 'X-Inertia' ] === 'true' ) {
                self::$isInertiaRequest = true;
            }

            self::$isInertiaRequest = false;

        }

        /**
         * Set an array item to a given value using "dot" notation.
         * Reference: https://github.com/laravel/framework/blob/5.8/src/Illuminate/Support/Arr.php#L510
         *
         */
        public static function set_array_item ( &$array, $key, $value ) {
            if ( is_null ( $key ) ) {
                return $array = $value;
            }

            $keys = explode ( '.', $key );

            while ( count($keys) > 1 ) {
                $key = array_shift ( $keys );

                // If the key doesn't exist at this depth, we will just create an empty array
                // to hold the next value, allowing us to create the arrays to hold final
                // values at the correct depth. Then we'll keep digging into the array.
                if ( !isset ( $array[$key] ) || !is_array ( $array[$key] ) ) {
                    $array[$key] = [];
                }

                $array = &$array[$key];
            }

            $array[array_shift($keys)] = $value;

            return $array;
        }

        /**
         * Return the default value of the given value
         * Reference: https://github.com/rappasoft/laravel-helpers/blob/master/src/helpers.php#L1431
         *
         */
        public static function get_value ( $value ) {

            return $value instanceof Closure ? $value() : $value;

        }

        /**
         * Get an item from an array or object using "dot" notation
         * Reference: https://github.com/rappasoft/laravel-helpers/blob/master/src/helpers.php#L484
         *
         */
        public static function get_array_item ( $target, $key, $default = null ) {

            if ( is_null ( $key ) ) {
                return $target;
            }

            foreach ( explode ( '.', $key ) as $segment ) {
                if ( is_array ( $target ) ) {
                    if ( !array_key_exists ( $segment, $target ) ) {
                        return self::get_value ( $default );
                    }

                    $target = $target[$segment];
                } elseif ( $target instanceof ArrayAccess ) {
                    if ( !isset ( $target[$segment] ) ) {
                        return self::get_value ( $default );
                    }

                    $target = $target[$segment];
                } elseif ( is_object ( $target ) ) {
                    if ( !isset ( $target->{$segment} ) ) {
                        return self::get_value ( $default );
                    }

                    $target = $target->{$segment};
                } else {
                    return self::get_value ( $default );
                }
            }

            return $target;

        }

    }

}
