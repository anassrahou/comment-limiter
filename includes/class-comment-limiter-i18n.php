<?php
/**
 * If accessed directly, then exit.
 */
defined( 'ABSPATH' ) || exit;


/**
 * If class do not exists, then create it.
 */
if ( ! class_exists( 'Comment_Limiter_i18n' ) ) {

    /**
     * Class that setup Comment Limiter internationalization
     */
    class Comment_Limiter_i18n
    {

        /**
         * Property instance.
         *
         * @since 1.0
         * @var object
         */
        private static $_instance;

        /**
         * Fire action and filter hooks
         *
         * @since 1.0
         */
        private function __construct() {
            $this->setup();
        }

        /**
         * Setup action and filter hooks.
         *
         * @since 1.0
         * @return boolean
         */
        public function setup() {
            add_action( 'plugins_loaded', array( $this, 'comment_limiter_textdomain' ) );
        }

        /**
         * Load the plugin text domain for translation.
         *
         * @since 1.0
         */
        public function comment_limiter_textdomain() {
            load_plugin_textdomain(
                'comment-limiter',
                false,
                dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
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
