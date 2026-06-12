<?php
/**
 * If accessed directly, then exit.
 */
defined( 'ABSPATH' ) || exit;


/**
 * Check if class already exists.
 */
if ( ! class_exists( 'Comment_Limiter_Config' ) ) {

    /**
     * Configuration class.
     */
    class Comment_Limiter_Config
    {

        /**
         * Property instance.
         *
         * @var object
         */
        private static $_instance;

        /**
         * Handles default values.
         *
         * @var array
         */
        public $defaults = array();

        /**
         * Create default values.
         */
        public function __construct() {

            $this->defaults = array(
                'maximum_characters' => array(
                    'default' => 1000,
                ),
                'maximum_message' => array(
                    'default' => 'Your message exceeds the maximum limit allowed. Please try to reduce it.',
                ),
                'minimum_characters' => array(
                    'default' => 20,
                ),
                'minimum_message' => array(
                    'default' => 'Your message is below the minimum allowed limit. Please try to be more expressive.',
                ),
                'enable_admin_feature' => array(
                    'default' => 'no',
                ),
            );
        }

        /**
         * Return only default values.
         *
         * @return array default values
         */
        public function get_defaults() {

            $defaults = array();
            foreach ( $this->defaults as $key => $default ) {
                $defaults[ $key ] = $default[ 'default' ];
            }

            return $defaults;
        }

        /**
         * Parse default values.
         *
         * @return array default values parsed
         */
        public function get() {

            $config = get_option( 'comment_limiter_settings', $this->get_defaults() );

            return wp_parse_args( $config, $this->get_defaults() );
        }

        /**
         * Instance class object.
         *
         * @return object
         */
        public static function factory() {

            if ( ! self::$_instance ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }
    }

}
