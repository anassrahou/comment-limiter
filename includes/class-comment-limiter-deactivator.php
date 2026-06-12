<?php
/**
 * If accessed directly, then exit
 */
defined( 'ABSPATH' ) || exit;


/**
 * Check if class already exists
 */
if ( ! class_exists( 'Comment_Limiter_Deactivator' ) ) {

    /**
     * Deactivation class.
     */
    class Comment_Limiter_Deactivator
    {

        /**
         * Property instance
         *
         * @var object
         */
        private static $_instance;

        /**
         * Create default values
         */
        public function __construct() {

            $this->setup();
        }

        /**
         * Setup methods.
         *
         * @since 1.0
         * @return void
         */
        public function setup() {

            $this->deactivate();
        }

        /**
         * Comment Limiter Deactivator.
         * Fire this function when the plugin is deactivated
         *
         * @since    1.0
         */
        public function deactivate() {

            register_deactivation_hook( __FILE__, array( $this, 'deactivate_comment_limiter' ) );
        }

        /**
         * Unregister setting method.
         *
         * @since   1.0
         * @return void
         */
        public function deactivate_comment_limiter() {

            unregister_setting(
                'comment_limiter_group',
                'comment_limiter_settings'
            );
        }

        /**
         * Instance class object.
         *
         * @since 1.0
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
